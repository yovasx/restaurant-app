<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class RefreshMaterializedViews extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'materialized:refresh';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Refresh materialized views used by the application';

    public function handle()
    {
        $this->info('Refreshing materialized view: restaurantes_stats');
        DB::statement('REFRESH MATERIALIZED VIEW restaurantes_stats');
        $this->info('Done.');
        return 0;
    }
}
