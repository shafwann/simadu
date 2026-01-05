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
        return view('laporan.index', compact('laporans'));
    }

    public function upload(Request $request)
    {
        // CEK STATUS AKTIF (sama kayak logbook)
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
        $path = $file->store('laporan');

        Laporan::create([
            'user_id' => auth()->id(),
            'nama_file' => $filename,
            'file_path' => $path,
            'is_validated' => false, // PENTING, sama seperti logbook
        ]);

        return back()->with('success', 'Laporan berhasil diunggah dan menunggu validasi admin.');
    }


    //validasi laporan
    public function validateLaporan($id)
    {
        // pastikan admin
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Akses ditolak');
        }

        $laporan = Laporan::findOrFail($id);

        // validasi laporan
        $laporan->update([
            'is_validated' => true,
            'validated_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Laporan berhasil divalidasi');
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

        $filePath = storage_path(
            'app/private/laporan/' . basename($laporan->file_path)
        );

        if (!file_exists($filePath)) {
            abort(404, 'File tidak ditemukan');
        }

        return response()->download($filePath, $laporan->nama_file);
    }


    //lihat preview pdf
    public function preview(Laporan $laporan)
    {
        $user = auth()->user();

        // cuma owner atau admin
        if ($user->id !== $laporan->user_id && $user->role !== 'admin') {
            abort(403, "Akses ditolak");
        }

        $filePath = storage_path('app/private/laporan/' . basename($laporan->file_path));

        if (!file_exists($filePath)) {
            abort(404, "File tidak ditemukan");
        }

        // preview di browser
        return response()->file($filePath, [
            'Content-Type' => 'application/pdf', // wajib!
            'Content-Disposition' => 'inline; filename="' . $laporan->nama_file . '"'
        ]);
    }

    //edit laporan
    public function edit(Laporan $laporan)
    {
        // hanya pemilik
        if (auth()->id() !== $laporan->user_id) {
            abort(403);
        }

        // sudah divalidasi → tidak boleh edit
        if ($laporan->is_validated) {
            return back()->withErrors('Laporan sudah divalidasi dan tidak dapat diubah.');
        }

        return view('laporan.edit', compact('laporan'));
    }

    //update laporan
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
            'is_validated' => false, // reset kalau perlu
        ]);

        return redirect()->route('laporan.index')
            ->with('success', 'Laporan berhasil diperbarui.');
    }




}
