<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    public function index()
    {
        $attendance = Attendance::where('user_id', Auth::id())
            ->whereDate('tanggal', Carbon::today())
            ->first();

        return view('attendance.index', compact('attendance'));
    }

    // --- LOGIKA CHECK-IN (MASUK) ---
    public function checkIn(Request $request)
    {
        $request->validate(['location' => 'required']);

        // 1. Cek Hari (Opsional: Cegah absen Sabtu/Minggu)
        // if (Carbon::now()->isWeekend()) {
        //     return back()->with('error', 'Hari ini libur, tidak perlu absen.');
        // }

        // 2. Tentukan Batas Jam Masuk (08:00)
        $jamMasukTelat = Carbon::createFromTime(8, 0, 0); // Jam 08:00:00
        $sekarang = Carbon::now();

        // 3. Tentukan Status
        // Jika waktu sekarang LEBIH DARI jam 08:00, status = Telat
        if ($sekarang->gt($jamMasukTelat)) {
            $status = 'Telat';
            $pesan = 'Absen berhasil, namun Anda tercatat Telat (Lewat jam 08:00).';
        } else {
            $status = 'Hadir';
            $pesan = 'Berhasil melakukan presensi tepat waktu!';
        }

        // 4. Validasi Jarak & Simpan (Kode Lama)
        $officeLat = -7.594917597849972;
        $officeLng = 110.94065517764916;
        $maxRadius = 500;

        $userLoc = explode(',', $request->location);
        $distance = $this->calculateDistance($officeLat, $officeLng, $userLoc[0], $userLoc[1]);

        if ($distance > $maxRadius) {
            return back()->with('error', 'Gagal! Jarak Anda ' . round($distance) . 'm dari kantor.');
        }

        Attendance::create([
            'user_id' => Auth::id(),
            'tanggal' => now()->toDateString(),
            'check_in' => now()->toTimeString(),
            'status' => $status, // <--- Simpan status (Hadir/Telat)
            'location' => $request->location,
        ]);

        return back()->with('success', $pesan);
    }

    // --- LOGIKA CHECK-OUT (PULANG) ---
    public function checkOut(Request $request)
    {
        $request->validate(['location' => 'required']);

        // 1. Tentukan Jam Mulai Pulang (14:30)
        $jamBolehPulang = Carbon::createFromTime(14, 30, 0);
        $sekarang = Carbon::now();

        // 2. Cek Apakah Sudah Waktunya?
        if ($sekarang->lt($jamBolehPulang)) {
            return back()->with('error', 'Belum waktunya pulang! Absen pulang baru dibuka jam 14:30.');
        }

        // 3. Validasi Jarak & Update (Kode Lama)
        $officeLat = -7.594917597849972;
        $officeLng = 110.94065517764916;
        $maxRadius = 500;

        $userLoc = explode(',', $request->location);
        $distance = $this->calculateDistance($officeLat, $officeLng, $userLoc[0], $userLoc[1]);

        if ($distance > $maxRadius) {
            return back()->with('error', 'Gagal! Anda masih di luar area kantor (' . round($distance) . 'm).');
        }

        $attendance = Attendance::where('user_id', Auth::id())
            ->whereDate('tanggal', Carbon::today())
            ->first();

        $attendance->update(['check_out' => now()->toTimeString()]);

        return back()->with('success', 'Hati-hati di jalan! Absen pulang berhasil.');
    }

    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371000;
        $latFrom = deg2rad($lat1); $lonFrom = deg2rad($lon1);
        $latTo = deg2rad($lat2); $lonTo = deg2rad($lon2);
        $latDelta = $latTo - $latFrom; $lonDelta = $lonTo - $lonFrom;
        $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) + cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));
        return $angle * $earthRadius;
    }
}
