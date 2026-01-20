<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        {{-- JUDUL DINAMIS --}}
        <title>Portal Magang - {{ $about->app_name ?? 'Kab. Karanganyar' }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700,800" rel="stylesheet" />

        {{-- FAVICON --}}
        @if(isset($about) && $about->logo_path)
            <link rel="icon" href="{{ asset('storage/' . $about->logo_path) }}" type="image/png">
        @else
            <link rel="icon" href="{{ asset('img/logobaperlitbang.png') }}" type="image/png">
        @endif

        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            /* Animasi Marquee Halus */
            @keyframes marquee {
                0% { transform: translateX(0); }
                100% { transform: translateX(-50%); }
            }
            .animate-marquee {
                display: flex;
                width: max-content;
                animation: marquee 250s linear infinite;
            }
            .animate-marquee:hover {
                animation-play-state: paused;
            }
            /* Custom Scrollbar untuk Tabel */
            .custom-scrollbar::-webkit-scrollbar {
                height: 6px;
            }
            .custom-scrollbar::-webkit-scrollbar-track {
                background: #f1f1f1;
            }
            .custom-scrollbar::-webkit-scrollbar-thumb {
                background: #cbd5e1;
                border-radius: 10px;
            }
            .custom-scrollbar::-webkit-scrollbar-thumb:hover {
                background: #94a3b8;
            }
        </style>
    </head>
    <body class="bg-gray-50 text-gray-900 antialiased min-h-screen flex flex-col font-[Instrument_Sans]">

        {{-- NAVBAR TRANSPARAN --}}
        <header class="w-full bg-white/80 backdrop-blur-md border-b border-gray-200 sticky top-0 z-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    {{-- LOGO --}}
                    @if(isset($about) && $about->logo_path)
                        <img src="{{ asset('storage/' . $about->logo_path) }}" alt="Logo" class="h-10 w-auto">
                    @else
                        <img src="{{ asset('img/logobaperlitbang.png') }}" alt="Logo Default" class="h-10 w-auto">
                    @endif

                    <div class="hidden sm:block leading-none">
                        <h1 class="text-sm font-black text-gray-800 uppercase tracking-wider">
                            {{ $about->app_name ?? 'SISTEM MAGANG' }}
                        </h1>
                        <p class="text-[10px] text-gray-500 font-bold uppercase mt-0.5">
                            {{ $about->agency_name ?? 'PEMERINTAH KAB. KARANGANYAR' }}
                        </p>
                    </div>
                </div>

                <div>
                    <a href="{{ route('login') }}" class="group relative inline-flex items-center gap-2 px-6 py-2.5 bg-gray-900 text-white text-sm font-bold rounded-full hover:bg-blue-600 transition-all duration-300 shadow-lg hover:shadow-blue-500/30">
                        <span>Login Portal</span>
                        <svg class="w-4 h-4 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                    </a>
                </div>
            </div>
        </header>

        {{-- RUNNING TEXT (MARQUEE) --}}
        @if(isset($marquee) && count($marquee) > 0)
        <div class="bg-blue-900 text-white py-2 overflow-hidden relative z-40 border-b border-blue-800">
            <div class="animate-marquee flex gap-8 items-center">
                @foreach($marquee as $m)
                    <div class="flex items-center gap-2 text-xs font-medium opacity-90">
                        <span class="w-1.5 h-1.5 rounded-full bg-green-400 animate-pulse"></span>
                        <span class="font-bold text-blue-200 uppercase">{{ $m->kategori_instansi ?? 'UMUM' }}</span>
                        <span class="text-gray-300">|</span>
                        <span class="uppercase">{{ $m->name }}</span>
                        <span class="text-blue-300">({{ Str::limit($m->instansi, 20) }})</span>
                    </div>
                @endforeach
                {{-- Duplikasi agar seamless loop --}}
                @foreach($marquee as $m)
                    <div class="flex items-center gap-2 text-xs font-medium opacity-90">
                        <span class="w-1.5 h-1.5 rounded-full bg-green-400 animate-pulse"></span>
                        <span class="font-bold text-blue-200 uppercase">{{ $m->kategori_instansi ?? 'UMUM' }}</span>
                        <span class="text-gray-300">|</span>
                        <span class="uppercase">{{ $m->name }}</span>
                        <span class="text-blue-300">({{ Str::limit($m->instansi, 20) }})</span>
                    </div>
                @endforeach
            </div>
        </div>
        @endif

        <main class="flex-grow">

            {{-- HERO SECTION --}}
            <div class="relative bg-white pt-16 pb-20 lg:pt-24 lg:pb-28 overflow-hidden">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative z-10">

                    <span class="inline-block py-1 px-3 rounded-full bg-blue-50 text-blue-600 text-xs font-bold tracking-widest uppercase mb-6 border border-blue-100">
                        Sistem Informasi Manajemen Magang
                    </span>

                    <h1 class="text-4xl sm:text-6xl font-black text-gray-900 tracking-tight leading-none mb-6">
                        Data Presensi & <br class="hidden sm:block">
                        <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-purple-600">
                            Aktivitas Harian
                        </span>
                    </h1>

                    <p class="text-lg text-gray-500 max-w-2xl mx-auto mb-10 leading-relaxed">
                        {{ $about->app_description ?? 'Platform terintegrasi untuk memantau kehadiran, laporan kegiatan, dan penilaian kinerja peserta magang di lingkungan Pemerintahan Kabupaten Karanganyar.' }}
                    </p>

                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 max-w-4xl mx-auto border-t border-gray-100 pt-10">
                        <div class="p-4 rounded-2xl bg-gray-50 border border-gray-100">
                            <div class="text-3xl font-black text-blue-600">{{ $total }}</div>
                            <div class="text-xs text-gray-500 font-bold uppercase mt-1">Total Peserta</div>
                        </div>
                        <div class="p-4 rounded-2xl bg-gray-50 border border-gray-100">
                            <div class="text-3xl font-black text-purple-600">{{ \App\Models\User::where('role', 'admin')->count() }}</div>
                            <div class="text-xs text-gray-500 font-bold uppercase mt-1">Instansi Aktif</div>
                        </div>
                        <div class="p-4 rounded-2xl bg-gray-50 border border-gray-100">
                            <div class="text-3xl font-black text-green-600">{{ \App\Models\User::where('role', 'mahasiswa')->where('status','aktif')->count() }}</div>
                            <div class="text-xs text-gray-500 font-bold uppercase mt-1">Sedang Magang</div>
                        </div>
                        <div class="p-4 rounded-2xl bg-gray-50 border border-gray-100">
                            <div class="text-3xl font-black text-orange-600">{{ date('Y') }}</div>
                            <div class="text-xs text-gray-500 font-bold uppercase mt-1">Tahun Periode</div>
                        </div>
                    </div>
                </div>

                {{-- Background Decoration --}}
                <div class="absolute top-0 left-1/2 w-full -translate-x-1/2 h-full z-0 pointer-events-none">
                    <div class="absolute top-20 left-10 w-72 h-72 bg-blue-200 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob"></div>
                    <div class="absolute top-20 right-10 w-72 h-72 bg-purple-200 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob animation-delay-2000"></div>
                </div>
            </div>

            {{-- DATA TABLE SECTION --}}
            <div class="bg-gray-50 py-16 border-t border-gray-200">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

                    <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-8 gap-4">
                        <div>
                            <h2 class="text-2xl font-bold text-gray-900">Direktori Peserta</h2>
                            <p class="text-sm text-gray-500 mt-1">Daftar mahasiswa/siswa yang terdaftar dalam sistem.</p>
                        </div>

                        {{-- Filter Dropdown (Opsional, tampilan saja) --}}
                        <div class="flex gap-2">
                            <span class="px-4 py-2 bg-white border border-gray-200 rounded-lg text-xs font-bold text-gray-500 shadow-sm flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-green-500"></span> Live Data
                            </span>
                        </div>
                    </div>

                    {{-- TABEL --}}
                    <div class="bg-white rounded-2xl shadow-xl shadow-gray-200/50 border border-gray-200 overflow-hidden">
                        <div class="overflow-x-auto custom-scrollbar">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="bg-gray-50/50 border-b border-gray-100 text-xs uppercase tracking-wider text-gray-500">
                                        <th class="px-6 py-4 font-bold">Peserta</th>
                                        <th class="px-6 py-4 font-bold">Asal Sekolah / Kampus</th>
                                        <th class="px-6 py-4 font-bold">Lokasi Magang</th>
                                        <th class="px-6 py-4 font-bold text-center">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @forelse($mahasiswa as $m)
                                    <tr class="hover:bg-blue-50/30 transition duration-150 group">
                                        {{-- NAMA & EMAIL --}}
                                        <td class="px-6 py-4">
                                            <div class="flex items-center gap-3">
                                                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-100 to-blue-200 text-blue-700 flex items-center justify-center font-bold text-sm">
                                                    {{ substr($m->name, 0, 1) }}
                                                </div>
                                                <div>
                                                    <div class="font-bold text-gray-900 group-hover:text-blue-600 transition">
                                                        {{ $m->name }}
                                                    </div>
                                                    <div class="text-xs text-gray-400">
                                                        {{ $m->prodi ?? 'Umum' }}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>

                                        {{-- ASAL SEKOLAH --}}
                                        <td class="px-6 py-4">
                                            <div class="text-sm font-medium text-gray-700">
                                                {{ $m->instansi }}
                                            </div>
                                            <div class="text-[10px] text-gray-400 uppercase mt-0.5">
                                                {{ $m->nim ?? 'No ID' }}
                                            </div>
                                        </td>

                                        {{-- LOKASI MAGANG (INSTANSI PEMERINTAH) --}}
                                        <td class="px-6 py-4">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-medium bg-purple-50 text-purple-700 border border-purple-100">
                                                {{ $m->kategori_instansi ?? 'BAPPERIDA' }}
                                            </span>
                                            <div class="text-[10px] text-gray-400 mt-1 pl-1">
                                                {{ Str::limit($m->bidang, 25) }}
                                            </div>
                                        </td>

                                        {{-- STATUS --}}
                                        <td class="px-6 py-4 text-center">
                                            @if($m->status === 'aktif')
                                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-green-100 text-green-700">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-green-500 animate-pulse"></span>
                                                    Aktif
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-gray-100 text-gray-500">
                                                    Selesai
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-12 text-center text-gray-400">
                                            <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                                            Belum ada data peserta magang.
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        {{-- PAGINATION --}}
                        <div class="px-6 py-4 border-t border-gray-100 bg-gray-50 flex items-center justify-between">
                            <div class="text-xs text-gray-500">
                                Menampilkan {{ $mahasiswa->firstItem() ?? 0 }} - {{ $mahasiswa->lastItem() ?? 0 }} dari {{ $mahasiswa->total() }} data
                            </div>
                            <div>
                                {{ $mahasiswa->onEachSide(1)->links('pagination::tailwind') }}
                            </div>
                        </div>
                    </div>

                </div>
            </div>

        </main>

        {{-- FOOTER --}}
        <footer class="bg-white border-t border-gray-200 pt-10 pb-6">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-8">
                    <div>
                        <div class="flex items-center gap-2 mb-4">
                            @if(isset($about) && $about->logo_path)
                                <img src="{{ asset('storage/' . $about->logo_path) }}" class="h-8 w-auto grayscale opacity-70 hover:grayscale-0 hover:opacity-100 transition">
                            @endif
                            <span class="font-bold text-gray-700">{{ $about->app_name ?? 'SISTEM MAGANG' }}</span>
                        </div>
                        <p class="text-xs text-gray-500 leading-relaxed">
                            {{ $about->office_address ?? 'Alamat kantor belum disetting.' }}
                        </p>
                    </div>
                    <div>
                        <h4 class="font-bold text-gray-900 text-sm mb-4">Tautan</h4>
                        <ul class="space-y-2 text-xs text-gray-500">
                            <li><a href="{{ route('login') }}" class="hover:text-blue-600">Login Peserta</a></li>
                            <li><a href="#" class="hover:text-blue-600">Panduan Magang</a></li>
                        </ul>
                    </div>
                    <div>
                        <h4 class="font-bold text-gray-900 text-sm mb-4">Kontak</h4>
                        <ul class="space-y-2 text-xs text-gray-500">
                            <li>{{ $about->office_phone ?? '-' }}</li>
                            <li>{{ $about->office_email ?? '-' }}</li>
                        </ul>
                    </div>
                </div>
                <div class="border-t border-gray-100 pt-6 text-center">
                    <p class="text-xs text-gray-400">
                        &copy; {{ date('Y') }} {{ $about->agency_name ?? 'Pemerintah Kabupaten Karanganyar' }}. All rights reserved.
                    </p>
                </div>
            </div>
        </footer>

    </body>
</html>
