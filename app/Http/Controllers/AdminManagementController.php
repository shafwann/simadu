<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class AdminManagementController extends Controller
{
    // Cek apakah user adalah SUPER ADMIN
    private function checkSuperAdmin()
    {
        if (Auth::user()->bidang !== 'Administrator Sistem') {
            abort(403, 'Akses Ditolak. Hanya Administrator Sistem yang boleh mengakses halaman ini.');
        }
    }

    // 1. LIHAT DAFTAR ADMIN
    public function index()
    {
        $this->checkSuperAdmin();

        // Ambil semua user dengan role 'admin', termasuk diri sendiri untuk reference
        $admins = User::where('role', 'admin')
            ->orderBy('bidang')
            ->orderBy('name')
            ->get();

        return view('admin.manage.index', compact('admins'));
    }

    // 2. FORM TAMBAH ADMIN
    public function create()
    {
        $this->checkSuperAdmin();
        return view('admin.manage.create');
    }

    // 3. SIMPAN ADMIN BARU
    public function store(Request $request)
    {
        $this->checkSuperAdmin();

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'bidang' => ['required', 'string'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'admin',
            'bidang' => $request->bidang,
            'status' => 'aktif',
        ]);

        return redirect()->route('admin.manage.index')
            ->with('success', 'Admin baru berhasil ditambahkan!');
    }

    // 4. HAPUS ADMIN
    public function destroy($id)
    {
        $this->checkSuperAdmin();

        $admin = User::findOrFail($id);

        // Jangan hapus diri sendiri
        if ($admin->id === Auth::id()) {
            return back()->with('error', 'Tidak dapat menghapus akun sendiri!');
        }

        // Jangan hapus sesama Super Admin jika ada logic tambahan
        if ($admin->bidang === 'Administrator Sistem') {
            return back()->with('error', 'Tidak dapat menghapus sesama Administrator Sistem!');
        }

        $admin->delete();

        return back()->with('success', 'Data Admin berhasil dihapus.');
    }
}
