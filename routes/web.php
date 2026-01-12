<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

// Controllers
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\LogbookController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminStudentController;
use App\Http\Controllers\AdminAttendanceController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\AdminManagementController;
use App\Http\Controllers\AdminDosenController;
use App\Http\Controllers\AssessmentController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AdminAboutController;
use App\Http\Controllers\AdminPresensiPdfController;

use App\Models\User;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
});

/**
 * Dashboard (Pakai DashboardController)
 * Pastikan DashboardController handle role redirect (admin/pembimbing/mahasiswa)
 */
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');


// ==========================================
// GROUP: AUTHENTICATED USERS (UMUM)
// ==========================================
Route::middleware('auth')->group(function () {

    // DOWNLOAD SERTIFIKAT (USER LOGIN, HANYA MILIK SENDIRI)
    Route::get('/certificate/download', function () {
        $user = User::with('assessment')->findOrFail(Auth::id());

        if (!$user->assessment) {
            abort(404, 'Penilaian belum ada.');
        }

        return app(AssessmentController::class)->certificate($user);
    })->name('certificate.download');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Attendance
    Route::get('/attendance', [AttendanceController::class, 'index'])->name('attendance.index');
    Route::post('/attendance/check-in', [AttendanceController::class, 'checkIn'])->name('attendance.checkin');
    Route::post('/attendance/check-out', [AttendanceController::class, 'checkOut'])->name('attendance.checkout');

    // ✅ TAMBAHAN: MAHASISWA BISA LIHAT RIWAYAT PRESENSI (AKTIF / NONAKTIF TETAP BISA)     
    Route::get('/attendance/history', [AttendanceController::class, 'history'])
        ->name('attendance.history');

    // ✅ TAMBAHAN: MAHASISWA BISA DOWNLOAD PDF PRESENSI (TAMPILAN SAMA DENGAN ADMIN)
    Route::get('/attendance/presensi-pdf', [AttendanceController::class, 'presensiPdf'])
        ->name('attendance.presensi_pdf');

    // Logbook
    Route::get('/logbook', [LogbookController::class, 'index'])->name('logbook.index');
    Route::post('/logbook', [LogbookController::class, 'store'])->name('logbook.store');

    // LAPORAN SHARED (OWNER / ADMIN)
    Route::get('/laporan/download/{laporan}', [LaporanController::class, 'download'])->name('laporan.download');
    Route::get('/laporan/preview/{laporan}', [LaporanController::class, 'preview'])->name('laporan.preview');

    // Edit/update laporan (owner)
    Route::get('/laporan/{laporan}/edit', [LaporanController::class, 'edit'])->name('laporan.edit');
    Route::put('/laporan/{laporan}', [LaporanController::class, 'update'])->name('laporan.update');

    // EXPORT PRESENSI (CSV pribadi)
    Route::get('/export-presensi', function () {
        $data = \App\Models\Attendance::where('user_id', Auth::id())->with('user')->get();

        return response()->stream(function () use ($data) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Nama Mahasiswa', 'Tanggal', 'Jam Masuk', 'Status', 'Lokasi']);

            foreach ($data as $row) {
                $googleMapsLink = $row->location ? $row->location : "Tidak ada lokasi";
                fputcsv($file, [
                    $row->user->name,
                    Carbon::parse($row->tanggal)->format('d/m/Y'),
                    $row->check_in,
                    $row->status,
                    $googleMapsLink
                ]);
            }

            fclose($file);
        }, 200, [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=laporan-presensi-pribadi.csv"
        ]);
    })->name('attendance.export');

    // EXPORT LOGBOOK (CSV pribadi)
    Route::get('/export-logbook', function () {
        $data = \App\Models\Logbook::where('user_id', Auth::id())->get();

        return response()->stream(function () use ($data) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Tanggal', 'Kegiatan', 'Status Validasi']);

            foreach ($data as $row) {
                fputcsv($file, [
                    Carbon::parse($row->tanggal)->format('d/m/Y'),
                    $row->kegiatan,
                    $row->is_validated ? 'Sudah' : 'Belum'
                ]);
            }

            fclose($file);
        }, 200, [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=logbook-magang.csv"
        ]);
    })->name('logbook.export');

    // EXPORT LOGBOOK (PDF pribadi)
    Route::get('/logbook/pdf', [LogbookController::class, 'exportPdf'])
        ->name('logbook.export.pdf');

});


// ==========================================
// GROUP: MAHASISWA ONLY
// ==========================================
Route::middleware(['auth', 'role:mahasiswa'])->group(function () {
    Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
    Route::post('/laporan/upload', [LaporanController::class, 'upload'])->name('laporan.upload');
});


// ==========================================
// GROUP: ADMIN ONLY
// ==========================================
Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        Route::get('/dashboard', [AdminController::class, 'index'])->name('dashboard');

        // validasi
        Route::post('/logbook/{id}/validate', [AdminController::class, 'validateLogbook'])->name('logbook.validate');
        Route::post('/laporan/{id}/validate', [LaporanController::class, 'validateLaporan'])->name('laporan.validate');

        // admin lihat semua laporan
        Route::get('/laporan-list', [LaporanController::class, 'adminIndex'])->name('laporan.index');

        // admin download laporan
        Route::get('/laporan/download/{laporan}', [LaporanController::class, 'download'])->name('laporan.download');

        // CRUD mahasiswa
        Route::resource('students', AdminStudentController::class);
        Route::patch('/students/{user}/status', [AdminStudentController::class, 'updateStatus'])->name('students.updateStatus');

        // ✅ ROUTE DOWNLOAD PDF PRESENSI (TARUH DI DALAM GROUP ADMIN)
        Route::get('/students/{id}/presensi-pdf', [AdminPresensiPdfController::class, 'download'])
            ->name('students.presensi.pdf');

        // ===== DOSEN & ASSIGN MAHASISWA =====
        Route::get('dosen', [AdminDosenController::class, 'index'])->name('dosen.index');
        Route::get('dosen/create', [AdminDosenController::class, 'create'])->name('dosen.create');
        Route::post('dosen/store', [AdminDosenController::class, 'store'])->name('dosen.store');
        Route::delete('dosen/{dosen}', [AdminDosenController::class, 'destroy'])->name('dosen.destroy');
        Route::get('/dosen/{id}/edit', [AdminDosenController::class, 'edit'])->name('dosen.edit');
        Route::put('/dosen/{id}', [AdminDosenController::class, 'update'])->name('dosen.update');
        Route::get('dosen/{dosen}/mahasiswa', [AdminDosenController::class, 'showMahasiswa'])->name('dosen.mahasiswa');
        Route::get('/mahasiswa/{id}', [AdminStudentController::class, 'show'])->name('mahasiswa.show');

        Route::get('dosen/{dosen}/assign', [AdminDosenController::class, 'showAssignForm'])->name('dosen.assign.form');
        Route::post('dosen/{dosen}/assign', [AdminDosenController::class, 'assignMahasiswa'])->name('dosen.assign.submit');

        // absensi manual
        Route::post('/attendance/store', [AdminAttendanceController::class, 'store'])->name('attendance.store');
        Route::delete('/attendance/{id}', [AdminAttendanceController::class, 'destroy'])->name('attendance.destroy');
        Route::get('/attendance/{id}/edit', [AdminAttendanceController::class, 'edit'])->name('attendance.edit');
        Route::put('/attendance/{id}', [AdminAttendanceController::class, 'update'])->name('attendance.update');

        // assessment
        Route::prefix('assessment')->name('assessment.')->group(function () {
            Route::post('/{user}', [AssessmentController::class, 'store'])->name('store');
            Route::patch('/{assessment}', [AssessmentController::class, 'update'])->name('update');
            Route::delete('/{assessment}', [AssessmentController::class, 'destroy'])->name('destroy');
            Route::get('/certificate/{user}', [AssessmentController::class, 'certificate'])->name('certificate');
        });

        // manage admins
        Route::prefix('manage-admins')->name('manage.')->group(function () {
            Route::get('/', [AdminManagementController::class, 'index'])->name('index');
            Route::get('/create', [AdminManagementController::class, 'create'])->name('create');
            Route::post('/', [AdminManagementController::class, 'store'])->name('store');
            Route::delete('/{id}', [AdminManagementController::class, 'destroy'])->name('destroy');
        });

        // ==========================================
        // ABOUT (SUPER ADMIN ONLY - Administrator Sistem)
        // ==========================================
        Route::get('/about', [AdminAboutController::class, 'index'])->name('about.index');
        Route::put('/about', [AdminAboutController::class, 'update'])->name('about.update');
    });


// ==========================================
// Route download data format Bapperlitbang (CSV)
// ==========================================
Route::get('/export-mahasiswa', function () {

    $mahasiswa = User::where('role', 'mahasiswa')->orderBy('name', 'asc')->get();

    $callback = function () use ($mahasiswa) {
        $file = fopen('php://output', 'w');

        // BOM utk Excel
        fputs($file, "\xEF\xBB\xBF");

        // Separator Excel
        fputs($file, "sep=;\n");

        fputcsv($file, ['DATA MAGANG BAPERLITBANG ' . date('Y')], ';');
        fputcsv($file, [], ';');

        fputcsv($file, [
            'NO', 'NAMA', 'NIM', 'JURUSAN', 'UNIVERSITAS', 'KETERANGAN', 'BIDANG', 'AKTIF'
        ], ';');

        foreach ($mahasiswa as $index => $user) {

            $periode = '-';
            if ($user->tanggal_masuk && $user->tanggal_selesai) {
                $start = Carbon::parse($user->tanggal_masuk);
                $end = Carbon::parse($user->tanggal_selesai);

                $bulanIndo = [
                    1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                    5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                    9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
                ];

                $strStart = $start->day . ' ' . $bulanIndo[$start->month] . ' ' . $start->year;
                $strEnd = $end->day . ' ' . $bulanIndo[$end->month] . ' ' . $end->year;

                if ($start->year == $end->year) {
                    $strStart = $start->day . ' ' . $bulanIndo[$start->month];
                }

                $periode = $strStart . ' - ' . $strEnd;
            }

            $bidang = strtoupper(str_replace(["\r", "\n"], " ", $user->bidang ?? ''));
            $status = ucfirst($user->status ?? '-');

            fputcsv($file, [
                $index + 1,
                strtoupper($user->name),
                "'" . $user->nim,
                strtoupper($user->prodi),
                strtoupper($user->instansi),
                $periode,
                $bidang,
                $status
            ], ';');
        }

        fclose($file);
    };

    return response()->stream($callback, 200, [
        "Content-type" => "text/csv",
        "Content-Disposition" => "attachment; filename=DATA_MAGANG_BAPERLITBANG_" . date('Y') . ".csv",
        "Pragma" => "no-cache",
        "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
        "Expires" => "0"
    ]);
})->name('admin.export.mahasiswa');


// ==========================================
// GROUP: PEMBIMBING ONLY
// ==========================================
Route::middleware(['auth', 'role:pembimbing'])
    ->prefix('pembimbing')
    ->name('pembimbing.')
    ->group(function () {

        Route::get('/dashboard', [AdminDosenController::class, 'dashboard'])->name('dashboard');
        Route::get('/mahasiswa/{id}', [AdminDosenController::class, 'detailMahasiswa'])->name('mahasiswa.detail');
    });

require __DIR__ . '/auth.php';
