<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User; // ✅ TAMBAH INI

class DashboardController extends Controller
{
    /**
     * Dashboard untuk user login (mahasiswa / admin)
     * - Kita load relasi assessment biar Auth::user()->assessment langsung ada.
     */
    public function index()
    {
        $user = Auth::user();

        // ✅ FIX: Jangan pakai $user->load() karena Auth::user() bisa GenericUser
        // ✅ Ambil ulang dari Eloquent supaya relasi bisa dipakai
        $user = User::with([
            'assessment', // ambil assessment terbaru (latestOfMany)
        ])->findOrFail(Auth::id());

        // ✅ Kalau kamu punya dashboard admin berbeda, bisa redirect di sini (opsional)
        // if ($user->role === 'admin') {
        //     return redirect()->route('admin.dashboard');
        // }

        return view('dashboard', compact('user'));
    }
}
