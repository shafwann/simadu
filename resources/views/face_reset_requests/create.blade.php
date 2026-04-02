<x-app-layout>
    <div class="py-12 max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">

            {{-- Header --}}
            <div class="bg-gradient-to-r from-red-50 to-orange-50 border-b border-red-100 p-8">
                <div class="flex items-center gap-3 mb-3">
                    <div class="p-3 bg-red-100 rounded-xl text-red-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-2xl font-black text-gray-900">Ajukan Reset Wajah</h1>
                        <p class="text-sm text-gray-500 mt-1">Kirim permintaan reset face verification Anda</p>
                    </div>
                </div>
            </div>

            {{-- Content --}}
            <div class="p-8 space-y-6">

                {{-- Alert Session --}}
                @if (session('success'))
                    <div class="p-4 bg-green-50 border border-green-200 text-green-700 rounded-xl">
                        <p class="font-semibold">✓ {{ session('success') }}</p>
                        <p class="text-sm mt-1">Admin akan meninjau permintaan Anda dalam waktu singkat.</p>
                    </div>
                @endif

                @if (session('warning'))
                    <div class="p-4 bg-yellow-50 border border-yellow-200 text-yellow-700 rounded-xl">
                        <p class="font-semibold">⚠️ {{ session('warning') }}</p>
                    </div>
                @endif

                {{-- Pending Request Section --}}
                @if ($existingRequest && $existingRequest->status === 'pending')
                    <div class="bg-yellow-50 border-l-4 border-yellow-500 rounded-xl p-6 space-y-4">
                        <div>
                            <h3 class="font-bold text-yellow-900 mb-1">⏳ Permintaan Sedang Ditinjau</h3>
                            <p class="text-sm text-yellow-800">
                                Permintaan reset Anda sudah dikirim pada
                                <strong>{{ $existingRequest->created_at->format('d M Y H:i') }}</strong>
                                dan sedang menunggu persetujuan dari admin.
                            </p>
                        </div>

                        @if ($existingRequest->reason)
                            <div class="bg-white rounded p-3">
                                <p class="text-xs text-gray-600 font-semibold mb-1">Alasan Anda:</p>
                                <p class="text-sm text-gray-700">{{ $existingRequest->reason }}</p>
                            </div>
                        @endif

                        <div class="pt-4 border-t border-yellow-200">
                            <p class="text-xs text-yellow-700 mb-3">Hubungi admin untuk mempercepat proses:</p>
                            <a href="https://wa.me/085934688957?text=Halo%20Admin%2C%20saya%20ingin%20bertanya%20status%20permintaan%20reset%20wajah%20saya.%20Nama%3A%20{{ Auth::user()->name }}%20(%20{{ Auth::user()->nim }}%20)"
                                target="_blank"
                                class="inline-flex items-center gap-2 px-6 py-3 bg-green-600 hover:bg-green-700 text-white font-bold rounded-xl transition shadow-lg">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                    <path
                                        d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421-7.403h-.004a9.87 9.87 0 00-9.746 9.778c0 2.716.738 5.355 2.124 7.627l-2.257 6.953 7.15-2.278c2.226 1.185 4.612 1.84 7.134 1.84 9.776 0 9.533-7.904 9.533-9.778 0-2.416-.484-4.755-1.416-6.844-.779-1.685-1.946-3.21-3.368-4.55-1.422-1.34-3.04-2.464-4.804-3.220-1.765-.755-3.638-1.208-5.593-1.208z" />
                                </svg>
                                Hubungi Admin via WhatsApp
                            </a>
                        </div>

                        <div class="flex gap-3">
                            <a href="{{ route('attendance.index') }}"
                                class="flex-1 px-6 py-3 bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold rounded-xl transition text-center">
                                ← Kembali ke Absensi
                            </a>
                        </div>
                    </div>

                    {{-- Approved/Rejected Request Section --}}
                @elseif ($existingRequest && ($existingRequest->status === 'approved' || $existingRequest->status === 'rejected'))
                    <div
                        class="@if ($existingRequest->status === 'approved') bg-green-50 border-l-4 border-green-500 @else bg-red-50 border-l-4 border-red-500 @endif rounded-xl p-6 space-y-4 mb-6">
                        <div>
                            <h3
                                class="@if ($existingRequest->status === 'approved') font-bold text-green-900 @else font-bold text-red-900 @endif mb-1">
                                @if ($existingRequest->status === 'approved')
                                    ✓ Permintaan Disetujui
                                @else
                                    ✕ Permintaan Ditolak
                                @endif
                            </h3>
                            <p
                                class="@if ($existingRequest->status === 'approved') text-sm text-green-800 @else text-sm text-red-800 @endif">
                                Permintaan Anda telah diproses pada
                                <strong>{{ $existingRequest->approved_at }}</strong>
                            </p>
                        </div>

                        @if ($existingRequest->reason)
                            <div class="bg-white rounded p-3">
                                <p class="text-xs text-gray-600 font-semibold mb-1">Alasan Anda:</p>
                                <p class="text-sm text-gray-700">{{ $existingRequest->reason }}</p>
                            </div>
                        @endif
                    </div>

                    {{-- Show Form Again for New Request --}}
                    <form action="{{ route('face-reset-request.store') }}" method="POST" class="space-y-6">
                        @csrf
                        <input type="hidden" name="user_id" value="{{ Auth::id() }}">

                        <div class="bg-blue-50 border border-blue-200 rounded-xl p-4">
                            <h3 class="font-bold text-blue-900 mb-2">ℹ️ Ajukan Permintaan Baru</h3>
                            <p class="text-sm text-blue-800">Anda dapat mengajukan permintaan reset wajah lagi.</p>
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Identitas Anda</label>
                            <div class="p-4 bg-gray-50 rounded-xl border border-gray-200">
                                <p class="font-semibold text-gray-900">{{ Auth::user()->name }}</p>
                                <p class="text-sm text-gray-500">{{ Auth::user()->nim }} • {{ Auth::user()->email }}
                                </p>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Alasan Reset</label>
                            <textarea name="reason" rows="4"
                                class="w-full rounded-xl border-gray-300 bg-gray-50 focus:ring-blue-500 focus:border-blue-500 text-gray-900 placeholder-gray-400"
                                placeholder="Jelaskan alasan Anda ingin mereset data wajah (opsional)"></textarea>
                        </div>

                        <div class="flex gap-3">
                            <button type="submit"
                                class="flex-1 px-6 py-3 bg-red-600 hover:bg-red-700 text-white font-bold rounded-xl transition shadow-lg">
                                ✓ Ajukan Permintaan Reset
                            </button>
                            <a href="{{ route('attendance.index') }}"
                                class="flex-1 px-6 py-3 bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold rounded-xl transition text-center">
                                ✕ Batal
                            </a>
                        </div>
                    </form>

                    {{-- Normal Form (No Previous Request) --}}
                @else
                    {{-- Information Box --}}
                    <div class="bg-blue-50 border border-blue-200 rounded-xl p-4">
                        <h3 class="font-bold text-blue-900 mb-2">ℹ️ Kapan menggunakan fitur ini?</h3>
                        <ul class="text-sm text-blue-800 space-y-1 list-disc list-inside">
                            <li>Wajah Anda tidak terkenali saat verifikasi</li>
                            <li>Ada perubahan signifikan pada penampilan wajah</li>
                            <li>Data wajah lama sudah tidak relevan</li>
                        </ul>
                    </div>

                    {{-- Warning Box --}}
                    <div class="bg-red-50 border border-red-200 rounded-xl p-4">
                        <h3 class="font-bold text-red-900 mb-2">⚠️ Perhatian</h3>
                        <ul class="text-sm text-red-800 space-y-1 list-disc list-inside">
                            <li>Setelah reset, Anda perlu melakukan setup wajah ulang</li>
                            <li>Admin akan meninjau permintaan Anda</li>
                            <li>Jangan submit berkali-kali, cukup sekali saja</li>
                        </ul>
                    </div>

                    {{-- Form --}}
                    <form action="{{ route('face-reset-request.store') }}" method="POST" class="space-y-6">
                        @csrf
                        <input type="hidden" name="user_id" value="{{ Auth::id() }}">

                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Identitas Anda</label>
                            <div class="p-4 bg-gray-50 rounded-xl border border-gray-200">
                                <p class="font-semibold text-gray-900">{{ Auth::user()->name }}</p>
                                <p class="text-sm text-gray-500">{{ Auth::user()->nim }} • {{ Auth::user()->email }}
                                </p>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Alasan Reset</label>
                            <textarea name="reason" rows="4"
                                class="w-full rounded-xl border-gray-300 bg-gray-50 focus:ring-blue-500 focus:border-blue-500 text-gray-900 placeholder-gray-400"
                                placeholder="Jelaskan alasan Anda ingin mereset data wajah (opsional)"></textarea>
                        </div>

                        <div class="flex gap-3">
                            <button type="submit"
                                class="flex-1 px-6 py-3 bg-red-600 hover:bg-red-700 text-white font-bold rounded-xl transition shadow-lg">
                                Ajukan Permintaan Reset
                            </button>
                            <a href="{{ route('attendance.index') }}"
                                class="flex-1 px-6 py-3 bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold rounded-xl transition text-center">
                                Batal
                            </a>
                        </div>
                    </form>
                @endif

            </div>

            {{-- Footer Info --}}
            <div class="bg-gray-50 border-t border-gray-200 p-6 text-center">
                <p class="text-xs text-gray-500">
                    Pertanyaan? Hubungi admin atau pembimbing Anda untuk bantuan lebih lanjut.
                </p>
            </div>

        </div>
    </div>
</x-app-layout>
