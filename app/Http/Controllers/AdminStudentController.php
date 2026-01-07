<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule; // Penting untuk validasi Enum/Pilihan

class AdminStudentController extends Controller
{
// 1. TAMPILKAN DAFTAR MAHASISWA
    public function index(Request $request)
    {
        $admin = Auth::user();
        $isSuperAdmin = $admin->bidang === 'Administrator Sistem';

        // Ambil input dari URL
        $search = $request->input('search');
        $status = $request->input('status'); // <--- 1. Ambil input status

        $students = User::where('role', 'mahasiswa')
            // Filter Bidang (Kecuali Super Admin)
            ->when(!$isSuperAdmin, function ($query) use ($admin) {
                return $query->where('bidang', $admin->bidang);
            })
            // Filter Pencarian
            ->when($search, function ($query, $search) {
                return $query->where(function($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%')
                      ->orWhere('nim', 'like', '%' . $search . '%')
                      ->orWhere('instansi', 'like', '%' . $search . '%');
                });
            })
            // 2. FILTER STATUS (BARU)
            ->when($status, function ($query, $status) {
                return $query->where('status', $status);
            })
            ->latest()
            ->paginate(10)
            ->withQueryString(); // Agar filter tetap ada saat pindah halaman

        // Jangan lupa kirim variabel $status ke view
        return view('admin.students.index', compact('students', 'search', 'status'));
    }


    // 2. FORM TAMBAH MAHASISWA
    public function create()
    {
        return view('admin.students.create');
    }

    // 3. SIMPAN MAHASISWA BARU
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'nim' => ['required', 'string', 'max:20'],
            'prodi' => ['required', 'string', 'max:100'],
            'instansi' => ['required', 'string', 'max:100'],
            'kegiatan' => ['required', 'string'],
            'fakultas' => ['nullable', 'string'],
            'tanggal_masuk' => ['required', 'date'],
            'tanggal_selesai' => ['required', 'date', 'after:tanggal_masuk'],

            // Validasi Bidang (Pakai Array agar koma aman)
            'bidang' => ['required', Rule::in([
                'Ekonomi',
                'Sekretariat',
                'Sosial & Budaya',
                'Fisik & Prasarana',
                'Penelitian, Pengembangan & Penyusunan Program',
            ])],
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'mahasiswa',
            'bidang' => $request->bidang, // Simpan sesuai input form
            'status' => 'aktif',
            'nim' => $request->nim,
            'prodi' => $request->prodi,
            'instansi' => $request->instansi,
            'fakultas' => $request->fakultas,
            'kegiatan' => $request->kegiatan,
            'tanggal_masuk' => $request->tanggal_masuk,
            'tanggal_selesai' => $request->tanggal_selesai,
        ]);

        return redirect()->route('admin.students.index')
            ->with('success', 'Mahasiswa berhasil ditambahkan.');
    }

    // 4. LIHAT DETAIL
    public function show($id)
    {
        $student = User::with(['attendances', 'logbooks'])->findOrFail($id);

        // Cek Hak Akses
        $admin = Auth::user();
        if ($admin->bidang !== 'Administrator Sistem' && $student->bidang !== $admin->bidang) {
            abort(403, 'Akses Ditolak.');
        }

        return view('admin.students.show', compact('student'));
    }

    // 5. FORM EDIT MAHASISWA (INI YANG BARU DITAMBAHKAN)
    public function edit($id)
    {
        $student = User::findOrFail($id);

        // Cek Hak Akses (Admin biasa hanya boleh edit mahasiswa bidangnya sendiri)
        $admin = Auth::user();
        if ($admin->bidang !== 'Administrator Sistem' && $student->bidang !== $admin->bidang) {
            abort(403, 'Anda tidak berhak mengedit data mahasiswa dari bidang lain.');
        }

        $bidangOptions = [
            'Ekonomi',
            'Sekretariat',
            'Sosial & Budaya',
            'Fisik & Prasarana',
            'Penelitian, Pengembangan & Penyusunan Program',
        ];

        return view('admin.students.edit', compact('student', 'bidangOptions'));
    }

    // 6. PROSES UPDATE MAHASISWA (INI YANG BARU DITAMBAHKAN)
    public function update(Request $request, $id)
    {
        $student = User::findOrFail($id);

        // Cek Hak Akses
        $admin = Auth::user();
        if ($admin->bidang !== 'Administrator Sistem' && $student->bidang !== $admin->bidang) {
            abort(403, 'Akses Ditolak.');
        }

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            // Validasi email unik KECUALI milik user ini sendiri
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($student->id)],
            'nim' => ['required', 'string', 'max:20'],
            'prodi' => ['required', 'string', 'max:100'],
            'instansi' => ['required', 'string', 'max:100'],
            'kegiatan' => ['required', 'string'],
            'fakultas' => ['nullable', 'string'],
            'tanggal_masuk' => ['required', 'date'],
            'tanggal_selesai' => ['required', 'date', 'after:tanggal_masuk'],
            'status' => ['required', 'in:aktif,nonaktif'],

            // Validasi Bidang Update
            'bidang' => ['required', Rule::in([
                'Ekonomi',
                'Sekretariat',
                'Sosial & Budaya',
                'Fisik & Prasarana',
                'Penelitian, Pengembangan & Penyusunan Program',
            ])],
        ]);

        // Update Data Utama
        $student->fill([
            'name' => $request->name,
            'email' => $request->email,
            'nim' => $request->nim,
            'prodi' => $request->prodi,
            'instansi' => $request->instansi,
            'bidang' => $request->bidang,
            'status' => $request->status,
            'fakultas' => $request->fakultas,
            'kegiatan' => $request->kegiatan,
            'tanggal_masuk' => $request->tanggal_masuk,
            'tanggal_selesai' => $request->tanggal_selesai,
        ]);

        // Cek Password (Hanya update jika diisi)
        if ($request->filled('password')) {
            $request->validate([
                'password' => ['confirmed', Rules\Password::defaults()],
            ]);
            $student->password = Hash::make($request->password);
        }

        $student->save();

        return redirect()->route('admin.students.index')
            ->with('success', 'Data mahasiswa berhasil diperbarui.');
    }

    // 7. HAPUS MAHASISWA
    public function destroy($id)
    {
        $student = User::findOrFail($id);

        $admin = Auth::user();
        if ($admin->bidang !== 'Administrator Sistem' && $student->bidang !== $admin->bidang) {
            abort(403, 'Dilarang menghapus mahasiswa dari divisi lain.');
        }

        $student->delete();

        return redirect()->back()->with('success', 'Data mahasiswa berhasil dihapus.');
    }

    // 8. UPDATE STATUS (Tombol Cepat di Show)
    public function updateStatus(Request $request, $id)
    {
        $student = User::findOrFail($id);

        $admin = Auth::user();
        if ($admin->bidang !== 'Administrator Sistem' && $student->bidang !== $admin->bidang) {
            abort(403, 'Akses Ditolak.');
        }

        $newStatus = ($student->status === 'aktif') ? 'nonaktif' : 'aktif';
        $student->update(['status' => $newStatus]);

        $pesan = ($newStatus === 'aktif') ? 'Mahasiswa diaktifkan kembali.' : 'Mahasiswa dinonaktifkan.';

        return back()->with('success', $pesan);
    }
}
