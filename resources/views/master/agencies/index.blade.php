<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Kelola Instansi (Master)') }}
            </h2>
            <a href="{{ route('master.agencies.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-bold hover:bg-blue-700">
                + Tambah Instansi Baru
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                    {{ session('success') }}
                </div>
            @endif

            {{-- ✅ RUNNING TEXT (MARQUEE) - GLOBAL BRANDING --}}
            <div class="mb-6 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-sm font-black text-gray-800 uppercase tracking-wider">
                            Running Text (Marquee)
                        </h3>
                        <span class="text-xs text-gray-400 font-bold uppercase">
                            Global Branding (MASTER)
                        </span>
                    </div>

                    {{-- PREVIEW --}}
                    @if(!empty($about?->running_text))
                        <div class="mb-4 border border-blue-200 bg-blue-50 rounded-lg px-4 py-3">
                            <div class="text-xs font-bold text-blue-700 mb-2">Preview</div>
                            <div class="text-sm text-blue-900 font-semibold">
                                {{ $about->running_text }}
                            </div>
                        </div>
                    @else
                        <div class="mb-4 border border-gray-200 bg-gray-50 rounded-lg px-4 py-3 text-xs text-gray-500">
                            Belum ada running text. Silakan isi lalu simpan.
                        </div>
                    @endif

                    {{-- FORM --}}
                    {{--
                    <form method="POST" action="{{ route('master.settings.running-text.update') }}">
                        @csrf
                        @method('PUT')

                        <div class="mt-4">
                            <label class="block text-sm font-bold text-gray-700">Running Text (Marquee)</label>
                            <textarea name="running_text" rows="3"
                                class="mt-1 w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">{{ old('running_text', $about->running_text ?? '') }}</textarea>
                            <p class="text-xs text-gray-400 mt-1">
                                Contoh: DISDIKBUD | RIZKI SAPTONO | DISPAPORA | ...
                            </p>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-bold hover:bg-blue-700">
                                Simpan Running Text
                            </button>
                        </div>
                    </form>
                    --}}
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <table class="w-full text-sm text-left text-gray-500">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                            <tr>
                                <th class="px-6 py-3">Kategori</th>
                                <th class="px-6 py-3">Super Admin</th>
                                <th class="px-6 py-3">Email</th>
                                <th class="px-6 py-3 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($agencies as $admin)
                            <tr class="bg-white border-b hover:bg-gray-50">
                                <td class="px-6 py-4">
                                    <span class="bg-blue-100 text-blue-800 text-xs font-bold mr-2 px-2.5 py-0.5 rounded border border-blue-400">
                                        {{ $admin->kategori_instansi }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 font-bold text-gray-900">
                                    {{ $admin->name }}
                                </td>
                                <td class="px-6 py-4">
                                    {{ $admin->email }}
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        {{-- TOMBOL EDIT --}}
                                        <a href="{{ route('master.agencies.edit', $admin->id) }}"
                                           class="text-white bg-yellow-500 hover:bg-yellow-600 focus:ring-4 focus:ring-yellow-300 font-medium rounded-lg text-xs px-3 py-2 focus:outline-none">
                                            Edit
                                        </a>

                                        {{-- TOMBOL HAPUS --}}
                                        <form action="{{ route('master.agencies.destroy', $admin->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus instansi {{ $admin->kategori_instansi }}? Data About Setting juga akan terhapus.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-white bg-red-600 hover:bg-red-700 focus:ring-4 focus:ring-red-300 font-medium rounded-lg text-xs px-3 py-2 focus:outline-none">
                                                Hapus
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="px-6 py-4 text-center text-gray-400">
                                    Belum ada instansi. Klik tombol tambah di atas.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>

                    <div class="mt-4">
                        {{ $agencies->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
