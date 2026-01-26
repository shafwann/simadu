{{-- resources/views/admin/attendance/recap.blade.php --}}

<x-app-layout>
    <div class="py-12 bg-white min-h-screen text-gray-900">
        <div class="max-w-7xl mx-auto px-6 lg:px-8 space-y-6">

            {{-- HEADER --}}
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-50 border border-blue-100 mb-2">
                        <span class="w-2 h-2 rounded-full bg-blue-600"></span>
                        <span class="text-xs font-bold text-blue-700 uppercase tracking-wider">Rekap Absensi</span>
                    </div>
                    <h1 class="text-3xl font-black text-gray-900 tracking-tight">Rekap Absensi Mahasiswa</h1>
                    <p class="text-gray-500 mt-1">{{ $labelPeriode ?? '-' }}</p>
                </div>

                <div class="flex gap-2 w-full md:w-auto">
                    <a href="{{ route('admin.students.index') }}"
                       class="w-full md:w-auto px-4 py-3 rounded-2xl bg-gray-100 hover:bg-gray-200 text-sm font-bold text-center transition">
                        ← Kembali
                    </a>

                    <a href="{{ route('admin.attendance.recap') }}"
                       class="w-full md:w-auto px-4 py-3 rounded-2xl bg-white border border-gray-200 hover:bg-gray-50 text-sm font-bold text-center transition">
                        Reset
                    </a>
                </div>
            </div>

            {{-- FILTER --}}
            <div class="bg-white border border-gray-100 rounded-2xl shadow-lg p-5">
                <form method="GET" action="{{ route('admin.attendance.recap') }}"
                      class="flex flex-col md:flex-row gap-3 md:items-center">

                    {{-- MODE --}}
                    <select name="mode" onchange="this.form.submit()"
                            class="px-4 py-3 rounded-2xl border-gray-200 text-sm font-bold shadow-sm bg-white w-full md:w-44">
                        <option value="harian"  {{ ($mode ?? 'harian')==='harian' ? 'selected' : '' }}>Harian</option>
                        <option value="bulanan" {{ ($mode ?? '')==='bulanan' ? 'selected' : '' }}>Bulanan</option>
                    </select>

                    {{-- DATE INPUT (dynamic) --}}
                    @php $modeVal = $mode ?? 'harian'; @endphp

                    @if($modeVal === 'harian')
                        <input type="date" name="date" value="{{ request('date') }}"
                               onchange="this.form.submit()"
                               class="px-4 py-3 rounded-2xl border-gray-200 text-sm shadow-sm w-full md:w-56">
                    @elseif($modeVal === 'mingguan')
                        <input type="date" name="week_start" value="{{ request('week_start') }}"
                               onchange="this.form.submit()"
                               class="px-4 py-3 rounded-2xl border-gray-200 text-sm shadow-sm w-full md:w-56"
                               title="Tanggal awal minggu">
                    @else
                        <input type="month" name="month" value="{{ request('month') }}"
                               onchange="this.form.submit()"
                               class="px-4 py-3 rounded-2xl border-gray-200 text-sm shadow-sm w-full md:w-56">
                    @endif

                    {{-- STATUS --}}
                    <select name="status" onchange="this.form.submit()"
                            class="px-4 py-3 rounded-2xl border-gray-200 text-sm font-bold shadow-sm bg-white w-full md:w-52">
                        @php $statusVal = $status ?? 'all'; @endphp
                        <option value="all"   {{ $statusVal==='all' ? 'selected' : '' }}>Semua Status</option>
                        <option value="hadir" {{ $statusVal==='hadir' ? 'selected' : '' }}>Hadir</option>
                        <option value="telat" {{ $statusVal==='telat' ? 'selected' : '' }}>Telat</option>
                        <option value="ijin"  {{ $statusVal==='ijin' ? 'selected' : '' }}>Ijin</option>
                        <option value="sakit" {{ $statusVal==='sakit' ? 'selected' : '' }}>Sakit</option>
                        <option value="alpha" {{ $statusVal==='alpha' ? 'selected' : '' }}>Alpha</option>
                    </select>

                   {{-- BIDANG (Super Admin only) --}}
@if(!empty($isSuperAdmin) && $isSuperAdmin)
    <select name="bidang" onchange="this.form.submit()"
            class="px-4 py-3 rounded-2xl border-gray-200 text-sm font-bold shadow-sm bg-white w-full md:w-64">
        @php $bidangVal = $bidang ?? 'all'; @endphp

        <option value="all" {{ $bidangVal === 'all' ? 'selected' : '' }}>
            Semua Bidang
        </option>

        @foreach($bidangList as $b)
            <option value="{{ $b }}" {{ $bidangVal === $b ? 'selected' : '' }}>
                {{ $b }}
            </option>
        @endforeach
    </select>
@endif

                    {{-- hint kecil --}}
                    <div class="text-xs text-gray-400 md:ml-auto">
                        *Filter otomatis saat pilihan berubah
                    </div>
                </form>
            </div>

            {{-- SUMMARY --}}
            @php
                $sum = $summary ?? ['mahasiswa'=>0,'hadir'=>0,'telat'=>0,'ijin'=>0,'sakit'=>0,'alpha'=>0,'total'=>0];
            @endphp

            <div class="grid grid-cols-2 md:grid-cols-7 gap-3">
                <div class="bg-white border border-gray-100 rounded-2xl shadow-sm p-4">
                    <div class="text-xs text-gray-400 font-bold uppercase">Mahasiswa</div>
                    <div class="text-2xl font-black text-gray-900 mt-1">{{ $sum['mahasiswa'] }}</div>
                </div>
                <div class="bg-white border border-gray-100 rounded-2xl shadow-sm p-4">
                    <div class="text-xs text-gray-400 font-bold uppercase">Hadir</div>
                    <div class="text-2xl font-black text-gray-900 mt-1">{{ $sum['hadir'] }}</div>
                </div>
                <div class="bg-white border border-gray-100 rounded-2xl shadow-sm p-4">
                    <div class="text-xs text-gray-400 font-bold uppercase">Telat</div>
                    <div class="text-2xl font-black text-gray-900 mt-1">{{ $sum['telat'] }}</div>
                </div>
                <div class="bg-white border border-gray-100 rounded-2xl shadow-sm p-4">
                    <div class="text-xs text-gray-400 font-bold uppercase">Ijin</div>
                    <div class="text-2xl font-black text-gray-900 mt-1">{{ $sum['ijin'] }}</div>
                </div>
                <div class="bg-white border border-gray-100 rounded-2xl shadow-sm p-4">
                    <div class="text-xs text-gray-400 font-bold uppercase">Sakit</div>
                    <div class="text-2xl font-black text-gray-900 mt-1">{{ $sum['sakit'] }}</div>
                </div>
                <div class="bg-white border border-gray-100 rounded-2xl shadow-sm p-4">
                    <div class="text-xs text-gray-400 font-bold uppercase">Alpha</div>
                    <div class="text-2xl font-black text-gray-900 mt-1">{{ $sum['alpha'] }}</div>
                </div>
                <div class="bg-white border border-gray-100 rounded-2xl shadow-sm p-4">
                    <div class="text-xs text-gray-400 font-bold uppercase">Total Data</div>
                    <div class="text-2xl font-black text-gray-900 mt-1">{{ $sum['total'] }}</div>
                </div>
            </div>

            {{-- TABLE --}}
            <div class="bg-white border border-gray-100 rounded-[2rem] shadow-2xl shadow-gray-100/50 overflow-hidden">
                <div class="p-6 border-b border-gray-50 flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                    <div>
                        <h3 class="text-lg font-black text-gray-800">Detail Rekap</h3>
                        <p class="text-sm text-gray-400">Berisi rekap absensi sesuai filter yang dipilih.</p>
                    </div>

                    <div class="text-xs text-gray-400">
                        Menampilkan: <span class="font-bold text-gray-700">{{ count($rekap ?? []) }}</span> mahasiswa
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50/50 border-b border-gray-100 text-xs uppercase tracking-widest text-gray-400 font-bold">
                                <th class="px-6 py-4">Mahasiswa</th>
                                <th class="px-6 py-4">NIM</th>
                                <th class="px-6 py-4">Instansi</th>
                                <th class="px-6 py-4">Bidang</th>
                                <th class="px-6 py-4 text-center">Hadir</th>
                                <th class="px-6 py-4 text-center">Telat</th>
                                <th class="px-6 py-4 text-center">Ijin</th>
                                <th class="px-6 py-4 text-center">Sakit</th>
                                <th class="px-6 py-4 text-center">Alpha</th>
                                <th class="px-6 py-4 text-center">Total</th>
                                {{-- <th class="px-6 py-4 text-center">%</th> --}}
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-50">
                            @forelse(($rekap ?? []) as $r)
                                <tr class="hover:bg-blue-50/30 transition">
                                    <td class="px-6 py-4">
                                        <div class="font-bold text-gray-900">{{ $r['name'] ?? '-' }}</div>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-600">{{ $r['nim'] ?? '-' }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-600">{{ $r['instansi'] ?? '-' }}</td>
                                    <td class="px-6 py-4">
                                        <span class="px-3 py-1 rounded-lg text-xs font-bold border whitespace-nowrap bg-purple-50 text-purple-700 border-purple-100">
                                            {{ $r['bidang'] ?? '-' }}
                                        </span>
                                    </td>

                                    {{-- HADIR --}}
<td class="px-6 py-4 text-center">
    @if($r['hadir'] > 0)
        <span class="px-3 py-1 rounded-full text-xs font-bold bg-green-50 text-green-700 border border-green-100">
            {{ $r['hadir'] }}
        </span>
    @else
        <span class="text-gray-400">0</span>
    @endif
</td>

{{-- TELAT --}}
<td class="px-6 py-4 text-center">
    @if($r['telat'] > 0)
        <span class="px-3 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-700 border border-amber-100">
            {{ $r['telat'] }}
        </span>
    @else
        <span class="text-gray-400">0</span>
    @endif
</td>

{{-- IJIN --}}
<td class="px-6 py-4 text-center">
    @if($r['ijin'] > 0)
        <span class="px-3 py-1 rounded-full text-xs font-bold bg-blue-50 text-blue-700 border border-blue-100">
            {{ $r['ijin'] }}
        </span>
    @else
        <span class="text-gray-400">0</span>
    @endif
</td>

{{-- SAKIT --}}
<td class="px-6 py-4 text-center">
    @if($r['sakit'] > 0)
        <span class="px-3 py-1 rounded-full text-xs font-bold bg-purple-50 text-purple-700 border border-purple-100">
            {{ $r['sakit'] }}
        </span>
    @else
        <span class="text-gray-400">0</span>
    @endif
</td>

{{-- ALPHA --}}
<td class="px-6 py-4 text-center">
    @if($r['alpha'] > 0)
        <span class="px-3 py-1 rounded-full text-xs font-bold bg-red-50 text-red-700 border border-red-100">
            {{ $r['alpha'] }}
        </span>
    @else
        <span class="text-gray-400">0</span>
    @endif
</td>

                                    <td class="px-6 py-4 text-center">
                                        @php $p = $r['persen'] ?? 0; @endphp
                                        <span class="px-3 py-1 rounded-full text-xs font-bold border
                                            {{ $p >= 80 ? 'bg-green-50 text-green-700 border-green-100' : ($p >= 50 ? 'bg-amber-50 text-amber-700 border-amber-100' : 'bg-red-50 text-red-700 border-red-100') }}">
                                            {{ $p }}%
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="11" class="px-6 py-14 text-center text-gray-400 italic">
                                        Tidak ada data rekap pada periode ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mt-6 text-center">
                <p class="text-xs text-gray-400">&copy; Sistem Monitoring Magang</p>
            </div>

        </div>
    </div>
</x-app-layout>
