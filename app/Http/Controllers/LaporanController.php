<?php

namespace App\Http\Controllers;

use App\Models\Laporan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LaporanController extends Controller
{
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

        // simpan ke storage/app/laporan/...
        $path = $file->store('laporan');

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

        // ✅ Path sesuai file yang di-store()
        $filePath = storage_path('app/' . $laporan->file_path);

        if (!file_exists($filePath)) {
            abort(404, 'File tidak ditemukan: ' . $laporan->file_path);
        }

        return response()->download($filePath, $laporan->nama_file);
    }

    // Preview pdf/doc (kalau bukan pdf, browser biasanya download)
    public function preview(Laporan $laporan)
    {
        $user = auth()->user();

        if ($user->id !== $laporan->user_id && $user->role !== 'admin') {
            abort(403);
        }

        if (!Storage::disk('private')->exists($laporan->file_path)) {
            abort(404, 'File tidak ditemukan');
        }

        $filePath = Storage::disk('private')->path($laporan->file_path);

        return response()->file($filePath, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline',
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

        // hapus file lama
        Storage::delete($laporan->file_path);

        // simpan file baru
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
