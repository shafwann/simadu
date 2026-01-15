<footer class="mt-auto text-sm">
    {{-- BAGIAN ATAS (GRADIENT) --}}
    <div style="background: linear-gradient(231deg, rgb(6, 147, 227) 0%, rgb(155, 81, 224) 94%);" class="text-white py-8 px-4 lg:px-8">
        <div class="max-w-7xl mx-auto">
            {{-- Grid 4 Kolom --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">

                {{-- KOLOM 1: LOGO & ALAMAT --}}
                <div class="space-y-4">
                    <div class="flex items-center gap-3">
                        @if(isset($globalAbout) && $globalAbout->logo_path)
                            <img src="{{ asset('storage/' . $globalAbout->logo_path) }}" alt="Logo" style="height: 50px; width: auto;" class="bg-white/10 p-1 rounded backdrop-blur-sm">
                        @else
                            <img src="{{ asset('img/logobaperlitbang.png') }}" alt="Logo" style="height: 50px; width: auto;" class="bg-white/10 p-1 rounded backdrop-blur-sm">
                        @endif

                        <div class="leading-tight">
                            <h3 class="font-bold text-sm uppercase">{{ $globalAbout->app_name ?? 'Bapperida' }}</h3>
                            <p class="text-[10px] opacity-90">{{ $globalAbout->agency_name ?? 'Kab. Karanganyar' }}</p>
                        </div>
                    </div>

                    <div class="text-[11px] space-y-2 opacity-90">
                        {{-- ALAMAT --}}
                        <div class="flex items-start gap-2">
                            <svg class="w-3.5 h-3.5 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            <span>{{ $globalAbout->office_address ?? 'Komplek perkantoran Cangakan.' }}</span>
                        </div>
                        {{-- TELEPON --}}
                        <div class="flex items-center gap-2">
                            <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                            <span>{{ $globalAbout->office_phone ?? '(0271) 495179' }}</span>
                        </div>

                        {{-- SOSMED ICONS (4 BUAH) --}}
                        <div class="flex gap-2 pt-1">
                            {{-- Instagram --}}
                            @if(!empty($globalAbout->social_instagram))
                                <a href="{{ $globalAbout->social_instagram }}" target="_blank" class="bg-white/20 p-1.5 rounded hover:bg-white/40 transition" title="Instagram">
                                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></path></svg>
                                </a>
                            @endif
                            {{-- Facebook --}}
                            @if(!empty($globalAbout->social_facebook))
                                <a href="{{ $globalAbout->social_facebook }}" target="_blank" class="bg-white/20 p-1.5 rounded hover:bg-white/40 transition" title="Facebook">
                                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                                </a>
                            @endif
                            {{-- YouTube --}}
                            @if(!empty($globalAbout->social_youtube))
                                <a href="{{ $globalAbout->social_youtube }}" target="_blank" class="bg-white/20 p-1.5 rounded hover:bg-white/40 transition" title="YouTube">
                                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>
                                </a>
                            @endif
                            {{-- X (Twitter) --}}
                            @if(!empty($globalAbout->social_x))
                                <a href="{{ $globalAbout->social_x }}" target="_blank" class="bg-white/20 p-1.5 rounded hover:bg-white/40 transition" title="X (Twitter)">
                                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M18.901 1.153h3.68l-8.04 9.19L24 22.846h-7.406l-5.8-7.584-6.638 7.584H.474l8.6-9.83L0 1.154h7.594l5.243 6.932ZM17.61 20.644h2.039L6.486 3.24H4.298Z"/></svg>
                                </a>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- KOLOM 2: GOOGLE MAP --}}
                <div class="h-full min-h-[180px] w-full rounded-xl overflow-hidden shadow-lg border-2 border-white/20 bg-gray-300">
                    @if(!empty($globalAbout->google_maps_embed))
                        <iframe
                            src="{{ $globalAbout->google_maps_embed }}"
                            width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade">
                        </iframe>
                    @else
                        <div class="flex items-center justify-center h-full text-gray-500 text-xs text-center p-4">
                            Peta belum disetting di menu About.
                        </div>
                    @endif
                </div>

                {{-- KOLOM 3: FITUR & LINK (DINAMIS) --}}
                <div class="space-y-6">
                    <div>
                        <h5 class="text-xs font-bold uppercase tracking-wider border-b border-white/20 pb-1 mb-2">Fitur Utama</h5>
                        <ul class="space-y-2 text-[11px]">
                            <li class="flex items-center gap-2 opacity-90"><span class="bg-white/20 p-1 rounded">✓</span> Presensi Digital</li>
                            <li class="flex items-center gap-2 opacity-90"><span class="bg-white/20 p-1 rounded">✓</span> Logbook Harian</li>
                            <li class="flex items-center gap-2 opacity-90"><span class="bg-white/20 p-1 rounded">✓</span> Laporan Magang</li>
                        </ul>
                    </div>

                    {{-- LINK TERKAIT DARI DATABASE --}}
                    <div>
                        <h5 class="text-xs font-bold uppercase tracking-wider border-b border-white/20 pb-1 mb-2">Link Terkait</h5>
                        <ul class="space-y-1 text-[11px] opacity-90">
                            @if(!empty($globalAbout->custom_links))
                                @php $links = json_decode($globalAbout->custom_links, true); @endphp
                                @if(is_array($links))
                                    @foreach($links as $link)
                                        <li>
                                            <a href="{{ $link['url'] }}" class="hover:text-blue-100 hover:underline">
                                                {{ $link['label'] }}
                                            </a>
                                        </li>
                                    @endforeach
                                @endif
                            @else
                                <li><span class="opacity-50 cursor-not-allowed">Belum ada link terkait.</span></li>
                            @endif
                        </ul>
                    </div>
                </div>

                {{-- KOLOM 4: JAM PELAYANAN & SAPAMAS (STATIS) --}}
                <div>
                    <h5 class="text-xs font-bold uppercase tracking-wider border-b border-white/20 pb-1 mb-2">
                        Jam Pelayanan
                    </h5>

                    @php
                        $jamOperasional = $globalAbout->jam_operasional ?? 'Senin - Jumat: 08:00 - 16:00';
                        $listJam = $jamOperasional ? array_map('trim', explode(',', $jamOperasional)) : [];
                    @endphp

                    <div class="space-y-1 text-[10px] opacity-90">
                        @foreach ($listJam as $jam)
                            <div class="border-b border-white/10 pb-0.5">{{ $jam }}</div>
                        @endforeach
                    </div>

                    {{-- WHATSAPP BUTTON (STATIS/HARDCODED) --}}
                    <div>
                        <br>
                        <a href="https://api.whatsapp.com/send?phone=628112629999" target="_blank">
                            <img src="https://baperlitbang.karanganyarkab.go.id/wp-content/uploads/2025/07/sapamas-button.png"
                                 alt="Sapa Mas"
                                 style="width: 160px; height: auto;"
                                 class="hover:scale-105 transition rounded shadow-sm">
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- COPYRIGHT DINAMIS --}}
    <div class="bg-gray-900 text-gray-400 py-3 text-center text-[10px]">
        <p>
            © {{ date('Y') }} Sistem Magang {{ $globalAbout->app_name ?? 'Bapperida' }} {{ $globalAbout->agency_name ?? 'Kab. Karanganyar' }}.
        </p>
    </div>
</footer>
