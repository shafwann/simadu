<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Attendance;
use App\Models\Logbook;
use App\Models\AboutSetting;
use App\Support\AttendanceStatus;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class PembimbingPdfController extends Controller
{
    private function ensureMahasiswaBimbingan(User $mahasiswa): void
    {
        $dosen = Auth::user();

        $isBimbingan = DB::table('dosen_mahasiswa')
            ->where('dosen_id', $dosen->id)
            ->where('mahasiswa_id', $mahasiswa->id)
            ->exists();

        if (!$isBimbingan) {
            abort(403, 'Mahasiswa ini bukan bimbingan Anda.');
        }
    }

    public function presensiPdf(Request $request, $id)
    {
        $month = (int) $request->get('month', now()->month);
        $year  = (int) $request->get('year', now()->year);

        if ($month < 1 || $month > 12) $month = now()->month;
        if ($year < 2000 || $year > 2100) $year = now()->year;

        $student = User::findOrFail($id);
        $this->ensureMahasiswaBimbingan($student);

        $about = AboutSetting::first();

        $start = Carbon::create($year, $month, 1, 0, 0, 0, 'Asia/Jakarta');
        $daysInMonth = $start->daysInMonth;

        $attendances = Attendance::where('user_id', $student->id)
            ->whereMonth('tanggal', $month)
            ->whereYear('tanggal', $year)
            ->get()
            ->keyBy(fn ($a) => Carbon::parse($a->tanggal)->format('Y-m-d'));

        $holidays = $this->getIndonesiaHolidaysRealtime($year);

        // jam checkout selesai per hari (untuk TAP)
        $checkoutEndMap = [
            1 => $about?->senin_checkout_selesai,
            2 => $about?->selasa_checkout_selesai,
            3 => $about?->rabu_checkout_selesai,
            4 => $about?->kamis_checkout_selesai,
            5 => $about?->jumat_checkout_selesai,
        ];

        $rows = [];
        for ($d = 1; $d <= $daysInMonth; $d++) {
            $date = Carbon::create($year, $month, $d, 0, 0, 0, 'Asia/Jakarta');
            $key  = $date->format('Y-m-d');

            $isHoliday = $date->isWeekend() || isset($holidays[$key]);
            $a = $attendances[$key] ?? null;

            $checkin  = (!empty($a?->check_in))  ? Carbon::parse($a->check_in)->format('H:i')  : '-';
            $checkout = (!empty($a?->check_out)) ? Carbon::parse($a->check_out)->format('H:i') : '-';

            // ===== STATUS FINAL (H/T + PC + TAP) =====
            $main = AttendanceStatus::mainCode($a?->status);
            $pc   = AttendanceStatus::isPC($a?->keterangan);

            $dow = $date->dayOfWeekIso; // 1..7
            $checkoutEnd = ($dow <= 5) ? ($checkoutEndMap[$dow] ?? null) : null;

            $tap = AttendanceStatus::isTAP(
                $a?->check_in,
                $a?->check_out,
                $checkoutEnd,
                $key,
                'Asia/Jakarta'
            );

            $statusFinal = AttendanceStatus::buildFinal($main, $pc, $tap);

            $tanggalLabel = $date->translatedFormat('d M Y');

            // ===== LOKASI =====
            $lokasiText = '-';
            $lokasiUrl  = null;

            if ($a) {
                if (!empty($a->location_name)) {
                    $lokasiText = $a->location_name;
                } elseif (in_array($a->status, ['Hadir', 'Telat'], true)) {
                    $lokasiText = 'KANTOR BAPPERIDA';
                } else {
                    $lokasiText = $statusFinal;
                }

                if (!empty($a->location) && (str_contains($a->location, 'http') || str_contains($a->location, 'maps'))) {
                    $lokasiUrl = $a->location;
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

        // ✅ F4 points: 595.28 x 935.43
        $f4 = [0, 0, 595.28, 935.43];

        return Pdf::loadView('admin.students.presensi_pdf', compact(
            'student', 'about', 'year', 'bulanLabel', 'left', 'right'
        ))
            ->setPaper($f4, 'portrait')
            ->download("DAFTAR-HADIR-MAGANG-{$student->nim}-{$bulanLabel}-{$year}.pdf");
    }

    public function logbookPdf(Request $request, $id)
    {
        $month = (int) $request->get('month', now()->month);
        $year  = (int) $request->get('year', now()->year);

        if ($month < 1 || $month > 12) $month = now()->month;
        if ($year < 2000 || $year > 2100) $year = now()->year;

        $student = User::findOrFail($id);
        $this->ensureMahasiswaBimbingan($student);

        $about = AboutSetting::first();

        $logbooks = Logbook::where('user_id', $student->id)
            ->whereMonth('tanggal', $month)
            ->whereYear('tanggal', $year)
            ->orderBy('tanggal', 'asc')
            ->get();

        $bulanLabel = $this->bulanIndo($month);

        $pdf = Pdf::loadView('admin.logbook.pdf', [
            'student'    => $student,
            'about'      => $about,
            'year'       => $year,
            'bulanLabel' => $bulanLabel,
            'logbooks'   => $logbooks,
        ])->setPaper('a4', 'portrait');

        $safeName = preg_replace('/[^A-Za-z0-9_\-]/', '_', $student->name);

        return $pdf->download("LOGBOOK_{$safeName}_{$bulanLabel}_{$year}.pdf");
    }

    private function getIndonesiaHolidaysRealtime(int $year): array
    {
        try {
            $manual = require app_path('Support/IndonesianHolidays.php');
            if (isset($manual[$year])) return $manual[$year];
        } catch (\Throwable $e) {
            // ignore
        }

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
        ][$m] ?? '-';
    }
}
