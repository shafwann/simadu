<x-app-layout>
    <div class="py-12 bg-white min-h-screen">
        <div class="max-w-2xl mx-auto px-6 lg:px-8">

            {{-- HEADER --}}
            <div class="mb-8">
                <h1 class="text-3xl font-black text-gray-900">Tambah Dosen</h1>
                <p class="text-gray-500 mt-2">
                    Daftarkan dosen baru dan assign mahasiswa aktif.
                </p>
            </div>

            {{-- ================= FILTER MAHASISWA (GET) ================= --}}
            <div class="mb-6 p-6 border rounded-2xl bg-gray-50">
                <form method="GET" class="grid grid-cols-2 gap-4">

                    {{-- Instansi --}}
                    <div>
                        <x-input-label value="Instansi" />
                        <select id="instansi_filter"
                                name="instansi"
                                class="w-full rounded-lg border-gray-300">
                            <option value="">-- Semua Instansi --</option>
                            @foreach($instansiList as $instansi)
                                <option value="{{ $instansi }}"
                                    {{ request('instansi') == $instansi ? 'selected' : '' }}>
                                    {{ $instansi }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Prodi --}}
                    <div>
                        <x-input-label value="Program Studi" />
                        <select id="prodi_filter"
                                name="prodi"
                                class="w-full rounded-lg border-gray-300">
                            <option value="">-- Semua Prodi --</option>
                            @foreach($prodiList as $prodi)
                                <option value="{{ $prodi }}"
                                    {{ request('prodi') == $prodi ? 'selected' : '' }}>
                                    {{ $prodi }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                </form>
            </div>

            {{-- ================= FORM CREATE DOSEN (POST) ================= --}}
            <div class="p-8 bg-white border border-gray-200 rounded-[2rem] shadow-xl shadow-blue-50/50">
                <form action="{{ route('admin.dosen.store') }}" method="POST" class="space-y-6">
                    @csrf

                    {{-- Nama --}}
                    <div>
                        <x-input-label value="Nama Lengkap" />
                        <x-text-input name="name" class="w-full" required />
                    </div>

                    {{-- Email --}}
                    <div>
                        <x-input-label value="Email" />
                        <x-text-input type="email" name="email" class="w-full" required />
                    </div>

                    {{-- Password --}}
                    <div>
                        <x-input-label value="Password Sementara" />
                        <x-text-input type="password" name="password" class="w-full" required />
                    </div>

                    {{-- Konfirmasi Password --}}
                    <div>
                        <x-input-label value="Konfirmasi Password" />
                        <x-text-input type="password" name="password_confirmation" class="w-full" required />
                    </div>

                    {{-- Assign Mahasiswa --}}
                    <div>
                        <x-input-label value="Assign Mahasiswa Aktif" />

                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4
                                    max-h-96 overflow-y-auto p-3 border rounded-xl mt-2">

                            @forelse($mahasiswa as $mhs)
                                <label class="relative border rounded-xl p-4 cursor-pointer
                                              hover:border-blue-400 hover:shadow">
                                    <input type="checkbox"
                                           name="mahasiswa_ids[]"
                                           value="{{ $mhs->id }}"
                                           class="absolute top-3 right-3">

                                    <div class="font-bold text-gray-900">
                                        {{ $mhs->name }}
                                    </div>

                                    <div class="text-xs text-gray-500">
                                        NIM: {{ $mhs->nim ?? '-' }}
                                    </div>

                                    <div class="text-xs text-gray-600">
                                        {{ $mhs->prodi ?? '-' }}
                                    </div>

                                    <div class="text-xs text-gray-400 italic">
                                        {{ $mhs->instansi ?? '-' }}
                                    </div>
                                </label>
                            @empty
                                <p class="text-gray-400 col-span-full text-center py-6">
                                    Tidak ada mahasiswa sesuai filter
                                </p>
                            @endforelse

                        </div>
                    </div>

                    {{-- Tombol --}}
                    <div class="flex items-center gap-4 pt-4">
                        <x-primary-button class="bg-blue-600 hover:bg-blue-700 px-6 py-3">
                            Simpan & Assign
                        </x-primary-button>

                        <a href="{{ route('admin.dosen.index') }}"
                           class="text-gray-500 font-bold text-sm hover:text-gray-800">
                            Batal
                        </a>
                    </div>

                </form>
            </div>

        </div>
    </div>
</x-app-layout>
