<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Super Admin Instansi') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

                <form method="POST" action="{{ route('master.agencies.update', $agency->id) }}">
                    @csrf
                    @method('PUT')

                    {{-- Kategori Instansi (READ ONLY) --}}
                    <div class="mb-4">
                        <x-input-label for="kategori_instansi" :value="__('Kode Kategori (Tidak dapat diubah)')" />
                        <x-text-input id="kategori_instansi" class="block mt-1 w-full bg-gray-100 text-gray-500 cursor-not-allowed"
                                      type="text" :value="$agency->kategori_instansi" readonly />
                        <p class="text-xs text-red-400 mt-1">
                            * Kategori tidak bisa diubah karena terikat dengan data mahasiswa. Jika salah, silakan hapus dan buat baru.
                        </p>
                    </div>

                    {{-- Nama Admin --}}
                    <div class="mb-4">
                        <x-input-label for="name" :value="__('Nama Super Admin')" />
                        <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name', $agency->name)" required />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>

                    {{-- Email --}}
                    <div class="mb-4">
                        <x-input-label for="email" :value="__('Email Login')" />
                        <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email', $agency->email)" required />
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    {{-- Password (Opsional) --}}
                    <div class="mb-4">
                        <x-input-label for="password" :value="__('Password Baru (Opsional)')" />
                        <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" placeholder="Kosongkan jika tidak ingin mengganti password" />
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>

                    <div class="flex items-center justify-end mt-4">
                        <a href="{{ route('master.agencies.index') }}" class="text-gray-600 hover:text-gray-900 mr-4">Batal</a>
                        <x-primary-button class="ml-4 bg-yellow-600 hover:bg-yellow-700">
                            {{ __('Simpan Perubahan') }}
                        </x-primary-button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>
