<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Logbook;
use App\Models\Laporan;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminDosenController extends Controller
{
    // 1. Daftar Dosen
    public function index()
    {
        $dosen = User::where('role', 'pembimbing')->latest()->with('mahasiswaBimbingan')->paginate(10);
        return view('admin.dosen.index', compact('dosen'));
    }

    // 2. Form buat akun Dosen
    public function create(Request $request)
    {
        // dropdown instansi (hanya mahasiswa AKTIF)
        $instansiList = User::where('role', 'mahasiswa')
            ->where('status', 'aktif')
            ->whereNotNull('instansi')
            ->distinct()
            ->orderBy('instansi')
            ->pluck('instansi');

        // dropdown prodi (tergantung instansi & AKTIF)
        $prodiList = User::where('role', 'mahasiswa')
            ->where('status', 'aktif')
            ->when($request->instansi, function ($q) use ($request) {
                $q->where('instansi', $request->instansi);
            })
            ->whereNotNull('prodi')
            ->distinct()
            ->orderBy('prodi')
            ->pluck('prodi');

        // mahasiswa terfilter (AKTIF SAJA)
        $mahasiswa = User::where('role', 'mahasiswa')
            ->where('status', 'aktif')
            ->when($request->instansi, fn ($q) =>
                $q->where('instansi', $request->instansi))
            ->when($request->prodi, fn ($q) =>
                $q->where('prodi', $request->prodi))
            ->get();

        return view('admin.dosen.create', compact(
            'instansiList',
            'prodiList',
            'mahasiswa'
        ));
    }



    // 3. Simpan akun Dosen
    public function store(Request $request)
    {
        $request->validate([
        'name' => 'required',
        'email' => 'required|email|unique:users',
        'password' => 'required|confirmed|min:8',
        'instansi' => 'required|string',
        'mahasiswa_ids' => 'array'
        ]);

        $dosen = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'pembimbing',
            'instansi' => $request->instansi,
            'status' => 'aktif',
        ]);

        // Assign mahasiswa yang dipilih
        if ($request->filled('mahasiswa_ids')) {
            $dosen->mahasiswaBimbingan()->sync($request->mahasiswa_ids);
        }

        return redirect()->route('admin.dosen.index')
                         ->with('success', 'Dosen berhasil dibuat dan mahasiswa berhasil diassign.');
    }

    // 4. Hapus Dosen
    public function destroy(User $dosen)
    {
        $dosen->delete();
        return redirect()->back()->with('success','Akun dosen berhasil dihapus.');
    }

    // 5. Form assign mahasiswa (opsional jika mau assign lagi)
    public function showAssignForm(User $dosen)
    {
        $mahasiswa = User::where('role', 'mahasiswa')
                         ->where('status', 'aktif')
                         ->get();

        $mahasiswaAssigned = $dosen->mahasiswaBimbingan->pluck('id')->toArray();

        return view('admin.dosen.assign', compact('dosen','mahasiswa','mahasiswaAssigned'));
    }

    // 6. Assign mahasiswa ke dosen (opsional)
    public function assignMahasiswa(Request $request, User $dosen)
    {
        $dosen->mahasiswaBimbingan()->sync(
            $request->input('mahasiswa_ids', [])
        );

        return redirect()
            ->route('admin.dosen.index')
            ->with('success','Mahasiswa berhasil diassign ke dosen.');
    }


    //7. edit dosen dari admin
    public function edit(Request $request, $id)
    {
        $dosen = User::where('role', 'pembimbing')->findOrFail($id);

        // ===============================
        // 1. DROPDOWN INSTANSI (GLOBAL)
        // ===============================
        $instansiList = User::where('role', 'mahasiswa')
            ->where('status', 'aktif')
            ->whereNotNull('instansi')
            ->distinct()
            ->orderBy('instansi')
            ->pluck('instansi');

        // ===============================
        // 2. DROPDOWN PRODI (TERGANTUNG INSTANSI)
        // ===============================
        $instansi = $request->instansi ?? $dosen->instansi;

        $prodiList = User::where('role', 'mahasiswa')
            ->where('status', 'aktif')
            ->when($instansi, function ($q) use ($instansi) {
                $q->where('instansi', $instansi);
            })
            ->whereNotNull('prodi')
            ->distinct()
            ->orderBy('prodi')
            ->pluck('prodi');

        // ===============================
        // 3. MAHASISWA TERFILTER
        // ===============================
        $prodi = $request->prodi ?? $dosen->prodi;

        $mahasiswa = User::where('role', 'mahasiswa')
            ->where('status', 'aktif')
            ->when($instansi, fn ($q) =>
                $q->where('instansi', $instansi))
            ->when($prodi, fn ($q) =>
                $q->where('prodi', $prodi))
            ->get();

        $mahasiswaAssigned = $dosen->mahasiswaBimbingan
            ->pluck('id')
            ->toArray();

        return view('admin.dosen.edit', compact(
            'dosen',
            'instansiList',
            'prodiList',
            'mahasiswa',
            'mahasiswaAssigned'
        ));

    }

    //8. update data dosen
    public function update(Request $request, $id)
    {
        $dosen = User::where('role', 'pembimbing')->findOrFail($id);

        // ===============================
        // VALIDASI
        // ===============================
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $dosen->id,
            'password' => 'nullable|confirmed|min:8',
            'mahasiswa_ids' => 'array',
        ]);

        // ===============================
        // UPDATE DATA DOSEN
        // ===============================
        $dosen->name = $request->name;
        $dosen->email = $request->email;

        if ($request->filled('password')) {
            $dosen->password = Hash::make($request->password);
        }

        // ===============================
        // FILTER MAHASISWA SESUAI INSTANSI
        // ===============================
        $mahasiswaIds = User::whereIn('id', $request->input('mahasiswa_ids', []))
            ->pluck('id')
            ->toArray();

        // SYNC PIVOT
        $dosen->mahasiswaBimbingan()->sync($mahasiswaIds);

        // ===============================
        // UPDATE INSTANSI DOSEN SESUAI MAHASISWA TERPILIH
        // ===============================
        if (!empty($mahasiswaIds)) {
            // Ambil data mahasiswa yang dipilih
            $mahasiswa = User::whereIn('id', $mahasiswaIds)->get();

            // Update instansi, prodi, fakultas, kegiatan sesuai mahasiswa pertama
            $dosen->instansi  = $mahasiswa->first()->instansi ?? $dosen->instansi;
            $dosen->prodi     = $mahasiswa->first()->prodi ?? $dosen->prodi;
            $dosen->fakultas  = $mahasiswa->first()->fakultas ?? $dosen->fakultas;
            $dosen->kegiatan  = $mahasiswa->first()->kegiatan ?? $dosen->kegiatan;
            $dosen->bidang    = $mahasiswa->first()->bidang ?? $dosen->bidang;

            // Jika mau, bisa diganti logikanya: misal gabungkan semua unik mahasiswa
            // $dosen->instansi = $mahasiswa->pluck('instansi')->unique()->implode(', ');
        } else {
            // Kalau tidak ada mahasiswa dipilih, tetap gunakan input dari form atau biarkan
            $dosen->instansi  = $request->instansi ?? $dosen->instansi;
            $dosen->prodi     = $request->prodi ?? $dosen->prodi;
            $dosen->fakultas  = $dosen->fakultas; // biarkan tetap
            $dosen->kegiatan  = $dosen->kegiatan; // biarkan tetap
            $dosen->bidang    = $dosen->bidang;
        }

        $dosen->save();

        return redirect()
            ->route('admin.dosen.index')
            ->with('success', 'Data dosen berhasil diperbarui.');
    }


    //9. dashboard dosen
    public function dashboard()
    {
        $dosen = auth()->user();
        $mahasiswa = $dosen->mahasiswaBimbingan()
            ->with([
                'attendances' => function ($q) {
                    $q->latest()->limit(10);
                },
                'logbooks' => function ($q) {
                    $q->latest()->limit(10);
                },
                'laporans' => function ($q) {
                    $q->latest();
                }
            ])
            ->paginate(5); // 👈 jumlah mahasiswa per halaman

        return view('pembimbing.dashboard', compact('mahasiswa'));
    }

    // 10. detail mahasiswa
    public function detailMahasiswa($id)
    {
        $dosen = auth()->user();

        // pastikan mahasiswa ini memang bimbingannya
        $mahasiswa = $dosen->mahasiswaBimbingan()
            ->where('users.id', $id)
            ->firstOrFail();

        $presensi = Attendance::where('user_id', $id)
            ->orderByDesc('tanggal')
            ->paginate(10);

        $logbooks = Logbook::where('user_id', $id)
            ->orderByDesc('tanggal')
            ->paginate(5);

        $laporans = Laporan::where('user_id', $id)
            ->orderByDesc('created_at')
            ->get();

        return view('pembimbing.mahasiswa-detail', compact(
            'mahasiswa',
            'presensi',
            'logbooks',
            'laporans'
        ));
    }

    // 11. lihat mahasiswa bimbingan dosen (ADMIN)
    public function showMahasiswa(User $dosen)
    {
        // pastikan ini dosen
        abort_if($dosen->role !== 'pembimbing', 404);

        $mahasiswa = $dosen->mahasiswaBimbingan()
            ->with(['attendances', 'logbooks'])
            ->paginate(10);

        return view('admin.dosen.mahasiswa', compact('dosen', 'mahasiswa'));
    }

}
