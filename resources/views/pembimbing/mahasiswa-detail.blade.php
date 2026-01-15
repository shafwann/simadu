<x-app-layout>
    <div class="py-8 bg-gray-50 min-h-screen">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- HEADER MAHASISWA --}}
            <div class="bg-white p-6 rounded-xl shadow">
                <h2 class="text-xl font-bold text-gray-800">
                    {{ $mahasiswa->name }}
                </h2>
                <p class="text-sm text-gray-600 mt-1">
                    NIM: {{ $mahasiswa->nim }} |
                    Prodi: {{ $mahasiswa->prodi }} |
                    Instansi: {{ $mahasiswa->instansi }}
                </p>
            </div>

            {{-- PRESENSI --}}
            <div class="bg-white p-6 rounded-xl shadow">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-semibold text-gray-800">
                        Riwayat Presensi
                    </h3>

                    <button type="button" id="btnDownloadPresensiDosen"
                        class="px-4 py-2 rounded-xl bg-purple-600 text-white font-bold text-sm hover:bg-purple-700 transition">
                        Download Presensi (PDF)
                    </button>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm border border-gray-200">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-3 py-2 border">Tanggal</th>
                                <th class="px-3 py-2 border">Status</th>
                                <th class="px-3 py-2 border">Check In</th>
                                <th class="px-3 py-2 border">Check Out</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($presensi as $p)
                                <tr>
                                    <td class="px-3 py-2 border">{{ $p->tanggal }}</td>
                                    <td class="px-3 py-2 border">{{ $p->status }}</td>
                                    <td class="px-3 py-2 border">{{ $p->check_in }}</td>
                                    <td class="px-3 py-2 border">
                                        {{ $p->check_out ?? '-' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-gray-500">
                                        Belum ada data presensi
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $presensi->links() }}
                </div>
            </div>

            {{-- LOGBOOK --}}
            <div class="bg-white p-6 rounded-xl shadow">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-semibold text-gray-800">
                        Logbook Kegiatan
                    </h3>

                    <button type="button" id="btnDownloadLogbookDosen"
                        class="px-4 py-2 rounded-xl bg-purple-600 text-white font-bold text-sm hover:bg-purple-700 transition">
                        Download Logbook (PDF)
                    </button>
                </div>

                <div class="space-y-3">
                    @forelse ($logbooks as $log)
                        <div class="border rounded-lg p-4">
                            <p class="text-sm text-gray-500">
                                {{ $log->tanggal }}
                            </p>
                            <p class="text-gray-800 mt-1">
                                {{ $log->kegiatan }}
                            </p>
                        </div>
                    @empty
                        <p class="text-gray-500 text-sm">
                            Belum ada logbook
                        </p>
                    @endforelse
                </div>

                <div class="mt-4">
                    {{ $logbooks->links() }}
                </div>
            </div>

            {{-- BACK --}}
            <div>
                <a href="{{ route('pembimbing.dashboard') }}"
                   class="text-blue-600 hover:underline text-sm">
                    ← Kembali ke Dashboard
                </a>
            </div>

        </div>
    </div>

    {{-- ===========================
        MODAL DOWNLOAD PRESENSI
    ============================ --}}
    <div id="downloadPresensiDosenModal" class="fixed inset-0 z-50 hidden">
        <div class="absolute inset-0 bg-black/50" data-backdrop></div>

        <div class="relative min-h-full flex items-center justify-center p-4">
            <div class="w-full max-w-md bg-white rounded-3xl shadow-2xl border border-gray-100 overflow-hidden">
                <div class="p-5 border-b border-gray-100 relative">
                    <button id="closePresensiDosenModal" type="button"
                        class="absolute top-4 right-4 w-10 h-10 rounded-xl flex items-center justify-center
                               text-gray-400 hover:text-gray-700 hover:bg-gray-100 transition">
                        <span class="text-2xl leading-none">&times;</span>
                    </button>
                    <h2 class="text-xl font-black text-gray-900 text-center">Download Presensi (PDF)</h2>
                    <p class="text-sm text-gray-500 mt-1">Pilih bulan & tahun yang ingin dicetak.</p>
                </div>

                <form class="p-5 space-y-4"
                    method="GET"
                    action="{{ route('pembimbing.mahasiswa.presensi.pdf', $mahasiswa->id) }}">

                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Bulan</label>
                        <select name="month" class="w-full rounded-xl border-gray-200 bg-gray-50 font-bold">
                            @for($m=1; $m<=12; $m++)
                                <option value="{{ $m }}" {{ $m==now()->month ? 'selected' : '' }}>
                                    {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                                </option>
                            @endfor
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Tahun</label>
                        <input type="number" name="year" value="{{ now()->year }}"
                            class="w-full rounded-xl border-gray-200 bg-gray-50 font-bold">
                    </div>

                    <button type="submit"
                        class="w-full py-3 rounded-2xl bg-purple-600 hover:bg-purple-700 text-white font-black transition">
                        Download PDF
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- ===========================
        MODAL DOWNLOAD LOGBOOK
    ============================ --}}
    <div id="downloadLogbookDosenModal" class="fixed inset-0 z-50 hidden">
        <div class="absolute inset-0 bg-black/50" data-backdrop></div>

        <div class="relative min-h-full flex items-center justify-center p-4">
            <div class="w-full max-w-md bg-white rounded-3xl shadow-2xl border border-gray-100 overflow-hidden">
                <div class="p-5 border-b border-gray-100 relative">
                    <button id="closeLogbookDosenModal" type="button"
                        class="absolute top-4 right-4 w-10 h-10 rounded-xl flex items-center justify-center
                               text-gray-400 hover:text-gray-700 hover:bg-gray-100 transition">
                        <span class="text-2xl leading-none">&times;</span>
                    </button>
                    <h2 class="text-xl font-black text-gray-900 text-center">Download Logbook (PDF)</h2>
                    <p class="text-sm text-gray-500 mt-1">Pilih bulan & tahun yang ingin dicetak.</p>
                </div>

                <form class="p-5 space-y-4"
                    method="GET"
                    action="{{ route('pembimbing.mahasiswa.logbook.pdf', $mahasiswa->id) }}">

                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Bulan</label>
                        <select name="month" class="w-full rounded-xl border-gray-200 bg-gray-50 font-bold">
                            @for($m=1; $m<=12; $m++)
                                <option value="{{ $m }}" {{ $m==now()->month ? 'selected' : '' }}>
                                    {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                                </option>
                            @endfor
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Tahun</label>
                        <input type="number" name="year" value="{{ now()->year }}"
                            class="w-full rounded-xl border-gray-200 bg-gray-50 font-bold">
                    </div>

                    <button type="submit"
                        class="w-full py-3 rounded-2xl bg-purple-600 hover:bg-purple-700 text-white font-black transition">
                        Download PDF
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- ===========================
        SCRIPT MODAL
    ============================ --}}
    <script>
    document.addEventListener('DOMContentLoaded', () => {
        // Presensi modal
        const presModal = document.getElementById('downloadPresensiDosenModal');
        const presBtn = document.getElementById('btnDownloadPresensiDosen');
        const presClose = document.getElementById('closePresensiDosenModal');
        const presBackdrop = presModal?.querySelector('[data-backdrop]');

        function openPres() {
            if (!presModal) return;
            presModal.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
        }
        function closePres() {
            if (!presModal) return;
            presModal.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        }
        presBtn?.addEventListener('click', openPres);
        presClose?.addEventListener('click', closePres);
        presBackdrop?.addEventListener('click', closePres);

        // Logbook modal
        const logModal = document.getElementById('downloadLogbookDosenModal');
        const logBtn = document.getElementById('btnDownloadLogbookDosen');
        const logClose = document.getElementById('closeLogbookDosenModal');
        const logBackdrop = logModal?.querySelector('[data-backdrop]');

        function openLog() {
            if (!logModal) return;
            logModal.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
        }
        function closeLog() {
            if (!logModal) return;
            logModal.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        }
        logBtn?.addEventListener('click', openLog);
        logClose?.addEventListener('click', closeLog);
        logBackdrop?.addEventListener('click', closeLog);

        // ESC close
        window.addEventListener('keydown', (e) => {
            if (e.key !== 'Escape') return;

            if (presModal && !presModal.classList.contains('hidden')) closePres();
            if (logModal && !logModal.classList.contains('hidden')) closeLog();
        });
    });
    </script>

</x-app-layout>
