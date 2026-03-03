<x-app-layout>
    <div class="py-12 bg-white min-h-screen">
        <div class="max-w-7xl mx-auto px-6 lg:px-8 space-y-10">

            <div class="border-b border-gray-100 pb-6">
                <h2 class="text-3xl font-black text-gray-900">Pengaturan Akun</h2>
                <p class="text-gray-500 mt-1">Kelola informasi profil dan keamanan akun Anda.</p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">

                {{-- SIDEBAR --}}
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
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0c0 .883-.393 1.627-1.017 2.122C9.274 8.795 9.01 9 8.646 9m4.308 0c-.364 0-.628-.205-1.337-.878C10.393 7.627 10 6.883 10 6m0 0h4m-4 0h4m-4 0h4"></path>
                                    </svg>
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
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                    </svg>
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

                            @if(isset($approvedRequest) && $approvedRequest)
                            <div class="flex items-start gap-3">
                                <div class="mt-0.5 p-1.5 bg-white rounded-lg text-blue-500 shadow-sm">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                </div>

                                <div class="min-w-0 flex-1">
                                    <div class="flex items-center gap-2">
                                        <p class="text-[10px] uppercase font-bold text-blue-400 tracking-wider">
                                            Pembimbing Instansi
                                        </p>

                                        {{-- Badge "Sudah disetujui" --}}
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-green-100 text-green-800">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            Sudah disetujui
                                        </span>
                                    </div>

                                    {{-- Nama Dosen --}}
                                    <p class="text-sm font-bold text-gray-700 truncate">
                                        {{ $approvedRequest->nama_dosen ?? '-' }}
                                    </p>

                                    {{-- Email --}}
                                    <p class="text-xs text-gray-500 mt-0.5 truncate">
                                        {{ $approvedRequest->email ?? '-' }}
                                    </p>

                                    {{-- NIDN --}}
                                    <p class="text-[10px] text-gray-400 mt-0.5 truncate">
                                        NIDN: {{ $approvedRequest->nidn ?? '-' }}
                                    </p>
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
                            Jaga kerahasiaaan password Anda. Hubungi admin bidang <strong>{{ Auth::user()->bidang ?? 'Sekretariat' }}</strong> jika ada kesalahan data yang tidak bisa diubah.
                        </p>
                    </div>
                </div>

                {{-- KONTEN UTAMA --}}
                <div class="lg:col-span-2 space-y-8">

                    {{-- =========================
                         FORM PEMBIMBING INSTANSI (AREA PUTIH)
                         - TETAP ADA biar bisa edit kapanpun
                         - Saat submit selalu jadi pending
                       ========================= --}}
                    @if(Auth::user()->role === 'mahasiswa')
                    <div class="p-8 bg-white border border-gray-200 rounded-[2rem] shadow-xl shadow-gray-100/50">
                        <div class="flex items-start justify-between gap-6">
                            <div>
                                <h3 class="text-xl font-black text-gray-900 mb-1">Ajukan Pembimbing Instansi</h3>
                                <p class="text-sm text-gray-500 leading-relaxed">
                                    Data pembimbing akan diverifikasi oleh admin. Pastikan data benar dan email aktif.
                                </p>
                            </div>
                            <div class="shrink-0 p-3 rounded-2xl bg-blue-50 border border-blue-100 text-blue-600">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 11c0 .552-.448 1-1 1H7a1 1 0 01-1-1V7c0-.552.448-1 1-1h4c.552 0 1 .448 1 1v4zm6 10H6a2 2 0 01-2-2V5a2 2 0 012-2h8l6 6v10a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                        </div>

                        {{-- ALERT --}}
                        @if(session('success'))
                        <div class="mt-6 p-4 bg-green-50 border border-green-200 rounded-xl text-green-800 text-sm font-medium">
                            {{ session('success') }}
                        </div>
                        @endif

                        @if($errors->any())
                        <div class="mt-6 p-4 bg-red-50 border border-red-200 rounded-xl text-red-700 text-sm">
                            <ul class="list-disc list-inside space-y-1">
                                @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                        @endif

                        {{-- STATUS PENGAJUAN (BERDASARKAN TERBARU) --}}
                        @if(isset($latestRequest) && $latestRequest)
                        @if($latestRequest->status === 'pending')
                        <div class="mt-6 p-4 bg-yellow-50 border border-yellow-200 rounded-xl text-yellow-800 text-sm font-semibold">
                            ⏳ Pengajuan pembimbing sedang menunggu persetujuan admin.
                        </div>
                        @elseif($latestRequest->status === 'approved')
                        <div class="mt-6 p-4 bg-green-50 border border-green-200 rounded-xl text-green-800 text-sm font-semibold">
                            ✅ Pengajuan pembimbing disetujui. Anda sekarang memiliki dosen pembimbing.
                        </div>
                        @elseif($latestRequest->status === 'rejected')
                        <div class="mt-6 p-4 bg-red-50 border border-red-200 rounded-xl text-red-800 text-sm font-semibold">
                            ❌ Pengajuan pembimbing ditolak. Silakan ajukan ulang.
                        </div>
                        @endif
                        @endif

                        <div class="mt-6">
                            <form method="POST" action="{{ route('mahasiswa.pembimbing.request.store') }}">
                                @csrf

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                                    {{-- NAMA DOSEN --}}
                                    <div class="md:col-span-2">
                                        <label class="block text-sm font-bold text-gray-700 mb-2">
                                            Nama Dosen Pembimbing
                                        </label>
                                        <input
                                            type="text"
                                            id="nama_dosen"
                                            name="nama_dosen"
                                            value="{{ old('nama_dosen', $latestRequest->nama_dosen ?? $approvedRequest->nama_dosen ?? '') }}"
                                            class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                                            placeholder="Contoh: Dr. Budi Santoso, M.Kom"
                                            required>
                                        <ul id="suggestions"></ul>
                                    </div>

                                    {{-- NIDN --}}
                                    <div>
                                        <label class="block text-sm font-bold text-gray-700 mb-2">
                                            NIDN
                                        </label>
                                        <input
                                            type="text"
                                            id="nidn"
                                            name="nidn"
                                            value="{{ old('nidn', $latestRequest->nidn ?? $approvedRequest->nidn ?? '') }}"
                                            class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                                            placeholder="Masukkan NIDN dosen"
                                            required>
                                    </div>

                                    {{-- ID DOSEN jika ada --}}
                                    <input type="hidden" name="dosen_id" id="dosen_id">

                                    {{-- EMAIL DOSEN --}}
                                    <div>
                                        <label class="block text-sm font-bold text-gray-700 mb-2">
                                            Email Dosen
                                        </label>
                                        <input
                                            type="email"
                                            id="email"
                                            name="email"
                                            value="{{ old('email', $latestRequest->email ?? $approvedRequest->email ?? '') }}"
                                            class="w-full rounded-xl border-gray-300"
                                            placeholder="email@domain.ac.id"
                                            required>
                                    </div>

                                    {{-- PASSWORD DOSEN --}}
                                    <div class="md:col-span-2">
                                        <label class="block text-sm font-bold text-gray-700 mb-2">
                                            Password Akun Dosen
                                        </label>
                                        <input
                                            type="password"
                                            id="password"
                                            name="password"
                                            class="w-full rounded-xl border-gray-300 disabled:bg-gray-100 disabled:text-gray-600"
                                            placeholder="Minimal 8 karakter"
                                            required>
                                    </div>

                                    {{-- INSTANSI (READ ONLY) --}}
                                    <div>
                                        <label class="block text-sm font-bold text-gray-700 mb-2">
                                            Instansi
                                        </label>
                                        <input
                                            type="text"
                                            value="{{ Auth::user()->instansi }}"
                                            class="w-full rounded-xl bg-gray-100 border-gray-200 text-gray-600"
                                            readonly>
                                    </div>

                                    {{-- PRODI (READ ONLY) --}}
                                    <div>
                                        <label class="block text-sm font-bold text-gray-700 mb-2">
                                            Program Studi
                                        </label>
                                        <input
                                            type="text"
                                            value="{{ Auth::user()->prodi }}"
                                            class="w-full rounded-xl bg-gray-100 border-gray-200 text-gray-600"
                                            readonly>
                                    </div>
                                </div>

                                <div class="mt-8 flex justify-end">
                                    <button
                                        type="submit"
                                        class="px-6 py-3 rounded-2xl bg-blue-700 text-white font-bold hover:bg-blue-800 transition shadow-lg shadow-blue-100"
                                        @if(isset($latestRequest) && $latestRequest && $latestRequest->status === 'pending') disabled @endif
                                        >
                                        Ajukan Pembimbing
                                    </button>
                                </div>

                                @if(isset($latestRequest) && $latestRequest && $latestRequest->status === 'pending')
                                <p class="text-sm text-gray-500 mt-4">
                                    Pengajuan sudah dikirim dan sedang diproses admin.
                                </p>
                                @endif
                            </form>
                        </div>
                    </div>
                    @endif

                    {{-- CARD: Informasi Profil --}}
                    <div class="p-8 bg-white border border-gray-200 rounded-[2rem] shadow-xl shadow-gray-100/50">
                        <h3 class="text-xl font-bold text-gray-800 mb-1">Informasi Profil</h3>
                        <p class="text-sm text-gray-800 mb-6">Perbarui nama lengkap dan alamat email akun Anda.</p>

                        <div class="max-w-xl">
                            @include('profile.partials.update-profile-information-form')
                        </div>
                    </div>

                    {{-- CARD: Ganti Password --}}
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

<script>
    document.addEventListener('DOMContentLoaded', function () {

    const input = document.getElementById('nama_dosen');
    const list = document.getElementById('suggestions');

    input.addEventListener('input', function () {

        if (this.value.length < 1) {
            list.innerHTML = '';
            return;
        }

        fetch(`/search-dosen?q=${this.value}`)
            .then(res => res.json())
            .then(data => {

                list.innerHTML = '';

                data.forEach(dosen => {

                    let li = document.createElement('li');
                    li.textContent = dosen.name;
                    li.classList.add('cursor-pointer', 'p-2', 'hover:bg-gray-100');

                    li.onclick = function () {

                        input.value = dosen.name;
                        document.getElementById('email').value = dosen.email;
                        document.getElementById('nidn').value = dosen.nidn;
                        const passwordField = document.getElementById('password');
                        passwordField.readOnly = true;
                        passwordField.disabled = true;
                        document.getElementById('dosen_id').value = dosen.id;

                        list.innerHTML = '';
                    };

                    list.appendChild(li);
                });
            });
    });

});
</script>
