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

                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Tanggal</label>
                                <input type="date" name="tanggal" value="{{ date('Y-m-d') }}"
                                    class="w-full rounded-xl border-gray-200 bg-gray-50 focus:ring-blue-500 focus:border-blue-500 font-bold text-gray-800">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Jam</label>
                                <input type="time" name="jam" value="08:00"
                                    class="w-full rounded-xl border-gray-200 bg-gray-50 focus:ring-blue-500 focus:border-blue-500 font-bold text-gray-800">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Tipe Absensi</label>
                                <select name="tipe_absensi"
                                    class="w-full rounded-xl border-gray-200 bg-gray-50 focus:ring-blue-500 focus:border-blue-500 font-bold text-gray-800">
                                    <option value="check_in">⬇️ Check IN</option>
                                    <option value="check_out">⬆️ Check OUT</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Status Kehadiran</label>
                                <select name="status" id="statusSelect"
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

                            <div id="pulangCepatWrapper" class="mt-3 hidden">
                                <label class="inline-flex items-center gap-2 font-semibold text-gray-700">
                                    <input type="checkbox"
                                        id="pulangCepatCheckbox"
                                        name="keterangan"
                                        value="Pulang Cepat"
                                        class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                    ⏱️ Pulang Cepat
                                </label>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Link Google Maps</label>
                                <input type="text" name="location" placeholder="https://goo.gl/maps/..."
                                    class="w-full rounded-xl border-gray-200 bg-gray-50 focus:ring-blue-500 focus:border-blue-500 font-bold text-gray-800 text-sm">
                                <p class="text-[10px] text-gray-400 mt-1">*Paste link 'Share' atau 'Embed' dari Google Maps di sini.</p>
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

                    @php
                    $isSuperAdmin = Auth::user()->bidang === 'Administrator Sistem';
                    $scanUrl = $student->assessment && $student->assessment->certificate_scan
                    ? asset('storage/' . $student->assessment->certificate_scan)
                    : null;
                    @endphp

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

                                {{-- Download Sertifikat hanya Super Admin --}}
                                @if($isSuperAdmin)
                                <a href="{{ route('admin.assessment.certificate', $student->id) }}"
                                    class="px-4 py-2 rounded-xl bg-blue-600 text-white font-bold text-sm hover:bg-blue-700 transition">
                                    Download
                                </a>
                                @endif
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

                            @if($student->assessment && $isSuperAdmin)
                            @php
                            $scanPath = $student->assessment->certificate_scan ?? null;
                            $scanUrl = $scanPath ? asset('storage/' . $scanPath) : null;

                            $scanOriginalName = $student->assessment->certificate_scan_original_name ?? null;

                            $scanDisplayName = $scanOriginalName ?: ($scanPath ? basename($scanPath) : 'Belum ada file dipilih');
                            @endphp

                            <div class="mt-6 bg-white border border-gray-100 rounded-2xl p-5">
                                <div class="flex items-center justify-between gap-3 flex-wrap">
                                    <div>
                                        <p class="text-sm font-black text-gray-900">Upload Scan Sertifikat</p>
                                        <p class="text-[11px] font-bold text-gray-400 mt-1">PDF/JPG/PNG/WEBP • Max 20MB</p>
                                    </div>
                                    @if($scanUrl)
                                    <form action="{{ route('admin.assessment.scan.destroy', $student->assessment->id) }}"
                                        method="POST"
                                        onsubmit="return confirm('Yakin ingin menghapus scan sertifikat ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="px-4 py-2 rounded-xl bg-red-600 text-white font-bold text-sm hover:bg-red-700 transition">
                                            Hapus
                                        </button>
                                    </form>
                                    @endif
                                </div>

                                <form class="mt-4 flex flex-col md:flex-row gap-3 items-start md:items-center"
                                    action="{{ route('admin.assessment.scan.upload', $student->assessment->id) }}"
                                    method="POST"
                                    enctype="multipart/form-data">
                                    @csrf
                                    @method('PATCH')

                                    <div class="w-full flex-1">
                                        <input
                                            id="certificate_scan_input"
                                            type="file"
                                            name="certificate_scan"
                                            accept="application/pdf,image/jpeg,image/png,image/webp"
                                            class="hidden">

                                        <div class="w-full rounded-2xl border border-gray-200 bg-gray-50 px-4 py-3 hover:bg-gray-100 transition">
                                            <div class="flex items-center justify-between gap-3">
                                                <label for="certificate_scan_input" class="cursor-pointer inline-flex items-center gap-2">
                                                    <span class="px-3 py-1.5 rounded-lg bg-white border border-gray-200 text-gray-700 font-bold text-sm">
                                                        Pilih File
                                                    </span>
                                                    @if($scanUrl)
                                                    <a href="{{ $scanUrl }}" target="_blank"
                                                        class="font-bold text-blue-600 hover:underline break-all">
                                                        <span id="certificate_scan_filename">{{ $scanDisplayName }}</span>
                                                    </a>
                                                    @else
                                                    <span id="certificate_scan_filename" class="font-semibold text-gray-500">
                                                        {{ $scanDisplayName }}
                                                    </span>
                                                    @endif
                                                </label>
                                            </div>
                                        </div>

                                        @error('certificate_scan')
                                        <p class="mt-2 text-xs text-red-600 font-semibold">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <button type="submit"
                                        class="w-full md:w-auto px-5 py-3 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold transition">
                                        {{ $scanUrl ? 'Upload Scan' : 'Upload Scan' }}
                                    </button>
                                </form>
                            </div>

                            <script>
                                document.addEventListener('DOMContentLoaded', function() {
                                    const input = document.getElementById('certificate_scan_input');
                                    const nameEl = document.getElementById('certificate_scan_filename');
                                    if (!input || !nameEl) return;

                                    const initial = nameEl.textContent;

                                    input.addEventListener('change', function() {
                                        const f = this.files && this.files[0] ? this.files[0] : null;
                                        if (!f) {
                                            nameEl.textContent = initial;
                                            return;
                                        }
                                        // ✅ tampilkan nama asli file yang dipilih user
                                        nameEl.textContent = f.name;
                                    });
                                });
                            </script>
                            @endif


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
                                class="px-4 py-2 rounded-xl bg-blue-600 text-white font-bold text-sm hover:bg-blue-700 transition">
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
                                    @php
                                    $stRaw = $a->status ?? '-';
                                    $ketRaw = $a->keterangan ?? null;
                                    $st = strtoupper(trim((string) $stRaw));
                                    $ket = strtoupper(trim((string) $ketRaw));

                                    $code = '-';
                                    if ($st === '' || $st === '-') $code = '-';
                                    elseif (in_array($st, ['ALPHA','TANPA KETERANGAN','TK'])) $code = 'TK';
                                    elseif ($st === 'HADIR') $code = 'H';
                                    elseif ($st === 'TELAT') $code = 'T';
                                    elseif (in_array($st, ['I','IZIN','IJIN'])) $code = 'I';
                                    elseif (in_array($st, ['S','SAKIT'])) $code = 'S';
                                    elseif (in_array($st, ['DL','DINAS LUAR'])) $code = 'DL';
                                    elseif (in_array($st, ['CT','CUTI'])) $code = 'CT';

                                    $isPC = ($ket === 'PULANG CEPAT');
                                    $isTAP = (!empty($a->check_in) && empty($a->check_out));

                                    $extras = [];
                                    if ($isPC) $extras[] = 'PC';
                                    if ($isTAP) $extras[] = 'TAP';

                                    $statusFinal = $code;
                                    if (!empty($extras)) $statusFinal = $code . ',' . implode(',', $extras);

                                    $parts = array_filter(array_map('trim', explode(',', $statusFinal)));
                                    @endphp

                                    <tr class="hover:bg-blue-50/50 transition">
                                        <td class="p-4 font-medium">{{ \Carbon\Carbon::parse($a->tanggal)->format('d M Y') }}</td>

                                        <td class="p-4 font-mono text-gray-600">
                                            {{ $a->check_in ? \Carbon\Carbon::parse($a->check_in)->format('H:i') : '-' }}
                                            <span class="text-gray-300">—</span>
                                            {{ $a->check_out ? \Carbon\Carbon::parse($a->check_out)->format('H:i') : '-' }}
                                        </td>

                                        <td class="p-4">
                                            <div class="flex flex-wrap gap-2">
                                                @foreach($parts as $p)
                                                @if($p === 'H')
                                                <span class="px-2 py-1 bg-green-100 text-green-700 rounded-md text-xs font-bold">H</span>
                                                @elseif($p === 'T')
                                                <span class="px-2 py-1 bg-yellow-100 text-yellow-700 rounded-md text-xs font-bold">T</span>
                                                @elseif($p === 'I')
                                                <span class="px-2 py-1 bg-blue-100 text-blue-700 rounded-md text-xs font-bold">I</span>
                                                @elseif($p === 'S')
                                                <span class="px-2 py-1 bg-amber-100 text-amber-700 rounded-md text-xs font-bold">S</span>
                                                @elseif($p === 'DL')
                                                <span class="px-2 py-1 bg-purple-100 text-purple-700 rounded-md text-xs font-bold">DL</span>
                                                @elseif($p === 'CT')
                                                <span class="px-2 py-1 bg-pink-100 text-pink-700 rounded-md text-xs font-bold">CT</span>
                                                @elseif($p === 'TK')
                                                <span class="px-2 py-1 bg-gray-100 text-gray-700 rounded-md text-xs font-bold">TK</span>
                                                @elseif($p === 'PC')
                                                <span class="px-2 py-1 bg-orange-100 text-orange-700 rounded-md text-xs font-bold">PC</span>
                                                @elseif($p === 'TAP')
                                                <span class="px-2 py-1 bg-red-100 text-red-700 rounded-md text-xs font-bold">TAP</span>
                                                @else
                                                <span class="px-2 py-1 bg-gray-100 text-gray-700 rounded-md text-xs font-bold">{{ $p }}</span>
                                                @endif
                                                @endforeach
                                            </div>
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
                        <div class="p-6 border-b border-gray-50 bg-gray-50/30 flex items-center justify-between">
                            <h3 class="font-bold text-lg text-gray-800">Riwayat Logbook</h3>

                            <button type="button" id="btnDownloadLogbookAdmin"
                                class="px-4 py-2 rounded-xl bg-blue-600 text-white font-bold text-sm hover:bg-blue-700 transition">
                                Download PDF
                            </button>
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
                    <div class="bg-white border border-gray-100 rounded-[2rem] p-6 shadow-sm overflow-hidden">
                        <h3 class="text-lg font-black text-gray-800 mb-4 flex items-center gap-2">
                            <span class="w-2 h-6 bg-blue-600 rounded-full"></span>
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
                                                data-url="{{ route('admin.laporan.preview', $lap->id) }}"
                                                class="btnSetPreviewIframe px-3 py-1 bg-gray-100 text-gray-600 text-xs font-bold rounded-lg hover:bg-gray-200 transition">
                                                Preview
                                            </button>

                                            <a href="{{ route('admin.laporan.download', $lap->id) }}"
                                                class="px-3 py-1 bg-purple-600 text-white text-xs font-bold rounded-lg hover:bg-purple-700 transition">
                                                Download
                                            </a>
                                            <form action="{{ route('admin.laporan.destroy', $lap->id) }}" method="POST"
                                                onsubmit="return confirm('Yakin hapus laporan ini? File juga akan ikut terhapus dan tidak muncul lagi di akun mahasiswa.');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="px-3 py-1 bg-red-600 text-white text-xs font-bold rounded-lg hover:bg-red-700 transition">
                                                    Hapus
                                                </button>
                                            </form>
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

                        <div class="w-full h-[520px] border border-gray-200 rounded-xl overflow-hidden">
                            <iframe id="preview-iframe" class="w-full h-full" src="" frameborder="0"></iframe>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    {{-- ✅ MODAL PENILAIAN --}}
    <div id="assessmentModal"
        data-has-assessment="{{ $student->assessment ? '1' : '0' }}"
        data-update-url="{{ $student->assessment ? route('admin.assessment.update', $student->assessment->id) : '' }}"
        data-store-url="{{ route('admin.assessment.store', $student->id) }}"
        data-student-status="{{ $student->status }}"
        class="fixed inset-0 z-50 hidden">

        <div class="absolute inset-0 bg-black/50" data-backdrop></div>

        <div class="relative min-h-full w-full flex items-center justify-center p-4">
            <div class="w-full max-w-lg bg-white rounded-3xl shadow-2xl border border-gray-100 overflow-hidden
                    max-h-[90vh] flex flex-col">

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

                <div class="p-5 pt-1 overflow-y-auto">
                    <form id="assessmentForm"
                        method="POST"
                        enctype="multipart/form-data"
                        action="{{ route('admin.assessment.store', $student->id) }}"
                        class="space-y-5">
                        @csrf
                        <input type="hidden" id="methodSpoof" name="_method" value="POST">

                        {{-- Admin bidang: hidden agar tetap terkirim --}}
                        @php
                        $oldNo = old('certificate_no', optional($student->assessment)->certificate_no);
                        $oldDate = old('certificate_date', optional(optional($student->assessment)->certificate_date)->format('Y-m-d'));
                        $defaultNo = $oldNo ?: '-';
                        $defaultDate = $oldDate ?: now()->format('Y-m-d');
                        @endphp

                        @if(!$isSuperAdmin)
                        <input type="hidden" name="certificate_no" value="{{ $defaultNo }}">
                        <input type="hidden" name="certificate_date" value="{{ $defaultDate }}">
                        @endif

                        {{-- Nomor Sertifikat (Super Admin) --}}
                        @if($isSuperAdmin)
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
                        @endif

                        {{-- Aspek Penilaian --}}
                        <div class="rounded-2xl border border-gray-100 bg-white p-4 mt-0">
                            <div class="mb-3">
                                <h3 class="text-base font-black text-gray-900">Aspek Penilaian</h3>
                                <p class="text-xs text-gray-500">Isi nilai 0–100 untuk tiap aspek.</p>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div class="rounded-1xl border border-gray-100 bg-white p-4">
                                    <p class="text-[11px] font-black text-gray-500 uppercase tracking-wide">1. Etika & Kedisiplinan</p>
                                    <input id="input1" type="number" name="aspek_1" min="0" max="100" required
                                        value="{{ old('aspek_1', optional($student->assessment)->aspek_1) }}"
                                        class="mt-2 w-full rounded-1xl border-gray-200 bg-gray-50 focus:ring-blue-500 focus:border-blue-500 font-black text-gray-900">
                                </div>

                                <div class="rounded-1xl border border-gray-100 bg-white p-4">
                                    <p class="text-[11px] font-black text-gray-500 uppercase tracking-wide">2. Komunikasi & Kerjasama</p>
                                    <input id="input2" type="number" name="aspek_2" min="0" max="100" required
                                        value="{{ old('aspek_2', optional($student->assessment)->aspek_2) }}"
                                        class="mt-2 w-full rounded-1xl border-gray-200 bg-gray-50 focus:ring-blue-500 focus:border-blue-500 font-black text-gray-900">
                                </div>

                                <div class="rounded-1xl border border-gray-100 bg-white p-4">
                                    <p class="text-[11px] font-black text-gray-500 uppercase tracking-wide">3. Kemandirian</p>
                                    <input id="input3" type="number" name="aspek_3" min="0" max="100" required
                                        value="{{ old('aspek_3', optional($student->assessment)->aspek_3) }}"
                                        class="mt-2 w-full rounded-1xl border-gray-200 bg-gray-50 focus:ring-blue-500 focus:border-blue-500 font-black text-gray-900">
                                </div>

                                <div class="rounded-1xl border border-gray-100 bg-white p-4">
                                    <p class="text-[11px] font-black text-gray-500 uppercase tracking-wide">4. Kreativitas</p>
                                    <input id="input4" type="number" name="aspek_4" min="0" max="100" required
                                        value="{{ old('aspek_4', optional($student->assessment)->aspek_4) }}"
                                        class="mt-2 w-full rounded-1xl border-gray-200 bg-gray-50 focus:ring-blue-500 focus:border-blue-500 font-black text-gray-900">
                                </div>

                                <div class="rounded-1xl border border-gray-100 bg-white p-4">
                                    <p class="text-[11px] font-black text-gray-500 uppercase tracking-wide">5. Penyelesaian Pekerjaan</p>
                                    <input id="input5" type="number" name="aspek_5" min="0" max="100" required
                                        value="{{ old('aspek_5', optional($student->assessment)->aspek_5) }}"
                                        class="mt-2 w-full rounded-1xl border-gray-200 bg-gray-50 focus:ring-blue-500 focus:border-blue-500 font-black text-gray-900">
                                </div>

                                <div class="rounded-1xl border border-gray-100 bg-white p-4">
                                    <p class="text-[11px] font-black text-gray-500 uppercase tracking-wide">6. Problem Solving</p>
                                    <input id="input6" type="number" name="aspek_6" min="0" max="100" required
                                        value="{{ old('aspek_6', optional($student->assessment)->aspek_6) }}"
                                        class="mt-2 w-full rounded-1xl border-gray-200 bg-gray-50 focus:ring-blue-500 focus:border-blue-500 font-black text-gray-900">
                                </div>
                            </div>
                        </div>

                        {{-- Apresiasi Khusus --}}
                        <div class="rounded-2xl border border-gray-100 bg-white p-4 mt-0">
                            <label class="block text-[11px] font-black text-gray-500 uppercase tracking-wide">
                                Apresiasi Khusus
                            </label>
                            <textarea id="inputApresiasiKhusus" name="apresiasi_khusus" rows="3"
                                class="mt-2 w-full rounded-2xl border-gray-200 bg-gray-50 focus:ring-blue-500 focus:border-blue-500 font-bold text-gray-800 text-sm">{{ old('apresiasi_khusus', optional($student->assessment)->apresiasi_khusus) }}</textarea>
                        </div>

                        {{-- Tanggal Sertifikat (Super Admin) --}}
                        @if($isSuperAdmin)
                        <div class="rounded-2xl border border-gray-100 bg-white p-4 mt-0">
                            <label class="block text-[11px] font-black text-gray-500 uppercase tracking-wide">
                                Tanggal Sertifikat
                            </label>
                            <input id="inputTanggalSertifikat" type="date" name="certificate_date" required
                                value="{{ old('certificate_date', optional(optional($student->assessment)->certificate_date)->format('Y-m-d')) }}"
                                class="mt-2 w-full rounded-2xl border-gray-200 bg-gray-50 focus:ring-blue-500 focus:border-blue-500 font-black text-gray-900">
                            <p class="mt-2 text-[11px] text-gray-400">
                                *Tanggal ini akan dipakai di sertifikat.
                            </p>
                        </div>
                        @endif

                        <div class="sticky bottom-0 bg-white pt-3">
                            <button id="assessmentSubmitBtn" type="submit"
                                class="w-full py-3 rounded-2xl bg-blue-600 hover:bg-blue-700 text-white font-black transition shadow-lg shadow-blue-200">
                                Simpan Penilaian
                            </button>
                        </div>

                    </form>
                </div>

            </div>
        </div>
    </div>

    {{-- ✅ SCRIPT MODAL + preview iframe --}}
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

            const apresiasiInput = document.getElementById('inputApresiasiKhusus');

            function focusApresiasiSelect() {
                if (!apresiasiInput) return;
                setTimeout(() => apresiasiInput.focus(), 50);
            }

            function openAssessment(mode) {
                assessmentModal.classList.remove('hidden');
                document.body.classList.add('overflow-hidden');

                if (mode === 'edit' && updateUrl) {
                    assessmentForm.action = updateUrl;
                    methodSpoof.value = "PATCH";
                    submitBtn.textContent = "Simpan Perubahan";
                    focusApresiasiSelect();
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

            if (statusButton && studentStatus === "aktif") {
                statusButton.addEventListener('click', function(e) {
                    e.preventDefault();
                    openAssessment(hasAssessment ? 'edit' : 'create');
                });
            }

            if (btnEditAssessmentTop) {
                btnEditAssessmentTop.addEventListener('click', function(e) {
                    e.preventDefault();
                    openAssessment('edit');
                });
            }

            if (closeBtn) closeBtn.addEventListener('click', closeAssessment);
            if (backdrop) backdrop.addEventListener('click', closeAssessment);

            window.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && !assessmentModal.classList.contains('hidden')) closeAssessment();
            });

            // ✅ Preview Iframe (Laporan & Scan Sertifikat pakai handler yang sama)
            const iframe = document.getElementById('preview-iframe');

            document.querySelectorAll('.btnSetPreviewIframe').forEach(btn => {
                btn.addEventListener('click', function() {
                    const url = this.dataset.url;
                    if (!iframe || !url) return;
                    iframe.src = url;

                    // Scroll halus ke iframe biar user langsung lihat preview
                    iframe.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                });
            });

            // tombol khusus dari Riwayat Penilaian (jika ada)
            const btnScan = document.getElementById('btnPreviewScanSertifikat');
            if (btnScan && iframe) {
                btnScan.addEventListener('click', function() {
                    const url = this.dataset.url;
                    if (!url) return;
                    iframe.src = url;
                    iframe.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                });
            }
        });

        // pulang cepat
        document.addEventListener('DOMContentLoaded', function() {
            const statusSelect = document.getElementById('statusSelect');
            const pcWrapper = document.getElementById('pulangCepatWrapper');
            const pcCheckbox = document.getElementById('pulangCepatCheckbox');

            function updatePulangCepatUI() {
                const status = statusSelect.value;
                if (status === 'Hadir' || status === 'Telat') {
                    pcWrapper.classList.remove('hidden');
                    pcCheckbox.disabled = false;
                } else {
                    pcWrapper.classList.add('hidden');
                    pcCheckbox.checked = false;
                    pcCheckbox.disabled = true;
                }
            }

            updatePulangCepatUI();
            statusSelect.addEventListener('change', updatePulangCepatUI);
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
                                {{ \Carbon\Carbon::create()->month($m)->locale('id')->translatedFormat('F') }}
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
                        class="w-full py-3 rounded-2xl bg-blue-600 hover:bg-blue-700 text-white font-black transition">
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

    {{-- MODAL DOWNLOAD LOGBOOK ADMIN --}}
    <div id="downloadLogbookAdminModal" class="fixed inset-0 z-50 hidden">
        <div class="absolute inset-0 bg-black/50" data-backdrop></div>

        <div class="relative min-h-full flex items-center justify-center p-4">
            <div class="w-full max-w-md bg-white rounded-3xl shadow-2xl border border-gray-100 overflow-hidden">
                <div class="p-5 border-b border-gray-100 relative">
                    <button id="closeLogbookAdminModal" type="button"
                        class="absolute top-4 right-4 w-10 h-10 rounded-xl flex items-center justify-center
                           text-gray-400 hover:text-gray-700 hover:bg-gray-100 transition">
                        <span class="text-2xl leading-none">&times;</span>
                    </button>
                    <h2 class="text-xl font-black text-gray-900 text-center">Download Logbook (PDF)</h2>
                    <p class="text-sm text-gray-500 mt-1">Pilih bulan & tahun yang ingin dicetak.</p>
                </div>

                <form class="p-5 space-y-4"
                    method="GET"
                    action="{{ route('admin.students.logbook.pdf', $student->id) }}">

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
                        class="w-full py-3 rounded-2xl bg-blue-600 hover:bg-blue-700 text-white font-black transition">
                        Download PDF
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const modal = document.getElementById('downloadLogbookAdminModal');
            const btn = document.getElementById('btnDownloadLogbookAdmin');
            const close = document.getElementById('closeLogbookAdminModal');
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