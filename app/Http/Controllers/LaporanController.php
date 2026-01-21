<?php

namespace App\Http\Controllers;

use App\Models\Laporan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LaporanController extends Controller
{
    // =========================
    // Helper: cari file di disk local/public
    // =========================
    private function resolveFile(Laporan $laporan): array
    {
        // file_path biasanya: "laporan/xxx.pdf" atau kadang "public/laporan/xxx.pdf"
        $path = ltrim($laporan->file_path ?? '', '/');

        // Kandidat path yang mungkin tersimpan
        $candidates = [
            $path,
        ];

        // Kalau ada yang menyimpan "public/xxx", coba juga versi tanpa "public/"
        if (str_starts_with($path, 'public/')) {
            $candidates[] = substr($path, 7);
        } else {
            // Kalau tidak ada "public/", coba juga versi dengan "public/"
            $candidates[] = 'public/' . $path;
        }

        // Cek disk local dan public
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

        // Tidak ketemu
        abort(404, 'File tidak ditemukan: ' . ($laporan->file_path ?? '-'));
    }

    // Mahasiswa lihat list & upload
    public function index()
    {
        $laporans = auth()->user()->laporans()->latest()->get();
        $bidangOptions = \App\Models\AboutSetting::bidangOptions();

        return view('laporan.index', compact('laporans', 'bidangOptions'));
    }

    public function validateLaporan($id)
    {
        if (auth()->user()->role !== 'admin') {
            abort(403);
        }

        $laporan = Laporan::findOrFail($id);

        if ($laporan->is_validated) {
            return back()->with('error', 'Laporan sudah divalidasi.');
        }

        $laporan->update(['is_validated' => true]);

        return back()->with('success', 'Laporan berhasil divalidasi.');
    }

    public function upload(Request $request)
    {
        $user = auth()->user();
        if ($user->status !== 'aktif') {
            return back()->with('error', 'Akun Anda tidak aktif, tidak bisa mengunggah laporan.');
        }

        $request->validate(
            [
                'file' => 'required|file|mimes:pdf,doc,docx|max:20480',
            ],
            [
                'file.required' => 'Silakan pilih file laporan terlebih dahulu.',
                'file.file'     => 'Data yang diunggah harus berupa file.',
                'file.mimes'    => 'Format file harus PDF, DOC, atau DOCX.',
                'file.max'      => 'Ukuran file maksimal 20 MB.',
            ]
        );

        $file = $request->file('file');
        $filename = $file->getClientOriginalName();

        // ✅ BIARKAN seperti semula: ikut default disk (biar tidak mengubah perilaku lama)
        $path = $file->store('laporan'); // bisa masuk local atau public tergantung config

        Laporan::create([
            'user_id' => auth()->id(),
            'nama_file' => $filename,
            'file_path' => $path,
            'is_validated' => false,
        ]);

        return back()->with('success', 'Laporan berhasil diunggah dan menunggu validasi admin.');
    }

    // Admin lihat semua
    public function adminIndex()
    {
        if (auth()->user()->role !== 'admin') {
            abort(403);
        }

        $laporans = Laporan::with('user')->latest()->get();
        return view('laporan.admin', compact('laporans'));
    }

    // Download file (admin & owner)
    public function download(Laporan $laporan)
    {
        $user = auth()->user();

        if ($user->id !== $laporan->user_id && $user->role !== 'admin') {
            abort(403, 'Akses ditolak');
        }

        $resolved = $this->resolveFile($laporan);

        return response()->download($resolved['absolute'], $laporan->nama_file);
    }

    // Preview (pdf tampil di iframe, doc/docx biasanya download)
    public function preview(Laporan $laporan)
    {
        $user = auth()->user();

        if ($user->id !== $laporan->user_id && $user->role !== 'admin') {
            abort(403);
        }

        $resolved = $this->resolveFile($laporan);

        $mime = @mime_content_type($resolved['absolute']) ?: 'application/octet-stream';

        return response()->file($resolved['absolute'], [
            'Content-Type' => $mime,
            'Content-Disposition' => 'inline; filename="' . $laporan->nama_file . '"',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    public function edit(Laporan $laporan)
    {
        if (auth()->id() !== $laporan->user_id) {
            abort(403);
        }

        if ($laporan->is_validated) {
            return back()->withErrors('Laporan sudah divalidasi dan tidak dapat diubah.');
        }

        return view('laporan.edit', compact('laporan'));
    }

    public function update(Request $request, Laporan $laporan)
    {
        if (auth()->id() !== $laporan->user_id) {
            abort(403);
        }

        if ($laporan->is_validated) {
            return back()->withErrors('Laporan sudah divalidasi dan tidak dapat diubah.');
        }

        $request->validate([
            'file' => 'required|file|mimes:pdf,doc,docx|max:20480',
        ], [
            'file.required' => 'Silakan pilih file pengganti.',
            'file.mimes' => 'Format harus PDF, DOC, atau DOCX.',
            'file.max' => 'Ukuran maksimal 20 MB.',
        ]);

        // ✅ hapus file lama: coba cari dulu di disk local/public
        try {
            $resolved = $this->resolveFile($laporan);
            Storage::disk($resolved['disk'])->delete($resolved['path']);
        } catch (\Throwable $e) {
            // kalau file lama memang hilang, lanjut saja upload baru
        }

        // simpan file baru (ikut default disk seperti semula)
        $file = $request->file('file');
        $path = $file->store('laporan');

        $laporan->update([
            'file_path' => $path,
            'nama_file' => $file->getClientOriginalName(),
            'is_validated' => false,
        ]);

        return redirect()->route('laporan.index')
            ->with('success', 'Laporan berhasil diperbarui.');
    }

    // ✅ Tambahan: Hapus laporan (mahasiswa owner / admin sesuai kebutuhan)
    public function destroy(Laporan $laporan)
    {
        $user = auth()->user();

        // hanya owner yang boleh hapus (kalau mau admin juga boleh, tinggal tambahkan OR role admin)
        if ($user->id !== $laporan->user_id) {
            abort(403);
        }

        // kalau sudah divalidasi, jangan boleh dihapus
        if ($laporan->is_validated) {
            return back()->with('error', 'Laporan sudah divalidasi dan tidak dapat dihapus.');
        }

        // hapus file fisik
        Storage::delete($laporan->file_path);

        // hapus record
        $laporan->delete();

        return back()->with('success', 'Laporan berhasil dihapus.');
    }
}
