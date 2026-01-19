<x-app-layout>
    <div class="py-12 bg-white min-h-screen">
        <div class="max-w-xl mx-auto px-6 lg:px-8">

            {{-- HEADER --}}
            <div class="mb-8">
                <h1 class="text-3xl font-black text-gray-900">Update Password Dosen</h1>
                <p class="text-gray-500 mt-2">
                    Ubah password akun dosen.
                </p>
            </div>

            <div class="p-8 bg-white border border-gray-200 rounded-2xl shadow">
                <form action="{{ route('admin.dosen.update', $dosen->id) }}"
                      method="POST"
                      class="space-y-6">
                    @csrf
                    @method('PUT')

                    {{-- Password Baru --}}
                    <div>
                        <x-input-label for="password" value="Password Baru" />
                        <x-text-input
                            id="password"
                            type="password"
                            name="password"
                            class="block mt-1 w-full"
                            placeholder="Kosongkan jika tidak ingin mengubah"
                        />
                        <x-input-error :messages="$errors->get('password')" />
                    </div>

                    {{-- Konfirmasi Password --}}
                    <div>
                        <x-input-label for="password_confirmation" value="Konfirmasi Password" />
                        <x-text-input
                            id="password_confirmation"
                            type="password"
                            name="password_confirmation"
                            class="block mt-1 w-full"
                        />
                    </div>

                    {{-- Tombol --}}
                    <div class="flex gap-4 pt-4">
                        <x-primary-button>
                            Update Password
                        </x-primary-button>

                        <a href="{{ route('admin.dosen.index') }}"
                           class="text-gray-500 font-bold text-sm hover:text-gray-800">
                            Batal
                        </a>
                    </div>

                </form>
            </div>
        </div>
    </div>
</x-app-layout>
