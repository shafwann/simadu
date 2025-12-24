<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Logbook;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class LogbookController extends Controller
{
        public function index()
        {
            // Mengambil ID user menggunakan Facade yang lebih dikenal oleh editor
            $userId = \Illuminate\Support\Facades\Auth::id();

            $logbooks = \App\Models\Logbook::where('user_id', $userId)
                                        ->orderBy('tanggal', 'desc')
                                        ->get();

            return view('logbook.index', compact('logbooks'));
        }

        public function store(Request $request)
        {
            // 1. Validasi input
            $request->validate([
                'tanggal' => 'required|date',
                'kegiatan' => 'required|string|min:5',
            ]);

            try {
                // 2. Simpan ke database
                Logbook::create([
                    'user_id' => \Illuminate\Support\Facades\Auth::id(),
                    'tanggal' => $request->tanggal,
                    'kegiatan' => $request->kegiatan,
                    'is_validated' => false, // Default awal belum divalidasi
                ]);

                // 3. Kembalikan ke halaman sebelumnya dengan pesan sukses
                return redirect()->route('logbook.index')->with('success', 'Kegiatan berhasil disimpan!');

            } catch (\Exception $e) {
                // Jika ada error database, munculkan di layar
                return redirect()->back()->with('error', 'Gagal menyimpan: ' . $e->getMessage());
            }
        }
}
