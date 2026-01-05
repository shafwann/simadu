<x-app-layout>
    {{-- Kita set background halaman jadi abu-abu muda biar form-nya menonjol --}}
    <div class="py-12 bg-gray-50 min-h-screen text-gray-900">
        <div class="max-w-2xl mx-auto px-6 lg:px-8">

            {{-- HEADER JUDUL --}}
            <div class="mb-10 text-center md:text-left">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-purple-100 border border-purple-200 mb-4">
                    <span class="w-2 h-2 rounded-full bg-purple-600"></span>
                    <span class="text-xs font-bold text-purple-700 uppercase tracking-wider">Super Admin Area</span>
                </div>
                <h1 class="text-3xl font-black text-gray-900 tracking-tight">Tambah Admin Baru</h1>
                <p class="text-gray-500 mt-2">Delegasikan tugas dengan membuat akun admin untuk divisi lain.</p>
            </div>

            {{-- CARD FORM --}}
            <div class="p-8 bg-white border border-gray-200 rounded-[2rem] shadow-xl shadow-purple-50/50 relative overflow-hidden">

                {{-- Hiasan Background Abstrak (Opsional) --}}
                <div class="absolute top-0 right-0 -mt-10 -mr-10 w-40 h-40 bg-purple-50 rounded-full blur-3xl opacity-50 pointer-events-none"></div>

                <form method="POST" action="{{ route('admin.manage.store') }}" class="space-y-6 relative z-10">
                    @csrf

                    <div>
                        <x-input-label for="name" :value="__('Nama Lengkap Admin')" class="text-gray-700 font-bold" />
                        <x-text-input id="name" class="block mt-2 w-full border-gray-300 focus:border-purple-500 focus:ring-purple-500 rounded-xl"
                                      type="text" name="name" :value="old('name')" required autofocus placeholder="Contoh: Budi Santoso" />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="bidang" :value="__('Divisi / Bidang yang Dikelola')" class="text-gray-700 font-bold" />
                        <div class="relative mt-2">
                            <select id="bidang" name="bidang" class="block w-full border-gray-300 rounded-xl shadow-sm focus:border-purple-500 focus:ring-purple-500 appearance-none py-3 px-4" required>
                                <option value="" disabled selected class="text-gray-400">-- Pilih Bidang --</option>
                                <option value="Ekonomi">Ekonomi</option>
                                <option value="Sekretariat">Sekretariat</option>
                                <option value="Sosial & Budaya">Sosial & Budaya</option>
                                <option value="Fisik & Prasarana">Fisik & Prasarana</option>
                                <option value="Penelitian, Pengembangan & Penyusunan Program">Penelitian, Pengembangan & Penyusunan Program</option>
                            </select>
                            {{-- Panah Dropdown Custom --}}
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-gray-500">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </div>
                        </div>
                        <p class="text-xs text-gray-400 mt-2">*Admin ini hanya bisa melihat data mahasiswa di bidang yang dipilih.</p>
                        <x-input-error :messages="$errors->get('bidang')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="email" :value="__('Email Login')" class="text-gray-700 font-bold" />
                        <x-text-input id="email" class="block mt-2 w-full border-gray-300 focus:border-purple-500 focus:ring-purple-500 rounded-xl"
                                      type="email" name="email" :value="old('email')" required placeholder="email@bapperlitbang.com" />
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <x-input-label for="password" :value="__('Password')" class="text-gray-700 font-bold" />
                            <x-text-input id="password" class="block mt-2 w-full border-gray-300 focus:border-purple-500 focus:ring-purple-500 rounded-xl"
                                          type="password" name="password" required autocomplete="new-password" placeholder="Minimal 8 karakter" />
                            <x-input-error :messages="$errors->get('password')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="password_confirmation" :value="__('Konfirmasi Password')" class="text-gray-700 font-bold" />
                            <x-text-input id="password_confirmation" class="block mt-2 w-full border-gray-300 focus:border-purple-500 focus:ring-purple-500 rounded-xl"
                                          type="password" name="password_confirmation" required placeholder="Ulangi password" />
                            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                        </div>
                    </div>

                    <div class="flex items-center gap-4 pt-6 border-t border-gray-100 mt-6">
                        <button type="submit" class="inline-flex items-center gap-2 bg-purple-700 text-white px-6 py-3 rounded-xl font-bold hover:bg-purple-800 transition shadow-lg shadow-purple-200 hover:-translate-y-0.5">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            Simpan Admin
                        </button>

                        <a href="{{ route('admin.manage.index') }}" class="px-6 py-3 rounded-xl font-bold text-gray-500 hover:text-gray-800 hover:bg-gray-100 transition">
                            Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
