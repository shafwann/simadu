<x-guest-layout>
    <div class="mb-8 text-center">
        <div class="mb-8">
            <a href="/">
                {{-- LOGO DINAMIS --}}
                @if(isset($globalAbout) && $globalAbout->logo_path)
                    <img src="{{ asset('storage/' . $globalAbout->logo_path) }}"
                         alt="Logo Aplikasi"
                         class="h-24 w-auto mx-auto hover:opacity-90 transition" />
                @else
                    {{-- Fallback Logo Default --}}
                    <img src="{{ asset('img/logobaperlitbang.png') }}"
                         alt="Logo Baperlitbang Karanganyar"
                         class="h-24 w-auto mx-auto hover:opacity-90 transition" />
                @endif
            </a>
        </div>

        <h1 class="text-2xl font-black text-gray-900 leading-tight">
            {{-- DESKRIPSI (Baris 1) --}}
            {{ $globalAbout->app_description ?? 'Website Absensi Magang' }} <br>

            {{-- NAMA APP (Baris 2 - Warna Biru) --}}
            <span class="text-blue-600 uppercase">
                {{ $globalAbout->app_name ?? 'BAPPERIDA' }}
            </span>
        </h1>

        <p class="text-sm text-gray-400 mt-2 font-medium">
            Silakan masuk menggunakan akun yang telah diberikan oleh Admin.
        </p>
    </div>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div>
            <x-input-label for="email" :value="__('Email')" class="text-gray-700 font-bold" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" placeholder="nama@email.com" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" class="text-gray-700 font-bold" />
            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="current-password"
                            placeholder="••••••••" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="flex items-center justify-between mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500" name="remember">
                <span class="ms-2 text-sm text-gray-500 hover:text-gray-700">{{ __('Ingat Saya') }}</span>
            </label>

            @if (Route::has('password.request'))
                <a class="underline text-sm text-gray-400 hover:text-blue-600 rounded-md transition" href="{{ route('password.request') }}">
                    {{ __('Lupa Password?') }}
                </a>
            @endif
        </div>

        <div class="mt-6">
            <x-primary-button class="w-full justify-center bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-xl shadow-lg shadow-blue-200 transition transform hover:scale-[1.02]">
                {{ __('Masuk ke Portal') }}
            </x-primary-button>
        </div>

        <div class="mt-8 pt-6 border-t border-gray-100 text-center">
            <p class="text-xs text-gray-400">
                Belum memiliki akun? <br>
                Silakan hubungi <span class="font-bold text-gray-600">Admin {{ $globalAbout->app_name ?? 'Bapperida' }}</span> untuk pendaftaran.
            </p>
        </div>
    </form>
</x-guest-layout>
