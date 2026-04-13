<?php

namespace App\Console\Commands;

use App\Models\Activity;
use Illuminate\Console\Command;

class CleanupViewedPages extends Command
{
    protected $signature = 'activities:cleanup-viewed-pages {--days=7 : Delete viewed_page activities older than X days}';
    protected $description = 'Cleanup old viewed_page activities';

    public function handle()
    {
        $days = $this->option('days');
        $cutoffDate = now()->subDays($days);
        
        $this->info("Cleaning up viewed_page activities older than {$days} days...");
        
        // Delete old viewed_page activities
        $deleted = Activity::where('action', 'like', '%viewed_page%')
                          ->where('created_at', '<', $cutoffDate)
                          ->delete();
        
        $this->info("Deleted {$deleted} viewed_page activities.");
        
        return 0;
    }
}