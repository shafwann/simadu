<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class AdminManagementController extends Controller
{
    /**
     * Cek apakah user adalah SUPER ADMIN
     */
    private function checkSuperAdmin(): void
    {
        if (
            Auth::user()->role !== 'admin' ||
            Auth::user()->bidang !== 'Administrator Sistem'
        ) {
            abort(403, 'Akses ditolak. Hanya Super Admin yang boleh mengakses.');
        }
    }

    /**
     * 1. DAFTAR ADMIN (HANYA 1 INSTANSI)
     */
    public function index()
    {
        $this->checkSuperAdmin();

        $admins = User::where('role', 'admin')
            ->where('kategori_instansi', Auth::user()->kategori_instansi)
            ->orderBy('bidang')
            ->orderBy('name')
            ->get();

        return view('admin.manage.index', compact('admins'));
    }

    /**
     * 2. FORM TAMBAH ADMIN
     */
    public function create()
    {
        $this->checkSuperAdmin();
        $bidangOptions = \App\Models\AboutSetting::bidangOptions();

        return view('admin.manage.create', compact('bidangOptions'));
    }

    /**
     * 3. SIMPAN ADMIN BARU
     */
    public function store(Request $request)
    {
        $this->checkSuperAdmin();

        $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'unique:users'],
            'bidang'   => ['required', 'string'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        User::create([
            'name'              => $request->name,
            'email'             => $request->email,
            'password'          => Hash::make($request->password),
            'role'              => 'admin',
            'bidang'            => $request->bidang,
            'status'            => 'aktif',

            // 🔐 OTOMATIS IKUT SUPER ADMIN
            'kategori_instansi' => Auth::user()->kategori_instansi,
        ]);

        return redirect()
            ->route('admin.manage.index')
            ->with('success', 'Admin bidang berhasil ditambahkan.');
    }

    /**
     * 4. HAPUS ADMIN
     */
    public function destroy($id)
    {
        $this->checkSuperAdmin();

        $admin = User::findOrFail($id);

        // ❌ Tidak boleh hapus diri sendiri
        if ($admin->id === Auth::id()) {
            return back()->with('error', 'Tidak dapat menghapus akun sendiri.');
        }

        // ❌ Tidak boleh hapus Super Admin
        if ($admin->bidang === 'Administrator Sistem') {
            return back()->with('error', 'Tidak dapat menghapus Super Admin.');
        }

        // 🔒 Cegah beda instansi
        if ($admin->kategori_instansi !== Auth::user()->kategori_instansi) {
            abort(403, 'Akses ditolak. Beda instansi.');
        }

        $admin->delete();

        return back()->with('success', 'Admin berhasil dihapus.');
    }
}
