<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminAttendanceRecapController extends Controller
{
    public function index(Request $request)
    {
        $admin = Auth::user();

        // 1. Role Logic
        $isSuperAdmin = ($admin->role === 'admin' && $admin->bidang === 'Administrator Sistem');
        $adminBidang  = $admin->bidang;

        // 2. Filter Input
        $mode   = $request->get('mode', 'harian');   // harian | mingguan | bulanan
        $status = $request->get('status', 'all');    // all | hadir | izin | sakit | alpha | telat
        $bidang = $request->get('bidang', 'all');    // hanya dipakai super admin

        // 3. Tentukan Range Tanggal
        [$start, $end, $labelPeriode] = $this->resolveDateRange($mode, $request);

        // 4. Query Mahasiswa
        $studentsQ = User::query()
            ->where('role', 'mahasiswa')
            // Filter Instansi (Wajib ada jika admin punya instansi)
            ->when($admin->kategori_instansi, fn($q) => $q->where('kategori_instansi', $admin->kategori_instansi))
            // Filter Bidang (Admin Biasa vs Super Admin)
            ->when(!$isSuperAdmin, fn($q) => $q->where('bidang', $adminBidang))
            ->when($isSuperAdmin && $bidang !== 'all', fn($q) => $q->where('bidang', $bidang));

        $students = $studentsQ->select('id', 'name', 'nim', 'bidang', 'instansi')->orderBy('name')->get();

        // Ambil daftar bidang untuk dropdown filter (hanya utk super admin)
        $bidangList = (clone $studentsQ)
            ->whereNotNull('bidang')
            ->select('bidang')
            ->distinct()
            ->orderBy('bidang')
            ->pluck('bidang');

        $studentIds = $students->pluck('id')->all();

        // 5. Query Absensi (Dengan Perbaikan Filter Status)
        $attQ = Attendance::query()
            ->whereIn('user_id', $studentIds)
            ->whereBetween('tanggal', [$start->toDateString(), $end->toDateString()])
            ->when($status !== 'all', function ($q) use ($status) {

                // FIX: Mapping dari Filter Frontend ke Nilai Database (Enum)
                // Frontend kirim: 'alpha', 'izin', 'hadir' (huruf kecil)
                // Database simpan: 'Tanpa Keterangan', 'Ijin', 'Hadir' (Sesuai Enum)

                $dbStatus = $status; // default

                if ($status === 'alpha') {
                    $dbStatus = 'Tanpa Keterangan';
                }
                elseif ($status === 'izin') {
                    $dbStatus = 'Ijin'; // Database pakai 'J'
                }
                elseif ($status === 'hadir') {
                    $dbStatus = 'Hadir';
                }
                elseif ($status === 'telat') {
                    $dbStatus = 'Telat';
                }
                elseif ($status === 'sakit') {
                    $dbStatus = 'Sakit';
                }

                return $q->where('status', $dbStatus);
            });

        $attendances = $attQ->get(['user_id', 'tanggal', 'status']);

        // 6. Logic Rekapitulasi (Hitung Jumlah)
        $rekap = $students->map(function ($s) use ($attendances) {

            // Ambil data absensi milik mahasiswa ini saja
            $rows = $attendances->where('user_id', $s->id);

            // FIX: Normalisasi Data untuk Perhitungan
            // Kita samakan semua istilah agar 'Ijin' menjadi 'izin', 'Tanpa Keterangan' menjadi 'alpha'
            $rows = $rows->map(function ($r) {
                $st = strtolower(trim($r->status)); // ubah ke huruf kecil

                // Mapping Standardisasi
                if ($st === 'ijin') $st = 'izin';
                if ($st === 'tanpa keterangan') $st = 'alpha';
                if ($st === 'alpa') $st = 'alpha';

                $r->normalized_status = $st; // simpan di properti sementara
                return $r;
            });

            // Hitung berdasarkan status yang sudah dinormalisasi
            $hadir = $rows->where('normalized_status', 'hadir')->count();
            $telat = $rows->where('normalized_status', 'telat')->count();
            $izin  = $rows->where('normalized_status', 'izin')->count(); // Pakai Z
            $sakit = $rows->where('normalized_status', 'sakit')->count();
            $alpha = $rows->where('normalized_status', 'alpha')->count(); // Termasuk 'Tanpa Keterangan'

            // Total data masuk (bukan total hari kerja, tapi total record yg ada)
            $total   = $rows->count();
            $present = $hadir + $telat;
            $persen  = $total > 0 ? round(($present / $total) * 100, 1) : 0;

            return [
                'id'       => $s->id,
                'name'     => $s->name,
                'nim'      => $s->nim,
                'bidang'   => $s->bidang,
                'instansi' => $s->instansi,
                'hadir'    => $hadir,
                'telat'    => $telat,
                'ijin'     => $izin,  // Key tetap 'ijin' agar view tidak error (atau sesuaikan view jadi 'izin')
                'sakit'    => $sakit,
                'alpha'    => $alpha,
                'total'    => $total,
                'persen'   => $persen,
            ];
        });

        // 7. Summary Card
        $summary = [
            'mahasiswa' => $rekap->count(),
            'hadir'     => $rekap->sum('hadir'),
            'telat'     => $rekap->sum('telat'),
            'ijin'      => $rekap->sum('ijin'),
            'sakit'     => $rekap->sum('sakit'),
            'alpha'     => $rekap->sum('alpha'),
            'total'     => $rekap->sum('total'),
        ];

        return view('admin.attendance.recap', compact(
            'rekap', 'summary', 'isSuperAdmin', 'mode', 'status', 'bidang', 'labelPeriode', 'bidangList'
        ));
    }

    private function resolveDateRange(string $mode, Request $request): array
    {
        $today = Carbon::today();

        if ($mode === 'bulanan') {
            // input: month (YYYY-MM)
            $m = $request->filled('month') ? Carbon::createFromFormat('Y-m', $request->month) : $today->copy();
            $start = $m->copy()->startOfMonth();
            $end   = $m->copy()->endOfMonth();
            return [$start, $end, $m->translatedFormat('F Y')];
        }

        if ($mode === 'mingguan') {
            // input: week_start (YYYY-MM-DD)
            $d = $request->filled('week_start') ? Carbon::parse($request->week_start) : $today->copy()->startOfWeek();
            $start = $d->copy()->startOfWeek();
            $end   = $d->copy()->endOfWeek();
            return [$start, $end, $start->format('d M') . ' - ' . $end->format('d M Y')];
        }

        // default harian
        $d = $request->filled('date') ? Carbon::parse($request->date) : $today;
        $start = $d->copy()->startOfDay();
        $end   = $d->copy()->endOfDay();
        return [$start, $end, $d->translatedFormat('l, d F Y')];
    }
}
