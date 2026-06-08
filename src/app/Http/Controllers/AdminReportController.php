<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\Admin\GlobalReportService;
use App\Exports\Admin\GlobalReportExport;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;

class AdminReportController extends Controller
{
    public function index(Request $request, GlobalReportService $service)
    {
        $validated = $request->validate([
            'from' => 'nullable|date',
            'to' => 'nullable|date|after_or_equal:from',
            'restaurante_id' => 'nullable|integer|exists:restaurantes,id',
            'estado_restaurante' => 'nullable|string|in:activo,inactivo,baneado',
            'score' => 'nullable|integer|between:1,5',
        ]);

        $data = $service->generate($validated);

        $restaurantes = \App\Models\Restaurante::select('id', 'nombre', 'estado')
            ->orderBy('nombre')
            ->get();

        return view('admin.reportes.index', array_merge($data, [
            'restaurantes' => $restaurantes,
        ]));
    }

    public function exportExcel(Request $request, GlobalReportService $service)
    {
        $validated = $request->validate([
            'from' => 'nullable|date',
            'to' => 'nullable|date|after_or_equal:from',
            'restaurante_id' => 'nullable|integer|exists:restaurantes,id',
            'estado_restaurante' => 'nullable|string|in:activo,inactivo,baneado',
            'score' => 'nullable|integer|between:1,5',
        ]);

        config(['excel.temporary_files.local_path' => '/tmp/laravel-excel']);

        try {
            $data = $service->generate($validated);

            $filename = 'reporte_global_' . now()->format('Ymd_His') . '.xlsx';
            app(\App\Services\Admin\AuditLogger::class)->log('reportes', 'exportar_excel', 'Reporte', null, 'Reporte global exportado a Excel', null, null, ['filtros' => $validated, 'archivo' => $filename]);

            return Excel::download(
                new GlobalReportExport($data),
                $filename
            );
        } catch (\Exception $e) {
            app(\App\Services\Admin\AuditLogger::class)->log('reportes', 'error_excel', null, null, 'Error al exportar Excel: ' . $e->getMessage());
            return redirect()->route('admin.reportes.index')
                ->with('error', 'Error al exportar Excel: ' . $e->getMessage());
        }
    }

    public function exportPdf(Request $request, GlobalReportService $service)
    {
        $validated = $request->validate([
            'from' => 'nullable|date',
            'to' => 'nullable|date|after_or_equal:from',
            'restaurante_id' => 'nullable|integer|exists:restaurantes,id',
            'estado_restaurante' => 'nullable|string|in:activo,inactivo,baneado',
            'score' => 'nullable|integer|between:1,5',
        ]);

        try {
            $data = $service->generate($validated);

            $filename = 'reporte_global_' . now()->format('Ymd_His') . '.pdf';
            app(\App\Services\Admin\AuditLogger::class)->log('reportes', 'exportar_pdf', 'Reporte', null, 'Reporte global exportado a PDF', null, null, ['filtros' => $validated, 'archivo' => $filename]);

            $pdf = Pdf::loadView('admin.reportes.pdf', $data);
            $pdf->setPaper('a4', 'portrait');

            return $pdf->download($filename);
        } catch (\Exception $e) {
            app(\App\Services\Admin\AuditLogger::class)->log('reportes', 'error_pdf', null, null, 'Error al exportar PDF: ' . $e->getMessage());
            return redirect()->route('admin.reportes.index')
                ->with('error', 'Error al exportar PDF: ' . $e->getMessage());
        }
    }
}
