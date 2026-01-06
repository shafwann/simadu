<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

// Import Controllers
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\LogbookController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminStudentController;
use App\Http\Controllers\AdminAttendanceController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\AdminManagementController;
use App\Http\Controllers\AdminDosenController;
use App\Models\User;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
});

// Dashboard Redirect Logic (ROLE AWARE)
Route::get('/dashboard', function () {
    $role = Auth::user()->role;

    return match ($role) {
        'admin' => redirect()->route('admin.dashboard'),
        'pembimbing' => redirect()->route('pembimbing.dashboard'),
        default => view('dashboard'), // mahasiswa
    };
})->middleware(['auth', 'verified'])->name('dashboard');


// ==========================================
// GROUP: AUTHENTICATED USERS (UMUM)
// ==========================================
Route::middleware('auth')->group(function () {
    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Attendance (Mahasiswa Check-in/out)
    Route::get('/attendance', [AttendanceController::class, 'index'])->name('attendance.index');
    Route::post('/attendance/check-in', [AttendanceController::class, 'checkIn'])->name('attendance.checkin');
    Route::post('/attendance/check-out', [AttendanceController::class, 'checkOut'])->name('attendance.checkout');

    // Logbook (Mahasiswa)
    Route::get('/logbook', [LogbookController::class, 'index'])->name('logbook.index');
    Route::post('/logbook', [LogbookController::class, 'store'])->name('logbook.store');

    // Laporan (Edit & Update - Shared logic)
    Route::get('/laporan/{laporan}/edit', [LaporanController::class, 'edit'])->name('laporan.edit');
    Route::put('/laporan/{laporan}/update', [LaporanController::class, 'update'])->name('laporan.update');
    Route::get('/laporan/preview/{laporan}', [LaporanController::class, 'preview'])->name('laporan.preview');

    // --- EXPORT ROUTES ---
    Route::get('/export-presensi', function () {
        $data = \App\Models\Attendance::where('user_id', Auth::id())->with('user')->get();
        $callback = function() use ($data) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Nama Mahasiswa', 'Tanggal', 'Jam Masuk', 'Status', 'Lokasi']);
            foreach ($data as $row) {
                $googleMapsLink = $row->location ? "https://www.google.com/maps?q=" . $row->location : "Tidak ada lokasi";
                fputcsv($file, [$row->user->name, Carbon::parse($row->tanggal)->format('d/m/Y'), $row->check_in, $row->status, $googleMapsLink]);
            }
            fclose($file);
        };
        return response()->stream($callback, 200, ["Content-type" => "text/csv", "Content-Disposition" => "attachment; filename=laporan-presensi-pribadi.csv"]);
    })->name('attendance.export');

    Route::get('/export-logbook', function () {
        $data = \App\Models\Logbook::where('user_id', Auth::id())->get();
        $callback = function() use ($data) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Tanggal', 'Kegiatan', 'Status Validasi']);
            foreach ($data as $row) {
                fputcsv($file, [Carbon::parse($row->tanggal)->format('d/m/Y'), $row->kegiatan, $row->is_validated ? 'Sudah' : 'Belum']);
            }
            fclose($file);
        };
        return response()->stream($callback, 200, ["Content-type" => "text/csv", "Content-Disposition" => "attachment; filename=logbook-magang.csv"]);
    })->name('logbook.export');
});

// ==========================================
// GROUP: MAHASISWA ONLY
// ==========================================
Route::middleware(['auth', 'role:mahasiswa'])->group(function() {
    Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
    Route::post('/laporan/upload', [LaporanController::class, 'upload'])->name('laporan.upload');
});

// ==========================================
// GROUP: ADMIN ONLY (Dashboard & Manajemen)
// ==========================================
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {

    // 1. Dashboard Admin
    Route::get('/dashboard', [AdminController::class, 'index'])->name('dashboard');

    // 2. Validasi (Logbook & Laporan)
    Route::post('/logbook/{id}/validate', [AdminController::class, 'validateLogbook'])->name('logbook.validate');
    Route::post('/laporan/{id}/validate', [AdminController::class, 'validateLaporan'])->name('laporan.validate');

    // 3. Manajemen Laporan (Lihat Semua & Download)
    Route::get('/laporan-list', [LaporanController::class, 'adminIndex'])->name('laporan.index');
    Route::get('/laporan/download/{laporan}', [LaporanController::class, 'download'])->name('laporan.download');

    // 4. CRUD Manajemen Mahasiswa
    Route::resource('students', AdminStudentController::class);
    Route::patch('/students/{id}/status', [AdminStudentController::class, 'updateStatus'])->name('students.updateStatus');

    // 5. CRUD Dosen & Assign Mahasiswa
    Route::get('dosen', [AdminDosenController::class, 'index'])->name('dosen.index');
    Route::get('dosen/create', [AdminDosenController::class, 'create'])->name('dosen.create');
    Route::post('dosen/store', [AdminDosenController::class, 'store'])->name('dosen.store');
    Route::delete('dosen/{dosen}', [AdminDosenController::class, 'destroy'])->name('dosen.destroy');
    Route::get('/dosen/{id}/edit', [AdminDosenController::class, 'edit'])->name('dosen.edit');
    Route::put('/dosen/{id}', [AdminDosenController::class, 'update'])->name('dosen.update');

    Route::get('dosen/{dosen}/assign', [AdminDosenController::class, 'showAssignForm'])->name('dosen.assign.form');
    Route::post('dosen/{dosen}/assign', [AdminDosenController::class, 'assignMahasiswa'])->name('dosen.assign.submit');

    // 6. Manajemen Absensi (Manual Input/Edit oleh Admin)
    Route::post('/attendance/store', [AdminAttendanceController::class, 'store'])->name('attendance.store');
    Route::delete('/attendance/{id}', [AdminAttendanceController::class, 'destroy'])->name('attendance.destroy');
    Route::get('/attendance/{id}/edit', [AdminAttendanceController::class, 'edit'])->name('attendance.edit');
    Route::put('/attendance/{id}', [AdminAttendanceController::class, 'update'])->name('attendance.update');

    // 7. MANAJEMEN ADMIN (Khusus Super Admin)
    Route::prefix('manage-admins')->name('manage.')->group(function () {
        Route::get('/', [AdminManagementController::class, 'index'])->name('index');
        Route::get('/create', [AdminManagementController::class, 'create'])->name('create');
        Route::post('/', [AdminManagementController::class, 'store'])->name('store');
        Route::delete('/{id}', [AdminManagementController::class, 'destroy'])->name('destroy');
    });
    
});

// ==========================================
// route utk download data keseluruhan
// ==========================================
Route::get('/export-mahasiswa', function() {
    $mahasiswa = User::where('role', 'mahasiswa')->get();

    $callback = function() use ($mahasiswa) {
        $file = fopen('php://output', 'w');

        // Header CSV
        fputcsv($file, [
            'Nama',
            'NIM',
            'Jurusan',
            'Universitas',
            'Periode Magang',
            'Bidang',
            'Status'
        ]);

        foreach ($mahasiswa as $user) {
            fputcsv($file, [
                $user->name,
                $user->nim,
                $user->prodi,
                $user->instansi,
                $user->tanggal_masuk && $user->tanggal_selesai
                    ? Carbon::parse($user->tanggal_masuk)->format('d F Y') 
                      .' - '.Carbon::parse($user->tanggal_selesai)->format('d F Y')
                    : '',
                $user->bidang ?? '',
                $user->status ?? ''
            ]);
        }

        fclose($file);
    };

    return response()->stream($callback, 200, [
        "Content-type" => "text/csv",
        "Content-Disposition" => "attachment; filename=mahasiswa-magang.csv"
    ]);
})->name('admin.export.mahasiswa');

// ==========================================
// GROUP: PEMBIMBING ONLY (Dashboard Mahasiswa Bimbingan)
// ==========================================
Route::middleware(['auth','role:pembimbing'])->prefix('pembimbing')->name('pembimbing.')->group(function(){
    Route::get('/dashboard', [AdminDosenController::class, 'dashboard'])->name('dashboard');
    Route::get('/mahasiswa/{id}', [AdminDosenController::class, 'detailMahasiswa'])->name('mahasiswa.detail');
});

require __DIR__.'/auth.php';
