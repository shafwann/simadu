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
                    <p class="text-gray-500 mt-2 text-sm">Kelola akun admin untuk setiap divisi/bidang di Bapperlitbang.</p>
                </div>

                <a href="{{ route('admin.manage.create') }}" class="flex items-center gap-2 bg-gray-900 text-white px-6 py-3 rounded-2xl font-bold hover:bg-black transition shadow-lg hover:-translate-y-0.5">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Tambah Admin Baru
                </a>
            </div>

            {{-- TABLE --}}
            <div class="bg-white border border-gray-100 rounded-[2rem] shadow-2xl shadow-purple-50/50 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50/50 border-b border-gray-100 text-xs uppercase tracking-widest text-gray-400 font-bold">
                                <th class="px-8 py-6">Nama Admin</th>
                                <th class="px-8 py-6">Bidang / Divisi</th>
                                <th class="px-8 py-6">Email</th>
                                <th class="px-8 py-6 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($admins as $admin)
                            <tr class="group hover:bg-purple-50/30 transition duration-200">
                                <td class="px-8 py-5 font-bold text-gray-900">{{ $admin->name }}</td>
                                <td class="px-8 py-5">
                                    <span class="px-3 py-1 bg-blue-50 text-blue-700 rounded-lg text-xs font-bold border border-blue-100">
                                        {{ $admin->bidang }}
                                    </span>
                                </td>
                                <td class="px-8 py-5 text-sm text-gray-500">{{ $admin->email }}</td>
                                <td class="px-8 py-5 text-center">
                                    <form action="{{ route('admin.manage.destroy', $admin->id) }}" method="POST" onsubmit="return confirm('Hapus akses admin {{ $admin->name }}?');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-red-500 hover:text-red-700 font-bold text-sm bg-red-50 px-3 py-1 rounded-lg hover:bg-red-100 transition">
                                            Hapus Akses
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="px-8 py-12 text-center text-gray-400 italic">
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
