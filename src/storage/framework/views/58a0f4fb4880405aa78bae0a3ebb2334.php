<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8"/>
    <title>Reporte - GastroGuía</title>
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
        <h1>Reporte</h1>
        <p><?php echo e($filters['from']->format('d/m/Y')); ?> — <?php echo e($filters['to']->format('d/m/Y')); ?> | Generado <?php echo e(now()->format('d/m/Y H:i')); ?></p>
        <p style="font-size: 10px; color: #a8a29e;">
            Alcance: <?php echo e($filters['scope'] === 'todas_mis_sucursales' ? 'Todas las sucursales' : 'Sucursal activa'); ?>

        </p>
    </div>

    <div class="section">
        <h2>Resumen</h2>
        <div class="kpi-grid">
            <div class="kpi-card">
                <div class="value"><?php echo e($kpis['productos_activos']); ?></div>
                <div class="label">Platos Activos</div>
            </div>
            <div class="kpi-card">
                <div class="value"><?php echo e($kpis['productos_sin_stock']); ?></div>
                <div class="label">Sin Stock</div>
            </div>
            <div class="kpi-card">
                <div class="value"><?php echo e($kpis['promociones_activas']); ?></div>
                <div class="label">Promociones</div>
            </div>
            <div class="kpi-card">
                <div class="value"><?php echo e($kpis['visitas']); ?></div>
                <div class="label">Visitas</div>
            </div>
            <div class="kpi-card">
                <div class="value"><?php echo e($kpis['resenas']); ?></div>
                <div class="label">Reseñas</div>
            </div>
            <div class="kpi-card">
                <div class="value"><?php echo e(number_format($kpis['promedio_score'] ?? 0, 1)); ?></div>
                <div class="label">Score Prom.</div>
            </div>
        </div>
    </div>

    <div class="col-2">
        <div class="section">
            <h2>Top Platos más Reseñados</h2>
            <table>
                <tr><th>#</th><th>Plato</th><th class="text-right">Reseñas</th></tr>
                <?php $__currentLoopData = $tables['top_platos_resenas']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td><?php echo e($i + 1); ?></td>
                    <td><?php echo e($item->nombre); ?></td>
                    <td class="text-right"><?php echo e($item->total); ?></td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </table>
        </div>

        <div class="section">
            <h2>Mejor Calificados</h2>
            <table>
                <tr><th>#</th><th>Plato</th><th class="text-right">Score</th></tr>
                <?php $__currentLoopData = $tables['top_platos_score']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td><?php echo e($i + 1); ?></td>
                    <td><?php echo e($item->nombre); ?></td>
                    <td class="text-right text-green"><?php echo e(number_format($item->promedio, 1)); ?></td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </table>
        </div>
    </div>

    <div class="col-2">
        <div class="section">
            <h2>Score</h2>
            <table>
                <?php $distTotal = array_sum($breakdowns['score_distribution']); ?>
                <tr><th>Score</th><th class="text-right">Cantidad</th></tr>
                <?php $__currentLoopData = $breakdowns['score_distribution']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $score => $total): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td><?php echo e($score); ?> estrella<?php echo e($score > 1 ? 's' : ''); ?></td>
                    <td class="text-right"><?php echo e($total); ?></td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </table>
        </div>

        <div class="section">
            <h2>Productos Sin Stock</h2>
            <?php if(count($tables['productos_sin_stock']) > 0): ?>
            <table>
                <tr><th>Nombre</th></tr>
                <?php $__currentLoopData = $tables['productos_sin_stock']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr><td><?php echo e($item['nombre']); ?></td></tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </table>
            <?php else: ?>
            <p style="font-size: 11px; color: #78716c;">Ninguno</p>
            <?php endif; ?>
        </div>
    </div>

    <div class="col-2">
        <div class="section">
            <h2>Promociones</h2>
            <table>
                <tr><th>Estado</th><th class="text-right">Cantidad</th></tr>
                <tr><td>Activas</td><td class="text-right text-green"><?php echo e(count($breakdowns['promociones_por_estado']['activas'])); ?></td></tr>
                <tr><td>Inactivas</td><td class="text-right"><?php echo e(count($breakdowns['promociones_por_estado']['inactivas'])); ?></td></tr>
                <tr><td>Vencidas</td><td class="text-right text-red"><?php echo e(count($breakdowns['promociones_por_estado']['vencidas'])); ?></td></tr>
            </table>
        </div>

        <div class="section">
            <h2>Productos</h2>
            <table>
                <tr><th>Estado</th><th class="text-right">Cantidad</th></tr>
                <tr><td>Activos</td><td class="text-right text-green"><?php echo e($breakdowns['productos_por_estado']['activos']); ?></td></tr>
                <tr><td>Inactivos</td><td class="text-right"><?php echo e($breakdowns['productos_por_estado']['inactivos']); ?></td></tr>
                <tr><td>Sin Stock</td><td class="text-right text-red"><?php echo e($breakdowns['productos_por_estado']['sin_stock']); ?></td></tr>
            </table>
        </div>
    </div>

    <div class="footer">
        GastroGuía — Reporte generado automáticamente el <?php echo e(now()->format('d/m/Y \a \l\a\s H:i')); ?>

    </div>
</body>
</html>
<?php /**PATH C:\Users\yovan\Proyecto-restaurant\src\resources\views/restaurante/reportes/pdf.blade.php ENDPATH**/ ?>