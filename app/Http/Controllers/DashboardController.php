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

        if ($user->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        if ($user->role === 'pembimbing') {
            return redirect()->route('pembimbing.dashboard');
        }

        // default = mahasiswa
        return view('dashboard', [
            'user' => User::with('assessment')->findOrFail($user->id)
        ]);
    }

}
