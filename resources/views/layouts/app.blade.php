<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        {{-- 1. INI UNTUK JUDUL DI TAB --}}
        <title>Monitoring Magang Baperlitbang - Kab. Karanganyar</title>
        {{-- <title>{{ config('app.name', 'Monitoring Magang Bapperida - Kab. Karanganyar') }}</title> --}}

        {{-- 2. INI UNTUK GAMBAR (FAVICON) DI TAB --}}
        {{-- Pastikan nama file gambarnya sesuai dengan yang ada di folder public/img --}}
        <link rel="icon" href="{{ asset('img/logobaperlitbang.png') }}" type="image/png">

        {{-- Fonts & Scripts bawaan --}}
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased flex flex-col min-h-screen">
        {{-- ^^^ Tambahkan class flex flex-col min-h-screen agar footer selalu di bawah --}}

        <div class="bg-gray-100 flex-grow"> {{-- Tambahkan flex-grow --}}
            @include('layouts.navigation')

            @isset($header)
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <main>
                {{ $slot }}
            </main>
        </div>

        {{-- TARUH FOOTER DISINI --}}
        @include('layouts.footer')

    </body>
</html>
