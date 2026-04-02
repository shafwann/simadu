<x-app-layout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="mb-6 flex items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Permintaan Reset Wajah</h1>
                <p class="text-sm text-gray-600">Daftar request dari mahasiswa untuk reset verifikasi wajah.</p>
            </div>
            <a href="{{ route('admin.dashboard') }}"
                class="inline-flex items-center px-4 py-2 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-800 text-sm font-semibold transition">
                Kembali ke Dashboard
            </a>
        </div>

        @if (session('success'))
            <div class="mb-4 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-green-800">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th
                                class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Mahasiswa</th>
                            <th
                                class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Alasan</th>
                            <th
                                class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Status</th>
                            <th
                                class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Tanggal</th>
                            <th
                                class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        @forelse ($faceResetRequests as $request)
                            <tr>
                                <td class="px-4 py-4 align-top">
                                    <div class="text-sm font-semibold text-gray-900">{{ $request->user->name ?? '-' }}
                                    </div>
                                    <div class="text-xs text-gray-500">{{ $request->user->nim ?? '-' }}</div>
                                </td>
                                <td class="px-4 py-4 text-sm text-gray-700 align-top">{{ $request->reason ?: '-' }}</td>
                                <td class="px-4 py-4 align-top">
                                    @if ($request->status === 'pending')
                                        <span
                                            class="inline-flex px-2 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800">Pending</span>
                                    @elseif ($request->status === 'approved')
                                        <span
                                            class="inline-flex px-2 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">Approved</span>
                                    @else
                                        <span
                                            class="inline-flex px-2 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-800">Rejected</span>
                                    @endif
                                </td>
                                <td class="px-4 py-4 text-sm text-gray-600 align-top">
                                    {{ $request->created_at?->format('d M Y H:i') }}</td>
                                <td class="px-4 py-4 align-top">
                                    @if ($request->status === 'pending')
                                        <div class="flex justify-end gap-2">
                                            <form action="{{ route('admin.face-reset.approve', $request->id) }}"
                                                method="POST">
                                                @csrf
                                                <button type="submit"
                                                    class="px-3 py-2 text-xs font-semibold rounded-lg bg-green-600 hover:bg-green-700 text-white">
                                                    Approve
                                                </button>
                                            </form>
                                            <form action="{{ route('admin.face-reset.reject', $request->id) }}"
                                                method="POST">
                                                @csrf
                                                <button type="submit"
                                                    class="px-3 py-2 text-xs font-semibold rounded-lg bg-red-600 hover:bg-red-700 text-white">
                                                    Reject
                                                </button>
                                            </form>
                                        </div>
                                    @else
                                        <div class="text-right text-xs text-gray-400">Diproses</div>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-10 text-center text-sm text-gray-500">Belum ada request reset wajah.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($faceResetRequests->hasPages())
                <div class="px-4 py-3 border-t border-gray-100">
                    {{ $faceResetRequests->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
