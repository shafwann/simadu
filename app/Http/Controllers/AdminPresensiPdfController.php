<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Attendance;
use App\Models\AboutSetting;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class AdminPresensiPdfController extends Controller
{
    public function download(Request $request, $id)
    {
        $month = (int) $request->get('month', now()->month);
        $year  = (int) $request->get('year', now()->year);

        if ($month < 1 || $month > 12) $month = now()->month;
        if ($year < 2000 || $year > 2100) $year = now()->year;

        $student = User::findOrFail($id);
        $about   = AboutSetting::first();

        $start = Carbon::create($year, $month, 1)->startOfDay();
        $end   = (clone $start)->endOfMonth()->endOfDay();

        $attendances = Attendance::where('user_id', $student->id)
            ->whereBetween('tanggal', [$start->toDateString(), $end->toDateString()])
            ->get()
            ->keyBy(fn ($a) => Carbon::parse($a->tanggal)->format('Y-m-d'));

        $daysInMonth = $start->daysInMonth;

        // ✅ realtime libur nasional (tanggal merah) untuk tahun berapa pun
        $holidays = $this->getIndonesiaHolidaysRealtime($year);

        // ✅ opsional debug: buka /presensi-pdf?...&debugHoliday=1
        if ((int)$request->get('debugHoliday') === 1) {
            return response()->json([
                'year'   => $year,
                'count'  => count($holidays),
                'sample' => array_slice($holidays, 0, 10, true),
            ]);
        }

        $rows = [];
        for ($d = 1; $d <= $daysInMonth; $d++) {
            $date = Carbon::create($year, $month, $d);
            $key  = $date->format('Y-m-d');

            $isWeekend = $date->isSaturday() || $date->isSunday();
            $isRedDate = isset($holidays[$key]);

            // ✅ pink kalau weekend atau tanggal merah
            $isHoliday = $isWeekend || $isRedDate;

            $a = $attendances[$key] ?? null;

            $status = $a?->status;

            // normalisasi data lama
            if ($status === 'Alpha') $status = 'Tanpa Keterangan';
            if ($status === 'Izin')  $status = 'Ijin';

            $mark = [
                'I'  => ($status === 'Ijin'),
                'S'  => ($status === 'Sakit'),
                'DL' => ($status === 'Dinas Luar'),
                'CT' => ($status === 'Cuti'),
                'TK' => ($status === 'Tanpa Keterangan'),
            ];

            $signed = false;
            if ($a) {
                if (in_array($status, ['Hadir', 'Telat'], true)) $signed = true;
                if (!empty($a->check_in) || !empty($a->check_out)) $signed = true;
            }

            $rows[] = [
                'day'       => str_pad((string)$d, 2, '0', STR_PAD_LEFT),
                'date'      => $key,
                'isHoliday' => $isHoliday,
                'signed'    => $signed,
                'mark'      => $mark,
            ];
        }

        // split seperti template: 1-16 dan 17-akhir
        $left  = array_slice($rows, 0, 16);
        $right = array_slice($rows, 16);

        // ✅ BULAN INDONESIA (pasti Indo, tidak tergantung locale server)
        $bulanLabel = $this->bulanIndo($month); // contoh: Januari

        $pdf = Pdf::loadView('admin.students.presensi_pdf', compact(
            'student', 'about', 'year', 'bulanLabel', 'left', 'right'
        ))->setPaper('A4', 'portrait');

        $filename = "DAFTAR-HADIR-MAGANG-{$student->nim}-{$bulanLabel}-{$year}.pdf";
        return $pdf->download($filename);
    }

    /**
     * ✅ Realtime libur nasional Indonesia dari API (tahun bebas).
     * Cache per tahun 30 hari, fallback weekend-only kalau gagal.
     */
    private function getIndonesiaHolidaysRealtime(int $year): array
    {
        $cacheKey = "holidays_id_realtime_{$year}";

        return Cache::remember($cacheKey, now()->addDays(30), function () use ($year) {
            try {
                $url = "https://date.nager.at/api/v3/PublicHolidays/{$year}/ID";

                $res = Http::retry(2, 300)
                    ->timeout(10)
                    ->acceptJson()
                    ->get($url);

                if ($res->failed()) {
                    return [];
                }

                $data = $res->json();
                if (!is_array($data)) {
                    return [];
                }

                $out = [];
                foreach ($data as $h) {
                    if (is_array($h) && !empty($h['date'])) {
                        $out[$h['date']] = $h['localName'] ?? ($h['name'] ?? 'Hari Libur');
                    }
                }

                return $out;
            } catch (\Throwable $e) {
                return [];
            }
        });
    }

    /**
     * ✅ Mapping BULAN Indonesia (1-12).
     */
    private function bulanIndo(int $month): string
    {
        $bulan = [
            1  => 'Januari',
            2  => 'Februari',
            3  => 'Maret',
            4  => 'April',
            5  => 'Mei',
            6  => 'Juni',
            7  => 'Juli',
            8  => 'Agustus',
            9  => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember',
        ];

        return $bulan[$month] ?? 'Januari';
    }
}
