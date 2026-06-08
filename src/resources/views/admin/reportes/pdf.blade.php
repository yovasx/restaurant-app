<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8"/>
    <title>Reporte Global - GastroGuía</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; color: #1c1917; font-size: 12px; line-height: 1.5; }
        .header { text-align: center; margin-bottom: 24px; padding-bottom: 16px; border-bottom: 2px solid #c0392b; }
        .header h1 { font-size: 24px; font-weight: 800; margin: 0; color: #c0392b; }
        .header p { font-size: 11px; color: #78716c; margin: 4px 0 0; }
        .section { margin-bottom: 20px; }
        .section h2 { font-size: 14px; font-weight: 700; color: #c0392b; border-bottom: 1px solid #e7e5e4; padding-bottom: 4px; margin-bottom: 8px; }
        .kpi-grid { display: flex; flex-wrap: wrap; gap: 8px; margin-bottom: 16px; }
        .kpi-card { flex: 1; min-width: 100px; background: #f5f5f4; border-radius: 8px; padding: 10px; text-align: center; }
        .kpi-card .value { font-size: 20px; font-weight: 800; color: #1c1917; }
        .kpi-card .label { font-size: 9px; text-transform: uppercase; color: #78716c; letter-spacing: 0.1em; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 12px; }
        th { background: #f5f5f4; text-align: left; padding: 6px 8px; font-size: 10px; text-transform: uppercase; color: #78716c; }
        td { padding: 5px 8px; border-bottom: 1px solid #e7e5e4; font-size: 11px; }
        .text-right { text-align: right; }
        .text-red { color: #dc2626; }
        .text-green { color: #16a34a; }
        .footer { text-align: center; font-size: 9px; color: #a8a29e; margin-top: 24px; border-top: 1px solid #e7e5e4; padding-top: 8px; }
        .col-2 { display: flex; gap: 16px; }
        .col-2 > div { flex: 1; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Reporte Global</h1>
        <p>{{ $filters['from']->format('d/m/Y') }} — {{ $filters['to']->format('d/m/Y') }} | Generado {{ now()->format('d/m/Y H:i') }}</p>
    </div>

    <div class="section">
        <h2>Resumen</h2>
        <div class="kpi-grid">
            <div class="kpi-card">
                <div class="value">{{ $kpis['cuentasRestaurante'] }}</div>
                <div class="label">Cuentas</div>
            </div>
            <div class="kpi-card">
                <div class="value">{{ $kpis['sucursalesActivas'] }}</div>
                <div class="label">Suc. Activas</div>
            </div>
            <div class="kpi-card">
                <div class="value">{{ $kpis['totalComensales'] }}</div>
                <div class="label">Comensales</div>
            </div>
            <div class="kpi-card">
                <div class="value">{{ $kpis['visitasRango'] }}</div>
                <div class="label">Visitas</div>
            </div>
            <div class="kpi-card">
                <div class="value">{{ $kpis['resenasRango'] }}</div>
                <div class="label">Reseñas</div>
            </div>
            <div class="kpi-card">
                <div class="value">{{ number_format($kpis['promedioScore'] ?? 0, 1) }}</div>
                <div class="label">Score Prom.</div>
            </div>
            <div class="kpi-card">
                <div class="value">{{ $kpis['promocionesActivas'] }}</div>
                <div class="label">Promociones</div>
            </div>
        </div>
    </div>

    <div class="col-2">
        <div class="section">
            <h2>Top Visitados</h2>
            <table>
                <tr><th>#</th><th>Restaurante</th><th class="text-right">Visitas</th></tr>
                @foreach($tables['top_restaurantes_visitas'] as $i => $item)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $item->nombre }}</td>
                    <td class="text-right">{{ $item->total }}</td>
                </tr>
                @endforeach
            </table>
        </div>

        <div class="section">
            <h2>Mejor Calificación</h2>
            <table>
                <tr><th>#</th><th>Restaurante</th><th class="text-right">Score</th></tr>
                @foreach($tables['top_restaurantes_rating'] as $i => $item)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $item->nombre }}</td>
                    <td class="text-right text-green">{{ number_format($item->promedio, 1) }}</td>
                </tr>
                @endforeach
            </table>
        </div>
    </div>

    <div class="col-2">
        <div class="section">
            <h2>Promociones</h2>
            <table>
                <tr><th>Estado</th><th class="text-right">Cantidad</th></tr>
                <tr><td>Activas</td><td class="text-right text-green">{{ count($tables['promociones_por_estado']['activas']) }}</td></tr>
                <tr><td>Inactivas</td><td class="text-right">{{ count($tables['promociones_por_estado']['inactivas']) }}</td></tr>
                <tr><td>Vencidas</td><td class="text-right text-red">{{ count($tables['promociones_por_estado']['vencidas']) }}</td></tr>
            </table>
        </div>

        <div class="section">
            <h2>Productos</h2>
            <table>
                <tr><th>Estado</th><th class="text-right">Cantidad</th></tr>
                <tr><td>Activos</td><td class="text-right text-green">{{ $tables['productos_por_estado']['activos'] }}</td></tr>
                <tr><td>Inactivos</td><td class="text-right">{{ $tables['productos_por_estado']['inactivos'] }}</td></tr>
                <tr><td>Sin Stock</td><td class="text-right text-red">{{ $tables['productos_por_estado']['sin_stock'] }}</td></tr>
            </table>
        </div>
    </div>

    <div class="footer">
        GastroGuía — Reporte generado automáticamente el {{ now()->format('d/m/Y \a \l\a\s H:i') }}
    </div>
</body>
</html>
