<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class DashboardController extends Controller
{
public function index(Request $request) // 👈 Tambahkan Request $request
{
    $user = Auth::user();

    // ====================================================
    // LOGIKA 1: JIKA MASTER ADMIN / SUPER ADMIN
    // ====================================================
    if ($user->role === 'super_admin' || $user->role === 'master') {

        // 1. Ambil Daftar Instansi untuk Dropdown Filter
        // Kita ambil dari user admin yang memiliki kategori_instansi
        $listInstansi = User::whereNotNull('kategori_instansi')
                            ->where('kategori_instansi', '!=', 'MASTER')
                            ->distinct()
                            ->pluck('kategori_instansi');

        // 2. Siapkan Query Dasar Mahasiswa
        $mahasiswaQuery = User::where('role', 'mahasiswa');
        $adminBidangQuery = User::where('role', 'admin_bidang');

        // 3. Cek Apakah Ada Filter
        $selectedInstansi = $request->query('instansi');

        if ($selectedInstansi) {
            // Jika ada filter, persempit pencarian
            $mahasiswaQuery->where('kategori_instansi', $selectedInstansi);
            $adminBidangQuery->where('kategori_instansi', $selectedInstansi);
        }

        // 4. Hitung Statistik (Sesuai Filter)
        $stats = [
            'total_mahasiswa'    => $mahasiswaQuery->count(), // Ikut terfilter
            'total_admin_bidang' => $adminBidangQuery->count(), // Ikut terfilter

            // Super Admin & Dosen biasanya global, jadi tidak perlu difilter instansi
            'total_super_admin'  => User::where('role', 'super_admin')->count(),
            'total_dosen'        => User::where('role', 'dosen')->count(),
        ];

        // 5. Ambil Data Tabel (Sesuai Filter)
        $mahasiswa = $mahasiswaQuery->latest()->paginate(10)->withQueryString(); // withQueryString agar pagination tidak mereset filter

        // Tampilkan View Dashboard Master
        return view('dashboard.master', compact('stats', 'mahasiswa', 'listInstansi', 'selectedInstansi'));
    }

    // ... (Logika role lain tetap sama seperti sebelumnya) ...

    // ... Copy paste logika role lain dibawah sini ...
    if ($user->role === 'admin_bidang' || $user->role === 'admin') {
        return redirect()->route('admin.dashboard');
    }
    if ($user->role === 'dosen' || $user->role === 'pembimbing') {
        return redirect()->route('pembimbing.dashboard');
    }
    if ($user->role === 'mahasiswa') {
        return view('dashboard');
    }
    abort(403, 'Anda tidak memiliki akses ke halaman ini.');
}
}
