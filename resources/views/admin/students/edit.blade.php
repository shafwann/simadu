<x-app-layout>
    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-2xl mx-auto px-6 lg:px-8">

            <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100">
                <div class="mb-6 pb-6 border-b border-gray-100">
                    <h2 class="text-2xl font-black text-gray-800">Edit Absensi</h2>
                    <p class="text-sm text-gray-500">Mahasiswa: <span class="font-bold text-blue-600">{{ $attendance->user->name }}</span></p>
                </div>

                <form action="{{ route('admin.attendance.update', $attendance->id) }}" method="POST" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Tanggal</label>
                        <input type="date" name="tanggal" value="{{ $attendance->tanggal }}"
                            class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                    </div>

                    <div class="grid grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Jam Masuk (Check In)</label>
                            <input type="time" name="check_in" value="{{ $attendance->check_in }}" required
                                class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500 font-mono">
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Jam Pulang (Check Out)</label>
                            <input type="time" name="check_out" value="{{ $attendance->check_out }}"
                                class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500 font-mono">
                            <p class="text-[10px] text-gray-400 mt-1">*Kosongkan jika belum pulang</p>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Status Kehadiran</label>
                        <select name="status" class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                            <option value="Hadir" {{ $attendance->status == 'Hadir' ? 'selected' : '' }}>✅ Hadir Tepat Waktu</option>
                            <option value="Telat" {{ $attendance->status == 'Telat' ? 'selected' : '' }}>⚠️ Hadir Terlambat</option>
                            <option value="Sakit" {{ $attendance->status == 'Sakit' ? 'selected' : '' }}>💊 Sakit</option>
                            <option value="Izin" {{ $attendance->status == 'Izin' ? 'selected' : '' }}>✉️ Izin</option>
                            <option value="Alpha" {{ $attendance->status == 'Alpha' ? 'selected' : '' }}>❌ Alpha</option>
                        </select>
                    </div>

                    <div class="flex gap-4 pt-4">
                        <button type="submit" class="flex-1 bg-blue-600 text-white py-3 rounded-xl font-bold hover:bg-blue-700 transition">
                            Simpan Perubahan
                        </button>
                        <a href="{{ route('admin.students.show', $attendance->user_id) }}" class="px-6 py-3 bg-gray-100 text-gray-600 rounded-xl font-bold hover:bg-gray-200 transition">
                            Batal
                        </a>
                    </div>
                </form>
            </div>

        </div>
    </div>
</x-app-layout>
