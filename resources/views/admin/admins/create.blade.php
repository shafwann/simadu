<x-app-layout>
    <div class="py-12 bg-white min-h-screen">
        <div class="max-w-2xl mx-auto px-6 lg:px-8">

            <div class="mb-8">
                <h1 class="text-3xl font-black text-gray-900">Tambah Admin Baru</h1>
                <p class="text-gray-500 mt-2">Buat akun untuk admin divisi/bidang lain.</p>
            </div>

            <div class="p-8 bg-white border border-gray-200 rounded-[2rem] shadow-xl shadow-purple-50/50">
                <form method="POST" action="{{ route('admin.manage.store') }}" class="space-y-6">
                    @csrf

                    <div>
                        <x-input-label for="name" :value="__('Nama Lengkap Admin')" />
                        <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="bidang" :value="__('Divisi / Bidang yang Dikelola')" />
                        <select id="bidang" name="bidang" class="block mt-1 w-full border-gray-300 rounded-xl shadow-sm focus:border-purple-500 focus:ring-purple-500" required>
                            <option value="" disabled selected>-- Pilih Bidang --</option>
                            <option value="Ekonomi">Ekonomi</option>
                            <option value="Sekretariat">Sekretariat</option>
                            <option value="Sosial & Budaya">Sosial & Budaya</option>
                            <option value="Fisik & Prasarana">Fisik & Prasarana</option>
                            <option value="Penelitian, Pengembangan & Penyusunan Program">Penelitian, Pengembangan & Penyusunan Program</option>
                            {{-- Opsi Administrator Sistem sengaja tidak ditampilkan agar tidak sembarang membuat Super Admin --}}
                        </select>
                        <x-input-error :messages="$errors->get('bidang')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="email" :value="__('Email Login')" />
                        <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required />
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="password" :value="__('Password')" />
                        <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password" />
                    </div>

                    <div>
                        <x-input-label for="password_confirmation" :value="__('Konfirmasi Password')" />
                        <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required />
                    </div>

                    <div class="flex items-center gap-4 pt-4">
                        <x-primary-button class="bg-purple-900 hover:bg-black py-3 px-6">
                            {{ __('Simpan Admin') }}
                        </x-primary-button>

                        <a href="{{ route('admin.manage.index') }}" class="text-gray-500 font-bold text-sm hover:text-gray-800">
                            Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
