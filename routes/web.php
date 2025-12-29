<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\LogbookController;
use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Kehadiran
    Route::get('/attendance', [AttendanceController::class, 'index'])->name('attendance.index');
    Route::post('/attendance/check-in', [AttendanceController::class, 'checkIn'])->name('attendance.checkin');

    // Logbook
    Route::get('/logbook', [LogbookController::class, 'index'])->name('logbook.index');
    Route::post('/logbook', [LogbookController::class, 'store'])->name('logbook.store');

    // Admin Dashboard & Validasi
    // Route::get('/admin/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');
    // Route::post('/admin/logbook/{id}/validate', [AdminController::class, 'validateLogbook'])->name('admin.logbook.validate');

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

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');
    Route::post('/admin/logbook/{id}/validate', [AdminController::class, 'validateLogbook'])->name('admin.logbook.validate');
});

require __DIR__.'/auth.php';
