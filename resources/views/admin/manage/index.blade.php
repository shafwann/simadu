<x-app-layout>
    <div class="py-12 bg-white min-h-screen text-gray-900">
        <div class="max-w-7xl mx-auto px-6 lg:px-8">

            {{-- HEADER --}}
            <div class="flex flex-col md:flex-row justify-between items-center mb-10 gap-4">
                <div>
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-purple-50 border border-purple-100 mb-2">
                        <span class="w-2 h-2 rounded-full bg-purple-600"></span>
                        <span class="text-xs font-bold text-purple-700 uppercase tracking-wider">Super Admin Area</span>
                    </div>
                    <h1 class="text-3xl font-black text-gray-900 tracking-tight">Manajemen Admin Bidang</h1>
                    <p class="text-gray-500 mt-2 text-sm">Kelola akun admin untuk setiap divisi/bidang di <b>{{ Auth::user()->kategori_instansi ?? 'UMUM' }}</b>.</p>
                </div>

            <a href="{{ route('admin.manage.create') }}"
            class="flex items-center gap-2 bg-blue-600 text-white px-6 py-3 rounded-2xl font-bold
                    hover:bg-blue-700 transition shadow-lg hover:-translate-y-0.5">

                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 4v16m8-8H4"></path>
                </svg>

                Tambah Admin Baru
            </a>
            </div>

            {{-- Flash Messages --}}
            @if (session('success'))
                <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-2xl">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-2xl">
                    {{ session('error') }}
                </div>
            @endif

            {{-- TABLE --}}
            <div class="bg-white border border-gray-100 rounded-[2rem] shadow-2xl shadow-purple-50/50 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50/50 border-b border-gray-100 text-xs uppercase tracking-widest text-gray-400 font-bold">
                                <th class="px-8 py-6">Nama Admin</th>
                                <th class="px-8 py-6">Bidang / Divisi</th>
                                <th class="px-8 py-6">Email</th>
                                <th class="px-8 py-6 text-center">Status</th>
                                <th class="px-8 py-6 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($admins as $admin)
                            <tr class="group hover:bg-purple-50/30 transition duration-200">
                                <td class="px-8 py-5">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center text-purple-700 font-bold text-sm">
                                            {{ substr($admin->name, 0, 2) }}
                                        </div>
                                        <div>
                                            <div class="font-bold text-gray-900">{{ $admin->name }}</div>
                                            <div class="text-xs text-gray-500">{{ $admin->role }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-8 py-5">
                                    <span class="px-3 py-1 bg-blue-50 text-blue-700 rounded-lg text-xs font-bold border border-blue-100">
                                        {{ $admin->bidang }}
                                    </span>
                                </td>
                                <td class="px-8 py-5 text-sm text-gray-600">{{ $admin->email }}</td>
                                <td class="px-8 py-5 text-center">
                                    @if($admin->status === 'aktif')
                                        <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            <span class="w-1.5 h-1.5 rounded-full bg-green-600"></span> Aktif
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                            <span class="w-1.5 h-1.5 rounded-full bg-gray-500"></span> Nonaktif
                                        </span>
                                    @endif
                                </td>
                                <td class="px-8 py-5 text-center">
                                    @if($admin->id !== Auth::id() && $admin->bidang !== 'Administrator Sistem')
                                        <form action="{{ route('admin.manage.destroy', $admin->id) }}" method="POST" onsubmit="return confirm('Hapus akses admin {{ $admin->name }}?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-500 hover:text-red-700 font-bold text-sm bg-red-50 px-3 py-1 rounded-lg hover:bg-red-100 transition">
                                                Hapus
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-gray-400 text-sm">Tidak dapat dihapus</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-8 py-12 text-center text-gray-400 italic">
                                    Belum ada admin bidang lain.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
