<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\AboutSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class MasterAgencyController extends Controller
{
    /**
     * Menampilkan daftar Admin Instansi yang ada.
     */
    public function index()
    {
        // Ambil user yang rolenya 'admin' (Admin Instansi)
        // Master Admin bisa melihat semua karena Scope di-bypass untuk role 'master'
        $agencies = User::where('role', 'admin')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('master.agencies.index', compact('agencies'));
    }

    /**
     * Form tambah instansi baru.
     */
    public function create()
    {
        return view('master.agencies.create');
    }

    /**
     * Proses pembuatan Instansi & Admin Baru.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255', // Nama Admin
            'email' => 'required|email|unique:users,email',
            'kategori_instansi' => 'required|string|unique:users,kategori_instansi', // Kategori harus unik agar tidak double admin
            'password' => 'required|min:8',
        ]);

        // Gunakan Transaction agar jika salah satu gagal, semua batal
        DB::transaction(function () use ($request) {

            $kategoriUpper = strtoupper($request->kategori_instansi); // Diseragamkan HURUF BESAR

            // 1. Buat Akun Admin Instansi
            User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'admin', // Ini adalah Super Admin-nya Instansi
                'kategori_instansi' => $kategoriUpper, // KUNCI UTAMA ISOLASI
                'bidang' => 'Administrator Sistem',
                'status' => 'aktif',
            ]);

            // 2. Buat Default AboutSetting untuk Instansi tersebut
            // Ini PENTING supaya dashboard admin baru tidak error saat load logo/judul
            AboutSetting::create([
                'kategori_instansi' => $kategoriUpper,
                'app_name' => $kategoriUpper, // Default nama aplikasi = nama instansi
                'agency_name' => 'Pemerintah Kab. Karanganyar',
                'app_description' => 'Sistem Manajemen Magang ' . $kategoriUpper,
                'nama_instansi' => 'Dinas ' . $kategoriUpper,
                'kepala_instansi' => '-',
                'nip_kepala_instansi' => '-',
                'jam_operasional' => 'Senin-Jumat: 08.00-16.00',
                'radius_meter' => 50,
            ]);

        });

        return redirect()->route('master.agencies.index')
            ->with('success', 'Instansi & Admin berhasil dibuat! Kategori: ' . strtoupper($request->kategori_instansi));
    }

    /**
     * Hapus Instansi (Hapus Adminnya).
     * Hati-hati: User mahasiswa dengan kategori sama mungkin akan yatim piatu (tidak punya admin),
     * tapi datanya tetap aman di database.
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);

        // Hapus juga setting-nya
        if ($user->kategori_instansi) {
            AboutSetting::where('kategori_instansi', $user->kategori_instansi)->delete();
        }

        $user->delete();

        return back()->with('success', 'Admin Instansi berhasil dihapus.');
    }
}
