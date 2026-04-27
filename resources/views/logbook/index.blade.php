<x-app-layout>
    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            {{-- Alert jika user nonaktif --}}
            @if (Auth::user()->status !== 'aktif')
                <div
                    class="mb-6 p-4 bg-red-50 text-red-700 rounded-xl border border-red-100 text-center flex items-center justify-center gap-2">
                    <svg class="w-6 h-6 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M18.364 5.636l-12.728 12.728M5.636 5.636l12.728 12.728"></path>
                    </svg>
                    <span class="font-semibold">Akun Anda sudah nonaktif!</span> Anda tidak dapat mengisi logbook.
                </div>
            @endif

            {{-- Form logbook --}}
            @if (Auth::user()->status === 'aktif')
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-gray-100">
                    <div class="p-8">
                        <div class="flex items-center mb-6">
                            <div class="p-3 bg-indigo-100 rounded-lg mr-4">
                                <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                    </path>
                                </svg>
                            </div>
                            <h3 class="text-xl font-bold text-gray-800">Isi Kegiatan Hari Ini</h3>
                        </div>

                        <form action="{{ route('logbook.store') }}" method="POST">
                            @csrf
                            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                                <div class="md:col-span-1">
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Tanggal</label>
                                    <input type="date" name="tanggal" value="{{ old('tanggal', date('Y-m-d')) }}"
                                        class="w-full rounded-xl border-gray-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition">
                                    <x-input-error :messages="$errors->get('tanggal')" class="mt-2" />
                                </div>
                                <div class="md:col-span-3">
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Deskripsi
                                        Kegiatan</label>
                                    <textarea name="kegiatan" rows="2" placeholder="Jelaskan apa yang kamu kerjakan..."
                                        class="w-full rounded-xl border-gray-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition">{{ old('kegiatan') }}</textarea>
                                    <x-input-error :messages="$errors->get('kegiatan')" class="mt-2" />
                                </div>
                            </div>
                            <div class="mt-6 flex justify-end">
                                <button type="submit"
                                    class="px-8 py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl shadow-lg transition transform hover:scale-105">
                                    Simpan Kegiatan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            @endif

            {{-- Riwayat logbook --}}
            <div class="bg-white shadow-sm sm:rounded-2xl border border-gray-100 overflow-hidden">
                {{-- Header --}}
                <div
                    class="p-8 border-b border-gray-50 flex flex-col md:flex-row md:justify-between md:items-center gap-4">
                    <div>
                        <h3 class="text-xl font-bold text-gray-800">Riwayat Logbook</h3>
                        <p class="text-sm text-gray-500 mt-1">Daftar kegiatan yang sudah kamu input.</p>
                    </div>

                    {{-- Tombol Download PDF --}}
                    <button id="btnDownloadLogbook"
                        class="w-full md:w-auto px-4 py-2 bg-blue-600 text-white font-bold rounded-xl shadow hover:bg-blue-700 transition">
                        Download Logbook (PDF)
                    </button>
                </div>

                {{-- Tabel logbook --}}
                <div class="p-4 md:p-8">
                    @php
                        $tanggalDirection = $sort === 'tanggal' && $direction === 'asc' ? 'desc' : 'asc';
                        $statusDirection = $sort === 'status' && $direction === 'asc' ? 'desc' : 'asc';
                        $tanggalSortUrl = route('logbook.index', array_merge(request()->except('page'), ['sort' => 'tanggal', 'direction' => $tanggalDirection]));
                        $statusSortUrl = route('logbook.index', array_merge(request()->except('page'), ['sort' => 'status', 'direction' => $statusDirection]));
                    @endphp

                    <div class="overflow-x-auto rounded-2xl border border-gray-100">
                        <table class="w-full text-left border-collapse">
                            <thead class="sticky top-0 bg-gray-50 z-10">
                                <tr class="bg-gray-50/50">
                                    <th
                                        class="px-6 md:px-8 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider border-b">
                                        <a href="{{ $tanggalSortUrl }}"
                                            class="inline-flex items-center gap-2 hover:text-gray-700 transition">
                                            <span>Tanggal</span>
                                            @if ($sort === 'tanggal')
                                                <span class="text-[10px] text-indigo-600">{{ strtoupper($direction) }}</span>
                                            @endif
                                        </a>
                                    </th>
                                    <th
                                        class="px-6 md:px-8 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider border-b">
                                        Aktivitas</th>
                                    <th
                                        class="px-6 md:px-8 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider border-b">
                                        <a href="{{ $statusSortUrl }}"
                                            class="inline-flex items-center gap-2 hover:text-gray-700 transition">
                                            <span>Status</span>
                                            @if ($sort === 'status')
                                                <span class="text-[10px] text-indigo-600">{{ strtoupper($direction) }}</span>
                                            @endif
                                        </a>
                                    </th>
                                    <th
                                        class="px-6 md:px-8 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider border-b">
                                        Aksi</th>
                                </tr>
                            </thead>

                            <tbody class="divide-y divide-gray-100">
                                @forelse($logbooks as $log)
                                    <tr class="hover:bg-gray-50/80 transition">
                                        {{-- Tanggal --}}
                                        <td
                                            class="px-6 md:px-8 py-5 text-sm font-semibold text-gray-700 whitespace-nowrap">
                                            {{ \Carbon\Carbon::parse($log->tanggal)->isoFormat('D MMMM Y') }}
                                        </td>

                                        {{-- Aktivitas --}}
                                        <td class="px-6 md:px-8 py-5 text-sm text-gray-600">
                                            <div class="truncate max-w-xs md:max-w-md" title="{{ $log->kegiatan }}">
                                                {{ $log->kegiatan }}
                                            </div>
                                        </td>


                                        {{-- Status (tanpa alasan ditolak di tabel) --}}
                                        <td class="px-6 md:px-8 py-5">
                                            @if ($log->is_rejected)
                                                <span
                                                    class="px-3 py-1 inline-flex text-xs font-bold rounded-full bg-red-100 text-red-700">
                                                    Rejected
                                                </span>
                                            @elseif($log->is_validated)
                                                <span
                                                    class="px-3 py-1 inline-flex text-xs font-bold rounded-full bg-green-100 text-green-700">
                                                    Validated
                                                </span>
                                            @else
                                                <span
                                                    class="px-3 py-1 inline-flex text-xs font-bold rounded-full bg-amber-100 text-amber-700">
                                                    Pending
                                                </span>
                                            @endif
                                        </td>

                                        {{-- Aksi --}}
                                        <td class="px-6 md:px-8 py-5">
                                            <div class="flex items-center gap-2">
                                                @if ($log->is_rejected)
                                                    {{-- Ditolak: Ajukan Kembali + Hapus --}}
                                                    <button type="button" title="Ajukan Kembali"
                                                        class="w-8 h-8 rounded-lg bg-amber-100 text-amber-600 hover:bg-amber-200 transition flex items-center justify-center"
                                                        data-open-edit-logbook data-id="{{ $log->id }}"
                                                        data-tanggal="{{ $log->tanggal }}"
                                                        data-kegiatan="{{ e($log->kegiatan) }}"
                                                        data-rejected-note="{{ e($log->catatan_pembimbing) }}"
                                                        data-is-rejected="true">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15">
                                                            </path>
                                                        </svg>
                                                    </button>
                                                    <form method="POST"
                                                        action="{{ route('logbook.destroy', $log->id) }}"
                                                        style="display: inline;"
                                                        onsubmit="return confirm('Hapus logbook ini?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" title="Hapus"
                                                            class="w-8 h-8 rounded-lg bg-red-100 text-red-600 hover:bg-red-200 transition flex items-center justify-center">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                                viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                                </path>
                                                            </svg>
                                                        </button>
                                                    </form>
                                                @elseif($log->is_validated)
                                                    {{-- Diterima: Edit + Hapus --}}
                                                    <button type="button" title="Edit"
                                                        class="w-8 h-8 rounded-lg bg-indigo-100 text-indigo-600 hover:bg-indigo-200 transition flex items-center justify-center"
                                                        data-open-edit-logbook data-id="{{ $log->id }}"
                                                        data-tanggal="{{ $log->tanggal }}"
                                                        data-kegiatan="{{ e($log->kegiatan) }}"
                                                        data-rejected-note="{{ e($log->catatan_pembimbing) }}"
                                                        data-is-rejected="false" data-is-validated="true">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                                            </path>
                                                        </svg>
                                                    </button>
                                                    <form method="POST"
                                                        action="{{ route('logbook.destroy', $log->id) }}"
                                                        style="display: inline;"
                                                        onsubmit="return confirm('Hapus logbook ini?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" title="Hapus"
                                                            class="w-8 h-8 rounded-lg bg-red-100 text-red-600 hover:bg-red-200 transition flex items-center justify-center">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                                viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                                </path>
                                                            </svg>
                                                        </button>
                                                    </form>
                                                @else
                                                    {{-- Pending: Edit + Hapus --}}
                                                    <button type="button" title="Edit"
                                                        class="w-8 h-8 rounded-lg bg-indigo-100 text-indigo-600 hover:bg-indigo-200 transition flex items-center justify-center"
                                                        data-open-edit-logbook data-id="{{ $log->id }}"
                                                        data-tanggal="{{ $log->tanggal }}"
                                                        data-kegiatan="{{ e($log->kegiatan) }}"
                                                        data-rejected-note="{{ e($log->catatan_pembimbing) }}"
                                                        data-is-rejected="false" data-is-validated="false">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                                            </path>
                                                        </svg>
                                                    </button>
                                                    <form method="POST"
                                                        action="{{ route('logbook.destroy', $log->id) }}"
                                                        style="display: inline;"
                                                        onsubmit="return confirm('Hapus logbook ini?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" title="Hapus"
                                                            class="w-8 h-8 rounded-lg bg-red-100 text-red-600 hover:bg-red-200 transition flex items-center justify-center">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                                viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                                </path>
                                                            </svg>
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-8 py-10 text-center text-gray-400 italic">
                                            Belum ada kegiatan tercatat.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if ($logbooks->hasPages())
                        <div class="mt-6 flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                            <p class="text-sm text-gray-500">
                                @php
                                    $sortLabel = $sort === 'status'
                                        ? 'status ' . strtoupper($direction)
                                        : 'tanggal ' . strtoupper($direction);
                                @endphp
                                Menampilkan {{ $logbooks->firstItem() }}-{{ $logbooks->lastItem() }} dari
                                {{ $logbooks->total() }} logbook, urutan {{ $sortLabel }}.
                            </p>
                            <div>
                                {{ $logbooks->links() }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>

    {{-- Modal Pending Checkout --}}
    <div id="pendingCheckoutModal" class="fixed inset-0 z-50 hidden">
        <div class="absolute inset-0 bg-black/50" data-pending-backdrop></div>

        <div class="relative min-h-full flex items-center justify-center p-4">
            <div class="w-full max-w-md bg-white rounded-3xl shadow-2xl border border-black-100 overflow-hidden">
                <div class="p-5 border-b border-gray-100 relative bg-gray-50">
                    <button id="closePendingCheckoutModal" type="button"
                        class="absolute top-4 right-4 w-10 h-10 rounded-xl flex items-center justify-center text-gray-700 hover:text-gray-900 hover:bg-gray-100 transition">
                        &times;
                    </button>
                    <h2 class="text-xl font-black text-gray-900 text-center">Isi Logbook Dulu</h2>
                    <p class="text-sm text-gray-800 mt-2 text-center">
                        Sebelum absen pulang, silakan isi logbook harian hari ini terlebih dahulu.
                    </p>
                </div>

                <div class="p-5">
                    <button id="confirmPendingCheckoutModal" type="button"
                        class="w-full py-3 rounded-2xl bg-amber-600 hover:bg-amber-700 text-white font-bold transition">
                        Siap, Saya Isi Logbook
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Download PDF --}}
    <div id="downloadLogbookModal" class="fixed inset-0 z-50 hidden">
        <div class="absolute inset-0 bg-black/50" data-backdrop></div>

        <div class="relative min-h-full flex items-center justify-center p-4">
            <div class="w-full max-w-md bg-white rounded-3xl shadow-2xl border border-gray-100 overflow-hidden">
                <div class="p-5 border-b border-gray-100 relative">
                    <button id="closeLogbookModal" type="button"
                        class="absolute top-4 right-4 w-10 h-10 rounded-xl flex items-center justify-center
                           text-gray-400 hover:text-gray-700 hover:bg-gray-100 transition">
                        &times;
                    </button>
                    <h2 class="text-xl font-black text-gray-900 text-center">Download Logbook (PDF)</h2>
                    <p class="text-sm text-gray-500 mt-1">Pilih bulan & tahun yang ingin dicetak.</p>
                </div>

                <form class="p-5 space-y-4" method="GET" action="{{ route('logbook.export.pdf') }}">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Bulan</label>
                        <select name="bulan" class="w-full rounded-xl border-gray-200 bg-gray-50 font-bold">
                            @for ($m = 1; $m <= 12; $m++)
                                <option value="{{ $m }}" {{ $m == now()->month ? 'selected' : '' }}>
                                    {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                                </option>
                            @endfor
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Tahun</label>
                        <select name="tahun" class="w-full rounded-xl border-gray-200 bg-gray-50 font-bold">
                            @foreach ($tahunList as $tahun)
                                <option value="{{ $tahun }}" {{ $tahun == now()->year ? 'selected' : '' }}>
                                    {{ $tahun }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <button type="submit"
                        class="w-full py-3 rounded-2xl bg-purple-600 hover:bg-purple-700 text-white font-black transition">
                        Download PDF
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal Edit Logbook (Popup) --}}
    <div id="editLogbookModal" class="fixed inset-0 z-50 hidden">
        <div class="absolute inset-0 bg-black/50" data-edit-backdrop></div>

        <div class="relative min-h-full flex items-center justify-center p-4">
            <div
                class="w-full max-w-md md:max-w-lg bg-white rounded-3xl shadow-2xl border border-gray-100 overflow-hidden">
                <div class="p-5 border-b border-gray-100 relative">
                    <button id="closeEditLogbookModal" type="button"
                        class="absolute top-4 right-4 w-10 h-10 rounded-xl flex items-center justify-center
                           text-gray-400 hover:text-gray-700 hover:bg-gray-100 transition">
                        &times;
                    </button>

                    <h2 class="text-xl font-black text-gray-900 text-center" id="editModalTitle">Edit Logbook</h2>
                    <p class="text-sm text-gray-500 mt-1 text-center" id="editModalSubtitle">Perbarui data logbook.
                    </p>
                </div>

                <form id="editLogbookForm" class="p-5 space-y-4" method="POST" action="">
                    @csrf
                    @method('PUT')

                    {{-- Alasan ditolak hanya di popup (tabel tidak menampilkan) --}}
                    <div id="rejectedNoteBox" class="hidden space-y-2">
                        <label class="block text-sm font-semibold text-red-700">Alasan Ditolak</label>
                        <textarea id="rejectedNoteText" rows="3" readonly
                            class="w-full rounded-xl border border-red-100 bg-red-50 text-red-700 shadow-sm focus:border-red-300 focus:ring-red-300"
                            placeholder="Alasan penolakan dari admin akan tampil di sini."></textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Tanggal</label>
                        <input id="editTanggal" type="date" name="tanggal" value=""
                            class="w-full rounded-xl border-gray-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Deskripsi Kegiatan</label>
                        <textarea id="editKegiatan" name="kegiatan" rows="3"
                            class="w-full rounded-xl border-gray-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition"></textarea>
                    </div>

                    <div class="flex flex-col-reverse md:flex-row justify-end gap-3 pt-2">
                        <button type="button" id="cancelEditLogbook"
                            class="px-5 py-2.5 text-sm rounded-xl bg-gray-200 text-gray-700 font-bold hover:bg-gray-300 transition w-full md:w-auto">
                            Batal
                        </button>
                        <button type="submit" id="editLogbookSubmitBtn"
                            class="px-5 py-2.5 text-sm rounded-xl bg-blue-600 text-white font-bold hover:bg-blue-700 transition w-full md:w-auto shadow-sm">
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Script modal download + edit --}}
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // ===== Modal Pending Checkout =====
            const pendingModal = document.getElementById('pendingCheckoutModal');
            const pendingBackdrop = pendingModal?.querySelector('[data-pending-backdrop]');
            const pendingClose = document.getElementById('closePendingCheckoutModal');
            const pendingConfirm = document.getElementById('confirmPendingCheckoutModal');
            const shouldShowPendingModal = @json(session('pending_checkout') ? true : false);

            function openPendingModal() {
                pendingModal?.classList.remove('hidden');
                document.body.classList.add('overflow-hidden');
            }

            function closePendingModal() {
                pendingModal?.classList.add('hidden');
                document.body.classList.remove('overflow-hidden');
            }

            if (shouldShowPendingModal) {
                openPendingModal();
            }

            pendingClose?.addEventListener('click', closePendingModal);
            pendingConfirm?.addEventListener('click', closePendingModal);
            pendingBackdrop?.addEventListener('click', closePendingModal);

            // ===== Modal Download PDF (punya kamu, tidak diubah logikanya) =====
            const modal = document.getElementById('downloadLogbookModal');
            const btn = document.getElementById('btnDownloadLogbook');
            const close = document.getElementById('closeLogbookModal');
            const backdrop = modal?.querySelector('[data-backdrop]');

            function openM() {
                modal.classList.remove('hidden');
                document.body.classList.add('overflow-hidden');
            }

            function closeM() {
                modal.classList.add('hidden');
                document.body.classList.remove('overflow-hidden');
            }

            btn?.addEventListener('click', openM);
            close?.addEventListener('click', closeM);
            backdrop?.addEventListener('click', closeM);

            window.addEventListener('keydown', (e) => {
                if (e.key === 'Escape' && pendingModal && !pendingModal.classList.contains('hidden')) {
                    closePendingModal();
                    return;
                }

                if (e.key === 'Escape' && modal && !modal.classList.contains('hidden')) closeM();
            });

            // ===== Modal Edit Logbook (popup) =====
            const editModal = document.getElementById('editLogbookModal');
            const editBackdrop = editModal?.querySelector('[data-edit-backdrop]');
            const editClose = document.getElementById('closeEditLogbookModal');
            const editCancel = document.getElementById('cancelEditLogbook');

            const editForm = document.getElementById('editLogbookForm');
            const editTanggal = document.getElementById('editTanggal');
            const editKegiatan = document.getElementById('editKegiatan');

            const rejectedNoteBox = document.getElementById('rejectedNoteBox');
            const rejectedNoteText = document.getElementById('rejectedNoteText');

            function openEditModal() {
                editModal.classList.remove('hidden');
                document.body.classList.add('overflow-hidden');
            }

            function closeEditModal() {
                editModal.classList.add('hidden');
                document.body.classList.remove('overflow-hidden');
            }

            document.querySelectorAll('[data-open-edit-logbook]').forEach((button) => {
                button.addEventListener('click', () => {
                    const id = button.dataset.id;
                    const tanggal = button.dataset.tanggal || '';
                    const kegiatan = button.dataset.kegiatan || '';
                    const rejectedNote = button.dataset.rejectedNote || '';
                    const isRejected = button.dataset.isRejected === 'true';
                    const isValidated = button.dataset.isValidated === 'true';

                    // set action update (mengikuti logika route PUT /logbook/{id})
                    editForm.action = `{{ url('/logbook') }}/${id}`;

                    editTanggal.value = tanggal;
                    editKegiatan.value = kegiatan;

                    // Update modal title dan subtitle berdasarkan status
                    const editModalTitle = document.getElementById('editModalTitle');
                    const editModalSubtitle = document.getElementById('editModalSubtitle');
                    const editLogbookSubmitBtn = document.getElementById('editLogbookSubmitBtn');

                    if (isRejected) {
                        editModalTitle.textContent = 'Edit Logbook (Ditolak)';
                        editModalSubtitle.textContent = 'Perbaiki data lalu ajukan ulang.';
                        editLogbookSubmitBtn.textContent = 'Simpan & Ajukan Ulang';
                    } else if (isValidated) {
                        editModalTitle.textContent = 'Edit Logbook (Tervalidasi)';
                        editModalSubtitle.textContent =
                            'Perbarui data logbook yang sudah tervalidasi.';
                        editLogbookSubmitBtn.textContent = 'Simpan Perubahan';
                    } else {
                        editModalTitle.textContent = 'Edit Logbook (Pending)';
                        editModalSubtitle.textContent =
                            'Perbarui data logbook yang sedang pending.';
                        editLogbookSubmitBtn.textContent = 'Simpan Perubahan';
                    }

                    // tampil alasan di popup jika ditolak
                    if (isRejected && rejectedNote && rejectedNote.trim() !== '') {
                        rejectedNoteBox.classList.remove('hidden');
                        rejectedNoteText.value = rejectedNote;
                    } else {
                        rejectedNoteBox.classList.add('hidden');
                        rejectedNoteText.value = '';
                    }

                    openEditModal();
                });
            });

            editClose?.addEventListener('click', closeEditModal);
            editCancel?.addEventListener('click', closeEditModal);
            editBackdrop?.addEventListener('click', closeEditModal);

            window.addEventListener('keydown', (e) => {
                if (e.key === 'Escape' && editModal && !editModal.classList.contains('hidden'))
                    closeEditModal();
            });
        });
    </script>

</x-app-layout>
