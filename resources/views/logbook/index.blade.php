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
                <div class="p-8 border-b border-gray-50 flex justify-between items-center">
                    <h3 class="text-xl font-bold text-gray-800">Riwayat Logbook</h3>

                    {{-- Tombol Download PDF --}}
                    <button id="btnDownloadLogbook"
                        class="px-4 py-2 bg-blue-600 text-white font-bold rounded-xl shadow hover:bg-blue-700 transition">
                        Download Logbook (PDF)
                    </button>
                </div>

                {{-- Tabel logbook --}}
                <div class="overflow-x-auto p-8">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50/50">
                                <th class="px-8 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider border-b">Tanggal</th>
                                <th class="px-8 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider border-b">Aktivitas</th>
                                <th class="px-8 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider border-b">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($logbooks as $log)
                            <tr class="hover:bg-gray-50/80 transition">
                                <td class="px-8 py-5 text-sm font-semibold text-gray-700">
                                    {{ \Carbon\Carbon::parse($log->tanggal)->isoFormat('D MMMM Y') }}
                                </td>
                                <td class="px-8 py-5 text-sm text-gray-600">
                                    {{ $log->kegiatan }}
                                </td>
                                <td class="px-8 py-5">
                                    <span class="px-3 py-1 inline-flex text-xs font-bold rounded-full {{ $log->is_validated ? 'bg-green-100 text-green-700' : 'bg-amber-100 text-amber-700' }}">
                                        {{ $log->is_validated ? 'Tervalidasi' : 'Pending' }}
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="px-8 py-10 text-center text-gray-400 italic">Belum ada kegiatan tercatat.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
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

    {{-- Script modal --}}
    <script>
    document.addEventListener('DOMContentLoaded', () => {
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
            if(e.key==='Escape' && !modal.classList.contains('hidden')) closeM();
        });
    });
    </script>

</x-app-layout>
