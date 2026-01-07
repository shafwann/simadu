<x-app-layout>
    {{-- <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard Mahasiswa') }}
        </h2>
    </x-slot> --}}

    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-blue-100">
                <div class="p-8 text-gray-900 flex items-center justify-between">
                    <div>
                        <h3 class="text-2xl font-bold text-blue-600">Selamat Datang, {{ Auth::user()->name }}! 👋</h3>
                        <p class="text-gray-500 mt-1">Jangan lupa untuk melakukan presensi dan mengisi logbook hari ini.</p>
                    </div>
                    <div class="hidden md:block">
                        <span class="text-sm font-medium text-gray-400">Role: <span class="text-blue-500 uppercase">{{ Auth::user()->role }}</span></span>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                <a href="{{ Auth::user()->status === 'aktif' ? route('attendance.index') : '#' }}"
                class="bg-white p-6 rounded-2xl shadow-sm border {{ Auth::user()->status === 'aktif' ? 'border-gray-100 hover:border-blue-500' : 'border-gray-200 cursor-not-allowed' }} transition group {{ Auth::user()->status !== 'aktif' ? 'opacity-50' : '' }}">
                    <div class="flex items-center space-x-4">
                        <div class="p-3 {{ Auth::user()->status === 'aktif' ? 'bg-blue-100 text-blue-600 group-hover:bg-blue-600 group-hover:text-white' : 'bg-gray-100 text-gray-400' }} rounded-xl transition">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <h4 class="font-bold text-gray-800">Presensi</h4>
                            <p class="text-xs text-gray-500">Catat kehadiran harianmu</p>
                        </div>
                    </div>
                </a>


                <a href="{{ Auth::user()->status === 'aktif' ? route('logbook.index') : '#' }}"
                class="bg-white p-6 rounded-2xl shadow-sm border
                        {{ Auth::user()->status === 'aktif' ? 'border-gray-100 hover:border-indigo-500' : 'border-gray-200 cursor-not-allowed opacity-50' }}
                        transition group">
                    <div class="flex items-center space-x-4">
                        <div class="p-3 {{ Auth::user()->status === 'aktif' ? 'bg-indigo-100 text-indigo-600 group-hover:bg-indigo-600 group-hover:text-white' : 'bg-gray-100 text-gray-400' }} rounded-xl transition">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                        </div>
                        <div>
                            <h4 class="font-bold text-gray-800">Logbook</h4>
                            <p class="text-xs text-gray-500">Laporkan kegiatan magang</p>
                        </div>
                    </div>
                </a>


                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                    <div class="flex items-center justify-between">
                        <div>
                            <h4 class="font-bold text-gray-800 italic text-sm">Status Akun</h4>
                            @php $status = Auth::user()->status; @endphp
                            <p class="{{ $status === 'aktif' ? 'text-green-500' : 'text-gray-500' }} font-bold mt-1">
                                {{ ucfirst($status) }}
                            </p>
                        </div>
                        <div class="p-3 {{ $status === 'aktif' ? 'bg-green-100 text-green-600' : 'bg-gray-100 text-gray-400' }} rounded-xl">
                            @if($status === 'aktif')
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            @else
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Card Laporan hanya muncul kalau status TIDAK aktif --}}
                @if(Auth::user()->status == 'aktif')
                <a href="{{ route('laporan.index') }}"
                class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 hover:border-green-500 transition group">
                    <div class="flex items-center space-x-4">
                        <div class="p-3 bg-green-100 text-green-600 group-hover:bg-green-600 group-hover:text-white rounded-xl transition">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <h4 class="font-bold text-gray-800">Laporan</h4>
                            <p class="text-xs text-gray-500">Unggah & lihat laporan magang</p>
                        </div>
                    </div>
                </a>
                @endif

                {{-- ✅ TAMBAHAN: Card Penilaian Magang tampil kalau sudah dinilai --}}
                @if(Auth::user()->assessment)
                @php $ass = Auth::user()->assessment; @endphp
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 md:col-span-3">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                        <div>
                            <h4 class="font-bold text-gray-800 text-lg">Penilaian Akhir Magang</h4>
                            <p class="text-gray-500 mt-1 text-sm">
                                Nomor Sertifikat: <span class="font-bold text-gray-700">{{ $ass->certificate_no }}</span>
                            </p>
                            <p class="text-gray-400 text-xs mt-1">
                                Dinilai pada: {{ optional($ass->assessed_at)->format('d M Y') ?? '-' }}
                            </p>
                        </div>

                        <div class="flex flex-col md:flex-row md:items-center gap-3">
                            <div class="flex items-center gap-3">
                                <div class="px-4 py-2 rounded-xl bg-gray-50 border">
                                    <p class="text-[10px] uppercase font-bold text-gray-400">Total</p>
                                    <p class="text-lg font-black text-gray-900">{{ $ass->total }}</p>
                                </div>

                                <div class="px-4 py-2 rounded-xl bg-blue-50 border border-blue-100">
                                    <p class="text-[10px] uppercase font-bold text-blue-400">Rata-rata</p>
                                    <p class="text-lg font-black text-blue-700">{{ number_format($ass->average, 2, ',', '.') }}</p>
                                </div>

                                <div class="px-4 py-2 rounded-xl bg-green-50 border border-green-100">
                                    <p class="text-[10px] uppercase font-bold text-green-500">Grade</p>
                                    <p class="text-lg font-black text-green-700">{{ $ass->grade_letter }}</p>
                                </div>
                            </div>

                            {{-- ✅ TAMBAHAN: Tombol Download Sertifikat --}}
                            <div class="flex items-center gap-2">
                                <a href="{{ route('certificate.download') }}"
                                   class="px-4 py-2 rounded-xl bg-blue-600 text-white font-bold text-sm hover:bg-blue-700 transition shadow">
                                    Download Sertifikat
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 overflow-x-auto">
                        <table class="w-full text-sm text-left">
                            <thead class="bg-gray-50 sticky top-0">
                                <tr>
                                    <th class="p-4 font-bold text-gray-500">No</th>
                                    <th class="p-4 font-bold text-gray-500">Aspek Penilaian</th>
                                    <th class="p-4 font-bold text-center text-gray-500">Nilai</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                <tr>
                                    <td class="p-4 font-medium">1</td>
                                    <td class="p-4">Etika dan Kedisiplinan</td>
                                    <td class="p-4 text-center font-black text-gray-800">{{ $ass->aspek_1 }}</td>
                                </tr>
                                <tr>
                                    <td class="p-4 font-medium">2</td>
                                    <td class="p-4">Kemampuan Berkomunikasi dan Bekerjasama</td>
                                    <td class="p-4 text-center font-black text-gray-800">{{ $ass->aspek_2 }}</td>
                                </tr>
                                <tr>
                                    <td class="p-4 font-medium">3</td>
                                    <td class="p-4">Kemandirian</td>
                                    <td class="p-4 text-center font-black text-gray-800">{{ $ass->aspek_3 }}</td>
                                </tr>
                                <tr>
                                    <td class="p-4 font-medium">4</td>
                                    <td class="p-4">Kreativitas</td>
                                    <td class="p-4 text-center font-black text-gray-800">{{ $ass->aspek_4 }}</td>
                                </tr>
                                <tr>
                                    <td class="p-4 font-medium">5</td>
                                    <td class="p-4">Kemampuan menyelesaikan pekerjaan di bidangnya</td>
                                    <td class="p-4 text-center font-black text-gray-800">{{ $ass->aspek_5 }}</td>
                                </tr>
                                <tr>
                                    <td class="p-4 font-medium">6</td>
                                    <td class="p-4">Kemampuan mengidentifikasi dan memecahkan masalah</td>
                                    <td class="p-4 text-center font-black text-gray-800">{{ $ass->aspek_6 }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                @else
                {{-- ✅ TAMBAHAN: Kalau belum ada penilaian --}}
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 md:col-span-3">
                    <h4 class="font-bold text-gray-800 text-lg">Penilaian Akhir Magang</h4>
                    <p class="text-sm text-gray-500 mt-2 italic">
                        Penilaian belum tersedia. Nilai akan muncul setelah admin mengisi penilaian akhir magang.
                    </p>
                </div>
                @endif

            </div>
        </div>
    </div>
</x-app-layout>
