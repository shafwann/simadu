<x-app-layout>
    {{-- Hapus Header Slot --}}

    <div class="py-12 bg-white min-h-screen text-gray-900">
        <div class="max-w-7xl mx-auto px-6 lg:px-8">

            {{-- HEADER SECTION --}}
<div class="flex flex-col md:flex-row justify-between items-end md:items-center mb-6 gap-4">

    {{-- Kiri: Judul --}}
    <div>
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-50 border border-blue-100 mb-2">
            <span class="w-2 h-2 rounded-full bg-blue-600"></span>
            <span class="text-xs font-bold text-blue-700 uppercase tracking-wider">Database Anggota</span>
        </div>
        <h1 class="text-3xl font-black text-gray-900 tracking-tight">Daftar Mahasiswa</h1>
    </div>

    {{-- Kanan: Aksi (Rekap + Filter + Search + Tambah) --}}
<div class="flex flex-col md:flex-row gap-3 w-full md:w-auto items-center">

    {{-- Tombol Rekap Absensi --}}
    <a href="{{ route('admin.attendance.recap') }}"
       class="group flex items-center justify-center gap-2 bg-blue-600 text-white px-5 py-3 rounded-2xl font-bold
              hover:bg-blue-700 transition shadow-lg shadow-blue-100 hover:shadow-blue-200 whitespace-nowrap text-sm w-full md:w-auto">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M9 17v-2a4 4 0 014-4h6M9 17H7a2 2 0 01-2-2V7a2 2 0 012-2h10a2 2 0 012 2v3" />
        </svg>
        Rekap Absensi
    </a>

    
                    {{-- FORM SEARCH & FILTER (Digabung dalam 1 Form) --}}
                    <form action="{{ route('admin.students.index') }}" method="GET" class="flex flex-col md:flex-row gap-3 w-full md:w-auto">

                        {{-- 1. FILTER STATUS (BARU) --}}
                        <div class="relative">
                            <select name="status" onchange="this.form.submit()"
                                class="appearance-none pl-4 pr-10 py-3 rounded-2xl border-gray-200 text-sm font-bold focus:border-blue-500 focus:ring-blue-500 shadow-sm cursor-pointer bg-white text-gray-700 w-full md:w-40 hover:border-blue-300 transition">
                                <option value="">Semua Status</option>
                                <option value="aktif" {{ request('status') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                                <option value="nonaktif" {{ request('status') == 'nonaktif' ? 'selected' : '' }}>Selesai</option>
                            </select>
                            {{-- Panah Dropdown Custom --}}
                            {{-- <div class="absolute inset-y-0 right-0 flex items-center px-3 pointer-events-none text-gray-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </div> --}}
                        </div>

                        {{-- 2. SEARCH INPUT --}}
                        <div class="relative w-full md:w-64">
                            <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Cari Nama / NIM..."
                                class="pl-10 pr-4 py-3 rounded-2xl border-gray-200 text-sm focus:border-blue-500 focus:ring-blue-500 shadow-sm w-full">
                            {{-- <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                            </div> --}}
                        </div>
                    </form>

                    {{-- TOMBOL TAMBAH --}}
                    <a href="{{ route('admin.students.create') }}" class="group flex items-center justify-center gap-2 bg-blue-700 text-white px-5 py-3 rounded-2xl font-bold hover:bg-blue-800 transition shadow-lg shadow-blue-100 hover:shadow-blue-200 hover:-translate-y-0.5 whitespace-nowrap text-sm w-full md:w-auto">
                        <svg class="w-5 h-5 transition-transform group-hover:rotate-90" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        Tambah
                    </a>
                </div>
            </div>

            {{-- TABLE --}}
            <div class="bg-white border border-gray-100 rounded-[2rem] shadow-2xl shadow-gray-100/50 overflow-hidden mb-6">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50/50 border-b border-gray-100 text-xs uppercase tracking-widest text-gray-400 font-bold">
                                <th class="px-6 py-5">Mahasiswa</th>
                                <th class="px-6 py-5">Instansi & Periode</th>
                                <th class="px-6 py-5">Bidang / Divisi</th>
                                <th class="px-6 py-5 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($students as $s)
                            <tr class="group transition duration-200 border-b border-gray-50
                                {{ $s->status !== 'aktif' ? 'bg-gray-50/80 opacity-75 grayscale-[20%]' : 'hover:bg-blue-50/40 bg-white' }}">

                                {{-- 1. IDENTITAS --}}
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-4">

                                        {{-- AVATAR --}}
                                        <div class="relative w-12 h-12 rounded-full flex items-center justify-center font-bold text-sm border shrink-0
                                            {{ $s->status === 'aktif'
                                                ? 'bg-gradient-to-br from-blue-100 to-blue-50 text-blue-600 border-blue-100'
                                                : 'bg-gray-100 text-gray-400 border-gray-200' }}">

                                            {{ substr($s->name, 0, 2) }}

                                            {{-- Dot Status --}}
                                            <span class="absolute bottom-0 right-0 block h-3.5 w-3.5 rounded-full ring-2 ring-white
                                                {{ $s->status === 'aktif' ? 'bg-green-500' : 'bg-gray-400' }}">
                                            </span>
                                        </div>

                                        <div>
                                            <div class="flex items-center gap-2">
                                                <div class="font-bold text-base {{ $s->status === 'aktif' ? 'text-gray-900 group-hover:text-blue-700' : 'text-gray-500 line-through decoration-gray-300' }}">
                                                    {{ $s->name }}
                                                </div>
                                                @if($s->status !== 'aktif')
                                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-gray-200 text-gray-500 uppercase tracking-wide border border-gray-300">
                                                        Selesai
                                                    </span>
                                                @endif
                                            </div>

                                            <div class="text-xs text-gray-400">{{ $s->email }}</div>

                                            <div class="flex gap-2 mt-1.5">
                                                @if($s->nim)
                                                    <span class="text-[10px] font-bold text-gray-500 bg-gray-100 px-2 py-0.5 rounded-md border border-gray-200">
                                                        {{ $s->nim }}
                                                    </span>
                                                @endif
                                                @if($s->kegiatan)
                                                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-md border
                                                        {{ $s->status === 'aktif' ? 'text-purple-600 bg-purple-50 border-purple-100' : 'text-gray-500 bg-gray-100 border-gray-200' }}">
                                                        {{ $s->kegiatan }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                {{-- 2. INSTANSI & TANGGAL --}}
                                <td class="px-6 py-4">
                                    @if($s->instansi)
                                        <div class="text-sm font-bold truncate max-w-[200px] {{ $s->status === 'aktif' ? 'text-gray-700' : 'text-gray-500' }}" title="{{ $s->instansi }}">
                                            {{ $s->instansi }}
                                        </div>
                                        <div class="text-xs text-gray-400 mb-2">{{ $s->prodi }}</div>

                                        @if($s->tanggal_masuk && $s->tanggal_selesai)
                                            @php
                                                $start = \Carbon\Carbon::parse($s->tanggal_masuk);
                                                $end = \Carbon\Carbon::parse($s->tanggal_selesai);
                                                $now = \Carbon\Carbon::now();
                                                $diff = $now->diffInDays($end, false);
                                                $isEndingSoon = $diff >= 0 && $diff <= 7;
                                                $isExpired = $diff < 0;
                                            @endphp

                                            <div class="flex flex-col gap-1">
                                                <div class="flex items-center gap-1.5 text-[11px] text-gray-500 bg-white px-2 py-1 rounded-lg border border-gray-200 w-fit shadow-sm">
                                                    <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                                    <span>{{ $start->format('d M') }} - {{ $end->format('d M Y') }}</span>
                                                </div>

                                                @if($s->status == 'aktif')
                                                    @if($isExpired)
                                                        <span class="text-[10px] font-bold text-red-600 flex items-center gap-1">
                                                            <span class="w-1.5 h-1.5 bg-red-500 rounded-full animate-pulse"></span>
                                                            Lewat {{ abs(intval($diff)) }} hari
                                                        </span>
                                                    @elseif($isEndingSoon)
                                                        <span class="text-[10px] font-bold text-amber-600 flex items-center gap-1">
                                                            <span class="w-1.5 h-1.5 bg-amber-500 rounded-full animate-pulse"></span>
                                                            Sisa {{ intval($diff) }} hari lagi
                                                        </span>
                                                    @endif
                                                @endif
                                            </div>
                                        @endif
                                    @else
                                        <span class="text-xs text-gray-400 italic">Data belum lengkap</span>
                                    @endif
                                </td>

                                {{-- 3. BIDANG --}}
                                <td class="px-6 py-4">
                                    <span class="px-3 py-1 rounded-lg text-xs font-bold border whitespace-nowrap
                                        {{ $s->status === 'aktif' ? 'bg-purple-50 text-purple-700 border-purple-100' : 'bg-gray-100 text-gray-500 border-gray-200' }}">
                                        {{ $s->bidang }}
                                    </span>

                                    @if($s->nama_pembimbing)
    <div class="mt-1 pl-3 text-xs text-gray-600">
        Pembimbing:
        <span class="font-semibold text-gray-800">
            {{ $s->nama_pembimbing }}
        </span>
    </div>
@endif

                                </td>

                                {{-- 4. AKSI --}}
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-center gap-2">
                                        <a href="{{ route('admin.students.show', $s->id) }}" class="p-2 bg-white border border-blue-100 text-blue-600 rounded-lg hover:bg-blue-50 transition shadow-sm" title="Lihat Detail">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                        </a>

                                        <a href="{{ route('admin.students.edit', $s->id) }}" class="p-2 bg-white border border-yellow-100 text-yellow-600 rounded-lg hover:bg-yellow-50 transition shadow-sm" title="Edit Data">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                        </a>

                                        <form action="{{ route('admin.students.destroy', $s->id) }}" method="POST" onsubmit="return confirm('Hapus permanen mahasiswa {{ $s->name }}?');">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="p-2 bg-white border border-red-100 text-red-600 rounded-lg hover:bg-red-50 transition shadow-sm" title="Hapus Permanen">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="px-8 py-16 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mb-4">
                                            <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                        </div>
                                        <p class="text-gray-500 font-medium">Data tidak ditemukan.</p>
                                        <p class="text-gray-400 text-sm mt-1">Coba sesuaikan filter atau kata kunci pencarian.</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- PAGINATION --}}
            <div class="mt-4">
                {{ $students->links() }}
            </div>

            <div class="mt-6 text-center">
                <p class="text-xs text-gray-400">&copy; Sistem Monitoring Baperlitbang</p>
            </div>

        </div>
    </div>
</x-app-layout>
