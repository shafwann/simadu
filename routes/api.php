<?php

use App\Http\Controllers\Api\AdminStudentApiController;
use App\Http\Controllers\Api\AuthApiController;
use App\Http\Controllers\Api\AttendanceApiController;
use App\Http\Controllers\Api\DosenApiController;
use App\Http\Controllers\DosenRequestController;
use App\Http\Controllers\Api\LaporanApiController;
use App\Http\Controllers\Api\LogbookApiController;
use App\Http\Controllers\FaceVerificationController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Endpoint di file ini dipakai untuk request JSON/AJAX dari frontend.
| Karena aplikasi masih memakai login session Laravel, route API ini tetap
| memuat middleware "web" agar session, auth, dan CSRF token tetap tersedia.
|
*/

Route::middleware(['web', 'no-cache'])->group(function () {
    Route::get('/documentation', function () {
        return view('docs.swagger');
    })->name('api.documentation');
});

Route::middleware(['web', 'no-cache'])
    ->prefix('auth')
    ->name('api.auth.')
    ->group(function () {
        Route::get('/csrf', [AuthApiController::class, 'csrf'])->name('csrf');
        Route::post('/login', [AuthApiController::class, 'login'])->name('login');
        Route::middleware('auth')->group(function () {
            Route::get('/me', [AuthApiController::class, 'me'])->name('me');
            Route::post('/logout', [AuthApiController::class, 'logout'])->name('logout');
        });
    });

Route::get('/', function () {
    return response()->json([
        'name' => config('app.name'),
        'message' => 'API aplikasi aktif.',
        'documentation' => url('/api/documentation'),
        'openapi' => url('/docs/openapi.json'),
        'endpoints' => [
            [
                'method' => 'POST',
                'path' => '/api/face-verify',
                'name' => 'api.face.verify',
            ],
            [
                'method' => 'GET',
                'path' => '/api/dosen/search',
                'name' => 'api.dosen.search',
            ],
            [
                'method' => 'GET',
                'path' => '/api/logbooks',
                'name' => 'api.logbooks.index',
            ],
            [
                'method' => 'GET',
                'path' => '/api/laporans',
                'name' => 'api.laporans.index',
            ],
            [
                'method' => 'GET',
                'path' => '/api/attendance/today',
                'name' => 'api.attendance.today',
            ],
            [
                'method' => 'GET',
                'path' => '/api/admin/students',
                'name' => 'api.admin.students.index',
            ],
            [
                'method' => 'GET',
                'path' => '/api/admin/dosen',
                'name' => 'api.admin.dosen.index',
            ],
        ],
    ]);
})->name('api.index');

Route::middleware(['web', 'auth', 'no-cache', 'role:mahasiswa'])
    ->name('api.')
    ->group(function () {
        Route::post('/face-verify', [FaceVerificationController::class, 'verify'])
            ->name('face.verify');

        Route::get('/dosen/search', [DosenRequestController::class, 'search'])
            ->name('dosen.search');

        Route::apiResource('logbooks', LogbookApiController::class);

        Route::get('/laporans/{laporan}/download', [LaporanApiController::class, 'download'])
            ->name('laporans.download');
        Route::get('/laporans/{laporan}/preview', [LaporanApiController::class, 'preview'])
            ->name('laporans.preview');
        Route::get('/laporans', [LaporanApiController::class, 'index'])
            ->name('laporans.index');
        Route::post('/laporans', [LaporanApiController::class, 'store'])
            ->name('laporans.store');
        Route::get('/laporans/{laporan}', [LaporanApiController::class, 'show'])
            ->name('laporans.show');
        Route::post('/laporans/{laporan}', [LaporanApiController::class, 'update'])
            ->name('laporans.update');
        Route::delete('/laporans/{laporan}', [LaporanApiController::class, 'destroy'])
            ->name('laporans.destroy');

        Route::prefix('attendance')->name('attendance.')->group(function () {
            Route::get('/today', [AttendanceApiController::class, 'today'])->name('today');
            Route::get('/history', [AttendanceApiController::class, 'history'])->name('history');
            Route::post('/check-in', [AttendanceApiController::class, 'checkIn'])->name('check-in');
            Route::post('/check-out', [AttendanceApiController::class, 'checkOut'])->name('check-out');
        });
    });

Route::middleware(['web', 'auth', 'no-cache', 'role:admin,master'])
    ->prefix('admin')
    ->name('api.admin.')
    ->group(function () {
        Route::apiResource('students', AdminStudentApiController::class);
        Route::patch('/students/{student}/status', [AdminStudentApiController::class, 'updateStatus'])
            ->name('students.status');
        Route::post('/students/{student}/face-reset', [AdminStudentApiController::class, 'resetFace'])
            ->name('students.face-reset');

        Route::apiResource('dosen', DosenApiController::class);
    });

Route::middleware(['web', 'auth', 'no-cache', 'role:pembimbing'])
    ->prefix('pembimbing')
    ->name('api.pembimbing.')
    ->group(function () {
        Route::get('/students', [DosenApiController::class, 'myStudents'])->name('students.index');
        Route::get('/students/{student}', [DosenApiController::class, 'myStudentDetail'])->name('students.show');
    });
