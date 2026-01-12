<footer class="mt-auto text-sm">
    {{-- BAGIAN ATAS (GRADIENT) --}}
    <div style="background: linear-gradient(231deg, rgb(6, 147, 227) 0%, rgb(155, 81, 224) 94%);" class="text-white py-8 px-4 lg:px-8">
        <div class="max-w-7xl mx-auto">
            {{-- Grid 4 Kolom --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">

                {{-- KOLOM 1: LOGO & ALAMAT --}}
                <div class="space-y-4">
                    <div class="flex items-center gap-3">
                        <img src="{{ asset('img/logobaperlitbang.png') }}" alt="Logo" style="height: 50px; width: auto;" class="bg-white/10 p-1 rounded backdrop-blur-sm">
                        <div class="leading-tight">
                            <h3 class="font-bold text-sm uppercase">Bapperida</h3>
                            <p class="text-[10px] opacity-90">Kab. Karanganyar</p>
                        </div>
                    </div>

                    <div class="text-[11px] space-y-2 opacity-90">
                        <div class="flex items-start gap-2">
                            <svg class="w-3.5 h-3.5 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            <span>Komplek perkantoran Cangakan.</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                            <span>(0271) 495179</span>
                        </div>
                        <div class="flex items-center gap-2">
                            {{-- <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg> --}}
                            <a href="https://www.instagram.com/bapperida_karanganyar/" class="bg-white/20 p-1.5 rounded hover:bg-white/40 transition"><svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg></a>
                            <span> bapperida_karanganyar</span>
                        </div>
                        <div class="flex items-center gap-2">
                             <a href="https://www.facebook.com/profile.php?id=61560330686043&ref=_xav_ig_profile_page_web#" class="bg-white/20 p-1.5 rounded hover:bg-white/40 transition"><svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg></a>
                            <span> Bapperida Kabupaten Karanganyar </span>
                        </div>
                    </div>

                    {{-- Sosmed --}}
                    {{-- <div class="flex gap-2 pt-1">
                        <a href="#" class="bg-white/20 p-1.5 rounded hover:bg-white/40 transition"><svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg></a>
                        <a href="https://www.instagram.com/baperlitbang_karanganyar/" class="bg-white/20 p-1.5 rounded hover:bg-white/40 transition"><svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg></a>
                    </div> --}}
                </div>

                {{-- KOLOM 2: GOOGLE MAP (DIBUAT BESAR & TINGGI) --}}
                <div class="h-full min-h-[180px] w-full rounded-xl overflow-hidden shadow-lg border-2 border-white/20">
                    <iframe
                        tittle="Lokasi Baperlitbang Kab. Karanganyar"
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3954.965707764724!2d110.9460593!3d-7.5895007!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e7a177267e76563%3A0xac4d6aa9671c6f4!2sBaperlitbang%20Kabupaten%20Karanganyar!5e0!3m2!1sid!2sid!4v1700000000000!5m2!1sid!2sid"
                        width="100%"
                        height="100%"
                        style="border:0;"
                        allowfullscreen=""
                        loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade">
                    </iframe>
                </div>

                {{-- KOLOM 3: FITUR & LINK TERKAIT (Digabung) --}}
                <div class="space-y-6">
                    {{-- Fitur --}}
                    <div>
                        <h5 class="text-xs font-bold uppercase tracking-wider border-b border-white/20 pb-1 mb-2">Fitur Utama</h5>
                        <ul class="space-y-2 text-[11px]">
                            <li class="flex items-center gap-2 opacity-90"><span class="bg-white/20 p-1 rounded">✓</span> Presensi Digital</li>
                            <li class="flex items-center gap-2 opacity-90"><span class="bg-white/20 p-1 rounded">✓</span> Logbook Harian</li>
                            <li class="flex items-center gap-2 opacity-90"><span class="bg-white/20 p-1 rounded">✓</span> Laporan Magang</li>
                        </ul>
                    </div>

                    {{-- Link Terkait --}}
                    <div>
                        <h5 class="text-xs font-bold uppercase tracking-wider border-b border-white/20 pb-1 mb-2">Link Terkait</h5>
                        <ul class="space-y-1 text-[11px] opacity-90">
                            <li><a href="https://www.karanganyarkab.go.id/" class="hover:text-blue-100 hover:underline">Kab. Karanganyar</a></li>
                            <li><a href="https://ppid.karanganyarkab.go.id/" class="hover:text-blue-100 hover:underline">PPID Karanganyar</a></li>
                            <li><a href="https://opendata.karanganyarkab.go.id/user/baperlitbang" class="hover:text-blue-100 hover:underline">Open Data</a></li>
                        </ul>
                    </div>
                </div>

                {{-- KOLOM 4: JAM PELAYANAN & SAPAMAS --}}
                {{-- JAM PELAYANAN --}}
                <div>
                    <h5 class="text-xs font-bold uppercase tracking-wider border-b border-white/20 pb-1 mb-2">
                        Jam Pelayanan
                    </h5>

                    @php
                        $jamOperasional = $aboutSetting->jam_operasional ?? '';
                        $listJam = $jamOperasional
                            ? array_map('trim', explode(',', $jamOperasional))
                            : [];
                    @endphp

                    <div class="space-y-1 text-[10px] opacity-90">
                        @foreach ($listJam as $jam)
                            <div class="border-b border-white/10 pb-0.5">
                                {{ $jam }}
                            </div>
                        @endforeach
                    </div>

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
    </div>

    {{-- COPYRIGHT --}}
    <div class="bg-gray-900 text-gray-400 py-3 text-center text-[10px]">
        <p>© {{ date('Y') }} Sistem Magang Baperlitbang Kab. Karanganyar.</p>
    </div>
</footer>
