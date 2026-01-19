<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Tambah Instansi & Admin Baru') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

                <form method="POST" action="{{ route('master.agencies.store') }}">
                    @csrf

                    {{-- Kategori Instansi --}}
                    <div class="mb-4">
                        <x-input-label for="kategori_instansi" :value="__('Kode Kategori Instansi (Unik)')" />
                        <x-text-input id="kategori_instansi" class="block mt-1 w-full uppercase" type="text" name="kategori_instansi" :value="old('kategori_instansi')" required autofocus placeholder="Contoh: DISKOMINFO / BAPPERIDA / DISDIK" />
                        <x-input-error :messages="$errors->get('kategori_instansi')" class="mt-2" />
                        <p class="text-xs text-gray-500 mt-1">Gunakan satu kata singkatan instansi, tanpa spasi. Ini akan menjadi pembeda data.</p>
                    </div>

                    {{-- Nama Admin --}}
                    <div class="mb-4">
                        <x-input-label for="name" :value="__('Nama Admin')" />
                        <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required placeholder="Contoh: Admin Diskominfo" />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>

                    {{-- Email --}}
                    <div class="mb-4">
                        <x-input-label for="email" :value="__('Email Login')" />
                        <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required placeholder="admin@diskominfo.karanganyarkab.go.id" />
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    {{-- Password --}}
                    <div class="mb-4">
                        <x-input-label for="password" :value="__('Password')" />
                        <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required />
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>

                    <div class="flex items-center justify-end mt-4">
                        <a href="{{ route('master.agencies.index') }}" class="text-gray-600 hover:text-gray-900 mr-4">Batal</a>
                        <x-primary-button class="ml-4">
                            {{ __('Simpan & Buat') }}
                        </x-primary-button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>
