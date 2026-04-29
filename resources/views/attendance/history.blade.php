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
                                    <td class="p-4 font-medium">
                                        {{ \Carbon\Carbon::parse($a->tanggal)->format('d M Y') }}</td>
                                    <td class="p-4 font-mono text-gray-700">{{ $a->check_in ?? '-' }}</td>
                                    <td class="p-4 font-mono text-gray-700">{{ $a->check_out ?? '-' }}</td>
                                    <td class="p-4">
                                        @php
                                            $stRaw = $a->status ?? '-';
                                            $ketRaw = $a->keterangan ?? null;
                                            $st = strtoupper(trim((string) $stRaw));
                                            $ket = strtoupper(trim((string) $ketRaw));

                                            $code = '-';
                                            if ($st === '' || $st === '-') {
                                                $code = '-';
                                            } elseif (in_array($st, ['ALPHA', 'TANPA KETERANGAN', 'TK'])) {
                                                $code = 'TK';
                                            } elseif ($st === 'HADIR') {
                                                $code = 'H';
                                            } elseif ($st === 'TELAT') {
                                                $code = 'T';
                                            } elseif (in_array($st, ['I', 'IZIN', 'IJIN'])) {
                                                $code = 'I';
                                            } elseif (in_array($st, ['S', 'SAKIT'])) {
                                                $code = 'S';
                                            } elseif (in_array($st, ['DL', 'DINAS LUAR'])) {
                                                $code = 'DL';
                                            } elseif (in_array($st, ['CT', 'CUTI'])) {
                                                $code = 'CT';
                                            }

                                            $isPC = $ket === 'PULANG CEPAT';
                                            $isTAP = !empty($a->check_in) && empty($a->check_out);

                                            $extras = [];
                                            if ($isPC) {
                                                $extras[] = 'PC';
                                            }
                                            if ($isTAP) {
                                                $extras[] = 'TAP';
                                            }

                                            $statusFinal = $code;
                                            if (!empty($extras)) {
                                                $statusFinal = $code . ',' . implode(',', $extras);
                                            }

                                            $parts = array_filter(array_map('trim', explode(',', $statusFinal)));
                                        @endphp

                                        @foreach ($parts as $p)
                                            @if ($p === 'H')
                                                <span
                                                    class="px-2 py-1 bg-green-100 text-green-700 rounded-md text-xs font-bold">H</span>
                                            @elseif($p === 'T')
                                                <span
                                                    class="px-2 py-1 bg-yellow-100 text-yellow-700 rounded-md text-xs font-bold">T</span>
                                            @elseif($p === 'I')
                                                <span
                                                    class="px-2 py-1 bg-blue-100 text-blue-700 rounded-md text-xs font-bold">I</span>
                                            @elseif($p === 'S')
                                                <span
                                                    class="px-2 py-1 bg-amber-100 text-amber-700 rounded-md text-xs font-bold">S</span>
                                            @elseif($p === 'DL')
                                                <span
                                                    class="px-2 py-1 bg-purple-100 text-purple-700 rounded-md text-xs font-bold">DL</span>
                                            @elseif($p === 'CT')
                                                <span
                                                    class="px-2 py-1 bg-pink-100 text-pink-700 rounded-md text-xs font-bold">CT</span>
                                            @elseif($p === 'TK')
                                                <span
                                                    class="px-2 py-1 bg-gray-100 text-gray-700 rounded-md text-xs font-bold">TK</span>
                                            @elseif($p === 'PC')
                                                <span
                                                    class="px-2 py-1 bg-orange-100 text-orange-700 rounded-md text-xs font-bold">PC</span>
                                            @elseif($p === 'TAP')
                                                <span
                                                    class="px-2 py-1 bg-red-100 text-red-700 rounded-md text-xs font-bold">TAP</span>
                                            @else
                                                <span
                                                    class="px-2 py-1 bg-gray-100 text-gray-700 rounded-md text-xs font-bold">{{ $p }}</span>
                                            @endif
                                        @endforeach
                                    </td>

                                    <td class="p-4 text-xs">
                                        @if ($a->location_url_formatted)
                                            <a href="{{ $a->location_url_formatted }}" target="_blank"
                                                class="text-blue-600 font-bold hover:underline">
                                                {{ $a->location_name_formatted ?? 'Lihat Lokasi' }}
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
