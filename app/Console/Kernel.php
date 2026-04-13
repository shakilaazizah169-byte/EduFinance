<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule): void
    {
        // Cek pending payment tiap menit (polling pengganti webhook)
        $schedule->command('payments:check-status')
                 ->everyMinute()
                 ->withoutOverlapping()
                 ->runInBackground()
                 ->appendOutputTo(storage_path('logs/payment-check.log'));

        // Expire lisensi yang sudah habis masa berlakunya, tiap jam
        $schedule->command('licenses:expire')
                 ->dailyAt('00:05')
                 ->appendOutputTo(storage_path('logs/license-expire.log'));
        
        $schedule->command('licenses:auto-expire')->hourly();
    }

    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');
        require base_path('routes/console.php');
    }
}