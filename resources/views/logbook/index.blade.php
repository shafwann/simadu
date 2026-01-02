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
                <div class="p-8 border-b border-gray-50 flex justify-between items-center">
                    <h3 class="text-xl font-bold text-gray-800">Riwayat Logbook</h3>
                    <a href="{{ route('logbook.export') }}" class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-bold rounded-xl transition shadow-md">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                        </svg>
                        Export Logbook (.csv)
                    </a>
                </div>
                <div class="overflow-x-auto">
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
</x-app-layout>
