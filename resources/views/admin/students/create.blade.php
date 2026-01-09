<x-app-layout>
    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-4xl mx-auto px-6 lg:px-8"> {{-- Lebar diperbesar jadi max-w-4xl --}}

            <div class="mb-8">
                <h1 class="text-3xl font-black text-gray-900">Tambah Peserta</h1>
                <p class="text-gray-500 mt-2">Daftarkan mahasiswa/siswa magang baru.</p>
            </div>

            <form method="POST" action="{{ route('admin.students.store') }}" class="space-y-8">
                @csrf

                {{-- CARD 1: DATA AKUN --}}
                <div class="p-8 bg-white border border-gray-200 rounded-[2rem] shadow-sm">
                    <h3 class="text-lg font-bold text-gray-800 mb-6 border-b pb-2">1. Informasi Akun</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <x-input-label for="name" :value="__('Nama Lengkap')" />
                            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="email" :value="__('Email')" />
                            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required />
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="password" :value="__('Password')" />
                            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required />
                        </div>

                        <div>
                            <x-input-label for="password_confirmation" :value="__('Konfirmasi Password')" />
                            <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required />
                        </div>

                        {{-- TAMBAHAN: Bidang / Divisi (di bawah Password) --}}
                        <div class="md:col-span-2">
                            <x-input-label for="bidang" :value="__('Bidang / Divisi')" />
                            <select name="bidang" id="bidang"
                                class="block mt-1 w-full border-gray-300 rounded-xl shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                required>
                                <option value="" disabled {{ old('bidang') ? '' : 'selected' }}>Pilih Bidang / Divisi</option>

                                {{-- Value dibuat sesuai ENUM migration (aman), label mengikuti permintaan kamu --}}
                                <option value="Penelitian, Pengembangan & Penyusunan Program"
                                    {{ old('bidang') == 'Penelitian, Pengembangan & Penyusunan Program' ? 'selected' : '' }}>
                                    Penelitian, Pengembangan & Penyusunan Program
                                </option>
                                <option value="Fisik & Prasarana" {{ old('bidang') == 'Fisik & Prasarana' ? 'selected' : '' }}>
                                    Fisik & Prasarana
                                </option>
                                <option value="Sosial & Budaya" {{ old('bidang') == 'Sosial & Budaya' ? 'selected' : '' }}>
                                    Sosial dan Budaya
                                </option>
                                <option value="Ekonomi" {{ old('bidang') == 'Ekonomi' ? 'selected' : '' }}>
                                    Ekonomi
                                </option>
                                <option value="Sekretariat" {{ old('bidang') == 'Sekretariat' ? 'selected' : '' }}>
                                    Sekretariat
                                </option>
                            </select>
                            <x-input-error :messages="$errors->get('bidang')" class="mt-2" />
                        </div>
                        {{-- END TAMBAHAN --}}

                        {{-- TAMBAHAN: Pembimbing Lapangan (di bawah Bidang / Divisi) --}}
                        <div class="md:col-span-2">
                            <x-input-label for="jabatan_pembimbing" :value="__('Jabatan Pembimbing Lapangan')" />
                            <x-text-input
                                id="jabatan_pembimbing"
                                class="block mt-1 w-full"
                                type="text"
                                name="jabatan_pembimbing"
                                :value="old('jabatan_pembimbing', 'Sekretaris')"
                                required
                            />
                            <x-input-error :messages="$errors->get('jabatan_pembimbing')" class="mt-2" />
                        </div>

                        <div class="md:col-span-2">
                            <x-input-label for="nama_pembimbing" :value="__('Nama Pembimbing Lapangan')" />
                            <x-text-input
                                id="nama_pembimbing"
                                class="block mt-1 w-full"
                                type="text"
                                name="nama_pembimbing"
                                :value="old('nama_pembimbing', 'Dr. Nining Indrastuti, S.T., M.T.')"
                                required
                            />
                            <x-input-error :messages="$errors->get('nama_pembimbing')" class="mt-2" />
                        </div>

                        <div class="md:col-span-2">
                            <x-input-label for="nip_pembimbing" :value="__('NIP Pembimbing Lapangan')" />
                            <x-text-input
                                id="nip_pembimbing"
                                class="block mt-1 w-full"
                                type="text"
                                name="nip_pembimbing"
                                :value="old('nip_pembimbing', 'NIP. 19761026 200604 2 022')"
                                required
                            />
                            <x-input-error :messages="$errors->get('nip_pembimbing')" class="mt-2" />
                        </div>
                        {{-- END TAMBAHAN --}}
                    </div>
                </div>

                {{-- CARD 2: DATA AKADEMIK --}}
                <div class="p-8 bg-white border border-gray-200 rounded-[2rem] shadow-sm">
                    <h3 class="text-lg font-bold text-gray-800 mb-6 border-b pb-2">2. Data Akademik & Instansi</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="md:col-span-2">
                            <x-input-label for="kegiatan" :value="__('Jenis Kegiatan')" />
                            <select name="kegiatan" id="kegiatan" class="block mt-1 w-full border-gray-300 rounded-xl shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="Magang Reguler">Magang Reguler (Mahasiswa)</option>
                                <option value="Praktik Kerja Lapangan (PKL)">Praktik Kerja Lapangan (PKL - SMK)</option>
                                <option value="Penelitian Skripsi">Penelitian / Skripsi</option>
                                <option value="MBKM">Program MBKM</option>
                            </select>
                        </div>

                        <div>
                            <x-input-label for="instansi" :value="__('Asal Instansi / Sekolah')" />
                            <x-text-input id="instansi" class="block mt-1 w-full" type="text" name="instansi" :value="old('instansi')" required placeholder="Contoh: Universitas Sebelas Maret" />
                        </div>

                        <div>
                            <x-input-label for="fakultas" :value="__('Fakultas (Opsional)')" />
                            <x-text-input id="fakultas" class="block mt-1 w-full" type="text" name="fakultas" :value="old('fakultas')" placeholder="Kosongi jika siswa SMK" />
                        </div>

                        <div>
                            <x-input-label for="prodi" :value="__('Program Studi / Jurusan')" />
                            <x-text-input id="prodi" class="block mt-1 w-full" type="text" name="prodi" :value="old('prodi')" required placeholder="Contoh: Teknik Informatika" />
                        </div>

                        <div>
                            <x-input-label for="nim" :value="__('NIM / NISN')" />
                            <x-text-input id="nim" class="block mt-1 w-full" type="text" name="nim" :value="old('nim')" required />
                        </div>
                    </div>
                </div>

                {{-- CARD 3: PERIODE MAGANG --}}
                <div class="p-8 bg-white border border-gray-200 rounded-[2rem] shadow-sm">
                    <h3 class="text-lg font-bold text-gray-800 mb-6 border-b pb-2">3. Periode Pelaksanaan</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <x-input-label for="tanggal_masuk" :value="__('Tanggal Mulai')" />
                            <input type="date" name="tanggal_masuk" class="block mt-1 w-full border-gray-300 rounded-xl shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                        </div>

                        <div>
                            <x-input-label for="tanggal_selesai" :value="__('Tanggal Selesai')" />
                            <input type="date" name="tanggal_selesai" class="block mt-1 w-full border-gray-300 rounded-xl shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-4 pt-4 pb-12">
                    <x-primary-button class="bg-blue-600 hover:bg-blue-700 py-3 px-8 text-base shadow-lg shadow-blue-200">
                        {{ __('Simpan Data') }}
                    </x-primary-button>

                    <a href="{{ route('admin.students.index') }}" class="text-gray-500 font-bold text-sm hover:text-gray-800 px-4">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
