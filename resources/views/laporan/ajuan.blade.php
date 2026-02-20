<x-app-layout>
    <div class="py-12 max-w-3xl mx-auto space-y-6">

        {{-- Alert Error --}}
        @if ($errors->any())
            <div class="p-4 bg-red-50 text-red-700 rounded-xl border border-red-100">
                <ul class="list-disc list-inside text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Info Penolakan --}}
        @if ($laporan->rejected_note)
            <div class="bg-red-50 p-6 rounded-2xl border border-red-200 shadow-sm">
                <div class="flex items-start gap-3">
                    <svg class="w-6 h-6 text-red-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                        </path>
                    </svg>
                    <div class="flex-1">
                        <h4 class="text-sm font-bold text-red-800 mb-1">Alasan Ditolak</h4>
                        <p class="text-sm text-red-700 leading-relaxed">{{ $laporan->rejected_note }}</p>
                    </div>
                </div>
            </div>
        @endif

        {{-- Form Ajukan Kembali --}}
        <div class="bg-white p-6 rounded-2xl border shadow-sm">
            <h3 class="font-bold text-lg mb-4 text-amber-700">Ajukan Kembali Laporan</h3>

            <p class="text-sm text-gray-500 mb-4">
                File lama: <span class="font-semibold">{{ $laporan->nama_file }}</span>
            </p>

            <form action="{{ route('laporan.ajuan-kembali', $laporan->id) }}" method="POST" enctype="multipart/form-data"
                class="space-y-4">
                @csrf
                @method('PATCH')

                <label class="block">
                    <span class="text-gray-700 font-medium">Ganti File Laporan (PDF / DOC / DOCX)</span>
                    <input type="file" name="file" required accept=".pdf,.doc,.docx"
                        class="mt-1 block w-full text-sm text-gray-500
                                  file:mr-4 file:py-2 file:px-4
                                  file:rounded-xl file:border-0
                                  file:text-sm file:font-semibold
                                  file:bg-amber-50 file:text-amber-700
                                  hover:file:bg-amber-100">
                </label>

                <div class="flex gap-3">
                    <button type="submit"
                        class="px-6 py-2 bg-amber-600 text-white font-bold rounded-xl hover:bg-amber-700 transition shadow-md">
                        Ajukan Kembali
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
