<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Attendance;
use App\Models\Logbook;
use App\Models\Laporan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // <--- PERBAIKAN: Tambahkan import ini

class AdminController extends Controller
{
    public function index(Request $request)
    {
        // 1. Cek keamanan Role
        if (Auth::user()->role !== 'admin') {
            return redirect()->dashboard();
        }

        // 2. Logika Pencarian Logbook
        $search = $request->input('search');

        // 3. --- LOGIKA STATISTIK BARU ---
        // Hitung total mahasiswa aktif
        $activeStudents = User::where('role', 'mahasiswa')
                            ->where('status', 'aktif')
                            ->count();

        // Hitung total mahasiswa selesai/nonaktif
        $finishedStudents = User::where('role', 'mahasiswa')
                            ->where('status', 'nonaktif')
                            ->count();

        // Ambil logbook pending (tetap sama)
        $pendingLogbooks = Logbook::with('user')
            ->where('is_validated', false)
            ->when($search, function ($query, $search) {
                return $query->whereHas('user', function ($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%');
                });
            })
            ->orderBy('tanggal', 'desc')
            ->get();

        // Ambil semua mahasiswa dengan laporan
        $students = User::with('laporans')
            ->where('role', 'mahasiswa')
            ->where('status', '!=', 'aktif') // <-- filter hanya nonaktif
            ->orderBy('name')
            ->get();

        //pending validasi laporan
        $pendingLaporans = Laporan::with('user')
            ->where('is_validated', false)
            ->latest()
            ->get();

        //card validasi laporan
        // $pendingLaporans = Laporan::where('is_validated', false)->get();



        return view('admin.dashboard', compact('activeStudents', 'finishedStudents', 'pendingLogbooks', 'pendingLaporans', 'search', 'students'));
    }


    public function validateLogbook($id)
    {
        $logbook = Logbook::findOrFail($id);
        $logbook->update(['is_validated' => true]);

        return redirect()->back()->with('success', 'Logbook berhasil divalidasi!');
    }

    public function validateLaporan($id)
    {
        $laporan = Laporan::findOrFail($id);
        $laporan->update(['is_validated' => true]);

        return redirect()->back()->with('success', 'Laporan berhasil divalidasi');
    }

}
