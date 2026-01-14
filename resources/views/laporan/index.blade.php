<x-app-layout>
    <div class="py-12 max-w-3xl mx-auto space-y-6">

        {{-- Notifikasi --}}
        @if(session('success'))
            <div class="p-4 bg-green-50 text-green-700 rounded-xl border border-green-100 text-center">
                {{ session('success') }}
            </div>
        @endif
        @if($errors->any())
            <div class="p-4 bg-red-50 text-red-700 rounded-xl border border-red-100 text-center">
                {{ implode(', ', $errors->all()) }}
            </div>
        @endif

        {{-- Form Upload --}}
        <div class="bg-white p-6 rounded-2xl border shadow-sm">
            <h3 class="font-bold text-lg mb-4">Unggah Laporan</h3>
            <form action="{{ route('laporan.upload') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf

                <label class="block">
                    <span class="text-gray-700 font-medium">Pilih File (max 20MB)</span>
                    <input type="file" name="file" accept=".pdf,.doc,.docx"
                        class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4
                               file:rounded-xl file:border-0
                               file:text-sm file:font-semibold
                               file:bg-blue-50 file:text-blue-700
                               hover:file:bg-blue-100
                        ">
                </label>

                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition">
                    Upload
                </button>
            </form>
        </div>

        {{-- Daftar & Preview --}}
        <div class="bg-white p-6 rounded-2xl border shadow-sm">
            <h3 class="font-bold text-lg mb-4">Daftar Laporan Saya</h3>

            @forelse($laporans as $lap)
    <div class="border rounded-xl p-4 mb-4 bg-white">

        <div class="flex items-start justify-between gap-4">
            {{-- INFO FILE --}}
            <div class="space-y-1">
                <p class="font-semibold text-gray-800">
                    {{ $lap->nama_file }}
                </p>
            </div>

            {{-- AKSI --}}
            <div class="flex items-center gap-3">
                @if(Str::endsWith($lap->file_path, '.pdf'))
                    <a href="{{ route('laporan.preview', $lap->id) }}"
                    target="_blank"
                    class="px-4 py-2 rounded-xl text-sm font-medium
                            bg-blue-600 text-white
                            hover:bg-blue-400
                            active:bg-blue-200
                            border border-blue-300
                            shadow-sm
                            transition-all duration-150">
                        Preview
</a>



                @endif

                @if(!$lap->is_validated)
                    <a href="{{ route('laporan.edit', $lap->id) }}"
                    class="px-4 py-2 rounded-xl text-sm font-semibold
                            bg-amber-600 text-white
                            hover:bg-amber-400
                            active:bg-amber-200
                            border border-amber-300
                            shadow-sm
                            transition-all duration-150">
                        Edit
                    </a>



                @else
                    <span class="px-4 py-2 text-sm text-gray-400 border rounded-xl cursor-not-allowed">
                        Edit
                    </span>
                @endif
            </div>
        </div>

        {{-- PREVIEW PDF --}}
        @if(Str::endsWith($lap->file_path, '.pdf'))
            <iframe
                src="{{ route('laporan.preview', $lap->id) }}"
                class="w-full h-80 mt-4 rounded-lg border"
                frameborder="0">
            </iframe>
        @endif

        {{-- STATUS MENUNGGU VALIDASI --}}
        @if(!$lap->is_validated)
            <div class="mt-4 bg-yellow-50 p-4 rounded-xl border border-yellow-100">
                <h4 class="text-sm font-bold text-yellow-800 mb-1">
                    Laporan sedang ditinjau
                </h4>
                <p class="text-xs text-yellow-700 leading-relaxed">
                    Laporan yang telah diunggah tidak dapat diubah
                    sampai proses validasi oleh admin selesai.
                </p>
            </div>
        @else
            <div class="mt-4 bg-green-50 p-4 rounded-xl border border-green-100">
                <h4 class="text-sm font-bold text-green-800 mb-1">
                    Laporan telah divalidasi
                </h4>
                <p class="text-xs text-green-700 leading-relaxed">
                    Laporan Anda telah disetujui oleh admin dan
                    tidak dapat diubah kembali.
                </p>
            </div>
        @endif


    </div>
@empty
    <p class="text-gray-400 italic">Belum ada laporan.</p>
@endforelse
 </div>

    </div>
</x-app-layout>
