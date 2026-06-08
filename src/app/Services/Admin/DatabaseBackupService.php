<?php

namespace App\Services\Admin;

use Illuminate\Support\Facades\Log;

class DatabaseBackupService
{
    protected string $backupDir;

    protected string $host;

    protected string $port;

    protected string $database;

    protected string $username;

    protected string $password;

    public function __construct()
    {
        $this->backupDir = storage_path('app/backups/database');
        $this->host = config('database.connections.pgsql.host');
        $this->port = config('database.connections.pgsql.port');
        $this->database = config('database.connections.pgsql.database');
        $this->username = config('database.connections.pgsql.username');
        $this->password = config('database.connections.pgsql.password');
    }

    public function generate(): string
    {
        if (!is_dir($this->backupDir)) {
            mkdir($this->backupDir, 0777, true);
        }

        if (!is_writable($this->backupDir)) {
            throw new \RuntimeException(
                'La carpeta de backups no tiene permisos de escritura. '
                . 'Solicita al administrador del servidor que ejecute: chmod 777 ' . $this->backupDir
            );
        }

        $filename = 'laravel_db_' . now()->format('Ymd_His') . '.dump';
        $filepath = $this->backupDir . DIRECTORY_SEPARATOR . $filename;

        $escapedFilepath = escapeshellarg($filepath);

        $command = sprintf(
            'PGPASSWORD=%s pg_dump -h %s -p %s -U %s -d %s -Fc -f %s 2>&1',
            escapeshellarg($this->password),
            escapeshellarg($this->host),
            escapeshellarg((string) $this->port),
            escapeshellarg($this->username),
            escapeshellarg($this->database),
            $escapedFilepath
        );

        exec($command, $output, $exitCode);

        if ($exitCode !== 0) {
            $errorMsg = implode("\n", $output);
            Log::error('Backup failed: ' . $errorMsg);
            throw new \RuntimeException('Error al generar el backup: ' . $errorMsg);
        }

        return $filename;
    }
}
