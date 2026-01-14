<x-app-layout>
    <x-slot name="header">
        <h2 class="font-black text-xl text-gray-800 leading-tight">
            About Instansi
        </h2>
        <p class="text-sm text-gray-500 mt-1">
            Data ini hanya bisa diubah oleh <span class="font-semibold">Super Admin (Administrator Sistem)</span>.
        </p>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="mb-4 p-4 rounded-xl bg-green-50 text-green-700 font-semibold">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-2xl p-6">
                <form method="POST" action="{{ route('admin.about.update') }}" class="space-y-5" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    {{-- ✅ BRANDING APLIKASI (JUDUL & LOGO) --}}
                    <div class="bg-blue-50 p-6 rounded-2xl border border-blue-100 mb-6">
                        <h3 class="font-bold text-lg text-blue-800 mb-4">Branding Aplikasi (Navbar & Judul)</h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="app_name" value="Nama Aplikasi (Judul Utama)" />
                                <x-text-input id="app_name" name="app_name" type="text" class="mt-1 block w-full font-bold"
                                    value="{{ old('app_name', $about->app_name) }}" placeholder="Bapperida" />
                            </div>

                            <div>
                                <x-input-label for="agency_name" value="Nama Instansi (Sub Judul)" />
                                <x-text-input id="agency_name" name="agency_name" type="text" class="mt-1 block w-full"
                                    value="{{ old('agency_name', $about->agency_name) }}" placeholder="Kab. Karanganyar" />
                            </div>

                            <div class="md:col-span-2">
                                <x-input-label for="app_description" value="Deskripsi Singkat (Muncul di bawah judul)" />
                                <x-text-input id="app_description" name="app_description" type="text" class="mt-1 block w-full text-sm"
                                    value="{{ old('app_description', $about->app_description) }}" placeholder="Sistem Monitoring Magang" />
                            </div>

                            <div class="md:col-span-2">
                                <x-input-label for="logo" value="Logo Aplikasi (Format PNG/JPG, Transparan lebih baik)" />
                                <div class="flex items-center gap-4 mt-2">
                                    @if($about->logo_path)
                                        <div class="p-2 bg-white rounded border">
                                            <img src="{{ asset('storage/' . $about->logo_path) }}" class="h-12 w-auto">
                                        </div>
                                    @endif
                                    <input type="file" name="logo" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- NAMA INSTANSI --}}
                    <div class="mb-6">
                        <x-input-label for="nama_instansi" value="Nama Instansi" />
                        <x-text-input
                            id="nama_instansi"
                            name="nama_instansi"
                            type="text"
                            class="mt-1 block w-full"
                            value="{{ old('nama_instansi', $about->nama_instansi) }}"
                            required
                        />
                        <x-input-error class="mt-2" :messages="$errors->get('nama_instansi')" />
                    </div>

                    {{-- KEPALA INSTANSI --}}
                    <div class="mb-6">
                        <x-input-label for="kepala_instansi" value="Kepala Instansi" />
                        <x-text-input
                            id="kepala_instansi"
                            name="kepala_instansi"
                            type="text"
                            class="mt-1 block w-full"
                            value="{{ old('kepala_instansi', $about->kepala_instansi) }}"
                            required
                        />
                        <x-input-error class="mt-2" :messages="$errors->get('kepala_instansi')" />
                    </div>

                    {{-- NIP --}}
                    <div class="mb-6">
                        <x-input-label for="nip_kepala_instansi" value="NIP Kepala Instansi" />
                        <x-text-input
                            id="nip_kepala_instansi"
                            name="nip_kepala_instansi"
                            type="text"
                            class="mt-1 block w-full"
                            value="{{ old('nip_kepala_instansi', $about->nip_kepala_instansi) }}"
                            required
                        />
                        <x-input-error class="mt-2" :messages="$errors->get('nip_kepala_instansi')" />
                    </div>

                    {{-- JAM OPERASIONAL (DESKRIPSI) --}}
                    <div>
                        <x-input-label for="jam_operasional" value="Jam Operasional" />
                        <p class="mt-1 text-sm text-gray-500">
                            Digunakan untuk mengatur <span class="font-semibold">jam operasional instansi</span> yang akan
                            ditampilkan pada <span class="font-semibold">footer aplikasi</span>.
                            Gunakan tanda <span class="font-semibold">koma (,)</span> untuk memisahkan jam operasional
                            ke baris berikutnya.
                        </p>


                        <x-text-input
                            id="jam_operasional"
                            name="jam_operasional"
                            type="text"
                            class="mt-1 block w-full"
                            placeholder="Contoh: Senin–Jumat 08.00–16.00"
                            value="{{ old('jam_operasional', $about->jam_operasional) }}"
                            required
                        />
                        <x-input-error class="mt-2" :messages="$errors->get('jam_operasional')" />

                        <p class="mt-1 text-xs text-gray-400">
                            Contoh: Senin–Kamis 08.00–16.00, Jumat 08.00–11.30
                        </p>

                    </div>

                    {{-- ================= LOKASI KANTOR (ABSENSI GPS) ================= --}}
                    <div class="pt-6 border-t">
                        <h3 class="font-black text-lg text-gray-800">
                            Lokasi Kantor (Absensi GPS)
                        </h3>
                        <p class="text-sm text-gray-500 mt-1">
                            Digunakan untuk menentukan titik koordinat dan radius absensi mahasiswa.
                        </p>
                    </div>

                    {{-- ✅ TAMBAHAN: NAMA LOKASI --}}
                    <div class="mt-4">
                        <x-input-label for="office_name" value="Nama Lokasi Kantor (Default Absensi)" />
                        <x-text-input
                            id="office_name"
                            name="office_name"
                            type="text"
                            class="mt-1 block w-full uppercase"
                            placeholder="Contoh: KANTOR BAPPERIDA"
                            value="{{ old('office_name', $about->office_name) }}"
                        />
                        <x-input-error class="mt-2" :messages="$errors->get('office_name')" />
                        <p class="mt-1 text-xs text-gray-400">
                            Nama ini akan otomatis terisi di absensi mahasiswa jika mereka check-in di area kantor.
                        </p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <x-input-label value="Latitude Kantor" />
                            <x-text-input
                                name="office_latitude"
                                type="text"
                                class="mt-1 block w-full"
                                placeholder="-7.5949176"
                                value="{{ old('office_latitude', $about->office_latitude) }}"
                            />
                            <x-input-error class="mt-2" :messages="$errors->get('office_latitude')" />
                        </div>

                        <div>
                            <x-input-label value="Longitude Kantor" />
                            <x-text-input
                                name="office_longitude"
                                type="text"
                                class="mt-1 block w-full"
                                placeholder="110.9406551"
                                value="{{ old('office_longitude', $about->office_longitude) }}"
                            />
                            <x-input-error class="mt-2" :messages="$errors->get('office_longitude')" />
                        </div>

                        <div>
                            <x-input-label value="Radius Absensi (meter)" />
                            <x-text-input
                                name="radius_meter"
                                type="number"
                                min="10"
                                class="mt-1 block w-full"
                                value="{{ old('radius_meter', $about->radius_meter ?? 100) }}"
                                required
                            />
                            <x-input-error class="mt-2" :messages="$errors->get('radius_meter')" />
                        </div>
                    </div>

                    {{-- ================= JAM CHECK-IN ================= --}}
                    <div class="pt-6 border-t">
                        <h3 class="font-black text-lg text-gray-800">
                            Jam Check-in Mahasiswa (Per Hari)
                        </h3>
                        <p class="text-sm text-gray-500 mt-1">
                            Digunakan untuk membatasi waktu absen masuk mahasiswa.
                        </p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        {{-- SENIN --}}
                        <div>
                            <x-input-label value="Senin (Mulai)" />
                            <x-text-input name="senin_mulai" type="time" class="mt-1 block w-full"
                                value="{{ old('senin_mulai', $about->senin_mulai) }}" required />
                            <x-input-error class="mt-2" :messages="$errors->get('senin_mulai')" />
                        </div>
                        <div>
                            <x-input-label value="Senin (Selesai)" />
                            <x-text-input name="senin_selesai" type="time" class="mt-1 block w-full"
                                value="{{ old('senin_selesai', $about->senin_selesai) }}" required />
                            <x-input-error class="mt-2" :messages="$errors->get('senin_selesai')" />
                        </div>

                        {{-- SELASA --}}
                        <div>
                            <x-input-label value="Selasa (Mulai)" />
                            <x-text-input name="selasa_mulai" type="time" class="mt-1 block w-full"
                                value="{{ old('selasa_mulai', $about->selasa_mulai) }}" required />
                            <x-input-error class="mt-2" :messages="$errors->get('selasa_mulai')" />
                        </div>
                        <div>
                            <x-input-label value="Selasa (Selesai)" />
                            <x-text-input name="selasa_selesai" type="time" class="mt-1 block w-full"
                                value="{{ old('selasa_selesai', $about->selasa_selesai) }}" required />
                            <x-input-error class="mt-2" :messages="$errors->get('selasa_selesai')" />
                        </div>

                        {{-- RABU --}}
                        <div>
                            <x-input-label value="Rabu (Mulai)" />
                            <x-text-input name="rabu_mulai" type="time" class="mt-1 block w-full"
                                value="{{ old('rabu_mulai', $about->rabu_mulai) }}" required />
                            <x-input-error class="mt-2" :messages="$errors->get('rabu_mulai')" />
                        </div>
                        <div>
                            <x-input-label value="Rabu (Selesai)" />
                            <x-text-input name="rabu_selesai" type="time" class="mt-1 block w-full"
                                value="{{ old('rabu_selesai', $about->rabu_selesai) }}" required />
                            <x-input-error class="mt-2" :messages="$errors->get('rabu_selesai')" />
                        </div>

                        {{-- KAMIS --}}
                        <div>
                            <x-input-label value="Kamis (Mulai)" />
                            <x-text-input name="kamis_mulai" type="time" class="mt-1 block w-full"
                                value="{{ old('kamis_mulai', $about->kamis_mulai) }}" required />
                            <x-input-error class="mt-2" :messages="$errors->get('kamis_mulai')" />
                        </div>
                        <div>
                            <x-input-label value="Kamis (Selesai)" />
                            <x-text-input name="kamis_selesai" type="time" class="mt-1 block w-full"
                                value="{{ old('kamis_selesai', $about->kamis_selesai) }}" required />
                            <x-input-error class="mt-2" :messages="$errors->get('kamis_selesai')" />
                        </div>

                        {{-- JUMAT --}}
                        <div>
                            <x-input-label value="Jumat (Mulai)" />
                            <x-text-input name="jumat_mulai" type="time" class="mt-1 block w-full"
                                value="{{ old('jumat_mulai', $about->jumat_mulai) }}" required />
                            <x-input-error class="mt-2" :messages="$errors->get('jumat_mulai')" />
                        </div>
                        <div>
                            <x-input-label value="Jumat (Selesai)" />
                            <x-text-input name="jumat_selesai" type="time" class="mt-1 block w-full"
                                value="{{ old('jumat_selesai', $about->jumat_selesai) }}" required />
                            <x-input-error class="mt-2" :messages="$errors->get('jumat_selesai')" />
                        </div>
                    </div>

                    {{-- ================= JAM CHECK-OUT ================= --}}
                    <div class="pt-6 border-t">
                        <h3 class="font-black text-lg text-gray-800">
                            Jam Check-out Mahasiswa (Per Hari)
                        </h3>
                        <p class="text-sm text-gray-500 mt-1">
                            Digunakan untuk membatasi waktu absen pulang mahasiswa.
                        </p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        {{-- SENIN --}}
                        <div>
                            <x-input-label value="Senin (Mulai Check-out)" />
                            <x-text-input name="senin_checkout_mulai" type="time" class="mt-1 block w-full"
                                value="{{ old('senin_checkout_mulai', $about->senin_checkout_mulai) }}" required />
                            <x-input-error class="mt-2" :messages="$errors->get('senin_checkout_mulai')" />
                        </div>
                        <div>
                            <x-input-label value="Senin (Selesai Check-out)" />
                            <x-text-input name="senin_checkout_selesai" type="time" class="mt-1 block w-full"
                                value="{{ old('senin_checkout_selesai', $about->senin_checkout_selesai) }}" required />
                            <x-input-error class="mt-2" :messages="$errors->get('senin_checkout_selesai')" />
                        </div>

                        {{-- SELASA --}}
                        <div>
                            <x-input-label value="Selasa (Mulai Check-out)" />
                            <x-text-input name="selasa_checkout_mulai" type="time" class="mt-1 block w-full"
                                value="{{ old('selasa_checkout_mulai', $about->selasa_checkout_mulai) }}" required />
                            <x-input-error class="mt-2" :messages="$errors->get('selasa_checkout_mulai')" />
                        </div>
                        <div>
                            <x-input-label value="Selasa (Selesai Check-out)" />
                            <x-text-input name="selasa_checkout_selesai" type="time" class="mt-1 block w-full"
                                value="{{ old('selasa_checkout_selesai', $about->selasa_checkout_selesai) }}" required />
                            <x-input-error class="mt-2" :messages="$errors->get('selasa_checkout_selesai')" />
                        </div>

                        {{-- RABU --}}
                        <div>
                            <x-input-label value="Rabu (Mulai Check-out)" />
                            <x-text-input name="rabu_checkout_mulai" type="time" class="mt-1 block w-full"
                                value="{{ old('rabu_checkout_mulai', $about->rabu_checkout_mulai) }}" required />
                            <x-input-error class="mt-2" :messages="$errors->get('rabu_checkout_mulai')" />
                        </div>
                        <div>
                            <x-input-label value="Rabu (Selesai Check-out)" />
                            <x-text-input name="rabu_checkout_selesai" type="time" class="mt-1 block w-full"
                                value="{{ old('rabu_checkout_selesai', $about->rabu_checkout_selesai) }}" required />
                            <x-input-error class="mt-2" :messages="$errors->get('rabu_checkout_selesai')" />
                        </div>

                        {{-- KAMIS --}}
                        <div>
                            <x-input-label value="Kamis (Mulai Check-out)" />
                            <x-text-input name="kamis_checkout_mulai" type="time" class="mt-1 block w-full"
                                value="{{ old('kamis_checkout_mulai', $about->kamis_checkout_mulai) }}" required />
                            <x-input-error class="mt-2" :messages="$errors->get('kamis_checkout_mulai')" />
                        </div>
                        <div>
                            <x-input-label value="Kamis (Selesai Check-out)" />
                            <x-text-input name="kamis_checkout_selesai" type="time" class="mt-1 block w-full"
                                value="{{ old('kamis_checkout_selesai', $about->kamis_checkout_selesai) }}" required />
                            <x-input-error class="mt-2" :messages="$errors->get('kamis_checkout_selesai')" />
                        </div>

                        {{-- JUMAT --}}
                        <div>
                            <x-input-label value="Jumat (Mulai Check-out)" />
                            <x-text-input name="jumat_checkout_mulai" type="time" class="mt-1 block w-full"
                                value="{{ old('jumat_checkout_mulai', $about->jumat_checkout_mulai) }}" required />
                            <x-input-error class="mt-2" :messages="$errors->get('jumat_checkout_mulai')" />
                        </div>
                        <div>
                            <x-input-label value="Jumat (Selesai Check-out)" />
                            <x-text-input name="jumat_checkout_selesai" type="time" class="mt-1 block w-full"
                                value="{{ old('jumat_checkout_selesai', $about->jumat_checkout_selesai) }}" required />
                            <x-input-error class="mt-2" :messages="$errors->get('jumat_checkout_selesai')" />
                        </div>
                    </div>

                    {{-- SUBMIT --}}
                    <div class="pt-4">
                        <x-primary-button class="rounded-xl">
                            Simpan
                        </x-primary-button>
                    </div>

                </form>
            </div>

        </div>
    </div>
</x-app-layout>
