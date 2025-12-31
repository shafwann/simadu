<x-app-layout>
    <div class="py-12 max-w-2xl mx-auto px-6">
        <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100">
            <h2 class="text-2xl font-bold mb-6">Tambah Mahasiswa Baru</h2>

            <form action="{{ route('admin.students.store') }}" method="POST" class="space-y-4">
                @csrf

                <div>
                    <label class="block font-bold text-sm text-gray-700">Nama Lengkap</label>
                    <input type="text" name="name" required class="w-full rounded-lg border-gray-300">
                </div>

                <div>
                    <label class="block font-bold text-sm text-gray-700">Email</label>
                    <input type="email" name="email" required class="w-full rounded-lg border-gray-300">
                </div>

                <div>
                    <label class="block font-bold text-sm text-gray-700">Password</label>
                    <input type="password" name="password" required class="w-full rounded-lg border-gray-300">
                </div>

                <div>
                    <label class="block font-bold text-sm text-gray-700">Konfirmasi Password</label>
                    <input type="password" name="password_confirmation" required class="w-full rounded-lg border-gray-300">
                </div>

                <div class="pt-4">
                    <button type="submit" class="w-full bg-blue-600 text-white font-bold py-3 rounded-xl hover:bg-blue-700 transition">
                        Simpan Akun
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
