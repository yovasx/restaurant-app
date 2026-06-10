<?php $__env->startSection('content'); ?>
<header class="mb-8">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-3xl font-extrabold text-on-surface tracking-tight mb-1">Reportes Globales</h2>
            <p class="text-on-surface-variant font-medium"><?php echo e($filters['from']->format('d/m/Y')); ?> — <?php echo e($filters['to']->format('d/m/Y')); ?></p>
        </div>
        <div class="flex gap-3">
            <a href="<?php echo e(route('admin.reportes.export.excel', request()->only(['from', 'to', 'restaurante_id', 'estado_restaurante', 'score']))); ?>" class="inline-flex items-center gap-2 bg-primary text-on-primary px-5 py-2.5 rounded-xl font-bold text-sm shadow-lg shadow-primary/20 hover:bg-primary-container transition-all">
                <span class="material-symbols-outlined" style="font-size: 18px;">table</span>
                Exportar Excel
            </a>
            <a href="<?php echo e(route('admin.reportes.export.pdf', request()->only(['from', 'to', 'restaurante_id', 'estado_restaurante', 'score']))); ?>" class="inline-flex items-center gap-2 bg-[#C0392B] text-white px-5 py-2.5 rounded-xl font-bold text-sm shadow-lg shadow-[#C0392B]/20 hover:bg-[#a32e22] transition-all">
                <span class="material-symbols-outlined" style="font-size: 18px;">picture_as_pdf</span>
                Exportar PDF
            </a>
        </div>
    </div>
</header>

<form method="GET" action="<?php echo e(route('admin.reportes.index')); ?>" class="bg-surface-container-lowest p-5 rounded-2xl shadow-sm border border-stone-100/50 mb-8">
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-4 items-end">
        <div>
            <label class="text-xs font-bold text-stone-500 uppercase tracking-wider mb-1 block">Desde</label>
            <input type="date" name="from" value="<?php echo e(request('from', $filters['from']->format('Y-m-d'))); ?>" class="w-full bg-surface-container-highest border-0 rounded-lg p-3 text-sm focus:ring-2 focus:ring-primary focus:bg-surface-container-lowest transition-all">
        </div>
        <div>
            <label class="text-xs font-bold text-stone-500 uppercase tracking-wider mb-1 block">Hasta</label>
            <input type="date" name="to" value="<?php echo e(request('to', $filters['to']->format('Y-m-d'))); ?>" class="w-full bg-surface-container-highest border-0 rounded-lg p-3 text-sm focus:ring-2 focus:ring-primary focus:bg-surface-container-lowest transition-all">
        </div>
        <div>
            <label class="text-xs font-bold text-stone-500 uppercase tracking-wider mb-1 block">Restaurante</label>
            <select name="restaurante_id" class="w-full bg-surface-container-highest border-0 rounded-lg p-3 text-sm focus:ring-2 focus:ring-primary focus:bg-surface-container-lowest transition-all">
                <option value="">Todos</option>
                <?php $__currentLoopData = $restaurantes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($r->id); ?>" <?php echo e(request('restaurante_id') == $r->id ? 'selected' : ''); ?>><?php echo e($r->nombre); ?> (<?php echo e($r->estado); ?>)</option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>
        <div>
            <label class="text-xs font-bold text-stone-500 uppercase tracking-wider mb-1 block">Estado</label>
            <select name="estado_restaurante" class="w-full bg-surface-container-highest border-0 rounded-lg p-3 text-sm focus:ring-2 focus:ring-primary focus:bg-surface-container-lowest transition-all">
                <option value="">Todos</option>
                <option value="activo" <?php echo e(request('estado_restaurante') === 'activo' ? 'selected' : ''); ?>>Activos</option>
                <option value="inactivo" <?php echo e(request('estado_restaurante') === 'inactivo' ? 'selected' : ''); ?>>Inactivos</option>
                <option value="baneado" <?php echo e(request('estado_restaurante') === 'baneado' ? 'selected' : ''); ?>>Baneados</option>
            </select>
        </div>
        <div>
            <label class="text-xs font-bold text-stone-500 uppercase tracking-wider mb-1 block">Score</label>
            <select name="score" class="w-full bg-surface-container-highest border-0 rounded-lg p-3 text-sm focus:ring-2 focus:ring-primary focus:bg-surface-container-lowest transition-all">
                <option value="">Todos</option>
                <?php $__currentLoopData = range(1, 5); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($s); ?>" <?php echo e(request('score') == $s ? 'selected' : ''); ?>><?php echo e($s); ?> estrella<?php echo e($s > 1 ? 's' : ''); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>
        <div class="flex gap-2">
            <button type="submit" class="flex-1 bg-primary text-on-primary px-4 py-3 rounded-xl font-bold text-sm shadow-sm hover:bg-primary-container transition-all">Aplicar</button>
            <a href="<?php echo e(route('admin.reportes.index')); ?>" class="flex-1 text-center bg-stone-100 text-stone-600 px-4 py-3 rounded-xl font-bold text-sm hover:bg-stone-200 transition-all">Limpiar</a>
        </div>
    </div>
</form>

<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
    <div class="bg-surface-container-lowest p-5 rounded-2xl shadow-sm border border-stone-100/50">
        <p class="text-xs font-bold text-stone-500 uppercase tracking-wider mb-1">Cuentas Restaurante</p>
        <h3 class="text-2xl font-black text-on-surface"><?php echo e($kpis['cuentasRestaurante']); ?></h3>
    </div>
    <div class="bg-surface-container-lowest p-5 rounded-2xl shadow-sm border border-stone-100/50">
        <p class="text-xs font-bold text-stone-500 uppercase tracking-wider mb-1">Sucursales Activas</p>
        <h3 class="text-2xl font-black text-on-surface"><?php echo e($kpis['sucursalesActivas']); ?></h3>
    </div>
    <div class="bg-surface-container-lowest p-5 rounded-2xl shadow-sm border border-stone-100/50">
        <p class="text-xs font-bold text-stone-500 uppercase tracking-wider mb-1">Comensales</p>
        <h3 class="text-2xl font-black text-on-surface"><?php echo e($kpis['totalComensales']); ?></h3>
    </div>
    <div class="bg-surface-container-lowest p-5 rounded-2xl shadow-sm border border-stone-100/50">
        <p class="text-xs font-bold text-stone-500 uppercase tracking-wider mb-1">Visitas (rango)</p>
        <h3 class="text-2xl font-black text-on-surface"><?php echo e($kpis['visitasRango']); ?></h3>
    </div>
    <div class="bg-surface-container-lowest p-5 rounded-2xl shadow-sm border border-stone-100/50">
        <p class="text-xs font-bold text-stone-500 uppercase tracking-wider mb-1">Reseñas (rango)</p>
        <h3 class="text-2xl font-black text-on-surface"><?php echo e($kpis['resenasRango']); ?></h3>
    </div>
    <div class="bg-surface-container-lowest p-5 rounded-2xl shadow-sm border border-stone-100/50">
        <p class="text-xs font-bold text-stone-500 uppercase tracking-wider mb-1">Promedio Score</p>
        <h3 class="text-2xl font-black text-on-surface"><?php echo e(number_format($kpis['promedioScore'] ?? 0, 1)); ?></h3>
    </div>
    <div class="bg-surface-container-lowest p-5 rounded-2xl shadow-sm border border-stone-100/50">
        <p class="text-xs font-bold text-stone-500 uppercase tracking-wider mb-1">Promociones Activas</p>
        <h3 class="text-2xl font-black text-on-surface"><?php echo e($kpis['promocionesActivas']); ?></h3>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div class="bg-surface-container-lowest rounded-2xl shadow-sm border border-stone-100/50 p-5">
        <h4 class="text-lg font-bold text-on-surface mb-4">Top Restaurantes por Visitas</h4>
        <?php if(count($tables['top_restaurantes_visitas']) > 0): ?>
            <div class="space-y-2">
                <?php $__currentLoopData = $tables['top_restaurantes_visitas']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="flex items-center justify-between py-2 border-b border-stone-100 last:border-0">
                        <div class="flex items-center gap-3">
                            <span class="text-xs font-bold text-stone-400 w-5"><?php echo e($i + 1); ?></span>
                            <span class="font-medium text-on-surface text-sm"><?php echo e($item->nombre); ?></span>
                        </div>
                        <span class="text-sm font-bold text-primary"><?php echo e($item->total); ?></span>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        <?php else: ?>
            <p class="text-sm text-stone-400">Sin datos en este rango.</p>
        <?php endif; ?>
    </div>

    <div class="bg-surface-container-lowest rounded-2xl shadow-sm border border-stone-100/50 p-5">
        <h4 class="text-lg font-bold text-on-surface mb-4">Mejor Calificación</h4>
        <?php if(count($tables['top_restaurantes_rating']) > 0): ?>
            <div class="space-y-2">
                <?php $__currentLoopData = $tables['top_restaurantes_rating']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="flex items-center justify-between py-2 border-b border-stone-100 last:border-0">
                        <div class="flex items-center gap-3">
                            <span class="text-xs font-bold text-stone-400 w-5"><?php echo e($i + 1); ?></span>
                            <span class="font-medium text-on-surface text-sm"><?php echo e($item->nombre); ?></span>
                        </div>
                        <span class="text-sm font-bold text-green-600"><?php echo e(number_format($item->promedio, 1)); ?> (<?php echo e($item->total_resenas); ?>)</span>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        <?php else: ?>
            <p class="text-sm text-stone-400">Sin datos suficientes.</p>
        <?php endif; ?>
    </div>

    <div class="bg-surface-container-lowest rounded-2xl shadow-sm border border-stone-100/50 p-5">
        <h4 class="text-lg font-bold text-on-surface mb-4">Peor Calificación</h4>
        <?php if(count($tables['bottom_restaurantes_rating']) > 0): ?>
            <div class="space-y-2">
                <?php $__currentLoopData = $tables['bottom_restaurantes_rating']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="flex items-center justify-between py-2 border-b border-stone-100 last:border-0">
                        <div class="flex items-center gap-3">
                            <span class="text-xs font-bold text-stone-400 w-5"><?php echo e($i + 1); ?></span>
                            <span class="font-medium text-on-surface text-sm"><?php echo e($item->nombre); ?></span>
                        </div>
                        <span class="text-sm font-bold text-red-600"><?php echo e(number_format($item->promedio, 1)); ?> (<?php echo e($item->total_resenas); ?>)</span>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        <?php else: ?>
            <p class="text-sm text-stone-400">Sin datos suficientes.</p>
        <?php endif; ?>
    </div>

    <div class="bg-surface-container-lowest rounded-2xl shadow-sm border border-stone-100/50 p-5">
        <h4 class="text-lg font-bold text-on-surface mb-4">Distribución de Reseñas por Score</h4>
        <?php if(array_sum($tables['resenas_por_score']) > 0): ?>
            <div class="space-y-2">
                <?php $__currentLoopData = $tables['resenas_por_score']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $score => $total): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php $pct = $tables['resenas_por_score'] ? round($total / array_sum($tables['resenas_por_score']) * 100) : 0; ?>
                    <div class="flex items-center gap-3">
                        <span class="text-xs font-bold text-stone-500 w-6"><?php echo e($score); ?>★</span>
                        <div class="flex-1 h-4 bg-stone-100 rounded-full overflow-hidden">
                            <div class="h-full rounded-full <?php echo e($score >= 4 ? 'bg-green-500' : ($score >= 3 ? 'bg-yellow-500' : 'bg-red-500')); ?>" style="width: <?php echo e($pct); ?>%"></div>
                        </div>
                        <span class="text-xs font-bold text-stone-500 w-10 text-right"><?php echo e($total); ?></span>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        <?php else: ?>
            <p class="text-sm text-stone-400">Sin reseñas en este rango.</p>
        <?php endif; ?>
    </div>

    <div class="bg-surface-container-lowest rounded-2xl shadow-sm border border-stone-100/50 p-5">
        <h4 class="text-lg font-bold text-on-surface mb-4">Promociones</h4>
        <div class="grid grid-cols-3 gap-3 text-center">
            <div class="bg-green-50 rounded-xl p-4">
                <p class="text-2xl font-black text-green-600"><?php echo e(count($tables['promociones_por_estado']['activas'])); ?></p>
                <p class="text-xs font-bold text-stone-500 uppercase mt-1">Activas</p>
            </div>
            <div class="bg-stone-50 rounded-xl p-4">
                <p class="text-2xl font-black text-stone-500"><?php echo e(count($tables['promociones_por_estado']['inactivas'])); ?></p>
                <p class="text-xs font-bold text-stone-500 uppercase mt-1">Inactivas</p>
            </div>
            <div class="bg-red-50 rounded-xl p-4">
                <p class="text-2xl font-black text-red-500"><?php echo e(count($tables['promociones_por_estado']['vencidas'])); ?></p>
                <p class="text-xs font-bold text-stone-500 uppercase mt-1">Vencidas</p>
            </div>
        </div>
    </div>

    <div class="bg-surface-container-lowest rounded-2xl shadow-sm border border-stone-100/50 p-5">
        <h4 class="text-lg font-bold text-on-surface mb-4">Productos</h4>
        <div class="grid grid-cols-3 gap-3 text-center">
            <div class="bg-green-50 rounded-xl p-4">
                <p class="text-2xl font-black text-green-600"><?php echo e($tables['productos_por_estado']['activos']); ?></p>
                <p class="text-xs font-bold text-stone-500 uppercase mt-1">Activos</p>
            </div>
            <div class="bg-stone-50 rounded-xl p-4">
                <p class="text-2xl font-black text-stone-500"><?php echo e($tables['productos_por_estado']['inactivos']); ?></p>
                <p class="text-xs font-bold text-stone-500 uppercase mt-1">Inactivos</p>
            </div>
            <div class="bg-red-50 rounded-xl p-4">
                <p class="text-2xl font-black text-red-500"><?php echo e($tables['productos_por_estado']['sin_stock']); ?></p>
                <p class="text-xs font-bold text-stone-500 uppercase mt-1">Sin Stock</p>
            </div>
        </div>
    </div>

    <?php if(count($tables['productos_sin_stock']) > 0): ?>
    <div class="bg-surface-container-lowest rounded-2xl shadow-sm border border-stone-100/50 p-5 lg:col-span-2">
        <h4 class="text-lg font-bold text-on-surface mb-4">Productos Sin Stock</h4>
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-stone-500 text-xs font-bold uppercase tracking-wider">
                    <th class="pb-3">Nombre</th>
                    <th class="pb-3">Restaurante</th>
                </tr>
            </thead>
            <tbody>
                <?php $__currentLoopData = $tables['productos_sin_stock']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr class="border-t border-stone-100">
                    <td class="py-2 font-medium text-on-surface"><?php echo e($item->nombre); ?></td>
                    <td class="py-2 text-stone-500"><?php echo e($item->restaurante); ?></td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\yovan\Proyecto-restaurant\src\resources\views/admin/reportes/index.blade.php ENDPATH**/ ?>