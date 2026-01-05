<x-app-layout>
    {{-- Hapus Header Slot --}}

    <div class="py-12 bg-white min-h-screen text-gray-900">
        <div class="max-w-7xl mx-auto px-6 lg:px-8">

            {{-- HEADER SECTION (JUDUL + TOMBOL TAMBAH) --}}
            <div class="flex flex-col md:flex-row justify-between items-end md:items-center mb-6 gap-4">

                {{-- Kiri: Judul --}}
                <div>
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-50 border border-blue-100 mb-2">
                        <span class="w-2 h-2 rounded-full bg-blue-600"></span>
                        <span class="text-xs font-bold text-blue-700 uppercase tracking-wider">Database Anggota</span>
                    </div>
                    <h1 class="text-3xl font-black text-gray-900 tracking-tight">Daftar Mahasiswa</h1>
                </div>

                {{-- Kanan: Aksi (Search & Tambah) --}}
                <div class="flex flex-col md:flex-row gap-3 w-full md:w-auto">

                    {{-- FORM PENCARIAN --}}
                    <form action="{{ route('admin.students.index') }}" method="GET" class="relative">
                        <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Cari Nama / NIM / Kampus..."
                            class="pl-10 pr-4 py-3 rounded-2xl border-gray-200 text-sm focus:border-blue-500 focus:ring-blue-500 shadow-sm w-full md:w-64">

                        {{-- Ikon Search --}}
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            {{-- <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg> --}}
                        </div>

                        {{-- Tombol Submit (Hidden tapi bisa di-enter) --}}
                        <button type="submit" class="hidden">Cari</button>
                    </form>

                    {{-- TOMBOL TAMBAH --}}
                    <a href="{{ route('admin.students.create') }}" class="group flex items-center justify-center gap-2 bg-blue-700 text-white px-5 py-3 rounded-2xl font-bold hover:bg-blue-800 transition shadow-lg shadow-blue-100 hover:shadow-blue-200 hover:-translate-y-0.5 whitespace-nowrap text-sm">
                        <svg class="w-5 h-5 transition-transform group-hover:rotate-90" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        Tambah Data
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
                                <th class="px-6 py-5">Instansi / Kampus</th>
                                <th class="px-6 py-5">Bidang / Divisi</th>
                                <th class="px-6 py-5 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($students as $s)
                            <tr class="group hover:bg-blue-50/30 transition duration-200">

                                {{-- 1. IDENTITAS --}}
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-4">
                                        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-100 to-blue-50 flex items-center justify-center text-blue-600 font-bold text-sm border border-blue-100 shrink-0">
                                            {{ substr($s->name, 0, 2) }}
                                        </div>
                                        <div>
                                            <div class="font-bold text-gray-900 group-hover:text-blue-700 transition">{{ $s->name }}</div>
                                            <div class="text-xs text-gray-400">{{ $s->email }}</div>
                                            @if($s->nim)
                                                <div class="text-[10px] font-bold text-gray-500 bg-gray-100 px-2 py-0.5 rounded-md inline-block mt-1">
                                                    {{ $s->nim }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </td>

                                {{-- 2. INSTANSI --}}
                                <td class="px-6 py-4">
                                    @if($s->instansi)
                                        <div class="text-sm font-bold text-gray-700 truncate max-w-[200px]" title="{{ $s->instansi }}">{{ $s->instansi }}</div>
                                        @if($s->prodi)
                                            <div class="text-xs text-gray-500">{{ $s->prodi }}</div>
                                        @endif
                                    @else
                                        <span class="text-xs text-gray-400 italic">Data belum lengkap</span>
                                    @endif
                                </td>

                                {{-- 3. BIDANG --}}
                                <td class="px-6 py-4">
                                    <span class="px-3 py-1 bg-purple-50 text-purple-700 rounded-lg text-xs font-bold border border-purple-100 whitespace-nowrap">
                                        {{ $s->bidang }}
                                    </span>
                                </td>

                                {{-- 4. AKSI --}}
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-center gap-3">
                                        {{-- Indikator Status --}}
                                        @if($s->status === 'aktif')
                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                <span class="w-1.5 h-1.5 rounded-full bg-green-600"></span> Aktif
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                <span class="w-1.5 h-1.5 rounded-full bg-gray-500"></span> Selesai
                                            </span>
                                        @endif

                                        {{-- Tombol Detail --}}
                                        <a href="{{ route('admin.students.show', $s->id) }}" class="p-2 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100 transition" title="Lihat Detail">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                        </a>

                                        {{-- Tombol Hapus --}}
                                        <form action="{{ route('admin.students.destroy', $s->id) }}" method="POST" onsubmit="return confirm('Hapus permanen mahasiswa {{ $s->name }}?');">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="p-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition" title="Hapus Permanen">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
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
                                        <p class="text-gray-400 text-sm mt-1">Coba kata kunci lain atau tambahkan data baru.</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- PAGINATION LINKS --}}
            <div class="mt-4">
                {{ $students->links() }}
            </div>

            <div class="mt-6 text-center">
                <p class="text-xs text-gray-400">&copy; Sistem Monitoring Baperlitbang</p>
            </div>

        </div>
    </div>
</x-app-layout>
