<?php

namespace App\Exports\Restaurante;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class RestaurantReportExport implements WithMultipleSheets
{
    protected array $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function sheets(): array
    {
        return [
            $this->resumenSheet(),
            $this->tendenciasSheet(),
            $this->topPlatosSheet(),
            $this->scoreSheet(),
            $this->promocionesSheet(),
            $this->productosSheet(),
        ];
    }

    protected function resumenSheet(): object
    {
        $data = $this->data;
        $f = $data['filters'];
        $k = $data['kpis'];

        return new class($f, $k) implements FromArray, WithTitle, WithHeadings, WithStyles {
            protected array $filters;
            protected array $kpis;

            public function __construct(array $filters, array $kpis)
            {
                $this->filters = $filters;
                $this->kpis = $kpis;
            }

            public function title(): string { return 'Resumen Ejecutivo'; }

            public function headings(): array { return ['Métrica', 'Valor']; }

            public function array(): array
            {
                $f = $this->filters;
                $k = $this->kpis;

                $scopeLabel = $f['scope'] === 'todas_mis_sucursales'
                    ? 'Todas mis sucursales (' . $f['total_sucursales'] . ')'
                    : 'Sucursal activa';

                return [
                    ['Fecha de generación', now()->format('d/m/Y H:i')],
                    ['Desde', $f['from']->format('d/m/Y')],
                    ['Hasta', $f['to']->format('d/m/Y')],
                    ['Alcance', $scopeLabel],
                    ['', ''],
                    ['Platos Activos', $k['productos_activos']],
                    ['Platos Sin Stock', $k['productos_sin_stock']],
                    ['Promociones Activas', $k['promociones_activas']],
                    ['Visitas', $k['visitas']],
                    ['Reseñas', $k['resenas']],
                    ['Promedio Score', number_format($k['promedio_score'] ?? 0, 1)],
                ];
            }

            public function styles(Worksheet $sheet)
            {
                return [
                    1 => ['font' => ['bold' => true, 'size' => 12]],
                    5 => ['font' => ['bold' => true]],
                ];
            }
        };
    }

    protected function tendenciasSheet(): object
    {
        $data = $this->data;
        return new class($data) implements FromArray, WithTitle, WithHeadings, WithStyles {
            protected array $data;

            public function __construct(array $data) { $this->data = $data; }

            public function title(): string { return 'Tendencias'; }

            public function headings(): array { return ['Tipo', 'Fecha', 'Cantidad']; }

            public function array(): array
            {
                $rows = [];

                $rows[] = ['VISITAS POR DÍA', '', ''];
                foreach ($this->data['series']['visitas_por_dia'] as $v) {
                    $rows[] = ['', (new \Carbon\Carbon($v->fecha))->toDateString(), $v->total];
                }

                $rows[] = ['RESEÑAS POR DÍA', '', ''];
                foreach ($this->data['series']['resenas_por_dia'] as $v) {
                    $rows[] = ['', (new \Carbon\Carbon($v->fecha))->toDateString(), $v->total];
                }

                $rows[] = ['PROMEDIO SCORE POR DÍA', '', ''];
                foreach ($this->data['series']['promedio_score_por_dia'] as $v) {
                    $rows[] = ['', (new \Carbon\Carbon($v->fecha))->toDateString(), $v->total];
                }

                return $rows;
            }

            public function styles(Worksheet $sheet)
            {
                $styles = [];
                $row = 1;
                foreach (['VISITAS POR DÍA', 'RESEÑAS POR DÍA', 'PROMEDIO SCORE POR DÍA'] as $section) {
                    $styles[$row] = ['font' => ['bold' => true, 'size' => 11]];
                    $row += 1 + count(match ($section) {
                        'VISITAS POR DÍA' => $this->data['series']['visitas_por_dia'],
                        'RESEÑAS POR DÍA' => $this->data['series']['resenas_por_dia'],
                        'PROMEDIO SCORE POR DÍA' => $this->data['series']['promedio_score_por_dia'],
                    });
                }
                return $styles;
            }
        };
    }

    protected function topPlatosSheet(): object
    {
        $data = $this->data;
        return new class($data) implements FromArray, WithTitle, WithHeadings, WithStyles {
            protected array $data;

            public function __construct(array $data) { $this->data = $data; }

            public function title(): string { return 'Top Platos'; }

            public function headings(): array { return ['Tipo', 'Posición', 'Nombre', 'Cantidad / Score']; }

            public function array(): array
            {
                $rows = [];

                $rows[] = ['MÁS RESEÑADOS', '', '', ''];
                foreach ($this->data['tables']['top_platos_resenas'] as $i => $v) {
                    $rows[] = ['', $i + 1, $v->nombre, $v->total];
                }

                $rows[] = ['MEJOR CALIFICADOS', '', '', ''];
                foreach ($this->data['tables']['top_platos_score'] as $i => $v) {
                    $rows[] = ['', $i + 1, $v->nombre, number_format($v->promedio, 1) . ' (' . $v->total_resenas . ')'];
                }

                return $rows;
            }

            public function styles(Worksheet $sheet)
            {
                $topRow = 1;
                $botRow = $topRow + 1 + count($this->data['tables']['top_platos_resenas']) + 1;
                return [
                    $topRow => ['font' => ['bold' => true, 'size' => 11]],
                    $botRow => ['font' => ['bold' => true, 'size' => 11]],
                ];
            }
        };
    }

    protected function scoreSheet(): object
    {
        $data = $this->data;
        return new class($data) implements FromArray, WithTitle, WithHeadings, WithStyles {
            protected array $data;

            public function __construct(array $data) { $this->data = $data; }

            public function title(): string { return 'Distribución Score'; }

            public function headings(): array { return ['Score', 'Cantidad', 'Porcentaje']; }

            public function array(): array
            {
                $dist = $this->data['breakdowns']['score_distribution'];
                $total = array_sum($dist);

                return array_map(fn($score, $count) => [
                    "{$score} estrella" . ($score > 1 ? 's' : ''),
                    $count,
                    $total > 0 ? round($count / $total * 100, 1) . '%' : '0%',
                ], array_keys($dist), $dist);
            }

            public function styles(Worksheet $sheet)
            {
                return [1 => ['font' => ['bold' => true]]];
            }
        };
    }

    protected function promocionesSheet(): object
    {
        $data = $this->data;
        return new class($data) implements FromArray, WithTitle, WithHeadings, WithStyles {
            protected array $data;

            public function __construct(array $data) { $this->data = $data; }

            public function title(): string { return 'Promociones'; }

            public function headings(): array { return ['Estado', 'Nombre', 'Fecha Fin']; }

            public function array(): array
            {
                $rows = [];
                $p = $this->data['breakdowns']['promociones_por_estado'];

                $rows[] = ['ACTIVAS (' . count($p['activas']) . ')', '', ''];
                foreach ($p['activas'] as $item) {
                    $rows[] = ['Activa', $item->nombre, $item->fecha_fin ?? '(sin fecha)'];
                }

                $rows[] = ['INACTIVAS (' . count($p['inactivas']) . ')', '', ''];
                foreach ($p['inactivas'] as $item) {
                    $rows[] = ['Inactiva', $item->nombre, $item->fecha_fin ?? '-'];
                }

                $rows[] = ['VENCIDAS (' . count($p['vencidas']) . ')', '', ''];
                foreach ($p['vencidas'] as $item) {
                    $rows[] = ['Vencida', $item->nombre, $item->fecha_fin ?? '-'];
                }

                return $rows;
            }

            public function styles(Worksheet $sheet)
            {
                $styles = [];
                $row = 1;
                $p = $this->data['breakdowns']['promociones_por_estado'];

                $styles[$row] = ['font' => ['bold' => true]];
                $row += 1 + count($p['activas']);

                $styles[$row] = ['font' => ['bold' => true]];
                $row += 1 + count($p['inactivas']);

                $styles[$row] = ['font' => ['bold' => true]];

                return $styles;
            }
        };
    }

    protected function productosSheet(): object
    {
        $data = $this->data;
        return new class($data) implements FromArray, WithTitle, WithHeadings, WithStyles {
            protected array $data;

            public function __construct(array $data) { $this->data = $data; }

            public function title(): string { return 'Productos'; }

            public function headings(): array { return ['Estado', 'Cantidad / Nombre']; }

            public function array(): array
            {
                $rows = [];
                $p = $this->data['breakdowns']['productos_por_estado'];

                $rows[] = ['RESUMEN', ''];
                $rows[] = ['Activos', $p['activos']];
                $rows[] = ['Inactivos', $p['inactivos']];
                $rows[] = ['Sin Stock', $p['sin_stock']];

                $rows[] = ['', ''];
                $rows[] = ['PRODUCTOS SIN STOCK', ''];

                if (count($this->data['tables']['productos_sin_stock']) > 0) {
                    foreach ($this->data['tables']['productos_sin_stock'] as $prod) {
                        $rows[] = ['', $prod['nombre']];
                    }
                } else {
                    $rows[] = ['', 'Ninguno'];
                }

                return $rows;
            }

            public function styles(Worksheet $sheet)
            {
                $styles = [];
                $resumenRow = 1;
                $styles[$resumenRow] = ['font' => ['bold' => true]];

                $sinStockRow = $resumenRow + 5;
                $styles[$sinStockRow] = ['font' => ['bold' => true]];

                return $styles;
            }
        };
    }
}
