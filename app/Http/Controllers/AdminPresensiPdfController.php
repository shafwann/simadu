<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Attendance;
use App\Models\AboutSetting;
use App\Support\AttendanceStatus;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class AdminPresensiPdfController extends Controller
{
    public function download(Request $request, $id)
    {
        $student = User::findOrFail($id);
        return $this->renderPresensiPdf($request, $student);
    }

    public function myPresensiPdf(Request $request)
    {
        $student = Auth::user();
        if (!$student) abort(403, 'Anda harus login.');
        return $this->renderPresensiPdf($request, $student);
    }

    private function renderPresensiPdf(Request $request, User $student)
    {
        $month = (int) $request->get('month', now()->month);
        $year  = (int) $request->get('year', now()->year);

        if ($month < 1 || $month > 12) $month = now()->month;
        if ($year < 2000 || $year > 2100) $year = now()->year;

        $tz = 'Asia/Jakarta';

        $about = AboutSetting::where('kategori_instansi', $student->kategori_instansi)->first();
        if (!$about) abort(500, 'Setting instansi belum dikonfigurasi.');

        $start = Carbon::create($year, $month, 1, 0, 0, 0, $tz);
        $daysInMonth = $start->daysInMonth;

        $attendances = Attendance::where('user_id', $student->id)
            ->whereMonth('tanggal', $month)
            ->whereYear('tanggal', $year)
            ->get()
            ->keyBy(function ($a) use ($tz) {
                return Carbon::parse($a->tanggal, $tz)->format('Y-m-d');
            });

        $holidays = $this->getIndonesiaHolidaysMerged($year);

        $checkoutEndMap = [
            1 => $about->senin_checkout_selesai,
            2 => $about->selasa_checkout_selesai,
            3 => $about->rabu_checkout_selesai,
            4 => $about->kamis_checkout_selesai,
            5 => $about->jumat_checkout_selesai,
        ];

        $rows = [];

        for ($d = 1; $d <= $daysInMonth; $d++) {
            $date = Carbon::create($year, $month, $d, 0, 0, 0, $tz);
            $key  = $date->format('Y-m-d');

            $isHoliday = $date->isWeekend() || isset($holidays[$key]);

            $a = $attendances[$key] ?? null;

            $checkin  = (!empty($a?->check_in))  ? Carbon::parse($a->check_in, $tz)->format('H:i')  : '-';
            $checkout = (!empty($a?->check_out)) ? Carbon::parse($a->check_out, $tz)->format('H:i') : '-';

            $main = AttendanceStatus::mainCode($a?->status);
            $pc   = AttendanceStatus::isPC($a?->keterangan);

            $dow = $date->dayOfWeekIso;
            $checkoutEnd = ($dow <= 5) ? ($checkoutEndMap[$dow] ?? null) : null;

            $tap = AttendanceStatus::isTAP(
                $a?->check_in,
                $a?->check_out,
                $checkoutEnd,
                $key,
                $tz
            );

            $statusFinal = AttendanceStatus::buildFinal($main, $pc, $tap);

            $tanggalLabel = $date->translatedFormat('d M Y');

            $lokasiText = '-';
            $lokasiUrl  = null;

            if ($a) {
                $hasLocation = filled($a->location);
                $hasName     = filled($a->location_name);

                $isUrl = $hasLocation && (
                    str_contains($a->location, 'http') ||
                    str_contains($a->location, 'maps')
                );

                $lokasiText = $hasName
                    ? $a->location_name
                    : 'KANTOR BAPPERIDA';

                if ($hasLocation) {
                    $lokasiUrl = $isUrl
                        ? $a->location
                        : "https://maps.google.com/?q=" . urlencode($a->location);
                }
            }

            $rows[] = [
                'tanggal_label'  => $tanggalLabel,
                'checkin_label'  => $checkin,
                'checkout_label' => $checkout,
                'status'         => $statusFinal,
                'status_label'   => $statusFinal,
                'location_text'  => $lokasiText,
                'location_url'   => $lokasiUrl,
                'location'       => $lokasiText,
                'isHoliday'      => $isHoliday,
            ];
        }

        $left  = array_slice($rows, 0, 16);
        $right = array_slice($rows, 16);

        $bulanLabel = $this->bulanIndo($month);

        $f4 = [0, 0, 595.28, 935.43];

        return Pdf::loadView('admin.students.presensi_pdf', compact(
            'student',
            'about',
            'year',
            'bulanLabel',
            'left',
            'right'
        ))
            ->setPaper($f4, 'portrait')
            ->download("DAFTAR-HADIR-MAGANG-{$student->nim}-{$bulanLabel}-{$year}.pdf");
    }

    private function getIndonesiaHolidaysMerged(int $year): array
    {
        $cacheKey = "holidays:merged:id:$year";

        return Cache::remember($cacheKey, now()->addHours(12), function () use ($year) {

            $manualOut = [];
            try {
                $manual = require app_path('Support/IndonesianHolidays.php');
                if (is_array($manual) && isset($manual[$year]) && is_array($manual[$year])) {
                    $manualOut = $manual[$year];
                }
            } catch (\Throwable $e) {
                Log::warning("Holiday manual file failed", ['year' => $year]);
            }

            $apiOut = [];
            try {
                $url = "https://date.nager.at/api/v3/PublicHolidays/{$year}/ID";

                $res = Http::timeout(15)
                    ->withHeaders([
                        'Accept' => 'application/json',
                        'User-Agent' => 'Laravel-Presensi/1.0',
                    ])
                    ->get($url);

                if ($res->successful()) {
                    $json = $res->json();
                    if (is_array($json)) {
                        foreach ($json as $h) {
                            if (isset($h['date'])) {
                                $apiOut[$h['date']] = $h['localName'] ?? 'Hari Libur';
                            }
                        }
                    }
                }
            } catch (\Throwable $e) {
            }

            return array_merge($manualOut, $apiOut);
        });
    }

    private function bulanIndo(int $m): string
    {
        return [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember'
        ][$m] ?? '-';
    }
}
