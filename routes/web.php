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
use App\Http\Controllers\AssessmentController;
use App\Http\Controllers\DashboardController; // ✅ TAMBAH INI

Route::get('/', function () {
    return view('welcome');
});

// ✅ GANTI /dashboard yang lama -> pakai DashboardController (load assessment)
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');


// ==========================================
// GROUP: AUTHENTICATED USERS (UMUM)
// ==========================================
Route::middleware('auth')->group(function () {

    // ✅ TAMBAHAN: DOWNLOAD SERTIFIKAT (USER LOGIN, HANYA MILIK SENDIRI)
    Route::get('/certificate/download', function () {
        $user = \App\Models\User::with('assessment')->findOrFail(Auth::id());

        if (!$user->assessment) {
            abort(404, 'Penilaian belum ada.');
        }

        return app(\App\Http\Controllers\AssessmentController::class)->certificate($user);
    })->name('certificate.download');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Attendance
    Route::get('/attendance', [AttendanceController::class, 'index'])->name('attendance.index');
    Route::post('/attendance/check-in', [AttendanceController::class, 'checkIn'])->name('attendance.checkin');
    Route::post('/attendance/check-out', [AttendanceController::class, 'checkOut'])->name('attendance.checkout');

    // Logbook
    Route::get('/logbook', [LogbookController::class, 'index'])->name('logbook.index');
    Route::post('/logbook', [LogbookController::class, 'store'])->name('logbook.store');

    // ✅ LAPORAN SHARED (OWNER / ADMIN)
    // ini yang bikin route('laporan.download') & route('laporan.preview') ADA dan aman
    Route::get('/laporan/download/{laporan}', [LaporanController::class, 'download'])->name('laporan.download');
    Route::get('/laporan/preview/{laporan}', [LaporanController::class, 'preview'])->name('laporan.preview');

    // Edit/update laporan (owner)
    Route::get('/laporan/{laporan}/edit', [LaporanController::class, 'edit'])->name('laporan.edit');
    Route::put('/laporan/{laporan}', [LaporanController::class, 'update'])->name('laporan.update');

    // --- EXPORT ROUTES (punyamu boleh tetap) ---
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

        // ✅ OPTIONAL: admin download versi route admin.*
        Route::get('/laporan/download/{laporan}', [LaporanController::class, 'download'])->name('laporan.download');

        // CRUD mahasiswa
        Route::resource('students', AdminStudentController::class);
        Route::patch('/students/{user}/status', [AdminStudentController::class, 'updateStatus'])->name('students.updateStatus');

        // absensi manual
        Route::post('/attendance/store', [AdminAttendanceController::class, 'store'])->name('attendance.store');
        Route::delete('/attendance/{attendance}', [AdminAttendanceController::class, 'destroy'])->name('attendance.destroy');
        Route::get('/attendance/{attendance}/edit', [AdminAttendanceController::class, 'edit'])->name('attendance.edit');
        Route::put('/attendance/{attendance}', [AdminAttendanceController::class, 'update'])->name('attendance.update');

        // assessment
        Route::middleware(['auth', 'role:admin'])->prefix('assessment')->name('assessment.')->group(function () {
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
    });

require __DIR__ . '/auth.php';
