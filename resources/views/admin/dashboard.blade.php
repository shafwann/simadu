<x-app-layout>
    {{-- LOGIKA JUDUL BIDANG --}}
    @php
        $userBidang = Auth::user()->bidang;
        $isSuperAdmin = $userBidang === 'Administrator Sistem';
        $labelBidang = $isSuperAdmin ? 'Semua Bidang' : $userBidang;
    @endphp

    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Dashboard Admin') }}
            </h2>

            {{-- Badge Nama Divisi --}}
            <span class="px-3 py-1 rounded-full text-xs font-bold shadow-sm border
                {{ $isSuperAdmin ? 'bg-purple-100 text-purple-700 border-purple-200' : 'bg-blue-100 text-blue-700 border-blue-200' }}">
                Divisi: {{ $userBidang }}
            </span>
        </div>
    </x-slot>

    <div class="py-12 bg-white min-h-screen">
        <div class="max-w-7xl mx-auto px-6 lg:px-12 space-y-10">

            {{-- 1. SECTION RINGKASAN AKTIVITAS --}}
            <div>
                <h1 class="text-3xl font-black text-gray-900 tracking-tight">Ringkasan Aktivitas</h1>
                <p class="text-gray-500 mt-2">
                    Pantau perkembangan mahasiswa magang di
                    <span class="font-bold text-gray-800 uppercase">
                        @if($isSuperAdmin)
                            Seluruh Lingkungan {{ $globalAbout->app_name ?? 'BAPPERIDA' }}
                        @else
                            Bidang {{ $userBidang }}
                        @endif
                    </span>.
                </p>
            </div>

            <div class="flex justify-end mb-6">
                <a href="{{ route('admin.export.mahasiswa') }}"
                    class="px-5 py-2 bg-green-600 hover:bg-green-700 text-white font-bold text-sm rounded-xl shadow-md transition">
                    Download CSV Mahasiswa
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                {{-- Card Mahasiswa Aktif --}}
                <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-xl shadow-blue-50/50 flex flex-col justify-between group hover:border-blue-200 transition">
                    <div class="flex justify-between items-start mb-4">
                        <div class="p-3 bg-blue-50 text-blue-600 rounded-2xl group-hover:bg-blue-600 group-hover:text-white transition duration-300">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                        </div>
                        <span class="bg-blue-100 text-blue-700 text-[10px] font-bold px-2 py-1 rounded-full uppercase tracking-wide">Aktif</span>
                    </div>
                    <div>
                        <p class="text-sm text-gray-400 font-medium mb-1">
                            Mahasiswa {{ $isSuperAdmin ? 'Total' : $labelBidang }} Aktif
                        </p>
                        <p class="text-4xl font-black text-gray-900">{{ $activeStudents }}</p>
                    </div>
                </div>

                {{-- Card Alumni Magang --}}
                <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-xl shadow-gray-100/50 flex flex-col justify-between group hover:border-gray-300 transition">
                    <div class="flex justify-between items-start mb-4">
                        <div class="p-3 bg-gray-50 text-gray-500 rounded-2xl group-hover:bg-gray-800 group-hover:text-white transition duration-300">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <span class="bg-gray-100 text-gray-600 text-[10px] font-bold px-2 py-1 rounded-full uppercase tracking-wide">Selesai</span>
                    </div>
                    <div>
                        <p class="text-sm text-gray-400 font-medium mb-1">
                            Alumni bidang {{ $isSuperAdmin ? 'Total' : $labelBidang }}
                        </p>
                        <p class="text-4xl font-black text-gray-900">{{ $finishedStudents }}</p>
                    </div>
                </div>

                {{-- Card Pending Logbook --}}
                <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-xl shadow-amber-50/50 flex flex-col justify-between group hover:border-amber-200 transition">
                    <div class="flex justify-between items-start mb-4">
                        <div class="p-3 bg-amber-50 text-amber-600 rounded-2xl group-hover:bg-amber-500 group-hover:text-white transition duration-300">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <span class="bg-amber-100 text-amber-700 text-[10px] font-bold px-2 py-1 rounded-full uppercase tracking-wide">Pending</span>
                    </div>
                    <div>
                        <p class="text-sm text-gray-400 font-medium mb-1">
                            Logbook {{ $isSuperAdmin ? '' : $labelBidang }} Butuh Validasi
                        </p>
                        <p class="text-4xl font-black text-gray-900">{{ $pendingLogbooks->count() }}</p>
                    </div>
                </div>

                {{-- Card Pending Laporan --}}
                <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-xl shadow-blue-50/50 flex flex-col justify-between group hover:border-blue-200 transition">
                    <div class="flex justify-between items-start mb-4">
                        <div class="p-3 bg-blue-50 text-blue-600 rounded-2xl group-hover:bg-blue-600 group-hover:text-white transition duration-300">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 4H7a2 2 0 01-2-2V6a2 2 0 012-2h5l5 5v9a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <span class="bg-blue-100 text-blue-700 text-[10px] font-bold px-2 py-1 rounded-full uppercase tracking-wide">Pending</span>
                    </div>
                    <div>
                        <p class="text-sm text-gray-400 font-medium mb-1">
                            Laporan {{ $isSuperAdmin ? '' : $labelBidang }} Perlu Validasi
                        </p>
                        <p class="text-4xl font-black text-gray-900">{{ $pendingLaporans->count() }}</p>
                    </div>
                </div>
            </div>


            {{-- 2. TABEL PERSETUJUAN LOGBOOK --}}
            <div id="logbook" class="bg-white border border-gray-100 rounded-[2rem] shadow-2xl shadow-gray-100/50 overflow-hidden">
                <div class="p-8 border-b border-gray-50 flex flex-col md:flex-row justify-between items-center gap-4">
                    <div>
                        <h3 class="text-lg font-black text-gray-800">Persetujuan Logbook</h3>
                        <p class="text-sm text-gray-400">Validasi kegiatan harian mahasiswa {{ $isSuperAdmin ? '' : $labelBidang }}.</p>
                    </div>

                    <div class="flex flex-col md:flex-row gap-2 w-full md:w-auto">
                        {{-- SEARCH --}}
                        <form action="{{ route('admin.dashboard') }}#logbook" method="GET" class="flex w-full md:w-auto gap-2">
                            <input id="logbookSearchInput" type="text" name="search" value="{{ $search ?? '' }}" placeholder="Cari mahasiswa..."
                                autocomplete="off"
                                class="w-full md:w-64 px-4 py-2 rounded-xl border border-gray-200 text-sm focus:border-blue-500 focus:ring-blue-500 bg-gray-50">
                            <button type="submit" class="px-5 py-2 bg-gray-900 text-white rounded-xl hover:bg-black transition text-sm font-bold shadow-lg">
                                Cari
                            </button>
                        </form>

                        {{-- ✅ VALIDASI SEMUA --}}
                        @if($pendingLogbooks->count() > 0)
                            <form action="{{ route('admin.logbook.validateAll') }}" method="POST"
                                onsubmit="return confirm('Validasi semua logbook yang pending?');">
                                @csrf
                                <button type="submit"
                                    class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl transition text-sm font-bold shadow-lg">
                                    Validasi Semua
                                </button>
                            </form>
                        @endif
                    </div>
                </div>

                {{-- ✅ WRAPPER ALPINE: MODAL + TABLE (JANGAN DIPISAH) --}}
                <div
                    x-data="{
                        open:false,
                        log:null,

                        rejectOpen:false,
                        rejectId:null,
                        rejectedNote:'',

                        show(data){ this.log=data; this.open=true; },
                        close(){ this.open=false; this.log=null; }
                    }"
                >

                    {{-- ✅ MODAL DETAIL LOGBOOK (FIX: TANPA OPTIONAL CHAINING + UI BAGUS) --}}
                    <div
                        x-show="open"
                        x-cloak
                        x-transition.opacity
                        class="fixed inset-0 z-50 flex items-center justify-center p-4"
                    >
                        <div class="absolute inset-0 bg-black/50" @click="close()"></div>

                        <div
                            x-transition
                            class="relative w-full max-w-2xl bg-white rounded-3xl shadow-2xl border border-gray-100 overflow-hidden"
                            @click.stop
                        >
                            {{-- Header modal --}}
                            <div class="p-5 border-b border-gray-100 flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <h3 class="text-lg md:text-xl font-black text-gray-900 truncate"
                                        x-text="log ? log.nama : ''"></h3>
                                    <p class="text-sm text-gray-500 mt-1"
                                        x-text="log ? log.tanggal : ''"></p>
                                </div>
                                <button
                                    type="button"
                                    class="w-10 h-10 rounded-2xl flex items-center justify-center
                                           text-gray-400 hover:text-gray-700 hover:bg-gray-100 transition"
                                    @click="close()"
                                >
                                    ✕
                                </button>
                            </div>

                            {{-- Body modal --}}
                            <div class="p-5">
                                <p class="text-sm font-bold text-gray-700">Catatan Kegiatan</p>

                                {{-- tampil semua + scroll kalau panjang --}}
                                <div
                                    class="mt-2 p-4 rounded-2xl bg-gray-50 text-gray-800 whitespace-pre-line leading-relaxed
                                           max-h-[60vh] overflow-y-auto border border-gray-100"
                                    x-text="log ? log.kegiatan : ''"
                                ></div>

                                <div class="mt-6 flex justify-end gap-3">

    {{-- ✅ Validasi --}}
    <form method="POST" :action="`/admin/logbook/${log ? log.id : ''}/validate`">
        @csrf
        <button type="submit"
            class="w-10 h-10 flex items-center justify-center rounded-2xl
                   bg-white border border-gray-200
                   text-green-500 hover:bg-green-50 hover:border-green-300
                   transition shadow-sm"
            title="Validasi">
            <svg xmlns="http://www.w3.org/2000/svg"
                class="w-4 h-4"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
                stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M5 13l4 4L19 7" />
            </svg>
        </button>
    </form>

    {{-- ⛔ Tolak --}}
    <button type="button"
        class="w-10 h-10 flex items-center justify-center rounded-2xl
               bg-white border border-gray-200
               text-gray-700 hover:bg-gray-100 hover:border-gray-300
               transition shadow-sm"
        title="Tolak"
        @click="rejectOpen=true; rejectId=(log ? log.id : null); rejectedNote='';">
        <svg xmlns="http://www.w3.org/2000/svg"
            class="w-4 h-4"
            fill="none"
            viewBox="0 0 24 24"
            stroke="currentColor"
            stroke-width="2.5">
            <path stroke-linecap="round" stroke-linejoin="round"
                d="M6 18L18 6M6 6l12 12" />
        </svg>
    </button>

    {{-- 🗑 Hapus --}}
    <form method="POST"
        :action="`/admin/logbook/${log ? log.id : ''}`"
        onsubmit="return confirm('Yakin hapus logbook ini?');">
        @csrf
        @method('DELETE')
        <button type="submit"
            class="w-10 h-10 flex items-center justify-center rounded-2xl
                   bg-white border border-red-300
                   text-red-500 hover:bg-red-50 hover:border-red-400
                   transition shadow-sm"
            title="Hapus">
            <svg xmlns="http://www.w3.org/2000/svg"
                class="w-4 h-4"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
                stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862
                       a2 2 0 01-1.995-1.858L5 7m5-4h4m-4 0a1 1 0 00-1 1v1h6V4
                       a1 1 0 00-1-1m-4 0h4" />
            </svg>
        </button>
    </form>

</div>

                            </div>
                        </div>
                    </div>

                    {{-- MODAL PENOLAKAN (tetap) --}}
                    <div x-show="rejectOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
                        <div class="absolute inset-0 bg-black/40" @click="rejectOpen=false"></div>

                        <div class="relative w-full max-w-lg bg-white rounded-2xl shadow-xl p-6">
                            <div class="flex items-start justify-between">
                                <div>
                                    <h3 class="text-lg font-black text-gray-900">Tolak Logbook</h3>
                                    <p class="text-sm text-gray-500">Masukkan alasan penolakan (wajib diisi).</p>
                                </div>
                                <button class="text-gray-500 hover:text-gray-800" @click="rejectOpen=false">✕</button>
                            </div>

                            <form method="POST" :action="`/admin/logbook/${rejectId}/reject`" class="mt-4 space-y-4"
                                @submit="
                                    if(rejectedNote.trim()===''){
                                        alert('Alasan wajib diisi');
                                        $event.preventDefault();
                                    }
                                ">
                                @csrf

                                <textarea name="rejected_note"
                                    x-model="rejectedNote"
                                    rows="4"
                                    class="w-full rounded-xl border-gray-200 shadow-sm focus:border-yellow-500 focus:ring-yellow-500 transition"
                                    placeholder="Contoh: Kegiatan kurang jelas, mohon tulis detail apa yang dikerjakan."
                                    required></textarea>

                                <div class="flex justify-end gap-2">
                                    <button type="button"
                                        class="px-5 py-2 rounded-xl bg-gray-100 text-gray-700 font-bold hover:bg-gray-200 transition"
                                        @click="rejectOpen=false">
                                        Batal
                                    </button>

                                    <button type="submit"
                                        class="px-5 py-2 rounded-xl bg-yellow-500 text-white font-bold hover:bg-yellow-600 transition">
                                        Tolak
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    {{-- TABEL LOGBOOK --}}
                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead>
                                <tr class="bg-gray-50/50 border-b border-gray-100 text-xs uppercase tracking-widest text-gray-400 font-bold">
                                    <th class="px-8 py-4">Mahasiswa</th>
                                    <th class="px-8 py-4">Tanggal</th>
                                    <th class="px-8 py-4">Kegiatan</th>
                                    <th class="px-8 py-4 text-center">Aksi</th>
                                </tr>
                            </thead>

                            <tbody class="divide-y divide-gray-50">
                                @forelse($pendingLogbooks as $log)
                                    @php
                                        $detailPayload = [
                                            "id" => $log->id,
                                            "nama" => $log->user->name,
                                            "tanggal" => \Carbon\Carbon::parse($log->tanggal)->format("d M Y"),
                                            "kegiatan" => $log->kegiatan,
                                        ];
                                    @endphp

                                    <tr class="hover:bg-blue-50/30 transition">
                                        {{-- Mahasiswa (klik untuk popup) --}}
                                        <td class="px-8 py-4">
                                            <button type="button"
                                                class="w-full text-left font-bold text-gray-800 hover:underline hover:text-blue-700"
                                                @click="show(@js($detailPayload))">
                                                {{ $log->user->name }}
                                            </button>
                                        </td>

                                        {{-- Tanggal (klik untuk popup) --}}
                                        <td class="px-8 py-4 text-sm text-gray-500">
                                            <button type="button"
                                                class="w-full text-left hover:text-blue-700"
                                                @click="show(@js($detailPayload))">
                                                {{ \Carbon\Carbon::parse($log->tanggal)->format('d M Y') }}
                                            </button>
                                        </td>

                                        {{-- Kegiatan (klik untuk popup) --}}
                                        <td class="px-8 py-4 text-sm text-gray-600">
                                            <button type="button"
                                                class="w-full text-left truncate max-w-xs hover:text-blue-700"
                                                @click="show(@js($detailPayload))"
                                                title="{{ $log->kegiatan }}">
                                                {{ $log->kegiatan }}
                                            </button>
                                        </td>

                                        {{-- Aksi (tidak membuka popup) --}}
                                        <td class="px-8 py-4 align-middle" @click.stop>
                                            <div class="flex items-center justify-center gap-2">

                                                {{-- ✅ Validasi --}}
                                                <form action="{{ route('admin.logbook.validate', $log->id) }}" method="POST">
                                                    @csrf
                                                    <button type="submit"
                                                        class="w-10 h-10 flex items-center justify-center rounded-xl border border-green-300
                                                        text-green-500 bg-white hover:bg-green-50 hover:shadow-md transition"
                                                        title="Validasi">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none"
                                                            viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                        </svg>
                                                    </button>
                                                </form>

                                                {{-- ⛔ Tolak --}}
                                                <button type="button"
                                                    class="w-10 h-10 flex items-center justify-center rounded-xl border border-yellow-300
                                                    text-yellow-500 bg-white hover:bg-yellow-50 hover:shadow-md transition"
                                                    title="Tolak"
                                                    @click="rejectOpen=true; rejectId={{ $log->id }}; rejectedNote='';">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none"
                                                        viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                    </svg>
                                                </button>

                                                {{-- 🗑 Hapus --}}
                                                <form action="{{ route('admin.logbook.destroy', $log->id) }}"
                                                    method="POST"
                                                    onsubmit="return confirm('Yakin hapus logbook ini?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                        class="w-10 h-10 flex items-center justify-center rounded-xl border border-red-300
                                                        text-red-500 bg-white hover:bg-red-50 hover:shadow-md transition"
                                                        title="Hapus">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none"
                                                            viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862
                                                                a2 2 0 01-1.995-1.858L5 7m5-4h4m-4 0a1 1 0 00-1 1v1h6V4
                                                                a1 1 0 00-1-1m-4 0h4" />
                                                        </svg>
                                                    </button>
                                                </form>

                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-8 py-12 text-center text-gray-400 italic">
                                            Semua logbook {{ $isSuperAdmin ? '' : $labelBidang }} aman terkendali.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>

                        </table>
                    </div>
                </div>
            </div>

            {{-- 3. TABEL VALIDASI LAPORAN --}}
            <div class="bg-white border border-gray-100 rounded-[2rem] shadow-2xl shadow-blue-50/50 overflow-hidden mt-10">
                <div class="p-8 border-b border-gray-50">
                    <h3 class="text-lg font-black text-gray-800">Validasi Laporan Magang</h3>
                    <p class="text-sm text-gray-400">Daftar laporan akhir yang perlu diperiksa dan disetujui untuk bidang {{ $labelBidang }}.</p>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="bg-blue-50/50 border-b border-blue-100 text-xs uppercase tracking-widest text-blue-600 font-bold">
                                <th class="px-6 py-4">Mahasiswa</th>
                                <th class="px-6 py-4">Status Magang</th>
                                <th class="px-6 py-4">File Laporan</th>
                                <th class="px-6 py-4">Tanggal Upload</th>
                                <th class="px-6 py-4 text-center">Status</th>
                                <th class="px-6 py-4 text-center">Aksi</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-50">
                            @forelse($pendingLaporans as $laporan)
                                <tr class="hover:bg-blue-50/30 transition">
                                    <td class="px-6 py-4 font-medium text-gray-800">{{ $laporan->user->name }}</td>

                                    <td class="px-6 py-4">
                                        <span class="px-2 py-1 rounded-full text-xs font-bold
                                            {{ $laporan->user->status === 'aktif' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">
                                            {{ ucfirst($laporan->user->status) }}
                                        </span>
                                    </td>

                                    <td class="px-6 py-4">
                                        <a href="{{ route('admin.laporan.download', $laporan->id) }}"
                                            class="text-blue-600 font-semibold text-sm hover:underline truncate max-w-xs inline-block">
                                            {{ $laporan->nama_file }}
                                        </a>
                                    </td>

                                    <td class="px-6 py-4 text-sm text-gray-500">{{ $laporan->created_at->format('d M Y') }}</td>

                                    <td class="px-6 py-4 text-center">
                                        <span class="px-3 py-1 bg-amber-100 text-amber-700 text-xs font-bold rounded-full">
                                            Menunggu Validasi
                                        </span>
                                    </td>

                                    <td class="px-6 py-4 text-center flex justify-center gap-2">
                                        <form action="{{ route('admin.laporan.validate', $laporan->id) }}" method="POST">
                                            @csrf
                                            <button type="submit"
                                                class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-lg transition shadow-md shadow-blue-200">
                                                Validasi
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="px-6 py-12 text-center italic text-gray-400">
                                        Tidak ada laporan {{ $isSuperAdmin ? '' : $labelBidang }} yang perlu divalidasi.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>

    {{-- AUTO SEARCH + AUTO SCROLL --}}
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const logbookSearchInput = document.getElementById("logbookSearchInput");
            if (!logbookSearchInput) return;

            const logbookForm = logbookSearchInput.closest("form");
            let typingTimer;

            const params = new URLSearchParams(window.location.search);

            if (params.has("search") && params.get("search").trim() !== "") {
                const logbookSection = document.getElementById("logbook");
                if (logbookSection) {
                    logbookSection.scrollIntoView({ behavior: "smooth", block: "start" });
                }

                logbookSearchInput.focus();
                const val = logbookSearchInput.value || "";
                logbookSearchInput.setSelectionRange(val.length, val.length);
            }

            logbookSearchInput.addEventListener("input", function() {
                clearTimeout(typingTimer);
                typingTimer = setTimeout(() => {
                    logbookForm.submit();
                }, 400);
            });
        });
    </script>

</x-app-layout>
