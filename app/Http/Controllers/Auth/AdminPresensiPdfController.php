<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Attendance;
use App\Models\AboutSetting;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

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

        $rows = [];
        for ($d = 1; $d <= $daysInMonth; $d++) {
            $date = Carbon::create($year, $month, $d);
            $key  = $date->format('Y-m-d');

            // libur = weekend saja (tanpa Yasumi)
            $isWeekend = $date->isSaturday() || $date->isSunday();
            $isHoliday = $isWeekend;

            $a = $attendances[$key] ?? null;

            $status = $a?->status;

            // normalize data lama
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
                'day' => str_pad((string)$d, 2, '0', STR_PAD_LEFT),
                'date' => $key,
                'isHoliday' => $isHoliday,
                'signed' => $signed,
                'mark' => $mark,
            ];
        }

        $left  = array_slice($rows, 0, 16);
        $right = array_slice($rows, 16);

        $bulanLabel = Carbon::create($year, $month, 1)->translatedFormat('F');

        $pdf = Pdf::loadView('admin.students.presensi_pdf', compact(
            'student', 'about', 'year', 'bulanLabel', 'left', 'right'
        ))->setPaper('A4', 'portrait');

        $filename = "DAFTAR-HADIR-MAGANG-{$student->nim}-{$bulanLabel}-{$year}.pdf";
        return $pdf->download($filename);
    }
}
