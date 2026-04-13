<?php

namespace App\Console\Commands;

use App\Models\Activity;
use Illuminate\Console\Command;

class CleanupOldActivities extends Command
{
    protected $signature = 'activities:cleanup {--days=30 : Delete activities older than X days}';
    protected $description = 'Cleanup old activities to prevent database bloat';

    public function handle()
    {
        $days = $this->option('days');
        $cutoffDate = now()->subDays($days);
        
        $this->info("Cleaning up activities older than {$days} days (before {$cutoffDate->format('Y-m-d')})...");
        
        // Delete old activities
        $deleted = Activity::where('created_at', '<', $cutoffDate)->delete();
        
        $this->info("Deleted {$deleted} old activities.");
        
        // Optimize table
        \DB::statement('OPTIMIZE TABLE activities');
        $this->info('Table optimized.');
        
        return 0;
    }
}