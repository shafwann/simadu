<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule; // Untuk validasi Enum/Pilihan
use App\Models\Attendance;
use App\Models\FaceResetRequest;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;


class AdminStudentController extends Controller
{
    // 1. TAMPILKAN DAFTAR MAHASISWA
    public function index(Request $request)
    {
        $admin = Auth::user();
        $isSuperAdmin = $admin->bidang === 'Administrator Sistem';

        // Ambil input dari URL
        $search = $request->input('search');
        $status = $request->input('status'); // Ambil input status

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
        $bidangOptions = array_merge(
            config('bidang.options', []),
            \App\Models\AboutSetting::bidangOptions()
        );

        return view('admin.students.create', compact('bidangOptions'));
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
            'bidang' => ['required', 'string', 'max:255'],

            'jabatan_pembimbing' => ['required', 'string', 'max:255'],
            'nama_pembimbing' => ['required', 'string', 'max:255'],
            'nip_pembimbing' => ['required', 'string', 'max:255'],
        ], [
            'password.confirmed' => 'Password dan konfirmasi password tidak sama.',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'mahasiswa',

            'kategori_instansi' => Auth::user()->kategori_instansi,
            'bidang' => $request->bidang,

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
            'laporans' => fn($q) => $q->latest(),
            'assessment'
        ])->findOrFail($id);

        // CEK INSTANSI
        if (
            Auth::user()->kategori_instansi &&
            $student->kategori_instansi !== Auth::user()->kategori_instansi
        ) {
            abort(403, 'Akses Ditolak. Beda instansi.');
        }

        // CEK BIDANG (LOGIKA LAMA)
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

        // CEK INSTANSI
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

        // CEK INSTANSI
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

        // CEK INSTANSI
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

        // CEK INSTANSI
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

        return back()->with(
            'success',
            $newStatus === 'aktif'
                ? 'Mahasiswa diaktifkan kembali.'
                : 'Mahasiswa dinonaktifkan.'
        );
    }

    // ========================================
    // 9. FACE VERIFICATION MANAGEMENT
    // ========================================

    public function resetFaceDescriptor(Request $request, $id)
    {
        $student = User::findOrFail($id);

        // CEK INSTANSI
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

        if (!empty($student->face_image_path) && Storage::disk('public')->exists($student->face_image_path)) {
            Storage::disk('public')->delete($student->face_image_path);
        }

        // Clear face data
        $student->update([
            'face_descriptor' => null,
            'face_image_path' => null,
        ]);

        // Mark all pending requests as approved because admin has completed the reset manually
        FaceResetRequest::where('user_id', $id)
            ->where('status', 'pending')
            ->update([
                'status' => 'approved',
                'approved_at' => now(),
                'approved_by' => Auth::id(),
            ]);

        return back()->with('success', 'Data wajah mahasiswa telah direset. Mahasiswa perlu setup ulang.');
    }

    public function faceResetRequests()
    {
        $admin = Auth::user();

        $faceResetRequests = FaceResetRequest::with('user')
            ->whereHas('user', function ($q) use ($admin) {
                if ($admin->kategori_instansi) {
                    $q->where('kategori_instansi', $admin->kategori_instansi);
                }

                if ($admin->bidang !== 'Administrator Sistem') {
                    $q->where('bidang', $admin->bidang);
                }
            })
            ->latest()
            ->paginate(15);

        return view('admin.face_reset_requests.index', compact('faceResetRequests'));
    }

    public function approveFaceReset($id)
    {
        $faceRequest = FaceResetRequest::findOrFail($id);
        $student = $faceRequest->user;

        // CEK INSTANSI
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

        if (!empty($student->face_image_path) && Storage::disk('public')->exists($student->face_image_path)) {
            Storage::disk('public')->delete($student->face_image_path);
        }

        // Clear face data
        $student->update([
            'face_descriptor' => null,
            'face_image_path' => null,
        ]);

        // Mark request as approved
        $faceRequest->update([
            'status' => 'approved',
            'approved_at' => now(),
            'approved_by' => Auth::id(),
        ]);

        return back()->with('success', 'Permintaan reset wajah disetujui. Mahasiswa dapat setup ulang.');
    }

    public function rejectFaceReset($id)
    {
        $faceRequest = FaceResetRequest::findOrFail($id);
        $student = $faceRequest->user;

        // CEK INSTANSI
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

        // Mark request as rejected
        $faceRequest->update([
            'status' => 'rejected',
        ]);

        return back()->with('success', 'Permintaan reset wajah ditolak.');
    }
}
