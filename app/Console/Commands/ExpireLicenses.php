<?php

namespace App\Console\Commands;

use App\Models\License;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ExpireLicenses extends Command
{
    protected $signature   = 'licenses:expire';
    protected $description = 'Auto-expire lisensi yang sudah melewati end_date';

    public function handle(): int
    {
        $now = Carbon::now();

        // Update lisensi 'active' yang end_date-nya sudah lewat → expired
        $count = License::where('status', 'active')
            ->where('end_date', '<', $now)
            ->update(['status' => 'expired']);

        Log::info("licenses:expire — {$count} lisensi diubah ke expired.");
        $this->info("✅ {$count} lisensi berhasil di-expire.");

        return self::SUCCESS;
    }
}