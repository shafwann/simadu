<nav x-data="{ open: false }" class="bg-white border-b border-gray-100 sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-20">
            <div class="flex">
                <div class="shrink-0 flex items-center">
                    <a href="{{ Auth::user()->role === 'admin' ? route('admin.dashboard') : route('dashboard') }}" class="flex items-center gap-3 group">
                        <img src="{{ asset('img/logobaperlitbang.png') }}" alt="Logo" class="h-12 w-auto transition-transform group-hover:scale-105">
                        <div class="hidden md:block leading-tight">
                            <span class="block text-sm font-black text-gray-800 uppercase tracking-wider">Baperlitbang</span>
                            <span class="block text-[10px] font-bold text-blue-600 uppercase tracking-tight">Kab. Karanganyar</span>
                        </div>
                    </a>
                </div>

                <div class="hidden space-x-6 sm:-my-px sm:ms-10 sm:flex">
                    {{-- MENU MAHASISWA --}}
                    @if(Auth::user()->role === 'mahasiswa')
                        <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" class="font-bold text-gray-600">
                            {{ __('Dashboard') }}
                        </x-nav-link>

                        <x-nav-link :href="route('attendance.index')" :active="request()->routeIs('attendance.index')" class="font-bold">
                            {{ __('Presensi') }}
                        </x-nav-link>

                        <x-nav-link :href="route('logbook.index')" :active="request()->routeIs('logbook.index')" class="font-bold">
                            {{ __('Logbook') }}
                        </x-nav-link>

                        <x-nav-link :href="route('laporan.index')" :active="request()->routeIs('laporan.*')" class="font-bold text-green-600">
                            {{ __('Laporan') }}
                        </x-nav-link>
                    @endif

                    {{-- MENU ADMIN --}}
                    @if(Auth::user()->role === 'admin')
                        <x-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')" class="font-bold text-blue-600">
                            <span class="flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                {{ __('Panel Admin') }}
                            </span>
                        </x-nav-link>

                        <x-nav-link :href="route('admin.students.index')" :active="request()->routeIs('admin.students.*')" class="font-bold text-gray-600 hover:text-blue-600">
                            <span class="flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                                {{ __('Data Mahasiswa') }}
                            </span>
                        </x-nav-link>

                            {{-- MENU MANAJEMEN DOSEN --}}
                        <x-nav-link :href="route('admin.dosen.index')" :active="request()->routeIs('admin.dosen.*')" class="font-bold text-indigo-600 hover:text-indigo-800">
                            <span class="flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.84 5.422L12 14z"></path>
                                </svg>
                                {{ __('Manajemen Dosen') }}
                            </span>
                        </x-nav-link>

                        {{-- MENU MANAJEMEN ADMIN (Hanya untuk Super Admin) --}}
                        @if(Auth::user()->bidang === 'Administrator Sistem')
                            <x-nav-link :href="route('admin.manage.index')" :active="request()->routeIs('admin.manage.*')" class="font-bold text-purple-600 hover:text-purple-800">
                                <span class="flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                                    {{ __('Data Admin') }}
                                </span>
                            </x-nav-link>

                            {{-- MENU ABOUT (Hanya untuk Super Admin) --}}
                            <x-nav-link :href="route('admin.about.index')" :active="request()->routeIs('admin.about.*')" class="font-bold text-gray-600 hover:text-blue-600">
                                <span class="flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M13 16h-1v-4h-1m1-4h.01M12 2a10 10 0 100 20 10 10 0 000-20z"/>
                                    </svg>
                                    {{ __('About') }}
                                </span>
                            </x-nav-link>
                        @endif
                    @endif
                </div>
            </div>

            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-4 py-2 border border-gray-100 text-sm leading-4 font-bold rounded-xl text-gray-700 bg-gray-50 hover:bg-white hover:shadow-sm focus:outline-none transition ease-in-out duration-150">
                            <div class="flex items-center gap-2">
                                <div class="w-7 h-7 bg-blue-600 rounded-full flex items-center justify-center text-[10px] text-white uppercase">
                                    {{ substr(Auth::user()->name, 0, 2) }}
                                </div>
                                {{ Auth::user()->name }}
                            </div>
                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')" class="font-medium text-gray-600">
                            {{ __('Profile') }}
                        </x-dropdown-link>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')" class="font-bold text-red-600"
                                    onclick="event.preventDefault(); this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden bg-white border-t border-gray-100 shadow-lg">
        <div class="pt-2 pb-3 space-y-1">
            {{-- MENU MOBILE MAHASISWA --}}
            @if(Auth::user()->role === 'mahasiswa')
                <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                    {{ __('Dashboard') }}
                </x-responsive-nav-link>

                <x-responsive-nav-link :href="route('attendance.index')" :active="request()->routeIs('attendance.index')">
                    {{ __('Presensi') }}
                </x-responsive-nav-link>

                <x-responsive-nav-link :href="route('logbook.index')" :active="request()->routeIs('logbook.index')">
                    {{ __('Logbook') }}
                </x-responsive-nav-link>

                <x-responsive-nav-link :href="route('laporan.index')" :active="request()->routeIs('laporan.*')" class="text-green-600 font-bold">
                    {{ __('Laporan') }}
                </x-responsive-nav-link>
            @endif

            {{-- MENU MOBILE ADMIN --}}
            @if(Auth::user()->role === 'admin')
                <x-responsive-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')" class="text-blue-600 font-bold">
                    {{ __('Panel Admin') }}
                </x-responsive-nav-link>

                <x-responsive-nav-link :href="route('admin.students.index')" :active="request()->routeIs('admin.students.*')">
                    {{ __('Data Mahasiswa') }}
                </x-responsive-nav-link>

                {{-- MENU MOBILE MANAJEMEN ADMIN --}}
                @if(Auth::user()->bidang === 'Administrator Sistem')
                    <x-responsive-nav-link :href="route('admin.manage.index')" :active="request()->routeIs('admin.manage.*')" class="text-purple-600 font-bold">
                        <span class="flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                            {{ __('Data Admin') }}
                        </span>
                    </x-responsive-nav-link>

                    {{-- MENU ABOUT (Hanya untuk Super Admin) --}}
                    <x-responsive-nav-link :href="route('admin.about.index')" :active="request()->routeIs('admin.about.*')" class="font-bold">
                        <span class="flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M13 16h-1v-4h-1m1-4h.01M12 2a10 10 0 100 20 10 10 0 000-20z"/>
                            </svg>
                            {{ __('About') }}
                        </span>
                    </x-responsive-nav-link>
                @endif
            @endif
        </div>

        <div class="pt-4 pb-1 border-t border-gray-100 bg-gray-50/50">
            <div class="px-4 flex items-center gap-3">
                <div class="w-10 h-10 bg-blue-600 rounded-full flex items-center justify-center text-white font-bold">
                    {{ substr(Auth::user()->name, 0, 1) }}
                </div>
                <div>
                    <div class="font-bold text-base text-gray-800">{{ Auth::user()->name }}</div>
                    <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
                </div>
            </div>

            <div class="mt-3 space-y-1 px-2 pb-2">
                <x-responsive-nav-link :href="route('profile.edit')" class="rounded-lg font-medium">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')" class="rounded-lg font-bold text-red-600"
                            onclick="event.preventDefault(); this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
