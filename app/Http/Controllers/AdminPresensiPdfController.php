<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Attendance;
use App\Models\AboutSetting;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
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

        $start = Carbon::create($year, $month, 1, 0, 0, 0, 'Asia/Jakarta');
        $daysInMonth = $start->daysInMonth;

        $attendances = Attendance::where('user_id', $student->id)
            ->whereMonth('tanggal', $month)
            ->whereYear('tanggal', $year)
            ->get()
            ->keyBy(fn ($a) => Carbon::parse($a->tanggal)->format('Y-m-d'));

        // libur nasional + cuti bersama (manual lengkap, fallback API)
        $holidays = $this->getIndonesiaHolidaysRealtime($year);

        $rows = [];
        for ($d = 1; $d <= $daysInMonth; $d++) {
            $date = Carbon::create($year, $month, $d, 0, 0, 0, 'Asia/Jakarta');
            $key  = $date->format('Y-m-d');

            $isHoliday = $date->isWeekend() || isset($holidays[$key]);

            $a = $attendances[$key] ?? null;

            // status
            $status = $a?->status ?? '-';
            if ($status === 'Alpha') $status = 'TK';
            if ($status === 'Izin')  $status = 'I';

            // checkin / checkout kolom sendiri
            $checkin  = (!empty($a?->check_in))  ? $a->check_in  : '-';
            $checkout = (!empty($a?->check_out)) ? $a->check_out : '-';

            // tanggal label seperti contoh
            $tanggalLabel = $date->translatedFormat('d M Y');

            $rows[] = [
                'tanggal_label'  => $tanggalLabel,
                'checkin_label'  => $checkin,
                'checkout_label' => $checkout,
                'status'         => $status,
                'status_label'   => $status,
                'location'       => $a?->location ?: null,
                'isHoliday'      => $isHoliday,
            ];
        }

        // ✅ tetap left / right (agar 1 halaman, 31 hari jadi 16 + sisanya)
        $left  = array_slice($rows, 0, 16);
        $right = array_slice($rows, 16);

        $bulanLabel = $this->bulanIndo($month);

        // ✅ F4 = 210mm x 330mm -> points: 595.28 x 935.43
        $f4 = [0, 0, 595.28, 935.43];

        return Pdf::loadView('admin.students.presensi_pdf', compact(
            'student','about','year','bulanLabel','left','right'
        ))
        ->setPaper($f4, 'portrait')
        ->download("DAFTAR-HADIR-MAGANG-{$student->nim}-{$bulanLabel}-{$year}.pdf");
    }

    private function getIndonesiaHolidaysRealtime(int $year): array
    {
        $manual = require app_path('Support/IndonesianHolidays.php');
        if (isset($manual[$year])) return $manual[$year];

        try {
            $res = Http::timeout(10)->get("https://date.nager.at/api/v3/PublicHolidays/$year/ID");
            if (!$res->ok()) return [];

            $out = [];
            foreach ($res->json() as $h) {
                $out[$h['date']] = $h['localName'] ?? 'Hari Libur';
            }
            return $out;
        } catch (\Throwable $e) {
            return [];
        }
    }

    private function bulanIndo(int $m): string
    {
        return [
            1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',
            5=>'Mei',6=>'Juni',7=>'Juli',8=>'Agustus',
            9=>'September',10=>'Oktober',11=>'November',12=>'Desember'
        ][$m];
    }
}
