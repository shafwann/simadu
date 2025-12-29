<x-app-layout>
    {{-- <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard Mahasiswa') }}
        </h2>
    </x-slot> --}}

    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-blue-100">
                <div class="p-8 text-gray-900 flex items-center justify-between">
                    <div>
                        <h3 class="text-2xl font-bold text-blue-600">Selamat Datang, {{ Auth::user()->name }}! 👋</h3>
                        <p class="text-gray-500 mt-1">Jangan lupa untuk melakukan presensi dan mengisi logbook hari ini.</p>
                    </div>
                    <div class="hidden md:block">
                        <span class="text-sm font-medium text-gray-400">Role: <span class="text-blue-500 uppercase">{{ Auth::user()->role }}</span></span>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                <a href="{{ route('attendance.index') }}" class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 hover:border-blue-500 transition group">
                    <div class="flex items-center space-x-4">
                        <div class="p-3 bg-blue-100 text-blue-600 rounded-xl group-hover:bg-blue-600 group-hover:text-white transition">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <div>
                            <h4 class="font-bold text-gray-800">Presensi</h4>
                            <p class="text-xs text-gray-500">Catat kehadiran harianmu</p>
                        </div>
                    </div>
                </a>

                <a href="{{ route('logbook.index') }}" class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 hover:border-indigo-500 transition group">
                    <div class="flex items-center space-x-4">
                        <div class="p-3 bg-indigo-100 text-indigo-600 rounded-xl group-hover:bg-indigo-600 group-hover:text-white transition">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                        </div>
                        <div>
                            <h4 class="font-bold text-gray-800">Logbook</h4>
                            <p class="text-xs text-gray-500">Laporkan kegiatan magang</p>
                        </div>
                    </div>
                </a>

                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                    <div class="flex items-center justify-between">
                        <div>
                            <h4 class="font-bold text-gray-800 italic text-sm">Status Akun</h4>
                            <p class="text-green-500 font-bold mt-1">Aktif</p>
                        </div>
                        <div class="p-3 bg-green-100 text-green-600 rounded-xl">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
