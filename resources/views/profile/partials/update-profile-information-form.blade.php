<section>
    {{-- <header>
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            {{ __('Profile Information') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ __("Update your account's profile information and email address.") }}
        </p>
    </header> --}}

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input
                id="name"
                name="name"
                type="text"
                class="mt-1 block w-full"
                :value="old('name', $user->name)"
                required
                autofocus
                autocomplete="name"
            />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input
                id="email"
                name="email"
                type="email"
                class="mt-1 block w-full"
                :value="old('email', $user->email)"
                required
                autocomplete="username"
            />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div>
                    <p class="text-sm mt-2 text-gray-800 dark:text-gray-200">
                        {{ __('Your email address is unverified.') }}

                        <button
                            form="send-verification"
                            class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800"
                        >
                            {{ __('Click here to re-send the verification email.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-sm text-green-600 dark:text-green-400">
                            {{ __('A new verification link has been sent to your email address.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        {{-- ================= PEMBIMBING LAPANGAN (MAHASISWA BOLEH EDIT) ================= --}}
        @if($user->role === 'mahasiswa')
            <div class="border-t pt-6 space-y-4">

                <div>
                    <x-input-label for="jabatan_pembimbing" value="Jabatan Pembimbing Lapangan" />
                    <x-text-input
                        id="jabatan_pembimbing"
                        name="jabatan_pembimbing"
                        type="text"
                        class="mt-1 block w-full"
                        :value="old('jabatan_pembimbing', $user->jabatan_pembimbing)"
                    />
                    <x-input-error class="mt-2" :messages="$errors->get('jabatan_pembimbing')" />
                </div>

                <div>
                    <x-input-label for="nama_pembimbing" value="Nama Pembimbing Lapangan" />
                    <x-text-input
                        id="nama_pembimbing"
                        name="nama_pembimbing"
                        type="text"
                        class="mt-1 block w-full"
                        :value="old('nama_pembimbing', $user->nama_pembimbing)"
                    />
                    <x-input-error class="mt-2" :messages="$errors->get('nama_pembimbing')" />
                </div>

                <div>
                    <x-input-label for="nip_pembimbing" value="NIP Pembimbing Lapangan" />
                    <x-text-input
                        id="nip_pembimbing"
                        name="nip_pembimbing"
                        type="text"
                        class="mt-1 block w-full"
                        :value="old('nip_pembimbing', $user->nip_pembimbing)"
                    />
                    <x-input-error class="mt-2" :messages="$errors->get('nip_pembimbing')" />
                </div>

            </div>
        @endif
        {{-- ================= END PEMBIMBING LAPANGAN ================= --}}

        {{-- ================= BIDANG / DIVISI (MAHASISWA BOLEH EDIT) ================= --}}
        @if($user->role === 'mahasiswa')
            <div class="border-t pt-6 space-y-4">

                <div>
                    <x-input-label for="bidang" value="Bidang / Divisi Penempatan" />

                    <input
                        type="text"
                        name="bidang"
                        id="bidang"
                        list="bidangList"
                        value="{{ old('bidang', $user->bidang) }}"
                        class="mt-1 block w-full rounded-xl border-gray-200 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        placeholder="Ketik manual atau pilih dari daftar..."
                        required
                    >

                    <datalist id="bidangList">
                        @foreach($bidangOptions as $bidang)
                            <option value="{{ $bidang }}"></option>
                        @endforeach
                    </datalist>

                    <x-input-error class="mt-2" :messages="$errors->get('bidang')" />
                </div>

            </div>
        @endif
        {{-- ================= END BIDANG / DIVISI ================= --}}

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Save') }}</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600 dark:text-gray-400"
                >{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>
</section>
