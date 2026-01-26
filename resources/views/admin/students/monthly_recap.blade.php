<x-app-layout>
    <div class="py-12 bg-white min-h-screen text-gray-900">
        <div class="max-w-7xl mx-auto px-6 lg:px-8">

            {{-- HEADER --}}
            <div class="flex flex-col md:flex-row justify-between items-center mb-8 gap-4">
                <div>
                    <div class="flex items-center gap-3 mb-1">
                        <a href="{{ route('admin.students.index') }}" class="p-2 rounded-full bg-gray-100 hover:bg-gray-200 transition">
                            <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                        </a>
                        <h1 class="text-2xl font-black text-gray-900">Rekap Presensi Bulanan</h1>
                    </div>
                    <p class="text-gray-500 text-sm ml-11">
                        Statistik kehadiran periode <span class="font-bold text-blue-600 uppercase">{{ $monthName }}</span>.
                    </p>
                </div>

                {{-- STAT CARD SIMPLE --}}
                <div class="bg-blue-50 px-5 py-3 rounded-2xl border border-blue-100">
                    <div class="text-xs font-bold text-blue-600 uppercase tracking-wider">Total Mahasiswa</div>
                    <div class="text-2xl font-black text-gray-900">{{ count($rekap) }} <span class="text-sm font-medium text-gray-400">Orang</span></div>
                </div>
            </div>

            {{-- TABLE --}}
            <div class="bg-white border border-gray-100 rounded-[2rem] shadow-xl overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50/50 border-b border-gray-100 text-xs uppercase tracking-widest text-gray-500 font-bold">
                                <th class="px-6 py-4">Mahasiswa</th>
                                <th class="px-6 py-4 text-center w-32">Hadir</th>
                                <th class="px-6 py-4 text-center w-32">Terlambat</th>
                                <th class="px-6 py-4 text-center w-32">Izin/Sakit</th>
                                <th class="px-6 py-4 text-center w-32">Alpha</th>
                                <th class="px-6 py-4 text-center w-24">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($rekap as $data)
                                <tr class="hover:bg-blue-50/30 transition group">
                                    {{-- NAMA --}}
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center font-bold text-gray-500 text-xs border border-gray-200">
                                                {{ substr($data['user']->name, 0, 2) }}
                                            </div>
                                            <div>
                                                <div class="font-bold text-gray-900 group-hover:text-blue-600 transition">{{ $data['user']->name }}</div>
                                                <div class="text-xs text-gray-400">{{ $data['user']->bidang }}</div>
                                            </div>
                                        </div>
                                    </td>

                                    {{-- HADIR --}}
                                    <td class="px-6 py-4 text-center">
                                        <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-green-100 text-green-700 font-bold text-sm">
                                            {{ $data['total_hadir'] }}
                                        </span>
                                    </td>

                                    {{-- TERLAMBAT --}}
                                    <td class="px-6 py-4 text-center">
                                        @if($data['total_terlambat'] > 0)
                                            <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-orange-100 text-orange-700 font-bold text-sm">
                                                {{ $data['total_terlambat'] }}
                                            </span>
                                        @else
                                            <span class="text-gray-300">-</span>
                                        @endif
                                    </td>

                                    {{-- IZIN / SAKIT --}}
                                    <td class="px-6 py-4 text-center">
                                        @if($data['total_izin'] > 0)
                                            <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-blue-100 text-blue-700 font-bold text-sm">
                                                {{ $data['total_izin'] }}
                                            </span>
                                        @else
                                            <span class="text-gray-300">-</span>
                                        @endif
                                    </td>

                                    {{-- ALPHA --}}
                                    <td class="px-6 py-4 text-center">
                                        @if($data['total_alpha'] > 0)
                                            <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-red-100 text-red-700 font-bold text-sm animate-pulse">
                                                {{ $data['total_alpha'] }}
                                            </span>
                                        @else
                                            <span class="text-gray-300">-</span>
                                        @endif
                                    </td>

                                    {{-- AKSI DETAIL --}}
                                    <td class="px-6 py-4 text-center">
                                        <a href="{{ route('admin.students.show', $data['user']->id) }}" class="text-gray-400 hover:text-blue-600 transition" title="Lihat Detail">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-8 text-center text-gray-400">
                                        Data presensi bulan ini belum tersedia.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- FOOTER NOTE --}}
            <div class="mt-4 flex gap-4 text-xs text-gray-400 justify-center">
                <div class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-green-500"></span> Hadir</div>
                <div class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-orange-500"></span> Terlambat</div>
                <div class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-blue-500"></span> Izin/Sakit</div>
                <div class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-red-500"></span> Alpha (Tanpa Keterangan)</div>
            </div>

        </div>
    </div>
</x-app-layout>
