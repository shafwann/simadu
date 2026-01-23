<x-app-layout>
    {{-- Hapus Slot Header Bawaan agar seragam dengan desain custom --}}

    <div class="py-12 bg-white min-h-screen text-gray-900">
        <div class="max-w-7xl mx-auto px-6 lg:px-8">

            {{-- ALERT SUKSES --}}
            @if(session('success'))
                <div x-data="{ show: true }" x-show="show" class="mb-6 flex items-center justify-between bg-green-50 border border-green-100 text-green-700 px-4 py-3 rounded-2xl shadow-sm relative">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        <span class="font-bold text-sm">{{ session('success') }}</span>
                    </div>
                    <button @click="show = false" class="text-green-500 hover:text-green-700">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
            @endif

            {{-- HEADER SECTION --}}
            <div class="flex flex-col md:flex-row justify-between items-end md:items-center mb-6 gap-4">

                {{-- Kiri: Judul --}}
                <div>
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-50 border border-blue-100 mb-2">
                        <span class="w-2 h-2 rounded-full bg-blue-600"></span>
                        <span class="text-xs font-bold text-blue-700 uppercase tracking-wider">Master Data</span>
                    </div>
                    <h1 class="text-3xl font-black text-gray-900 tracking-tight">Kelola Instansi</h1>
                    <p class="text-gray-500 text-sm mt-1">Daftar akun admin dinas/instansi yang terdaftar.</p>
                </div>

                {{-- Kanan: Tombol Tambah --}}
                <div class="flex gap-3 w-full md:w-auto">
                    <a href="{{ route('master.agencies.create') }}" class="group flex items-center justify-center gap-2 bg-blue-700 text-white px-5 py-3 rounded-2xl font-bold hover:bg-blue-800 transition shadow-lg shadow-blue-100 hover:shadow-blue-200 hover:-translate-y-0.5 whitespace-nowrap text-sm w-full md:w-auto">
                        <svg class="w-5 h-5 transition-transform group-hover:rotate-90" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        Tambah Instansi
                    </a>
                </div>
            </div>

            {{-- TABLE SECTION --}}
            <div class="bg-white border border-gray-100 rounded-[2rem] shadow-2xl shadow-gray-100/50 overflow-hidden mb-6">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50/50 border-b border-gray-100 text-xs uppercase tracking-widest text-gray-400 font-bold">
                                <th class="px-6 py-5">Nama Instansi (Kategori)</th>
                                <th class="px-6 py-5">Super Admin</th>
                                <th class="px-6 py-5">Email Login</th>
                                <th class="px-6 py-5 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($agencies as $admin)
                            <tr class="group transition duration-200 border-b border-gray-50 hover:bg-blue-50/40 bg-white">

                                {{-- 1. KATEGORI INSTANSI --}}
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center font-bold text-lg">
                                            {{ substr($admin->kategori_instansi, 0, 1) }}
                                        </div>
                                        <div>
                                            <span class="px-2.5 py-1 rounded-lg text-xs font-bold bg-blue-50 text-blue-700 border border-blue-100">
                                                {{ $admin->kategori_instansi }}
                                            </span>
                                        </div>
                                    </div>
                                </td>

                                {{-- 2. NAMA ADMIN --}}
                                <td class="px-6 py-4">
                                    <div class="font-bold text-gray-900 group-hover:text-blue-700 transition">
                                        {{ $admin->name }}
                                    </div>
                                    <div class="text-xs text-gray-400 mt-0.5">Role: {{ ucfirst($admin->role) }}</div>
                                </td>

                                {{-- 3. EMAIL --}}
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-600 font-medium">
                                        {{ $admin->email }}
                                    </div>
                                </td>

                                {{-- 4. AKSI (ICON BUTTONS) --}}
                                <td class="px-6 py-4 text-center">
                                    <div class="flex items-center justify-center gap-2">

                                        {{-- TOMBOL EDIT (Kuning) --}}
                                        <a href="{{ route('master.agencies.edit', $admin->id) }}"
                                           class="p-2 bg-white border border-yellow-100 text-yellow-600 rounded-lg hover:bg-yellow-50 transition shadow-sm"
                                           title="Edit Data">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                            </svg>
                                        </a>

                                        {{-- TOMBOL HAPUS (Merah) --}}
                                        <form action="{{ route('master.agencies.destroy', $admin->id) }}" method="POST"
                                              onsubmit="return confirm('Yakin ingin menghapus instansi {{ $admin->kategori_instansi }}? \n\nPERINGATAN: Semua data About Setting untuk instansi ini juga akan terhapus.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="p-2 bg-white border border-red-100 text-red-600 rounded-lg hover:bg-red-50 transition shadow-sm"
                                                    title="Hapus Permanen">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
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
                                            <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                                        </div>
                                        <p class="text-gray-500 font-medium">Belum ada instansi yang didaftarkan.</p>
                                        <p class="text-gray-400 text-sm mt-1">Silakan klik tombol tambah di atas.</p>
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
                {{ $agencies->links() }}
            </div>

            <div class="mt-6 text-center">
                <p class="text-xs text-gray-400">&copy; Sistem Master Admin</p>
            </div>

        </div>
    </div>
</x-app-layout>
