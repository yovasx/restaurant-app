<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Services\Admin\DatabaseBackupService;

class AdminBackupController extends Controller
{
    protected string $backupPath;

    public function __construct()
    {
        $this->backupPath = storage_path('app/backups/database');
    }

    public function index()
    {
        $files = [];

        if (is_dir($this->backupPath)) {
            $items = array_diff(scandir($this->backupPath), ['.', '..']);
            foreach ($items as $item) {
                $fullPath = $this->backupPath . DIRECTORY_SEPARATOR . $item;
                if (is_file($fullPath)) {
                    $files[] = (object) [
                        'name' => $item,
                        'size' => filesize($fullPath),
                        'last_modified' => filemtime($fullPath),
                    ];
                }
            }
            usort($files, fn($a, $b) => $b->last_modified <=> $a->last_modified);
        }

        return view('admin.backups.index', compact('files'));
    }

    public function download(string $file)
    {
        $fullPath = $this->backupPath . DIRECTORY_SEPARATOR . basename($file);

        if (!file_exists($fullPath)) {
            abort(404, 'Backup no encontrado.');
        }

        app(\App\Services\Admin\AuditLogger::class)->log('backups', 'descargar_backup', 'Backup', null, "Backup descargado: {$file}");

        return response()->download($fullPath, $file);
    }

    public function generate(DatabaseBackupService $service)
    {
        try {
            $filename = $service->generate();
            app(\App\Services\Admin\AuditLogger::class)->log('backups', 'generar_backup', 'Backup', null, "Backup generado: {$filename}", null, null, ['archivo' => $filename]);
            return redirect()->route('admin.backups.index')
                ->with('success', "Backup generado exitosamente: {$filename}");
        } catch (\Exception $e) {
            app(\App\Services\Admin\AuditLogger::class)->log('backups', 'error_backup', null, null, 'Error al generar backup: ' . $e->getMessage());
            return redirect()->route('admin.backups.index')
                ->with('error', 'Error al generar backup: ' . $e->getMessage());
        }
    }
}
