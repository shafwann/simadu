<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Laporan;
use App\Models\Logbook;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class DosenApiController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $admin = $request->user();
        $validated = $request->validate([
            'search' => ['nullable', 'string'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);

        $query = User::withoutGlobalScope(\App\Models\Scopes\AgencyScope::class)
            ->where('role', 'pembimbing')
            ->when($validated['search'] ?? null, function ($q, $search) {
                $q->where(function ($inner) use ($search) {
                    $inner->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('nidn', 'like', "%{$search}%");
                });
            })
            ->when($admin->bidang !== 'Administrator Sistem', function ($q) use ($admin) {
                $q->whereHas('mahasiswaBimbingan', function ($m) use ($admin) {
                    $m->withoutGlobalScope(\App\Models\Scopes\AgencyScope::class)
                        ->where('bidang', $admin->bidang)
                        ->where('kategori_instansi', $admin->kategori_instansi);
                });
            })
            ->latest();

        return response()->json($query->paginate($validated['per_page'] ?? 10));
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', 'min:8'],
            'nidn' => ['nullable', 'string', 'max:255'],
            'instansi' => ['required', 'string', 'max:255'],
            'mahasiswa_ids' => ['nullable', 'array'],
            'mahasiswa_ids.*' => ['integer', 'exists:users,id'],
        ]);

        $mahasiswaIds = $this->allowedMahasiswaIds($request, $data['mahasiswa_ids'] ?? []);

        $dosen = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'nidn' => $data['nidn'] ?? null,
            'role' => 'pembimbing',
            'instansi' => $data['instansi'],
            'status' => 'aktif',
            'kategori_instansi' => $request->user()->kategori_instansi,
        ]);

        if ($mahasiswaIds !== []) {
            $dosen->mahasiswaBimbingan()->sync($mahasiswaIds);
        }

        return response()->json([
            'message' => 'Dosen berhasil dibuat.',
            'data' => $dosen->load('mahasiswaBimbingan'),
        ], 201);
    }

    public function show(Request $request, User $dosen): JsonResponse
    {
        $this->authorizeDosen($request, $dosen);

        return response()->json([
            'data' => $dosen->load('mahasiswaBimbingan'),
        ]);
    }

    public function update(Request $request, User $dosen): JsonResponse
    {
        $this->authorizeDosen($request, $dosen);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($dosen->id)],
            'password' => ['nullable', 'confirmed', 'min:8'],
            'nidn' => ['nullable', 'string', 'max:255'],
            'mahasiswa_ids' => ['nullable', 'array'],
            'mahasiswa_ids.*' => ['integer', 'exists:users,id'],
        ]);

        $dosen->fill([
            'name' => $data['name'],
            'email' => $data['email'],
            'nidn' => $data['nidn'] ?? $dosen->nidn,
        ]);

        if (!empty($data['password'])) {
            $dosen->password = Hash::make($data['password']);
        }

        $mahasiswaIds = $this->allowedMahasiswaIds($request, $data['mahasiswa_ids'] ?? []);
        $dosen->mahasiswaBimbingan()->sync($mahasiswaIds);
        $dosen->save();

        return response()->json([
            'message' => 'Data dosen berhasil diperbarui.',
            'data' => $dosen->fresh()->load('mahasiswaBimbingan'),
        ]);
    }

    public function destroy(Request $request, User $dosen): JsonResponse
    {
        $this->authorizeDosen($request, $dosen);
        $dosen->delete();

        return response()->json(['message' => 'Akun dosen berhasil dihapus.']);
    }

    public function myStudents(Request $request): JsonResponse
    {
        abort_if($request->user()->role !== 'pembimbing', 403);

        $students = $request->user()
            ->mahasiswaBimbingan()
            ->with(['attendances', 'logbooks', 'laporans'])
            ->paginate((int) $request->query('per_page', 10));

        return response()->json($students);
    }

    public function myStudentDetail(Request $request, User $student): JsonResponse
    {
        abort_if($request->user()->role !== 'pembimbing', 403);

        $isAssigned = $request->user()
            ->mahasiswaBimbingan()
            ->where('users.id', $student->id)
            ->exists();

        abort_if(!$isAssigned, 403, 'Akses Ditolak.');

        return response()->json([
            'data' => $student,
            'attendances' => Attendance::where('user_id', $student->id)->orderByDesc('tanggal')->paginate(10),
            'logbooks' => Logbook::where('user_id', $student->id)->orderByDesc('tanggal')->paginate(10),
            'laporans' => Laporan::where('user_id', $student->id)->latest()->get(),
        ]);
    }

    private function authorizeDosen(Request $request, User $dosen): void
    {
        abort_if($dosen->role !== 'pembimbing', 404);

        $admin = $request->user();
        abort_if($admin->role !== 'admin' && $admin->role !== 'master', 403);
        abort_if($admin->kategori_instansi && $dosen->kategori_instansi !== $admin->kategori_instansi, 403, 'Akses Ditolak. Beda instansi.');
    }

    private function allowedMahasiswaIds(Request $request, array $ids): array
    {
        if ($ids === []) {
            return [];
        }

        $admin = $request->user();

        return User::whereIn('id', $ids)
            ->where('role', 'mahasiswa')
            ->when($admin->kategori_instansi, fn ($q) => $q->where('kategori_instansi', $admin->kategori_instansi))
            ->when($admin->bidang !== 'Administrator Sistem', fn ($q) => $q->where('bidang', $admin->bidang))
            ->pluck('id')
            ->all();
    }
}
