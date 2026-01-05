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
                        <div class="flex items-center gap-4 mb-4">
                            <div class="w-12 h-12 bg-blue-600 rounded-full flex items-center justify-center text-white font-bold text-xl">
                                {{ substr(Auth::user()->name, 0, 1) }}
                            </div>
                            <div>
                                <h3 class="font-bold text-gray-900">{{ Auth::user()->name }}</h3>
                                <p class="text-xs text-blue-600 uppercase font-bold">{{ Auth::user()->role }}</p>
                            </div>
                        </div>
                        <p class="text-sm text-gray-600 leading-relaxed">
                            Jaga kerahasiaan password Anda. Hubungi admin jika ada kesalahan data yang tidak bisa diubah.
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
