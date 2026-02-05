<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schedule;


Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::call(function () {
    DB::table('authentication_logs')
        ->where('login_at', '<', now()->subMonths(2)) // Kita set 2 bulan biar aman
        ->delete();
})->daily();
