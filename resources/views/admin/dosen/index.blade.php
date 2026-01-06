<x-app-layout>
    <div class="py-12 bg-white min-h-screen text-gray-900">
        <div class="max-w-7xl mx-auto px-6 lg:px-8">

            {{-- HEADER SECTION --}}
            <div class="flex flex-col md:flex-row justify-between items-end md:items-center mb-6 gap-4">

                {{-- Kiri: Judul --}}
                <div>
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-50 border border-blue-100 mb-2">
                        <span class="w-2 h-2 rounded-full bg-blue-600"></span>
                        <span class="text-xs font-bold text-blue-700 uppercase tracking-wider">Database Dosen</span>
                    </div>
                    <h1 class="text-3xl font-black text-gray-900 tracking-tight">Daftar Dosen</h1>
                </div>

                {{-- Kanan: Tambah --}}
                <div class="flex flex-col md:flex-row gap-3 w-full md:w-auto">
                    <a href="{{ route('admin.dosen.create') }}" class="group flex items-center justify-center gap-2 bg-blue-700 text-white px-5 py-3 rounded-2xl font-bold hover:bg-blue-800 transition shadow-lg shadow-blue-100 hover:shadow-blue-200 hover:-translate-y-0.5 text-sm">
                        <svg class="w-5 h-5 transition-transform group-hover:rotate-90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Tambah Dosen
                    </a>
                </div>
            </div>

            {{-- TABEL DOSEN --}}
            <div class="bg-white border border-gray-100 rounded-[2rem] shadow-2xl shadow-gray-100/50 overflow-hidden mb-6">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50/50 border-b border-gray-100 text-xs uppercase tracking-widest text-gray-400 font-bold">
                                <th class="px-6 py-5">Dosen</th>
                                <th class="px-6 py-5">Email</th>
                                <th class="px-6 py-5">Instansi</th>
                                <th class="px-6 py-5 text-center">Status</th>
                                <th class="px-6 py-5 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($dosen as $d)
                            <tr class="group hover:bg-blue-50/30 transition duration-200">

                                {{-- Nama Dosen --}}
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-4">
                                        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-100 to-blue-50 flex items-center justify-center text-blue-600 font-bold text-sm border border-blue-100 shrink-0">
                                            {{ substr($d->name, 0, 2) }}
                                        </div>
                                        <div>
                                            <div class="font-bold text-gray-900 group-hover:text-blue-700 transition">{{ $d->name }}</div>
                                        </div>
                                    </div>
                                </td>

                                {{-- Email --}}
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-700">{{ $d->email }}</div>
                                </td>

                                {{-- instansi --}}
                                <td class="px-6 py-4">
                                    <span class="px-3 py-1 bg-purple-50 text-purple-700 rounded-lg text-xs font-bold border border-purple-100 whitespace-nowrap">
                                        {{ $d->instansi }}
                                    </span>
                                </td>

                                {{-- Status --}}
                                <td class="px-6 py-4 text-center">
                                    @if($d->status === 'aktif')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            <span class="w-1.5 h-1.5 rounded-full bg-green-600"></span> Aktif
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                            <span class="w-1.5 h-1.5 rounded-full bg-gray-500"></span> Nonaktif
                                        </span>
                                    @endif
                                </td>

                                {{-- Aksi --}}
                                <td class="px-6 py-4 text-center flex justify-center gap-3">

                                    {{-- Edit Dosen --}}
                                    <a href="{{ route('admin.dosen.edit', $d->id) }}"
                                        class="p-2 text-blue-400 hover:text-blue-600 transition"
                                        title="Edit Dosen & Assign Mahasiswa">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5
                                                m-1.414-9.414a2 2 0 112.828 2.828
                                                L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </a>


                                    {{-- Hapus --}}
                                    <form action="{{ route('admin.dosen.destroy', $d->id) }}" method="POST"
                                        onsubmit="return confirm('Hapus dosen ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="p-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition"
                                            title="Hapus Dosen">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7" />
                                            </svg>
                                        </button>
                                    </form>

                                </td>

                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-8 py-16 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mb-4">
                                            <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                            </svg>
                                        </div>
                                        <p class="text-gray-500 font-medium">Data dosen tidak ditemukan.</p>
                                        <p class="text-gray-400 text-sm mt-1">Tambahkan dosen baru untuk mengisi data.</p>
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
                {{ $dosen->links() }}
            </div>

        </div>
    </div>
</x-app-layout>
