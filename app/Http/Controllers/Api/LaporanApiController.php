<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Laporan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LaporanApiController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'status' => ['nullable', 'in:pending,validated,rejected'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);

        $query = Laporan::query()
            ->where('user_id', $request->user()->id)
            ->latest();

        if (!empty($validated['status'])) {
            $query = match ($validated['status']) {
                'validated' => $query->where('is_validated', true)->where('is_rejected', false),
                'rejected' => $query->where('is_rejected', true),
                default => $query->where('is_validated', false)->where('is_rejected', false),
            };
        }

        return response()->json($query->paginate($validated['per_page'] ?? 10));
    }

    public function store(Request $request): JsonResponse
    {
        if ($request->user()->status !== 'aktif') {
            return response()->json([
                'message' => 'Akun Anda tidak aktif, tidak bisa mengunggah laporan.',
            ], 403);
        }

        $validated = $request->validate([
            'file' => ['required', 'file', 'mimes:pdf,doc,docx', 'max:' . $this->effectiveUploadLimitKilobytes()],
        ]);

        $file = $validated['file'];
        $path = $file->store('laporan');

        $laporan = Laporan::create([
            'user_id' => $request->user()->id,
            'nama_file' => $file->getClientOriginalName(),
            'file_path' => $path,
            'is_validated' => false,
            'is_rejected' => false,
        ]);

        return response()->json([
            'message' => 'Laporan berhasil diunggah dan menunggu validasi admin.',
            'data' => $laporan,
        ], 201);
    }

    public function show(Request $request, Laporan $laporan): JsonResponse
    {
        $this->authorizeOwner($request, $laporan);

        return response()->json([
            'data' => $this->formatLaporan($laporan),
        ]);
    }

    public function update(Request $request, Laporan $laporan): JsonResponse
    {
        $this->authorizeOwner($request, $laporan);

        if ($laporan->is_validated) {
            return response()->json([
                'message' => 'Laporan sudah divalidasi dan tidak dapat diubah.',
            ], 422);
        }

        $validated = $request->validate([
            'file' => ['required', 'file', 'mimes:pdf,doc,docx', 'max:' . $this->effectiveUploadLimitKilobytes()],
        ]);

        try {
            $resolved = $this->resolveFile($laporan);
            Storage::disk($resolved['disk'])->delete($resolved['path']);
        } catch (\Throwable $e) {
            // File lama boleh saja sudah tidak ada; data laporan tetap bisa diperbarui.
        }

        $file = $validated['file'];
        $path = $file->store('laporan');

        $laporan->update([
            'file_path' => $path,
            'nama_file' => $file->getClientOriginalName(),
            'is_validated' => false,
            'is_rejected' => false,
            'rejected_at' => null,
            'rejected_note' => null,
        ]);

        return response()->json([
            'message' => 'Laporan berhasil diperbarui.',
            'data' => $this->formatLaporan($laporan->fresh()),
        ]);
    }

    public function destroy(Request $request, Laporan $laporan): JsonResponse
    {
        $this->authorizeOwner($request, $laporan);

        if ($laporan->is_validated) {
            return response()->json([
                'message' => 'Laporan sudah divalidasi dan tidak dapat dihapus.',
            ], 422);
        }

        try {
            $resolved = $this->resolveFile($laporan);
            Storage::disk($resolved['disk'])->delete($resolved['path']);
        } catch (\Throwable $e) {
            // Kalau file fisik tidak ditemukan, record tetap boleh dihapus.
        }

        $laporan->delete();

        return response()->json([
            'message' => 'Laporan berhasil dihapus.',
        ]);
    }

    public function download(Request $request, Laporan $laporan)
    {
        $this->authorizeOwner($request, $laporan);

        $resolved = $this->resolveFile($laporan);

        return response()->download($resolved['absolute'], $laporan->nama_file);
    }

    public function preview(Request $request, Laporan $laporan)
    {
        $this->authorizeOwner($request, $laporan);

        $resolved = $this->resolveFile($laporan);
        $mime = @mime_content_type($resolved['absolute']) ?: 'application/octet-stream';

        return response()->file($resolved['absolute'], [
            'Content-Type' => $mime,
            'Content-Disposition' => 'inline; filename="' . $laporan->nama_file . '"',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    private function authorizeOwner(Request $request, Laporan $laporan): void
    {
        abort_if($laporan->user_id !== $request->user()->id, 403, 'Akses ditolak.');
    }

    private function formatLaporan(Laporan $laporan): array
    {
        return [
            'id' => $laporan->id,
            'user_id' => $laporan->user_id,
            'nama_file' => $laporan->nama_file,
            'file_path' => $laporan->file_path,
            'is_validated' => $laporan->is_validated,
            'is_rejected' => $laporan->is_rejected,
            'rejected_at' => $laporan->rejected_at,
            'rejected_note' => $laporan->rejected_note,
            'created_at' => $laporan->created_at,
            'updated_at' => $laporan->updated_at,
            'download_url' => route('api.laporans.download', $laporan),
            'preview_url' => route('api.laporans.preview', $laporan),
        ];
    }

    private function resolveFile(Laporan $laporan): array
    {
        $path = ltrim($laporan->file_path ?? '', '/');
        $candidates = [$path];

        if (str_starts_with($path, 'public/')) {
            $candidates[] = substr($path, 7);
        } else {
            $candidates[] = 'public/' . $path;
        }

        foreach (['local', 'public'] as $disk) {
            foreach ($candidates as $candidate) {
                if ($candidate && Storage::disk($disk)->exists($candidate)) {
                    return [
                        'disk' => $disk,
                        'path' => $candidate,
                        'absolute' => Storage::disk($disk)->path($candidate),
                    ];
                }
            }
        }

        abort(404, 'File tidak ditemukan: ' . ($laporan->file_path ?? '-'));
    }

    private function effectiveUploadLimitKilobytes(): int
    {
        $configuredMegabytes = max((int) env('LAPORAN_UPLOAD_MAX_MB', 20), 1);

        return $configuredMegabytes * 1024;
    }
}
