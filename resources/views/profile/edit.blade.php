<x-app-layout>
    <div class="py-12 bg-white min-h-screen">
        <div class="max-w-7xl mx-auto px-6 lg:px-8 space-y-10">

            <div class="border-b border-gray-100 pb-6">
                <h2 class="text-3xl font-black text-gray-900">Pengaturan Akun</h2>
                <p class="text-gray-500 mt-1">Kelola informasi profil dan keamanan akun Anda.</p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">

                <div class="hidden lg:block lg:col-span-1 space-y-6">
                    <div class="p-6 bg-blue-50 rounded-3xl border border-blue-100">
                        {{-- Header Profil --}}
                        <div class="flex items-center gap-4 mb-6">
                            <div class="w-14 h-14 bg-blue-600 rounded-full flex items-center justify-center text-white font-bold text-xl shrink-0 shadow-lg shadow-blue-200">
                                {{ substr(Auth::user()->name, 0, 1) }}
                            </div>
                            <div class="overflow-hidden">
                                <h3 class="font-bold text-gray-900 truncate" title="{{ Auth::user()->name }}">
                                    {{ Auth::user()->name }}
                                </h3>
                                <div class="flex items-center gap-2">
                                    <p class="text-xs text-blue-600 uppercase font-bold bg-blue-100 px-2 py-0.5 rounded-md inline-block">
                                        {{ Auth::user()->role }}
                                    </p>
                                    @if(Auth::user()->status === 'aktif')
                                        <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse" title="Status: Aktif"></span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- Informasi Tambahan (NIM & Instansi) --}}
                        @if(Auth::user()->role === 'mahasiswa')
                            <div class="space-y-3 mb-6 border-t border-blue-100 pt-4">
                                @if(Auth::user()->nim)
                                    <div class="flex items-start gap-3">
                                        <div class="mt-0.5 p-1.5 bg-white rounded-lg text-blue-500 shadow-sm">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0c0 .883-.393 1.627-1.017 2.122C9.274 8.795 9.01 9 8.646 9m4.308 0c-.364 0-.628-.205-1.337-.878C10.393 7.627 10 6.883 10 6m0 0h4m-4 0h4m-4 0h4"></path></svg>
                                        </div>
                                        <div>
                                            <p class="text-[10px] uppercase font-bold text-blue-400 tracking-wider">NIM / NISN</p>
                                            <p class="text-sm font-bold text-gray-700 font-mono">{{ Auth::user()->nim }}</p>
                                        </div>
                                    </div>
                                @endif

                                @if(Auth::user()->instansi)
                                    <div class="flex items-start gap-3">
                                        <div class="mt-0.5 p-1.5 bg-white rounded-lg text-blue-500 shadow-sm">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                                        </div>
                                        <div>
                                            <p class="text-[10px] uppercase font-bold text-blue-400 tracking-wider">Instansi / Sekolah</p>
                                            <p class="text-sm font-bold text-gray-700 leading-tight">{{ Auth::user()->instansi }}</p>
                                            @if(Auth::user()->prodi)
                                                <p class="text-xs text-gray-500 mt-0.5">{{ Auth::user()->prodi }}</p>
                                            @endif
                                        </div>
                                    </div>
                                @endif

                                {{-- Pembimbing Lapangan (Jika Ada) --}}
                                @if(Auth::user()->nama_pembimbing || Auth::user()->nip_pembimbing || Auth::user()->jabatan_pembimbing)
                                    <div class="flex items-start gap-3">
                                        <div class="mt-0.5 p-1.5 bg-white rounded-lg text-blue-500 shadow-sm">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="text-[10px] uppercase font-bold text-blue-400 tracking-wider">Pembimbing Lapangan</p>

                                            @if(Auth::user()->nama_pembimbing)
                                                <p class="text-sm font-bold text-gray-700">{{ Auth::user()->nama_pembimbing }}</p>
                                            @endif

                                            @if(Auth::user()->jabatan_pembimbing)
                                                <p class="text-xs text-gray-500 mt-0.5">{{ Auth::user()->jabatan_pembimbing }}</p>
                                            @endif

                                            @if(Auth::user()->nip_pembimbing)
                                                <p class="text-[10px] text-gray-400 truncate">NIP: {{ Auth::user()->nip_pembimbing }}</p>
                                            @endif
                                        </div>
                                    </div>
                                @endif

                            </div>
                        @endif

                        <p class="text-xs text-gray-500 leading-relaxed border-t border-blue-100 pt-4">
                            Jaga kerahasiaan password Anda. Hubungi admin bidang <strong>{{ Auth::user()->bidang ?? 'Sekretariat' }}</strong> jika ada kesalahan data yang tidak bisa diubah.
                        </p>
                    </div>
                </div>

                <div class="lg:col-span-2 space-y-8">

                    <div class="p-8 bg-white border border-gray-200 rounded-[2rem] shadow-xl shadow-gray-100/50">
                        <h3 class="text-xl font-bold text-gray-800 mb-1">Informasi Profil</h3>
                        <p class="text-sm text-gray-800 mb-6">Perbarui nama lengkap dan alamat email akun Anda.</p>

                        <div class="max-w-xl">
                            @include('profile.partials.update-profile-information-form')
                        </div>
                    </div>

                    <div class="p-8 bg-white border border-gray-200 rounded-[2rem] shadow-xl shadow-gray-100/50">
                        <h3 class="text-xl font-bold text-gray-800 mb-1">Ganti Password</h3>
                        <p class="text-sm text-gray-800 mb-6">Pastikan menggunakan password yang aman.</p>

                        <div class="max-w-xl">
                            @include('profile.partials.update-password-form')
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
