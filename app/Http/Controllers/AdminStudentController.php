<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\Auth; // Jangan lupa import Auth

class AdminStudentController extends Controller
{
    // 1. TAMPILKAN DAFTAR MAHASISWA (SESUAI BIDANG)
    public function index(Request $request)
    {
        $admin = Auth::user();
        $isSuperAdmin = $admin->bidang === 'Administrator Sistem';

        // Ambil input pencarian
        $search = $request->input('search');

        $students = User::where('role', 'mahasiswa')
            // 1. FILTER BIDANG (Jika bukan Super Admin)
            ->when(!$isSuperAdmin, function ($query) use ($admin) {
                return $query->where('bidang', $admin->bidang);
            })
            // 2. FILTER PENCARIAN (Nama, NIM, atau Instansi)
            ->when($search, function ($query, $search) {
                return $query->where(function($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%')
                      ->orWhere('nim', 'like', '%' . $search . '%')
                      ->orWhere('instansi', 'like', '%' . $search . '%');
                });
            })
            ->latest()
            ->paginate(10) // Limit 10 per halaman
            ->withQueryString(); // Agar saat pindah halaman, pencarian tidak hilang

        return view('admin.students.index', compact('students', 'search'));
    }

    // 2. FORM TAMBAH MAHASISWA
    public function create()
    {
        return view('admin.students.create');
    }

    // 3. SIMPAN MAHASISWA BARU
    public function store(Request $request)
    {
        $admin = Auth::user();

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],

            // Data Akademik
            'nim' => ['required', 'string', 'max:20'],
            'prodi' => ['required', 'string', 'max:100'],
            'instansi' => ['required', 'string', 'max:100'],

            // --- VALIDASI BARU ---
            'kegiatan' => ['required', 'string'],
            'fakultas' => ['nullable', 'string'], // Boleh kosong
            'tanggal_masuk' => ['required', 'date'],
            'tanggal_selesai' => ['required', 'date', 'after:tanggal_masuk'], // Harus setelah tgl masuk
        ]);

        $bidangMahasiswa = ($admin->bidang === 'Administrator Sistem') ? 'Sekretariat' : $admin->bidang;

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'mahasiswa',
            'bidang' => $bidangMahasiswa,
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

        // KEAMANAN: Cek apakah admin berhak melihat mahasiswa ini
        $admin = Auth::user();
        if ($admin->bidang !== 'Administrator Sistem' && $student->bidang !== $admin->bidang) {
            abort(403, 'Anda tidak memiliki akses ke mahasiswa bidang lain.');
        }

        return view('admin.students.show', compact('student'));
    }

    // 5. HAPUS MAHASISWA
    public function destroy($id)
    {
        $student = User::findOrFail($id);

        // KEAMANAN: Cek hak akses sebelum hapus
        $admin = Auth::user();
        if ($admin->bidang !== 'Administrator Sistem' && $student->bidang !== $admin->bidang) {
            abort(403, 'Dilarang menghapus mahasiswa dari divisi lain.');
        }

        $student->delete();

        return redirect()->back()->with('success', 'Data mahasiswa berhasil dihapus.');
    }

    // 6. UPDATE STATUS
    public function updateStatus(Request $request, $id)
    {
        $student = User::findOrFail($id);

        // KEAMANAN
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
