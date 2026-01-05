<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Dashboard Admin</h2>
    </x-slot>

    <div class="py-12 bg-white min-h-screen">
        <div class="max-w-7xl mx-auto px-6 lg:px-8 space-y-10">

            {{-- 1. SECTION RINGKASAN AKTIVITAS (KARTU ATAS) --}}
            <div>
                <h1 class="text-3xl font-black text-gray-900 tracking-tight">Ringkasan Aktivitas</h1>
                <p class="text-gray-500 mt-2">Pantau perkembangan mahasiswa magang dan validasi laporan.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Card Mahasiswa Aktif --}}
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

                {{-- Card Alumni Magang --}}
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

                {{-- Card Pending Logbook --}}
                <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-xl shadow-amber-50/50 flex flex-col justify-between group hover:border-amber-200 transition">
                    <div class="flex justify-between items-start mb-4">
                        <div class="p-3 bg-amber-50 text-amber-600 rounded-2xl group-hover:bg-amber-500 group-hover:text-white transition duration-300">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <span class="bg-amber-100 text-amber-700 text-[10px] font-bold px-2 py-1 rounded-full uppercase tracking-wide">Pending</span>
                    </div>
                    <div>
                        <p class="text-sm text-gray-400 font-medium mb-1">Logbook Butuh Validasi</p>
                        <p class="text-4xl font-black text-gray-900">{{ $pendingLogbooks->count() }}</p>
                    </div>
                </div>

                {{-- Card Pending Laporan --}}
                <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-xl shadow-blue-50/50 flex flex-col justify-between group hover:border-blue-200 transition">
                    <div class="flex justify-between items-start mb-4">
                        <div class="p-3 bg-blue-50 text-blue-600 rounded-2xl group-hover:bg-blue-600 group-hover:text-white transition duration-300">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 4H7a2 2 0 01-2-2V6a2 2 0 012-2h5l5 5v9a2 2 0 01-2 2z" /></svg>
                        </div>
                        <span class="bg-blue-100 text-blue-700 text-[10px] font-bold px-2 py-1 rounded-full uppercase tracking-wide">Pending</span>
                    </div>
                    <div>
                        <p class="text-sm text-gray-400 font-medium mb-1">Laporan Perlu Validasi</p>
                        <p class="text-4xl font-black text-gray-900">{{ $pendingLaporans->count() }}</p>
                    </div>
                </div>
            </div>


            {{-- 2. TABEL PERSETUJUAN LOGBOOK --}}
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
                                <td class="px-8 py-4 font-bold text-gray-800">{{ $log->user->name }}</td>
                                <td class="px-8 py-4 text-sm text-gray-500">{{ \Carbon\Carbon::parse($log->tanggal)->format('d M Y') }}</td>
                                <td class="px-8 py-4 text-sm text-gray-600 truncate max-w-xs">{{ $log->kegiatan }}</td>
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
                                <td colspan="4" class="px-8 py-12 text-center text-gray-400 italic">
                                    Semua logbook aman terkendali.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>


            {{-- 3. TABEL VALIDASI LAPORAN (YANG BARU DITAMBAHKAN) --}}
            {{-- Ini tabel khusus untuk pendingLaporans agar Admin mudah memvalidasi --}}
            <div class="bg-white border border-gray-100 rounded-[2rem] shadow-2xl shadow-blue-50/50 overflow-hidden mt-10">
                <div class="p-8 border-b border-gray-50">
                    <h3 class="text-lg font-black text-gray-800">Validasi Laporan Magang</h3>
                    <p class="text-sm text-gray-400">Daftar laporan akhir yang perlu diperiksa dan disetujui.</p>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="bg-blue-50/50 border-b border-blue-100 text-xs uppercase tracking-widest text-blue-600 font-bold">
                                <th class="px-8 py-4">Mahasiswa</th>
                                <th class="px-8 py-4">Tanggal Upload</th>
                                <th class="px-8 py-4">Nama File</th>
                                <th class="px-8 py-4 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($pendingLaporans as $laporan)
                            <tr class="hover:bg-blue-50/30 transition">
                                <td class="px-8 py-4 font-bold text-gray-800">{{ $laporan->user->name }}</td>
                                <td class="px-8 py-4 text-sm text-gray-500">{{ $laporan->created_at->format('d M Y') }}</td>
                                <td class="px-8 py-4">
                                    <div class="flex items-center gap-2">
                                        <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                                        <span class="text-sm text-gray-600 truncate max-w-xs">{{ $laporan->nama_file }}</span>
                                    </div>
                                </td>
                                <td class="px-8 py-4 text-center flex items-center justify-center gap-2">
                                    {{-- Tombol Download --}}
                                    <a href="{{ route('laporan.download', $laporan->id) }}"
                                       class="px-3 py-2 bg-gray-100 hover:bg-gray-200 text-gray-600 text-xs font-bold rounded-lg transition"
                                       target="_blank" title="Download">
                                        ⬇ Download
                                    </a>

                                    {{-- Tombol Validasi --}}
                                    <form action="{{ route('admin.laporan.validate', $laporan->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-lg transition shadow-md shadow-blue-200">
                                            Validasi ✅
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="px-8 py-12 text-center text-gray-400 italic">
                                    Tidak ada laporan yang perlu divalidasi.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>


            {{-- 4. DAFTAR STATUS MAHASISWA & LAPORAN (BAWAAN LAMA) --}}
            <div class="bg-white border border-gray-100 rounded-[2rem] shadow-2xl shadow-gray-100/50 overflow-hidden mt-10">
                <div class="p-8 border-b border-gray-50 flex flex-col md:flex-row justify-between items-center gap-4">
                    <div>
                        <h3 class="text-lg font-black text-gray-800">Daftar Status Laporan (Alumni)</h3>
                        <p class="text-sm text-gray-400">Pantau mahasiswa yang sudah selesai dan status laporannya.</p>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="bg-gray-50/50 border-b border-gray-100 text-xs uppercase tracking-widest text-gray-400 font-bold">
                                <th class="px-6 py-4">Mahasiswa</th>
                                <th class="px-6 py-4">Status Magang</th>
                                <th class="px-6 py-4">Status Laporan</th>
                                <th class="px-6 py-4">Tanggal Upload Terakhir</th>
                                <th class="px-6 py-4 text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($students as $student)
                            <tr class="hover:bg-blue-50/30 transition">
                                <td class="px-6 py-4 font-medium">{{ $student->name }}</td>
                                <td class="px-6 py-4">
                                    @if($student->status === 'aktif')
                                        <span class="px-2 py-1 bg-green-100 text-green-700 rounded-full text-xs font-bold">Aktif</span>
                                    @else
                                        <span class="px-2 py-1 bg-gray-100 text-gray-600 rounded-full text-xs font-bold">Selesai</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    @if($student->laporans->isEmpty())
                                        <span class="px-2 py-1 bg-red-100 text-red-600 rounded-full text-xs font-bold">Belum Upload</span>
                                    @else
                                        <span class="px-2 py-1 bg-blue-100 text-blue-700 rounded-full text-xs font-bold">Sudah Upload</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-gray-500 text-sm">
                                    {{ $student->laporans->last()?->created_at->format('d M Y') ?? '-' }}
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @php
                                        $laporanTerakhir = $student->laporans->last();
                                    @endphp

                                    @if(!$laporanTerakhir)
                                        <span class="text-gray-400 text-xs italic">Belum upload</span>
                                    @elseif($laporanTerakhir->is_validated)
                                        <span class="px-3 py-1 bg-green-100 text-green-700 text-xs font-bold rounded-full">
                                            Sudah divalidasi
                                        </span>
                                    @else
                                        <span class="px-3 py-1 bg-amber-100 text-amber-700 text-xs font-bold rounded-full">
                                            Menunggu Validasi
                                        </span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center italic text-gray-400">Belum ada mahasiswa non-aktif.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
