<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\Logbook;
use App\Models\AboutSetting;

class AttendanceController extends Controller
{
    public function index()
    {
        $attendance = Attendance::where('user_id', Auth::id())
            ->whereDate('tanggal', Carbon::today())
            ->orderByDesc('id')
            ->first();

        return view('attendance.index', compact('attendance'));
    }

    //--logika cek status mahasiswa
    public function store(Request $request)
    {
        $user = Auth::user();

        // --- CEK STATUS AKTIF ---
        if ($user->status !== 'aktif') {
            return redirect()->back()->with('error', 'Akun Anda tidak aktif, tidak bisa mengisi logbook.');
        }

        $request->validate([
            'tanggal' => 'required|date',
            'kegiatan' => 'required|string|min:5',
        ]);

        try {
            Logbook::create([
                'user_id' => $user->id,
                'tanggal' => $request->tanggal,
                'kegiatan' => $request->kegiatan,
                'is_validated' => false,
            ]);

            return redirect()->route('logbook.index')->with('success', 'Kegiatan berhasil disimpan!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menyimpan: ' . $e->getMessage());
        }
    }

    /**
     * Ambil jadwal absensi hari ini (check-in & check-out) dari AboutSetting.
     */
    private function getTodayAbsensiSchedule(): array
    {
        $about = AboutSetting::first();

        if (!$about) {
            return ['error' => 'Jam absensi belum diatur oleh admin.'];
        }

        $now = Carbon::now('Asia/Jakarta');
        $dow = $now->dayOfWeekIso; // 1=Senin ... 5=Jumat ... 7=Minggu

        if ($dow >= 6) {
            return ['error' => 'Absensi tidak tersedia di hari Sabtu/Minggu.'];
        }

        $map = [
            1 => [
                'label' => 'Senin',
                'checkin_mulai' => $about->senin_mulai,
                'checkin_selesai' => $about->senin_selesai,
                'checkout_mulai' => $about->senin_checkout_mulai,
                'checkout_selesai' => $about->senin_checkout_selesai,
            ],
            2 => [
                'label' => 'Selasa',
                'checkin_mulai' => $about->selasa_mulai,
                'checkin_selesai' => $about->selasa_selesai,
                'checkout_mulai' => $about->selasa_checkout_mulai,
                'checkout_selesai' => $about->selasa_checkout_selesai,
            ],
            3 => [
                'label' => 'Rabu',
                'checkin_mulai' => $about->rabu_mulai,
                'checkin_selesai' => $about->rabu_selesai,
                'checkout_mulai' => $about->rabu_checkout_mulai,
                'checkout_selesai' => $about->rabu_checkout_selesai,
            ],
            4 => [
                'label' => 'Kamis',
                'checkin_mulai' => $about->kamis_mulai,
                'checkin_selesai' => $about->kamis_selesai,
                'checkout_mulai' => $about->kamis_checkout_mulai,
                'checkout_selesai' => $about->kamis_checkout_selesai,
            ],
            5 => [
                'label' => 'Jumat',
                'checkin_mulai' => $about->jumat_mulai,
                'checkin_selesai' => $about->jumat_selesai,
                'checkout_mulai' => $about->jumat_checkout_mulai,
                'checkout_selesai' => $about->jumat_checkout_selesai,
            ],
        ];

        $today = $map[$dow];

        if (
            !$today['checkin_mulai'] || !$today['checkin_selesai'] ||
            !$today['checkout_mulai'] || !$today['checkout_selesai']
        ) {
            return ['error' => 'Jam check-in/check-out hari ini belum diatur oleh admin.'];
        }

        return $today;
    }

    private function ensureWithinCheckInWindow(): ?string
    {
        $today = $this->getTodayAbsensiSchedule();
        if (isset($today['error'])) return $today['error'];

        $now = Carbon::now('Asia/Jakarta');
        $start = Carbon::createFromTimeString($today['checkin_mulai'], 'Asia/Jakarta');
        $end = Carbon::createFromTimeString($today['checkin_selesai'], 'Asia/Jakarta');

        if ($now->lt($start) || $now->gt($end)) {
            return "Absensi masuk hanya bisa dilakukan pada jam {$start->format('H:i')} - {$end->format('H:i')} WIB ({$today['label']}).";
        }

        return null;
    }

    private function ensureWithinCheckOutWindow(): ?string
    {
        $today = $this->getTodayAbsensiSchedule();
        if (isset($today['error'])) return $today['error'];

        $now = Carbon::now('Asia/Jakarta');
        $start = Carbon::createFromTimeString($today['checkout_mulai'], 'Asia/Jakarta');
        $end = Carbon::createFromTimeString($today['checkout_selesai'], 'Asia/Jakarta');

        if ($now->lt($start) || $now->gt($end)) {
            return "Absensi pulang hanya bisa dilakukan pada jam {$start->format('H:i')} - {$end->format('H:i')} WIB ({$today['label']}).";
        }

        return null;
    }

    // --- LOGIKA CHECK-IN (MASUK) ---
    public function checkIn(Request $request)
    {
        $user = Auth::user();
        if ($user->status !== 'aktif') {
            return back()->with('error', 'Akun Anda tidak aktif, tidak bisa melakukan presensi.');
        }

        $msg = $this->ensureWithinCheckInWindow();
        if ($msg) return back()->with('error', $msg);

        $request->validate(['location' => 'required']);

        // Status telat (tetap sesuai kode lama)
        $jamMasukTelat = Carbon::createFromTime(8, 0, 0);
        $sekarang = Carbon::now();

        if ($sekarang->gt($jamMasukTelat)) {
            $status = 'Telat';
            $pesan = 'Absen berhasil, namun Anda tercatat Telat (Lewat jam 08:00).';
        } else {
            $status = 'Hadir';
            $pesan = 'Berhasil melakukan presensi tepat waktu!';
        }

        // Validasi jarak
        $officeLat = -7.594917597849972;
        $officeLng = 110.94065517764916;
        $maxRadius = 99999;

        // ============================
        // TAMBAHAN: Ambil koordinat dari setting Super Admin (jika ada)
        // ============================
        $about = AboutSetting::first();
        if ($about && $about->office_latitude && $about->office_longitude) {
            $officeLat = $about->office_latitude;
            $officeLng = $about->office_longitude;
            $maxRadius = $about->radius_meter ?? $maxRadius;
        }
        // ============================

        $userLoc = explode(',', $request->location);
        $distance = $this->calculateDistance($officeLat, $officeLng, $userLoc[0], $userLoc[1]);

        if ($distance > $maxRadius) {
            return back()->with('error', 'Gagal! Jarak Anda ' . round($distance) . 'm dari kantor.');
        }

        Attendance::create([
            'user_id' => Auth::id(),
            'tanggal' => now()->toDateString(),
            'check_in' => now()->toTimeString(),
            'status' => $status,
            'location' => $request->location,
        ]);

        return back()->with('success', $pesan);
    }

    // --- LOGIKA CHECK-OUT (PULANG) ---
    public function checkOut(Request $request)
    {
        $request->validate(['location' => 'required']);

        $msg = $this->ensureWithinCheckOutWindow();
        if ($msg) return back()->with('error', $msg);

        // Validasi jarak
        $officeLat = -7.594917597849972;
        $officeLng = 110.94065517764916;
        $maxRadius = 99999;

        // ============================
        // TAMBAHAN: Ambil koordinat dari setting Super Admin (jika ada)
        // ============================
        $about = AboutSetting::first();
        if ($about && $about->office_latitude && $about->office_longitude) {
            $officeLat = $about->office_latitude;
            $officeLng = $about->office_longitude;
            $maxRadius = $about->radius_meter ?? $maxRadius;
        }
        // ============================

        $userLoc = explode(',', $request->location);
        $distance = $this->calculateDistance($officeLat, $officeLng, $userLoc[0], $userLoc[1]);

        if ($distance > $maxRadius) {
            return back()->with('error', 'Gagal! Anda masih di luar area kantor (' . round($distance) . 'm).');
        }

        $attendance = Attendance::where('user_id', Auth::id())
            ->whereDate('tanggal', Carbon::today())
            ->first();

        if (!$attendance) {
            return back()->with('error', 'Anda belum melakukan check-in hari ini.');
        }

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
