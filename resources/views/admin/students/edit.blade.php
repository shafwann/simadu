<x-app-layout>
    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-xl mx-auto px-6 lg:px-8">

            <div class="mb-6">
                <h1 class="text-2xl font-black text-gray-900">Edit Absensi</h1>
                <p class="text-gray-500 text-sm">Perbarui data kehadiran mahasiswa.</p>
            </div>

            <div class="bg-white p-8 rounded-[2rem] border border-gray-100 shadow-xl shadow-gray-100/50">
                <form action="{{ route('admin.attendance.update', $attendance->id) }}" method="POST" class="space-y-5">
                    @csrf
                    @method('PUT')

                    {{-- Tanggal --}}
                    <div>
                        <x-input-label for="tanggal" :value="__('Tanggal')" />
                        <input type="date" name="tanggal" value="{{ old('tanggal', $attendance->tanggal) }}"
                            class="block mt-1 w-full border-gray-200 rounded-xl bg-gray-50 focus:border-blue-500 focus:ring-blue-500 font-bold text-gray-800" required>
                    </div>

                    {{-- Jam Masuk --}}
                    <div>
                        <x-input-label for="check_in" :value="__('Jam Masuk (Check In)')" />
                        <input type="time" name="check_in" value="{{ old('check_in', $attendance->check_in) }}"
                            class="block mt-1 w-full border-gray-200 rounded-xl bg-gray-50 focus:border-blue-500 focus:ring-blue-500 font-bold text-gray-800">
                    </div>

                    {{-- Jam Pulang --}}
                    <div>
                        <x-input-label for="check_out" :value="__('Jam Pulang (Check Out)')" />
                        <input type="time" name="check_out" value="{{ old('check_out', $attendance->check_out) }}"
                            class="block mt-1 w-full border-gray-200 rounded-xl bg-gray-50 focus:border-blue-500 focus:ring-blue-500 font-bold text-gray-800">
                    </div>

                    {{-- Status --}}
                    <div>
                        <x-input-label for="status" :value="__('Status Kehadiran')" />
                        <select name="status" class="block mt-1 w-full border-gray-200 rounded-xl bg-gray-50 focus:border-blue-500 focus:ring-blue-500 font-bold text-gray-800">
                            @foreach(['Hadir','Telat','Ijin','Sakit','Dinas Luar','Cuti','Tanpa Keterangan'] as $s)
                                <option value="{{ $s }}" {{ $attendance->status == $s ? 'selected' : '' }}>
                                    {{ $s }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Link Maps --}}
                    <div>
                        <x-input-label for="location" :value="__('Link Google Maps')" />
                        <input type="text" name="location" value="{{ old('location', $attendance->location) }}"
                            class="block mt-1 w-full border-gray-200 rounded-xl bg-gray-50 focus:border-blue-500 focus:ring-blue-500 text-sm font-medium text-gray-600"
                            placeholder="https://maps.google.com/...">
                    </div>

                    {{-- ✅ TAMBAHAN: NAMA LOKASI --}}
                    <div>
                        <x-input-label for="location_name" :value="__('Nama Lokasi (Teks PDF)')" />
                        <input type="text" name="location_name" value="{{ old('location_name', $attendance->location_name) }}"
                            class="block mt-1 w-full border-gray-200 rounded-xl bg-gray-50 focus:border-blue-500 focus:ring-blue-500 font-bold text-gray-800 text-sm uppercase"
                            placeholder="Contoh: KANTOR BAPPERIDA">
                        <p class="text-[10px] text-gray-400 mt-1">*Ubah teks ini jika ingin mengganti tampilan lokasi di PDF.</p>
                    </div>

                    <div class="pt-4 flex gap-3">
                        <button type="submit" class="flex-1 py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl transition shadow-lg shadow-blue-200">
                            Simpan Perubahan
                        </button>
                        <a href="{{ route('admin.students.show', $attendance->user_id) }}" class="px-6 py-3 bg-gray-100 text-gray-600 font-bold rounded-xl hover:bg-gray-200 transition">
                            Batal
                        </a>
                    </div>
                </form>
            </div>

        </div>
    </div>
</x-app-layout>
