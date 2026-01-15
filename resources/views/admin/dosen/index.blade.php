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

            {{-- TABEL APPROVAL DOSEN (PENDING) --}}
            {{-- =============================== --}}
            @if(isset($pendingDosens))
                @if($pendingDosens->count())
                    <div class="bg-white border border-yellow-100 rounded-[2rem] shadow-xl shadow-yellow-100/40 overflow-hidden mb-10">

                        <div class="px-6 py-5 border-b border-yellow-100 bg-yellow-50/40">
                            <div class="flex items-center gap-2">
                                <span class="w-2 h-2 bg-yellow-500 rounded-full"></span>
                                <h2 class="text-lg font-black text-yellow-800 tracking-tight">
                                    Pengajuan Dosen dari Mahasiswa
                                </h2>
                            </div>
                            <p class="text-sm text-yellow-700 mt-1">
                                Menunggu persetujuan admin
                            </p>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse">

                                <thead>
                                    <tr class="bg-yellow-50 text-xs uppercase tracking-widest text-yellow-700 font-bold">
                                        <th class="px-6 py-4">Nama Dosen</th>
                                        <th class="px-6 py-4">NIDN</th>
                                        <th class="px-6 py-4">Instansi</th>
                                        <th class="px-6 py-4 text-center">Aksi</th>
                                    </tr>
                                </thead>

                                <tbody class="divide-y divide-yellow-100">
                                    @foreach($pendingDosens as $d)
                                    <tr>

                                        <td class="px-6 py-4 font-semibold text-gray-800">
                                            {{ $d->nama_dosen  }}
                                        </td>

                                        <td class="px-6 py-4 text-sm text-gray-600">
                                            {{ $d->nidn }}
                                        </td>

                                        <td class="px-6 py-4 text-sm text-gray-600">
                                            {{ $d->instansi }}
                                        </td>

                                        {{-- 🔥 INI TEMPAT APPROVE / REJECT --}}
                                        <td class="px-6 py-4 text-center">
                                            <div class="flex justify-center gap-2">

                                                {{-- APPROVE --}}
                                                <form action="{{ route('admin.pembimbing-requests.approve', $d->id) }}" method="POST">
                                                    @csrf
                                                    <button type="submit"
                                                        class="px-3 py-1.5 bg-green-600 text-white text-xs font-bold rounded-lg hover:bg-green-700">
                                                        Approve
                                                    </button>
                                                </form>

                                                {{-- REJECT --}}
                                                <form action="{{ route('admin.pembimbing-requests.reject', $d->id) }}" method="POST"
                                                    onsubmit="return confirm('Tolak pengajuan dosen ini?')">
                                                    @csrf
                                                    <button type="submit"
                                                        class="px-3 py-1.5 bg-red-600 text-white text-xs font-bold rounded-lg hover:bg-red-700">
                                                        Reject
                                                    </button>
                                                </form>

                                            </div>
                                        </td>

                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>

                        </div>

                    </div>
                @else
                    <div class="mb-10 p-6 text-sm text-gray-500 bg-yellow-50 rounded-xl">
                        Belum ada pengajuan dosen dari mahasiswa.
                    </div>
                @endif
            @endif



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
                                        {{ $d->instansi ?? $d->mahasiswaBimbingan->first()?->instansi ?? '-' }}
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
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-center gap-2">

                                        {{-- LIHAT MAHASISWA (ICON MATA) --}}
                                        <a href="{{ route('admin.dosen.mahasiswa', $d->id) }}"
                                        class="p-2 bg-white border border-blue-100 text-blue-600 rounded-lg
                                                hover:bg-blue-50 transition shadow-sm"
                                        title="Lihat Mahasiswa Bimbingan">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5
                                                        c4.478 0 8.268 2.943 9.542 7
                                                        -1.274 4.057-5.064 7-9.542 7
                                                        -4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                        </a>

                                        {{-- EDIT --}}
                                        <a href="{{ route('admin.dosen.edit', $d->id) }}"
                                        class="p-2 bg-white border border-yellow-100 text-yellow-600 rounded-lg
                                                hover:bg-yellow-50 transition shadow-sm"
                                        title="Edit Dosen">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11
                                                        a2 2 0 002 2h11a2 2 0 002-2v-5
                                                        m-1.414-9.414a2 2 0 112.828 2.828
                                                        L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                        </a>

                                        {{-- DELETE --}}
                                        <form action="{{ route('admin.dosen.destroy', $d->id) }}"
                                            method="POST"
                                            onsubmit="return confirm('Hapus dosen ini?')">
                                            @csrf @method('DELETE')
                                            <button type="submit"
                                                class="p-2 bg-white border border-red-100 text-red-600 rounded-lg
                                                    hover:bg-red-50 transition shadow-sm"
                                                title="Hapus Dosen">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21
                                                            H7.862a2 2 0 01-1.995-1.858L5 7
                                                            m5 4v6m4-6v6
                                                            m1-10V4a1 1 0 00-1-1h-4
                                                            a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                            </button>
                                        </form>

                                    </div>
                                </td>

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
