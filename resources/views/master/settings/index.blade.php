<x-app-layout>
    <x-slot name="header">
        <h2 class="font-black text-xl text-gray-800 leading-tight">
            Global System Settings
        </h2>
        <p class="text-sm text-gray-500 mt-1">
            Pengaturan ini akan tampil di halaman Login & Welcome Page (Default).
        </p>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-8">

            {{-- Flash Message --}}
            @if(session('success'))
                <div class="p-4 bg-green-50 text-green-700 font-bold rounded-lg">{{ session('success') }}</div>
            @endif

            {{-- FORM 1: GLOBAL BRANDING --}}
            <form method="POST" action="{{ route('master.settings.update') }}" enctype="multipart/form-data"
                  class="bg-white shadow-sm sm:rounded-2xl p-6 border border-gray-100">
                @csrf @method('PUT')
                <input type="hidden" name="section" value="branding">

                <div class="mb-6 border-b pb-4">
                    <h3 class="font-bold text-lg text-purple-700">1. Tampilan Utama (Welcome & Login)</h3>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <x-input-label value="Judul Aplikasi Utama" />
                        <x-text-input name="app_name" class="w-full font-bold" value="{{ old('app_name', $about->app_name) }}" />
                    </div>
                    <div>
                        <x-input-label value="Sub Judul (Instansi Pusat)" />
                        <x-text-input name="agency_name" class="w-full" value="{{ old('agency_name', $about->agency_name) }}" />
                    </div>
                    <div class="md:col-span-2">
                        <x-input-label value="Deskripsi Singkat (Slogan)" />
                        <x-text-input name="app_description" class="w-full" value="{{ old('app_description', $about->app_description) }}" />
                    </div>
                    <div class="md:col-span-2">
                        <x-input-label value="Logo Utama Sistem" />
                        @if($about->logo_path)
                            <img src="{{ asset('storage/' . $about->logo_path) }}" class="h-16 w-auto border p-1 rounded mt-2 mb-2">
                        @endif
                        <input type="file" name="logo" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:bg-purple-50 file:text-purple-700">
                    </div>
                </div>
                <div class="mt-4 pt-4 border-t flex justify-end">
                    <x-primary-button>Simpan Tampilan</x-primary-button>
                </div>
            </form>

            {{-- FORM 2: KONTAK PUSAT (FOOTER) --}}
            <form method="POST" action="{{ route('master.settings.update') }}"
                  class="bg-white shadow-sm sm:rounded-2xl p-6 border border-gray-100">
                @csrf @method('PUT')
                <input type="hidden" name="section" value="footer">

                <div class="mb-6 border-b pb-4">
                    <h3 class="font-bold text-lg text-purple-700">2. Kontak Pusat (Footer Default)</h3>
                </div>

                <div class="space-y-4">
                    <div>
                        <x-input-label value="Alamat Kantor Pusat" />
                        <textarea name="office_address" class="w-full rounded-xl border-gray-200 text-sm" rows="2">{{ old('office_address', $about->office_address) }}</textarea>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div><x-input-label value="No. Telepon" /><x-text-input name="office_phone" class="w-full" value="{{ old('office_phone', $about->office_phone) }}" /></div>
                        <div><x-input-label value="Email Pusat" /><x-text-input name="office_email" class="w-full" value="{{ old('office_email', $about->office_email) }}" /></div>
                    </div>
                </div>
                <div class="mt-4 pt-4 border-t flex justify-end">
                    <x-primary-button>Simpan Kontak</x-primary-button>
                </div>
            </form>

        </div>
    </div>
</x-app-layout>
