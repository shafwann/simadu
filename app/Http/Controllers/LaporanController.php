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
        $request->validate([
            'file' => 'required|file|max:20480', // max 20 MB
        ]);

        $file = $request->file('file');
        $filename = $file->getClientOriginalName();
        $path = $file->store('laporan');

        Laporan::create([
            'user_id' => auth()->id(),
            'nama_file' => $filename,
            'file_path' => $path,
        ]);

        return back()->with('success', 'Laporan berhasil diunggah!');
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
            abort(403, "Akses ditolak");
        }

        return Storage::download($laporan->path, $laporan->nama_file);
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

}
