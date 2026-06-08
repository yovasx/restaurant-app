<?php

namespace App\Exports\Admin;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class GlobalReportExport implements WithMultipleSheets
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
            $this->rankingVisitasSheet(),
            $this->rankingRatingSheet(),
            $this->distribucionScoreSheet(),
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

                $restauranteLabel = $f['restauranteId']
                    ? "ID {$f['restauranteId']}"
                    : 'Todos';

                $estadoLabel = $f['estadoRestaurante'] ?? 'Todos';
                $scoreLabel  = $f['scoreFilter'] ? "{$f['scoreFilter']} estrellas" : 'Todos';

                return [
                    ['Fecha de generación', now()->format('d/m/Y H:i')],
                    ['Desde', $f['from']->format('d/m/Y')],
                    ['Hasta', $f['to']->format('d/m/Y')],
                    ['Restaurante', $restauranteLabel],
                    ['Estado restaurante', $estadoLabel],
                    ['Score', $scoreLabel],
                    ['', ''],
                    ['Cuentas de Restaurante', $k['cuentasRestaurante']],
                    ['Sucursales Activas', $k['sucursalesActivas']],
                    ['Comensales Totales', $k['totalComensales']],
                    ['Visitas en el Rango', $k['visitasRango']],
                    ['Reseñas en el Rango', $k['resenasRango']],
                    ['Promedio de Score', number_format($k['promedioScore'] ?? 0, 2)],
                    ['Promociones Activas', $k['promocionesActivas']],
                ];
            }

            public function styles(Worksheet $sheet)
            {
                return [
                    1 => ['font' => ['bold' => true, 'size' => 12]],
                    7 => ['font' => ['bold' => true]],
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

                $rows[] = ['ALTAS RESTAURANTES', '', ''];
                foreach ($this->data['series']['altas_restaurantes'] as $v) {
                    $rows[] = ['', (new \Carbon\Carbon($v->fecha))->toDateString(), $v->total];
                }

                $rows[] = ['ALTAS COMENSALES', '', ''];
                foreach ($this->data['series']['altas_comensales'] as $v) {
                    $rows[] = ['', (new \Carbon\Carbon($v->fecha))->toDateString(), $v->total];
                }

                $rows[] = ['VISITAS POR DÍA', '', ''];
                foreach ($this->data['series']['visitas_por_dia'] as $v) {
                    $rows[] = ['', (new \Carbon\Carbon($v->fecha))->toDateString(), $v->total];
                }

                $rows[] = ['RESEÑAS POR DÍA', '', ''];
                foreach ($this->data['series']['resenas_por_dia'] as $v) {
                    $rows[] = ['', (new \Carbon\Carbon($v->fecha))->toDateString(), $v->total];
                }

                return $rows;
            }

            public function styles(Worksheet $sheet)
            {
                $styles = [];
                $row = 1;
                foreach (['ALTAS RESTAURANTES', 'ALTAS COMENSALES', 'VISITAS POR DÍA', 'RESEÑAS POR DÍA'] as $section) {
                    $styles[$row] = ['font' => ['bold' => true, 'size' => 11]];
                    $row += 1 + count(match ($section) {
                        'ALTAS RESTAURANTES' => $this->data['series']['altas_restaurantes'],
                        'ALTAS COMENSALES' => $this->data['series']['altas_comensales'],
                        'VISITAS POR DÍA' => $this->data['series']['visitas_por_dia'],
                        'RESEÑAS POR DÍA' => $this->data['series']['resenas_por_dia'],
                    });
                }
                return $styles;
            }
        };
    }

    protected function rankingVisitasSheet(): object
    {
        $data = $this->data;
        return new class($data) implements FromArray, WithTitle, WithHeadings, WithStyles {
            protected array $data;

            public function __construct(array $data) { $this->data = $data; }

            public function title(): string { return 'Ranking Visitas'; }

            public function headings(): array { return ['Posición', 'Restaurante', 'Visitas']; }

            public function array(): array
            {
                return array_map(
                    fn($v, $i) => [$i + 1, $v->nombre, $v->total],
                    $this->data['tables']['top_restaurantes_visitas'],
                    array_keys($this->data['tables']['top_restaurantes_visitas'])
                );
            }

            public function styles(Worksheet $sheet)
            {
                return [1 => ['font' => ['bold' => true]]];
            }
        };
    }

    protected function rankingRatingSheet(): object
    {
        $data = $this->data;
        return new class($data) implements FromArray, WithTitle, WithHeadings, WithStyles {
            protected array $data;

            public function __construct(array $data) { $this->data = $data; }

            public function title(): string { return 'Ranking Rating'; }

            public function headings(): array { return ['Tipo', 'Posición', 'Restaurante', 'Promedio', 'Reseñas']; }

            public function array(): array
            {
                $rows = [];

                $rows[] = ['MEJORES CALIFICACIONES', '', '', '', ''];
                $top = $this->data['tables']['top_restaurantes_rating'];
                foreach ($top as $i => $v) {
                    $rows[] = ['', $i + 1, $v->nombre, number_format($v->promedio, 2), $v->total_resenas];
                }

                $rows[] = ['PEORES CALIFICACIONES', '', '', '', ''];
                $bottom = $this->data['tables']['bottom_restaurantes_rating'];
                foreach ($bottom as $i => $v) {
                    $rows[] = ['', $i + 1, $v->nombre, number_format($v->promedio, 2), $v->total_resenas];
                }

                return $rows;
            }

            public function styles(Worksheet $sheet)
            {
                $topRow = 1;
                $bottomRow = $topRow + 1 + count($this->data['tables']['top_restaurantes_rating']) + 1;
                return [
                    $topRow => ['font' => ['bold' => true, 'size' => 11]],
                    $bottomRow => ['font' => ['bold' => true, 'size' => 11]],
                ];
            }
        };
    }

    protected function distribucionScoreSheet(): object
    {
        $data = $this->data;
        return new class($data) implements FromArray, WithTitle, WithHeadings, WithStyles {
            protected array $data;

            public function __construct(array $data) { $this->data = $data; }

            public function title(): string { return 'Distribución Score'; }

            public function headings(): array { return ['Score', 'Cantidad', 'Porcentaje']; }

            public function array(): array
            {
                $dist = $this->data['tables']['resenas_por_score'];
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

                $rows[] = ['ACTIVAS (' . count($this->data['tables']['promociones_por_estado']['activas']) . ')', '', ''];

                foreach ($this->data['tables']['promociones_por_estado']['activas'] as $p) {
                    $rows[] = ['Activa', $p->nombre, $p->fecha_fin ?? '(sin fecha)'];
                }

                $rows[] = ['INACTIVAS (' . count($this->data['tables']['promociones_por_estado']['inactivas']) . ')', '', ''];

                foreach ($this->data['tables']['promociones_por_estado']['inactivas'] as $p) {
                    $rows[] = ['Inactiva', $p->nombre, $p->fecha_fin ?? '-'];
                }

                $rows[] = ['VENCIDAS (' . count($this->data['tables']['promociones_por_estado']['vencidas']) . ')', '', ''];

                foreach ($this->data['tables']['promociones_por_estado']['vencidas'] as $p) {
                    $rows[] = ['Vencida', $p->nombre, $p->fecha_fin ?? '-'];
                }

                return $rows;
            }

            public function styles(Worksheet $sheet)
            {
                $styles = [];
                $row = 1;
                $p = $this->data['tables']['promociones_por_estado'];

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

            public function headings(): array { return ['Estado', 'Cantidad / Nombre', 'Restaurante']; }

            public function array(): array
            {
                $rows = [];
                $p = $this->data['tables']['productos_por_estado'];

                $rows[] = ['RESUMEN', '', ''];
                $rows[] = ['Activos', $p['activos'], ''];
                $rows[] = ['Inactivos', $p['inactivos'], ''];
                $rows[] = ['Sin Stock', $p['sin_stock'], ''];

                $rows[] = ['', '', ''];
                $rows[] = ['PRODUCTOS SIN STOCK', '', ''];

                if (count($this->data['tables']['productos_sin_stock']) > 0) {
                    foreach ($this->data['tables']['productos_sin_stock'] as $prod) {
                        $rows[] = ['', $prod->nombre, $prod->restaurante];
                    }
                } else {
                    $rows[] = ['', 'Ninguno', ''];
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
