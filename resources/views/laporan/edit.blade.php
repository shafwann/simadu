<x-app-layout>
    <div class="py-12 max-w-3xl mx-auto space-y-6">

        <div class="bg-white p-6 rounded-2xl border shadow-sm">
            <h3 class="font-bold text-lg mb-4">Edit Laporan</h3>

            <p class="text-sm text-gray-500 mb-4">
                File lama: <span class="font-semibold">{{ $laporan->nama_file }}</span>
            </p>

            <form action="{{ route('laporan.update', $laporan->id) }}"
                  method="POST"
                  enctype="multipart/form-data"
                  class="space-y-4">
                @csrf
                @method('PUT')

                <label class="block">
                    <span class="text-gray-700 font-medium">Ganti File (PDF / DOC / DOCX)</span>
                    <input type="file"
                           name="file"
                           required
                           accept=".pdf,.doc,.docx"
                           class="mt-1 block w-full text-sm text-gray-500
                                  file:mr-4 file:py-2 file:px-4
                                  file:rounded-xl file:border-0
                                  file:text-sm file:font-semibold
                                  file:bg-blue-50 file:text-blue-700
                                  hover:file:bg-blue-100">
                </label>

                <div class="flex gap-3">
                    <button type="submit"
                        class="px-6 py-2 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition">
                        Simpan Perubahan
                    </button>

                    <a href="{{ route('laporan.index') }}"
                       class="px-6 py-2 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 transition">
                        Batal
                    </a>
                </div>
            </form>
        </div>

    </div>
</x-app-layout>
