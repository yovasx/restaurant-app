<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\Restaurante\RestaurantReportService;
use App\Exports\Restaurante\RestaurantReportExport;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;

class RestaurantReportController extends Controller
{
    public function index(Request $request, RestaurantReportService $service)
    {
        $usuario = Auth::guard('restaurante')->user();
        $validated = $this->resolveFilters($request, $usuario);
        $data = $service->generate($usuario->id, $validated);

        $sucursales = $usuario->restaurantes()
            ->select('id', 'nombre', 'estado')
            ->orderBy('nombre')
            ->get();

        return view('restaurante.reportes.index', array_merge($data, [
            'sucursales' => $sucursales,
        ]));
    }

    public function exportExcel(Request $request, RestaurantReportService $service)
    {
        $usuario = Auth::guard('restaurante')->user();
        $validated = $this->resolveFilters($request, $usuario);

        config(['excel.temporary_files.local_path' => '/tmp/laravel-excel']);

        try {
            $data = $service->generate($usuario->id, $validated);
            $filename = 'reporte_local_' . now()->format('Ymd_His') . '.xlsx';

            app(\App\Services\Admin\AuditLogger::class)->log('restaurante/reportes', 'exportar_excel', 'Reporte', null, 'Reporte de restaurante exportado a Excel', null, null, ['filtros' => $validated, 'archivo' => $filename]);

            return Excel::download(new RestaurantReportExport($data), $filename);
        } catch (\Exception $e) {
            app(\App\Services\Admin\AuditLogger::class)->log('restaurante/reportes', 'error_excel', null, null, 'Error al exportar Excel: ' . $e->getMessage());
            return redirect()->route('restaurante.reportes.index')->with('error', 'Error al exportar Excel: ' . $e->getMessage());
        }
    }

    public function exportPdf(Request $request, RestaurantReportService $service)
    {
        $usuario = Auth::guard('restaurante')->user();
        $validated = $this->resolveFilters($request, $usuario);

        try {
            $data = $service->generate($usuario->id, $validated);
            $filename = 'reporte_local_' . now()->format('Ymd_His') . '.pdf';

            app(\App\Services\Admin\AuditLogger::class)->log('restaurante/reportes', 'exportar_pdf', 'Reporte', null, 'Reporte de restaurante exportado a PDF', null, null, ['filtros' => $validated, 'archivo' => $filename]);

            $pdf = Pdf::loadView('restaurante.reportes.pdf', $data);
            $pdf->setPaper('a4', 'portrait');

            return $pdf->download($filename);
        } catch (\Exception $e) {
            app(\App\Services\Admin\AuditLogger::class)->log('restaurante/reportes', 'error_pdf', null, null, 'Error al exportar PDF: ' . $e->getMessage());
            return redirect()->route('restaurante.reportes.index')->with('error', 'Error al exportar PDF: ' . $e->getMessage());
        }
    }

    private function resolveFilters(Request $request, $usuario): array
    {
        $validated = $request->validate([
            'from' => 'nullable|date',
            'to' => 'nullable|date|after_or_equal:from',
            'scope' => 'nullable|string|in:sucursal_activa,todas_mis_sucursales',
            'restaurante_id' => 'nullable|integer',
        ]);

        $validated['scope'] ??= 'sucursal_activa';

        if ($validated['scope'] === 'sucursal_activa' && empty($validated['restaurante_id'])) {
            $sucursalId = session('restaurante_sucursal_id');
            if ($sucursalId) {
                $sucursal = $usuario->restaurantes()->where('id', $sucursalId)->first();
                if ($sucursal) {
                    $validated['restaurante_id'] = $sucursal->id;
                }
            }
            if (empty($validated['restaurante_id'])) {
                $principal = $usuario->restaurantes()->where('es_principal', true)->first();
                $validated['restaurante_id'] = $principal?->id ?? $usuario->restaurantes()->first()?->id;
            }
        }

        if (!empty($validated['restaurante_id'])) {
            $pertenece = $usuario->restaurantes()->where('id', $validated['restaurante_id'])->exists();
            if (!$pertenece) {
                $validated['restaurante_id'] = null;
            }
        }

        return $validated;
    }
}
