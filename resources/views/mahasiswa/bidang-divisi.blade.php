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

        <div class="bg-white p-6 rounded-2xl border shadow-sm">
            <h3 class="font-bold text-lg mb-4">Bidang / Divisi Penempatan</h3>

            <form method="POST" action="{{ route('mahasiswa.bidang.update') }}" class="space-y-4">
                @csrf
                @method('PATCH')

                <label class="block">
                
                    <input
                        type="text"
                        name="bidang"
                        id="bidang"
                        list="bidangList"
                        value="{{ old('bidang', auth()->user()->bidang) }}"
                        class="mt-1 block w-full rounded-xl border-gray-200 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        placeholder="Ketik manual atau pilih dari daftar..."
                        required
                    >

                    <datalist id="bidangList">
                        @foreach($bidangOptions as $bidang)
                            <option value="{{ $bidang }}"></option>
                        @endforeach
                    </datalist>
                </label>

                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition">
                    Simpan
                </button>
            </form>
        </div>

    </div>
</x-app-layout>
