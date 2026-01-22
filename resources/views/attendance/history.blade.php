<x-app-layout>
    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto px-6 lg:px-8 space-y-6">

            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-black text-gray-900">Riwayat Presensi</h1>
                    <p class="text-gray-500 text-sm">{{ Auth::user()->name }} ({{ Auth::user()->email }})</p>
                </div>

                <div class="flex gap-2">
                    <a href="{{ route('attendance.presensi_pdf') }}"
                        class="px-4 py-2 rounded-xl bg-purple-600 text-white font-bold text-sm hover:bg-purple-700 transition">
                        Download PDF
                    </a>
                    <a href="{{ route('attendance.index') }}"
                        class="px-4 py-2 rounded-xl bg-gray-100 text-gray-700 font-bold text-sm hover:bg-gray-200 transition">
                        Kembali
                    </a>
                </div>
            </div>

            <div class="bg-white border border-gray-100 rounded-[2rem] shadow-sm overflow-hidden">
                <div class="p-6 border-b border-gray-50 bg-gray-50/30">
                    <h3 class="font-bold text-lg text-gray-800">Daftar Presensi</h3>
                </div>

                <div class="overflow-y-auto max-h-[500px]">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-gray-50 sticky top-0">
                            <tr>
                                <th class="p-4 font-bold text-gray-500">Tanggal</th>
                                <th class="p-4 font-bold text-gray-500">Check In</th>
                                <th class="p-4 font-bold text-gray-500">Check Out</th>
                                <th class="p-4 font-bold text-gray-500">Status</th>
                                <th class="p-4 font-bold text-gray-500">Lokasi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($attendances as $a)
                            <tr class="hover:bg-blue-50/50 transition">
                                <td class="p-4 font-medium">{{ \Carbon\Carbon::parse($a->tanggal)->format('d M Y') }}</td>
                                <td class="p-4 font-mono text-gray-700">{{ $a->check_in ?? '-' }}</td>
                                <td class="p-4 font-mono text-gray-700">{{ $a->check_out ?? '-' }}</td>
                                <td class="p-4">
                                    @php
                                    $st = strtolower(trim($a->status ?? ''));
                                    $ket = strtolower(trim($a->keterangan ?? ''));

                                    // ===== KODE UTAMA =====
                                    $code = '-';
                                    if ($st === 'telat') $code = 'T';
                                    elseif ($st === 'hadir') $code = 'H';
                                    elseif (in_array($st, ['ijin','izin','i'])) $code = 'I';
                                    elseif (in_array($st, ['sakit','s'])) $code = 'S';
                                    elseif ($st === 'dinas luar') $code = 'DL';
                                    elseif ($st === 'cuti') $code = 'CT';
                                    elseif (in_array($st, ['tanpa keterangan','alpha','tk'])) $code = 'TK';

                                    // ===== PC =====
                                    $isPC = ($ket === 'pulang cepat');

                                    // ===== TAP =====
                                    $isTAP = false;
                                    if (!empty($a->check_in) && empty($a->check_out)) {
                                    $date = \Carbon\Carbon::parse($a->tanggal, 'Asia/Jakarta');
                                    $dow = $date->dayOfWeekIso; // 1..7

                                    // weekday only
                                    if ($dow <= 5) {
                                        $endStr=$checkoutEndMap[$dow] ?? null;

                                        if (!empty($endStr)) {
                                        // batas waktu checkout hari itu
                                        $end=\Carbon\Carbon::parse($date->format('Y-m-d') . ' ' . $endStr, 'Asia/Jakarta');

                                        // TAP jika sekarang sudah lewat batas checkout
                                        $isTAP = now('Asia/Jakarta')->gt($end);
                                        }
                                        }
                                        }
                                        @endphp

                                        <div class="flex items-center gap-2">
                                            {{-- badge utama --}}
                                            <span class="px-2 py-1 bg-gray-100 text-gray-700 rounded-md text-xs font-bold">
                                                {{ $code }}
                                            </span>

                                            {{-- Pulang Cepat --}}
                                            @if($isPC)
                                            <span class="px-2 py-1 bg-orange-100 text-orange-700 rounded-md text-xs font-bold">
                                                PC
                                            </span>
                                            @endif

                                            {{-- Tidak Absen Pulang --}}
                                            @if($isTAP)
                                            <span class="px-2 py-1 bg-red-100 text-red-700 rounded-md text-xs font-bold">
                                                TAP
                                            </span>
                                            @endif
                                        </div>
                                </td>

                                <td class="p-4 text-xs">
                                    @if($a->location)
                                    <a href="{{ $a->location }}" target="_blank" class="text-blue-600 font-bold hover:underline">
                                        Buka Link Peta
                                    </a>
                                    @else
                                    <span class="text-gray-400 italic text-[10px]">Lokasi Kosong</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="p-6 text-center italic text-gray-400">
                                    Belum ada data presensi.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>