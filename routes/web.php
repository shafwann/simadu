<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\LogbookController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminStudentController;
use App\Http\Controllers\AdminAttendanceController;
use App\Http\Controllers\LaporanController;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    // Gunakan Auth::user() agar lebih aman dan dikenali sistem
    if (Auth::user()->role === 'admin') {
        return redirect()->route('admin.dashboard');
    }

    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/attendance', [AttendanceController::class, 'index'])->name('attendance.index');
    Route::post('/attendance/check-in', [AttendanceController::class, 'checkIn'])->name('attendance.checkin');
    Route::post('/attendance/check-out', [AttendanceController::class, 'checkOut'])->name('attendance.checkout');
    Route::get('/logbook', [LogbookController::class, 'index'])->name('logbook.index');
    Route::post('/logbook', [LogbookController::class, 'store'])->name('logbook.store');

    // Export Presensi (PERBAIKAN: Hanya milik user yang login)
    Route::get('/export-presensi', function () {
        // Filter berdasarkan user_id yang sedang login
        $data = \App\Models\Attendance::where('user_id', Auth::id())
                    ->with('user')
                    ->get();

        $callback = function() use ($data) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Nama Mahasiswa', 'Tanggal', 'Jam Masuk', 'Status', 'Lokasi']);

            foreach ($data as $row) {
                $googleMapsLink = $row->location
                    ? "https://www.google.com/maps?q=" . $row->location
                    : "Tidak ada lokasi";

                fputcsv($file, [
                    $row->user->name,
                    Carbon::parse($row->tanggal)->format('d/m/Y'),
                    $row->check_in,
                    $row->status,
                    $googleMapsLink
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=laporan-presensi-pribadi.csv",
        ]);
    })->name('attendance.export');

    // Export Logbook (Sudah benar)
    Route::get('/export-logbook', function () {
        $data = \App\Models\Logbook::where('user_id', Auth::id())->get();

        $callback = function() use ($data) {
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
        };

        return response()->stream($callback, 200, [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=logbook-magang.csv",
        ]);
    })->name('logbook.export');
});

// Mahasiswa: upload & lihat laporan sendiri
Route::middleware(['auth', 'role:mahasiswa'])->group(function() {
    Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
    Route::post('/laporan/upload', [LaporanController::class, 'upload'])->name('laporan.upload');
});

//validasi laporan
Route::post('/admin/laporan/{id}/validate', 
    [LaporanController::class, 'validateLaporan']
)->name('laporan.validate');

Route::post('/admin/laporan/{id}/validate',
    [AdminController::class, 'validateLaporan']
)->name('admin.laporan.validate');

//edit update laporan
Route::middleware('auth')->group(function () {
    Route::get('/laporan/{laporan}/edit', [LaporanController::class, 'edit'])->name('laporan.edit');
    Route::put('/laporan/{laporan}/update', [LaporanController::class, 'update'])->name('laporan.update');
});


// Admin: lihat semua + download
Route::middleware(['auth', 'role:admin'])->group(function() {
    Route::get('/laporan/admin', [LaporanController::class, 'adminIndex'])->name('laporan.admin');
    Route::get('/laporan/download/{laporan}', [LaporanController::class, 'download'])->name('laporan.download');
});

Route::get('/laporan/preview/{laporan}', [LaporanController::class, 'preview'])
    ->name('laporan.preview')
    ->middleware('auth');


Route::middleware(['auth', 'role:admin'])->group(function () {
    // 1. Dashboard Admin
    Route::get('/admin/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');

    // 2. Validasi Logbook
    Route::post('/admin/logbook/{id}/validate', [AdminController::class, 'validateLogbook'])->name('admin.logbook.validate');

    // 3. CRUD Manajemen Mahasiswa
    Route::resource('admin/students', AdminStudentController::class)->names([
        'index' => 'admin.students.index',
        'create' => 'admin.students.create',
        'store' => 'admin.students.store',
        'show' => 'admin.students.show',
        'destroy' => 'admin.students.destroy',
    ]);

    // 4. Update Status Mahasiswa (Aktif/Selesai)
    Route::patch('/admin/students/{id}/status', [AdminStudentController::class, 'updateStatus'])
        ->name('admin.students.updateStatus');

    // 5. Absensi Manual oleh Admin (Sakit/Izin/Alpha)
    Route::post('/admin/attendance/store', [AdminAttendanceController::class, 'store'])
        ->name('admin.attendance.store');

    // 6. Hapus Absensi oleh Admin
    Route::delete('/admin/attendance/{id}', [AdminAttendanceController::class, 'destroy'])
        ->name('admin.attendance.destroy');

    Route::get('/admin/attendance/{id}/edit', [AdminAttendanceController::class, 'edit'])->name('admin.attendance.edit'); // <--- BARU
    Route::put('/admin/attendance/{id}', [AdminAttendanceController::class, 'update'])->name('admin.attendance.update'); // <--- BARU
});

require __DIR__.'/auth.php';
