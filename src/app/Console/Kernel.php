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
        // Refresh materialized view hourly (adjust as needed)
        $schedule->command('materialized:refresh')->hourly();
    }

    protected function commands()
    {
        // load commands if any are in routes/console.php or default locations
        require base_path('routes/console.php');
    }
}
