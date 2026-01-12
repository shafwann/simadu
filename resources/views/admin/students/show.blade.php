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
                    {{-- ✅ IMPORTANT: kasih id="statusForm" biar JS gak error --}}
                    <form id="statusForm" action="{{ route('admin.students.updateStatus', $student->id) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <button type="submit"
                            class="px-5 py-2.5 rounded-xl font-bold text-sm transition shadow-lg flex items-center gap-2
                            {{ $student->status === 'aktif'
                                ? 'bg-red-50 text-red-600 hover:bg-red-100 border border-red-100'
                                : 'bg-green-50 text-green-600 hover:bg-green-100 border border-green-100' }}">
                            @if($student->status === 'aktif')
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path>
                            </svg>
                            Tandai Selesai Magang
                            @else
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7"></path>
                            </svg>
                            Aktifkan Kembali
                            @endif
                        </button>
                    </form>

                    <a href="{{ route('admin.students.index') }}"
                        class="px-5 py-2.5 bg-gray-100 text-gray-600 rounded-xl font-bold text-sm hover:bg-gray-200 transition">
                        Kembali
                    </a>
                </div>
            </div>

            {{-- Grid Utama --}}
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

                            {{-- Tanggal --}}
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Tanggal</label>
                                <input type="date" name="tanggal" value="{{ date('Y-m-d') }}"
                                    class="w-full rounded-xl border-gray-200 bg-gray-50 focus:ring-blue-500 focus:border-blue-500 font-bold text-gray-800">
                            </div>

                            {{-- Jam --}}
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Jam</label>
                                <input type="time" name="jam" value="08:00"
                                    class="w-full rounded-xl border-gray-200 bg-gray-50 focus:ring-blue-500 focus:border-blue-500 font-bold text-gray-800">
                            </div>

                            {{-- Tipe Absensi --}}
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Tipe Absensi</label>
                                <select name="tipe_absensi"
                                    class="w-full rounded-xl border-gray-200 bg-gray-50 focus:ring-blue-500 focus:border-blue-500 font-bold text-gray-800">
                                    <option value="check_in">⬇️ Check IN</option>
                                    <option value="check_out">⬆️ Check OUT</option>
                                </select>
                            </div>

                            {{-- Status Kehadiran --}}
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Status Kehadiran</label>
                                <select name="status"
                                    class="w-full rounded-xl border-gray-200 bg-gray-50 focus:ring-blue-500 focus:border-blue-500 font-bold text-gray-800">
                                    <option value="Hadir">✅ Hadir</option>
                                    <option value="Telat">⏰ Telat</option>
                                    <option value="Ijin">✉️ Ijin (I)</option>
                                    <option value="Sakit">💊 Sakit (S)</option>
                                    <option value="Dinas Luar">🚗 Dinas Luar (DL)</option>
                                    <option value="Cuti">🏖️ Cuti (CT)</option>
                                    <option value="Tanpa Keterangan">❌ Tanpa Keterangan (TK)</option>
                                </select>
                            </div>


                            {{-- Link Maps --}}
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Link Google Maps</label>
                                <input type="text" name="location" placeholder="https://goo.gl/maps/..."
                                    class="w-full rounded-xl border-gray-200 bg-gray-50 focus:ring-blue-500 focus:border-blue-500 font-bold text-gray-800 text-sm">
                                <p class="text-[10px] text-gray-400 mt-1">*Paste link 'Share' atau 'Embed' dari Google Maps di sini.</p>
                            </div>

                            {{-- ✅ TAMBAHAN BARU: INPUT NAMA LOKASI --}}
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">
                                    Nama Lokasi (Untuk Tampilan PDF)
                                </label>
                                <input type="text" name="location_name" placeholder="Contoh: KANTOR BAPPERIDA / DINAS LUAR / RUMAH"
                                    class="w-full rounded-xl border-gray-200 bg-gray-50 focus:ring-blue-500 focus:border-blue-500 font-bold text-gray-800 text-sm uppercase">
                                <p class="text-[10px] text-gray-400 mt-1">
                                    *Teks ini yang akan muncul di kolom "LOKASI" pada cetak PDF.
                                </p>
                            </div>

                            <button type="submit"
                                class="w-full py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl transition shadow-lg shadow-blue-200">
                                Simpan Data Absen
                            </button>
                        </form>
                    </div>
                </div>

                {{-- KANAN: Riwayat --}}
                <div class="lg:col-span-2 space-y-6">

                    {{-- Riwayat Penilaian --}}
                    <div class="bg-white border border-gray-100 rounded-[2rem] shadow-sm overflow-hidden">
                        <div class="p-6 border-b border-gray-50 bg-gray-50/30 flex items-center justify-between">
                            <h3 class="font-bold text-lg text-gray-800">Riwayat Penilaian Magang</h3>

                            @if($student->assessment)
                            <div class="flex items-center gap-2">
                                <button type="button" id="btnEditAssessmentTop"
                                    class="px-3 py-2 rounded-xl bg-amber-500 text-white font-bold text-sm hover:bg-amber-600 transition">
                                    Edit
                                </button>

                                <form action="{{ route('admin.assessment.destroy', $student->assessment->id) }}"
                                    method="POST" onsubmit="return confirm('Yakin ingin menghapus penilaian ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="px-3 py-2 rounded-xl bg-red-600 text-white font-bold text-sm hover:bg-red-700 transition">
                                        Hapus
                                    </button>
                                </form>

                                <a href="{{ route('admin.assessment.certificate', $student->id) }}"
                                    class="px-4 py-2 rounded-xl bg-blue-600 text-white font-bold text-sm hover:bg-blue-700 transition">
                                    Download
                                </a>
                            </div>
                            @endif
                        </div>

                        <div class="p-6">
                            @if($student->assessment)
                            @php
                            $total = (int) $student->assessment->total;
                            $rata = (float) $student->assessment->average;
                            $grade = (string) $student->assessment->grade_letter;
                            @endphp

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div class="bg-gray-50 rounded-2xl p-4 border">
                                    <p class="text-xs uppercase font-bold text-gray-400">Nomor Sertifikat</p>
                                    <p class="text-lg font-black text-gray-900 mt-1">{{ $student->assessment->certificate_no }}</p>
                                </div>
                                <div class="bg-gray-50 rounded-2xl p-4 border">
                                    <p class="text-xs uppercase font-bold text-gray-400">Total</p>
                                    <p class="text-lg font-black text-gray-900 mt-1">{{ $total }}</p>
                                </div>
                                <div class="bg-blue-50 rounded-2xl p-4 border border-blue-100">
                                    <p class="text-xs uppercase font-bold text-blue-400">Rata-rata / Nilai Huruf</p>
                                    <p class="text-lg font-black text-blue-700 mt-1">{{ number_format($rata, 2, ',', '.') }} / {{ $grade }}</p>
                                </div>
                            </div>

                            <div class="mt-6 overflow-x-auto">
                                <table class="min-w-full text-sm">
                                    <thead class="bg-gray-50 text-gray-600 uppercase text-xs">
                                        <tr>
                                            <th class="p-4 text-left">No</th>
                                            <th class="p-4 text-left">Aspek Penilaian</th>
                                            <th class="p-4 text-center">Nilai</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-100">
                                        <tr>
                                            <td class="p-4">1</td>
                                            <td class="p-4">Etika dan Kedisiplinan</td>
                                            <td class="p-4 text-center font-bold">{{ $student->assessment->aspek_1 }}</td>
                                        </tr>
                                        <tr>
                                            <td class="p-4">2</td>
                                            <td class="p-4">Kemampuan Berkomunikasi dan Bekerjasama</td>
                                            <td class="p-4 text-center font-bold">{{ $student->assessment->aspek_2 }}</td>
                                        </tr>
                                        <tr>
                                            <td class="p-4">3</td>
                                            <td class="p-4">Kemandirian</td>
                                            <td class="p-4 text-center font-bold">{{ $student->assessment->aspek_3 }}</td>
                                        </tr>
                                        <tr>
                                            <td class="p-4">4</td>
                                            <td class="p-4">Kreativitas</td>
                                            <td class="p-4 text-center font-bold">{{ $student->assessment->aspek_4 }}</td>
                                        </tr>
                                        <tr>
                                            <td class="p-4">5</td>
                                            <td class="p-4">Kemampuan menyelesaikan pekerjaan di bidangnya</td>
                                            <td class="p-4 text-center font-bold">{{ $student->assessment->aspek_5 }}</td>
                                        </tr>
                                        <tr>
                                            <td class="p-4">6</td>
                                            <td class="p-4">Kemampuan mengidentifikasi dan memecahkan masalah</td>
                                            <td class="p-4 text-center font-bold">{{ $student->assessment->aspek_6 }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            @else
                            <p class="text-gray-500 italic">
                                Belum ada penilaian. Klik <b>Tandai Selesai Magang</b> untuk mengisi penilaian.
                            </p>
                            @endif
                        </div>
                    </div>

                    {{-- Riwayat Presensi --}}
                    <div class="bg-white border border-gray-100 rounded-[2rem] shadow-sm overflow-hidden">
                        <div class="p-6 border-b border-gray-50 bg-gray-50/30 flex items-center justify-between">
                            <h3 class="font-bold text-lg text-gray-800">Riwayat Presensi</h3>
                            <button type="button" id="btnDownloadPresensi"
                                class="px-4 py-2 rounded-xl bg-purple-600 text-white font-bold text-sm hover:bg-purple-700 transition">
                                Download PDF
                            </button>
                        </div>

                        <div class="overflow-y-auto max-h-[400px]">
                            <table class="w-full text-sm text-left">
                                <thead class="bg-gray-50 sticky top-0">
                                    <tr>
                                        <th class="p-4 font-bold text-gray-500">Tanggal</th>
                                        <th class="p-4 font-bold text-gray-500">Jam</th>
                                        <th class="p-4 font-bold text-gray-500">Status</th>
                                        <th class="p-4 font-bold text-gray-500">Lokasi / Map</th>
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
                                            @elseif($a->status == 'Ijin' || $a->status == 'Izin')
                                            <span class="px-2 py-1 bg-blue-100 text-blue-700 rounded-md text-xs font-bold">Ijin</span>
                                            @elseif($a->status == 'Dinas Luar')
                                            <span class="px-2 py-1 bg-purple-100 text-purple-700 rounded-md text-xs font-bold">Dinas Luar</span>
                                            @elseif($a->status == 'Cuti')
                                            <span class="px-2 py-1 bg-pink-100 text-pink-700 rounded-md text-xs font-bold">Cuti</span>
                                            @elseif($a->status == 'Tanpa Keterangan' || $a->status == 'Alpha')
                                            <span class="px-2 py-1 bg-gray-100 text-gray-700 rounded-md text-xs font-bold">TK</span>
                                            @else
                                            <span class="px-2 py-1 bg-gray-100 text-gray-700 rounded-md text-xs font-bold">{{ $a->status }}</span>
                                            @endif

                                        </td>

                                        <td class="p-4 text-xs">
                                            @if($a->location)
                                            <div class="flex flex-col gap-1">
                                                <a href="{{ $a->location }}" target="_blank"
                                                    class="flex items-center gap-1 text-blue-600 font-bold hover:underline">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                    </svg>
                                                    Buka Link Peta
                                                </a>
                                                <span class="text-[10px] text-gray-400 truncate max-w-[160px]" title="{{ $a->location }}">
                                                    {{ $a->location }}
                                                </span>
                                            </div>
                                            @else
                                            <span class="text-gray-400 italic text-[10px]">Lokasi Kosong</span>
                                            @endif
                                        </td>

                                        <td class="p-4 text-center flex items-center justify-center gap-2">
                                            <a href="{{ route('admin.attendance.edit', $a->id) }}"
                                                class="text-blue-400 hover:text-blue-600 transition" title="Edit Absensi">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                </svg>
                                            </a>
                                            <form action="{{ route('admin.attendance.destroy', $a->id) }}" method="POST"
                                                onsubmit="return confirm('Hapus data presensi ini?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-400 hover:text-red-600 transition" title="Hapus">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                    </svg>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="5" class="p-6 text-center italic text-gray-400">Belum ada data presensi.</td>
                                    </tr>
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
                                            @if($l->is_validated)
                                            <span class="text-green-600 font-bold text-xs">Valid</span>
                                            @else
                                            <span class="text-amber-500 font-bold text-xs">Pending</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="3" class="p-6 text-center italic text-gray-400">Belum ada data logbook.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

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
                                            <button type="button"
                                                onclick="document.getElementById('preview-iframe').src='{{ route('laporan.preview', $lap->id) }}'"
                                                class="px-3 py-1 bg-gray-100 text-gray-600 text-xs font-bold rounded-lg hover:bg-gray-200 transition">
                                                Preview
                                            </button>
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

                        <div class="w-full h-[500px] border border-gray-200 rounded-xl overflow-hidden">
                            <iframe id="preview-iframe" class="w-full h-full" src="" frameborder="0"></iframe>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    {{-- ✅ MODAL PENILAIAN (POPUP KECIL + 2 KOLOM + BACKDROP HITAM) --}}
    <div id="assessmentModal"
        data-has-assessment="{{ $student->assessment ? '1' : '0' }}"
        data-update-url="{{ $student->assessment ? route('admin.assessment.update', $student->assessment->id) : '' }}"
        data-store-url="{{ route('admin.assessment.store', $student->id) }}"
        data-student-status="{{ $student->status }}"
        class="fixed inset-0 z-50 hidden">

        {{-- Backdrop --}}
        <div class="absolute inset-0 bg-black/50" data-backdrop></div>

        {{-- Wrapper --}}
        <div class="relative min-h-full w-full flex items-center justify-center p-4">
            {{-- Panel (kecil seperti gambar) --}}
            <div class="w-full max-w-lg bg-white rounded-3xl shadow-2xl border border-gray-100 overflow-hidden
                    max-h-[90vh] flex flex-col">

                {{-- Header (dibuat lebih rapet) --}}
                <div class="p-5 border-b border-gray-100 relative">
                    <button id="closeModal" type="button"
                        class="absolute top-4 right-4 w-10 h-10 rounded-xl flex items-center justify-center
                               text-gray-400 hover:text-gray-700 hover:bg-gray-100 transition">
                        <span class="text-2xl leading-none">&times;</span>
                    </button>

                    <h2 class="text-xl font-black text-gray-900 text-center">
                        Penilaian Akhir Magang
                    </h2>
                    <p class="text-sm text-gray-500 mt-1">
                        Mahasiswa: <span class="font-bold text-gray-700">{{ $student->name }}</span>
                    </p>

                    <div class="mt-3 flex flex-wrap gap-2">
                        <span class="px-3 py-1 rounded-full text-xs font-bold bg-blue-100 text-blue-700">
                            Form Penilaian
                        </span>
                        @if($student->assessment)
                        <span class="px-3 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-700">
                            Edit
                        </span>
                        @else
                        <span class="px-3 py-1 rounded-full text-xs font-bold bg-green-100 text-green-700">
                            Input
                        </span>
                        @endif
                    </div>
                </div>

                {{-- Body (scrollable) -> FIX JARAK BESAR DI ATAS --}}
                <div class="p-5 pt-1 overflow-y-auto">
                    <form id="assessmentForm"
                        method="POST"
                        action="{{ route('admin.assessment.store', $student->id) }}"
                        class="space-y-5">
                        @csrf
                        <input type="hidden" id="methodSpoof" name="_method" value="POST">

                        {{-- ✅ Nomor Sertifikat (FULL 1 baris) --}}
                        <div class="rounded-2xl border border-gray-100 bg-white p-4 mt-0">
                            <label class="block text-[11px] font-black text-gray-500 uppercase tracking-wide">
                                Nomor Sertifikat
                            </label>
                            <input id="inputNomor"
                                type="text"
                                name="certificate_no"
                                required
                                value="{{ old('certificate_no', optional($student->assessment)->certificate_no) }}"
                                class="mt-2 w-full rounded-2xl border-gray-200 bg-gray-50
                                      focus:ring-blue-500 focus:border-blue-500 font-bold text-gray-800">
                            <p class="mt-2 text-[11px] text-gray-400">
                                *Pastikan format sertifikat sesuai instansi.
                            </p>
                        </div>

                        {{-- ✅ Aspek Penilaian (jarak konsisten dari Nomor Sertifikat) --}}
                        <div class="rounded-2xl border border-gray-100 bg-white p-4 mt-0">
                            <div class="mb-3">
                                <h3 class="text-base font-black text-gray-900">Aspek Penilaian</h3>
                                <p class="text-xs text-gray-500">Isi nilai 0–100 untuk tiap aspek.</p>
                            </div>

                            {{-- INI YANG BIKIN 2 KOLOM --}}
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                                {{-- 1 --}}
                                <div class="rounded-1xl border border-gray-100 bg-white p-4">
                                    <p class="text-[11px] font-black text-gray-500 uppercase tracking-wide">
                                        1. Etika & Kedisiplinan
                                    </p>
                                    <input id="input1"
                                        type="number"
                                        name="aspek_1"
                                        min="0"
                                        max="100"
                                        required
                                        value="{{ old('aspek_1', optional($student->assessment)->aspek_1) }}"
                                        class="mt-2 w-full rounded-1xl border-gray-200 bg-gray-50
                                              focus:ring-blue-500 focus:border-blue-500 font-black text-gray-900">
                                </div>

                                {{-- 2 --}}
                                <div class="rounded-1xl border border-gray-100 bg-white p-4">
                                    <p class="text-[11px] font-black text-gray-500 uppercase tracking-wide">
                                        2. Komunikasi & Kerjasama
                                    </p>
                                    <input id="input2"
                                        type="number"
                                        name="aspek_2"
                                        min="0"
                                        max="100"
                                        required
                                        value="{{ old('aspek_2', optional($student->assessment)->aspek_2) }}"
                                        class="mt-2 w-full rounded-1xl border-gray-200 bg-gray-50
                                              focus:ring-blue-500 focus:border-blue-500 font-black text-gray-900">
                                </div>

                                {{-- 3 --}}
                                <div class="rounded-1xl border border-gray-100 bg-white p-4">
                                    <p class="text-[11px] font-black text-gray-500 uppercase tracking-wide">
                                        3. Kemandirian
                                    </p>
                                    <input id="input3"
                                        type="number"
                                        name="aspek_3"
                                        min="0"
                                        max="100"
                                        required
                                        value="{{ old('aspek_3', optional($student->assessment)->aspek_3) }}"
                                        class="mt-2 w-full rounded-1xl border-gray-200 bg-gray-50
                                              focus:ring-blue-500 focus:border-blue-500 font-black text-gray-900">
                                </div>

                                {{-- 4 --}}
                                <div class="rounded-1xl border border-gray-100 bg-white p-4">
                                    <p class="text-[11px] font-black text-gray-500 uppercase tracking-wide">
                                        4. Kreativitas
                                    </p>
                                    <input id="input4"
                                        type="number"
                                        name="aspek_4"
                                        min="0"
                                        max="100"
                                        required
                                        value="{{ old('aspek_4', optional($student->assessment)->aspek_4) }}"
                                        class="mt-2 w-full rounded-1xl border-gray-200 bg-gray-50
                                              focus:ring-blue-500 focus:border-blue-500 font-black text-gray-900">
                                </div>

                                {{-- 5 --}}
                                <div class="rounded-1xl border border-gray-100 bg-white p-4">
                                    <p class="text-[11px] font-black text-gray-500 uppercase tracking-wide">
                                        5. Penyelesaian Pekerjaan
                                    </p>
                                    <input id="input5"
                                        type="number"
                                        name="aspek_5"
                                        min="0"
                                        max="100"
                                        required
                                        value="{{ old('aspek_5', optional($student->assessment)->aspek_5) }}"
                                        class="mt-2 w-full rounded-1xl border-gray-200 bg-gray-50
                                              focus:ring-blue-500 focus:border-blue-500 font-black text-gray-900">
                                </div>

                                {{-- 6 --}}
                                <div class="rounded-1xl border border-gray-100 bg-white p-4">
                                    <p class="text-[11px] font-black text-gray-500 uppercase tracking-wide">
                                        6. Problem Solving
                                    </p>
                                    <input id="input6"
                                        type="number"
                                        name="aspek_6"
                                        min="0"
                                        max="100"
                                        required
                                        value="{{ old('aspek_6', optional($student->assessment)->aspek_6) }}"
                                        class="mt-2 w-full rounded-1xl border-gray-200 bg-gray-50
                                              focus:ring-blue-500 focus:border-blue-500 font-black text-gray-900">
                                </div>

                            </div>
                        </div>

                        {{-- Button --}}
                        <div class="sticky bottom-0 bg-white pt-3">
                            <button id="assessmentSubmitBtn"
                                type="submit"
                                class="w-full py-3 rounded-2xl bg-blue-600 hover:bg-blue-700
                                       text-white font-black transition shadow-lg shadow-blue-200">
                                Simpan Penilaian
                            </button>
                        </div>

                    </form>
                </div>

            </div>
        </div>
    </div>


    {{-- ✅ SCRIPT MODAL (FIX: klik status -> modal muncul) --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const assessmentModal = document.getElementById('assessmentModal');
            if (!assessmentModal) return;

            const hasAssessment = assessmentModal.dataset.hasAssessment === "1";
            const updateUrl = assessmentModal.dataset.updateUrl || "";
            const storeUrl = assessmentModal.dataset.storeUrl || "";
            const studentStatus = assessmentModal.dataset.studentStatus || "";

            const backdrop = assessmentModal.querySelector('[data-backdrop]');
            const closeBtn = document.getElementById('closeModal');

            const statusForm = document.getElementById('statusForm');
            const statusButton = statusForm ? statusForm.querySelector('button[type="submit"]') : null;

            const btnEditAssessmentTop = document.getElementById('btnEditAssessmentTop');

            const assessmentForm = document.getElementById('assessmentForm');
            const methodSpoof = document.getElementById('methodSpoof');
            const submitBtn = document.getElementById('assessmentSubmitBtn');

            function openAssessment(mode) {
                assessmentModal.classList.remove('hidden');
                document.body.classList.add('overflow-hidden');

                if (mode === 'edit' && updateUrl) {
                    assessmentForm.action = updateUrl;
                    methodSpoof.value = "PATCH";
                    submitBtn.textContent = "Simpan Perubahan";
                } else {
                    assessmentForm.action = storeUrl;
                    methodSpoof.value = "POST";
                    submitBtn.textContent = "Simpan Penilaian";
                }
            }

            function closeAssessment() {
                assessmentModal.classList.add('hidden');
                document.body.classList.remove('overflow-hidden');
            }

            // Klik "Tandai Selesai Magang" (hanya jika status aktif)
            if (statusButton && studentStatus === "aktif") {
                statusButton.addEventListener('click', function(e) {
                    e.preventDefault();
                    openAssessment(hasAssessment ? 'edit' : 'create');
                });
            }

            // Klik tombol Edit pada Riwayat Penilaian
            if (btnEditAssessmentTop) {
                btnEditAssessmentTop.addEventListener('click', function(e) {
                    e.preventDefault();
                    openAssessment('edit');
                });
            }

            // Close
            if (closeBtn) closeBtn.addEventListener('click', closeAssessment);
            if (backdrop) backdrop.addEventListener('click', closeAssessment);

            // ESC
            window.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && !assessmentModal.classList.contains('hidden')) {
                    closeAssessment();
                }
            });
        });
    </script>

    {{-- MODAL DOWNLOAD PRESENSI --}}
    <div id="downloadPresensiModal" class="fixed inset-0 z-50 hidden">
        <div class="absolute inset-0 bg-black/50" data-backdrop></div>

        <div class="relative min-h-full flex items-center justify-center p-4">
            <div class="w-full max-w-md bg-white rounded-3xl shadow-2xl border border-gray-100 overflow-hidden">
                <div class="p-5 border-b border-gray-100 relative">
                    <button id="closeDownloadModal" type="button"
                        class="absolute top-4 right-4 w-10 h-10 rounded-xl flex items-center justify-center
                           text-gray-400 hover:text-gray-700 hover:bg-gray-100 transition">
                        <span class="text-2xl leading-none">&times;</span>
                    </button>
                    <h2 class="text-xl font-black text-gray-900 text-center">Download Presensi (PDF)</h2>
                    <p class="text-sm text-gray-500 mt-1">Pilih bulan & tahun yang ingin dicetak.</p>
                </div>

                <form class="p-5 space-y-4"
                    method="GET"
                    action="{{ route('admin.students.presensi.pdf', $student->id) }}">

                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Bulan</label>
                        <select name="month" class="w-full rounded-xl border-gray-200 bg-gray-50 font-bold">
                            @for($m=1; $m<=12; $m++)
                                <option value="{{ $m }}" {{ $m==now()->month ? 'selected' : '' }}>
                                {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                                </option>
                                @endfor
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Tahun</label>
                        <input type="number" name="year" value="{{ now()->year }}"
                            class="w-full rounded-xl border-gray-200 bg-gray-50 font-bold">
                    </div>

                    <button type="submit"
                        class="w-full py-3 rounded-2xl bg-purple-600 hover:bg-purple-700 text-white font-black transition">
                        Download PDF
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const modal = document.getElementById('downloadPresensiModal');
            const btn = document.getElementById('btnDownloadPresensi');
            const close = document.getElementById('closeDownloadModal');
            const backdrop = modal?.querySelector('[data-backdrop]');

            function openM() {
                if (!modal) return;
                modal.classList.remove('hidden');
                document.body.classList.add('overflow-hidden');
            }

            function closeM() {
                if (!modal) return;
                modal.classList.add('hidden');
                document.body.classList.remove('overflow-hidden');
            }

            btn?.addEventListener('click', openM);
            close?.addEventListener('click', closeM);
            backdrop?.addEventListener('click', closeM);

            window.addEventListener('keydown', (e) => {
                if (e.key === 'Escape' && modal && !modal.classList.contains('hidden')) closeM();
            });
        });
    </script>

</x-app-layout>
