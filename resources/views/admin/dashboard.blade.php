<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Dashboard Admin</h2>
    </x-slot>

    <div class="py-12 bg-white min-h-screen">
        <div class="max-w-7xl mx-auto px-6 lg:px-8 space-y-10">

            <div>
                <h1 class="text-3xl font-black text-gray-900 tracking-tight">Ringkasan Aktivitas</h1>
                <p class="text-gray-500 mt-2">Pantau perkembangan mahasiswa magang dan validasi laporan.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-xl shadow-blue-50/50 flex flex-col justify-between group hover:border-blue-200 transition">
                    <div class="flex justify-between items-start mb-4">
                        <div class="p-3 bg-blue-50 text-blue-600 rounded-2xl group-hover:bg-blue-600 group-hover:text-white transition duration-300">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                        </div>
                        <span class="bg-blue-100 text-blue-700 text-[10px] font-bold px-2 py-1 rounded-full uppercase tracking-wide">Aktif</span>
                    </div>
                    <div>
                        <p class="text-sm text-gray-400 font-medium mb-1">Sedang Magang</p>
                        <p class="text-4xl font-black text-gray-900">{{ $activeStudents }}</p>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-xl shadow-gray-100/50 flex flex-col justify-between group hover:border-gray-300 transition">
                    <div class="flex justify-between items-start mb-4">
                        <div class="p-3 bg-gray-50 text-gray-500 rounded-2xl group-hover:bg-gray-800 group-hover:text-white transition duration-300">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <span class="bg-gray-100 text-gray-600 text-[10px] font-bold px-2 py-1 rounded-full uppercase tracking-wide">Selesai</span>
                    </div>
                    <div>
                        <p class="text-sm text-gray-400 font-medium mb-1">Alumni Magang</p>
                        <p class="text-4xl font-black text-gray-900">{{ $finishedStudents }}</p>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-xl shadow-amber-50/50 flex flex-col justify-between group hover:border-amber-200 transition">
                    <div class="flex justify-between items-start mb-4">
                        <div class="p-3 bg-amber-50 text-amber-600 rounded-2xl group-hover:bg-amber-500 group-hover:text-white transition duration-300">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <span class="bg-amber-100 text-amber-700 text-[10px] font-bold px-2 py-1 rounded-full uppercase tracking-wide">Pending</span>
                    </div>
                    <div>
                        <p class="text-sm text-gray-400 font-medium mb-1">Butuh Validasi</p>
                        <p class="text-4xl font-black text-gray-900">{{ $pendingLogbooks->count() }}</p>
                    </div>
                </div>

            </div>

            <div class="bg-white border border-gray-100 rounded-[2rem] shadow-2xl shadow-gray-100/50 overflow-hidden">
                <div class="p-8 border-b border-gray-50 flex flex-col md:flex-row justify-between items-center gap-4">
                    <div>
                        <h3 class="text-lg font-black text-gray-800">Persetujuan Logbook</h3>
                        <p class="text-sm text-gray-400">Validasi kegiatan harian mahasiswa.</p>
                    </div>

                    <form action="{{ route('admin.dashboard') }}" method="GET" class="flex w-full md:w-auto gap-2">
                        <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Cari mahasiswa..."
                            class="w-full md:w-64 px-4 py-2 rounded-xl border border-gray-200 text-sm focus:border-blue-500 focus:ring-blue-500 bg-gray-50">
                        <button type="submit" class="px-5 py-2 bg-gray-900 text-white rounded-xl hover:bg-black transition text-sm font-bold shadow-lg">
                            Cari
                        </button>
                    </form>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="bg-gray-50/50 border-b border-gray-100 text-xs uppercase tracking-widest text-gray-400 font-bold">
                                <th class="px-8 py-4">Mahasiswa</th>
                                <th class="px-8 py-4">Tanggal</th>
                                <th class="px-8 py-4">Kegiatan</th>
                                <th class="px-8 py-4 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($pendingLogbooks as $log)
                            <tr class="hover:bg-blue-50/30 transition">
                                <td class="px-8 py-4">
                                    <div class="font-bold text-gray-800">{{ $log->user->name }}</div>
                                </td>
                                <td class="px-8 py-4 text-sm text-gray-500 font-medium">
                                    {{ \Carbon\Carbon::parse($log->tanggal)->format('d M Y') }}
                                </td>
                                <td class="px-8 py-4 text-sm text-gray-600 truncate max-w-xs">
                                    {{ $log->kegiatan }}
                                </td>
                                <td class="px-8 py-4 text-center">
                                    <form action="{{ route('admin.logbook.validate', $log->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-lg transition shadow-md shadow-blue-200">
                                            Validasi
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="px-8 py-12 text-center">
                                    <div class="inline-block p-4 rounded-full bg-green-50 text-green-500 mb-2">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                    </div>
                                    <p class="text-gray-500 font-medium">Semua logbook aman terkendali.</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
