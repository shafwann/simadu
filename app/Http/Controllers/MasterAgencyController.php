<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\AboutSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class MasterAgencyController extends Controller
{
    public function index()
    {
        // ✅ FILTER: Hanya ambil 'Administrator Sistem' (Super Admin) dari setiap instansi
        $agencies = User::where('role', 'admin')
            ->where('bidang', 'Administrator Sistem') // Agar admin divisi tidak muncul
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('master.agencies.index', compact('agencies'));
    }

    public function create()
    {
        return view('master.agencies.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'kategori_instansi' => 'required|string|unique:users,kategori_instansi',
            'password' => 'required|min:8',
        ]);

        DB::transaction(function () use ($request) {
            $kategoriUpper = strtoupper($request->kategori_instansi);

            User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'admin',
                'kategori_instansi' => $kategoriUpper,
                'bidang' => 'Administrator Sistem', // Menandakan dia Super Admin
                'status' => 'aktif',
            ]);

            AboutSetting::create([
                'kategori_instansi' => $kategoriUpper,
                'app_name' => $kategoriUpper,
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
            ->with('success', 'Instansi & Super Admin berhasil dibuat!');
    }

    // ✅ METHOD EDIT
    public function edit($id)
    {
        $agency = User::findOrFail($id);
        return view('master.agencies.edit', compact('agency'));
    }

    // ✅ METHOD UPDATE
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'password' => 'nullable|min:8', // Password opsional saat edit
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
        ];

        // Jika password diisi, update. Jika kosong, biarkan lama.
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('master.agencies.index')
            ->with('success', 'Data Super Admin berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);

        if ($user->kategori_instansi) {
            AboutSetting::where('kategori_instansi', $user->kategori_instansi)->delete();
        }

        $user->delete();

        return back()->with('success', 'Instansi berhasil dihapus.');
    }
}
