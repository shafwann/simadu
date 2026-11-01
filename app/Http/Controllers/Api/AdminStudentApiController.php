<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FaceResetRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;

class AdminStudentApiController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $admin = $request->user();
        $validated = $request->validate([
            'search' => ['nullable', 'string'],
            'status' => ['nullable', 'in:aktif,nonaktif'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);

        $query = User::where('role', 'mahasiswa')
            ->when($admin->kategori_instansi, fn ($q) => $q->where('kategori_instansi', $admin->kategori_instansi))
            ->when($admin->bidang !== 'Administrator Sistem', fn ($q) => $q->where('bidang', $admin->bidang))
            ->where('status', $validated['status'] ?? 'aktif')
            ->latest();

        if (!empty($validated['search'])) {
            $search = $validated['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('nim', 'like', "%{$search}%")
                    ->orWhere('instansi', 'like', "%{$search}%");
            });
        }

        return response()->json($query->paginate($validated['per_page'] ?? 10));
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate($this->rules());

        $data['password'] = Hash::make($data['password']);
        $data['role'] = 'mahasiswa';
        $data['status'] = 'aktif';
        $data['kategori_instansi'] = $request->user()->kategori_instansi;

        $student = User::create($data);

        return response()->json([
            'message' => 'Mahasiswa berhasil ditambahkan.',
            'data' => $student,
        ], 201);
    }

    public function show(Request $request, User $student): JsonResponse
    {
        $this->authorizeStudent($request, $student);

        return response()->json([
            'data' => $student->load(['attendances', 'logbooks', 'laporans', 'assessment']),
        ]);
    }

    public function update(Request $request, User $student): JsonResponse
    {
        $this->authorizeStudent($request, $student);

        $data = $request->validate($this->rules($student));

        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $student->update($data);

        return response()->json([
            'message' => 'Data mahasiswa berhasil diperbarui.',
            'data' => $student->fresh(),
        ]);
    }

    public function destroy(Request $request, User $student): JsonResponse
    {
        $this->authorizeStudent($request, $student);
        $student->delete();

        return response()->json(['message' => 'Data mahasiswa berhasil dihapus.']);
    }

    public function updateStatus(Request $request, User $student): JsonResponse
    {
        $this->authorizeStudent($request, $student);

        $data = $request->validate([
            'status' => ['required', 'in:aktif,nonaktif'],
        ]);

        $student->update(['status' => $data['status']]);

        return response()->json([
            'message' => 'Status mahasiswa berhasil diperbarui.',
            'data' => $student->fresh(),
        ]);
    }

    public function resetFace(Request $request, User $student): JsonResponse
    {
        $this->authorizeStudent($request, $student);

        if (!empty($student->face_image_path) && Storage::disk('public')->exists($student->face_image_path)) {
            Storage::disk('public')->delete($student->face_image_path);
        }

        $student->update([
            'face_descriptor' => null,
            'face_image_path' => null,
        ]);

        FaceResetRequest::where('user_id', $student->id)
            ->where('status', 'pending')
            ->update([
                'status' => 'approved',
                'approved_at' => now(),
                'approved_by' => $request->user()->id,
            ]);

        return response()->json([
            'message' => 'Data wajah mahasiswa telah direset.',
            'data' => $student->fresh(),
        ]);
    }

    private function rules(?User $student = null): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($student?->id)],
            'password' => [$student ? 'nullable' : 'required', 'confirmed', Rules\Password::defaults()],
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
        ];
    }

    private function authorizeStudent(Request $request, User $student): void
    {
        abort_if($student->role !== 'mahasiswa', 404);

        $admin = $request->user();
        abort_if($admin->kategori_instansi && $student->kategori_instansi !== $admin->kategori_instansi, 403, 'Akses Ditolak. Beda instansi.');
        abort_if($admin->bidang !== 'Administrator Sistem' && $student->bidang !== $admin->bidang, 403, 'Akses Ditolak.');
    }
}
