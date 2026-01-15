<x-app-layout>
    <x-slot name="header">
        <h2 class="font-black text-xl text-gray-800 leading-tight">
            Ajukan Pembimbing Instansi
        </h2>
        <p class="text-sm text-gray-500 mt-1">
            Data pembimbing akan diverifikasi oleh admin
        </p>
    </x-slot>

    <div class="py-12 bg-white min-h-screen">
        <div class="max-w-3xl mx-auto px-6 lg:px-8">

            {{-- ALERT --}}
            @if(session('success'))
                <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-xl text-green-800 text-sm font-medium">
                    {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-xl text-red-700 text-sm">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- STATUS PENGAJUAN --}}
            @if($request)
                @if($request->status === 'pending')
                    <div class="mb-6 p-4 bg-yellow-50 border border-yellow-200 rounded-xl text-yellow-800 text-sm font-semibold">
                        ⏳ Pengajuan pembimbing sedang menunggu persetujuan admin.
                    </div>

                @elseif($request->status === 'approved')
                    <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-xl text-green-800 text-sm font-semibold">
                        ✅ Pengajuan pembimbing disetujui. Anda sekarang memiliki dosen pembimbing.
                    </div>

                @elseif($request->status === 'rejected')
                    <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-xl text-red-800 text-sm font-semibold">
                        ❌ Pengajuan pembimbing ditolak. Silakan ajukan ulang.
                    </div>
                @endif
            @endif




            {{-- CARD --}}
            <div class="bg-white border border-gray-100 rounded-3xl shadow-xl shadow-gray-100/50 p-8">

                <form method="POST" action="{{ route('mahasiswa.pembimbing.request.store') }}">
                    @csrf

                    {{-- NAMA DOSEN --}}
                    <div class="mb-6">
                        <label class="block text-sm font-bold text-gray-700 mb-2">
                            Nama Dosen Pembimbing
                        </label>
                        <input
                            type="text"
                            name="nama_dosen"
                            value="{{ old('nama_dosen') }}"
                            class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                            placeholder="Contoh: Dr. Budi Santoso, M.Kom"
                            required
                        >
                    </div>

                    {{-- NIDN --}}
                    <div class="mb-6">
                        <label class="block text-sm font-bold text-gray-700 mb-2">
                            NIDN
                        </label>
                        <input
                            type="text"
                            name="nidn"
                            value="{{ old('nidn') }}"
                            class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                            placeholder="Masukkan NIDN dosen"
                            required
                        >
                    </div>

                    {{-- EMAIL DOSEN --}}
                    <div class="mb-6">
                        <label class="block text-sm font-bold text-gray-700 mb-2">
                            Email Dosen
                        </label>
                        <input
                            type="email"
                            name="email"
                            value="{{ old('email') }}"
                            class="w-full rounded-xl border-gray-300"
                            placeholder="email@domain.ac.id"
                            required
                        >
                    </div>

                    {{-- PASSWORD DOSEN --}}
                    <div class="mb-6">
                        <label class="block text-sm font-bold text-gray-700 mb-2">
                            Password Akun Dosen
                        </label>
                        <input
                            type="password"
                            name="password"
                            class="w-full rounded-xl border-gray-300"
                            placeholder="Minimal 8 karakter"
                            required
                        >
                    </div>


                    {{-- INSTANSI (READ ONLY) --}}
                    <div class="mb-6">
                        <label class="block text-sm font-bold text-gray-700 mb-2">
                            Instansi
                        </label>
                        <input
                            type="text"
                            value="{{ $mahasiswa->instansi }}"
                            class="w-full rounded-xl bg-gray-100 border-gray-200 text-gray-600"
                            readonly
                        >
                    </div>

                    {{-- PRODI (READ ONLY) --}}
                    <div class="mb-8">
                        <label class="block text-sm font-bold text-gray-700 mb-2">
                            Program Studi
                        </label>
                        <input
                            type="text"
                            value="{{ $mahasiswa->prodi }}"
                            class="w-full rounded-xl bg-gray-100 border-gray-200 text-gray-600"
                            readonly
                        >
                    </div>

                    {{-- SUBMIT --}}
                    <div class="flex justify-end">
                        <button
                            type="submit"
                            class="px-6 py-3 rounded-2xl bg-blue-700 text-white font-bold hover:bg-blue-800 transition shadow-lg shadow-blue-100"
                        >
                            Ajukan Pembimbing
                        </button>
                    </div>

                </form>

            </div>
        </div>
    </div>
</x-app-layout>
