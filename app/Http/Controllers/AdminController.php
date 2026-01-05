<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Attendance;
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
        // Pastikan ejaannya sama persis dengan yang ada di Database/Migration
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
                // Jika bukan super admin, filter berdasarkan bidang admin tersebut
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
        // QUERY 3: Logbook Pending
        // =========================================
        // Kita pakai whereHas('user') karena logbook milik user.
        // Kita harus cek bidang si user (mahasiswa) tersebut.
        $pendingLogbooks = Logbook::with('user')
            ->where('is_validated', false)
            ->whereHas('user', function ($q) use ($isSuperAdmin, $adminBidang, $search) {
                // Filter Bidang
                if (!$isSuperAdmin) {
                    $q->where('bidang', $adminBidang);
                }
                // Filter Pencarian Nama
                if ($search) {
                    $q->where('name', 'like', '%' . $search . '%');
                }
            })
            ->orderBy('tanggal', 'desc')
            ->get();

        // =========================================
        // QUERY 4: Laporan Pending
        // =========================================
        $pendingLaporans = Laporan::with('user')
            ->where('is_validated', false)
            ->whereHas('user', function ($q) use ($isSuperAdmin, $adminBidang) {
                if (!$isSuperAdmin) {
                    $q->where('bidang', $adminBidang);
                }
            })
            ->latest()
            ->get();

        // =========================================
        // QUERY 5: Daftar Mahasiswa (Tabel Bawah)
        // =========================================
        // Menampilkan mahasiswa nonaktif (alumni) atau semua jika perlu
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

        // Opsional: Validasi keamanan ekstra
        // (Pastikan admin bidang tidak memvalidasi logbook bidang lain via URL hack)
        $user = Auth::user();
        if ($user->bidang !== 'Administrator Sistem' && $logbook->user->bidang !== $user->bidang) {
            return back()->with('error', 'Anda tidak berhak memvalidasi mahasiswa dari bidang lain.');
        }

        $logbook->update(['is_validated' => true]);

        return redirect()->back()->with('success', 'Logbook berhasil divalidasi!');
    }

    public function validateLaporan($id)
    {
        $laporan = Laporan::findOrFail($id);

        // Opsional: Validasi keamanan ekstra
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
}
