<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Dashboard Pembimbing
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <h1 class="text-2xl font-bold mb-6">
                Mahasiswa Bimbingan
            </h1>

            @if($mahasiswa->count())
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($mahasiswa as $mhs)
                        <div class="bg-white shadow-md rounded-xl p-6 hover:shadow-lg transition">

                            <h2 class="text-lg font-bold mb-1">
                                {{ $mhs->name }}
                            </h2>

                            <p class="text-sm text-gray-500 mb-2">
                                NIM: {{ $mhs->nim }}
                            </p>

                            <div class="text-sm text-gray-700 space-y-1">
                                <p>
                                    <span class="font-semibold">Prodi:</span>
                                    {{ $mhs->prodi }}
                                </p>
                                <p>
                                    <span class="font-semibold">Instansi:</span>
                                    {{ $mhs->instansi }}
                                </p>
                            </div>

                            <div class="mt-4">
                                <a href="{{ route('pembimbing.mahasiswa.detail', $mhs->id) }}"
                                   class="inline-block text-sm font-semibold text-blue-600 hover:text-blue-800">
                                    Lihat Detail →
                                </a>
                            </div>

                        </div>
                    @endforeach
                </div>
            @else
                <div class="bg-white p-6 rounded-lg shadow text-gray-600">
                    Belum ada mahasiswa bimbingan.
                </div>
            @endif

        </div>
    </div>
</x-app-layout>
