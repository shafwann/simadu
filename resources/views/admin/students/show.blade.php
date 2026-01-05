<x-app-layout>
    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto px-6 lg:px-8 space-y-8">

            {{-- Header Mahasiswa --}}
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 pb-6 border-b border-gray-200">
                <div>
                    <div class="flex items-center gap-3">
                        <h1 class="text-3xl font-black text-gray-900">{{ $student->name }}</h1>
                        @if($student->status === 'aktif')
                            <span class="px-3 py-1 bg-green-100 text-green-700 text-xs font-bold rounded-full uppercase">Aktif Magang</span>
                        @else
                            <span class="px-3 py-1 bg-gray-100 text-gray-600 text-xs font-bold rounded-full uppercase">Selesai / Nonaktif</span>
                        @endif
                    </div>
                    <p class="text-gray-500 mt-1">{{ $student->email }}</p>
                </div>

                <div class="flex gap-3">
                    <form action="{{ route('admin.students.updateStatus', $student->id) }}" method="POST">
                        @csrf @method('PATCH')
                        <button type="submit" class="px-5 py-2.5 rounded-xl font-bold text-sm transition shadow-lg flex items-center gap-2 {{ $student->status === 'aktif' ? 'bg-red-50 text-red-600 hover:bg-red-100 border border-red-100' : 'bg-green-50 text-green-600 hover:bg-green-100 border border-green-100' }}">
                            @if($student->status === 'aktif')
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path></svg>
                                Tandai Selesai Magang
                            @else
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                Aktifkan Kembali
                            @endif
                        </button>
                    </form>

                    <a href="{{ route('admin.students.index') }}" class="px-5 py-2.5 bg-gray-100 text-gray-600 rounded-xl font-bold text-sm hover:bg-gray-200 transition">
                        Kembali
                    </a>
                </div>
            </div>

            {{-- Grid Utama: Kiri Absensi, Kanan Laporan & Presensi/Logbook --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

                {{-- KIRI: Absensi Manual --}}
                <div class="space-y-6">
                    <div class="bg-white border border-blue-100 rounded-[2rem] p-6 shadow-xl shadow-blue-50/50">
                        <h3 class="text-lg font-black text-gray-800 mb-4 flex items-center gap-2">
                            <span class="w-2 h-6 bg-blue-600 rounded-full"></span>
                            Input Absensi Manual
                        </h3>
                        <p class="text-xs text-gray-400 mb-6">Gunakan ini jika mahasiswa lupa absen, sakit, atau izin.</p>

                        <form action="{{ route('admin.attendance.store') }}" method="POST" class="space-y-4">
                            @csrf
                            <input type="hidden" name="user_id" value="{{ $student->id }}">
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Tanggal</label>
                                <input type="date" name="tanggal" value="{{ date('Y-m-d') }}" class="w-full rounded-xl border-gray-200 bg-gray-50 focus:ring-blue-500 focus:border-blue-500 font-bold text-gray-800">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Jam</label>
                                <input type="time" name="jam" value="08:00" class="w-full rounded-xl border-gray-200 bg-gray-50 focus:ring-blue-500 focus:border-blue-500 font-bold text-gray-800">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Tipe Absensi (Otomatis)</label>
                                <select name="tipe_absensi" class="w-full rounded-xl border-gray-200 bg-gray-50 focus:ring-blue-500 focus:border-blue-500 font-bold text-gray-800">
                                    <option value="check_in">⬇️ Check IN (Set ke 07:30)</option>
                                    <option value="check_out">⬆️ Check OUT (Set ke 15:00)</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Status Kehadiran</label>
                                <select name="status" class="w-full rounded-xl border-gray-200 bg-gray-50 focus:ring-blue-500 focus:border-blue-500 font-bold text-gray-800">
                                    <option value="Hadir">✅ Hadir (WFO/WFH)</option>
                                    <option value="Sakit">💊 Sakit</option>
                                    <option value="Izin">✉️ Izin</option>
                                    <option value="Alpha">❌ Alpha (Tanpa Ket.)</option>
                                </select>
                            </div>
                            <button type="submit" class="w-full py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl transition shadow-lg shadow-blue-200">
                                Simpan Data Absen
                            </button>
                        </form>
                    </div>
                </div>

                {{-- KANAN: Riwayat Laporan, Presensi & Logbook --}}
                <div class="lg:col-span-2 space-y-6">

                    {{-- Riwayat Laporan --}}
 <div class="bg-white border border-gray-100 rounded-[2rem] p-6 shadow-sm overflow-hidden">
    <h3 class="text-lg font-black text-gray-800 mb-4 flex items-center gap-2">
        <span class="w-2 h-6 bg-purple-600 rounded-full"></span>
        Riwayat Laporan Mahasiswa
    </h3>

    <div class="overflow-y-auto max-h-[250px] mb-4">
        <table class="w-full text-sm text-left">
            <thead class="bg-gray-50 sticky top-0">
                <tr>
                    <th class="p-4 font-bold text-gray-500">Nama File</th>
                    <th class="p-4 font-bold text-gray-500">Tanggal Upload</th>
                    <th class="p-4 font-bold text-center text-gray-500">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($student->laporans as $lap)
                <tr class="hover:bg-purple-50/50 transition">
                    <td class="p-4 font-medium">{{ $lap->nama_file }}</td>
                    <td class="p-4 text-gray-500">{{ \Carbon\Carbon::parse($lap->created_at)->format('d M Y') }}</td>
                    <td class="p-4 text-center flex justify-center gap-2">
                        {{-- Tombol Preview --}}
                        <button onclick="document.getElementById('preview-iframe').src='{{ route('laporan.preview', $lap->id) }}'" 
                            class="px-3 py-1 bg-gray-100 text-gray-600 text-xs font-bold rounded-lg hover:bg-gray-200 transition">
                            Preview
                        </button>

                        {{-- Download --}}
                        <a href="{{ route('laporan.download', $lap->id) }}" 
                           class="px-3 py-1 bg-purple-600 text-white text-xs font-bold rounded-lg hover:bg-purple-700 transition">
                            Download
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="3" class="p-6 text-center italic text-gray-400">Belum ada laporan.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- IFRAME Preview --}}
    <div class="w-full h-[500px] border border-gray-200 rounded-xl overflow-hidden">
        <iframe id="preview-iframe" class="w-full h-full" src="" frameborder="0"></iframe>
    </div>
</div>



                    {{-- Riwayat Presensi --}}
                    <div class="bg-white border border-gray-100 rounded-[2rem] shadow-sm overflow-hidden">
                        <div class="p-6 border-b border-gray-50 bg-gray-50/30">
                            <h3 class="font-bold text-lg text-gray-800">Riwayat Presensi</h3>
                        </div>
                        <div class="overflow-y-auto max-h-[400px]">
                            <table class="w-full text-sm text-left">
                                <thead class="bg-gray-50 sticky top-0">
                                    <tr>
                                        <th class="p-4 font-bold text-gray-500">Tanggal</th>
                                        <th class="p-4 font-bold text-gray-500">Jam</th>
                                        <th class="p-4 font-bold text-gray-500">Status</th>
                                        <th class="p-4 font-bold text-gray-500">Lokasi</th>
                                        <th class="p-4 font-bold text-center text-gray-500">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-50">
                                    @forelse($student->attendances()->orderBy('tanggal', 'desc')->get() as $a)
                                    <tr class="hover:bg-blue-50/50 transition">
                                        <td class="p-4 font-medium">{{ \Carbon\Carbon::parse($a->tanggal)->format('d M Y') }}</td>
                                        <td class="p-4 font-mono text-gray-600">{{ $a->check_in }}</td>
                                        <td class="p-4">
                                            @if($a->status == 'Hadir') 
                                                <span class="px-2 py-1 bg-green-100 text-green-700 rounded-md text-xs font-bold">Hadir</span>
                                            @elseif($a->status == 'Telat') 
                                                <span class="px-2 py-1 bg-yellow-100 text-yellow-700 rounded-md text-xs font-bold">Telat</span>
                                            @elseif($a->status == 'Sakit') 
                                                <span class="px-2 py-1 bg-yellow-100 text-yellow-700 rounded-md text-xs font-bold">Sakit</span>
                                            @elseif($a->status == 'Izin') 
                                                <span class="px-2 py-1 bg-blue-100 text-blue-700 rounded-md text-xs font-bold">Izin</span>
                                            @else 
                                                <span class="px-2 py-1 bg-gray-100 text-gray-700 rounded-md text-xs font-bold">Alpha</span>
                                            @endif
                                        </td>
                                        <td class="p-4 text-xs">
                                            @if($a->location)
                                                <a href="https://www.google.com/maps/search/?api=1&query={{ $a->location }}" target="_blank" class="text-blue-500 underline">Lihat Peta</a>
                                            @else
                                                <span class="text-gray-400 italic">Manual Admin</span>
                                            @endif
                                        </td>
                                        <td class="p-4 text-center flex items-center justify-center gap-2">
                                            <a href="{{ route('admin.attendance.edit', $a->id) }}" class="text-blue-400 hover:text-blue-600 transition" title="Edit Absensi">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                            </a>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr><td colspan="5" class="p-6 text-center italic text-gray-400">Belum ada data presensi.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- Riwayat Logbook --}}
                    <div class="bg-white border border-gray-100 rounded-[2rem] shadow-sm overflow-hidden">
                        <div class="p-6 border-b border-gray-50 bg-gray-50/30">
                            <h3 class="font-bold text-lg text-gray-800">Riwayat Logbook</h3>
                        </div>
                        <div class="overflow-y-auto max-h-[300px]">
                            <table class="w-full text-sm">
                                <thead class="bg-gray-50 sticky top-0">
                                    <tr>
                                        <th class="p-4 font-bold text-gray-500">Tanggal</th>
                                        <th class="p-4 font-bold text-gray-500">Kegiatan</th>
                                        <th class="p-4 font-bold text-gray-500">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-50">
                                    @forelse($student->logbooks as $l)
                                    <tr>
                                        <td class="p-4">{{ \Carbon\Carbon::parse($l->tanggal)->format('d M Y') }}</td>
                                        <td class="p-4">{{ $l->kegiatan }}</td>
                                        <td class="p-4">
                                            @if($l->is_validated) <span class="text-green-600 font-bold text-xs">Valid</span>
                                            @else <span class="text-amber-500 font-bold text-xs">Pending</span> @endif
                                        </td>
                                    </tr>
                                    @empty
                                    <tr><td colspan="3" class="p-6 text-center italic text-gray-400">Belum ada data logbook.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>
</x-app-layout>
