<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    public function index()
    {
        // Mengambil riwayat absen user yang login hari ini
        $attendance = Attendance::where('user_id', Auth::id())
                                ->whereDate('tanggal', Carbon::today())
                                ->first();

        return view('attendance.index', compact('attendance'));
    }

    // PERBAIKAN: Tambahkan (Request $request) di sini
    public function checkIn(Request $request)
    {
        $userId = Auth::id();

        // Mengatur timezone secara manual ke Asia/Jakarta
        $now = Carbon::now('Asia/Jakarta');
        $today = $now->toDateString();
        $timeNow = $now->toTimeString();

        $exists = Attendance::where('user_id', $userId)
                            ->whereDate('tanggal', $today)
                            ->exists();

        if (!$exists) {
            Attendance::create([
                'user_id' => $userId,
                'tanggal' => $today,
                'check_in' => $timeNow,
                'status' => 'hadir',
                'location' => $request->location, // Sekarang $request sudah bisa dibaca
            ]);

            return redirect()->back()->with('success', 'Berhasil presensi pada jam ' . $timeNow . ' WIB');
        }

        return redirect()->back()->with('error', 'Kamu sudah presensi hari ini.');
    }
}
