<x-app-layout>
    <div class="py-12 max-w-5xl mx-auto space-y-6">
        <h2 class="text-xl font-bold mb-4">Semua Laporan Mahasiswa</h2>
        <div class="bg-white p-6 rounded-2xl border shadow-sm overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="px-4 py-2 border">Mahasiswa</th>
                        <th class="px-4 py-2 border">Nama File</th>
                        <th class="px-4 py-2 border">Tanggal Upload</th>
                        <th class="px-4 py-2 border">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($laporans as $lap)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-2 border">{{ $lap->user->name }}</td>
                        <td class="px-4 py-2 border">{{ $lap->nama_file }}</td>
                        <td class="px-4 py-2 border">{{ $lap->created_at->format('d M Y') }}</td>
                        <td class="px-4 py-2 border">
                            <a href="{{ route('laporan.download', $lap->id) }}" class="text-blue-600 font-semibold">Download</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-4 py-2 text-gray-400 italic">Belum ada laporan</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
