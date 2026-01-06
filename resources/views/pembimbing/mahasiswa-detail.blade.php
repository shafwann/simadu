<x-app-layout>
    <div class="py-8 bg-gray-50 min-h-screen">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- HEADER MAHASISWA --}}
            <div class="bg-white p-6 rounded-xl shadow">
                <h2 class="text-xl font-bold text-gray-800">
                    {{ $mahasiswa->name }}
                </h2>
                <p class="text-sm text-gray-600 mt-1">
                    NIM: {{ $mahasiswa->nim }} |
                    Prodi: {{ $mahasiswa->prodi }} |
                    Instansi: {{ $mahasiswa->instansi }}
                </p>
            </div>

            {{-- PRESENSI --}}
            <div class="bg-white p-6 rounded-xl shadow">
                <h3 class="font-semibold text-gray-800 mb-4">
                    Riwayat Presensi
                </h3>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm border border-gray-200">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-3 py-2 border">Tanggal</th>
                                <th class="px-3 py-2 border">Status</th>
                                <th class="px-3 py-2 border">Check In</th>
                                <th class="px-3 py-2 border">Check Out</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($presensi as $p)
                                <tr>
                                    <td class="px-3 py-2 border">{{ $p->tanggal }}</td>
                                    <td class="px-3 py-2 border">{{ $p->status }}</td>
                                    <td class="px-3 py-2 border">{{ $p->check_in }}</td>
                                    <td class="px-3 py-2 border">
                                        {{ $p->check_out ?? '-' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-gray-500">
                                        Belum ada data presensi
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $presensi->links() }}
                </div>
            </div>

            {{-- LOGBOOK --}}
            <div class="bg-white p-6 rounded-xl shadow">
                <h3 class="font-semibold text-gray-800 mb-4">
                    Logbook Kegiatan
                </h3>

                <div class="space-y-3">
                    @forelse ($logbooks as $log)
                        <div class="border rounded-lg p-4">
                            <p class="text-sm text-gray-500">
                                {{ $log->tanggal }}
                            </p>
                            <p class="text-gray-800 mt-1">
                                {{ $log->kegiatan }}
                            </p>
                        </div>
                    @empty
                        <p class="text-gray-500 text-sm">
                            Belum ada logbook
                        </p>
                    @endforelse
                </div>

                <div class="mt-4">
                    {{ $logbooks->links() }}
                </div>
            </div>

            {{-- BACK --}}
            <div>
                <a href="{{ route('pembimbing.dashboard') }}"
                   class="text-blue-600 hover:underline text-sm">
                    ← Kembali ke Dashboard
                </a>
            </div>

        </div>
    </div>
</x-app-layout>
