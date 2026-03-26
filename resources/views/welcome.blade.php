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
         @keyframes marquee2 {
  0%   { transform: translateX(0); }
  100% { transform: translateX(-100%); }
}

.marquee2 {
  width: 100%;
  overflow: hidden;
}

.marquee2__track {
  display: flex;
  width: max-content;
  animation: marquee2 280s linear infinite;
  will-change: transform;
}

.marquee2__track:hover {
  animation-play-state: paused;
}

.marquee2__group {
  display: flex;
  align-items: center;
}

.marquee2__item {
  display: inline-flex;
  align-items: center;
  padding: 0 34px;   /* bikin renggang */
  white-space: nowrap;
  font-size: 12px;
  font-weight: 800;
  text-transform: uppercase;
  opacity: 0.95;
}

/* ========================= */
/* TITIK HIJAU KELAP-KELIP  */
/* ========================= */
@keyframes pulse-green {
  0% {
    opacity: 0.4;
    transform: scale(0.8);
  }
  50% {
    opacity: 1;
    transform: scale(1.3);
  }
  100% {
    opacity: 0.4;
    transform: scale(0.8);
  }
}

.marquee2__dot {
  display: inline-block;
  width: 6px;
  height: 6px;
  margin-left: 18px;
  border-radius: 9999px;
  background-color: #22c55e; /* green-500 */
  box-shadow: 0 0 6px rgba(34, 197, 94, 0.9);
  animation: pulse-green 1.8s ease-in-out infinite;
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

       </header>

{{-- RUNNING TEXT (INSTANSI AKTIF) --}}
@if(isset($instansiAktif) && $instansiAktif->count() > 0)
<div class="bg-blue-900 text-white py-2 overflow-hidden relative z-40 border-b border-blue-800">
    <div class="marquee2">
        <div class="marquee2__track">
            {{-- GROUP 1 (dibikin panjang) --}}
            <div class="marquee2__group">
                @for($r = 0; $r < 35; $r++)
                    @foreach($instansiAktif as $i)
                        <span class="marquee2__item">
                            {{ $i->kategori_instansi }} <span class="marquee2__dot"></span>

                        </span>
                    @endforeach
                @endfor
            </div>

            {{-- GROUP 2 (duplikat persis) --}}
            <div class="marquee2__group" aria-hidden="true">
                @for($r = 0; $r < 4; $r++)
                    @foreach($instansiAktif as $i)
                        <span class="marquee2__item">
                            {{ $i->kategori_instansi }} <span class="marquee2__dot"></span>

                        </span>
                    @endforeach
                @endfor
            </div>
        </div>
    </div>
</div>
@endif


        <main class="flex-grow">

            {{-- HERO SECTION --}}
            <div class="relative bg-white pt-16 pb-20 lg:pt-24 lg:pb-28 overflow-hidden">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative z-10">

                    @if(isset($about) && $about->hero_image_path)
                        <div class="mb-10 flex justify-center">
                            <img
                                src="{{ asset('storage/' . $about->hero_image_path) }}"
                                alt="Welcome Image"
                                class="w-full max-w-5xl object-cover">
                        </div>
                    @else
                        {{-- FALLBACK TEXT --}}
                        <span class="inline-block py-1 px-3 rounded-full bg-blue-50 text-blue-600 text-xs font-bold uppercase mb-6">
                            Sistem Informasi Manajemen Magang
                        </span>

                        <h1 class="text-4xl sm:text-6xl font-black text-gray-900 mb-6">
                            Data Presensi & <br>
                            <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-purple-600">
                                Aktivitas Harian
                            </span>
                        </h1>
                    @endif


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
                            <div class="text-xs text-gray-500 font-bold uppercase mt-1">Unit Kerja Aktif</div>
                        </div>
                        <div class="p-4 rounded-2xl bg-gray-50 border border-gray-100">
                            <div class="text-3xl font-black text-green-600">{{ \App\Models\User::where('role', 'mahasiswa')->where('status','aktif')->count() }}</div>
                            <div class="text-xs text-gray-500 font-bold uppercase mt-1">Magang Aktif</div>
                        </div>
                        <div class="p-4 rounded-2xl bg-gray-50 border border-gray-100">
                            <div class="text-3xl font-black text-orange-600">{{ date('Y') }}</div>
                            <div class="text-xs text-gray-500 font-bold uppercase mt-1">Tahun Periode</div>
                        </div>
                    </div>
                </div>

        {{-- PESERTA MAGANG AKTIF PER INSTANSI --}}
        <div class="mt-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">

            {{-- HEADER SECTION --}}
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between mb-4 gap-4">
                <div>
                    <h3 class="text-base font-black uppercase tracking-wider text-gray-900 leading-tight">
                        Peserta Magang Aktif
                    </h3>
                    <p class="text-sm text-gray-500 mt-1 hidden sm:block">
                        Distribusi peserta berdasarkan instansi penempatan saat ini.
                    </p>
                </div>

                <span class="inline-flex items-center gap-2 bg-green-50 text-green-700 text-[10px] font-bold uppercase px-3 py-1.5 rounded-full border border-green-200 shadow-sm whitespace-nowrap">
                    <span class="relative flex h-2.5 w-2.5">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-green-600"></span>
                    </span>
                    Live Data
                </span>
            </div>

            {{-- CONTENT SECTION --}}
            <div class="bg-white border border-gray-200 rounded-2xl p-4 sm:p-6 shadow-[0_8px_30px_rgb(0,0,0,0.04)]">

                @if($pesertaAktifPerInstansi->count() > 0)

                    {{--
                    LOGIKA TAMPILAN BARU:
                    1. Mobile: 'flex' & 'overflow-x-auto' (Scrolling Samping Kiri-Kanan)
                    2. Desktop (md+): 'flex-wrap' & 'justify-center' (Tumpuk Rata Tengah)
                    --}}
                    <div class="
                        flex gap-3 overflow-x-auto pb-4 -mx-4 px-4 snap-x             {{-- Style HP: Scrolling --}}
                        md:flex-wrap md:justify-center md:pb-0 md:mx-0 md:px-0 md:overflow-visible {{-- Style PC: Center Wrap --}}
                        custom-scrollbar
                    ">
                        @foreach($pesertaAktifPerInstansi as $item)
                            <div class="
                                flex-shrink-0 snap-center min-w-[150px] w-[40vw] md:w-auto md:min-w-[180px]
                                group flex items-center justify-between p-3
                                bg-gray-50 rounded-xl border border-gray-100
                                hover:bg-white hover:border-blue-200 hover:shadow-md
                                transition-all duration-200 cursor-default
                            ">

                                <div class="flex items-center gap-3 overflow-hidden">
                                    {{-- Indikator Bulat --}}
                                    <div class="flex-shrink-0 w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center">
                                        <span class="text-blue-600 font-black text-xs">
                                            {{ substr($item->kategori_instansi, 0, 1) }}
                                        </span>
                                    </div>

                                    {{-- Nama Instansi --}}
                                    <span class="text-xs font-bold uppercase text-gray-700 group-hover:text-blue-800 transition-colors whitespace-nowrap">
                                        {{ $item->kategori_instansi }}
                                    </span>
                                </div>

                                {{-- Total Count Badge --}}
                                <span class="flex-shrink-0 ml-3 inline-flex items-center justify-center bg-blue-600 text-white text-[10px] font-black h-5 min-w-[20px] px-1 rounded-md shadow-sm group-hover:bg-blue-700 group-hover:scale-105 transition-all">
                                    {{ $item->total }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                @else
                    {{-- State Kosong --}}
                    <div class="text-center py-8 text-gray-400">
                        <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        Belum ada data peserta aktif.
                    </div>
                @endif
            </div>
        </div>

        </main>

        {{-- FOOTER --}}
        <footer style="background: linear-gradient(231deg, rgb(6, 147, 227) 0%, rgb(155, 81, 224) 94%);" class="text-white py-8 px-4 lg:px-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-8">
                    <div>
                        <div class="flex items-center gap-2 mb-4">
                            @if(isset($about) && $about->logo_path)
                                <img src="{{ asset('storage/' . $about->logo_path) }}" class="h-8 w-auto grayscale opacity-70 hover:grayscale-0 hover:opacity-100 transition">
                            @endif
                            <span class="font-bold text-white">{{ $about->app_name ?? 'SISTEM MAGANG' }}</span>
                        </div>
                        <p class="text-xs text-white leading-relaxed">
                            {{ $about->office_address ?? 'Alamat kantor belum disetting.' }}
                        </p>
                    </div>
                    <div>
                        <h4 class="font-bold text-white text-sm mb-4">Tautan</h4>
                        <ul class="space-y-2 text-xs text-white">
                            <li><a href="{{ route('login') }}" class="hover:text-blue-600">Login Peserta</a></li>
                            <li><a href="#" class="hover:text-blue-600">Panduan Magang</a></li>
                        </ul>
                    </div>
                    <div>
                        <h4 class="font-bold text-white text-sm mb-4">Kontak</h4>
                        <ul class="space-y-2 text-xs text-white">
                            <li>{{ $about->office_phone ?? '-' }}</li>
                            <li><a href="mailto:{{ $about->office_email }}" class="hover:text-blue-600">{{ $about->office_email ?? '-' }}</a></li>
                        </ul>
                    </div>
                    {{-- KOLOM 4: STATISTIK KUNJUNGAN --}}
                    <div class="space-y-3">
                        {{-- JUDUL (sejajar dengan Tautan & Kontak) --}}
                        <div class="flex items-center justify-between">
                            <h4 class="font-bold text-white text-sm">
                                Statistik Kunjungan
                            </h4>

                            {{-- LIVE BADGE --}}
                            <span class="inline-flex items-center gap-1 text-[9px] font-bold uppercase px-2 py-0.5 rounded-full
                                        bg-green-500 text-white">
                                <span class="relative flex h-1.5 w-1.5">
                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-white opacity-75"></span>
                                    <span class="relative inline-flex rounded-full h-1.5 w-1.5 bg-white"></span>
                                </span>
                                Live
                            </span>
                        </div>

                        {{-- BOX STAT --}}
                        <div class="bg-white text-gray-900 rounded-lg px-3 py-2 text-[11px]
                                    shadow-md w-[170px] border border-gray-200">
                            <div class="space-y-1">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Vis. today</span>
                                    <span class="font-black">{{ $visitsToday ?? 0 }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Visits</span>
                                    <span class="font-black">{{ $totalVisits ?? 0 }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Pag. today</span>
                                    <span class="font-black">{{ $pagesToday ?? 0 }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Pages</span>
                                    <span class="font-black">{{ $totalPages ?? 0 }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="border-t border-gray-100 pt-6 text-center">
                    <p class="text-xs text-white-400">
                        &copy; {{ date('Y') }} Aspa, Virda, Aura, Adnan. Hak Cipta Dilindungi.
                    </p>
                </div>
            </div>
        </footer>

    </body>
</html>
