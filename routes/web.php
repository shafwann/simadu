<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

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
use App\Http\Controllers\MasterAgencyController;
use App\Http\Controllers\MasterSettingController;
use App\Http\Controllers\AdminAttendanceRecapController;
use App\Http\Controllers\FaceResetRequestController;
use App\Http\Controllers\FaceVerificationController;
use App\Models\AboutSetting;
use App\Models\User;
use App\Models\Visit;

/*
|--------------------------------------------------------------------------
| Web Routes (ISOLASI KATEGORI INSTANSI)
|--------------------------------------------------------------------------
*/

// ==========================================
// 1. LANDING PAGE (PUBLIC)
// ==========================================
Route::get('/', function (Request $request) {

    $filterInstansi = $request->query('instansi');

    $query = User::where('role', 'mahasiswa');

    if ($filterInstansi) {
        $query->where('kategori_instansi', $filterInstansi);
    }

    $total = (clone $query)->count();

    $mahasiswa = (clone $query)
        ->select(
            'name',
            'instansi',
            'kategori_instansi',
            'nim',
            'prodi',
            'bidang',
            'status'
        )
        ->latest()
        ->paginate(7);

    $marquee = (clone $query)
        ->select('name', 'instansi', 'kategori_instansi')
        ->latest()
        ->take(100)
        ->get();

    $about = null;
    if ($filterInstansi) {
        $about = AboutSetting::where('kategori_instansi', $filterInstansi)->first();
    }

    if (!$about) {
        $about = AboutSetting::firstOrCreate(
            ['kategori_instansi' => 'MASTER'],
            [
                'app_name'        => 'SISTEM MAGANG KARANGAYAR',
                'agency_name'     => 'PEMERINTAH KAB. KARANGANYAR',
                'app_description' => 'Portal Magang Kabupaten Karanganyar',
                'nama_instansi'   => 'Pusat',
                'jam_operasional' => 'Senin-Jumat: 08.00-16.00',
                'radius_meter'    => 50
            ]
        );
    }

    $aboutMaster = AboutSetting::firstOrCreate(
        ['kategori_instansi' => 'MASTER'],
        [
            'app_name'        => 'SISTEM MAGANG KARANGAYAR',
            'agency_name'     => 'PEMERINTAH KAB. KARANGANYAR',
            'app_description' => 'Portal Magang Kabupaten Karanganyar',
            'nama_instansi'   => 'Pusat',
            'jam_operasional' => 'Senin-Jumat: 08.00-16.00',
            'radius_meter'    => 50
        ]
    );

    $instansiAktif = User::where('role', 'admin')
        ->where('bidang', 'Administrator Sistem')
        ->where('status', 'aktif')
        ->whereNotNull('kategori_instansi')
        ->where('kategori_instansi', '!=', 'MASTER')
        ->select('kategori_instansi')
        ->distinct()
        ->orderBy('kategori_instansi')
        ->get();

    $pesertaAktifPerInstansi = User::where('role', 'mahasiswa')
        ->where('status', 'aktif')
        ->whereNotNull('kategori_instansi')
        ->select('kategori_instansi', DB::raw('COUNT(*) as total'))
        ->groupBy('kategori_instansi')
        ->orderByDesc('total')
        ->get();

    $visitsToday = Visit::whereDate('created_at', today())->count();
    $totalVisits = Visit::count();

    // halaman unik yang dibuka hari ini
    $pagesToday = Visit::whereDate('created_at', today())
        ->distinct('path')
        ->count('path');

    // total halaman unik sepanjang waktu
    $totalPages = Visit::distinct('path')->count('path');

    return view('welcome', compact(
        'total',
        'mahasiswa',
        'marquee',
        'about',
        'aboutMaster',
        'instansiAktif',
        'pesertaAktifPerInstansi',
        'visitsToday',
        'totalVisits',
        'pagesToday',
        'totalPages',
    ));
})->middleware('track.visits');

// ==========================================
// 2. LOBBY UTAMA (PENGALIH ARUS)
// ==========================================
Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'verified', 'no-cache'])->name('dashboard');

// ==========================================
// 3. GROUP: AUTHENTICATED USERS (SHARED)
// ==========================================
Route::middleware(['auth', 'no-cache'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// ==========================================
// 4. GROUP: MAHASISWA ONLY
// ==========================================
Route::middleware(['auth', 'no-cache', 'role:mahasiswa'])->group(function () {

    // Certificate
    Route::get('/certificate/download', function () {
        $user = User::with('assessment')->findOrFail(Auth::id());
        if (!$user->assessment) abort(404, 'Penilaian belum ada.');
        return app(AssessmentController::class)->certificate($user);
    })->name('certificate.download');

    // Attendance
    Route::get('/attendance', [AttendanceController::class, 'index'])->name('attendance.index');
    Route::post('/attendance/check-in', [AttendanceController::class, 'checkIn'])->name('attendance.checkin');
    Route::post('/attendance/check-out', [AttendanceController::class, 'checkOut'])->name('attendance.checkout');
    Route::get('/attendance/history', [AttendanceController::class, 'history'])->name('attendance.history');

    Route::get('/attendance/presensi-pdf', [AdminPresensiPdfController::class, 'myPresensiPdf'])->name('attendance.presensi_pdf');

    // Logbook
    Route::get('/logbook', [LogbookController::class, 'index'])->name('logbook.index');
    Route::post('/logbook', [LogbookController::class, 'store'])->name('logbook.store');
    Route::get('/logbook/pdf', [LogbookController::class, 'exportPdf'])->name('logbook.export.pdf');
    Route::get('/logbook/{logbook}/edit', [LogbookController::class, 'edit'])->name('logbook.edit');
    Route::put('/logbook/{logbook}', [LogbookController::class, 'update'])->name('logbook.update');
    Route::delete('/logbook/{logbook}', [LogbookController::class, 'destroy'])->name('logbook.destroy');

    // Laporan
    Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
    Route::post('/laporan/upload', [LaporanController::class, 'upload'])->name('laporan.upload');
    Route::get('/laporan/download/{laporan}', [LaporanController::class, 'download'])->name('laporan.download');
    Route::get('/laporan/preview/{laporan}', [LaporanController::class, 'preview'])->name('laporan.preview');
    Route::get('/laporan/{laporan}/edit', [LaporanController::class, 'edit'])->name('laporan.edit');
    Route::put('/laporan/{laporan}', [LaporanController::class, 'update'])->name('laporan.update');
    Route::delete('/laporan/{laporan}', [LaporanController::class, 'destroy'])->name('laporan.destroy');
    Route::get('/laporan/{laporan}/ajuan', [LaporanController::class, 'ajuan'])->name('laporan.ajuan');
    Route::patch('/laporan/{laporan}/ajuan-kembali', [LaporanController::class, 'ajuanKembali'])->name('laporan.ajuan-kembali');

    // Bidang
    Route::get('/bidang-divisi', [ProfileController::class, 'showBidang'])->name('mahasiswa.bidang.show');
    Route::patch('/mahasiswa/bidang', [ProfileController::class, 'updateBidang'])->name('mahasiswa.bidang.update');

    // Request Pembimbing
    Route::get('/pembimbing/request', [DosenRequestController::class, 'create'])->name('mahasiswa.pembimbing.request.create');
    Route::post('/pembimbing/request', [DosenRequestController::class, 'store'])->name('mahasiswa.pembimbing.request.store');

    // Search Dosen (AJAX)
    Route::get('/search-dosen', [DosenRequestController::class, 'search'])->name('search.dosen');

    // Face Reset Request
    Route::get('/face-reset-request', [FaceResetRequestController::class, 'create'])->name('face-reset-request.create');
    Route::post('/face-reset-request', [FaceResetRequestController::class, 'store'])->name('face-reset-request.store');

    // Face Verification
    Route::get('/face-setup', [FaceVerificationController::class, 'setupView'])->name('face.setup');
    Route::post('/face-setup', [FaceVerificationController::class, 'storeSetup'])->name('face.setup.store');
    Route::get('/face-verification', [FaceVerificationController::class, 'verifyView'])->name('face.verify');
    Route::post('/face-verify', [FaceVerificationController::class, 'verify'])->name('face.verify.submit');
});

// ==========================================
// 5. GROUP: ADMIN ONLY
// ==========================================
Route::middleware(['auth', 'no-cache', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        Route::get('/dashboard', [AdminController::class, 'index'])->name('dashboard');

        // Rekap Absensi
        Route::get('/rekap-absensi', [AdminAttendanceRecapController::class, 'index'])->name('attendance.recap');

        // Validasi
        Route::post('/logbook/{id}/validate', [AdminController::class, 'validateLogbook'])->name('logbook.validate');
        Route::post('/laporan/{id}/validate', [LaporanController::class, 'validateLaporan'])->name('laporan.validate');
        Route::post('/logbook/validate-all', [AdminController::class, 'validateAllLogbook'])->name('logbook.validateAll');

        // Logbook actions
        Route::post('/logbook/{id}/reject', [AdminController::class, 'rejectLogbook'])->name('logbook.reject');
        Route::delete('/logbook/{id}', [AdminController::class, 'destroyLogbook'])->name('logbook.destroy');

        // Laporan actions
        Route::post('/laporan/{id}/reject', [AdminController::class, 'rejectLaporan'])->name('laporan.reject');

        // Laporan (Admin View)
        Route::get('/laporan/{laporan}/preview', [LaporanController::class, 'preview'])->name('laporan.preview');
        Route::get('/laporan/{laporan}/download', [LaporanController::class, 'download'])->name('laporan.download');
        Route::delete('laporan/{laporan}/destroy', [LaporanController::class, 'adminDestroy'])->name('laporan.destroy');

        // CRUD Mahasiswa
        Route::resource('students', AdminStudentController::class);
        Route::patch('/students/{user}/status', [AdminStudentController::class, 'updateStatus'])->name('students.updateStatus');

        // Face Reset Management
        Route::get('/face-reset-requests', [AdminStudentController::class, 'faceResetRequests'])->name('face-reset.index');
        Route::post('/students/{id}/face-reset', [AdminStudentController::class, 'resetFaceDescriptor'])->name('students.face.reset');
        Route::post('/face-reset-requests/{id}/approve', [AdminStudentController::class, 'approveFaceReset'])->name('face-reset.approve');
        Route::post('/face-reset-requests/{id}/reject', [AdminStudentController::class, 'rejectFaceReset'])->name('face-reset.reject');

        // PDF & Export
        Route::get('/students/{id}/presensi-pdf', [AdminPresensiPdfController::class, 'download'])->name('students.presensi.pdf');
        Route::get('/students/{student}/logbook/pdf', [AdminLogbookController::class, 'exportPdf'])->name('students.logbook.pdf');

        // Dosen Request
        Route::get('/pembimbing-requests', [DosenRequestController::class, 'index'])->name('pembimbing-requests.requests');
        Route::post('/pembimbing-requests/{id}/approve', [DosenRequestController::class, 'approve'])->name('pembimbing-requests.approve');
        Route::post('/pembimbing-requests/{id}/reject', [DosenRequestController::class, 'reject'])->name('pembimbing-requests.reject');

        // Dosen
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

        // Assessment
        Route::prefix('assessment')->name('assessment.')->group(function () {
            Route::post('/{user}', [AssessmentController::class, 'store'])->name('store');
            Route::patch('/{assessment}', [AssessmentController::class, 'update'])->name('update');
            Route::delete('/{assessment}', [AssessmentController::class, 'destroy'])->name('destroy');
            Route::get('/certificate/{user}', [AssessmentController::class, 'certificate'])->name('certificate');

            // upload & delete scan sertifikat
            Route::patch('/{assessment}/scan', [AssessmentController::class, 'uploadScan'])->name('scan.upload');
            Route::delete('/{assessment}/scan', [AssessmentController::class, 'destroyScan'])->name('scan.destroy');
        });

        // Manage Admins
        Route::prefix('manage-admins')->name('manage.')->group(function () {
            Route::get('/', [AdminManagementController::class, 'index'])->name('index');
            Route::get('/create', [AdminManagementController::class, 'create'])->name('create');
            Route::post('/', [AdminManagementController::class, 'store'])->name('store');
            Route::delete('/{id}', [AdminManagementController::class, 'destroy'])->name('destroy');
        });

        // About Setting
        Route::get('/about', [AdminAboutController::class, 'index'])->name('about.index');
        Route::put('/about', [AdminAboutController::class, 'update'])->name('about.update');

        // Export Mahasiswa (CSV)
        Route::get('/export-mahasiswa', [AdminStudentController::class, 'exportMahasiswaCsv'])->name('export.mahasiswa');
    });

// ==========================================
// 6. GROUP: PEMBIMBING ONLY
// ==========================================
Route::middleware(['auth', 'no-cache', 'role:pembimbing'])
    ->prefix('pembimbing')
    ->name('pembimbing.')
    ->group(function () {
        Route::get('/dashboard', [AdminDosenController::class, 'dashboard'])->name('dashboard');
        Route::get('/mahasiswa/{id}', [AdminDosenController::class, 'detailMahasiswa'])->name('mahasiswa.detail');
        Route::get('/mahasiswa/{id}/presensi-pdf', [PembimbingPdfController::class, 'presensiPdf'])->name('mahasiswa.presensi.pdf');
        Route::get('/mahasiswa/{id}/logbook-pdf', [PembimbingPdfController::class, 'logbookPdf'])->name('mahasiswa.logbook.pdf');
    });

// ==========================================
// 7. GROUP: MASTER ONLY
// ==========================================
Route::middleware(['auth', 'no-cache', 'role:master'])
    ->prefix('master')
    ->name('master.')
    ->group(function () {
        Route::get('/agencies', [MasterAgencyController::class, 'index'])->name('agencies.index');
        Route::get('/agencies/create', [MasterAgencyController::class, 'create'])->name('agencies.create');
        Route::post('/agencies', [MasterAgencyController::class, 'store'])->name('agencies.store');

        Route::get('/agencies/{id}/edit', [MasterAgencyController::class, 'edit'])->name('agencies.edit');
        Route::put('/agencies/{id}', [MasterAgencyController::class, 'update'])->name('agencies.update');
        Route::delete('/agencies/{id}', [MasterAgencyController::class, 'destroy'])->name('agencies.destroy');

        Route::get('/settings', [MasterSettingController::class, 'index'])->name('settings.index');
        Route::put('/settings', [MasterSettingController::class, 'update'])->name('settings.update');
    });

require __DIR__ . '/auth.php';
