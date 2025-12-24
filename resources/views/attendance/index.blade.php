<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight text-blue-600">Presensi Kehadiran</h2>
    </x-slot>

    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-2xl border border-gray-100">

                <div class="bg-gradient-to-r from-blue-700 to-blue-500 p-8 text-white text-center">
                    <div class="inline-flex p-3 rounded-full bg-white/20 mb-4 shadow-inner">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <h3 class="text-2xl font-extrabold">{{ Carbon\Carbon::now()->isoFormat('dddd') }}</h3>
                    <p class="text-blue-100 opacity-90">{{ Carbon\Carbon::now()->isoFormat('D MMMM Y') }}</p>
                </div>

                <div class="p-8">
                    @if(session('success'))
                        <div class="mb-6 p-4 bg-green-50 text-green-700 rounded-xl border border-green-100 animate-bounce text-sm font-medium">{{ session('success') }}</div>
                    @endif
                    @if(session('error'))
                        <div class="mb-6 p-4 bg-red-50 text-red-700 rounded-xl border border-red-100 text-sm font-medium">{{ session('error') }}</div>
                    @endif

                    <div class="space-y-6">
                        @if(!$attendance)
                            <div class="text-center space-y-4">
                                <p class="text-gray-600">Silakan klik tombol di bawah untuk absen masuk.</p>
                                <form id="attendance-form" action="{{ route('attendance.checkin') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="location" id="location-input">
                                    <button type="button" onclick="getLocation()" id="btn-checkin"
                                        class="w-full py-4 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-2xl shadow-lg transition transform hover:scale-[1.02]">
                                        Absen Sekarang
                                    </button>
                                </form>
                            </div>
                        @else
                            <div class="bg-blue-50 rounded-2xl p-6 border border-blue-100 text-center">
                                <p class="text-blue-800 font-bold text-sm uppercase mb-2">Status Kehadiran</p>
                                <div class="text-4xl font-black text-blue-700 mb-2">{{ Carbon\Carbon::parse($attendance->check_in)->format('H:i') }}</div>
                                <span class="bg-green-500 text-white text-[10px] px-2 py-1 rounded-md font-bold uppercase">Sudah Absen</span>

                                @if($attendance->location)
                                    <div class="mt-4 pt-4 border-t border-blue-100">
                                        <a href="https://www.google.com/maps/search/?api=1&query={{ $attendance->location }}" target="_blank" class="text-xs text-blue-500 hover:underline italic">
                                            📍 Lokasi GPS tercatat (Klik untuk lihat Map)
                                        </a>
                                    </div>
                                @endif
                            </div>
                        @endif

                        <a href="{{ route('attendance.export') }}" class="flex items-center justify-center w-full px-4 py-3 border-2 border-green-500 text-green-600 hover:bg-green-500 hover:text-white font-bold rounded-2xl transition">
                            Download Riwayat (.csv)
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

<script>
function getLocation() {
    const btn = document.getElementById('btn-checkin');
    const locationInput = document.getElementById('location-input');
    const form = document.getElementById('attendance-form');

    if (navigator.geolocation) {
        btn.innerText = "Mengambil Lokasi...";
        btn.disabled = true;

        navigator.geolocation.getCurrentPosition(function(position) {
            locationInput.value = position.coords.latitude + "," + position.coords.longitude;
            form.submit();
        }, function(error) {
            alert("Gagal mengambil lokasi. Mohon izinkan akses GPS di browser Anda.");
            btn.innerText = "Absen Sekarang";
            btn.disabled = false;
        }, { enableHighAccuracy: true });
    } else {
        alert("Browser tidak mendukung lokasi.");
    }
}
</script>
