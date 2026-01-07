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
                                <input type="date" name="tanggal" value="{{ date('Y-m-d') }}" class="w-full rounded-xl border-gray-200 bg-gray-50 focus:ring-blue-500 focus:border-blue-500 font-bold text-gray-800">
                            </div>

                            {{-- Jam --}}
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Jam</label>
                                <input type="time" name="jam" value="08:00" class="w-full rounded-xl border-gray-200 bg-gray-50 focus:ring-blue-500 focus:border-blue-500 font-bold text-gray-800">
                            </div>

                            {{-- Tipe Absensi --}}
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Tipe Absensi</label>
                                <select name="tipe_absensi" class="w-full rounded-xl border-gray-200 bg-gray-50 focus:ring-blue-500 focus:border-blue-500 font-bold text-gray-800">
                                    <option value="check_in">⬇️ Check IN</option>
                                    <option value="check_out">⬆️ Check OUT</option>
                                </select>
                            </div>

                            {{-- Status Kehadiran --}}
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Status Kehadiran</label>
                                <select name="status" class="w-full rounded-xl border-gray-200 bg-gray-50 focus:ring-blue-500 focus:border-blue-500 font-bold text-gray-800">
                                    <option value="Hadir">✅ Hadir (WFO/WFH)</option>
                                    <option value="Sakit">💊 Sakit</option>
                                    <option value="Izin">✉️ Izin</option>
                                    <option value="Alpha">❌ Alpha (Tanpa Ket.)</option>
                                </select>
                            </div>

                            {{-- INPUT BARU: LINK MAPS (BUKAN KOORDINAT) --}}
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Link Google Maps</label>
                                <input type="text" name="location" placeholder="https://goo.gl/maps/..."
                                       class="w-full rounded-xl border-gray-200 bg-gray-50 focus:ring-blue-500 focus:border-blue-500 font-bold text-gray-800 text-sm">
                                <p class="text-[10px] text-gray-400 mt-1">*Paste link 'Share' atau 'Embed' dari Google Maps di sini.</p>
                            </div>

                            <button type="submit" class="w-full py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl transition shadow-lg shadow-blue-200">
                                Simpan Data Absen
                            </button>
                        </form>
                    </div>
                </div>

                {{-- KANAN: Riwayat --}}
                <div class="lg:col-span-2 space-y-6">

                    {{-- ✅ RIWAYAT PENILAIAN MAGANG (TAMBAHAN TANPA MENGURANGI KODEMU) --}}
                    <div class="bg-white border border-gray-100 rounded-[2rem] shadow-sm overflow-hidden">
                        <div class="p-6 border-b border-gray-50 bg-gray-50/30 flex items-center justify-between">
                            <h3 class="font-bold text-lg text-gray-800">Riwayat Penilaian Magang</h3>

                            @if($student->assessment)
                                <div class="flex items-center gap-2">
                                    <button type="button"
                                            id="btnEditAssessmentTop"
                                            class="px-3 py-2 rounded-xl bg-amber-500 text-white font-bold text-sm hover:bg-amber-600 transition">
                                        Edit
                                    </button>

                                    <form action="{{ route('admin.assessment.destroy', $student->assessment->id) }}"
                                          method="POST"
                                          onsubmit="return confirm('Yakin ingin menghapus penilaian ini?');">
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
                                    $scores = [
                                        (int) $student->assessment->aspek_1,
                                        (int) $student->assessment->aspek_2,
                                        (int) $student->assessment->aspek_3,
                                        (int) $student->assessment->aspek_4,
                                        (int) $student->assessment->aspek_5,
                                        (int) $student->assessment->aspek_6,
                                    ];
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
                                        <th class="p-4 font-bold text-gray-500">Lokasi / Map</th> {{-- JUDUL KOLOM --}}
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

                                        {{-- KOLOM LINK MAPS (Langsung Pakai Link Database) --}}
                                        <td class="p-4 text-xs">
                                            @if($a->location)
                                                <div class="flex flex-col gap-1">
                                                    {{-- Link langsung ke URL yang disimpan --}}
                                                    <a href="{{ $a->location }}" target="_blank" class="flex items-center gap-1 text-blue-600 font-bold hover:underline">
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                                        Buka Link Peta
                                                    </a>
                                                    {{-- Tampilkan potongan link biar tau isinya --}}
                                                    <span class="text-[10px] text-gray-400 truncate max-w-[120px]" title="{{ $a->location }}">
                                                        {{ $a->location }}
                                                    </span>
                                                </div>
                                            @else
                                                <span class="text-gray-400 italic text-[10px]">Lokasi Kosong</span>
                                            @endif
                                        </td>

                                        <td class="p-4 text-center flex items-center justify-center gap-2">
                                            <a href="{{ route('admin.attendance.edit', $a->id) }}" class="text-blue-400 hover:text-blue-600 transition" title="Edit Absensi">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                            </a>
                                            <form action="{{ route('admin.attendance.destroy', $a->id) }}" method="POST" onsubmit="return confirm('Hapus data presensi ini?');">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="text-red-400 hover:text-red-600 transition" title="Hapus">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr><td colspan="5" class="p-6 text-center italic text-gray-400">Belum ada data presensi.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- Riwayat Logbook & Laporan (Sama seperti sebelumnya) --}}
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
                                            <button onclick="document.getElementById('preview-iframe').src='{{ route('laporan.preview', $lap->id) }}'"
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
                                    <tr><td colspan="3" class="p-6 text-center italic text-gray-400">Belum ada laporan.</td></tr>
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

    {{-- ✅ MODAL PENILAIAN (TAMBAHAN TANPA MENGURANGI KODEMU) --}}
    <div id="assessmentModal" class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-3xl shadow-lg p-8 w-full max-w-lg relative">
            <button id="closeModal"
                class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 font-bold text-lg">&times;</button>

            <h2 class="text-2xl font-black mb-6">
                Penilaian Akhir Magang: {{ $student->name }}
            </h2>

            <form id="assessmentForm"
                  method="POST"
                  action="{{ route('admin.assessment.store', $student->id) }}"
                  class="space-y-4">
                @csrf
                <input type="hidden" id="methodSpoof" name="_method" value="POST">

                <div>
                    <label class="block text-sm font-bold">Nomor Sertifikat</label>
                    <input id="inputNomor" type="text" name="certificate_no" required
                           value="{{ old('certificate_no', optional($student->assessment)->certificate_no) }}"
                           class="w-full mt-2 rounded-xl border-gray-300">
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-bold">1. Etika dan Kedisiplinan</label>
                        <input id="input1" type="number" name="aspek_1" min="0" max="100" required
                               value="{{ old('aspek_1', optional($student->assessment)->aspek_1) }}"
                               class="w-full mt-2 rounded-xl border-gray-300">
                    </div>

                    <div>
                        <label class="block text-sm font-bold">2. Kemampuan Berkomunikasi dan Bekerjasama</label>
                        <input id="input2" type="number" name="aspek_2" min="0" max="100" required
                               value="{{ old('aspek_2', optional($student->assessment)->aspek_2) }}"
                               class="w-full mt-2 rounded-xl border-gray-300">
                    </div>

                    <div>
                        <label class="block text-sm font-bold">3. Kemandirian</label>
                        <input id="input3" type="number" name="aspek_3" min="0" max="100" required
                               value="{{ old('aspek_3', optional($student->assessment)->aspek_3) }}"
                               class="w-full mt-2 rounded-xl border-gray-300">
                    </div>

                    <div>
                        <label class="block text-sm font-bold">4. Kreativitas</label>
                        <input id="input4" type="number" name="aspek_4" min="0" max="100" required
                               value="{{ old('aspek_4', optional($student->assessment)->aspek_4) }}"
                               class="w-full mt-2 rounded-xl border-gray-300">
                    </div>

                    <div>
                        <label class="block text-sm font-bold">5. Kemampuan menyelesaikan pekerjaan di bidangnya</label>
                        <input id="input5" type="number" name="aspek_5" min="0" max="100" required
                               value="{{ old('aspek_5', optional($student->assessment)->aspek_5) }}"
                               class="w-full mt-2 rounded-xl border-gray-300">
                    </div>

                    <div>
                        <label class="block text-sm font-bold">6. Kemampuan mengidentifikasi dan memecahkan masalah</label>
                        <input id="input6" type="number" name="aspek_6" min="0" max="100" required
                               value="{{ old('aspek_6', optional($student->assessment)->aspek_6) }}"
                               class="w-full mt-2 rounded-xl border-gray-300">
                    </div>
                </div>

                <button id="assessmentSubmitBtn" class="w-full py-3 bg-blue-600 text-white font-bold rounded-xl">
                    Simpan Penilaian
                </button>
            </form>
        </div>
    </div>

    {{-- ✅ SCRIPT MODAL (TAMBAHAN TANPA MENGURANGI KODEMU) --}}
    <script>
        const assessmentModal = document.getElementById('assessmentModal');
        const closeAssessmentModal = document.getElementById('closeModal');

        // Tombol "Tandai Selesai Magang" yang sudah ada (kita "tumpangi" kliknya)
        const statusForm = document.querySelector('form[action="{{ route('admin.students.updateStatus', $student->id) }}"]');
        const statusButton = statusForm?.querySelector('button[type="submit"]');

        // Form assessment
        const assessmentForm = document.getElementById('assessmentForm');
        const methodSpoof = document.getElementById('methodSpoof');

        // Kalau sudah punya assessment, mode edit
        const hasAssessment = {{ $student->assessment ? 'true' : 'false' }};
        const updateUrl = "{{ $student->assessment ? route('admin.assessment.update', $student->assessment->id) : '' }}";

        function openAssessment(mode) {
            assessmentModal.classList.remove('hidden');

            if (mode === 'edit') {
                assessmentForm.action = updateUrl;
                methodSpoof.value = "PATCH";
                document.getElementById('assessmentSubmitBtn').textContent = "Simpan Perubahan";
            } else {
                assessmentForm.action = "{{ route('admin.assessment.store', $student->id) }}";
                methodSpoof.value = "POST";
                document.getElementById('assessmentSubmitBtn').textContent = "Simpan Penilaian";
            }
        }

        // ✅ Saat klik "Tandai Selesai Magang" (status aktif), tampilkan modal, BUKAN submit form
        if (statusButton && "{{ $student->status }}" === "aktif") {
            statusButton.addEventListener('click', function (e) {
                e.preventDefault();
                openAssessment(hasAssessment ? 'edit' : 'create');
            });
        }

        // Tombol edit dari Riwayat Penilaian (kalau ada)
        const btnEditAssessmentTop = document.getElementById('btnEditAssessmentTop');
        if (btnEditAssessmentTop) {
            btnEditAssessmentTop.addEventListener('click', function (e) {
                e.preventDefault();
                openAssessment('edit');
            });
        }

        closeAssessmentModal?.addEventListener('click', () => {
            assessmentModal.classList.add('hidden');
        });

        window.addEventListener('click', (e) => {
            if (e.target === assessmentModal) assessmentModal.classList.add('hidden');
        });
    </script>
</x-app-layout>
