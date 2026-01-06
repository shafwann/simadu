<x-app-layout>
    <div class="py-12 bg-white min-h-screen">
        <div class="max-w-2xl mx-auto px-6 lg:px-8">

            {{-- HEADER --}}
            <div class="mb-8">
                <h1 class="text-3xl font-black text-gray-900">Edit Dosen</h1>
                <p class="text-gray-500 mt-2">
                    Perbarui data dosen dan assign mahasiswa aktif.
                </p>
            </div>

            {{-- ========================= --}}
            {{-- FORM FILTER (GET) --}}
            {{-- ========================= --}}
            <div class="mb-6 p-6 border rounded-2xl bg-gray-50">
                <form method="GET"
                      action="{{ route('admin.dosen.edit', $dosen->id) }}"
                      class="grid grid-cols-1 md:grid-cols-2 gap-4">

                    {{-- Instansi --}}
                    <div>
                        <x-input-label value="Instansi" />
                        <select name="instansi"
                                onchange="this.form.submit()"
                                class="w-full rounded-lg border-gray-300">
                            <option value="">-- Semua Instansi --</option>
                            @foreach($instansiList as $instansi)
                                <option value="{{ $instansi }}"
                                    {{ request('instansi', $dosen->instansi) == $instansi ? 'selected' : '' }}>
                                    {{ $instansi }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Prodi --}}
                    <div>
                        <x-input-label value="Program Studi" />
                        <select name="prodi"
                                onchange="this.form.submit()"
                                class="w-full rounded-lg border-gray-300">
                            <option value="">-- Semua Prodi --</option>
                            @foreach($prodiList as $prodi)
                                <option value="{{ $prodi }}"
                                    {{ request('prodi', $dosen->prodi) == $prodi ? 'selected' : '' }}>
                                    {{ $prodi }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                </form>
            </div>

            {{-- ========================= --}}
            {{-- FORM UPDATE (PUT) --}}
            {{-- ========================= --}}
            <div class="p-8 bg-white border border-gray-200 rounded-[2rem] shadow-xl shadow-blue-50/50">
                <form action="{{ route('admin.dosen.update', $dosen->id) }}"
                      method="POST"
                      class="space-y-6">
                    @csrf
                    @method('PUT')

                    {{-- Nama --}}
                    <div>
                        <x-input-label for="name" value="Nama Lengkap" />
                        <x-text-input id="name"
                                      class="block mt-1 w-full"
                                      type="text"
                                      name="name"
                                      :value="old('name', $dosen->name)"
                                      required />
                        <x-input-error :messages="$errors->get('name')" />
                    </div>

                    {{-- Email --}}
                    <div>
                        <x-input-label for="email" value="Email" />
                        <x-text-input id="email"
                                      class="block mt-1 w-full"
                                      type="email"
                                      name="email"
                                      :value="old('email', $dosen->email)"
                                      required />
                        <x-input-error :messages="$errors->get('email')" />
                    </div>

                    {{-- Password --}}
                    <div>
                        <x-input-label for="password" value="Password Baru (Opsional)" />
                        <x-text-input id="password"
                                      class="block mt-1 w-full"
                                      type="password"
                                      name="password"
                                      placeholder="Kosongkan jika tidak ingin mengganti" />
                    </div>

                    {{-- ========================= --}}
                    {{-- ASSIGN MAHASISWA --}}
                    {{-- ========================= --}}
                    <div>
                        <x-input-label value="Assign Mahasiswa Aktif" />

                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4
                                    gap-4 max-h-96 overflow-y-auto p-2 border rounded-lg bg-white mt-2">

                            @forelse($mahasiswa as $mhs)
                                <label class="group relative cursor-pointer">
                                    <div class="border rounded-xl p-4 flex flex-col gap-1
                                                transition-shadow hover:shadow-lg hover:border-blue-300 bg-white">

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
                                    </div>

                                    <input type="checkbox"
                                           name="mahasiswa_ids[]"
                                           value="{{ $mhs->id }}"
                                           class="absolute top-2 right-2 w-5 h-5
                                                  text-blue-600 border-gray-300 rounded"
                                           {{ in_array($mhs->id, old('mahasiswa_ids', $mahasiswaAssigned)) ? 'checked' : '' }}>
                                </label>
                            @empty
                                <p class="text-sm text-gray-500 col-span-full text-center">
                                    Tidak ada mahasiswa sesuai filter.
                                </p>
                            @endforelse

                        </div>
                    </div>

                    {{-- Tombol --}}
                    <div class="flex items-center gap-4 pt-4">
                        <x-primary-button class="bg-blue-600 hover:bg-blue-700 py-3 px-6 text-sm">
                            Update Dosen
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
