<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

// Schedule::call(function () {
//     \Log::info('Hostinger cron is running every minute');
// })->everyMinute();

Schedule::command('app:check-messages')
    ->everyMinute()
    ->withoutOverlapping()
    ->appendOutputTo(storage_path('logs/sms-cron.log'));

Schedule::command('app:send-pharmacy-ads')
    ->dailyAt('10:00')
    ->withoutOverlapping()
    ->appendOutputTo(storage_path('logs/pharmacy-ads-cron.log'));
