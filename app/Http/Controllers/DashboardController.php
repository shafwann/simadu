<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User; // ✅ TAMBAH INI

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // 1. Jika Master Admin -> Ke Halaman List Instansi
        if ($user->role === 'master') {
            return redirect()->route('master.agencies.index');
        }

        // 2. Jika Admin Instansi -> Ke Dashboard Admin
        if ($user->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        // 3. Jika Pembimbing -> Ke Dashboard Pembimbing
        if ($user->role === 'pembimbing') {
            return redirect()->route('pembimbing.dashboard');
        }

        // 4. Jika Mahasiswa -> Tampilkan View Dashboard Mahasiswa
        if ($user->role === 'mahasiswa') {
            return view('dashboard'); // File: resources/views/dashboard.blade.php
        }

        // 5. Default Fallback (jika role tidak dikenali)
        abort(403, 'Role tidak dikenali.');
    }
}
