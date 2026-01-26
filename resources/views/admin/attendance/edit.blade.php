<x-app-layout>
    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-xl mx-auto px-6 lg:px-8">

            {{-- HEADER --}}
            <div class="mb-6">
                <h1 class="text-2xl font-black text-gray-900">Edit Absensi</h1>
                <p class="text-gray-500 text-sm">Perbarui data kehadiran mahasiswa.</p>
                <div class="mt-2 inline-flex items-center gap-2 bg-blue-50 px-3 py-1 rounded-lg border border-blue-100">
                    <span class="text-xs font-bold text-blue-600 uppercase">Mahasiswa:</span>
                    <span class="text-sm font-bold text-gray-800">{{ $attendance->user->name }}</span>
                </div>
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

                    {{-- Status Kehadiran (Pindah ke atas agar logika JS lebih masuk akal bagi user) --}}
                    <div>
                        <x-input-label for="status" :value="__('Status Kehadiran')" />
                        <select name="status" id="statusSelect" class="block mt-1 w-full border-gray-200 rounded-xl bg-white focus:border-blue-500 focus:ring-blue-500 font-bold text-gray-800">
                            @foreach(['Hadir','Telat','Ijin','Sakit','Dinas Luar','Cuti','Tanpa Keterangan'] as $s)
                                <option value="{{ $s }}" {{ $attendance->status == $s ? 'selected' : '' }}>
                                    {{ $s }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Pulang Cepat --}}
                    <div id="pulangCepatWrapper" class="hidden mt-2">
                        <label class="inline-flex items-center gap-2 font-semibold text-gray-700">
                            <input type="checkbox"
                                id="pulangCepatCheckbox"
                                name="keterangan"
                                value="Pulang Cepat"
                                class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                {{ old('keterangan', $attendance->keterangan) === 'Pulang Cepat' ? 'checked' : '' }}>
                            ⏱️ Pulang Cepat
                        </label>
                    </div>


                    {{-- Grid Jam Masuk & Pulang --}}
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="check_in" :value="__('Jam Masuk')" />
                            <input type="time" name="check_in" id="checkInInput" value="{{ old('check_in', $attendance->check_in) }}"
                                class="block mt-1 w-full border-gray-200 rounded-xl bg-gray-50 focus:border-blue-500 focus:ring-blue-500 font-mono text-gray-800 disabled:bg-gray-100 disabled:text-gray-400">
                        </div>
                        <div>
                            <x-input-label for="check_out" :value="__('Jam Pulang')" />
                            <input type="time" name="check_out" id="checkOutInput" value="{{ old('check_out', $attendance->check_out) }}"
                                class="block mt-1 w-full border-gray-200 rounded-xl bg-gray-50 focus:border-blue-500 focus:ring-blue-500 font-mono text-gray-800 disabled:bg-gray-100 disabled:text-gray-400">
                        </div>
                    </div>

                    {{-- Link Maps --}}
                    <div>
                        <x-input-label for="location" :value="__('Link Google Maps')" />
                        <input type="text" name="location" value="{{ old('location', $attendance->location) }}"
                            class="block mt-1 w-full border-gray-200 rounded-xl bg-gray-50 focus:border-blue-500 focus:ring-blue-500 text-sm font-medium text-blue-600 underline"
                            placeholder="https://goo.gl/maps/...">
                    </div>

                    {{-- ✅ TAMBAHAN: NAMA LOKASI --}}
                    <div class="bg-gray-50 p-4 rounded-xl border border-gray-200">
                        <x-input-label for="location_name" :value="__('Nama Lokasi (Teks PDF)')" />
                        <input type="text" name="location_name" value="{{ old('location_name', $attendance->location_name) }}"
                            class="block mt-1 w-full border-gray-300 rounded-lg focus:border-blue-500 focus:ring-blue-500 font-bold text-gray-800 text-sm uppercase"
                            placeholder="Contoh: KANTOR BAPPERIDA">
                        <p class="text-[10px] text-gray-500 mt-2 flex items-start gap-1">
                            <svg class="w-3 h-3 mt-0.5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M12 2a10 10 0 100 20 10 10 0 000-20z"></path></svg>
                            Teks ini yang akan muncul di kolom "LOKASI" pada laporan PDF.
                        </p>
                    </div>

                    {{-- Tombol Aksi --}}
                    <div class="pt-6 flex gap-3 border-t border-gray-100">
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

    {{-- SCRIPT TOGGLE JAM --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const statusSelect = document.getElementById('statusSelect');
            const checkInInput = document.getElementById('checkInInput');
            const checkOutInput = document.getElementById('checkOutInput');

            function toggleJam() {
                // Daftar status yang TIDAK butuh jam
                const noTimeStatus = ['Sakit', 'Ijin', 'Cuti', 'Tanpa Keterangan', 'Alpha'];
                const selected = statusSelect.value;

                // Cek apakah status yang dipilih termasuk yang tidak butuh jam
                // Kita pakai logika string matching sederhana
                const isDisabled = noTimeStatus.some(s => selected.includes(s));

                checkInInput.disabled = isDisabled;
                checkOutInput.disabled = isDisabled;

                if (isDisabled) {
                    checkInInput.value = '';
                    checkOutInput.value = '';
                    checkInInput.required = false;
                } else {
                    checkInInput.required = true;
                }
            }

            // Jalankan saat load & saat ganti status
            toggleJam();
            statusSelect.addEventListener('change', toggleJam);

            //PULANG CEPAT
            const pulangCepatCheckbox = document.getElementById('pulangCepatCheckbox');
            const pulangCepatWrapper = document.getElementById('pulangCepatWrapper');

            function togglePulangCepat() {
                const status = statusSelect.value;

                // Hanya muncul saat Hadir / Telat
                const allowPulangCepat =
                    status.includes('Hadir') || status.includes('Telat');

                if (allowPulangCepat) {
                    pulangCepatWrapper.style.display = 'block';
                    pulangCepatCheckbox.disabled = false;
                } else {
                    pulangCepatWrapper.style.display = 'none';
                    pulangCepatCheckbox.checked = false;
                    pulangCepatCheckbox.disabled = true;
                }
            }

            togglePulangCepat();
            statusSelect.addEventListener('change', togglePulangCepat);
                });
    </script>
</x-app-layout>
