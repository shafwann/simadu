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
    public function index(Request $request)
    {
        // Mengambil ID user menggunakan Facade yang lebih dikenal oleh editor
        $userId = \Illuminate\Support\Facades\Auth::id();
        $sort = $request->query('sort', 'tanggal');
        $direction = $request->query('direction', 'desc');

        if (!in_array($sort, ['tanggal', 'status'], true)) {
            $sort = 'tanggal';
        }

        if (!in_array($direction, ['asc', 'desc'], true)) {
            $direction = 'desc';
        }

        $baseQuery = \App\Models\Logbook::where('user_id', $userId);

        $logbooksQuery = (clone $baseQuery);

        if ($sort === 'status') {
            $logbooksQuery->orderByRaw(
                "CASE
                    WHEN is_rejected = 1 THEN 2
                    WHEN is_validated = 1 THEN 3
                    ELSE 1
                END {$direction}"
            );
            $logbooksQuery
                ->orderBy('tanggal', 'desc')
                ->orderBy('updated_at', 'desc');
        } else {
            $logbooksQuery
                ->orderBy('tanggal', $direction)
                ->orderBy('updated_at', 'desc');
        }

        $logbooks = $logbooksQuery->paginate(10)->withQueryString();

        // Ambil tahun unik dari logbook user
        $tahunList = (clone $baseQuery)->pluck('tanggal')
            ->map(fn($d) => Carbon::parse($d)->year)
            ->unique()
            ->sortDesc()
            ->values(); // supaya 2025,2026 urut

        return view('logbook.index', compact('logbooks', 'tahunList', 'sort', 'direction'));
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
        ], [
            'tanggal.required' => 'Tanggal logbook wajib diisi.',
            'tanggal.date' => 'Format tanggal logbook tidak valid.',
            'kegiatan.required' => 'Deskripsi kegiatan wajib diisi.',
            'kegiatan.min' => 'Deskripsi kegiatan minimal 5 karakter.',
        ]);

        try {
            // 2. Simpan ke database
            Logbook::create([
                'user_id' => \Illuminate\Support\Facades\Auth::id(),
                'tanggal' => $request->tanggal,
                'kegiatan' => $request->kegiatan,
                'is_validated' => false, // Default awal belum divalidasi
            ]);

            // 3. Cek apakah ada pending checkout
            if (session('pending_checkout')) {
                session()->forget('pending_checkout');
                session([
                    'face_intended_action' => 'check-out',
                ]);
                return redirect()->route('face.verify')
                    ->with('success', 'Logbook berhasil disimpan! Silakan verifikasi wajah untuk absen pulang.');
            }

            // 4. Kembalikan ke halaman sebelumnya dengan pesan sukses
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

        $request->validate([
            'tanggal' => 'required|date',
            'kegiatan' => 'required|string|min:5',
        ]);

        $updateData = [
            'tanggal' => $request->tanggal,
            'kegiatan' => $request->kegiatan,
        ];

        // Jika ditolak, reset status rejected saat update
        if ($logbook->is_rejected) {
            $updateData['is_rejected'] = false;
            $updateData['rejected_at'] = null;
            $updateData['rejected_note'] = null;
            $updateData['is_validated'] = false;
            $message = 'Logbook berhasil diperbarui dan diajukan ulang.';
        } else {
            // Untuk pending dan validated, tetap di status sebelumnya
            $message = 'Logbook berhasil diperbarui.';
        }

        $logbook->update($updateData);

        return redirect()->route('logbook.index')->with('success', $message);
    }

    public function destroy(Logbook $logbook)
    {
        if ($logbook->user_id !== Auth::id()) abort(403);

        $logbook->delete();

        return redirect()->route('logbook.index')->with('success', 'Logbook berhasil dihapus.');
    }
}
