<x-app-layout>
    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-4xl mx-auto px-6 lg:px-8">

            {{-- HEADER --}}
            <div class="mb-8 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div>
                    <h1 class="text-3xl font-black text-gray-900">Edit Data Mahasiswa</h1>
                    <p class="text-gray-500 mt-2">Perbarui informasi akademik atau status magang.</p>
                </div>

                {{-- Badge Status di Header --}}
                @if($student->status === 'aktif')
                    <span class="px-4 py-2 bg-green-100 text-green-700 font-bold rounded-xl text-sm flex items-center gap-2">
                        <span class="w-2 h-2 bg-green-600 rounded-full animate-pulse"></span> Aktif
                    </span>
                @else
                    <span class="px-4 py-2 bg-gray-100 text-gray-600 font-bold rounded-xl text-sm">
                        Nonaktif / Selesai
                    </span>
                @endif
            </div>

            <form method="POST" action="{{ route('admin.students.update', $student->id) }}" class="space-y-8">
                @csrf
                @method('PUT') {{-- PENTING: Method PUT untuk Update --}}

                {{-- CARD 1: DATA AKUN --}}
                <div class="p-8 bg-white border border-gray-200 rounded-[2rem] shadow-sm">
                    <h3 class="text-lg font-bold text-gray-800 mb-6 border-b pb-2 flex items-center gap-2">
                        <span class="bg-blue-100 text-blue-600 w-6 h-6 flex items-center justify-center rounded-full text-xs">1</span>
                        Informasi Akun
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <x-input-label for="name" :value="__('Nama Lengkap')" />
                            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name', $student->name)" required />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="email" :value="__('Email')" />
                            <x-text-input id="email" class="block mt-1 w-full bg-gray-50" type="email" name="email" :value="old('email', $student->email)" required />
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>

                        {{-- Password Opsional --}}
                        <div class="md:col-span-2 bg-yellow-50 p-4 rounded-xl border border-yellow-100">
                            <h4 class="text-sm font-bold text-yellow-800 mb-2">Ganti Password (Opsional)</h4>
                            <p class="text-xs text-yellow-700 mb-4">Biarkan kosong jika tidak ingin mengubah password mahasiswa.</p>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <x-input-label for="password" :value="__('Password Baru')" />
                                    <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" autocomplete="new-password" />
                                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="password_confirmation" :value="__('Konfirmasi Password')" />
                                    <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" />
                                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- CARD 2: DATA AKADEMIK --}}
                <div class="p-8 bg-white border border-gray-200 rounded-[2rem] shadow-sm">
                    <h3 class="text-lg font-bold text-gray-800 mb-6 border-b pb-2 flex items-center gap-2">
                        <span class="bg-purple-100 text-purple-600 w-6 h-6 flex items-center justify-center rounded-full text-xs">2</span>
                        Data Akademik & Instansi
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="md:col-span-2">
                           <x-input-label for="kegiatan" :value="__('Jenis Kegiatan')" />

<input
    type="text"
    name="kegiatan"
    id="kegiatan"
    list="kegiatanList"
    value="{{ old('kegiatan', $student->kegiatan ?? '') }}"
    class="block mt-1 w-full border-gray-300 rounded-xl shadow-sm focus:border-purple-500 focus:ring-purple-500"
    placeholder="Ketik manual atau pilih dari daftar..."
    required
>

<datalist id="kegiatanList">
    <option value="Magang Reguler">
    <option value="Praktik Kerja Lapangan (PKL)">
    <option value="Penelitian Skripsi">
    <option value="MBKM">
</datalist>

                        </div>

                        <div>
                            <x-input-label for="instansi" :value="__('Asal Instansi / Sekolah')" />
                            <x-text-input id="instansi" class="block mt-1 w-full" type="text" name="instansi" :value="old('instansi', $student->instansi)" required />
                        </div>

                        <div>
                            <x-input-label for="fakultas" :value="__('Fakultas (Opsional)')" />
                            <x-text-input id="fakultas" class="block mt-1 w-full" type="text" name="fakultas" :value="old('fakultas', $student->fakultas)" />
                        </div>

                        <div>
                            <x-input-label for="prodi" :value="__('Program Studi / Jurusan')" />
                            <x-text-input id="prodi" class="block mt-1 w-full" type="text" name="prodi" :value="old('prodi', $student->prodi)" required />
                        </div>

                        <div>
                            <x-input-label for="nim" :value="__('NIM / NISN')" />
                            <x-text-input id="nim" class="block mt-1 w-full" type="text" name="nim" :value="old('nim', $student->nim)" required />
                        </div>
                    </div>
                </div>

                {{-- CARD 3: PERIODE & STATUS --}}
                <div class="p-8 bg-white border border-gray-200 rounded-[2rem] shadow-sm">
                    <h3 class="text-lg font-bold text-gray-800 mb-6 border-b pb-2 flex items-center gap-2">
                        <span class="bg-orange-100 text-orange-600 w-6 h-6 flex items-center justify-center rounded-full text-xs">3</span>
                        Periode & Penempatan
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {{-- Tanggal --}}
                        <div>
                            <x-input-label for="tanggal_masuk" :value="__('Tanggal Mulai')" />
                            <input type="date" name="tanggal_masuk" value="{{ old('tanggal_masuk', $student->tanggal_masuk) }}" class="block mt-1 w-full border-gray-300 rounded-xl shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                        </div>

                        <div>
                            <x-input-label for="tanggal_selesai" :value="__('Tanggal Selesai')" />
                            <input type="date" name="tanggal_selesai" value="{{ old('tanggal_selesai', $student->tanggal_selesai) }}" class="block mt-1 w-full border-gray-300 rounded-xl shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                        </div>

                        {{-- Bidang --}}
<div class="md:col-span-2 border-t pt-4 mt-2">
    <x-input-label for="bidang" :value="__('Divisi / Bidang Penempatan')" />

    <input
        type="text"
        name="bidang"
        id="bidang"
        list="bidangList"
        value="{{ old('bidang', $student->bidang) }}"
        class="block mt-1 w-full border-gray-300 rounded-xl shadow-sm
               focus:border-blue-500 focus:ring-blue-500 bg-gray-50"
        placeholder="Ketik manual atau pilih dari daftar..."
        required
    >

    <datalist id="bidangList">
        @foreach($bidangOptions as $bidang)
            <option value="{{ $bidang }}">
        @endforeach
    </datalist>

    <p class="text-xs text-gray-500 mt-1">
        Mengubah bidang akan memindahkan mahasiswa ke admin bidang tersebut.
    </p>
</div>


                        {{-- TAMBAHAN: Pembimbing Lapangan --}}
                        <div class="md:col-span-2 mt-4">
                            <x-input-label for="jabatan_pembimbing" :value="__('Jabatan Pembimbing Lapangan')" />
                            <x-text-input
                                id="jabatan_pembimbing"
                                class="block mt-1 w-full"
                                type="text"
                                name="jabatan_pembimbing"
                                value="{{ old('jabatan_pembimbing', $student->jabatan_pembimbing) }}"
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
                                value="{{ old('nama_pembimbing', $student->nama_pembimbing) }}"
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
                                value="{{ old('nip_pembimbing', $student->nip_pembimbing) }}"
                                required
                            />
                            <x-input-error :messages="$errors->get('nip_pembimbing')" class="mt-2" />
                        </div>
                        {{-- END TAMBAHAN --}}

                        {{-- Status Aktif/Nonaktif --}}
                        <div class="md:col-span-2">
                            <x-input-label for="status" :value="__('Status Mahasiswa')" />
                            <div class="mt-2 flex gap-4">
                                <label class="inline-flex items-center">
                                    <input type="radio" class="form-radio text-green-600 w-5 h-5" name="status" value="aktif" {{ $student->status == 'aktif' ? 'checked' : '' }}>
                                    <span class="ml-2 font-bold text-gray-700">Aktif</span>
                                </label>
                                <label class="inline-flex items-center">
                                    <input type="radio" class="form-radio text-gray-400 w-5 h-5" name="status" value="nonaktif" {{ $student->status == 'nonaktif' ? 'checked' : '' }}>
                                    <span class="ml-2 text-gray-600">Selesai / Nonaktif</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- TOMBOL AKSI --}}
                <div class="flex items-center gap-4 pt-4 pb-12">
                    <x-primary-button class="bg-blue-600 hover:bg-blue-700 py-3 px-8 text-base shadow-lg shadow-blue-200">
                        {{ __('Simpan Perubahan') }}
                    </x-primary-button>

                    <a href="{{ route('admin.students.index') }}" class="text-gray-500 font-bold text-sm hover:text-gray-800 px-4">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
