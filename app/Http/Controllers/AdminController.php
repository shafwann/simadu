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
            return redirect('/dashboard')->with('error', 'Anda tidak memiliki akses admin.');
        }

        // 2. Logika Pencarian
        $search = $request->input('search');

        // Ambil semua mahasiswa untuk statistik
        $users = User::where('role', 'mahasiswa')->get();

        // Logbook yang butuh validasi (dengan Fitur Search)
        $pendingLogbooks = Logbook::with('user')
            ->where('is_validated', false)
            ->when($search, function ($query, $search) {
                return $query->whereHas('user', function ($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%');
                });
            })
            ->orderBy('tanggal', 'desc')
            ->get();

        // 3. Cukup gunakan SATU return view di akhir fungsi
        return view('admin.dashboard', compact('users', 'pendingLogbooks', 'search'));
    }

    public function validateLogbook($id)
    {
        $logbook = Logbook::findOrFail($id);
        $logbook->update(['is_validated' => true]);

        return redirect()->back()->with('success', 'Logbook berhasil divalidasi!');
    }
}
