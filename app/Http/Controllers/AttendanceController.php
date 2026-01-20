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
        public function index()
    {
        $attendance = Attendance::where('user_id', Auth::id())
            ->whereDate('tanggal', Carbon::today())
            ->orderByDesc('id')
            ->first();

        $schedule = $this->getTodayAbsensiSchedule();

        // ✅ TAMBAHAN: Ambil Settingan Lokasi Kantor
        $about = AboutSetting::first();

        $checkinExpired = false;
        $checkoutAvailable = false;
        $checkoutExpired = false;

        if (!isset($schedule['error'])) {
            $now = Carbon::now('Asia/Jakarta');

            $checkinStart = Carbon::createFromTimeString($schedule['checkin_mulai']);
            $checkinEnd   = Carbon::createFromTimeString($schedule['checkin_selesai']);

            $checkoutStart = Carbon::createFromTimeString($schedule['checkout_mulai']);
            $checkoutEnd   = Carbon::createFromTimeString($schedule['checkout_selesai']);

            $checkinExpired     = $now->gt($checkinEnd);
            $checkoutAvailable  = $now->between($checkoutStart, $checkoutEnd);
            $checkoutExpired    = $now->gt($checkoutEnd);
        }

        return view('attendance.index', compact(
            'attendance',
            'schedule',
            'about', // ✅ Kirim variabel $about ke view
            'checkinExpired',
            'checkoutAvailable',
            'checkoutExpired'
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

        public function checkIn(Request $request)
            {
                $user = Auth::user();
                if ($user->status !== 'aktif') {
                    return back()->with('error', 'Akun Anda tidak aktif, tidak bisa melakukan presensi.');
                }

                $msg = $this->ensureWithinCheckInWindow();
                if ($msg) return back()->with('error', $msg);

                // Validasi input location (koordinat)
                $request->validate(['location' => 'required']);

                // --- LOGIKA JARAK & SETTING KANTOR ---
                $officeLat = -7.594917597849972;
                $officeLng = 110.94065517764916;
                $maxRadius = 20; // Default 20 meter
                $officeName = 'KANTOR BAPPERIDA'; // Default nama kantor

                $about = AboutSetting::first();
                if ($about) {
                    if ($about->office_latitude && $about->office_longitude) {
                        $officeLat = $about->office_latitude;
                        $officeLng = $about->office_longitude;
                    }
                    $maxRadius = $about->radius_meter ?? $maxRadius;

                    // Ambil nama kantor dari database jika ada
                    if (!empty($about->office_name)) {
                        $officeName = $about->office_name;
                    }
                }

                $userLoc = explode(',', $request->location);
                $distance = $this->calculateDistance($officeLat, $officeLng, $userLoc[0], $userLoc[1]);

                // --- PENENTUAN NAMA LOKASI & STATUS ---
                $statusInput = $request->input('status', 'Hadir'); // Default Hadir
                $locationName = 'LUAR KANTOR'; // Default

                if (in_array($statusInput, ['Hadir', 'Telat'])) {
                    // Kalau Hadir/Telat, WAJIB di dalam radius
                    if ($distance > $maxRadius) {
                        return back()->with('error', 'Gagal! Jarak Anda ' . round($distance) . 'm dari kantor. Harap mendekat ke kantor.');
                    }
                    // ✅ GUNAKAN NAMA KANTOR DARI DATABASE (ATAU DEFAULT)
                    $locationName = $officeName;
                } else {
                    // Kalau Ijin/Sakit/DL, nama lokasi sesuai status
                    if ($statusInput == 'Dinas Luar') $locationName = 'DINAS LUAR';
                    elseif ($statusInput == 'Sakit') $locationName = 'RUMAH / RS';
                    elseif ($statusInput == 'Ijin') $locationName = 'TEMPAT KEPERLUAN';
                    elseif ($statusInput == 'Cuti') $locationName = 'CUTI';
                }

                Attendance::create([
                    'user_id' => Auth::id(),
                    'tanggal' => now()->toDateString(),
                    'check_in' => now()->toTimeString(),
                    'status' => $statusInput,
                    'location' => $request->location, // Koordinat Link
                    'location_name' => $locationName, // ✅ SIMPAN NAMA LOKASI SESUAI SETTING
                ]);

                return back()->with('success', 'Berhasil melakukan presensi masuk.');
            }

        public function checkOut(Request $request)
        {
            $user = Auth::user();
            if ($user->status !== 'aktif') {
                return back()->with('error', 'Akun Anda tidak aktif, tidak bisa melakukan presensi.');
            }

            $request->validate(['location' => 'required']);

            $msg = $this->ensureWithinCheckOutWindow();
            if ($msg) return back()->with('error', $msg);

            $officeLat = -7.594917597849972;
            $officeLng = 110.94065517764916;
            $maxRadius = 99999;

            $about = AboutSetting::first();
            if ($about && $about->office_latitude && $about->office_longitude) {
                $officeLat = $about->office_latitude;
                $officeLng = $about->office_longitude;
                $maxRadius = $about->radius_meter ?? $maxRadius;
            }

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
