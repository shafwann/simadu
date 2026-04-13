<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Logbook;
use App\Models\Laporan;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Models\PembimbingRequest;

class AdminDosenController extends Controller
{
    // ==================================================
    // 1. DAFTAR DOSEN (FIX AGENCY SCOPE)
    // ==================================================
    public function index()
    {
        $admin = auth()->user();

        // ===============================
        // 1. PENDING DOSEN
        // ===============================
        $pendingDosens = PembimbingRequest::with('mahasiswa')
            ->where('status', 'pending')
            ->when(
                $admin->bidang !== 'Administrator Sistem',
                function ($q) use ($admin) {
                    $q->whereHas('mahasiswa', function ($m) use ($admin) {
                        $m->where('bidang', $admin->bidang)
                          ->where('kategori_instansi', $admin->kategori_instansi);
                    });
                }
            )
            ->latest()
            ->get();

        // ===============================
        // 2. DAFTAR DOSEN (FINAL & BENAR)
        // ===============================
        $dosen = User::withoutGlobalScope(\App\Models\Scopes\AgencyScope::class)
            ->where('role', 'pembimbing')
            ->when(
                $admin->bidang !== 'Administrator Sistem',
                function ($q) use ($admin) {

                    // HARUS punya mahasiswa sesuai admin
                    $q->whereHas('mahasiswaBimbingan', function ($m) use ($admin) {
                        $m->withoutGlobalScope(\App\Models\Scopes\AgencyScope::class)
                          ->where('bidang', $admin->bidang)
                          ->where('kategori_instansi', $admin->kategori_instansi);
                    })

                    // TIDAK BOLEH punya mahasiswa dari instansi lain
                    ->whereDoesntHave('mahasiswaBimbingan', function ($m) use ($admin) {
                        $m->withoutGlobalScope(\App\Models\Scopes\AgencyScope::class)
                          ->where(function ($x) use ($admin) {
                              $x->where('bidang', '!=', $admin->bidang)
                                ->orWhere('kategori_instansi', '!=', $admin->kategori_instansi);
                          });
                    });
                }
            )
            ->latest()
            ->paginate(10);

        return view('admin.dosen.index', compact('pendingDosens', 'dosen'));
    }

    // ==================================================
    // 2. SIMPAN DOSEN MANUAL
    // ==================================================
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
            'kategori_instansi' => auth()->user()->kategori_instansi,
        ]);

        if ($request->filled('mahasiswa_ids')) {
            $dosen->mahasiswaBimbingan()->sync($request->mahasiswa_ids);
        }

        return redirect()->route('admin.dosen.index')
            ->with('success', 'Dosen berhasil dibuat.');
    }

    // ==================================================
    // 3. HAPUS DOSEN
    // ==================================================
    public function destroy(User $dosen)
    {
        $this->authorizeInstansi($dosen);
        $dosen->delete();

        return redirect()->back()->with('success','Akun dosen berhasil dihapus.');
    }

    // ==================================================
    // 4. EDIT DOSEN
    // ==================================================
    public function edit(Request $request, $id)
    {
        $dosen = User::where('role', 'pembimbing')->findOrFail($id);

        if (
            auth()->user()->kategori_instansi &&
            $dosen->kategori_instansi !== auth()->user()->kategori_instansi
        ) {
            abort(403, 'Akses Ditolak. Beda instansi.');
        }

        $instansiList = User::where('role', 'mahasiswa')
            ->where('status', 'aktif')
            ->whereNotNull('instansi')
            ->distinct()
            ->orderBy('instansi')
            ->pluck('instansi');

        $instansi = $request->instansi ?? $dosen->instansi;

        $prodiList = User::where('role', 'mahasiswa')
            ->where('status', 'aktif')
            ->when($instansi, fn ($q) => $q->where('instansi', $instansi))
            ->whereNotNull('prodi')
            ->distinct()
            ->orderBy('prodi')
            ->pluck('prodi');

        $prodi = $request->prodi ?? $dosen->prodi;

        $mahasiswa = User::where('role', 'mahasiswa')
            ->where('status', 'aktif')
            ->when($instansi, fn ($q) => $q->where('instansi', $instansi))
            ->when($prodi, fn ($q) => $q->where('prodi', $prodi))
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

    // ==================================================
    // 5. UPDATE DOSEN
    // ==================================================
    public function update(Request $request, $id)
    {
        $dosen = User::where('role', 'pembimbing')->findOrFail($id);

        if (
            auth()->user()->kategori_instansi &&
            $dosen->kategori_instansi !== auth()->user()->kategori_instansi
        ) {
            abort(403, 'Akses Ditolak.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $dosen->id,
            'password' => 'nullable|confirmed|min:8',
            'mahasiswa_ids' => 'array',
        ]);

        $dosen->name = $request->name;
        $dosen->email = $request->email;

        if ($request->filled('password')) {
            $dosen->password = Hash::make($request->password);
        }

        $mahasiswaIds = User::whereIn('id', $request->input('mahasiswa_ids', []))
            ->pluck('id')
            ->toArray();

        $dosen->mahasiswaBimbingan()->sync($mahasiswaIds);

        if (!empty($mahasiswaIds)) {
            $mhs = User::whereIn('id', $mahasiswaIds)->first();
            $dosen->instansi = $mhs->instansi;
            $dosen->prodi = $mhs->prodi;
            $dosen->fakultas = $mhs->fakultas;
            $dosen->bidang = $mhs->bidang;
        }

        $dosen->save();

        return redirect()->route('admin.dosen.index')
            ->with('success', 'Data dosen berhasil diperbarui.');
    }

    // ==================================================
    // 6. DASHBOARD DOSEN
    // ==================================================
    public function dashboard()
    {
        $dosen = auth()->user();

        $mahasiswa = $dosen->mahasiswaBimbingan()
            ->with(['attendances','logbooks','laporans'])
            ->paginate(5);

        return view('pembimbing.dashboard', compact('mahasiswa'));
    }

    // ==================================================
    // 7. DETAIL MAHASISWA DOSEN
    // ==================================================
    public function detailMahasiswa($id)
    {
        $dosen = auth()->user();

        $mahasiswa = $dosen->mahasiswaBimbingan()
            ->where('users.id', $id)
            ->firstOrFail();

        $presensi = Attendance::where('user_id', $id)->paginate(10);
        $logbooks = Logbook::where('user_id', $id)->paginate(5);
        $laporans = Laporan::where('user_id', $id)->get();

        return view('pembimbing.mahasiswa-detail', compact(
            'mahasiswa','presensi','logbooks','laporans'
        ));
    }

    // ==================================================
    // 8. ADMIN LIHAT MAHASISWA DOSEN
    // ==================================================
    public function showMahasiswa(User $dosen)
    {
        abort_if($dosen->role !== 'pembimbing', 404);

        if (
            auth()->user()->kategori_instansi &&
            $dosen->kategori_instansi !== auth()->user()->kategori_instansi
        ) {
            abort(403);
        }

        $mahasiswa = $dosen->mahasiswaBimbingan()->paginate(10);

        return view('admin.dosen.mahasiswa', compact('dosen','mahasiswa'));
    }

    // ==================================================
    // HELPER
    // ==================================================
    private function authorizeInstansi(User $user)
    {
        if (
            auth()->user()->kategori_instansi &&
            $user->kategori_instansi !== auth()->user()->kategori_instansi
        ) {
            abort(403, 'Beda instansi');
        }
    }
}
