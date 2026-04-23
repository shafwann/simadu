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
        $mode          = $request->get('mode', 'harian');            // harian | mingguan | bulanan
        $status        = $this->normalizeFilterStatus($request->get('status', 'all')); // all | hadir | ijin | sakit | alpha | telat
        $studentStatus = $request->get('student_status', 'aktif');   // aktif | nonaktif
        $bidang        = $request->get('bidang', 'all');             // hanya dipakai super admin

        // 3. Tentukan Range Tanggal
        [$start, $end, $labelPeriode] = $this->resolveDateRange($mode, $request);

        // 4. Query Mahasiswa
        $studentsBaseQuery = User::query()
            ->where('role', 'mahasiswa')
            ->where('status', $studentStatus)
            // Filter Instansi (Wajib ada jika admin punya instansi)
            ->when($admin->kategori_instansi, fn($q) => $q->where('kategori_instansi', $admin->kategori_instansi))
            // Filter Bidang (Admin Biasa vs Super Admin)
            ->when(!$isSuperAdmin, fn($q) => $q->where('bidang', $adminBidang))
            ->when($isSuperAdmin && $bidang !== 'all', fn($q) => $q->where('bidang', $bidang));

        $students = (clone $studentsBaseQuery)
            ->select('id', 'name', 'nim', 'bidang', 'instansi')
            ->orderBy('name')
            ->get();

        // Ambil daftar bidang untuk dropdown filter (hanya utk super admin)
        $bidangList = (clone $studentsBaseQuery)
            ->whereNotNull('bidang')
            ->select('bidang')
            ->distinct()
            ->orderBy('bidang')
            ->pluck('bidang');

        $studentIds = $students->pluck('id')->all();

        // 5. Query Absensi
        $attQ = Attendance::query()
            ->whereIn('user_id', $studentIds)
            ->whereBetween('tanggal', [$start->toDateString(), $end->toDateString()])
            ->when($status !== 'all', function ($q) use ($status) {
                return $q->whereIn('status', $this->resolveDbStatuses($status));
            });

        $attendances = $attQ->get(['user_id', 'tanggal', 'status']);

        // 6. Logic Rekapitulasi (Hitung Jumlah)
        $rekap = $students->map(function ($s) use ($attendances) {

            // Ambil data absensi milik mahasiswa ini saja
            $rows = $attendances->where('user_id', $s->id);

            // Samakan beberapa variasi status database ke kode yang dipakai view/filter.
            $rows = $rows->map(function ($r) {
                $st = strtolower(trim((string) $r->status));

                if (in_array($st, ['ijin', 'izin', 'i', 'cuti', 'dinas luar'], true)) $st = 'ijin';
                if (in_array($st, ['tanpa keterangan', 'alpha', 'alpa', 'tk'], true)) $st = 'alpha';
                if ($st === 'terlambat') $st = 'telat';

                $r->normalized_status = $st; // simpan di properti sementara
                return $r;
            });

            // Hitung berdasarkan status yang sudah dinormalisasi
            $hadir = $rows->where('normalized_status', 'hadir')->count();
            $telat = $rows->where('normalized_status', 'telat')->count();
            $izin  = $rows->where('normalized_status', 'ijin')->count();
            $sakit = $rows->where('normalized_status', 'sakit')->count();
            $alpha = $rows->where('normalized_status', 'alpha')->count();

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

        if ($status !== 'all') {
            $rekap = $rekap->filter(fn ($row) => ($row['total'] ?? 0) > 0)->values();
        }

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
            'rekap', 'summary', 'isSuperAdmin', 'mode', 'status', 'studentStatus', 'bidang', 'labelPeriode', 'bidangList'
        ));
    }

    private function normalizeFilterStatus(?string $status): string
    {
        $status = strtolower(trim((string) $status));

        return match ($status) {
            'izin' => 'ijin',
            'hadir', 'telat', 'ijin', 'sakit', 'alpha' => $status,
            default => 'all',
        };
    }

    private function resolveDbStatuses(string $status): array
    {
        return match ($status) {
            'hadir' => ['Hadir'],
            'telat' => ['Telat', 'Terlambat'],
            'ijin' => ['Ijin', 'Izin', 'I', 'Cuti', 'Dinas Luar'],
            'sakit' => ['Sakit'],
            'alpha' => ['Tanpa Keterangan', 'Alpha', 'Alpa'],
            default => [],
        };
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
