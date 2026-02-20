<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Logbook;
use App\Models\Laporan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        // 1. Cek keamanan Role
        if ($user->role !== 'admin') {
            return redirect()->route('dashboard');
        }

        // 2. Tentukan apakah dia Super Admin (Administrator Sistem) atau Admin Bidang
        $isSuperAdmin = $user->bidang === 'Administrator Sistem';
        $adminBidang  = $user->bidang;

        // 3. Logika Pencarian Nama Mahasiswa
        $search = $request->input('search');

        // =========================================
        // QUERY 1: Total Mahasiswa Aktif
        // =========================================
        $activeStudents = User::where('role', 'mahasiswa')
            ->where('status', 'aktif')
            ->when(!$isSuperAdmin, function ($q) use ($adminBidang) {
                return $q->where('bidang', $adminBidang);
            })
            ->count();

        // =========================================
        // QUERY 2: Total Mahasiswa Selesai/Nonaktif
        // =========================================
        $finishedStudents = User::where('role', 'mahasiswa')
            ->where('status', 'nonaktif')
            ->when(!$isSuperAdmin, function ($q) use ($adminBidang) {
                return $q->where('bidang', $adminBidang);
            })
            ->count();

        // =========================================
        // QUERY 3: Logbook Pending (DENGAN PAGINATION)
        // =========================================
        $pendingLogbooks = Logbook::with('user')
            ->where('is_validated', false)
            ->where('is_rejected', false) // ✅ tambahan filter biar yang ditolak hilang
            ->whereHas('user', function ($q) use ($isSuperAdmin, $adminBidang, $search) {

                if (!$isSuperAdmin) {
                    $q->where('bidang', $adminBidang);
                }

                if ($search) {
                    $q->where('name', 'like', '%' . $search . '%');
                }
            })
            ->orderBy('tanggal', 'desc')
            ->paginate(5, ['*'], 'logbook_page');

        // =========================================
        // QUERY 4: Laporan Pending (DENGAN PAGINATION)
        // =========================================
        // Menggunakan paginate(5) dengan nama halaman 'laporan_page'
        $pendingLaporans = Laporan::with('user')
            ->where('is_validated', false)
            ->where('is_rejected', false) // tambahan filter biar yang ditolak hilang
            ->whereHas('user', function ($q) use ($isSuperAdmin, $adminBidang) {
                if (!$isSuperAdmin) {
                    $q->where('bidang', $adminBidang);
                }
            })
            ->latest()
            ->paginate(5, ['*'], 'laporan_page'); // ✅ Ganti get() jadi paginate()

        // =========================================
        // QUERY 5: Daftar Mahasiswa (Tabel Bawah - Opsional)
        // =========================================
        $students = User::with('laporans')
            ->where('role', 'mahasiswa')
            ->where('status', '!=', 'aktif')
            ->when(!$isSuperAdmin, function ($q) use ($adminBidang) {
                return $q->where('bidang', $adminBidang);
            })
            ->when($search, function ($q) use ($search) {
                return $q->where('name', 'like', '%' . $search . '%');
            })
            ->orderBy('name')
            ->get();

        return view('admin.dashboard', compact(
            'activeStudents',
            'finishedStudents',
            'pendingLogbooks',
            'pendingLaporans',
            'search',
            'students'
        ));
    }

    public function validateLogbook($id)
    {
        $logbook = Logbook::findOrFail($id);
        $user = Auth::user();

        if ($user->bidang !== 'Administrator Sistem' && $logbook->user->bidang !== $user->bidang) {
            return back()->with('error', 'Anda tidak berhak memvalidasi mahasiswa dari bidang lain.');
        }

        $logbook->update(['is_validated' => true]);

        return redirect()->back()->with('success', 'Logbook berhasil divalidasi!');
    }

    public function rejectLogbook(Request $request, $id)
    {
        $request->validate([
            'rejected_note' => 'required|string|min:3',
        ]);

        $logbook = Logbook::with('user')->findOrFail($id);
        $user = Auth::user();

        // proteksi bidang (sama seperti validate)
        if ($user->bidang !== 'Administrator Sistem' && $logbook->user->bidang !== $user->bidang) {
            return back()->with('error', 'Anda tidak berhak menolak mahasiswa dari bidang lain.');
        }

        $logbook->update([
            'is_rejected' => true,
            'rejected_at' => now(),
            'rejected_note' => $request->input('rejected_note'), // opsional
            'is_validated' => false, // biar jelas statusnya
        ]);

        return back()->with('success', 'Logbook berhasil ditolak.');
    }

    public function destroyLogbook($id)
    {
        $logbook = Logbook::with('user')->findOrFail($id);
        $user = Auth::user();

        if ($user->bidang !== 'Administrator Sistem' && $logbook->user->bidang !== $user->bidang) {
            return back()->with('error', 'Anda tidak berhak menghapus logbook dari bidang lain.');
        }

        $logbook->delete();
        return back()->with('success', 'Logbook berhasil dihapus.');
    }


    // ✅ TAMBAHAN: VALIDASI SEMUA LOGBOOK (sesuai bidang admin & optional search)
    public function validateAllLogbook(Request $request)
    {
        $user = Auth::user();

        // Ambil search seperti di dashboard
        $search = $request->input('search');

        // Query logbook pending
        $query = Logbook::where('is_validated', false)
            ->whereHas('user', function ($q) use ($user, $search) {

                // ✅ jika bukan Super Admin, batasi bidang
                if ($user->bidang !== 'Administrator Sistem') {
                    $q->where('bidang', $user->bidang);
                }

                // ✅ kalau ada search, batasi nama mahasiswa
                if (!empty($search)) {
                    $q->where('name', 'like', '%' . $search . '%');
                }
            });

        $count = $query->count();

        // Update massal
        $query->update([
            'is_validated' => true
        ]);

        return redirect()->back()->with('success', "Berhasil validasi semua logbook ({$count} data)!");
    }

    public function validateLaporan($id)
    {
        $laporan = Laporan::findOrFail($id);
        $user = Auth::user();

        if ($user->bidang !== 'Administrator Sistem' && $laporan->user->bidang !== $user->bidang) {
            return back()->with('error', 'Anda tidak berhak memvalidasi laporan dari bidang lain.');
        }

        $laporan->update([
            'is_validated' => true,
            'validated_at' => now()
        ]);

        return redirect()->back()->with('success', 'Laporan berhasil divalidasi');
    }

    public function rejectLaporan(Request $request, $id)
    {
        $request->validate([
            'rejected_note' => 'required|string|min:3',
        ]);

        $laporan = Laporan::with('user')->findOrFail($id);
        $user = Auth::user();

        if ($user->bidang !== 'Administrator Sistem' && $laporan->user->bidang !== $user->bidang) {
            return back()->with('error', 'Anda tidak berhak menolak laporan dari bidang lain.');
        }

        $laporan->update([
            'is_rejected' => true,
            'rejected_at' => now(),
            'rejected_note' => $request->input('rejected_note'),
            'is_validated' => false,
        ]);

        return back()->with('success', 'Laporan berhasil ditolak.');
    }

    public function destroyLaporan($id)
    {
        $laporan = Laporan::with('user')->findOrFail($id);
        $user = Auth::user();

        if ($user->bidang !== 'Administrator Sistem' && $laporan->user->bidang !== $user->bidang) {
            return back()->with('error', 'Anda tidak berhak menghapus laporan dari bidang lain.');
        }

        $laporan->delete();
        return back()->with('success', 'Laporan berhasil dihapus.');
    }
}
