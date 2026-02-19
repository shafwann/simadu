<?php

namespace App\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use App\Models\Logbook;
use App\Models\Student;
use App\Models\AboutSetting;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class LogbookController extends Controller
{
        public function index()
        {
            // Mengambil ID user menggunakan Facade yang lebih dikenal oleh editor
            $userId = \Illuminate\Support\Facades\Auth::id();

            $logbooks = \App\Models\Logbook::where('user_id', $userId)
                                        ->orderBy('tanggal', 'desc')
                                        ->get();

            // Ambil tahun unik dari logbook user
            $tahunList = $logbooks->pluck('tanggal')
                                ->map(fn($d) => Carbon::parse($d)->year)
                                ->unique()
                                ->sortDesc()
                                ->values(); // supaya 2025,2026 urut

            return view('logbook.index', compact('logbooks', 'tahunList'));
        }

        public function store(Request $request)
        {
            // --- CEK STATUS AKTIF ---
            $user = Auth::user();
            if ($user->status !== 'aktif') {
                return redirect()->back()->with('error', 'Akun Anda tidak aktif, tidak bisa mengisi logbook.');
            }

            // 1. Validasi input
            $request->validate([
                'tanggal' => 'required|date',
                'kegiatan' => 'required|string|min:5',
            ]);

            try {
                // 2. Simpan ke database
                Logbook::create([
                    'user_id' => \Illuminate\Support\Facades\Auth::id(),
                    'tanggal' => $request->tanggal,
                    'kegiatan' => $request->kegiatan,
                    'is_validated' => false, // Default awal belum divalidasi
                ]);

                // 3. Kembalikan ke halaman sebelumnya dengan pesan sukses
                return redirect()->route('logbook.index')->with('success', 'Kegiatan berhasil disimpan!');

            } catch (\Exception $e) {
                // Jika ada error database, munculkan di layar
                return redirect()->back()->with('error', 'Gagal menyimpan: ' . $e->getMessage());
            }
        }

        //export logbook pdf
        public function exportPdf(Request $request)
        {
            $user = Auth::user();          // mahasiswa login
            $about = AboutSetting::where('kategori_instansi', $user->kategori_instansi)->first() ?? abort(500, 'Setting instansi belum dikonfigurasi.'); // data instansi

            // Filter bulan & tahun, default bulan ini & tahun ini
            $bulan = $request->get('bulan', date('m'));
            $tahun = $request->get('tahun', date('Y'));

            $logbooks = Logbook::where('user_id', $user->id)
                ->when($bulan, fn($q) => $q->whereMonth('tanggal', $bulan))
                ->when($tahun, fn($q) => $q->whereYear('tanggal', $tahun))
                ->orderBy('tanggal', 'asc')
                ->get();

            // Nama bulan dalam bahasa Indonesia
            $bulanIndo = [
                1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
            ];
            $bulanLabel = $bulanIndo[(int)$bulan] ?? '-';

            $pdf = Pdf::loadView('logbook.pdf', [
                'student'    => $user,
                'about'      => $about,
                'year'       => $tahun,
                'bulanLabel' => $bulanLabel,
                'logbooks'   => $logbooks,
            ]);

            return $pdf->download("LOGBOOK_{$user->name}_{$bulan}_{$tahun}.pdf");
        }
            public function edit(Logbook $logbook)
    {
        if ($logbook->user_id !== Auth::id()) abort(403);
        if (!$logbook->is_rejected) return redirect()->route('logbook.index')->with('error', 'Logbook ini tidak bisa diedit.');

        return view('logbook.edit', compact('logbook'));
    }

    public function update(Request $request, Logbook $logbook)
    {
        if ($logbook->user_id !== Auth::id()) abort(403);
        if (!$logbook->is_rejected) return redirect()->route('logbook.index')->with('error', 'Logbook ini tidak bisa diajukan ulang.');

        $request->validate([
            'tanggal' => 'required|date',
            'kegiatan' => 'required|string|min:5',
        ]);

        $logbook->update([
            'tanggal' => $request->tanggal,
            'kegiatan' => $request->kegiatan,

            // ajukan ulang → balik pending
            'is_rejected' => false,
            'rejected_at' => null,
            'rejected_note' => null,
            'is_validated' => false,
        ]);

        return redirect()->route('logbook.index')->with('success', 'Logbook berhasil diperbarui dan diajukan ulang.');
    }



}