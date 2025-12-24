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
    Route::get('/admin/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');
    Route::post('/admin/logbook/{id}/validate', [AdminController::class, 'validateLogbook'])->name('admin.logbook.validate');

    // Export Presensi
    Route::get('/export-presensi', function () {
        $data = \App\Models\Attendance::with('user')->get();
        $callback = function() use ($data) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Nama Mahasiswa', 'Tanggal', 'Jam Masuk', 'Status', 'Lokasi']);
            foreach ($data as $row) {
                $googleMapsLink = $row->location
                    ? "https://www.google.com/maps/search/?api=1&query=" . $row->location
                    : "Tidak ada lokasi";
                fputcsv($file, [
                    $row->user->name,
                    Carbon::parse($row->tanggal)->format('d/m/Y'),
                    $row->check_in,
                    $row->status,
                    $googleMapsLink // Tambahkan ini
                ]);
            }
            fclose($file);
        };
        return response()->stream($callback, 200, [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=laporan-presensi.csv",
        ]);
    })->name('attendance.export');

    // Export Logbook
    Route::get('/export-logbook', function () {
        $userId = Auth::id();
        $data = \App\Models\Logbook::where('user_id', $userId)->get();
        $callback = function() use ($data) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Tanggal', 'Kegiatan', 'Status Validasi']);
            foreach ($data as $row) {
                fputcsv($file, [
                    Carbon::parse($row->tanggal)->format('d/m/Y'), // Diperbaiki agar seragam
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

require __DIR__.'/auth.php';
