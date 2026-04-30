<x-app-layout>
    <div class="py-12 max-w-3xl mx-auto px-4">
        <div class="w-full max-w-xl mx-auto">
            <div class="bg-white overflow-hidden shadow-xl rounded-2xl border border-gray-100">

                {{-- Header Hari & Tanggal --}}
                <div class="bg-gradient-to-r from-blue-700 to-blue-500 p-8 text-white text-center">
                    <div class="inline-flex p-3 rounded-full bg-white/20 mb-4 shadow-inner">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-extrabold">{{ Carbon\Carbon::now()->isoFormat('dddd') }}</h3>
                    <p class="text-blue-100 opacity-90">{{ Carbon\Carbon::now()->isoFormat('D MMMM Y') }}</p>
                </div>

                <div class="p-8">
                    {{-- Flash Message --}}
                    @if (session('success'))
                        <div
                            class="mb-6 p-4 bg-green-50 text-green-700 rounded-xl border border-green-100 text-sm font-medium text-center">
                            {{ session('success') }}
                        </div>
                    @endif
                    @if (session('error'))
                        <div
                            class="mb-6 p-4 bg-red-50 text-red-700 rounded-xl border border-red-100 text-sm font-medium text-center">
                            {{ session('error') }}
                        </div>
                    @endif

                    @php
                        $checkinTooEarly = false;
                        $checkoutExpired = false;
                        $scheduleError = null;

                        if (isset($schedule) && is_array($schedule) && isset($schedule['error'])) {
                            $scheduleError = $schedule['error'];
                        }

                        if (!$scheduleError && isset($schedule) && is_array($schedule)) {
                            try {
                                $now = \Carbon\Carbon::now('Asia/Jakarta');

                                if (!empty($schedule['checkin_mulai'])) {
                                    $checkinStart = \Carbon\Carbon::createFromTimeString(
                                        $schedule['checkin_mulai'],
                                        'Asia/Jakarta',
                                    );
                                    $checkinTooEarly = $now->lt($checkinStart);
                                }

                                if (!empty($schedule['checkout_selesai'])) {
                                    $checkoutEnd = \Carbon\Carbon::createFromTimeString(
                                        $schedule['checkout_selesai'],
                                        'Asia/Jakarta',
                                    );
                                    $checkoutExpired = $now->gt($checkoutEnd);
                                }
                            } catch (\Throwable $e) {
                                // biarkan default false
                            }
                        }
                    @endphp

                    <div class="space-y-6">

                        {{-- CEK USER AKTIF ATAU NONAKTIF --}}
                        @if (Auth::user()->status !== 'aktif')
                            <div
                                class="p-4 bg-red-50 text-red-700 rounded-xl border border-red-100 text-center flex items-center justify-center gap-2">
                                <svg class="w-6 h-6 inline-block" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M18.364 5.636l-12.728 12.728M5.636 5.636l12.728 12.728"></path>
                                </svg>
                                <span class="font-semibold">Akun Anda sudah nonaktif!</span> Tidak dapat melakukan
                                presensi.
                            </div>
                        @endif

                        {{-- FORM PRESENSI --}}
                        @if (!$attendance)
                            <div class="text-center space-y-4">
                                <div class="bg-blue-50 p-4 rounded-xl border border-blue-100">
                                    <p class="text-blue-800 font-bold">Halo, {{ Auth::user()->name }}!</p>
                                    <p class="text-sm text-blue-600">Silakan absen masuk untuk memulai kegiatan magang.
                                    </p>
                                </div>

                                <div id="location-info"
                                    class="hidden mb-4 p-3 bg-yellow-50 text-yellow-800 text-xs rounded-lg border border-yellow-200">
                                    Sedang mendeteksi lokasi...
                                </div>

                                @if ($checkinTooEarly)
                                    <div
                                        class="p-4 bg-yellow-50 border border-yellow-200 rounded-xl text-yellow-800 text-sm flex gap-3 text-left">
                                        <svg class="w-6 h-6 mt-0.5" viewBox="0 0 24 24" fill="currentColor">
                                            <path fill-rule="evenodd"
                                                d="M12 2C6.477 2 2 6.477 2 12s4.477 10 10 10 10-4.477 10-10S17.523 2 12 2zm1 5a1 1 0 10-2 0v5c0 .265.105.52.293.707l3 3a1 1 0 001.414-1.414L13 11.586V7z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        <div>
                                            <p class="font-bold">Absen Masuk Belum Tersedia</p>
                                            <p class="text-xs opacity-80">Presensi akan dibuka sesuai jadwal.</p>
                                        </div>
                                    </div>
                                @endif

                                @if (Auth::user()->status === 'aktif')
                                    @if (!$checkinTooEarly)
                                        <form id="attendance-form" action="{{ route('attendance.checkin') }}"
                                            method="POST">
                                            @csrf
                                            <input type="hidden" name="location" id="location-input">

                                            <button type="button" onclick="getLocation('checkin')" id="btn-checkin"
                                                class="w-full py-4 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-2xl shadow-lg transition transform hover:scale-[1.02] flex items-center justify-center gap-2">
                                                <svg class="w-6 h-6" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1">
                                                    </path>
                                                </svg>
                                                Absen Masuk
                                            </button>
                                        </form>
                                    @endif
                                @endif
                            </div>
                        @elseif($attendance->check_in && !$attendance->check_out)
                            <div class="text-center space-y-4">
                                <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
                                    <p class="text-gray-500 font-bold text-sm uppercase mb-1">Status Kehadiran</p>

                                    <div class="text-4xl font-black text-gray-800 mb-2">
                                        {{ \Carbon\Carbon::parse($attendance->check_in)->format('H:i') }}
                                    </div>

                                    @if ($attendance->status == 'Telat')
                                        <div
                                            class="inline-flex items-center gap-1 px-3 py-1 bg-red-100 text-red-700 rounded-full text-xs font-bold animate-pulse">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            TERLAMBAT
                                        </div>
                                    @else
                                        <div
                                            class="inline-flex items-center gap-1 px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs font-bold">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M5 13l4 4L19 7"></path>
                                            </svg>
                                            HADIR
                                        </div>
                                    @endif
                                </div>

                                <div id="location-info"
                                    class="hidden mb-4 p-3 bg-yellow-50 text-yellow-800 text-xs rounded-lg border border-yellow-200">
                                    Sedang mendeteksi lokasi...
                                </div>

                                {{-- Jika checkout sudah expired -> tombol DIHILANGKAN, tampilkan info --}}
                                @if ($checkoutExpired)
                                    <div
                                        class="p-4 bg-red-50 border border-red-200 rounded-xl text-red-700 text-sm flex gap-3 text-left">
                                        <svg class="w-6 h-6 mt-0.5" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M18.364 5.636l-12.728 12.728M5.636 5.636l12.728 12.728"></path>
                                        </svg>
                                        <div>
                                            <p class="font-bold">Absen Pulang Sudah Ditutup</p>
                                            <p class="text-xs opacity-80">Waktu absen pulang hari ini sudah berakhir.
                                            </p>
                                        </div>
                                    </div>
                                @endif

                                @if (Auth::user()->status === 'aktif')
                                    {{-- tombol hanya tampil kalau masih boleh checkout --}}
                                    @if (!$checkoutExpired)
                                        <form id="attendance-form" action="{{ route('attendance.checkout') }}"
                                            method="POST">
                                            @csrf
                                            <input type="hidden" name="location" id="location-input">

                                            <button type="button" onclick="getLocation('checkout')" id="btn-checkout"
                                                class="w-full py-4 bg-orange-500 hover:bg-orange-600 text-white font-bold rounded-2xl shadow-lg transition transform hover:scale-[1.02] flex items-center justify-center gap-2">
                                                <svg class="w-6 h-6" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1">
                                                    </path>
                                                </svg>
                                                Absen Pulang
                                            </button>
                                        </form>
                                    @endif
                                @endif
                            </div>
                        @else
                            <div class="bg-gray-50 rounded-2xl p-6 border border-gray-200 text-center">
                                <div class="inline-flex p-2 bg-green-100 text-green-600 rounded-full mb-4">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 13l4 4L19 7"></path>
                                    </svg>
                                </div>
                                <h3 class="text-lg font-bold text-gray-800">Presensi Hari Ini Selesai!</h3>
                                <p class="text-gray-500 text-sm mb-6">Terima kasih atas kerja kerasnya hari ini.</p>

                                <div class="grid grid-cols-2 gap-4">
                                    <div class="p-3 bg-white rounded-xl border border-gray-100 shadow-sm">
                                        <p class="text-xs text-gray-400 uppercase font-bold">Masuk</p>
                                        <p class="text-xl font-black text-green-600">
                                            {{ \Carbon\Carbon::parse($attendance->check_in)->format('H:i') }}</p>
                                    </div>
                                    <div class="p-3 bg-white rounded-xl border border-gray-100 shadow-sm">
                                        <p class="text-xs text-gray-400 uppercase font-bold">Pulang</p>
                                        <p class="text-xl font-black text-orange-500">
                                            {{ \Carbon\Carbon::parse($attendance->check_out)->format('H:i') }}</p>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <a href="{{ route('attendance.history') }}"
                            class="flex items-center justify-center w-full px-4 py-3 border-2 border-gray-200 text-gray-600 hover:border-green-500 hover:text-green-600 font-bold rounded-2xl transition gap-2 text-sm">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-3.05-6.95M21 3v6h-6">
                                </path>
                            </svg>
                            Riwayat Presensi
                        </a>

                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

<script>
    // --- KONFIGURASI LOKASI KANTOR ---
    const officeConfig = @json($officeConfig ?? [
        'latitude' => -7.594917597849972,
        'longitude' => 110.94065517764916,
        'radius' => 1500,
    ]);
    const officeLat = Number(officeConfig.latitude);
    const officeLng = Number(officeConfig.longitude);
    const maxRadius = Number(officeConfig.radius); // meter

    // Timeout yang lebih realistis (GPS indoor sering lama)
    const TIMEOUT_HIGH = 60000; // 60s
    const TIMEOUT_LOW = 60000; // 60s
    const WATCH_MAX_MS = 20000; // 20s nunggu fix terakhir

    function getLocation(type) {
        const btnId = (type === 'checkin') ? 'btn-checkin' : 'btn-checkout';
        const btn = document.getElementById(btnId);
        const locationInput = document.getElementById('location-input');
        const form = document.getElementById('attendance-form');
        const infoDiv = document.getElementById('location-info');

        if (!navigator.geolocation) {
            showError("Browser tidak mendukung lokasi.");
            return;
        }

        // kalau tombol disable karena jadwal, jangan dipaksa
        if (btn && btn.disabled) return;

        setLoading(btn, infoDiv, "Sedang menghubungi layanan lokasi...");

        const optionsHigh = {
            enableHighAccuracy: true,
            timeout: TIMEOUT_HIGH,
            maximumAge: 0
        };
        const optionsLow = {
            enableHighAccuracy: false,
            timeout: TIMEOUT_LOW,
            maximumAge: 300000
        }; // boleh pakai cache 5 menit

        const onSuccess = (position) => {
            const userLat = position.coords.latitude;
            const userLng = position.coords.longitude;

            const accuracy = position.coords.accuracy; // meter
            const distance = calculateDistance(officeLat, officeLng, userLat, userLng);

            if (infoDiv) {
                infoDiv.className =
                    "mb-4 p-3 bg-green-50 text-green-700 rounded-lg border border-green-200 text-xs";
                infoDiv.innerText =
                    `Posisi ditemukan! Akurasi ±${Math.round(accuracy)}m, jarak: ${Math.round(distance)}m.`;
            }

            if (distance <= maxRadius) {
                locationInput.value = userLat + "," + userLng;
                form.submit();
            } else {
                if (infoDiv) {
                    infoDiv.className = "mb-4 p-3 bg-red-50 text-red-700 rounded-lg border border-red-200 text-xs";
                    infoDiv.innerText = `Di luar radius. Jarak: ${Math.round(distance)}m (Max: ${maxRadius}m).`;
                }
                alert(`Gagal! Anda berjarak ${Math.round(distance)} meter dari kantor. (Max: ${maxRadius}m)`);
                resetButton(btn, type);
            }
        };

        const onFinalError = (err) => {
            let msg = "Gagal mengambil lokasi.";
            if (err && typeof err.code !== "undefined") {
                switch (err.code) {
                    case err.PERMISSION_DENIED:
                        msg = "Izin lokasi ditolak. Silakan Allow lokasi untuk situs ini.";
                        break;
                    case err.POSITION_UNAVAILABLE:
                        msg =
                            "Lokasi tidak tersedia. Biasanya karena Location Services OS mati / browser tidak diizinkan / sinyal lokasi tidak ada.";
                        break;
                    case err.TIMEOUT:
                        msg = "Waktu habis! Sinyal lokasi belum didapat. Coba di tempat terbuka / nyalakan Wi-Fi.";
                        break;
                }
            }
            if (infoDiv) {
                infoDiv.className =
                    "mb-4 p-3 bg-yellow-50 text-yellow-800 rounded-lg border border-yellow-200 text-xs";
                infoDiv.innerText = msg;
            }
            alert(msg);
            resetButton(btn, type);
        };

        navigator.geolocation.getCurrentPosition(
            onSuccess,
            (err1) => {
                if (infoDiv) infoDiv.innerText = "GPS sulit didapat, mencoba mode jaringan...";
                navigator.geolocation.getCurrentPosition(
                    onSuccess,
                    (err2) => {
                        if (infoDiv) infoDiv.innerText =
                            "Masih sulit, mencoba menunggu sinyal lokasi (watch)...";

                        let got = false;
                        const watchId = navigator.geolocation.watchPosition(
                            (pos) => {
                                got = true;
                                navigator.geolocation.clearWatch(watchId);
                                onSuccess(pos);
                            },
                            (err3) => {
                                // jangan langsung error; tunggu sampai timeout watch habis
                            }, {
                                enableHighAccuracy: false,
                                maximumAge: 0
                            }
                        );

                        setTimeout(() => {
                            if (!got) {
                                navigator.geolocation.clearWatch(watchId);
                                // pakai error terakhir yang paling “jelas”
                                onFinalError(err2 || err1);
                            }
                        }, WATCH_MAX_MS);
                    },
                    optionsLow
                );
            },
            optionsHigh
        );
    }

    function setLoading(btn, infoDiv, msg) {
        if (btn) {
            btn.dataset.originalText = btn.innerText;
            btn.innerText = "Mendeteksi Lokasi...";
            btn.disabled = true;
        }
        if (infoDiv) {
            infoDiv.classList.remove('hidden');
            infoDiv.className = "mb-4 p-3 bg-yellow-50 text-yellow-800 rounded-lg border border-yellow-200 text-xs";
            infoDiv.innerText = msg || "Sedang mendeteksi lokasi...";
        }
    }

    function resetButton(btn, type) {
        if (!btn) return;
        btn.innerText = btn.dataset.originalText || ((type === 'checkin') ? "Absen Masuk" : "Absen Pulang");
        btn.disabled = false;
    }

    function showError(msg) {
        alert(msg);
    }

    function calculateDistance(lat1, lon1, lat2, lon2) {
        const R = 6371e3;
        const φ1 = lat1 * Math.PI / 180;
        const φ2 = lat2 * Math.PI / 180;
        const Δφ = (lat2 - lat1) * Math.PI / 180;
        const Δλ = (lon2 - lon1) * Math.PI / 180;
        const a = Math.sin(Δφ / 2) ** 2 + Math.cos(φ1) * Math.cos(φ2) * Math.sin(Δλ / 2) ** 2;
        const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
        return R * c;
    }
</script>
