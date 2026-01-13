<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Sistem Monitoring Magang - Baperlitbang</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-white text-gray-900 antialiased min-h-screen flex flex-col">

        <header class="w-full max-w-7xl mx-auto px-6 py-6 flex justify-between items-center border-b border-gray-50">
            <div class="flex items-center gap-4">
                <img src="{{ asset('img/logobaperlitbang.png') }}" alt="Logo Baperlitbang" class="h-12 w-auto">
                <div class="hidden sm:block">
                    <h1 class="text-sm font-bold leading-none text-gray-800 uppercase tracking-wider">Bapperida</h1>
                    <p class="text-[10px] text-gray-500 uppercase">Kabupaten Karanganyar</p>
                </div>
            </div>

            {{-- @if (Route::has('login'))
                <nav class="flex items-center gap-4">
                    @auth
                        <a href="{{ url('/dashboard') }}" class="px-5 py-2 bg-blue-700 text-white rounded-xl text-sm font-bold shadow-md hover:bg-blue-800 transition">Dashboard &rarr;</a>
                    @else
                        <a href="{{ route('login') }}" class="text-sm font-semibold text-gray-600 hover:text-blue-700 transition">Log in</a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="px-5 py-2 border border-blue-700 text-blue-700 rounded-xl text-sm font-bold hover:bg-blue-50 transition">
                                Daftar
                            </a>
                        @endif
                    @endauth
                </nav>
            @endif --}}
        </header>

        <main class="flex-grow flex items-center justify-center py-12">
            <div class="max-w-7xl mx-auto px-6 grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">

                <div class="space-y-6 text-center lg:text-left">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-lg bg-blue-50 text-blue-700 border border-blue-100">
                        <span class="text-[10px] font-bold uppercase tracking-[0.2em]">Monitoring Magang v1.0</span>
                    </div>

                    <h2 class="text-4xl lg:text-5xl font-extrabold text-gray-900 leading-tight">
                        Portal Presensi & <br>
                        <span class="text-blue-700">Logbook Digital</span>
                    </h2>

                    <p class="text-md text-gray-500 max-w-md mx-auto lg:mx-0 leading-relaxed">
                        Selamat datang di aplikasi resmi Monitoring Magang Baperlitbang.
                        Gunakan sistem ini untuk mencatat kehadiran harian dan aktivitas kegiatan secara transparan.
                    </p>

                    <div class="flex flex-col sm:flex-row items-center justify-center lg:justify-start gap-4 pt-4">
                        <a href="{{ route('login') }}" class="w-full sm:w-auto px-10 py-4 bg-blue-700 text-white rounded-2xl font-bold shadow-xl shadow-blue-200 hover:bg-blue-800 transition transform hover:scale-105">
                            Login Presensi & Logbook
                        </a>
                        <div class="flex items-center gap-2 text-gray-400 text-sm italic">
                            <svg class="w-4 h-4 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            Pastikan Sistem Lokasi GPS Aktif
                        </div>
                    </div>
                </div>

                <div class="relative hidden lg:block">
                    <div class="absolute -inset-10 bg-blue-50 rounded-full blur-3xl opacity-50"></div>

                    <div class="relative bg-white border border-gray-100 shadow-2xl rounded-2xl p-6 space-y-4">
                        <div class="flex items-center gap-3 border-b border-gray-50 pb-3">
                            <div class="w-3 h-3 rounded-full bg-red-400"></div>
                            <div class="w-3 h-3 rounded-full bg-yellow-400"></div>
                            <div class="w-3 h-3 rounded-full bg-green-400"></div>
                        </div>
                        <div class="space-y-3">
                            <div class="h-4 w-1/3 bg-gray-100 rounded"></div>
                            <div class="h-10 w-full bg-gray-50 rounded-lg border border-gray-100"></div>
                            <div class="h-4 w-2/3 bg-gray-100 rounded"></div>
                            <div class="h-20 w-full bg-gray-50 rounded-lg border border-gray-100"></div>
                        </div>
                        <div class="h-10 w-1/4 bg-blue-700 rounded-lg ml-auto"></div>
                    </div>

                    <div class="absolute -bottom-6 -left-6 bg-white p-4 rounded-xl shadow-lg border border-blue-100 flex items-center gap-3 animate-bounce">
                        <div class="p-2 bg-green-100 text-green-600 rounded-lg">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            </svg>
                        </div>
                        <span class="text-xs font-bold text-gray-700 italic">Terverifikasi GPS</span>
                    </div>
                </div>

            </div>
        </main>

        <footer class="w-full max-w-7xl mx-auto px-6 py-8 border-t border-gray-50 text-center">
            <p class="text-xs text-gray-400 font-medium">
                &copy; {{ date('Y') }} BADAN PERENCANAAN, PENELITIAN DAN PENGEMBANGAN KABUPATEN KARANGANYAR
            </p>
        </footer>
    </body>
</html>
