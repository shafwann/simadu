<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Carbon\Carbon;

// Controllers
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\LogbookController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminStudentController;
use App\Http\Controllers\AdminAttendanceController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\AdminLogbookController;
use App\Http\Controllers\PembimbingPdfController;
use App\Http\Controllers\AdminManagementController;
use App\Http\Controllers\AdminDosenController;
use App\Http\Controllers\AssessmentController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AdminAboutController;
use App\Http\Controllers\AdminPresensiPdfController;
use App\Http\Controllers\DosenRequestController;
use App\Http\Controllers\MasterAgencyController; // Pastikan Controller ini ada

use App\Models\AboutSetting;
use App\Models\User;

/*
|--------------------------------------------------------------------------
| Web Routes (ISOLASI KATEGORI INSTANSI)
|--------------------------------------------------------------------------
*/

// ==========================================
// 1. LANDING PAGE (PUBLIC)
// ==========================================
Route::get('/', function (Request $request) {
    // Fitur Filter Publik: /?instansi=BAPPERIDA
    $filterInstansi = $request->query('instansi');

    $query = User::where('role', 'mahasiswa');

    if ($filterInstansi) {
        $query->where('kategori_instansi', $filterInstansi);
    }

    $total = (clone $query)->count();

    // ✅ PERBAIKAN 1: Tambahkan kolom nim, prodi, bidang, status
    $mahasiswa = (clone $query)
        ->select(
            'name',
            'instansi',
            'kategori_instansi',
            'nim',      // Tambahan
            'prodi',    // Tambahan
            'bidang',   // Tambahan
            'status'    // Tambahan
        )
        ->latest()
        ->paginate(7);

    // ✅ PERBAIKAN 2: Marquee butuh kategori_instansi
    $marquee = (clone $query)
        ->select('name', 'instansi', 'kategori_instansi')
        ->latest()
        ->take(100)
        ->get();

    // 1. Coba Cari Berdasarkan Filter URL
    $about = null;
    if ($filterInstansi) {
        $about = AboutSetting::where('kategori_instansi', $filterInstansi)->first();
    }

    // 2. Jika tidak ada filter, atau data filter tidak ketemu -> Cari MASTER (Buat jika belum ada)
    if (!$about) {
        $about = AboutSetting::firstOrCreate(
            ['kategori_instansi' => 'MASTER'],
            [
                'app_name' => 'SISTEM MAGANG KARANGANYAR', // Default Title
                'agency_name' => 'PEMERINTAH KAB. KARANGANYAR',
                'app_description' => 'Portal Magang Kabupaten Karanganyar',
                'nama_instansi' => 'Pusat',
                'jam_operasional' => 'Senin-Jumat: 08.00-16.00',
                'radius_meter' => 50
            ]
        );
    }

    return view('welcome', compact('total', 'mahasiswa', 'marquee', 'about'));
});

// ==========================================
// 2. LOBBY UTAMA (PENGALIH ARUS)
// ==========================================
// Route ini yang menentukan user akan dilempar kemana (Admin/Mahasiswa/Master)
// Pastikan logic di DashboardController sudah benar seperti diskusi sebelumnya.
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');


// ==========================================
// 3. GROUP: AUTHENTICATED USERS (SHARED)
// ==========================================
// Hanya fitur umum seperti Profile yang ditaruh disini.
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


// ==========================================
// 4. GROUP: MAHASISWA ONLY
// ==========================================
// Semua fitur yang HANYA boleh diakses mahasiswa masuk sini.
Route::middleware(['auth', 'role:mahasiswa'])->group(function () {

    // Certificate
    Route::get('/certificate/download', function () {
        $user = User::with('assessment')->findOrFail(Auth::id());
        if (!$user->assessment) abort(404, 'Penilaian belum ada.');
        return app(AssessmentController::class)->certificate($user);
    })->name('certificate.download');

    // Attendance (Absen)
    Route::get('/attendance', [AttendanceController::class, 'index'])->name('attendance.index');
    Route::post('/attendance/check-in', [AttendanceController::class, 'checkIn'])->name('attendance.checkin');
    Route::post('/attendance/check-out', [AttendanceController::class, 'checkOut'])->name('attendance.checkout');
    Route::get('/attendance/history', [AttendanceController::class, 'history'])->name('attendance.history');
    Route::get('/attendance/presensi-pdf', [AttendanceController::class, 'presensiPdf'])->name('attendance.presensi_pdf');

    // Logbook
    Route::get('/logbook', [LogbookController::class, 'index'])->name('logbook.index');
    Route::post('/logbook', [LogbookController::class, 'store'])->name('logbook.store');
    Route::get('/logbook/pdf', [LogbookController::class, 'exportPdf'])->name('logbook.export.pdf');

    // Laporan (CRUD Mahasiswa)
    Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
    Route::post('/laporan/upload', [LaporanController::class, 'upload'])->name('laporan.upload');
    Route::get('/laporan/download/{laporan}', [LaporanController::class, 'download'])->name('laporan.download');
    Route::get('/laporan/preview/{laporan}', [LaporanController::class, 'preview'])->name('laporan.preview');
    Route::get('/laporan/{laporan}/edit', [LaporanController::class, 'edit'])->name('laporan.edit');
    Route::put('/laporan/{laporan}', [LaporanController::class, 'update'])->name('laporan.update');
    Route::delete('/laporan/{laporan}', [LaporanController::class, 'destroy'])->name('laporan.destroy');


    // Data Diri & Bidang
    Route::get('/bidang-divisi', [ProfileController::class, 'showBidang'])->name('mahasiswa.bidang.show');
    Route::patch('/mahasiswa/bidang', [ProfileController::class, 'updateBidang'])->name('mahasiswa.bidang.update');

    // Request Pembimbing
    Route::get('/pembimbing/request', [DosenRequestController::class, 'create'])->name('mahasiswa.pembimbing.request.create');
    Route::post('/pembimbing/request', [DosenRequestController::class, 'store'])->name('mahasiswa.pembimbing.request.store');

    // Export Data Pribadi (CSV)
    Route::get('/export-presensi', function () {
        $data = \App\Models\Attendance::where('user_id', Auth::id())->with('user')->get();
        return response()->stream(function () use ($data) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Nama', 'Tanggal', 'Masuk', 'Status', 'Lokasi']);
            foreach ($data as $row) {
                fputcsv($file, [$row->user->name, Carbon::parse($row->tanggal)->format('d/m/Y'), $row->check_in, $row->status, $row->location ?? '-']);
            }
            fclose($file);
        }, 200, ["Content-type" => "text/csv", "Content-Disposition" => "attachment; filename=presensi-pribadi.csv"]);
    })->name('attendance.export');

    Route::get('/export-logbook', function () {
        $data = \App\Models\Logbook::where('user_id', Auth::id())->get();
        return response()->stream(function () use ($data) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Tanggal', 'Kegiatan', 'Status Validasi']);
            foreach ($data as $row) {
                fputcsv($file, [Carbon::parse($row->tanggal)->format('d/m/Y'), $row->kegiatan, $row->is_validated ? 'Valid' : 'Pending']);
            }
            fclose($file);
        }, 200, ["Content-type" => "text/csv", "Content-Disposition" => "attachment; filename=logbook-pribadi.csv"]);
    })->name('logbook.export');
});


// ==========================================
// 5. GROUP: ADMIN ONLY (ISOLASI BERLAKU)
// ==========================================
Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        // Dashboard Admin
        Route::get('/dashboard', [AdminController::class, 'index'])->name('dashboard');

        // Validasi
        Route::post('/logbook/{id}/validate', [AdminController::class, 'validateLogbook'])->name('logbook.validate');
        Route::post('/laporan/{id}/validate', [LaporanController::class, 'validateLaporan'])->name('laporan.validate');
        Route::post('/logbook/validate-all', [AdminController::class, 'validateAllLogbook'])->name('logbook.validateAll');

        // Laporan (View Admin)
        Route::get('/laporan-list', [LaporanController::class, 'adminIndex'])->name('laporan.index');
        Route::get('/laporan/download/{laporan}', [LaporanController::class, 'download'])->name('laporan.download');

        // CRUD Mahasiswa
        Route::resource('students', AdminStudentController::class);
        Route::patch('/students/{user}/status', [AdminStudentController::class, 'updateStatus'])->name('students.updateStatus');

        // PDF & Export
        Route::get('/students/{id}/presensi-pdf', [AdminPresensiPdfController::class, 'download'])->name('students.presensi.pdf');
        Route::get('/students/{student}/logbook/pdf', [AdminLogbookController::class, 'exportPdf'])->name('students.logbook.pdf');

        // Dosen Request & CRUD
        Route::get('/pembimbing-requests', [DosenRequestController::class, 'index'])->name('pembimbing-requests.requests');
        Route::post('/pembimbing-requests/{id}/approve', [DosenRequestController::class, 'approve'])->name('pembimbing-requests.approve');
        Route::post('/pembimbing-requests/{id}/reject', [DosenRequestController::class, 'reject'])->name('pembimbing-requests.reject');

        Route::get('/dosen', [AdminDosenController::class, 'index'])->name('dosen.index');
        Route::get('/dosen/create', [AdminDosenController::class, 'create'])->name('dosen.create');
        Route::post('/dosen', [AdminDosenController::class, 'store'])->name('dosen.store');
        Route::get('/dosen/{id}/edit', [AdminDosenController::class, 'edit'])->name('dosen.edit');
        Route::put('/dosen/{id}', [AdminDosenController::class, 'update'])->name('dosen.update');
        Route::delete('/dosen/{dosen}', [AdminDosenController::class, 'destroy'])->name('dosen.destroy');
        Route::get('/dosen/{dosen}/mahasiswa', [AdminDosenController::class, 'showMahasiswa'])->name('dosen.mahasiswa');

        // Attendance Management
        Route::post('/attendance/store', [AdminAttendanceController::class, 'store'])->name('attendance.store');
        Route::delete('/attendance/{id}', [AdminAttendanceController::class, 'destroy'])->name('attendance.destroy');
        Route::get('/attendance/{id}/edit', [AdminAttendanceController::class, 'edit'])->name('attendance.edit');
        Route::put('/attendance/{id}', [AdminAttendanceController::class, 'update'])->name('attendance.update');

        // Assessment / Sertifikat
        Route::prefix('assessment')->name('assessment.')->group(function () {
            Route::post('/{user}', [AssessmentController::class, 'store'])->name('store');
            Route::patch('/{assessment}', [AssessmentController::class, 'update'])->name('update');
            Route::delete('/{assessment}', [AssessmentController::class, 'destroy'])->name('destroy');
            Route::get('/certificate/{user}', [AssessmentController::class, 'certificate'])->name('certificate');
        });

        // Manage Admins
        Route::prefix('manage-admins')->name('manage.')->group(function () {
            Route::get('/', [AdminManagementController::class, 'index'])->name('index');
            Route::get('/create', [AdminManagementController::class, 'create'])->name('create');
            Route::post('/', [AdminManagementController::class, 'store'])->name('store');
            Route::delete('/{id}', [AdminManagementController::class, 'destroy'])->name('destroy');
        });

        // About Setting (Hanya mengubah setting instansinya sendiri)
        Route::get('/about', [AdminAboutController::class, 'index'])->name('about.index');
        Route::put('/about', [AdminAboutController::class, 'update'])->name('about.update');

        // Export Mahasiswa (CSV)
        Route::get('/export-mahasiswa', function () {
            $mahasiswa = User::where('role', 'mahasiswa')->orderBy('name', 'asc')->get();
            $filename = "DATA_MAGANG_" . (Auth::user()->kategori_instansi ?? 'ALL') . "_" . date('Y') . ".csv";

            return response()->stream(function () use ($mahasiswa) {
                $file = fopen('php://output', 'w');
                fputs($file, "\xEF\xBB\xBF"); // BOM
                fputs($file, "sep=;\n");
                fputcsv($file, ['DATA MAGANG ' . (Auth::user()->kategori_instansi ?? '') . ' ' . date('Y')], ';');
                fputcsv($file, [], ';');
                fputcsv($file, ['NO', 'NAMA', 'NIM', 'JURUSAN', 'UNIVERSITAS', 'KETERANGAN', 'BIDANG', 'AKTIF'], ';');

                foreach ($mahasiswa as $index => $u) {
                    fputcsv($file, [
                        $index + 1, strtoupper($u->name), "'" . $u->nim, strtoupper($u->prodi), strtoupper($u->instansi),
                        ($u->tanggal_masuk && $u->tanggal_selesai ? Carbon::parse($u->tanggal_masuk)->format('d/m/Y') . ' - ' . Carbon::parse($u->tanggal_selesai)->format('d/m/Y') : '-'),
                        strtoupper($u->bidang ?? ''), ucfirst($u->status)
                    ], ';');
                }
                fclose($file);
            }, 200, ["Content-type" => "text/csv", "Content-Disposition" => "attachment; filename=" . $filename]);
        })->name('export.mahasiswa');
    });


// ==========================================
// 6. GROUP: PEMBIMBING ONLY
// ==========================================
Route::middleware(['auth', 'role:pembimbing'])
    ->prefix('pembimbing')
    ->name('pembimbing.')
    ->group(function () {
        Route::get('/dashboard', [AdminDosenController::class, 'dashboard'])->name('dashboard');
        Route::get('/mahasiswa/{id}', [AdminDosenController::class, 'detailMahasiswa'])->name('mahasiswa.detail');
        Route::get('/mahasiswa/{id}/presensi-pdf', [PembimbingPdfController::class, 'presensiPdf'])->name('mahasiswa.presensi.pdf');
        Route::get('/mahasiswa/{id}/logbook-pdf', [PembimbingPdfController::class, 'logbookPdf'])->name('mahasiswa.logbook.pdf');
    });


// ==========================================
// GROUP: MASTER ADMIN ONLY
// ==========================================
Route::middleware(['auth', 'role:master'])
    ->prefix('master')
    ->name('master.')
    ->group(function () {
        Route::get('/agencies', [MasterAgencyController::class, 'index'])->name('agencies.index');
        Route::get('/agencies/create', [MasterAgencyController::class, 'create'])->name('agencies.create');
        Route::post('/agencies', [MasterAgencyController::class, 'store'])->name('agencies.store');

        // ✅ TAMBAHAN: Route Edit & Update
        Route::get('/agencies/{id}/edit', [MasterAgencyController::class, 'edit'])->name('agencies.edit');
        Route::put('/agencies/{id}', [MasterAgencyController::class, 'update'])->name('agencies.update');

        Route::delete('/agencies/{id}', [MasterAgencyController::class, 'destroy'])->name('agencies.destroy');

        //Setting Global Branding & Footer
        Route::get('/settings', [App\Http\Controllers\MasterSettingController::class, 'index'])->name('settings.index');
        Route::put('/settings', [App\Http\Controllers\MasterSettingController::class, 'update'])->name('settings.update');
    });

require __DIR__ . '/auth.php';
