<x-app-layout>
    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Alert jika user nonaktif --}}
            @if(Auth::user()->status !== 'aktif')
            <div class="mb-6 p-4 bg-red-50 text-red-700 rounded-xl border border-red-100 text-center flex items-center justify-center gap-2">
                <svg class="w-6 h-6 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-12.728 12.728M5.636 5.636l12.728 12.728"></path>
                </svg>
                <span class="font-semibold">Akun Anda sudah nonaktif!</span> Anda tidak dapat mengisi logbook.
            </div>
            @endif

            {{-- Form logbook --}}
            @if(Auth::user()->status === 'aktif')
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-gray-100">
                <div class="p-8">
                    <div class="flex items-center mb-6">
                        <div class="p-3 bg-indigo-100 rounded-lg mr-4">
                            <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-800">Isi Kegiatan Hari Ini</h3>
                    </div>

                    <form action="{{ route('logbook.store') }}" method="POST">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                            <div class="md:col-span-1">
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Tanggal</label>
                                <input type="date" name="tanggal" value="{{ date('Y-m-d') }}"
                                    class="w-full rounded-xl border-gray-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition">
                            </div>
                            <div class="md:col-span-3">
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Deskripsi Kegiatan</label>
                                <textarea name="kegiatan" rows="2" placeholder="Jelaskan apa yang kamu kerjakan..."
                                    class="w-full rounded-xl border-gray-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition"></textarea>
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
                <div class="p-8 border-b border-gray-50 flex flex-col md:flex-row md:justify-between md:items-center gap-4">
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
                    <div class="overflow-x-auto rounded-2xl border border-gray-100">
                        <table class="w-full text-left border-collapse">
                            <thead class="sticky top-0 bg-gray-50 z-10">
                                <tr class="bg-gray-50/50">
                                    <th class="px-6 md:px-8 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider border-b">Tanggal</th>
                                    <th class="px-6 md:px-8 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider border-b">Aktivitas</th>
                                    <th class="px-6 md:px-8 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider border-b">Status</th>
                                    <th class="px-6 md:px-8 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider border-b">Aksi</th>
                                </tr>
                            </thead>

                            <tbody class="divide-y divide-gray-100">
                                @forelse($logbooks as $log)
                                <tr class="hover:bg-gray-50/80 transition">
                                    {{-- Tanggal --}}
                                    <td class="px-6 md:px-8 py-5 text-sm font-semibold text-gray-700 whitespace-nowrap">
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
                                        @if($log->is_rejected)
                                        <span class="px-3 py-1 inline-flex text-xs font-bold rounded-full bg-red-100 text-red-700">
                                            Ditolak
                                        </span>
                                        @elseif($log->is_validated)
                                        <span class="px-3 py-1 inline-flex text-xs font-bold rounded-full bg-green-100 text-green-700">
                                            Tervalidasi
                                        </span>
                                        @else
                                        <span class="px-3 py-1 inline-flex text-xs font-bold rounded-full bg-amber-100 text-amber-700">
                                            Pending
                                        </span>
                                        @endif
                                    </td>

                                    {{-- Aksi --}}
                                    <td class="px-6 md:px-8 py-5">
                                        @if($log->is_rejected)
                                        <button type="button"
                                            class="px-3 py-1.5 text-sm rounded-lg bg-blue-600 text-white font-semibold hover:bg-blue-700 transition shadow-sm"
                                            data-open-edit-logbook
                                            data-id="{{ $log->id }}"
                                            data-tanggal="{{ $log->tanggal }}"
                                            data-kegiatan="{{ e($log->kegiatan) }}"
                                            data-rejected-note="{{ e($log->rejected_note) }}">
                                            Edit & Ajukan Ulang
                                        </button>
                                        @else
                                        <span class="text-sm text-gray-400">-</span>
                                        @endif
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
                            @for($m=1; $m<=12; $m++)
                                <option value="{{ $m }}" {{ $m==now()->month ? 'selected' : '' }}>
                                {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                                </option>
                                @endfor
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Tahun</label>
                        <select name="tahun" class="w-full rounded-xl border-gray-200 bg-gray-50 font-bold">
                            @foreach($tahunList as $tahun)
                            <option value="{{ $tahun }}" {{ $tahun==now()->year ? 'selected' : '' }}>
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
            <div class="w-full max-w-md md:max-w-lg bg-white rounded-3xl shadow-2xl border border-gray-100 overflow-hidden">
                <div class="p-5 border-b border-gray-100 relative">
                    <button id="closeEditLogbookModal" type="button"
                        class="absolute top-4 right-4 w-10 h-10 rounded-xl flex items-center justify-center
                           text-gray-400 hover:text-gray-700 hover:bg-gray-100 transition">
                        &times;
                    </button>

                    <h2 class="text-xl font-black text-gray-900 text-center">Edit Logbook (Ditolak)</h2>
                    <p class="text-sm text-gray-500 mt-1 text-center">Perbaiki data lalu ajukan ulang.</p>
                </div>

                <form id="editLogbookForm" class="p-5 space-y-4" method="POST" action="">
                    @csrf
                    @method('PUT')

                    {{-- Alasan ditolak hanya di popup (tabel tidak menampilkan) --}}
                    <div id="rejectedNoteBox" class="p-4 rounded-2xl bg-red-50 border border-red-100 text-red-700">
                        <p class="text-sm text-red-600">
                            Alasan ditolak: <span class="font-semibold" id="rejectedNoteText"></span>
                        </p>
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
                        <button type="submit"
                            class="px-5 py-2.5 text-sm rounded-xl bg-blue-600 text-white font-bold hover:bg-blue-700 transition w-full md:w-auto shadow-sm">
                            Simpan & Ajukan Ulang
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Script modal download + edit --}}
    <script>
        document.addEventListener('DOMContentLoaded', () => {
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

                    // set action update (mengikuti logika route PUT /logbook/{id})
                    editForm.action = `{{ url('/logbook') }}/${id}`;

                    editTanggal.value = tanggal;
                    editKegiatan.value = kegiatan;

                    // tampil alasan di popup saja
                    if (rejectedNote && rejectedNote.trim() !== '') {
                        rejectedNoteBox.classList.remove('hidden');
                        rejectedNoteText.textContent = rejectedNote;
                    } else {
                        rejectedNoteBox.classList.add('hidden');
                        rejectedNoteText.textContent = '';
                    }

                    openEditModal();
                });
            });

            editClose?.addEventListener('click', closeEditModal);
            editCancel?.addEventListener('click', closeEditModal);
            editBackdrop?.addEventListener('click', closeEditModal);

            window.addEventListener('keydown', (e) => {
                if (e.key === 'Escape' && editModal && !editModal.classList.contains('hidden')) closeEditModal();
            });
        });
    </script>

</x-app-layout>