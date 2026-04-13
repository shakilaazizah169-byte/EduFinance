<?php

namespace App\Console\Commands;

use App\Models\Activity;
use App\Models\User;
use Illuminate\Console\Command;

class SeedHistoricalActivities extends Command
{
    protected $signature = 'activities:seed-historical';
    protected $description = 'Seed historical activities from existing data';

    public function handle()
    {
        $this->info('Seeding historical activities...');
        
        // Seed untuk setiap user
        $users = User::all();
        
        foreach ($users as $user) {
            $this->seedUserActivities($user);
        }
        
        $this->info('Historical activities seeded successfully!');
        return 0;
    }
    
    private function seedUserActivities(User $user)
    {
        // Log account creation
        Activity::create([
            'user_id' => $user->id,
            'action' => 'account_created',
            'description' => 'User account was created',
            'ip_address' => '127.0.0.1',
            'user_agent' => 'System',
            'created_at' => $user->created_at,
            'metadata' => [
                'account_created_at' => $user->created_at
            ]
        ]);
        
        // Log jika ada update profile
        if ($user->updated_at->gt($user->created_at)) {
            Activity::create([
                'user_id' => $user->id,
                'action' => 'updated_profile',
                'description' => 'User profile was updated',
                'ip_address' => '127.0.0.1',
                'user_agent' => 'System',
                'created_at' => $user->updated_at,
                'metadata' => [
                    'last_updated' => $user->updated_at
                ]
            ]);
        }
        
        $this->info("Activities seeded for user: {$user->name}");
    }
}