<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Attendance;
use App\Models\Logbook;
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

        return view('admin.dashboard', compact('activeStudents', 'finishedStudents', 'pendingLogbooks', 'search'));
    }

    
    public function validateLogbook($id)
    {
        $logbook = Logbook::findOrFail($id);
        $logbook->update(['is_validated' => true]);

        return redirect()->back()->with('success', 'Logbook berhasil divalidasi!');
    }
}
