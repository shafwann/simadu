<?php

namespace App\Http\Controllers;

use App\Models\Laporan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LaporanController extends Controller
{
    private function iniSizeToBytes(string $value): int
    {
        $value = trim($value);
        if ($value === '') {
            return 0;
        }

        $number = (float) $value;
        $unit = strtolower(substr($value, -1));

        return match ($unit) {
            'g' => (int) ($number * 1024 * 1024 * 1024),
            'm' => (int) ($number * 1024 * 1024),
            'k' => (int) ($number * 1024),
            default => (int) $number,
        };
    }

    private function getServerUploadLimitBytes(): int
    {
        $uploadMax = $this->iniSizeToBytes((string) ini_get('upload_max_filesize'));
        $postMax = $this->iniSizeToBytes((string) ini_get('post_max_size'));

        $limits = array_filter([$uploadMax, $postMax]);

        return $limits === [] ? 0 : min($limits);
    }

    private function getServerUploadLimitKilobytes(): int
    {
        $bytes = $this->getServerUploadLimitBytes();

        return $bytes > 0 ? (int) floor($bytes / 1024) : 20480;
    }

    private function getServerUploadLimitLabel(): string
    {
        $bytes = $this->getServerUploadLimitBytes();

        if ($bytes <= 0) {
            return '20 MB';
        }

        $megabytes = $bytes / 1024 / 1024;

        if ($megabytes >= 1) {
            $formatted = fmod($megabytes, 1.0) === 0.0
                ? number_format($megabytes, 0)
                : number_format($megabytes, 1);

            return $formatted . ' MB';
        }

        return number_format($bytes / 1024, 0) . ' KB';
    }

    private function validateUploadedLaporan(Request $request): void
    {
        $uploadError = data_get($_FILES, 'file.error');
        $maxLabel = $this->getServerUploadLimitLabel();

        if ($uploadError === UPLOAD_ERR_INI_SIZE || $uploadError === UPLOAD_ERR_FORM_SIZE) {
            back()->withInput()->withErrors([
                'file' => "Ukuran file melebihi batas upload server ({$maxLabel}).",
            ])->throwResponse();
        }

        $request->validate(
            [
                'file' => 'required|file|mimes:pdf,doc,docx|max:' . $this->getServerUploadLimitKilobytes(),
            ],
            [
                'file.required' => 'Silakan pilih file laporan terlebih dahulu.',
                'file.file'     => 'Data yang diunggah harus berupa file yang valid.',
                'file.mimes'    => 'Format file harus PDF, DOC, atau DOCX.',
                'file.max'      => "Ukuran file maksimal {$maxLabel}.",
            ]
        );
    }

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
        $maxUploadLabel = $this->getServerUploadLimitLabel();

        return view('laporan.index', compact('laporans', 'bidangOptions', 'maxUploadLabel'));
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

        $this->validateUploadedLaporan($request);

        $file = $request->file('file');
        $filename = $file->getClientOriginalName();

        // BIARKAN seperti semula: ikut default disk (biar tidak mengubah perilaku lama)
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

        $maxUploadLabel = $this->getServerUploadLimitLabel();

        return view('laporan.edit', compact('laporan', 'maxUploadLabel'));
    }

    public function update(Request $request, Laporan $laporan)
    {
        if (auth()->id() !== $laporan->user_id) {
            abort(403);
        }

        if ($laporan->is_validated) {
            return back()->withErrors('Laporan sudah divalidasi dan tidak dapat diubah.');
        }

        $this->validateUploadedLaporan($request);

        // hapus file lama: coba cari dulu di disk local/public
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

    // Tambahan: Hapus laporan (mahasiswa owner / admin sesuai kebutuhan)
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

    // TAMBAHAN BARU: Hapus laporan oleh ADMIN (hapus juga file & record DB)
    public function adminDestroy(Laporan $laporan)
    {
        if (auth()->user()->role !== 'admin') {
            abort(403);
        }

        // hapus file fisik (aman local/public + kandidat public/)
        try {
            $resolved = $this->resolveFile($laporan);
            Storage::disk($resolved['disk'])->delete($resolved['path']);
        } catch (\Throwable $e) {
            // kalau file sudah tidak ada, tetap lanjut hapus record
        }

        // hapus record DB
        $laporan->delete();

        return back()->with('success', 'Laporan berhasil dihapus oleh admin (file ikut terhapus).');
    }

    // ajuan
    public function ajuan(Laporan $laporan)
    {
        if (auth()->id() !== $laporan->user_id) {
            abort(403);
        }

        if (!$laporan->is_rejected) {
            return back()->withErrors('Laporan tidak dalam status ditolak, tidak bisa diajukan kembali.');
        }

        $maxUploadLabel = $this->getServerUploadLimitLabel();

        return view('laporan.ajuan', compact('laporan', 'maxUploadLabel'));
    }

    // ajuan-kembali
    public function ajuanKembali(Request $request, Laporan $laporan)
    {
        if (auth()->id() !== $laporan->user_id) {
            abort(403);
        }

        if ($laporan->is_validated) {
            return back()->withErrors('Laporan sudah divalidasi dan tidak dapat diubah.');
        }

        $this->validateUploadedLaporan($request);

        try {
            $resolved = $this->resolveFile($laporan);
            Storage::disk($resolved['disk'])->delete($resolved['path']);
        } catch (\Throwable $e) {
            // kalau file lama memang hilang, lanjut saja upload baru
        }

        // simpan file baru
        $file = $request->file('file');
        $path = $file->store('laporan');

        $laporan->update([
            'file_path' => $path,
            'nama_file' => $file->getClientOriginalName(),
            'is_validated' => false,
            'is_rejected' => false,
            'rejected_at' => null,
            'rejected_note' => null,
        ]);

        return redirect()->route('laporan.index')
            ->with('success', 'Laporan berhasil diperbarui dan diajukan kembali.');
    }
}
