<x-app-layout>
    {{-- Header Slot dihapus agar tampilan menyatu putih bersih --}}

    <div class="py-12 bg-white min-h-screen text-gray-900">
        <div class="max-w-7xl mx-auto px-6 lg:px-8">

            <div class="flex flex-col md:flex-row justify-between items-center mb-10 gap-4">
                <div>
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-50 border border-blue-100 mb-2">
                        <span class="w-2 h-2 rounded-full bg-blue-600"></span>
                        <span class="text-xs font-bold text-blue-700 uppercase tracking-wider">Database Anggota</span>
                    </div>
                    <h1 class="text-3xl font-black text-gray-900 tracking-tight">Daftar Mahasiswa Magang</h1>
                    <p class="text-gray-500 mt-2 text-sm">Kelola akun, reset password, dan hapus data peserta magang di sini.</p>
                </div>

                <a href="{{ route('admin.students.create') }}" class="group flex items-center gap-2 bg-blue-700 text-white px-6 py-3 rounded-2xl font-bold hover:bg-blue-800 transition shadow-lg shadow-blue-100 hover:shadow-blue-200 hover:-translate-y-0.5">
                    <svg class="w-5 h-5 transition-transform group-hover:rotate-90" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Tambah Mahasiswa
                </a>
            </div>

            <div class="bg-white border border-gray-100 rounded-[2rem] shadow-2xl shadow-gray-100/50 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50/50 border-b border-gray-100 text-xs uppercase tracking-widest text-gray-400 font-bold">
                                <th class="px-8 py-6">Nama Lengkap</th>
                                <th class="px-8 py-6">Email Akun</th>
                                <th class="px-8 py-6 text-center">Status & Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($students as $s)
                            <tr class="group hover:bg-blue-50/30 transition duration-200">
                                <td class="px-8 py-5">
                                    <div class="flex items-center gap-4">
                                        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-100 to-blue-50 flex items-center justify-center text-blue-600 font-bold text-sm border border-blue-100">
                                            {{ substr($s->name, 0, 2) }}
                                        </div>
                                        <span class="font-bold text-gray-800 group-hover:text-blue-700 transition">{{ $s->name }}</span>
                                    </div>
                                </td>
                                <td class="px-8 py-5 text-sm text-gray-500 font-medium">
                                    {{ $s->email }}
                                </td>
                                <td class="px-8 py-5">
                                    <div class="flex items-center justify-center gap-3">
                                        <a href="{{ route('admin.students.show', $s->id) }}" class="inline-flex items-center gap-1.5 px-4 py-2 bg-white border border-gray-200 rounded-xl text-xs font-bold text-gray-700 hover:border-blue-300 hover:text-blue-700 hover:bg-blue-50 transition shadow-sm">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                            Detail
                                        </a>

                                        <form action="{{ route('admin.students.destroy', $s->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data {{ $s->name }} beserta seluruh riwayat presensi & logbooknya?');">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2 bg-white border border-red-100 rounded-xl text-xs font-bold text-red-600 hover:bg-red-50 hover:border-red-200 transition shadow-sm">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                Hapus
                                            </button>
                                        </form>
                                    </div>
                                </td>
                                <td class="px-8 py-5">
    <div class="flex items-center justify-between gap-4">

        @if($s->status === 'aktif')
            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                <span class="w-1.5 h-1.5 rounded-full bg-green-600"></span> Aktif
            </span>
        @else
            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                <span class="w-1.5 h-1.5 rounded-full bg-gray-500"></span> Selesai
            </span>
        @endif

        <div class="flex items-center gap-2">
            <a href="{{ route('admin.students.show', $s->id) }}" class="p-2 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100 transition" title="Lihat Detail & Kelola">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
            </a>

            <form action="{{ route('admin.students.destroy', $s->id) }}" method="POST" onsubmit="return confirm('Hapus permanen?');">
                @csrf @method('DELETE')
                <button type="submit" class="p-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition" title="Hapus Permanen">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                </button>
            </form>
        </div>
    </div>
</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="px-8 py-16 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mb-4">
                                            <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                        </div>
                                        <p class="text-gray-500 font-medium">Belum ada mahasiswa terdaftar.</p>
                                        <p class="text-gray-400 text-sm mt-1">Klik tombol tambah di atas untuk memulai.</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mt-6 text-center">
                <p class="text-xs text-gray-400">&copy; Sistem Monitoring Baperlitbang</p>
            </div>

        </div>
    </div>
</x-app-layout>
