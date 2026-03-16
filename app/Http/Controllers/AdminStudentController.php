<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule; // Penting untuk validasi Enum/Pilihan
use App\Models\Attendance;
use Carbon\Carbon;

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
            //  FILTER INSTANSI SISTEM
            ->when($admin->kategori_instansi, function ($query) use ($admin) {
                return $query->where('kategori_instansi', $admin->kategori_instansi);
            })
            //  LOGIKA LAMA TETAP (bidang)
            ->when(!$isSuperAdmin, function ($query) use ($admin) {
                return $query->where('bidang', $admin->bidang);
            })
            // Filter Pencarian
            ->when($search, function ($query, $search) {
                return $query->where(function ($q) use ($search) {
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

            // ✅ Bidang DIBUAT OPTIONAL (karena di form create mau dihapus)
            // Kalau suatu saat masih terkirim dari request, tetap divalidasi agar aman.
            'bidang' => ['nullable', Rule::in([
                'Ekonomi',
                'Sekretariat',
                'Sosial & Budaya',
                'Fisik & Prasarana',
                'Penelitian, Pengembangan & Penyusunan Program',
            ])],

            'jabatan_pembimbing' => ['required', 'string', 'max:255'],
            'nama_pembimbing' => ['required', 'string', 'max:255'],
            'nip_pembimbing' => ['required', 'string', 'max:255'],
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'mahasiswa',

            'kategori_instansi' => Auth::user()->kategori_instansi,
            // ✅ Bidang tidak diinput di create, jadi default null (mahasiswa isi sendiri)
            'bidang' => null,

            'status' => 'aktif',
            'nim' => $request->nim,
            'prodi' => $request->prodi,
            'instansi' => $request->instansi,
            'fakultas' => $request->fakultas,
            'kegiatan' => $request->kegiatan,
            'tanggal_masuk' => $request->tanggal_masuk,
            'tanggal_selesai' => $request->tanggal_selesai,
            'jabatan_pembimbing' => $request->jabatan_pembimbing,
            'nama_pembimbing' => $request->nama_pembimbing,
            'nip_pembimbing' => $request->nip_pembimbing,
        ]);

        return redirect()->route('admin.students.index')
            ->with('success', 'Mahasiswa berhasil ditambahkan.');
    }

    // 4. LIHAT DETAIL
    public function show($id)
    {
        $student = User::with([
            'laporans' => fn ($q) => $q->latest(),
            'assessment'
        ])->findOrFail($id);

        // 🔒 CEK INSTANSI
        if (
            Auth::user()->kategori_instansi &&
            $student->kategori_instansi !== Auth::user()->kategori_instansi
        ) {
            abort(403, 'Akses Ditolak. Beda instansi.');
        }

        // 🔒 CEK BIDANG (LOGIKA LAMA)
        $admin = Auth::user();
        if ($admin->bidang !== 'Administrator Sistem' && $student->bidang !== $admin->bidang) {
            abort(403, 'Akses Ditolak.');
        }

        $attendances = $student->attendances()
            ->orderBy('tanggal', 'desc')
            ->paginate(5, ['*'], 'presensi_page');

        $logbooks = $student->logbooks()
            ->orderBy('tanggal', 'desc')
            ->paginate(5, ['*'], 'logbook_page');

        return view('admin.students.show', compact('student', 'attendances', 'logbooks'));
    }


    // 5. FORM EDIT MAHASISWA (INI YANG BARU DITAMBAHKAN)
    public function edit($id)
    {
        $student = User::findOrFail($id);

        // 🔒 CEK INSTANSI
        if (
            Auth::user()->kategori_instansi &&
            $student->kategori_instansi !== Auth::user()->kategori_instansi
        ) {
            abort(403, 'Akses Ditolak. Beda instansi.');
        }

        $admin = Auth::user();
        if ($admin->bidang !== 'Administrator Sistem' && $student->bidang !== $admin->bidang) {
            abort(403, 'Anda tidak berhak mengedit data mahasiswa dari bidang lain.');
        }

        $bidangOptions = array_merge(
            config('bidang.options', []),
            \App\Models\AboutSetting::bidangOptions()
        );

        return view('admin.students.edit', compact('student', 'bidangOptions'));
    }


    // 6. PROSES UPDATE MAHASISWA (INI YANG BARU DITAMBAHKAN)
    public function update(Request $request, $id)
    {
        $student = User::findOrFail($id);

        // 🔒 CEK INSTANSI
        if (
            Auth::user()->kategori_instansi &&
            $student->kategori_instansi !== Auth::user()->kategori_instansi
        ) {
            abort(403, 'Akses Ditolak. Beda instansi.');
        }

        $admin = Auth::user();
        if ($admin->bidang !== 'Administrator Sistem' && $student->bidang !== $admin->bidang) {
            abort(403, 'Akses Ditolak.');
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($student->id)],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
            'nim' => ['required', 'string', 'max:20'],
            'prodi' => ['required', 'string', 'max:100'],
            'instansi' => ['required', 'string', 'max:100'],
            'kegiatan' => ['required', 'string'],
            'fakultas' => ['nullable', 'string'],
            'tanggal_masuk' => ['required', 'date'],
            'tanggal_selesai' => ['required', 'date', 'after:tanggal_masuk'],
            'status' => ['required', 'in:aktif,nonaktif'],
            'jabatan_pembimbing' => ['required', 'string', 'max:255'],
            'nama_pembimbing' => ['required', 'string', 'max:255'],
            'nip_pembimbing' => ['required', 'string', 'max:255'],
            'bidang' => ['nullable', 'string', 'max:255'],
        ]);

        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $student->update($data);

        return redirect()->route('admin.students.index')
            ->with('success', 'Data mahasiswa berhasil diperbarui.');
    }

    // 7. HAPUS MAHASISWA
    public function destroy($id)
    {
        $student = User::findOrFail($id);

        // 🔒 CEK INSTANSI
        if (
            Auth::user()->kategori_instansi &&
            $student->kategori_instansi !== Auth::user()->kategori_instansi
        ) {
            abort(403, 'Akses Ditolak. Beda instansi.');
        }

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

        // 🔒 CEK INSTANSI
        if (
            Auth::user()->kategori_instansi &&
            $student->kategori_instansi !== Auth::user()->kategori_instansi
        ) {
            abort(403, 'Akses Ditolak. Beda instansi.');
        }

        $admin = Auth::user();
        if ($admin->bidang !== 'Administrator Sistem' && $student->bidang !== $admin->bidang) {
            abort(403, 'Akses Ditolak.');
        }

        $newStatus = $student->status === 'aktif' ? 'nonaktif' : 'aktif';
        $student->update(['status' => $newStatus]);

        return back()->with('success',
            $newStatus === 'aktif'
                ? 'Mahasiswa diaktifkan kembali.'
                : 'Mahasiswa dinonaktifkan.'
        );
    }
    // public function monthlyRecap(Request $request)
    // {
    //     $admin = Auth::user();
    //     // Ambil tanggal awal & akhir bulan ini
    //     $startOfMonth = Carbon::now()->startOfMonth();
    //     $endOfMonth   = Carbon::now()->endOfMonth();
    //     $monthName    = Carbon::now()->translatedFormat('F Y'); // Contoh: Januari 2026

    //     // Query Mahasiswa (Sama seperti sebelumnya)
    //     $studentsQuery = User::where('role', 'mahasiswa')->where('status', 'aktif');

    //     if ($admin->kategori_instansi) {
    //         $studentsQuery->where('kategori_instansi', $admin->kategori_instansi);
    //     }
    //     if ($admin->bidang !== 'Administrator Sistem') {
    //         $studentsQuery->where('bidang', $admin->bidang);
    //     }

    //     $students = $studentsQuery->orderBy('name')->get();

    //     $rekap = [];

    //     foreach ($students as $student) {
    //         // Ambil semua data presensi bulan ini punya mahasiswa tsb
    //         $attendances = Attendance::where('user_id', $student->id)
    //             ->whereBetween('tanggal', [$startOfMonth, $endOfMonth])
    //             ->get();

    //         // Hitung Statistik
    //         $rekap[] = [
    //             'user' => $student,
    //             'total_hadir'     => $attendances->where('status', 'Hadir')->count(),
    //             'total_terlambat' => $attendances->where('is_late', true)->count(),
    //             'total_izin'      => $attendances->whereIn('status', ['Izin', 'Sakit'])->count(),
    //             'total_alpha'     => $attendances->where('status', 'Alpha')->count(),
    //             // Hitung persentase kehadiran (opsional, biar keren)
    //             'attendance_count'=> $attendances->count()
    //         ];
    //     }

    //     return view('admin.students.monthly_recap', compact('rekap', 'monthName'));
    // }

}
