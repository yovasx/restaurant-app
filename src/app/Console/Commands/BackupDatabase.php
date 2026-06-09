<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\Admin\DatabaseBackupService;

class BackupDatabase extends Command
{
    protected $signature = 'backup:database';

    protected $description = 'Generate .dump and .sql.gz backups and upload to cloud if configured';

    public function handle(DatabaseBackupService $service)
    {
        $this->info('Starting database backup...');

        try {
            $result = $service->generate();

            $this->info("Backup generated:");
            $this->line("  - {$result['dump']}");
            $this->line("  - {$result['sql']}");

            if ($result['cloud']) {
                $this->info('Uploaded to cloud storage.');
            }

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('Backup failed: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
