<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// JIKA ingin dijalankan jam 12 MALAM lewat 14 menit (setelah hari berganti)
Schedule::command('absensi:proses-harian')
    ->dailyAt('02:17')
    ->withoutOverlapping();
