<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard Master Admin') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

                        {{-- 1. SECTION STATISTIK --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                {{-- ... (Kode kartu statistik tetap sama, tapi angkanya otomatis berubah sesuai filter) ... --}}

                {{-- Card 1: Total Mahasiswa --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border-l-4 border-blue-500 p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-blue-100 text-blue-500 mr-4">
                           <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">
                                {{ $selectedInstansi ? 'Mhs di '.$selectedInstansi : 'Total Mahasiswa' }}
                            </p>
                            <p class="text-2xl font-black text-gray-800">{{ $stats['total_mahasiswa'] }}</p>
                        </div>
                    </div>
                </div>

                {{-- Card Lainnya biarkan sama ... --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border-l-4 border-purple-500 p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-purple-100 text-purple-500 mr-4">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Super Admin</p>
                            <p class="text-2xl font-black text-gray-800">{{ $stats['total_super_admin'] }}</p>
                        </div>
                    </div>
                </div>

                 {{-- Card 3: Admin Bidang (IKUT TERFILTER) --}}
                 <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border-l-4 border-orange-500 p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-orange-100 text-orange-500 mr-4">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">
                                {{ $selectedInstansi ? 'Admin '.$selectedInstansi : 'Total Admin Bidang' }}
                            </p>
                            <p class="text-2xl font-black text-gray-800">{{ $stats['total_admin_bidang'] }}</p>
                        </div>
                    </div>
                </div>

                {{-- Card 4: Dosen --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border-l-4 border-green-500 p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-green-100 text-green-500 mr-4">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Dosen Pembimbing</p>
                            <p class="text-2xl font-black text-gray-800">{{ $stats['total_dosen'] }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- FILTER SECTION (BARU) --}}
            <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-200 flex flex-col sm:flex-row justify-between items-center gap-4">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
                    <span class="font-bold text-gray-700">Filter Instansi:</span>
                </div>

                <form method="GET" action="{{ route('dashboard') }}" class="flex items-center gap-2 w-full sm:w-auto">
                    <select name="instansi" onchange="this.form.submit()" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm w-full sm:w-64">
                        <option value="">-- Tampilkan Semua --</option>
                        @foreach($listInstansi as $instansi)
                            <option value="{{ $instansi }}" {{ $selectedInstansi == $instansi ? 'selected' : '' }}>
                                {{ $instansi }}
                            </option>
                        @endforeach
                    </select>

                    {{-- Tombol Reset (Muncul jika sedang memfilter) --}}
                    @if($selectedInstansi)
                        <a href="{{ route('dashboard') }}" class="px-3 py-2 bg-gray-100 text-gray-600 rounded-md text-sm hover:bg-gray-200 transition">
                            Reset
                        </a>
                    @endif
                </form>
            </div>

            {{-- 2. SECTION TABEL PESERTA MAGANG --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-200">
                <div class="p-6 bg-white border-b border-gray-200 flex justify-between items-center">
                    <h3 class="text-lg font-bold text-gray-800">
                        {{ $selectedInstansi ? 'Data Peserta: ' . $selectedInstansi : 'Direktori Semua Peserta Magang' }}
                    </h3>
                </div>

                <div class="overflow-x-auto">
                    {{-- TABEL SAMA SEPERTI SEBELUMNYA --}}
                    <table class="w-full text-left border-collapse">
                        <thead class="bg-gray-50">
                            <tr class="text-xs uppercase tracking-wider text-gray-500 border-b border-gray-100">
                                <th class="px-6 py-4 font-bold">Mahasiswa / Siswa</th>
                                <th class="px-6 py-4 font-bold">Asal Instansi</th>
                                <th class="px-6 py-4 font-bold">Penempatan</th>
                                <th class="px-6 py-4 font-bold text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($mahasiswa as $m)
                            <tr class="hover:bg-gray-50 transition duration-150">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center font-bold text-sm">
                                            {{ substr($m->name, 0, 1) }}
                                        </div>
                                        <div>
                                            <div class="font-bold text-gray-900">{{ $m->name }}</div>
                                            <div class="text-xs text-gray-500">{{ $m->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-700 font-medium">{{ $m->instansi }}</div>
                                    <div class="text-xs text-gray-400 mt-1">{{ $m->nim ?? '-' }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-medium bg-purple-50 text-purple-700 border border-purple-100">
                                        {{ $m->kategori_instansi ?? 'Belum Ploting' }}
                                    </span>
                                    <div class="text-[10px] text-gray-400 mt-1">{{ Str::limit($m->bidang, 30) }}</div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @if($m->status === 'aktif')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-green-100 text-green-700">
                                            <span class="w-1.5 h-1.5 rounded-full bg-green-500 animate-pulse"></span>
                                            Aktif
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-gray-100 text-gray-500">
                                            Selesai
                                        </span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="px-6 py-8 text-center text-gray-400">
                                    Tidak ada data peserta untuk filter ini.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- PAGINATION --}}
                <div class="px-6 py-4 border-t border-gray-100">
                    {{ $mahasiswa->links() }}
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
