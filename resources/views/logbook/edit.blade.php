<x-app-layout>
  <div class="py-12 bg-gray-50 min-h-screen">
    <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
      <div class="bg-white shadow-sm sm:rounded-2xl border border-gray-100 p-8">
        <h3 class="text-xl font-bold text-gray-800 mb-2">Edit Logbook (Ditolak)</h3>
        <p class="text-sm text-red-600 mb-6">
          Alasan ditolak: <span class="font-semibold">{{ $logbook->rejected_note }}</span>
        </p>

        <form method="POST" action="{{ route('logbook.update', $logbook->id) }}">
          @csrf
          @method('PUT')

          <div class="mb-4">
            <label class="block text-sm font-semibold text-gray-700 mb-2">Tanggal</label>
            <input type="date" name="tanggal" value="{{ $logbook->tanggal }}"
                   class="w-full rounded-xl border-gray-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition">
          </div>

          <div class="mb-6">
            <label class="block text-sm font-semibold text-gray-700 mb-2">Deskripsi Kegiatan</label>
            <textarea name="kegiatan" rows="3"
                      class="w-full rounded-xl border-gray-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition">{{ $logbook->kegiatan }}</textarea>
          </div>

          <div class="flex justify-end gap-3">
            <a href="{{ route('logbook.index') }}"
               class="px-6 py-3 rounded-xl bg-gray-200 text-gray-700 font-bold hover:bg-gray-300 transition">
              Batal
            </a>
            <button type="submit"
               class="px-6 py-3 rounded-xl bg-yellow-500 text-white font-bold hover:bg-yellow-600 transition">
              Simpan & Ajukan Ulang
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</x-app-layout>