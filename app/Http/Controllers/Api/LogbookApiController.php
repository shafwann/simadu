<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Logbook;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LogbookApiController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'sort' => ['nullable', 'in:tanggal,status,created_at,updated_at'],
            'direction' => ['nullable', 'in:asc,desc'],
            'status' => ['nullable', 'in:pending,validated,rejected'],
            'year' => ['nullable', 'integer', 'min:2000', 'max:2100'],
            'month' => ['nullable', 'integer', 'min:1', 'max:12'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);

        $sort = $validated['sort'] ?? 'tanggal';
        $direction = $validated['direction'] ?? 'desc';

        $query = Logbook::query()
            ->where('user_id', $request->user()->id);

        if (!empty($validated['status'])) {
            $query = match ($validated['status']) {
                'validated' => $query->where('is_validated', true)->where('is_rejected', false),
                'rejected' => $query->where('is_rejected', true),
                default => $query->where('is_validated', false)->where('is_rejected', false),
            };
        }

        if (!empty($validated['year'])) {
            $query->whereYear('tanggal', $validated['year']);
        }

        if (!empty($validated['month'])) {
            $query->whereMonth('tanggal', $validated['month']);
        }

        if ($sort === 'status') {
            $query->orderByRaw(
                "CASE
                    WHEN is_rejected = 1 THEN 2
                    WHEN is_validated = 1 THEN 3
                    ELSE 1
                END {$direction}"
            )->orderByDesc('tanggal');
        } else {
            $query->orderBy($sort, $direction);
        }

        $logbooks = $query->paginate($validated['per_page'] ?? 10);

        return response()->json($logbooks);
    }

    public function store(Request $request): JsonResponse
    {
        if ($request->user()->status !== 'aktif') {
            return response()->json([
                'message' => 'Akun Anda tidak aktif, tidak bisa mengisi logbook.',
            ], 403);
        }

        $validated = $request->validate([
            'tanggal' => ['required', 'date'],
            'kegiatan' => ['required', 'string', 'min:5'],
        ]);

        $logbook = Logbook::create([
            'user_id' => $request->user()->id,
            'tanggal' => Carbon::parse($validated['tanggal'])->toDateString(),
            'kegiatan' => $validated['kegiatan'],
            'is_validated' => false,
            'is_rejected' => false,
        ]);

        return response()->json([
            'message' => 'Logbook berhasil disimpan.',
            'data' => $logbook,
        ], 201);
    }

    public function show(Request $request, Logbook $logbook): JsonResponse
    {
        $this->authorizeOwner($request, $logbook);

        return response()->json([
            'data' => $logbook,
        ]);
    }

    public function update(Request $request, Logbook $logbook): JsonResponse
    {
        $this->authorizeOwner($request, $logbook);

        $validated = $request->validate([
            'tanggal' => ['required', 'date'],
            'kegiatan' => ['required', 'string', 'min:5'],
        ]);

        $updateData = [
            'tanggal' => Carbon::parse($validated['tanggal'])->toDateString(),
            'kegiatan' => $validated['kegiatan'],
        ];

        if ($logbook->is_rejected) {
            $updateData['is_rejected'] = false;
            $updateData['rejected_at'] = null;
            $updateData['rejected_note'] = null;
            $updateData['is_validated'] = false;
        }

        $logbook->update($updateData);

        return response()->json([
            'message' => 'Logbook berhasil diperbarui.',
            'data' => $logbook->fresh(),
        ]);
    }

    public function destroy(Request $request, Logbook $logbook): JsonResponse
    {
        $this->authorizeOwner($request, $logbook);

        $logbook->delete();

        return response()->json([
            'message' => 'Logbook berhasil dihapus.',
        ]);
    }

    private function authorizeOwner(Request $request, Logbook $logbook): void
    {
        abort_if($logbook->user_id !== $request->user()->id, 403, 'Akses ditolak.');
    }
}
