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

        $data = $service->generate($validated);

        return Excel::download(
            new GlobalReportExport($data),
            'reporte_global_' . now()->format('Ymd_His') . '.xlsx'
        );
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

        $data = $service->generate($validated);

        $pdf = Pdf::loadView('admin.reportes.pdf', $data);
        $pdf->setPaper('a4', 'portrait');

        return $pdf->download('reporte_global_' . now()->format('Ymd_His') . '.pdf');
    }
}
