<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\AboutSetting;
use App\Models\Logbook;
use App\Models\User; // atau Student (sesuaikan model kamu)
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AdminLogbookController extends Controller
{
    public function exportPdf(Request $request, $student)
    {
        // sesuaikan model: kalau mahasiswa itu User, pakai User::findOrFail
        $student = User::findOrFail($student);

        $about = AboutSetting::first();

        // default bulan & tahun sekarang
        $bulan = (int) $request->get('month', now()->month);
        $tahun = (int) $request->get('year', now()->year);

        $logbooks = Logbook::where('user_id', $student->id)
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->orderBy('tanggal', 'asc')
            ->get();

        $bulanIndo = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        $bulanLabel = $bulanIndo[$bulan] ?? '-';

        $pdf = Pdf::loadView('admin.logbook.pdf', [
            'student'    => $student,
            'about'      => $about,
            'year'       => $tahun,
            'bulanLabel' => $bulanLabel,
            'logbooks'   => $logbooks,
        ])->setPaper('a4', 'portrait');

        $safeName = preg_replace('/[^A-Za-z0-9_\-]/', '_', $student->name);

        return $pdf->download("LOGBOOK_{$safeName}_{$bulan}_{$tahun}.pdf");
    }
}
