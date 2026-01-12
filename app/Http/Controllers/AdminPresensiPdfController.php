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
            $checkin  = (!empty($a?->check_in))  ? Carbon::parse($a->check_in)->format('H:i')  : '-';
            $checkout = (!empty($a?->check_out)) ? Carbon::parse($a->check_out)->format('H:i') : '-';

            // tanggal label seperti contoh
            $tanggalLabel = $date->translatedFormat('d M Y');

            // --- LOGIKA BARU UNTUK PDF ---
            $lokasiText = '-';
            $lokasiUrl  = null;

            if ($a) {
                // 1. Tentukan TEKS yang tampil (Nama Lokasi)
                if (!empty($a->location_name)) {
                    $lokasiText = $a->location_name;
                }
                elseif (in_array($a->status, ['Hadir', 'Telat'])) {
                    $lokasiText = 'KANTOR BAPPERIDA';
                }
                else {
                    $lokasiText = strtoupper($a->status);
                }

                // 2. Tentukan URL LINK (Link Maps dari kolom location)
                if (!empty($a->location) && (str_contains($a->location, 'http') || str_contains($a->location, 'maps'))) {
                    $lokasiUrl = $a->location;
                }
            }

            $rows[] = [
                'tanggal_label'  => $tanggalLabel,
                'checkin_label'  => $checkin,
                'checkout_label' => $checkout,
                'status'         => $status,
                'status_label'   => $status,

                // ✅ Update disini
                'location_text'  => $lokasiText,
                'location_url'   => $lokasiUrl,
                'location'       => $lokasiText, // Fallback key

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
        // ... (kode sama, tidak berubah) ...
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
