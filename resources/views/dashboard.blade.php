<x-app-layout>
    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-blue-100">
                <div class="p-8 text-gray-900 flex items-center justify-between">
                    <div>
                        <h3 class="text-2xl font-bold text-blue-600">Selamat Datang, {{ Auth::user()->name }}!</h3>
                        <p class="text-gray-500 mt-1">Jangan lupa untuk melakukan presensi dan mengisi logbook hari ini.
                        </p>
                    </div>
                    <div class="hidden md:block">
                        <span class="text-sm font-medium text-gray-400">Role: <span
                                class="text-blue-500 uppercase">{{ Auth::user()->role }}</span></span>
                    </div>
                </div>
            </div>

            @if ($rejectedLogbooksCount > 0 || $rejectedLaporansCount > 0)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-red-100">
                    <div class="p-6 md:p-8">
                        <div class="flex flex-col gap-4 md:flex-row md:items-start md:justify-between">
                            <div>
                                <div
                                    class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-red-50 text-red-700 text-xs font-bold uppercase tracking-wide">
                                    Perlu Tindak Lanjut
                                </div>
                                <h3 class="mt-3 text-xl font-bold text-gray-900">Ada logbook atau laporan yang ditolak
                                    admin</h3>
                                <p class="text-sm text-gray-600 mt-2">
                                    Segera cek catatan penolakan lalu perbaiki dan ajukan kembali.
                                </p>
                            </div>

                            <div class="flex flex-wrap gap-3">
                                @if ($rejectedLogbooksCount > 0)
                                    <a href="{{ route('logbook.index') }}"
                                        class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-red-50 text-red-700 font-semibold hover:bg-red-100 transition">
                                        <span>{{ $rejectedLogbooksCount }} Logbook Ditolak</span>
                                    </a>
                                @endif

                                @if ($rejectedLaporansCount > 0)
                                    <a href="{{ route('laporan.index') }}"
                                        class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-amber-50 text-amber-700 font-semibold hover:bg-amber-100 transition">
                                        <span>{{ $rejectedLaporansCount }} Laporan Ditolak</span>
                                    </a>
                                @endif
                            </div>
                        </div>

                        @if ($recentRejectionAlerts->isNotEmpty())
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-6">
                                @foreach ($recentRejectionAlerts as $alert)
                                    <div class="rounded-2xl border border-gray-100 bg-gray-50 p-5">
                                        <div class="flex items-start justify-between gap-4">
                                            <div>
                                                <p
                                                    class="text-xs font-bold uppercase tracking-wide {{ $alert['type'] === 'logbook' ? 'text-red-600' : 'text-amber-600' }}">
                                                    {{ $alert['title'] }}
                                                </p>
                                                <h4 class="mt-2 text-base font-bold text-gray-900">
                                                    {{ $alert['headline'] }}
                                                </h4>
                                            </div>
                                            <span class="text-xs text-gray-400 whitespace-nowrap">
                                                {{ optional($alert['rejected_at'])->diffForHumans() }}
                                            </span>
                                        </div>

                                        <div
                                            class="mt-3 rounded-xl {{ $alert['type'] === 'logbook' ? 'bg-red-50 border-red-100' : 'bg-amber-50 border-amber-100' }} border p-4">
                                            <p class="text-xs font-bold uppercase tracking-wide text-gray-500">Catatan
                                                Admin</p>
                                            <p class="mt-2 text-sm leading-relaxed text-gray-700">
                                                {{ $alert['note'] ?: 'Belum ada catatan penolakan.' }}
                                            </p>
                                        </div>

                                        <div class="mt-4">
                                            <a href="{{ $alert['route'] }}"
                                                class="inline-flex items-center gap-2 text-sm font-semibold {{ $alert['type'] === 'logbook' ? 'text-red-700 hover:text-red-800' : 'text-amber-700 hover:text-amber-800' }}">
                                                {{ $alert['action'] }}
                                            </a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                <a href="{{ Auth::user()->status === 'aktif' ? route('attendance.index') : '#' }}"
                    class="bg-white p-6 rounded-2xl shadow-sm border {{ Auth::user()->status === 'aktif' ? 'border-gray-100 hover:border-blue-500' : 'border-gray-200 cursor-not-allowed' }} transition group {{ Auth::user()->status !== 'aktif' ? 'opacity-50' : '' }}">
                    <div class="flex items-center space-x-4">
                        <div
                            class="p-3 {{ Auth::user()->status === 'aktif' ? 'bg-blue-100 text-blue-600 group-hover:bg-blue-600 group-hover:text-white' : 'bg-gray-100 text-gray-400' }} rounded-xl transition">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <h4 class="font-bold text-gray-800">Presensi</h4>
                            <p class="text-xs text-gray-500">Catat kehadiran harianmu</p>
                        </div>
                    </div>
                </a>

                <a href="{{ Auth::user()->status === 'aktif' ? route('logbook.index') : '#' }}"
                    class="bg-white p-6 rounded-2xl shadow-sm border
                        {{ Auth::user()->status === 'aktif' ? 'border-gray-100 hover:border-indigo-500' : 'border-gray-200 cursor-not-allowed opacity-50' }}
                        transition group">
                    <div class="flex items-center justify-between gap-4">
                        <div class="flex items-center space-x-4">
                            <div
                                class="p-3 {{ Auth::user()->status === 'aktif' ? 'bg-indigo-100 text-indigo-600 group-hover:bg-indigo-600 group-hover:text-white' : 'bg-gray-100 text-gray-400' }} rounded-xl transition">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                    </path>
                                </svg>
                            </div>
                            <div>
                                <h4 class="font-bold text-gray-800">Logbook</h4>
                                <p class="text-xs text-gray-500">Laporkan kegiatan magang</p>
                            </div>
                        </div>

                        @if ($rejectedLogbooksCount > 0)
                            <span
                                class="inline-flex items-center justify-center min-w-[2rem] h-8 px-2 rounded-full bg-red-100 text-red-700 text-xs font-bold">
                                {{ $rejectedLogbooksCount }}
                            </span>
                        @endif
                    </div>
                </a>

                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                    <div class="flex items-center justify-between">
                        <div>
                            <h4 class="font-bold text-gray-800 italic text-sm">Status Akun</h4>
                            @php $status = Auth::user()->status; @endphp
                            <p class="{{ $status === 'aktif' ? 'text-green-500' : 'text-gray-500' }} font-bold mt-1">
                                {{ ucfirst($status) }}
                            </p>
                        </div>
                        <div
                            class="p-3 {{ $status === 'aktif' ? 'bg-green-100 text-green-600' : 'bg-gray-100 text-gray-400' }} rounded-xl">
                            @if ($status === 'aktif')
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            @else
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            @endif
                        </div>
                    </div>
                </div>

                @if (Auth::user()->status == 'aktif')
                    <a href="{{ route('laporan.index') }}"
                        class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 hover:border-green-500 transition group">
                        <div class="flex items-center justify-between gap-4">
                            <div class="flex items-center space-x-4">
                                <div
                                    class="p-3 bg-green-100 text-green-600 group-hover:bg-green-600 group-hover:text-white rounded-xl transition">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="font-bold text-gray-800">Laporan</h4>
                                    <p class="text-xs text-gray-500">Unggah & lihat laporan magang</p>
                                </div>
                            </div>

                            @if ($rejectedLaporansCount > 0)
                                <span
                                    class="inline-flex items-center justify-center min-w-[2rem] h-8 px-2 rounded-full bg-red-100 text-red-700 text-xs font-bold">
                                    {{ $rejectedLaporansCount }}
                                </span>
                            @endif
                        </div>
                    </a>
                @endif

                @php
                    $ass = Auth::user()->assessment;
                    $scanPath = $ass?->certificate_scan;
                    $scanUrl = $scanPath ? asset('storage/' . $scanPath) : null;
                    $scanOriginalName = $ass?->certificate_scan_original_name ?? null;
                    $scanDisplayName = $scanOriginalName ?: ($scanPath ? basename($scanPath) : null);
                @endphp

                @if ($scanUrl)
                    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 md:col-span-3">
                        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                            <div>
                                <h4 class="font-bold text-gray-800 text-lg">Sertifikat Magang</h4>
                                <p class="text-sm text-gray-500 mt-1">
                                    File:
                                    <a href="{{ $scanUrl }}" target="_blank"
                                        class="font-bold text-blue-600 hover:underline break-all">
                                        {{ $scanDisplayName }}
                                    </a>
                                </p>
                            </div>

                            <div class="flex flex-wrap gap-2">
                                <button type="button" id="btnPreviewScanMahasiswa" data-url="{{ $scanUrl }}"
                                    class="px-4 py-2 rounded-xl bg-gray-900 text-white font-bold text-sm hover:bg-black transition shadow">
                                    Preview
                                </button>

                                <a href="{{ $scanUrl }}" download
                                    class="px-4 py-2 rounded-xl bg-blue-600 text-white font-bold text-sm hover:bg-blue-700 transition shadow">
                                    Download
                                </a>
                            </div>
                        </div>

                        <div class="mt-8 w-full h-[560px] border border-gray-200 rounded-xl overflow-hidden">
                            <iframe id="preview-scan-iframe" class="w-full h-full" src=""
                                frameborder="0"></iframe>
                        </div>
                    </div>
                @else
                    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 md:col-span-3">
                        <h4 class="font-bold text-gray-800 text-lg">Scan Sertifikat Magang</h4>
                        <p class="text-sm text-gray-500 mt-2 italic">
                            Scan sertifikat belum tersedia. File akan muncul setelah admin mengupload scan sertifikat.
                        </p>
                    </div>
                @endif

            </div>
        </div>
    </div>

    <div id="rejectionNoticeModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="fixed inset-0 bg-black/50" data-rejection-backdrop></div>

        <div class="relative h-full flex items-center justify-center p-3 sm:p-4">
            <div
                class="w-full max-w-3xl max-h-[calc(100vh-2rem)] sm:max-h-[calc(100vh-3rem)] bg-white rounded-3xl shadow-2xl border border-red-100 overflow-hidden flex flex-col">
                <div class="p-5 sm:p-6 border-b border-red-100 relative bg-red-50 shrink-0">
                    <button id="closeRejectionNoticeModal" type="button"
                        class="absolute top-4 right-4 w-10 h-10 rounded-xl flex items-center justify-center text-red-500 hover:text-red-700 hover:bg-white/70 transition">
                        &times;
                    </button>
                    <div class="pr-10">
                        <p class="text-xs font-bold uppercase tracking-[0.2em] text-red-600">Pemberitahuan Admin</p>
                        <h2 class="text-2xl font-black text-gray-900 mt-2">Ada logbook atau laporan yang ditolak</h2>
                        <p class="text-sm text-gray-700 mt-2">
                            Periksa catatan admin di bawah ini, lalu lakukan perbaikan dari menu yang sesuai.
                        </p>
                    </div>
                </div>

                <div class="px-5 sm:px-6 py-4 bg-white border-b border-gray-100 shrink-0">
                    <div class="flex flex-wrap items-center gap-2 text-sm">
                        <span
                            class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-red-50 text-red-700 font-semibold">
                            {{ $rejectedLogbooksCount }} logbook ditolak
                        </span>
                        <span
                            class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-amber-50 text-amber-700 font-semibold">
                            {{ $rejectedLaporansCount }} laporan ditolak
                        </span>
                    </div>
                </div>

                <div class="flex-1 overflow-y-auto px-5 sm:px-6 py-5 space-y-4 bg-gray-50">
                    @foreach ($modalRejectionAlerts as $alert)
                        <div class="rounded-2xl border border-gray-100 bg-white p-4 sm:p-5 shadow-sm">
                            <div class="flex items-start justify-between gap-4">
                                <div>
                                    <p
                                        class="text-xs font-bold uppercase tracking-wide {{ $alert['type'] === 'logbook' ? 'text-red-600' : 'text-amber-600' }}">
                                        {{ $alert['title'] }}
                                    </p>
                                    <h3 class="mt-2 text-base sm:text-lg font-bold text-gray-900">
                                        {{ $alert['headline'] }}</h3>
                                </div>
                                <span class="text-xs text-gray-400 whitespace-nowrap">
                                    {{ optional($alert['rejected_at'])->diffForHumans() }}
                                </span>
                            </div>

                            <div
                                class="mt-4 rounded-xl {{ $alert['type'] === 'logbook' ? 'bg-red-50 border-red-100' : 'bg-amber-50 border-amber-100' }} border p-4">
                                <p class="text-xs font-bold uppercase tracking-wide text-gray-500">Catatan Admin</p>
                                <p class="mt-2 text-sm leading-relaxed text-gray-700">
                                    {{ $alert['note'] ?: 'Belum ada catatan penolakan.' }}
                                </p>
                            </div>

                            <div class="mt-4">
                                <a href="{{ $alert['route'] }}"
                                    class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-bold {{ $alert['type'] === 'logbook' ? 'bg-red-600 hover:bg-red-700' : 'bg-amber-600 hover:bg-amber-700' }} text-white transition">
                                    {{ $alert['action'] }}
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div
                    class="px-5 sm:px-6 py-4 bg-white border-t border-gray-100 shrink-0 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <p class="text-sm text-gray-500">
                        Tutup notifikasi ini setelah kamu selesai membaca catatan admin.
                    </p>
                    <button id="confirmRejectionNoticeModal" type="button"
                        class="inline-flex items-center justify-center px-4 py-2 rounded-xl bg-gray-900 text-white text-sm font-bold hover:bg-black transition">
                        Saya Mengerti
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const btn = document.getElementById('btnPreviewScanMahasiswa');
            const iframe = document.getElementById('preview-scan-iframe');

            if (btn && iframe) {
                btn.addEventListener('click', function() {
                    const url = this.dataset.url;
                    if (!url) return;

                    iframe.src = url;
                    iframe.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                });
            }

            const rejectionModal = document.getElementById('rejectionNoticeModal');
            const rejectionBackdrop = rejectionModal?.querySelector('[data-rejection-backdrop]');
            const rejectionClose = document.getElementById('closeRejectionNoticeModal');
            const rejectionConfirm = document.getElementById('confirmRejectionNoticeModal');
            const shouldShowRejectionModal = @json($shouldShowRejectionModal);

            function openRejectionModal() {
                rejectionModal?.classList.remove('hidden');
                document.body.classList.add('overflow-hidden');
            }

            function closeRejectionModal() {
                rejectionModal?.classList.add('hidden');
                document.body.classList.remove('overflow-hidden');
            }

            if (shouldShowRejectionModal) {
                openRejectionModal();
            }

            rejectionClose?.addEventListener('click', closeRejectionModal);
            rejectionConfirm?.addEventListener('click', closeRejectionModal);
            rejectionBackdrop?.addEventListener('click', closeRejectionModal);

            window.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && rejectionModal && !rejectionModal.classList.contains('hidden')) {
                    closeRejectionModal();
                }
            });
        });
    </script>
</x-app-layout>
