<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Attendance;
use App\Models\Logbook;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index(Request $request)
{
    $search = $request->input('search');

    // Ambil semua mahasiswa untuk statistik
    $users = User::where('role', 'mahasiswa')->get();

    // Logbook yang butuh validasi (dengan Fitur Search)
    $pendingLogbooks = Logbook::with('user')
        ->where('is_validated', false)
        ->when($search, function ($query, $search) {
            return $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%');
            });
        })
        ->orderBy('tanggal', 'desc')
        ->get();

    return view('admin.dashboard', compact('users', 'pendingLogbooks', 'search'));
}

    public function validateLogbook($id)
    {
        $logbook = Logbook::findOrFail($id);
        $logbook->update(['is_validated' => true]);

        return redirect()->back()->with('success', 'Logbook berhasil divalidasi!');
    }
}
