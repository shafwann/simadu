<x-app-layout>
    <x-slot name="header">
        <h2 class="font-black text-xl text-gray-800 leading-tight">About Instansi</h2>
        <p class="text-sm text-gray-500 mt-1">Hanya Super Admin yang bisa mengubah data ini.</p>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-8">

            {{-- Flash Message --}}
            @if(session('success'))
                <div class="p-4 rounded-xl bg-green-50 text-green-700 font-bold border border-green-100 flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="p-4 rounded-xl bg-red-50 text-red-700 font-bold border border-red-100">
                    {{ session('error') }}
                </div>
            @endif

            {{-- ================= FORM 1: BRANDING ================= --}}
            <form method="POST" action="{{ route('admin.about.update') }}" enctype="multipart/form-data"
                  class="bg-white shadow-sm sm:rounded-2xl p-6 border border-gray-100">
                @csrf @method('PUT')
                <input type="hidden" name="section" value="branding">

                {{-- Header --}}
                <div class="mb-6 border-b pb-4">
                    <h3 class="font-bold text-lg text-blue-800">1. Branding Aplikasi</h3>
                    <p class="text-xs text-gray-400 mt-1">Mengatur nama, deskripsi, dan logo yang tampil di Navbar/Login.</p>
                </div>

                {{-- Inputs --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <x-input-label for="app_name" value="Nama Aplikasi" />
                        <x-text-input id="app_name" name="app_name" type="text" class="mt-1 block w-full font-bold" value="{{ old('app_name', $about->app_name) }}" placeholder="Bapperida" />
                    </div>
                    <div>
                        <x-input-label for="agency_name" value="Sub Judul (Instansi)" />
                        <x-text-input id="agency_name" name="agency_name" type="text" class="mt-1 block w-full" value="{{ old('agency_name', $about->agency_name) }}" placeholder="Kab. Karanganyar" />
                    </div>
                    <div class="md:col-span-2">
                        <x-input-label for="app_description" value="Deskripsi Singkat" />
                        <x-text-input id="app_description" name="app_description" type="text" class="mt-1 block w-full text-sm" value="{{ old('app_description', $about->app_description) }}" />
                    </div>
                    <div class="md:col-span-2">
                        <x-input-label for="logo" value="Logo Aplikasi" />
                        <div class="flex items-center gap-4 mt-2">
                            @if($about->logo_path)
                                <img src="{{ asset('storage/' . $about->logo_path) }}" class="h-12 w-auto border p-1 rounded">
                            @endif
                            <input type="file" name="logo" class="text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                        </div>
                    </div>
                </div>

                {{-- Button Bawah Kiri --}}
                <div class="mt-6 pt-4 border-t border-gray-100 flex justify-start">
                    <x-primary-button>Simpan Branding</x-primary-button>
                </div>
            </form>

            {{-- ================= FORM 2: BIDANG / DIVISI ================= --}}
            <form method="POST" action="{{ route('admin.about.update') }}"
                  class="bg-white shadow-sm sm:rounded-2xl p-6 border border-gray-100"
                  x-data="bidangDivisiForm()" x-init="init()">
                @csrf @method('PUT')
                <input type="hidden" name="section" value="bidang">

                <div class="mb-6 border-b pb-4">
                    <h3 class="font-bold text-lg text-gray-800">2. Daftar Bidang/Divisi</h3>
                    <p class="text-xs text-gray-400 mt-1">Digunakan untuk dropdown registrasi mahasiswa.</p>
                </div>

                {{-- Logic AlpineJS --}}
                <textarea x-ref="seed" class="hidden">{{ old('bidang_options', $bidangOptionsText ?? '') }}</textarea>
                <input type="hidden" name="bidang_options" :value="serialized">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mb-4">
                    <template x-for="(item, index) in items" :key="index">
                        <div class="relative">
                            <input type="text" class="w-full rounded-xl border-gray-200 focus:border-blue-500 focus:ring-blue-500"
                                   x-model="items[index]" @input="sync()" placeholder="Nama Bidang">
                            <button type="button" x-show="items.length > 1" @click="remove(index)"
                                    class="absolute right-2 top-2 text-gray-400 hover:text-red-500 font-bold px-2">
                                &times;
                            </button>
                        </div>
                    </template>
                </div>
                <button type="button" @click="add()" class="text-sm text-blue-600 font-bold hover:underline flex items-center gap-1">
                    <span>+</span> Tambah Baris Baru
                </button>

                {{-- Button Bawah Kiri --}}
                <div class="mt-6 pt-4 border-t border-gray-100 flex justify-start">
                    <x-primary-button>Simpan Bidang</x-primary-button>
                </div>
            </form>

            {{-- ================= FORM 3: INFO INSTANSI ================= --}}
            <form method="POST" action="{{ route('admin.about.update') }}" class="bg-white shadow-sm sm:rounded-2xl p-6 border border-gray-100">
                @csrf @method('PUT')
                <input type="hidden" name="section" value="instansi">

                <div class="mb-6 border-b pb-4">
                    <h3 class="font-bold text-lg text-gray-800">3. Identitas Instansi</h3>
                    <p class="text-xs text-gray-400 mt-1">Data resmi untuk kop surat laporan/PDF.</p>
                </div>

                <div class="space-y-4">
                    <div>
                        <x-input-label value="Nama Instansi Resmi" />
                        <x-text-input name="nama_instansi" class="mt-1 block w-full" value="{{ old('nama_instansi', $about->nama_instansi) }}" required />
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <x-input-label value="Kepala Instansi" />
                            <x-text-input name="kepala_instansi" class="mt-1 block w-full" value="{{ old('kepala_instansi', $about->kepala_instansi) }}" required />
                        </div>
                        <div>
                            <x-input-label value="NIP Kepala" />
                            <x-text-input name="nip_kepala_instansi" class="mt-1 block w-full" value="{{ old('nip_kepala_instansi', $about->nip_kepala_instansi) }}" required />
                        </div>
                    </div>
                    <div>
                        <x-input-label value="Jam Operasional (Footer)" />
                        <x-text-input name="jam_operasional" class="mt-1 block w-full" value="{{ old('jam_operasional', $about->jam_operasional) }}" required />
                        <p class="text-xs text-gray-400 mt-1">Pisahkan dengan koma (,) untuk baris baru.</p>
                    </div>
                </div>

                {{-- Button Bawah Kiri --}}
                <div class="mt-6 pt-4 border-t border-gray-100 flex justify-start">
                    <x-primary-button>Simpan Identitas</x-primary-button>
                </div>
            </form>

            {{-- ================= FORM 4: LOKASI GPS ================= --}}
            <form method="POST" action="{{ route('admin.about.update') }}" class="bg-white shadow-sm sm:rounded-2xl p-6 border border-gray-100">
                @csrf @method('PUT')
                <input type="hidden" name="section" value="lokasi">

                <div class="mb-6 border-b pb-4">
                    <h3 class="font-bold text-lg text-gray-800">4. Lokasi Kantor (GPS)</h3>
                    <p class="text-xs text-gray-400 mt-1">Menentukan titik pusat absensi mahasiswa.</p>
                </div>

                <div class="space-y-4">
                    <div>
                        <x-input-label value="Nama Lokasi (Label Absen)" />
                        <x-text-input name="office_name" class="mt-1 block w-full uppercase" value="{{ old('office_name', $about->office_name) }}" />
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <x-input-label value="Latitude" />
                            <x-text-input name="office_latitude" class="mt-1 block w-full" value="{{ old('office_latitude', $about->office_latitude) }}" />
                        </div>
                        <div>
                            <x-input-label value="Longitude" />
                            <x-text-input name="office_longitude" class="mt-1 block w-full" value="{{ old('office_longitude', $about->office_longitude) }}" />
                        </div>
                        <div>
                            <x-input-label value="Radius (Meter)" />
                            <x-text-input name="radius_meter" type="number" class="mt-1 block w-full" value="{{ old('radius_meter', $about->radius_meter) }}" required />
                        </div>
                    </div>
                </div>

                {{-- Button Bawah Kiri --}}
                <div class="mt-6 pt-4 border-t border-gray-100 flex justify-start">
                    <x-primary-button>Simpan Lokasi</x-primary-button>
                </div>
            </form>

            {{-- ================= FORM 5: JADWAL ABSENSI ================= --}}
            <form method="POST" action="{{ route('admin.about.update') }}" class="bg-white shadow-sm sm:rounded-2xl p-6 border border-gray-100">
                @csrf @method('PUT')
                <input type="hidden" name="section" value="jadwal">

                <div class="mb-6 border-b pb-4">
                    <h3 class="font-bold text-lg text-gray-800">5. Jadwal Absensi</h3>
                    <p class="text-xs text-gray-400 mt-1">Mengatur jam buka tutup tombol absen masuk & pulang.</p>
                </div>

                <div class="space-y-6">
                    {{-- Check IN --}}
                    <div>
                        <h4 class="font-bold text-sm text-gray-500 uppercase mb-3 flex items-center gap-2">
                            <span class="w-2 h-2 bg-green-500 rounded-full"></span> Check-IN (Masuk)
                        </h4>
                        <div class="grid grid-cols-1 gap-3">
                            @foreach(['senin', 'selasa', 'rabu', 'kamis', 'jumat'] as $day)
                            <div class="grid grid-cols-12 gap-2 items-center">
                                <div class="col-span-2 text-sm font-bold capitalize">{{ $day }}</div>
                                <div class="col-span-5">
                                    <x-text-input name="{{ $day }}_mulai" type="time" class="w-full text-xs" value="{{ old($day.'_mulai', $about->{$day.'_mulai'}) }}" required />
                                </div>
                                <div class="col-span-5">
                                    <x-text-input name="{{ $day }}_selesai" type="time" class="w-full text-xs" value="{{ old($day.'_selesai', $about->{$day.'_selesai'}) }}" required />
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- Check OUT --}}
                    <div class="border-t pt-4">
                        <h4 class="font-bold text-sm text-gray-500 uppercase mb-3 flex items-center gap-2">
                            <span class="w-2 h-2 bg-orange-500 rounded-full"></span> Check-OUT (Pulang)
                        </h4>
                        <div class="grid grid-cols-1 gap-3">
                            @foreach(['senin', 'selasa', 'rabu', 'kamis', 'jumat'] as $day)
                            <div class="grid grid-cols-12 gap-2 items-center">
                                <div class="col-span-2 text-sm font-bold capitalize">{{ $day }}</div>
                                <div class="col-span-5">
                                    <x-text-input name="{{ $day }}_checkout_mulai" type="time" class="w-full text-xs" value="{{ old($day.'_checkout_mulai', $about->{$day.'_checkout_mulai'}) }}" required />
                                </div>
                                <div class="col-span-5">
                                    <x-text-input name="{{ $day }}_checkout_selesai" type="time" class="w-full text-xs" value="{{ old($day.'_checkout_selesai', $about->{$day.'_checkout_selesai'}) }}" required />
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- Button Bawah Kiri --}}
                <div class="mt-6 pt-4 border-t border-gray-100 flex justify-start">
                    <x-primary-button>Simpan Jadwal</x-primary-button>
                </div>
            </form>

            {{-- ================= FORM 6: PENGATURAN FOOTER ================= --}}
            <form method="POST" action="{{ route('admin.about.update') }}"
                  class="bg-white shadow-sm sm:rounded-2xl p-6 border border-gray-100"
                  x-data="dynamicLinksForm()" x-init="init()">
                @csrf @method('PUT')
                <input type="hidden" name="section" value="footer">

                <div class="mb-6 border-b pb-4">
                    <h3 class="font-bold text-lg text-gray-800">6. Pengaturan Footer</h3>
                    <p class="text-xs text-gray-400 mt-1">Mengatur alamat, peta, sosmed, dan link terkait.</p>
                </div>

                <div class="space-y-6">
                    {{-- Kontak --}}
                    <div>
                        <h4 class="font-bold text-sm text-blue-600 uppercase mb-3">Kontak & Alamat</h4>
                        <div class="grid grid-cols-1 gap-4">
                            <div>
                                <x-input-label value="Alamat Kantor" />
                                <textarea name="office_address" class="w-full rounded-xl border-gray-200 text-sm" rows="2">{{ old('office_address', $about->office_address) }}</textarea>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <x-input-label value="No. Telepon" />
                                    <x-text-input name="office_phone" class="w-full" value="{{ old('office_phone', $about->office_phone) }}" />
                                </div>
                                <div>
                                    <x-input-label value="Email" />
                                    <x-text-input name="office_email" class="w-full" value="{{ old('office_email', $about->office_email) }}" />
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Sosmed --}}
                    <div class="border-t pt-4">
                        <h4 class="font-bold text-sm text-blue-600 uppercase mb-3">Media Sosial (URL Lengkap)</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-4">
                            <div>
                                <x-input-label value="Instagram" />
                                <x-text-input name="social_instagram" class="w-full" value="{{ old('social_instagram', $about->social_instagram) }}" placeholder="https://instagram.com/..." />
                            </div>
                            <div>
                                <x-input-label value="Facebook" />
                                <x-text-input name="social_facebook" class="w-full" value="{{ old('social_facebook', $about->social_facebook) }}" placeholder="https://facebook.com/..." />
                            </div>
                            <div>
                                <x-input-label value="YouTube" />
                                <x-text-input name="social_youtube" class="w-full" value="{{ old('social_youtube', $about->social_youtube) }}" placeholder="https://youtube.com/@..." />
                            </div>
                            <div>
                                <x-input-label value="X (Twitter)" />
                                <x-text-input name="social_x" class="w-full" value="{{ old('social_x', $about->social_x) }}" placeholder="https://x.com/..." />
                            </div>
                        </div>
                    </div>

                    {{-- Link Terkait (DINAMIS) --}}
                    <div class="border-t pt-4">
                        <h4 class="font-bold text-sm text-blue-600 uppercase mb-3">Link Terkait (Dinamis)</h4>
                        <p class="text-xs text-gray-400 mb-3">Tambahkan link eksternal untuk ditampilkan di footer (misal: Website Utama, PPID, dll).</p>

                        <div id="existing-links" data-links="{{ $about->custom_links ?? '[]' }}" class="hidden"></div>

                        <div class="space-y-3">
                            <template x-for="(row, index) in rows" :key="index">
                                <div class="flex gap-3 items-center">
                                    <div class="w-1/3">
                                        <input type="text" name="links_label[]" x-model="row.label"
                                            class="w-full rounded-xl border-gray-200 text-sm" placeholder="Nama Link">
                                    </div>
                                    <div class="w-2/3">
                                        <input type="url" name="links_url[]" x-model="row.url"
                                            class="w-full rounded-xl border-gray-200 text-sm" placeholder="URL (https://...)">
                                    </div>
                                    <button type="button" @click="remove(index)" class="text-red-500 hover:text-red-700 font-bold px-2">
                                        &times;
                                    </button>
                                </div>
                            </template>
                        </div>

                        <button type="button" @click="add()" class="mt-3 text-sm text-blue-600 font-bold hover:underline flex items-center gap-1">
                            <span>+</span> Tambah Link
                        </button>
                    </div>

                    {{-- Peta --}}
                    <div class="border-t pt-4">
                        <h4 class="font-bold text-sm text-blue-600 uppercase mb-3">Peta Lokasi</h4>
                        <div>
                            <x-input-label value="Link Embed Google Maps (Src)" />
                            <x-text-input name="google_maps_embed" class="w-full" value="{{ old('google_maps_embed', $about->google_maps_embed) }}" />
                            <p class="text-[10px] text-gray-400 mt-1">
                                Copy URL dari menu "Embed a map" di Google Maps (hanya bagian dalam tanda kutip <code>src="..."</code>).
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Button Bawah Kiri --}}
                <div class="mt-6 pt-4 border-t border-gray-100 flex justify-start">
                    <x-primary-button>Simpan Footer</x-primary-button>
                </div>
            </form>

            <script>
                function dynamicLinksForm() {
                    return {
                        rows: [],
                        init() {
                            const raw = document.getElementById('existing-links').getAttribute('data-links');
                            try {
                                const parsed = JSON.parse(raw);
                                if (Array.isArray(parsed) && parsed.length > 0) {
                                    this.rows = parsed;
                                } else {
                                    this.rows = [{ label: '', url: '' }];
                                }
                            } catch (e) {
                                this.rows = [{ label: '', url: '' }];
                            }
                        },
                        add() { this.rows.push({ label: '', url: '' }); },
                        remove(index) {
                            if (this.rows.length > 1) this.rows.splice(index, 1);
                            else { this.rows[0].label = ''; this.rows[0].url = ''; }
                        }
                    }
                }
            </script>

            {{-- Script Alpine untuk Link Dinamis --}}
            <script>
                function dynamicLinksForm() {
                    return {
                        rows: [],
                        init() {
                            // Ambil data dari atribut data-links
                            const raw = document.getElementById('existing-links').getAttribute('data-links');
                            try {
                                const parsed = JSON.parse(raw);
                                if (Array.isArray(parsed) && parsed.length > 0) {
                                    this.rows = parsed;
                                } else {
                                    this.rows = [{ label: '', url: '' }]; // Default 1 baris kosong
                                }
                            } catch (e) {
                                this.rows = [{ label: '', url: '' }];
                            }
                        },
                        add() {
                            this.rows.push({ label: '', url: '' });
                        },
                        remove(index) {
                            if (this.rows.length > 1) {
                                this.rows.splice(index, 1);
                            } else {
                                // Jika sisa 1, kosongkan saja jangan dihapus
                                this.rows[0].label = '';
                                this.rows[0].url = '';
                            }
                        }
                    }
                }
            </script>

        </div>
    </div>

    {{-- SCRIPT ALPINE UNTUK BIDANG --}}
    <script>
        function bidangDivisiForm() {
            return {
                items: [],
                serialized: '',
                init() {
                    const raw = (this.$refs.seed?.value || '').toString();
                    this.items = raw.split(/\r\n|\n|\r/).map(s => (s || '').trim()).filter(Boolean);
                    if(this.items.length === 0) this.items.push('');
                    this.sync();
                },
                sync() {
                    this.serialized = this.items.map(s => (s || '').trim()).filter(Boolean).join("\n");
                },
                add() { this.items.push(''); this.$nextTick(() => this.sync()); },
                remove(index) { this.items.splice(index, 1); this.$nextTick(() => this.sync()); },
            }
        }
    </script>
</x-app-layout>
