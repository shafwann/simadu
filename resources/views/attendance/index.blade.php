<x-app-layout>
    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-2xl border border-gray-100">

                {{-- Header Hari & Tanggal --}}
                <div class="bg-gradient-to-r from-blue-700 to-blue-500 p-8 text-white text-center">
                    <div class="inline-flex p-3 rounded-full bg-white/20 mb-4 shadow-inner">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-extrabold">{{ Carbon\Carbon::now()->isoFormat('dddd') }}</h3>
                    <p class="text-blue-100 opacity-90">{{ Carbon\Carbon::now()->isoFormat('D MMMM Y') }}</p>

                    {{-- Tampilkan Nama Kantor di Header --}}
                    <div class="mt-2 text-xs font-medium bg-blue-800/30 inline-block px-3 py-1 rounded-full">
                        📍 {{ $about->office_name ?? 'KANTOR BAPPERIDA' }}
                    </div>
                </div>

                <div class="p-8">
                    {{-- Flash Message --}}
                    @if(session('success'))
                    <div class="mb-6 p-4 bg-green-50 text-green-700 rounded-xl border border-green-100 animate-bounce text-sm font-medium text-center">
                        {{ session('success') }}
                    </div>
                    @endif
                    @if(session('error'))
                    <div class="mb-6 p-4 bg-red-50 text-red-700 rounded-xl border border-red-100 text-sm font-medium text-center">
                        {{ session('error') }}
                    </div>
                    @endif

                    <div class="space-y-6">
                        {{-- CEK USER AKTIF ATAU NONAKTIF --}}
                        @if(Auth::user()->status !== 'aktif')
                        <div class="mb-6 p-4 bg-red-50 text-red-700 rounded-xl border border-red-100 text-center flex items-center justify-center gap-2">
                            <svg class="w-6 h-6 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-12.728 12.728M5.636 5.636l12.728 12.728"></path>
                            </svg>
                            <span class="font-semibold">Akun Anda sudah nonaktif!</span> Tidak dapat melakukan presensi.
                        </div>
                        @endif

                        {{-- FORM PRESENSI --}}
                        @if(!$attendance)
                            <div class="text-center space-y-4">
                                <div class="bg-blue-50 p-4 rounded-xl border border-blue-100">
                                    <p class="text-blue-800 font-bold">Halo, {{ Auth::user()->name }}!</p>
                                    <p class="text-sm text-blue-600">Silakan absen masuk untuk memulai kegiatan magang.</p>
                                </div>

                                <div id="location-info" class="hidden mb-4 p-3 bg-yellow-50 text-yellow-800 text-xs rounded-lg border border-yellow-200">
                                    Sedang mendeteksi lokasi...
                                </div>

                                @if(Auth::user()->status === 'aktif')
                                    @if(isset($schedule['error']))
                                        <div class="p-4 bg-red-50 text-red-700 rounded-xl border text-sm text-center">
                                            {{ $schedule['error'] }}
                                        </div>

                                    @elseif($checkinExpired)
                                        <div class="p-4 bg-red-50 text-red-700 rounded-xl border text-sm text-center font-semibold">
                                            ⛔ Waktu absen masuk telah berakhir<br>
                                            <span class="text-xs">
                                                ({{ $schedule['checkin_mulai'] }} – {{ $schedule['checkin_selesai'] }} WIB)
                                            </span>
                                        </div>

                                        <button disabled class="w-full py-4 bg-gray-300 text-gray-600 font-bold rounded-2xl cursor-not-allowed">
                                            Absen Masuk Ditutup
                                        </button>

                                    @else
                                        <form id="attendance-form" action="{{ route('attendance.checkin') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="location" id="location-input">

                                            <button type="button" onclick="getLocation('checkin')" id="btn-checkin"
                                                class="w-full py-4 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-2xl shadow-lg transition transform hover:scale-[1.02] flex items-center justify-center gap-2">
                                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
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

                                    @if($attendance->status == 'Telat')
                                    <div class="inline-flex items-center gap-1 px-3 py-1 bg-red-100 text-red-700 rounded-full text-xs font-bold animate-pulse">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        TELAT
                                    </div>
                                    @else
                                    <div class="inline-flex items-center gap-1 px-3 py-1 bg-green-100 text-green-600 rounded-full mb-4 font-bold text-xs">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                        TEPAT WAKTU
                                    </div>
                                    @endif

                                    <p class="text-xs text-gray-400 mt-4">
                                        Absen pulang dibuka pukul <span class="font-bold text-gray-600">{{ $schedule['checkout_mulai'] }}</span>
                                    </p>
                                </div>

                                <div id="location-info" class="hidden mb-4 p-3 bg-yellow-50 text-yellow-800 text-xs rounded-lg border border-yellow-200">
                                    Sedang mendeteksi lokasi...
                                </div>

                                @if(Auth::user()->status === 'aktif')
                                    @if($checkoutExpired)
                                        <button disabled class="w-full py-4 bg-gray-300 text-gray-600 font-bold rounded-2xl cursor-not-allowed">
                                            Absen Pulang Ditutup
                                        </button>

                                    @elseif(!$checkoutAvailable)
                                        <div class="p-3 bg-yellow-50 text-yellow-700 rounded-xl border text-xs text-center">
                                            ⏳ Absen pulang belum dibuka<br>
                                            ({{ $schedule['checkout_mulai'] }} WIB)
                                        </div>

                                    @else
                                        <form id="attendance-form" action="{{ route('attendance.checkout') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="location" id="location-input">

                                            <button type="button" onclick="getLocation('checkout')" id="btn-checkout"
                                                class="w-full py-4 bg-orange-500 hover:bg-orange-600 text-white font-bold rounded-2xl shadow-lg transition transform hover:scale-[1.02] flex items-center justify-center gap-2">
                                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
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
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                </div>
                                <h3 class="text-lg font-bold text-gray-800">Presensi Hari Ini Selesai!</h3>
                                <p class="text-gray-500 text-sm mb-6">Terima kasih atas kerja kerasnya hari ini.</p>

                                <div class="grid grid-cols-2 gap-4">
                                    <div class="p-3 bg-white rounded-xl border border-gray-100 shadow-sm">
                                        <p class="text-xs text-gray-400 uppercase font-bold">Masuk</p>
                                        <p class="text-xl font-black text-green-600">{{ \Carbon\Carbon::parse($attendance->check_in)->format('H:i') }}</p>
                                    </div>
                                    <div class="p-3 bg-white rounded-xl border border-gray-100 shadow-sm">
                                        <p class="text-xs text-gray-400 uppercase font-bold">Pulang</p>
                                        <p class="text-xl font-black text-orange-500">{{ \Carbon\Carbon::parse($attendance->check_out)->format('H:i') }}</p>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <a href="{{ route('attendance.presensi_pdf') }}" class="flex items-center justify-center w-full px-4 py-3 border-2 border-gray-200 text-gray-600 hover:border-green-500 hover:text-green-600 font-bold rounded-2xl transition gap-2 text-sm">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            Download Riwayat Presensi
                        </a>

                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

{{-- ✅ SCRIPT GPS DINAMIS (MENGAMBIL DARI DATABASE) --}}
<script>
    // --- KONFIGURASI LOKASI KANTOR (DINAMIS DARI CONTROLLER) ---
    // Jika data database kosong, fallback ke default (Bapperida)
    const officeLat = {{ $about->office_latitude ?? -7.594917597849972 }};
    const officeLng = {{ $about->office_longitude ?? 110.94065517764916 }};
    const maxRadius = {{ $about->radius_meter ?? 500 }}; // Ambil radius dari DB

    function getLocation(type) {
        const btnId = (type === 'checkin') ? 'btn-checkin' : 'btn-checkout';
        const btn = document.getElementById(btnId);
        const locationInput = document.getElementById('location-input');
        const form = document.getElementById('attendance-form');
        const infoDiv = document.getElementById('location-info');

        if (navigator.geolocation) {
            btn.innerText = "Mendeteksi Lokasi...";
            btn.disabled = true;
            if (infoDiv) {
                infoDiv.classList.remove('hidden');
                infoDiv.innerText = "Sedang menghubungi satelit GPS...";
            }

            const options = {
                enableHighAccuracy: true,
                timeout: 10000, // 10 detik timeout
                maximumAge: 0
            };

            navigator.geolocation.getCurrentPosition(
                function(position) {
                    const userLat = position.coords.latitude;
                    const userLng = position.coords.longitude;
                    const distance = calculateDistance(officeLat, officeLng, userLat, userLng);

                    if (infoDiv) infoDiv.innerText = `Posisi ditemukan! Jarak: ${Math.round(distance)} meter.`;

                    if (distance <= maxRadius) {
                        if (infoDiv) infoDiv.className = "mb-4 p-3 bg-green-50 text-green-700 rounded-lg border border-green-200 text-xs";
                        locationInput.value = userLat + "," + userLng;
                        form.submit();
                    } else {
                        if (infoDiv) infoDiv.className = "mb-4 p-3 bg-red-50 text-red-700 rounded-lg border border-red-200 text-xs";
                        alert(`Gagal! Anda berjarak ${Math.round(distance)} meter dari kantor. (Max: ${maxRadius}m)`);
                        resetButton(btn, type);
                    }
                },
                function(error) {
                    let msg = "Gagal mengambil lokasi.";
                    switch (error.code) {
                        case error.PERMISSION_DENIED:
                            msg = "Izin lokasi ditolak. Mohon izinkan lokasi di browser.";
                            break;
                        case error.POSITION_UNAVAILABLE:
                            msg = "Sinyal GPS lemah atau tidak tersedia.";
                            break;
                        case error.TIMEOUT:
                            msg = "Waktu habis! Sinyal GPS tidak ditemukan.";
                            break;
                    }
                    alert(msg);
                    if (infoDiv) infoDiv.innerText = msg;
                    resetButton(btn, type);
                },
                options
            );
        } else {
            alert("Browser tidak mendukung lokasi.");
        }
    }

    function resetButton(btn, type) {
        btn.innerText = (type === 'checkin') ? "Absen Masuk" : "Absen Pulang";
        btn.disabled = false;
        // Kembalikan icon (opsional, karena innerText menghapus svg)
        // btn.innerHTML = `...svg... Absen...`; (Jika ingin mengembalikan icon persis)
    }

    // Rumus Haversine untuk hitung jarak
    function calculateDistance(lat1, lon1, lat2, lon2) {
        const R = 6371e3; // Radius bumi dalam meter
        const φ1 = lat1 * Math.PI / 180;
        const φ2 = lat2 * Math.PI / 180;
        const Δφ = (lat2 - lat1) * Math.PI / 180;
        const Δλ = (lon2 - lon1) * Math.PI / 180;

        const a = Math.sin(Δφ / 2) ** 2 +
                  Math.cos(φ1) * Math.cos(φ2) *
                  Math.sin(Δλ / 2) ** 2;
        const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));

        return R * c; // Hasil dalam meter
    }
</script>
