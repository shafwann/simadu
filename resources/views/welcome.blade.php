<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Sistem Monitoring Magang - Baperlitbang</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700,800" rel="stylesheet" />


        <link rel="icon" href="{{ asset('img/logobaperlitbang.png') }}" type="image/png">

        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            /* Animasi Marquee (Berjalan Kiri) */
            @keyframes marquee {
                0% { transform: translateX(0); }
                100% { transform: translateX(-50%); }
            }
            /* .animate-marquee {
                display: flex;
                width: max-content;
                animation: marquee 40s linear infinite; /* Kecepatan animasi */
            }
            .animate-marquee:hover {
                animation-play-state: paused; /* Berhenti saat di-hover */
            } */
                .tooltip-enter {
                opacity: 0;
                transform: translateY(5px);
            }

            .tooltip-enter-active {
                opacity: 1;
                transform: translateY(0);
                transition: opacity 200ms, transform 200ms;
            }
        </style>
    </head>
    <body class="bg-white text-gray-900 antialiased min-h-screen flex flex-col font-[Instrument_Sans]">

        <header class="w-full max-w-7xl mx-auto px-6 py-6 flex justify-between items-center border-b border-gray-50 bg-white/80 backdrop-blur-md sticky top-0 z-50">
            <div class="flex items-center gap-4">
                <img src="{{ asset('img/logobaperlitbang.png') }}" alt="Logo Baperlitbang" class="h-12 w-auto">
                <div class="hidden sm:block">
                    <h1 class="text-sm font-bold leading-none text-gray-800 uppercase tracking-wider">Bapperida</h1>
                    <p class="text-[10px] text-gray-500 uppercase">Kabupaten Karanganyar</p>
                </div>
            </div>
        </header>

        <main class="flex-grow flex flex-col justify-center">

            {{-- HERO SECTION --}}
            <div class="w-full max-w-7xl mx-auto px-6 py-12 lg:py-20 grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">

                <div class="space-y-8 text-center lg:text-left">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-lg bg-blue-50 text-blue-700 border border-blue-100">
                        <span class="text-[10px] font-bold uppercase tracking-[0.2em]">Monitoring Magang v1.0</span>
                    </div>

                    <h2 class="text-4xl lg:text-6xl font-extrabold text-gray-900 leading-tight tracking-tight">
                        Portal Presensi & <br>
                        <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-purple-600">Logbook Digital</span>
                    </h2>

                    <p class="text-lg text-gray-500 max-w-md mx-auto lg:mx-0 leading-relaxed">
                        Platform terintegrasi untuk mencatat kehadiran dan aktivitas kegiatan magang di Bapperida Kabupaten Karanganyar.
                    </p>

                    {{-- ✅ DISPLAY JUMLAH TOTAL MAHASISWA --}}
                    <div class="flex items-center justify-center lg:justify-start gap-4 py-2">
                        <div class="relative">
                            <div class="flex -space-x-3">
                                <div class="relative group">
                                    <!-- Counter Circle -->
                                    <div class="w-12 h-12 rounded-full border-3 border-white bg-gradient-to-br from-blue-500 to-blue-700 text-black-300 flex items-center justify-center text-sm font-bold shadow-lg hover:shadow-xl transition-shadow duration-200">
                                        {{ $total > 99 ? '99+' : $total }}
                                    </div>

                                    <!-- Tooltip untuk angka > 99 -->
                                    @if($total > 99)
                                        <div class="absolute z-50 invisible group-hover:visible opacity-0 group-hover:opacity-100 transition-all duration-300 bg-gray-900 text-white text-xs px-3 py-2 rounded-lg -top-10 left-1/2 transform -translate-x-1/2 whitespace-nowrap">
                                            <div class="flex items-center gap-1">
                                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                                                </svg>
                                                <span>Total: {{ number_format($total, 0, ',', '.') }} peserta</span>
                                            </div>
                                            <div class="absolute w-3 h-3 bg-gray-900 rotate-45 bottom-[-6px] left-1/2 -translate-x-1/2"></div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="text-left">
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-wide">
                                Peserta Magang Telah Bergabung
                            </p>
                            <p class="text-xs text-gray-500 mt-0.5">
                                {{ now()->format('F Y') }}
                            </p>

                            <!-- Tampilkan angka lengkap di bawah (opsional) -->
                            @if($total > 99)
                                <p class="text-xs text-blue-600 font-medium mt-1">
                                    <svg class="w-3 h-3 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                    </svg>
                                    Total {{ number_format($total, 0, ',', '.') }} peserta
                                </p>
                            @endif
                        </div>
                    </div>

                    <div class="flex flex-col sm:flex-row items-center justify-center lg:justify-start gap-4 pt-4">
                        <a href="{{ route('login') }}" class="w-full sm:w-auto px-10 py-4 bg-blue-700 text-white rounded-2xl font-bold shadow-xl shadow-blue-200 hover:bg-blue-800 transition transform hover:scale-105">
                            Login Aplikasi
                        </a>
                        <div class="flex items-center gap-2 text-gray-400 text-sm italic">
                            <svg class="w-4 h-4 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            Sistem GPS Aktif
                        </div>
                    </div>
                </div>

            <div class="relative hidden lg:block h-full">
                                {{-- Dekorasi Background --}}
                                <div class="absolute -inset-4 bg-gradient-to-tr from-blue-100 to-purple-50 rounded-[2rem] blur-2xl opacity-60"></div>

                                {{-- Card Tabel --}}
                                <div class="relative bg-white border border-gray-100 shadow-2xl rounded-2xl p-6 h-full flex flex-col justify-between">

                                    <div>
                                        <div class="flex items-center justify-between mb-4 border-b border-gray-50 pb-3">
                                            <h3 class="font-bold text-gray-800 flex items-center gap-2">
                                                <span class="w-2 h-6 bg-blue-600 rounded-full"></span>
                                                Peserta Terbaru
                                            </h3>
                                            <span class="text-[10px] bg-gray-100 text-gray-500 px-2 py-1 rounded-md">
                                                Page {{ $mahasiswa->currentPage() }}
                                            </span>
                                        </div>

                                        {{-- Tabel --}}
                                        <div class="overflow-hidden rounded-xl border border-gray-100">
                                            <table class="w-full text-sm text-left">
                                                <thead class="bg-gray-50 text-gray-500 font-bold uppercase text-[10px]">
                                                    <tr>
                                                        <th class="p-3">Nama</th>
                                                        <th class="p-3">Instansi</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="divide-y divide-gray-50">
                                                    @foreach($mahasiswa as $m)
                                                    <tr class="hover:bg-blue-50/50 transition duration-150">
                                                        <td class="p-3 font-bold text-gray-700 truncate max-w-[140px]" title="{{ $m->name }}">
                                                            {{ $m->name }}
                                                        </td>
                                                        <td class="p-3 text-gray-500 text-xs truncate max-w-[140px]" title="{{ $m->instansi }}">
                                                            {{ $m->instansi }}
                                                        </td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>

                                    {{-- Pagination Link --}}
                                    <div class="mt-4 pt-2 border-t border-gray-50 text-xs flex justify-center">
                                        {{-- Menggunakan simplePagination agar rapi di kotak kecil --}}
                                        {{ $mahasiswa->onEachSide(0)->links('pagination::simple-tailwind') }}
                                    </div>
                                </div>
                            </div>
            </div>
            </div>

        </main>

        <footer class="w-full max-w-7xl mx-auto px-6 py-8 text-center">
            <p class="text-xs text-gray-400 font-medium">
                &copy; {{ date('Y') }} BADAN PERENCANAAN, PENELITIAN DAN PENGEMBANGAN KABUPATEN KARANGANYAR
            </p>
        </footer>
    </body>
</html>
