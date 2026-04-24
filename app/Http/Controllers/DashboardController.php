<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class DashboardController extends Controller
{
public function index(Request $request)
{
    $user = Auth::user();

    // ====================================================
    // LOGIKA 1: JIKA MASTER ADMIN / SUPER ADMIN
    // ====================================================
    if ($user->role === 'super_admin' || $user->role === 'master') {

        // 1. Ambil Daftar Instansi untuk Dropdown Filter
        // Kita ambil dari user admin yang memiliki kategori_instansi
        $listInstansi = User::whereNotNull('kategori_instansi')
                            ->where('kategori_instansi', '!=', 'MASTER')
                            ->distinct()
                            ->pluck('kategori_instansi');

        // 2. Siapkan Query Dasar Mahasiswa
        $mahasiswaQuery = User::where('role', 'mahasiswa');
        $adminBidangQuery = User::where('role', 'admin_bidang');

        // 3. Cek Apakah Ada Filter
        $selectedInstansi = $request->query('instansi');

        if ($selectedInstansi) {
            // Jika ada filter, persempit pencarian
            $mahasiswaQuery->where('kategori_instansi', $selectedInstansi);
            $adminBidangQuery->where('kategori_instansi', $selectedInstansi);
        }

        // 4. Hitung Statistik (Sesuai Filter)
        $stats = [
            'total_mahasiswa'    => $mahasiswaQuery->count(), // Ikut terfilter
            'total_admin_bidang' => $adminBidangQuery->count(), // Ikut terfilter

            // Super Admin & Dosen biasanya global, jadi tidak perlu difilter instansi
            'total_super_admin'  => User::where('role', 'super_admin')->count(),
            'total_dosen'        => User::where('role', 'dosen')->count(),
        ];

        // 5. Ambil Data Tabel (Sesuai Filter)
        $mahasiswa = $mahasiswaQuery->latest()->paginate(10)->withQueryString(); // withQueryString agar pagination tidak mereset filter

        // Tampilkan View Dashboard Master
        return view('dashboard.master', compact('stats', 'mahasiswa', 'listInstansi', 'selectedInstansi'));
    }

    // ... (Logika role lain tetap sama seperti sebelumnya) ...

    // ... Copy paste logika role lain dibawah sini ...
    if ($user->role === 'admin_bidang' || $user->role === 'admin') {
        return redirect()->route('admin.dashboard');
    }
    if ($user->role === 'dosen' || $user->role === 'pembimbing') {
        return redirect()->route('pembimbing.dashboard');
    }
    if ($user->role === 'mahasiswa') {
        $rejectedLogbooks = $user->logbooks()
            ->where('is_rejected', true)
            ->latest('rejected_at')
            ->latest('updated_at')
            ->get();

        $rejectedLaporans = $user->laporans()
            ->where('is_rejected', true)
            ->latest('rejected_at')
            ->latest('updated_at')
            ->get();

        $latestRejectedLogbookAt = optional($rejectedLogbooks->first())->rejected_at;
        $latestRejectedLaporanAt = optional($rejectedLaporans->first())->rejected_at;

        $latestRejectionAt = collect([$latestRejectedLogbookAt, $latestRejectedLaporanAt])
            ->filter()
            ->sortDesc()
            ->first();

        $shouldShowRejectionModal = $latestRejectionAt
            && (
                !$user->last_seen_rejection_modal_at
                || $latestRejectionAt->gt($user->last_seen_rejection_modal_at)
            );

        if ($shouldShowRejectionModal) {
            $user->forceFill([
                'last_seen_rejection_modal_at' => $latestRejectionAt,
            ])->save();
        }

        $rejectionAlerts = collect()
            ->merge($rejectedLogbooks->map(function ($logbook) {
                return [
                    'type' => 'logbook',
                    'title' => 'Logbook ditolak',
                    'headline' => $logbook->tanggal
                        ? 'Logbook tanggal ' . \Carbon\Carbon::parse($logbook->tanggal)->isoFormat('D MMMM Y')
                        : 'Logbook perlu diperbaiki',
                    'note' => $logbook->rejected_note,
                    'rejected_at' => $logbook->rejected_at,
                    'route' => route('logbook.index'),
                    'action' => 'Buka Logbook',
                ];
            }))
            ->merge($rejectedLaporans->map(function ($laporan) {
                return [
                    'type' => 'laporan',
                    'title' => 'Laporan ditolak',
                    'headline' => $laporan->nama_file ?: 'Laporan perlu diperbaiki',
                    'note' => $laporan->rejected_note,
                    'rejected_at' => $laporan->rejected_at,
                    'route' => route('laporan.index'),
                    'action' => 'Buka Laporan',
                ];
            }))
            ->sortByDesc('rejected_at')
            ->values();

        $recentRejectionAlerts = $rejectionAlerts->take(4);
        $modalRejectionAlerts = $rejectionAlerts->take(5);

        return view('dashboard', [
            'rejectedLogbooksCount' => $rejectedLogbooks->count(),
            'rejectedLaporansCount' => $rejectedLaporans->count(),
            'recentRejectionAlerts' => $recentRejectionAlerts,
            'modalRejectionAlerts' => $modalRejectionAlerts,
            'shouldShowRejectionModal' => $shouldShowRejectionModal,
        ]);
    }
    abort(403, 'Anda tidak memiliki akses ke halaman ini.');
}
}
