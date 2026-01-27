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

        // role logic (sesuai project kamu)
        $isSuperAdmin = ($admin->role === 'admin' && $admin->bidang === 'Administrator Sistem');
        $adminBidang  = $admin->bidang;

        // filter input
        $mode   = $request->get('mode', 'harian');   // harian | mingguan | bulanan
        $status = $request->get('status', 'all');    // all | hadir | izin | sakit | alpha | telat | ...
        $bidang = $request->get('bidang', 'all');    // hanya dipakai super admin

        // tentukan start-end tanggal
        [$start, $end, $labelPeriode] = $this->resolveDateRange($mode, $request);

        // ambil mahasiswa sesuai instansi + bidang (kalau admin bidang)
        $studentsQ = User::query()
            ->where('role', 'mahasiswa')
            ->when(!$isSuperAdmin, fn($q) => $q->where('bidang', $adminBidang))
            ->when($isSuperAdmin && $bidang !== 'all', fn($q) => $q->where('bidang', $bidang));

        $students = $studentsQ->select('id','name','nim','bidang','instansi')->get();

        // ✅ TAMBAHAN: daftar bidang dinamis untuk dropdown (tidak mengurangi apapun)
        // - kalau super admin: ambil semua bidang dari mahasiswa (yang sudah kena filter instansi/bidang admin jika ada)
        // - kalau admin bidang biasa: tetap akan hasilkan 1 bidang (bidang admin itu sendiri), tapi aman
        $bidangList = (clone $studentsQ)
            ->whereNotNull('bidang')
            ->select('bidang')
            ->distinct()
            ->orderBy('bidang')
            ->pluck('bidang');

        $studentIds = $students->pluck('id')->all();

        // query absensi (JOIN via user_id)
        $attQ = Attendance::query()
            ->whereIn('user_id', $studentIds)
            ->whereBetween('tanggal', [$start->toDateString(), $end->toDateString()])
            ->when($status !== 'all', function($q) use ($status) {
                // asumsi kolom "status" menyimpan: Hadir/Telat/Izin/Sakit/Alpha dll
                return $q->where('status', $status);
            });

        $attendances = $attQ->get(['user_id','tanggal','status']);

        // rekap per mahasiswa
        $rekap = $students->map(function($s) use ($attendances) {

    // 1️⃣ Ambil absensi mahasiswa
    $rows = $attendances->where('user_id', $s->id);

    // 2️⃣ NORMALISASI status (AMAN)
    $rows = $rows->map(function ($r) {
        $r->status = strtolower(trim($r->status));
        return $r;
    });

    // 3️⃣ FILTER status valid SAJA (INI JAWABAN PERTANYAAN KAMU)
    $validStatus = ['hadir','telat','ijin','sakit','alpha'];
    $rows = $rows->whereIn('status', $validStatus);

    // 4️⃣ Hitung per status
    $count = fn($st) => $rows->where('status', $st)->count();

    $hadir = $count('hadir');
    $telat = $count('telat');
    $ijin  = $count('ijin');
    $sakit = $count('sakit');
    $alpha = $count('alpha');

    // 5️⃣ Total SEKARANG sudah bersih
    $total   = $rows->count();
    $present = $hadir + $telat;
    $persen  = $total > 0 ? round(($present / $total) * 100, 1) : 0;

    return [
        'id' => $s->id,
        'name' => $s->name,
        'nim' => $s->nim,
        'bidang' => $s->bidang,
        'instansi' => $s->instansi,
        'hadir' => $hadir,
        'telat' => $telat,
        'ijin' => $ijin,
        'sakit' => $sakit,
        'alpha' => $alpha,
        'total' => $total,
        'persen' => $persen,
    ];
});

        // kartu ringkasan total
        $summary = [
            'mahasiswa' => $rekap->count(),
            'hadir' => $rekap->sum('hadir'),
            'telat' => $rekap->sum('telat'),
            'ijin'  => $rekap->sum('ijin'),
            'sakit' => $rekap->sum('sakit'),
            'alpha' => $rekap->sum('alpha'),
            'total' => $rekap->sum('total'),
        ];

        return view('attendance.recap', compact(
            'rekap','summary','isSuperAdmin','mode','status','bidang','labelPeriode',
            'bidangList' // ✅ TAMBAHAN: kirim ke view
        ));
    }

    private function resolveDateRange(string $mode, Request $request): array
    {
        $today = Carbon::today();

        
            
        if ($mode === 'bulanan') {
            // input: month (YYYY-MM) optional
            $m = $request->filled('month') ? Carbon::createFromFormat('Y-m', $request->month) : $today->copy();
            $start = $m->copy()->startOfMonth();
            $end   = $m->copy()->endOfMonth();
            return [$start, $end, $m->format('F Y')];
        }

        // default harian
        $d = $request->filled('date') ? Carbon::parse($request->date) : $today;
        $start = $d->copy()->startOfDay();
        $end   = $d->copy()->endOfDay();
        return [$start, $end, $d->format('d M Y')];
    }
}