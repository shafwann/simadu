<x-app-layout>
    <div class="py-12 bg-white min-h-screen">
        <div class="max-w-2xl mx-auto px-6 lg:px-8">

            <div class="mb-8">
                <h1 class="text-3xl font-black text-gray-900">Tambah Mahasiswa</h1>
                <p class="text-gray-500 mt-2">Daftarkan peserta magang baru ke dalam sistem.</p>
            </div>

            <div class="p-8 bg-white border border-gray-200 rounded-[2rem] shadow-xl shadow-blue-50/50">
                <form method="POST" action="{{ route('admin.students.store') }}" class="space-y-6">
                    @csrf

                    <div>
                        <x-input-label for="name" :value="__('Nama Lengkap')" />
                        <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <x-input-label for="nim" :value="__('NIM / NISN')" />
                            <x-text-input id="nim" class="block mt-1 w-full" type="text" name="nim" :value="old('nim')" required placeholder="Contoh: 210103040" />
                            <x-input-error :messages="$errors->get('nim')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="prodi" :value="__('Program Studi / Jurusan')" />
                            <x-text-input id="prodi" class="block mt-1 w-full" type="text" name="prodi" :value="old('prodi')" required placeholder="Contoh: Informatika" />
                            <x-input-error :messages="$errors->get('prodi')" class="mt-2" />
                        </div>
                    </div>

                    <div>
                        <x-input-label for="instansi" :value="__('Asal Instansi / Universitas')" />
                        <x-text-input id="instansi" class="block mt-1 w-full" type="text" name="instansi" :value="old('instansi')" required placeholder="Contoh: Universitas Sebelas Maret" />
                        <x-input-error :messages="$errors->get('instansi')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="email" :value="__('Email')" />
                        <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required />
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="password" :value="__('Password Sementara')" />
                        <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password" />
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="password_confirmation" :value="__('Konfirmasi Password')" />
                        <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required />
                        <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                    </div>

                    <div class="flex items-center gap-4 pt-4">
                        <x-primary-button class="bg-blue-600 hover:bg-blue-700 py-3 px-6 text-sm">
                            {{ __('Simpan Data') }}
                        </x-primary-button>

                        <a href="{{ route('admin.students.index') }}" class="text-gray-500 font-bold text-sm hover:text-gray-800">
                            Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
