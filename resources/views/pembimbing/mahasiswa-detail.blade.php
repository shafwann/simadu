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

                {{-- Badge style --}}
                <style>
                    .badgex {
                        display: inline-flex;
                        align-items: center;
                        padding: 4px 10px;
                        border-radius: 999px;
                        font-weight: 800;
                        font-size: 12px;
                        line-height: 1;
                        border: 1px solid rgba(0, 0, 0, .06);
                        white-space: nowrap;
                    }

                    .b-h {
                        background: #dcfce7;
                        color: #166534;
                    }

                    /* Hadir */
                    .b-t {
                        background: #fef9c3;
                        color: #854d0e;
                    }

                    /* Terlambat */
                    .b-i {
                        background: #dbeafe;
                        color: #1e40af;
                    }

                    /* Ijin */
                    .b-s {
                        background: #fde68a;
                        color: #92400e;
                    }

                    /* Sakit */
                    .b-dl {
                        background: #e9d5ff;
                        color: #6b21a8;
                    }

                    /* Dinas Luar */
                    .b-ct {
                        background: #fbcfe8;
                        color: #9d174d;
                    }

                    /* Cuti */
                    .b-tk {
                        background: #e5e7eb;
                        color: #374151;
                    }

                    /* Tanpa Keterangan */
                    .b-pc {
                        background: #ffedd5;
                        color: #9a3412;
                    }

                    /* Pulang Cepat */
                    .b-tap {
                        background: #fee2e2;
                        color: #991b1b;
                    }

                    /* Tidak Absen Pulang */
                    .b-na {
                        background: #f3f4f6;
                        color: #6b7280;
                    }

                    /* Default/Unknown */
                </style>

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
                                @php
                                    // ============================
                                    // NORMALISASI STATUS (TAMPIL LENGKAP)
                                    // ============================
                                    $rawStatus = strtoupper(trim((string) ($p->status ?? '')));

                                    // Label utama (lengkap)
                                    if ($rawStatus === '' || $rawStatus === '-') {
                                        $mainLabel = '-';
                                        $mainClass = 'b-na';
                                    } elseif (in_array($rawStatus, ['ALPHA', 'TANPA KETERANGAN', 'TK'], true)) {
                                        $mainLabel = 'Tanpa Keterangan';
                                        $mainClass = 'b-tk';
                                    } elseif ($rawStatus === 'HADIR') {
                                        $mainLabel = 'Hadir';
                                        $mainClass = 'b-h';
                                    } elseif ($rawStatus === 'TELAT' || $rawStatus === 'TERLAMBAT') {
                                        $mainLabel = 'Terlambat';
                                        $mainClass = 'b-t';
                                    } elseif (in_array($rawStatus, ['IJIN', 'IZIN', 'I'], true)) {
                                        $mainLabel = 'Ijin';
                                        $mainClass = 'b-i';
                                    } elseif (in_array($rawStatus, ['SAKIT', 'S'], true)) {
                                        $mainLabel = 'Sakit';
                                        $mainClass = 'b-s';
                                    } elseif (in_array($rawStatus, ['DINAS LUAR', 'DL'], true)) {
                                        $mainLabel = 'Dinas Luar';
                                        $mainClass = 'b-dl';
                                    } elseif (in_array($rawStatus, ['CUTI', 'CT'], true)) {
                                        $mainLabel = 'Cuti';
                                        $mainClass = 'b-ct';
                                    } else {
                                        // fallback kalau ada status lain
                                        $mainLabel = ucwords(strtolower($p->status ?? '-'));
                                        $mainClass = 'b-na';
                                    }

                                    // ============================
                                    // TAG TAMBAHAN (LENGKAP)
                                    // ============================
                                    // Pulang Cepat (ambil dari kolom keterangan jika kamu pakai itu)
                                    $pc = strtoupper(trim((string) ($p->keterangan ?? ''))) === 'PULANG CEPAT';

                                    // Tidak Absen Pulang (punya check_in tapi check_out kosong)
                                    $tap = !empty($p->check_in) && empty($p->check_out);
                                @endphp

                                <tr>
                                    <td class="px-3 py-2 border">{{ $p->tanggal }}</td>

                                    {{-- STATUS LENGKAP --}}
                                    <td class="px-3 py-2 border">
                                        <div class="flex flex-wrap gap-2">
                                            <span class="badgex {{ $mainClass }}">{{ $mainLabel }}</span>

                                            @if ($pc)
                                                <span class="badgex b-pc">Pulang Cepat</span>
                                            @endif

                                            @if ($tap)
                                                <span class="badgex b-tap">Tidak Absen Pulang</span>
                                            @endif
                                        </div>
                                    </td>


                                    <td class="px-3 py-2 border">
                                        {{ $p->check_in ? \Carbon\Carbon::parse($p->check_in)->format('H:i') : '-' }}
                                    </td>

                                    <td class="px-3 py-2 border">
                                        {{ $p->check_out ? \Carbon\Carbon::parse($p->check_out)->format('H:i') : '-' }}
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
                <a href="{{ route('pembimbing.dashboard') }}" class="text-blue-600 hover:underline text-sm">
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

                <form class="p-5 space-y-4" method="GET"
                    action="{{ route('pembimbing.mahasiswa.presensi.pdf', $mahasiswa->id) }}">

                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Bulan</label>
                        <select name="month" class="w-full rounded-xl border-gray-200 bg-gray-50 font-bold">
                            @for ($m = 1; $m <= 12; $m++)
                                <option value="{{ $m }}" {{ $m == now()->month ? 'selected' : '' }}>
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

                <form class="p-5 space-y-4" method="GET"
                    action="{{ route('pembimbing.mahasiswa.logbook.pdf', $mahasiswa->id) }}">

                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Bulan</label>
                        <select name="month" class="w-full rounded-xl border-gray-200 bg-gray-50 font-bold">
                            @for ($m = 1; $m <= 12; $m++)
                                <option value="{{ $m }}" {{ $m == now()->month ? 'selected' : '' }}>
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
