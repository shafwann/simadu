<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Panel Pembimbing / Admin</h2>
    </x-slot>

    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center">
                    <div class="p-4 bg-blue-100 text-blue-600 rounded-xl mr-4">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 font-medium">Mahasiswa Magang</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $users->count() }} Orang</p>
                    </div>
                </div>
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center">
                    <div class="p-4 bg-amber-100 text-amber-600 rounded-xl mr-4">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 font-medium">Logbook Butuh Validasi</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $pendingLogbooks->count() }} Laporan</p>
                    </div>
                </div>
            </div>

            <div class="bg-white shadow-sm sm:rounded-2xl border border-gray-100 overflow-hidden">
                <div class="p-6 border-b border-gray-50">
                    <h3 class="text-lg font-bold text-gray-800 text-blue-600">Persetujuan Logbook Mahasiswa</h3>
                </div>
                <div class="overflow-x-auto">
                    <div class="bg-white shadow-sm sm:rounded-2xl border border-gray-100 overflow-hidden">
                        <div class="p-6 border-b border-gray-50 flex flex-col md:flex-row justify-between items-center gap-4">
                            <h3 class="text-lg font-bold text-gray-800">Persetujuan Logbook Mahasiswa</h3>

                            <form action="{{ route('admin.dashboard') }}" method="GET" class="flex w-full md:w-auto gap-2">
                                <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama mahasiswa..."
                                    class="w-full md:w-64 rounded-xl border-gray-200 text-sm focus:border-blue-500 focus:ring-blue-500">
                                <button type="submit" class="px-4 py-2 bg-gray-800 text-white rounded-xl hover:bg-black transition text-sm font-bold">
                                    Cari
                                </button>
                                @if($search)
                                    <a href="{{ route('admin.dashboard') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-xl hover:bg-gray-300 transition text-sm font-bold">
                                        Reset
                                    </a>
                                @endif
                            </form>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="w-full text-left">
                                </table>
                        </div>
                    </div>
                    <table class="w-full text-left">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase">Mahasiswa</th>
                                <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase">Tanggal</th>
                                <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase">Kegiatan</th>
                                <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($pendingLogbooks as $log)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4 text-sm font-bold text-gray-900">{{ $log->user->name }}</td>
                                <td class="px-6 py-4 text-sm text-gray-600">{{ \Carbon\Carbon::parse($log->tanggal)->format('d M Y') }}</td>
                                <td class="px-6 py-4 text-sm text-gray-600">{{ $log->kegiatan }}</td>
                                <td class="px-6 py-4 text-center">
                                    <form action="{{ route('admin.logbook.validate', $log->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-lg transition">
                                            Validasi
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="px-6 py-10 text-center text-gray-400 italic">Semua logbook sudah tervalidasi. ✅</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
