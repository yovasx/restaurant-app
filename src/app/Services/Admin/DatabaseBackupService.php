<?php

namespace App\Services\Admin;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

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

    public function generate(): array
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

        $timestamp = now()->format('Ymd_His');
        $filename = "laravel_db_{$timestamp}";
        $dumpPath = $this->backupDir . DIRECTORY_SEPARATOR . $filename . '.dump';
        $sqlPath = $this->backupDir . DIRECTORY_SEPARATOR . $filename . '.sql.gz';

        $this->runPgDump("-Fc -f " . escapeshellarg($dumpPath), $dumpPath);
        $this->runPgDump("-Fp 2>&1 | gzip > " . escapeshellarg($sqlPath), $sqlPath, true);

        if (config('backups.cloud_enabled')) {
            $this->uploadToCloud($filename, $dumpPath, $sqlPath);
        }

        $this->cleanOldBackups();

        return [
            'dump' => $filename . '.dump',
            'sql'  => $filename . '.sql.gz',
            'cloud' => config('backups.cloud_enabled'),
        ];
    }

    protected function runPgDump(string $extraArgs, string $filepath, bool $usePipe = false): void
    {
        if ($usePipe) {
            $command = sprintf(
                'PGPASSWORD=%s pg_dump -h %s -p %s -U %s -d %s %s',
                escapeshellarg($this->password),
                escapeshellarg($this->host),
                escapeshellarg((string) $this->port),
                escapeshellarg($this->username),
                escapeshellarg($this->database),
                $extraArgs
            );
        } else {
            $command = sprintf(
                'PGPASSWORD=%s pg_dump -h %s -p %s -U %s -d %s %s',
                escapeshellarg($this->password),
                escapeshellarg($this->host),
                escapeshellarg((string) $this->port),
                escapeshellarg($this->username),
                escapeshellarg($this->database),
                $extraArgs
            );
        }

        exec($command, $output, $exitCode);

        if ($exitCode !== 0) {
            $errorMsg = implode("\n", $output);
            Log::error('Backup failed: ' . $errorMsg);
            throw new \RuntimeException('Error al generar el backup: ' . $errorMsg);
        }

        if (!file_exists($filepath) || filesize($filepath) === 0) {
            throw new \RuntimeException('El archivo de backup no se creó o está vacío.');
        }
    }

    protected function uploadToCloud(string $filename, string $dumpPath, string $sqlPath): void
    {
        $disk = config('backups.cloud_disk', 'r2');
        $prefix = config('backups.cloud_prefix', 'dev');
        $datePath = now()->format('Y/m');

        $paths = [
            "{$prefix}/database-backups/{$datePath}/{$filename}.dump"  => $dumpPath,
            "{$prefix}/database-backups/{$datePath}/{$filename}.sql.gz" => $sqlPath,
        ];

        foreach ($paths as $remote => $local) {
            try {
                $stream = fopen($local, 'r');
                Storage::disk($disk)->writeStream($remote, $stream);
                fclose($stream);
            } catch (\Exception $e) {
                Log::error("Backup upload failed for {$remote}: " . $e->getMessage());
                throw new \RuntimeException("Error al subir backup a la nube: {$e->getMessage()}");
            }
        }
    }

    protected function cleanOldBackups(): void
    {
        $retention = config('backups.local_retention_days', 7);
        $cutoff = now()->subDays($retention)->timestamp;

        foreach (glob($this->backupDir . DIRECTORY_SEPARATOR . '*') as $file) {
            if (is_file($file) && filemtime($file) < $cutoff) {
                unlink($file);
            }
        }
    }
}
