<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class AdminStudentController extends Controller
{
    // 1. TAMPILKAN DAFTAR MAHASISWA
    public function index()
    {
        $students = User::where('role', 'mahasiswa')->latest()->get();
        return view('admin.students.index', compact('students'));
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
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'mahasiswa', // Otomatis set role jadi mahasiswa
        ]);

        return redirect()->route('admin.students.index')->with('success', 'Akun mahasiswa berhasil dibuat!');
    }

    // 4. LIHAT DETAIL (LOGBOOK & PRESENSI)
    public function show($id)
    {
        // Ambil user beserta data logbook dan presensinya
        $student = User::with(['attendances', 'logbooks'])->findOrFail($id);

        return view('admin.students.show', compact('student'));
    }

    // 5. HAPUS MAHASISWA
    public function destroy($id)
    {
        $student = User::findOrFail($id);
        $student->delete();

        return redirect()->back()->with('success', 'Data mahasiswa berhasil dihapus.');
    }

    public function updateStatus(Request $request, $id)
{
    $student = \App\Models\User::findOrFail($id);

    // Toggle Status: Jika Aktif jadi Nonaktif, dan sebaliknya
    $newStatus = ($student->status === 'aktif') ? 'nonaktif' : 'aktif';

    $student->update(['status' => $newStatus]);

    $pesan = ($newStatus === 'aktif') ? 'Mahasiswa diaktifkan kembali.' : 'Mahasiswa dinonaktifkan (Lulus/Keluar).';

    return back()->with('success', $pesan);
}
}
