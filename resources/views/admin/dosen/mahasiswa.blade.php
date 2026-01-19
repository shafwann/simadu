<x-app-layout>
    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- HEADER --}}
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
                <div>
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full
                                bg-blue-50 border border-blue-100 mb-2">
                        <span class="w-2 h-2 rounded-full bg-blue-600"></span>
                        <span class="text-xs font-bold text-blue-700 uppercase tracking-wider">
                            Mahasiswa Bimbingan oleh Dosen :
                        </span>
                    </div>
                    <h1 class="text-3xl font-black text-gray-900 tracking-tight">
                        {{ $dosen->name }}
                    </h1>
                </div>
            </div>

            {{-- CARD --}}
            <div class="bg-white border border-gray-100 rounded-[2rem]
                        shadow-2xl shadow-gray-100/60 overflow-hidden">

                {{-- INFO DOSEN --}}
                <div class="px-8 py-6 border-b border-gray-100 bg-gray-50/50">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-full bg-gradient-to-br
                                    from-blue-100 to-blue-50 flex items-center
                                    justify-center text-blue-600 font-bold text-lg">
                            {{ substr($dosen->name, 0, 2) }}
                        </div>
                        <div>
                            <div class="font-bold text-gray-900">
                                {{ $dosen->name }}
                            </div>
                            <div class="text-sm text-gray-600">
                                Instansi:
                                <span class="font-semibold">
                                    {{ $dosen->instansi ?? '-' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- TABLE --}}
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50 border-b border-gray-100
                                       text-xs uppercase tracking-widest
                                       text-gray-400 font-bold">
                                <th class="px-6 py-4">No</th>
                                <th class="px-6 py-4">Nama</th>
                                <th class="px-6 py-4">NIM</th>
                                <th class="px-6 py-4">Prodi</th>
                                <th class="px-6 py-4 text-center">Status</th>
                                <th class="px-6 py-4 text-center">Aksi</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-50">
                        @forelse ($mahasiswa as $index => $mhs)
                            <tr class="group hover:bg-blue-50/30 transition">
                                <td class="px-6 py-4 text-sm text-gray-500">
                                    {{ $mahasiswa->firstItem() + $index }}
                                </td>

                                <td class="px-6 py-4">
                                    <div class="font-bold text-gray-900
                                                group-hover:text-blue-700 transition">
                                        {{ $mhs->name }}
                                    </div>
                                </td>

                                <td class="px-6 py-4 text-sm text-gray-700">
                                    {{ $mhs->nim }}
                                </td>

                                <td class="px-6 py-4 text-sm text-gray-700">
                                    {{ $mhs->prodi }}
                                </td>

                                <td class="px-6 py-4 text-center">
                                    @if($mhs->status === 'aktif')
                                        <span class="inline-flex items-center gap-1
                                                     px-2.5 py-0.5 rounded-full
                                                     text-xs font-medium
                                                     bg-green-100 text-green-800">
                                            <span class="w-1.5 h-1.5 rounded-full bg-green-600"></span>
                                            Aktif
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1
                                                     px-2.5 py-0.5 rounded-full
                                                     text-xs font-medium
                                                     bg-gray-100 text-gray-800">
                                            <span class="w-1.5 h-1.5 rounded-full bg-gray-500"></span>
                                            Nonaktif
                                        </span>
                                    @endif
                                </td>

                                <td class="px-6 py-4 text-center">
                                    <a href="{{ route('admin.students.show', $mhs->id) }}"
                                    class="inline-flex items-center gap-1
                                            text-sm font-semibold text-blue-600
                                            hover:text-blue-800 transition">
                                        Detail
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M9 5l7 7-7 7" />
                                        </svg>
                                    </a>
                                </td>

                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-8 py-16 text-center">
                                    <p class="text-gray-400 font-medium">
                                        Tidak ada mahasiswa bimbingan
                                    </p>
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
