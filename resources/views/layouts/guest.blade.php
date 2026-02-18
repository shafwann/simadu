<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'SI-MaDu') }}</title>

        {{-- <linkpreconnect href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" /> --}}

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-50">
            {{-- <div class="mb-8"> <a href="https://baperlitbang.karanganyarkab.go.id">
                    <img src="{{ asset('img/logobaperlitbang.png') }}"
                         alt="Logo Baperlitbang Karanganyar"
                         class="h-24 w-auto mx-auto hover:opacity-90 transition" />
                </a>
            </div> --}}
            <div class="w-full sm:max-w-md mt-6 px-8 py-8 bg-white shadow-xl shadow-gray-100/50 border border-gray-100 overflow-hidden sm:rounded-[2rem]">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
