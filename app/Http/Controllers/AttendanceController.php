<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\Attendance;
use App\Models\AboutSetting;
 use Barryvdh\DomPDF\Facade\Pdf;
 use App\Models\Logbook;

class AttendanceController extends Controller
{
    /**
     * Halaman utama presensi mahasiswa
     */
public function index()
{
    $user = Auth::user();
    $todayDate = Carbon::now()->toDateString();

    // 🔹 Ambil hari (senin, selasa, ...)
    $today = strtolower(
        Carbon::now()->locale('id')->isoFormat('dddd')
    );

    // 🔹 Ambil kategori instansi user
    $kategori = $user->kategori_instansi ?? 'UMUM';

    // 🔹 Ambil setting instansi SESUAI kategori
    $about = AboutSetting::where('kategori_instansi', $kategori)->first();

    if (!$about) {
        return view('attendance.index', [
            'error' => 'Data instansi belum diatur oleh admin.',
            'attendance' => null,
            'about' => null,
            'schedule' => [],
            'checkinExpired' => false,
            'checkoutExpired' => false,
            'checkoutAvailable' => false,
        ]);
    }

    // 🔹 Ambil attendance hari ini (boleh null)
    $attendance = Attendance::where('user_id', $user->id)
        ->whereDate('tanggal', $todayDate)
        ->first();

    // 🔹 BENTUK JADWAL HARI INI (INI KUNCI UTAMA)
    $schedule = [
        'checkin_mulai'    => $about->{$today . '_mulai'} ?? null,
        'checkin_selesai'  => $about->{$today . '_selesai'} ?? null,
        'checkout_mulai'   => $about->{$today . '_checkout_mulai'} ?? null,
        'checkout_selesai' => $about->{$today . '_checkout_selesai'} ?? null,
    ];

    // 🔹 JIKA JADWAL BELUM DIISI ADMIN
    if (
        !$schedule['checkin_mulai'] ||
        !$schedule['checkin_selesai'] ||
        !$schedule['checkout_mulai'] ||
        !$schedule['checkout_selesai']
    ) {
        $schedule = [
            'error' => 'Jam check-in/check-out hari ini belum diatur oleh admin.'
        ];
    }

    // 🔹 LOGIC STATUS WAKTU
    $now = Carbon::now();

    $checkinExpired = false;
    $checkoutExpired = false;
    $checkoutAvailable = false;

    if (!isset($schedule['error'])) {
        $checkinExpired = $now->gt(
            Carbon::createFromFormat('H:i:s', $schedule['checkin_selesai'])
        );

        $checkoutAvailable = $now->gte(
            Carbon::createFromFormat('H:i:s', $schedule['checkout_mulai'])
        );

        $checkoutExpired = $now->gt(
            Carbon::createFromFormat('H:i:s', $schedule['checkout_selesai'])
        );
    }

    return view('attendance.index', compact(
        'about',
        'attendance',
        'schedule',
        'checkinExpired',
        'checkoutExpired',
        'checkoutAvailable'
    ));
}


        // ✅ RIWAYAT PRESENSI
        public function history()
        {
            $attendances = Attendance::where('user_id', Auth::id())
                ->orderBy('tanggal', 'desc')
                ->get();

            return view('attendance.history', compact('attendances'));
        }

        // ✅ DOWNLOAD PDF PRESENSI (TABEL SIMPLE)
        public function presensiPdf(Request $request)
        {
            $student = Auth::user();
            $about = AboutSetting::first();

            $month = (int) ($request->month ?? now()->month);
            $year  = (int) ($request->year ?? now()->year);

            $start = Carbon::create($year, $month, 1, 0, 0, 0, 'Asia/Jakarta');
            $end   = $start->copy()->endOfMonth();

            $bulanLabel = $start->translatedFormat('F'); // e.g., "Januari"

            $attendances = Attendance::where('user_id', $student->id)
                ->whereBetween('tanggal', [$start->toDateString(), $end->toDateString()])
                ->orderBy('tanggal', 'asc')
                ->get()
                ->keyBy(fn ($x) => Carbon::parse($x->tanggal)->format('Y-m-d'));

            $rows = [];
            for ($d = 1; $d <= $end->day; $d++) {
                $date = Carbon::create($year, $month, $d, 0, 0, 0, 'Asia/Jakarta');
                $key  = $date->format('Y-m-d');

                $a = $attendances->get($key);

                $isHoliday = $date->isWeekend();

                $status = $a?->status ?? '-';
                $statusLabel = $status;

                if (strtolower($status) === 'izin') $statusLabel = 'Ijin';
                if (strtolower($status) === 'alpha') $statusLabel = 'Tanpa Keterangan';

                // --- LOGIKA LOKASI ---
                $lokasiText = '-';
                $lokasiUrl  = null;

                if ($a) {
                    if (!empty($a->location_name)) {
                        $lokasiText = $a->location_name;
                    } elseif (in_array($a->status, ['Hadir', 'Telat'])) {
                        $lokasiText = 'KANTOR BAPPERIDA';
                    } else {
                        $lokasiText = strtoupper($a->status);
                    }

                    if (!empty($a->location) && (str_contains($a->location, 'http') || str_contains($a->location, 'maps'))) {
                        $lokasiUrl = $a->location;
                    }
                }

                $rows[] = [
                    'tanggal_label' => $date->translatedFormat('d M Y'),
                    'checkin_label' => $a?->check_in ? Carbon::parse($a->check_in)->format('H:i') : '-',
                    'checkout_label'=> $a?->check_out ? Carbon::parse($a->check_out)->format('H:i') : '-',
                    'status'        => $statusLabel,
                    'location_text' => $lokasiText,
                    'location_url'  => $lokasiUrl,
                    'location'      => $lokasiText,
                    'isHoliday'     => $isHoliday,
                ];
            }

            // ✅ FIX: SPLIT ROWS INTO LEFT & RIGHT
            // Split agar muat dalam 2 kolom tabel berdampingan (Page 1 Full)
            // Biasanya dipotong di hari ke-16
            $left  = array_slice($rows, 0, 16);
            $right = array_slice($rows, 16);

            $F4 = [0, 0, 595.28, 935.43];

            return Pdf::loadView('admin.students.presensi_pdf', [
                'student'    => $student,
                'about'      => $about,
                'month'      => $month,
                'year'       => $year,
                'bulanLabel' => $bulanLabel,

                // ✅ Kirim variable ini ke view
                'left'       => $left,
                'right'      => $right,
                // 'rows' tidak perlu dikirim jika view pakai $left/$right
            ])->setPaper($F4, 'portrait')
            ->stream("PRESENSI_{$student->name}_{$month}_{$year}.pdf"); // Gunakan stream() utk preview atau download()
        }

        //--logika cek status mahasiswa
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

        private function getTodayAbsensiSchedule(): array
        {
                $about = AboutSetting::where(
        'kategori_instansi',
        auth()->user()->kategori_instansi
    )->first();

            if (!$about) {
                return ['error' => 'Jam absensi belum diatur oleh admin.'];
            }

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
        
    /**
     * Proses CHECK-IN
     */
  public function checkIn(Request $request)
{
    $request->validate([
        'latitude' => 'required',
        'longitude' => 'required',
    ]);

    $user = Auth::user();
    $now = Carbon::now();

  $kategori = $user->kategori_instansi ?? 'UMUM';

$about = AboutSetting::where('kategori_instansi', $kategori)->first();




if (
    !$about ||
    !$about->office_latitude ||
    !$about->office_longitude
) {
    return back()->with('error', 'Lokasi instansi belum diatur oleh admin.');
}


    // ✅ AMBIL KOORDINAT YANG BENAR
    $instansiLat = (float) $about->office_latitude;
    $instansiLng = (float) $about->office_longitude;
    $radius      = (int) $about->radius_meter;

    // 🛑 VALIDASI KOORDINAT INSTANSI
    if (!$instansiLat || !$instansiLng) {
        return back()->with('error', 'Koordinat instansi belum diisi oleh admin.');
    }

    // USER COORD
    $userLat = (float) $request->latitude;
    $userLng = (float) $request->longitude;

    // 🔍 DEBUG TERAKHIR (hapus setelah yakin)
    /*
    dd(
        'INSTANSI', $instansiLat, $instansiLng,
        'USER', $userLat, $userLng,
        'RADIUS', $radius
    );
    */

    // 📏 HITUNG JARAK
    $distance = $this->distance(
        $userLat,
        $userLng,
        $instansiLat,
        $instansiLng
    );

    if ($distance > $radius) {
        return back()->with('error', 'Anda berada di luar radius instansi.');
    }

    // ❌ CEK DUPLIKAT
    $attendance = Attendance::where('user_id', $user->id)
        ->whereDate('tanggal', $now->toDateString())
        ->first();

    if ($attendance) {
        return back()->with('error', 'Anda sudah melakukan check-in hari ini.');
    }

    Attendance::create([
        'user_id'   => $user->id,
        'tanggal'   => $now->toDateString(),
        'check_in'  => $now->toTimeString(),
        'status'    => 'Hadir',
        'latitude'  => $userLat,
        'longitude' => $userLng,
        'location_name' => $about->office_name,
    ]);

    return back()->with('success', 'Check-in berhasil.');
}


    /**
     * Proses CHECK-OUT
     */
    public function checkOut(Request $request)
    {
        $request->validate([
            'latitude' => 'required',
            'longitude' => 'required',
        ]);

        $user = Auth::user();
        $now = Carbon::now();

        $attendance = Attendance::where('user_id', $user->id)
            ->whereDate('tanggal', $now->toDateString())
            ->first();

        if (!$attendance) {
            return back()->with('error', 'Anda belum check-in hari ini.');
        }

        if ($attendance->check_out) {
            return back()->with('error', 'Anda sudah check-out.');
        }

        $attendance->update([
            'check_out' => $now->toTimeString()
        ]);

        return back()->with('success', 'Check-out berhasil.');
    }

    /**
     * Hitung jarak (meter) antara 2 koordinat
     */
    private function distance($lat1, $lon1, $lat2, $lon2)
    {
        $theta = $lon1 - $lon2;
        $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +
                cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
        $dist = acos($dist);
        $dist = rad2deg($dist);
        $meters = $dist * 60 * 1.1515 * 1609.344;

        return $meters;
    }
}


