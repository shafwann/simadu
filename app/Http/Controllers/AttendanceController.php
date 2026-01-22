<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\Logbook;
use App\Models\AboutSetting;
use Barryvdh\DomPDF\Facade\Pdf;

class AttendanceController extends Controller
{
    // ============================
    // Helper: ambil AboutSetting sesuai instansi user
    // ============================
    private function getAboutForUser(): ?AboutSetting
    {
        $user = Auth::user();
        $kategori = $user->kategori_instansi ?? 'UMUM';

        return AboutSetting::where('kategori_instansi', $kategori)->first();
    }

    public function index()
    {
        $attendance = Attendance::where('user_id', Auth::id())
            ->whereDate('tanggal', Carbon::today())
            ->orderByDesc('id')
            ->first();

        $schedule = $this->getTodayAbsensiSchedule();

        // optional info buat view
        $checkinTooEarly = false;   // sebelum checkin_mulai
        $checkoutExpired = false;   // lewat checkout_selesai
        $scheduleError = $schedule['error'] ?? null;

        if (!$scheduleError) {
            $now = Carbon::now('Asia/Jakarta');

            $checkinStart = Carbon::createFromTimeString($schedule['checkin_mulai'], 'Asia/Jakarta');
            $checkoutEnd  = Carbon::createFromTimeString($schedule['checkout_selesai'], 'Asia/Jakarta');

            $checkinTooEarly = $now->lt($checkinStart);
            $checkoutExpired = $now->gt($checkoutEnd);
        }

        return view('attendance.index', compact(
            'attendance',
            'schedule',
            'checkinTooEarly',
            'checkoutExpired',
            'scheduleError'
        ));
    }

    public function history()
    {
        $user = Auth::user();
        $kategori = $user->kategori_instansi ?? 'UMUM';

        $about = AboutSetting::where('kategori_instansi', $kategori)->first();

        $attendances = Attendance::where('user_id', $user->id)
            ->orderBy('tanggal', 'desc')
            ->get();

        // jadwal checkout selesai per hari (untuk TAP)
        $checkoutEndMap = [
            1 => $about?->senin_checkout_selesai,
            2 => $about?->selasa_checkout_selesai,
            3 => $about?->rabu_checkout_selesai,
            4 => $about?->kamis_checkout_selesai,
            5 => $about?->jumat_checkout_selesai,
        ];

        return view('attendance.history', compact('attendances', 'checkoutEndMap'));
    }


    public function presensiPdf(Request $request)
    {
        $student = Auth::user();
        $kategori = $student->kategori_instansi ?? 'UMUM';

        // ✅ penting: sesuai instansi user, bukan first()
        $about = AboutSetting::where('kategori_instansi', $kategori)->first();

        $month = (int) $request->get('month', now()->month);
        $year  = (int) $request->get('year', now()->year);

        if ($month < 1 || $month > 12) $month = now()->month;
        if ($year < 2000 || $year > 2100) $year = now()->year;

        $start = Carbon::create($year, $month, 1, 0, 0, 0, 'Asia/Jakarta');
        $end   = $start->copy()->endOfMonth();
        $daysInMonth = $start->daysInMonth;

        // ✅ PAKSA LOCALE INDONESIA BIAR NAMA BULAN INDONESIA (Januari, Februari, dst)
        // (agar tidak keluar "January")
        $bulanLabel = $start->locale('id')->translatedFormat('F');

        $attendances = Attendance::where('user_id', $student->id)
            ->whereBetween('tanggal', [$start->toDateString(), $end->toDateString()])
            ->orderBy('tanggal', 'asc')
            ->get()
            ->keyBy(fn ($a) => Carbon::parse($a->tanggal)->format('Y-m-d'));

        // ✅ map checkout_selesai per hari untuk TAP
        $checkoutEndMap = [
            1 => $about?->senin_checkout_selesai,
            2 => $about?->selasa_checkout_selesai,
            3 => $about?->rabu_checkout_selesai,
            4 => $about?->kamis_checkout_selesai,
            5 => $about?->jumat_checkout_selesai,
        ];

        $rows = [];

        for ($d = 1; $d <= $daysInMonth; $d++) {
            $date = Carbon::create($year, $month, $d, 0, 0, 0, 'Asia/Jakarta');
            $key  = $date->format('Y-m-d');

            $a = $attendances[$key] ?? null;

            $isHoliday = $date->isWeekend();

            // check in/out tampil HH:mm
            $checkin  = (!empty($a?->check_in))  ? Carbon::parse($a->check_in)->format('H:i')  : '-';
            $checkout = (!empty($a?->check_out)) ? Carbon::parse($a->check_out)->format('H:i') : '-';

            // ===== STATUS RINGKAS PDF =====
            $statusRaw = $a?->status ?? '-';
            $ketRaw    = $a?->keterangan ?? null;

            $st = strtoupper(trim((string)$statusRaw));
            $ket = strtoupper(trim((string)$ketRaw));

            $code = '-';

            if ($st === '' || $st === '-') $code = '-';
            elseif (in_array($st, ['ALPHA', 'TANPA KETERANGAN', 'TK'])) $code = 'TK';
            elseif ($st === 'HADIR') $code = 'H';
            elseif ($st === 'TELAT') $code = 'T';
            elseif (in_array($st, ['I', 'IZIN', 'IJIN', 'IJIN'])) $code = 'I';
            elseif (in_array($st, ['S', 'SAKIT'])) $code = 'S';
            elseif (in_array($st, ['DL', 'DINAS LUAR'])) $code = 'DL';
            elseif (in_array($st, ['CT', 'CUTI'])) $code = 'CT';

            $isPC = ($ket === 'PULANG CEPAT');

            $isTAP = false;
            if ($a && !empty($a->check_in) && empty($a->check_out)) {
                $dow = $date->dayOfWeekIso; // 1..7
                if ($dow <= 5) {
                    $endStr = $checkoutEndMap[$dow] ?? null;
                    if (!empty($endStr)) {
                        $endTime = Carbon::parse($date->format('Y-m-d') . ' ' . $endStr, 'Asia/Jakarta');
                        $isTAP = Carbon::now('Asia/Jakarta')->gt($endTime);
                    }
                }
            }

            $extra = [];
            if ($isPC) $extra[] = 'PC';
            if ($isTAP) $extra[] = 'TAP';

            $statusFinal = $code;
            if (!empty($extra)) {
                $statusFinal = $code . ',' . implode(',', $extra);
            }

            $lokasiText = '-';
            $lokasiUrl  = null;

            if ($a) {
                if (!empty($a->location_name)) {
                    $lokasiText = $a->location_name;
                } elseif (in_array($st, ['HADIR', 'TELAT'])) {
                    $lokasiText = 'KANTOR ' . strtoupper($kategori);
                } else {
                    $lokasiText = $statusFinal;
                }

                if (!empty($a->location) && (str_contains($a->location, 'http') || str_contains($a->location, 'maps'))) {
                    $lokasiUrl = $a->location;
                }
            }

            $rows[] = [
                'tanggal_label'  => $date->locale('id')->translatedFormat('d M Y'),
                'checkin_label'  => $checkin,
                'checkout_label' => $checkout,
                'status'         => $statusFinal,
                'status_label'   => $statusFinal,

                'location_text'  => $lokasiText,
                'location_url'   => $lokasiUrl,
                'location'       => $lokasiText,

                'isHoliday'      => $isHoliday,
            ];
        }

        $left  = array_slice($rows, 0, 16);
        $right = array_slice($rows, 16);

        // F4 points
        $F4 = [0, 0, 595.28, 935.43];

        return Pdf::loadView('admin.students.presensi_pdf', compact(
            'student',
            'about',
            'year',
            'bulanLabel',
            'left',
            'right'
        ))
        ->setPaper($F4, 'portrait')
        ->download("DAFTAR-HADIR-{$student->nim}-{$bulanLabel}-{$year}.pdf");
    }


    // logbook store (punyamu)
    public function store(Request $request)
    {
        $user = Auth::user();

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

    // ============================
    // Jadwal hari ini dari AboutSetting sesuai instansi user
    // ============================
    private function getTodayAbsensiSchedule(): array
    {
        $about = $this->getAboutForUser();
        if (!$about) return ['error' => 'Jam absensi belum diatur oleh admin untuk instansi Anda.'];

        $now = Carbon::now('Asia/Jakarta');
        $dow = $now->dayOfWeekIso;

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

        $today = $map[$dow] ?? null;
        if (!$today) return ['error' => 'Jadwal hari ini tidak ditemukan.'];

        if (
            empty($today['checkin_mulai']) || empty($today['checkin_selesai']) ||
            empty($today['checkout_mulai']) || empty($today['checkout_selesai'])
        ) {
            return ['error' => 'Jam check-in/check-out hari ini belum diatur oleh admin.'];
        }

        return $today;
    }

    // ============================
    // Office config dari AboutSetting (instansi user)
    // ============================
    private function getOfficeConfig(): array
    {
        $officeLat = -7.594917597849972;
        $officeLng = 110.94065517764916;
        $maxRadius = 1500;

        $about = $this->getAboutForUser();
        if ($about && $about->office_latitude && $about->office_longitude) {
            $officeLat = (float)$about->office_latitude;
            $officeLng = (float)$about->office_longitude;
            $maxRadius = (int)($about->radius_meter ?? $maxRadius);
        }

        return [$officeLat, $officeLng, $maxRadius];
    }

    public function checkIn(Request $request)
    {
        $user = Auth::user();
        if ($user->status !== 'aktif') {
            return back()->with('error', 'Akun Anda tidak aktif, tidak bisa melakukan presensi.');
        }

        $schedule = $this->getTodayAbsensiSchedule();
        if (isset($schedule['error'])) {
            return back()->with('error', $schedule['error']);
        }

        $request->validate([
            'location' => ['required', 'regex:/^-?\d+(\.\d+)?,-?\d+(\.\d+)?$/'],
        ]);

        $exists = Attendance::where('user_id', $user->id)
            ->whereDate('tanggal', Carbon::today())
            ->exists();

        if ($exists) {
            return back()->with('error', 'Anda sudah melakukan check-in hari ini.');
        }

        $now = Carbon::now('Asia/Jakarta');
        $checkinStart = Carbon::createFromTimeString($schedule['checkin_mulai'], 'Asia/Jakarta');
        $checkinEnd   = Carbon::createFromTimeString($schedule['checkin_selesai'], 'Asia/Jakarta');

        if ($now->lt($checkinStart)) {
            return back()->with('error', "Absensi masuk belum dibuka. Mulai pukul {$checkinStart->format('H:i')} WIB.");
        }

        [$officeLat, $officeLng, $maxRadius] = $this->getOfficeConfig();
        [$latStr, $lngStr] = explode(',', $request->location);
        $distance = $this->calculateDistance($officeLat, $officeLng, (float)$latStr, (float)$lngStr);

        if ($distance > $maxRadius) {
            return back()->with('error', 'Gagal! Jarak Anda ' . round($distance) . 'm dari kantor.');
        }

        $status = $now->gt($checkinEnd) ? 'Telat' : 'Hadir';

        Attendance::create([
            'user_id'   => $user->id,
            'tanggal'   => $now->toDateString(),
            'check_in'  => $now->toTimeString(),
            'check_out' => null,
            'status'    => $status,
            'keterangan' => null,
            'location'  => $request->location,
        ]);

        $pesan = ($status === 'Telat')
            ? 'Check-in berhasil, namun Anda TERLAMBAT.'
            : 'Check-in berhasil.';

        return back()->with('success', $pesan);
    }

    public function checkOut(Request $request)
    {
        $user = Auth::user();
        if ($user->status !== 'aktif') {
            return back()->with('error', 'Akun Anda tidak aktif, tidak bisa melakukan presensi.');
        }

        $schedule = $this->getTodayAbsensiSchedule();
        if (isset($schedule['error'])) {
            return back()->with('error', $schedule['error']);
        }

        $request->validate([
            'location' => ['required', 'regex:/^-?\d+(\.\d+)?,-?\d+(\.\d+)?$/'],
        ]);

        $attendance = Attendance::where('user_id', $user->id)
            ->whereDate('tanggal', Carbon::today('Asia/Jakarta'))
            ->first();

        if (!$attendance || !$attendance->check_in) {
            return back()->with('error', 'Anda belum melakukan check-in hari ini.');
        }

        if ($attendance->check_out) {
            return back()->with('error', 'Anda sudah melakukan check-out hari ini.');
        }

        $now = Carbon::now('Asia/Jakarta');
        $checkoutStart = Carbon::createFromTimeString($schedule['checkout_mulai'], 'Asia/Jakarta');
        $checkoutEnd   = Carbon::createFromTimeString($schedule['checkout_selesai'], 'Asia/Jakarta');

        if ($now->gt($checkoutEnd)) {
            return back()->with('error', "Absensi pulang sudah ditutup (maks {$checkoutEnd->format('H:i')} WIB).");
        }

        [$officeLat, $officeLng, $maxRadius] = $this->getOfficeConfig();
        [$latStr, $lngStr] = explode(',', $request->location);
        $distance = $this->calculateDistance($officeLat, $officeLng, (float)$latStr, (float)$lngStr);

        if ($distance > $maxRadius) {
            return back()->with('error', 'Gagal! Anda masih di luar area kantor (' . round($distance) . 'm).');
        }

        $pulangCepat = $now->lt($checkoutStart);

        $attendance->update([
            'check_out'  => $now->toTimeString(),
            'keterangan' => $pulangCepat ? 'Pulang Cepat' : $attendance->keterangan,
            'location'   => $request->location,
        ]);

        return back()->with(
            'success',
            $pulangCepat
                ? 'Absen pulang berhasil, status: PULANG CEPAT.'
                : 'Hati-hati di jalan! Absen pulang berhasil.'
        );
    }


    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371000;

        $latFrom = deg2rad($lat1);
        $lonFrom = deg2rad($lon1);
        $latTo = deg2rad($lat2);
        $lonTo = deg2rad($lon2);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $angle = 2 * asin(sqrt(
            pow(sin($latDelta / 2), 2) +
                cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)
        ));

        return $angle * $earthRadius;
    }
}
