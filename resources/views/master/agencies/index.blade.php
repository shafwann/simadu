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

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <table class="w-full text-sm text-left text-gray-500">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                            <tr>
                                <th class="px-6 py-3">Nama Admin</th>
                                <th class="px-6 py-3">Email</th>
                                <th class="px-6 py-3">Kategori Instansi</th>
                                <th class="px-6 py-3">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($agencies as $admin)
                            <tr class="bg-white border-b hover:bg-gray-50">
                                <td class="px-6 py-4 font-bold text-gray-900">{{ $admin->name }}</td>
                                <td class="px-6 py-4">{{ $admin->email }}</td>
                                <td class="px-6 py-4">
                                    <span class="bg-blue-100 text-blue-800 text-xs font-medium mr-2 px-2.5 py-0.5 rounded border border-blue-400">
                                        {{ $admin->kategori_instansi }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <form action="{{ route('master.agencies.destroy', $admin->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus instansi ini? Pengaturan instansi juga akan terhapus.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900 font-bold">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
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
