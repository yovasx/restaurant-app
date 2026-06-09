<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected $commands = [
        \App\Console\Commands\RefreshMaterializedViews::class,
    ];

    protected function schedule(Schedule $schedule)
    {
        // Refresh materialized view hourly
        $schedule->command('materialized:refresh')->hourly();

        // Database backup — nightly
        if (config('backups.schedule_enabled')) {
            $schedule->command('backup:database')
                ->dailyAt(config('backups.schedule_time', '02:00'))
                ->withoutOverlapping();
        }
    }

    protected function commands()
    {
        // load commands if any are in routes/console.php or default locations
        require base_path('routes/console.php');
    }
}
