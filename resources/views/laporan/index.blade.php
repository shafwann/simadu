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
                <div class="bg-gray-50 p-3 rounded-lg border mb-4">
                    <h4 class="font-semibold mb-2">{{ $lap->nama_file }}</h4>
                    @if($lap->is_validated)
                        <span class="text-green-600 text-sm font-semibold">✔ Sudah divalidasi admin</span>
                    @else
                        <span class="text-yellow-600 text-sm font-semibold">⏳ Menunggu validasi admin</span>
                    @endif

                    {{-- Preview PDF langsung --}}
                    @if(Str::endsWith($lap->file_path, '.pdf'))
                        <iframe 
                            src="{{ route('laporan.preview', $lap->id) }}" 
                            class="w-full h-96 rounded-lg border"
                            frameborder="0">
                        </iframe>
                    @endif

                        @if(!$lap->is_validated)
                            <a href="{{ route('laporan.edit', $lap->id) }}"
                            class="text-yellow-600 font-semibold">
                                Edit
                            </a>
                        @endif
                    </div>

                </div>
            @empty
                <p class="text-gray-400 italic">Belum ada laporan</p>
            @endforelse
        </div>

    </div>
</x-app-layout>
