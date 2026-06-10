<?php $__env->startSection('title', 'Reportes'); ?>
<?php $__env->startSection('page-title', 'Reportes'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $deltaClass = fn($d) => $d > 0 ? 'text-green-600' : ($d < 0 ? 'text-red-500' : 'text-stone-400');
    $deltaIcon = fn($d) => $d > 0 ? 'arrow_upward' : ($d < 0 ? 'arrow_downward' : 'remove');
?>


<div class="mb-6 flex flex-wrap items-center justify-between gap-4">
    <div>
        <h2 class="text-3xl font-extrabold text-on-surface tracking-tight mb-1">Reportes</h2>
        <p class="text-on-surface-variant font-medium text-sm flex items-center gap-2">
            <span class="material-symbols-outlined text-stone-400 text-sm">date_range</span>
            <?php echo e($range['from']); ?> — <?php echo e($range['to']); ?>

            <span class="text-stone-300 mx-1">|</span>
            <span class="text-stone-400 text-xs">vs <?php echo e($range['prev_from']); ?> — <?php echo e($range['prev_to']); ?></span>
        </p>
    </div>
    <div class="flex gap-3">
        <a href="<?php echo e(route('restaurante.reportes.export.excel', request()->only(['from', 'to', 'scope', 'restaurante_id']))); ?>" class="inline-flex items-center gap-2 bg-primary text-on-primary px-5 py-2.5 rounded-xl font-bold text-sm shadow-lg shadow-primary/20 hover:bg-primary-container transition-all">
            <span class="material-symbols-outlined" style="font-size: 18px;">table</span>
            Exportar Excel
        </a>
        <a href="<?php echo e(route('restaurante.reportes.export.pdf', request()->only(['from', 'to', 'scope', 'restaurante_id']))); ?>" class="inline-flex items-center gap-2 bg-[#C0392B] text-white px-5 py-2.5 rounded-xl font-bold text-sm shadow-lg shadow-[#C0392B]/20 hover:bg-[#a32e22] transition-all">
            <span class="material-symbols-outlined" style="font-size: 18px;">picture_as_pdf</span>
            Exportar PDF
        </a>
    </div>
</div>


<form method="GET" action="<?php echo e(route('restaurante.reportes.index')); ?>" class="bg-surface-container-lowest p-5 rounded-2xl shadow-sm border border-stone-100/50 mb-8">
    <div class="flex flex-wrap items-center gap-2 mb-4">
        <span class="text-xs font-bold text-stone-500 uppercase tracking-wider mr-2">Rango rápido:</span>
        <?php $__currentLoopData = [7, 14, 30, 60, 90]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php
                $qFrom = now()->subDays($r)->format('Y-m-d');
                $qTo = now()->format('Y-m-d');
                $qScope = request('scope', $filters['scope']);
                $qRest = request('restaurante_id', '');
                $qUrl = route('restaurante.reportes.index', ['from' => $qFrom, 'to' => $qTo, 'scope' => $qScope, 'restaurante_id' => $qRest]);
            ?>
            <a href="<?php echo e($qUrl); ?>" class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all cursor-pointer <?php echo e($range['days'] == $r ? 'bg-primary text-white shadow-sm' : 'bg-stone-100 text-stone-600 hover:bg-stone-200'); ?>">
                <?php echo e($r); ?>d
            </a>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
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
            <label class="text-xs font-bold text-stone-500 uppercase tracking-wider mb-1 block">Alcance</label>
            <select name="scope" class="w-full bg-surface-container-highest border-0 rounded-lg p-3 text-sm focus:ring-2 focus:ring-primary focus:bg-surface-container-lowest transition-all">
                <option value="sucursal_activa" <?php echo e(request('scope', $filters['scope']) === 'sucursal_activa' ? 'selected' : ''); ?>>Sucursal activa</option>
                <option value="todas_mis_sucursales" <?php echo e(request('scope') === 'todas_mis_sucursales' ? 'selected' : ''); ?>>Todas mis sucursales</option>
            </select>
        </div>
        <div>
            <label class="text-xs font-bold text-stone-500 uppercase tracking-wider mb-1 block">Sucursal</label>
            <select name="restaurante_id" class="w-full bg-surface-container-highest border-0 rounded-lg p-3 text-sm focus:ring-2 focus:ring-primary focus:bg-surface-container-lowest transition-all">
                <option value="">Automática</option>
                <?php $__currentLoopData = $sucursales; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($s->id); ?>" <?php echo e(request('restaurante_id') == $s->id ? 'selected' : ''); ?>><?php echo e($s->nombre); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>
        <div class="flex gap-2">
            <button type="submit" class="flex-1 bg-primary text-on-primary px-4 py-3 rounded-xl font-bold text-sm shadow-sm hover:bg-primary-container transition-all">Aplicar</button>
            <a href="<?php echo e(route('restaurante.reportes.index')); ?>" class="flex-1 text-center bg-stone-100 text-stone-600 px-4 py-3 rounded-xl font-bold text-sm hover:bg-stone-200 transition-all">Limpiar</a>
        </div>
    </div>
</form>


<?php if(count($insights) > 0): ?>
<div class="mb-6 flex flex-wrap gap-2">
    <?php $__currentLoopData = $insights; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $insight): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <span class="inline-flex items-center gap-1.5 bg-amber-50 border border-amber-200 text-amber-800 text-xs font-bold px-3 py-1.5 rounded-full"><?php echo e($insight); ?></span>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>
<?php endif; ?>


<?php
    $kpiCardDefs = [
        ['key' => 'productos_activos', 'label' => 'Platos Activos', 'icon' => 'restaurant', 'color' => 'text-primary', 'temporal' => false],
        ['key' => 'promociones_activas', 'label' => 'Promociones', 'icon' => 'local_offer', 'color' => 'text-green-600', 'temporal' => false],
        ['key' => 'visitas', 'label' => 'Visitas', 'icon' => 'footprint', 'color' => 'text-[#C0392B]', 'temporal' => true],
        ['key' => 'resenas', 'label' => 'Reseñas', 'icon' => 'rate_review', 'color' => 'text-tertiary', 'temporal' => true],
        ['key' => 'promedio_score', 'label' => 'Score Prom.', 'icon' => 'star', 'color' => 'text-amber-600', 'temporal' => true],
        ['key' => 'productos_sin_stock', 'label' => 'Sin Stock', 'icon' => 'inventory_2', 'color' => 'text-red-500', 'temporal' => false],
    ];
?>

<div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-6 gap-4 mb-8">
    <?php $__currentLoopData = $kpiCardDefs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $card): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php $k = $kpiCards[$card['key']]; ?>
        <div class="bg-surface-container-lowest rounded-2xl shadow-sm border border-stone-100/50 p-4 relative overflow-hidden group hover:shadow-md transition-all">
            <div class="flex items-center justify-between mb-2">
                <span class="material-symbols-outlined <?php echo e($card['color']); ?> text-lg"><?php echo e($card['icon']); ?></span>
                <?php if($card['temporal']): ?>
                    <span class="text-xs font-bold <?php echo e($deltaClass($k['delta'])); ?> flex items-center gap-0.5">
                        <span class="material-symbols-outlined text-sm"><?php echo e($deltaIcon($k['delta'])); ?></span>
                        <?php echo e($k['delta'] >= 0 ? '+' : ''); ?><?php echo e($k['delta']); ?>%
                    </span>
                <?php endif; ?>
            </div>
            <p class="text-[10px] font-bold text-stone-500 uppercase tracking-wider"><?php echo e($card['label']); ?></p>
            <h3 class="text-2xl font-black text-on-surface mt-0.5">
                <?php if($card['key'] === 'promedio_score'): ?>
                    <?php echo e(number_format($k['current'], 1)); ?>

                <?php else: ?>
                    <?php echo e(number_format($k['current'])); ?>

                <?php endif; ?>
            </h3>
            <?php if($card['temporal']): ?>
                <?php $slKey = $card['key']; ?>
                <?php if(count($sparklines[$slKey] ?? []) > 0): ?>
                    <div class="mt-2 h-8 opacity-60 group-hover:opacity-100 transition-opacity"
                         data-sparkline='<?php echo json_encode($sparklines[$slKey], 15, 512) ?>'
                         data-color="<?php echo e(match($card['key']) { 'visitas' => '#6366f1', 'resenas' => '#10b981', 'promedio_score' => '#f59e0b', default => '#a1a1aa' }); ?>">
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>


<div class="grid grid-cols-1 xl:grid-cols-3 gap-6 mb-8">
    <div class="xl:col-span-2 bg-surface-container-lowest rounded-2xl shadow-sm border border-stone-100/50 p-5">
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center gap-3">
                <span class="material-symbols-outlined text-indigo-500 text-lg">monitoring</span>
                <h4 class="font-headline text-base font-bold text-on-surface">Actividad / Calidad</h4>
            </div>
            <div class="flex gap-1" id="chartTabs">
                <button data-tab="actividad" class="px-3 py-1.5 rounded-lg text-xs font-bold bg-indigo-100 text-indigo-700">Actividad</button>
                <button data-tab="calidad" class="px-3 py-1.5 rounded-lg text-xs font-bold bg-stone-100 text-stone-500 hover:bg-stone-200">Calidad</button>
            </div>
        </div>
        <?php
            $chartSeriesActividad = [
                ['name' => 'Visitas', 'data' => collect($series['visitas_por_dia'] ?? [])->pluck('total')->toArray()],
                ['name' => 'Reseñas', 'data' => collect($series['resenas_por_dia'] ?? [])->pluck('total')->toArray()],
            ];
            $chartSeriesCalidad = [
                ['name' => 'Score Prom.', 'data' => collect($series['promedio_score_por_dia'] ?? [])->pluck('total')->map(fn($v) => (float) $v)->toArray()],
            ];
        ?>
        <div id="mainChart"
             data-series-actividad='<?php echo json_encode($chartSeriesActividad, 15, 512) ?>'
             data-series-calidad='<?php echo json_encode($chartSeriesCalidad, 15, 512) ?>'
             data-categories='<?php echo json_encode($chartCategories, 15, 512) ?>'
             class="w-full">
        </div>
    </div>

    <div class="bg-surface-container-lowest rounded-2xl shadow-sm border border-stone-100/50 p-5">
        <h4 class="font-headline text-base font-bold text-on-surface mb-4 flex items-center gap-2">
            <span class="material-symbols-outlined text-amber-500 text-lg">info</span>
            Resumen
        </h4>
        <div class="space-y-4 text-sm">
            <div class="flex justify-between items-center py-2 border-b border-stone-100">
                <span class="text-stone-500">Platos Activos</span>
                <span class="font-bold text-on-surface"><?php echo e($kpis['productos_activos']); ?></span>
            </div>
            <div class="flex justify-between items-center py-2 border-b border-stone-100">
                <span class="text-stone-500">Sin Stock</span>
                <span class="font-bold <?php echo e($kpis['productos_sin_stock'] > 0 ? 'text-red-500' : 'text-green-600'); ?>"><?php echo e($kpis['productos_sin_stock']); ?></span>
            </div>
            <div class="flex justify-between items-center py-2 border-b border-stone-100">
                <span class="text-stone-500">Promociones Activas</span>
                <span class="font-bold text-green-600"><?php echo e($kpis['promociones_activas']); ?></span>
            </div>
            <div class="flex justify-between items-center py-2 border-b border-stone-100">
                <span class="text-stone-500">Visitas</span>
                <span class="font-bold text-on-surface"><?php echo e($kpis['visitas']); ?></span>
            </div>
            <div class="flex justify-between items-center py-2 border-b border-stone-100">
                <span class="text-stone-500">Reseñas</span>
                <span class="font-bold text-on-surface"><?php echo e($kpis['resenas']); ?></span>
            </div>
            <div class="flex justify-between items-center py-2">
                <span class="text-stone-500">Score Promedio</span>
                <span class="font-bold text-amber-600"><?php echo e(number_format($kpis['promedio_score'] ?? 0, 1)); ?></span>
            </div>
            <div class="pt-2 border-t border-stone-100">
                <p class="text-xs text-stone-400">Alcance: <?php echo e($filters['scope'] === 'todas_mis_sucursales' ? 'Todas las sucursales (' . $filters['total_sucursales'] . ')' : 'Sucursal activa'); ?></p>
            </div>
        </div>
    </div>
</div>


<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    
    <div class="bg-surface-container-lowest rounded-2xl shadow-sm border border-stone-100/50 p-5">
        <h5 class="font-headline text-sm font-bold text-on-surface mb-3">Distribución de Score</h5>
        <?php
            $dist = $breakdowns['score_distribution'];
            $distTotal = array_sum($dist);
            $scoreColors = ['#ef4444', '#f97316', '#eab308', '#84cc16', '#22c55e'];
        ?>
        <?php if($distTotal > 0): ?>
            <div class="space-y-2">
                <?php $__currentLoopData = $dist; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $score => $total): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php $pct = round(($total / $distTotal) * 100); ?>
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-bold text-stone-500 w-5 shrink-0"><?php echo e($score); ?>★</span>
                        <div class="flex-1 h-4 bg-stone-100 rounded-full overflow-hidden">
                            <div class="h-full rounded-full transition-all" style="width: <?php echo e($pct); ?>%; background-color: <?php echo e($scoreColors[$score - 1] ?? '#a1a1aa'); ?>"></div>
                        </div>
                        <span class="text-xs font-bold text-stone-600 w-8 text-right shrink-0"><?php echo e($total); ?></span>
                        <span class="text-xs text-stone-400 w-8 shrink-0"><?php echo e($pct); ?>%</span>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        <?php else: ?>
            <p class="text-sm text-stone-400 text-center py-6">Sin reseñas en el periodo.</p>
        <?php endif; ?>
    </div>

    
    <div class="bg-surface-container-lowest rounded-2xl shadow-sm border border-stone-100/50 p-5">
        <h5 class="font-headline text-sm font-bold text-on-surface mb-3">Promociones</h5>
        <?php
            $pActivas = count($breakdowns['promociones_por_estado']['activas']);
            $pVencidas = count($breakdowns['promociones_por_estado']['vencidas']);
            $pInactivas = count($breakdowns['promociones_por_estado']['inactivas']);
            $pTotal = max(1, $pActivas + $pVencidas + $pInactivas);
        ?>
        <?php if(($pActivas + $pVencidas + $pInactivas) > 0): ?>
            <div class="space-y-2">
                <?php $__currentLoopData = [['label' => 'Activas', 'value' => $pActivas, 'color' => 'bg-green-500', 'text' => 'text-green-600'],
                            ['label' => 'Vencidas', 'value' => $pVencidas, 'color' => 'bg-red-500', 'text' => 'text-red-500'],
                            ['label' => 'Inactivas', 'value' => $pInactivas, 'color' => 'bg-stone-400', 'text' => 'text-stone-400']]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $bar): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-bold <?php echo e($bar['text']); ?> w-16 shrink-0"><?php echo e($bar['label']); ?></span>
                        <div class="flex-1 h-4 bg-stone-100 rounded-full overflow-hidden">
                            <div class="h-full rounded-full <?php echo e($bar['color']); ?> transition-all" style="width: <?php echo e(round(($bar['value'] / $pTotal) * 100)); ?>%"></div>
                        </div>
                        <span class="text-xs font-bold text-stone-600 w-8 text-right"><?php echo e($bar['value']); ?></span>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
            <a href="<?php echo e(route('restaurante.promociones.index')); ?>" class="mt-3 inline-block text-xs font-bold text-primary hover:underline">Gestionar promociones →</a>
        <?php else: ?>
            <p class="text-sm text-stone-400 text-center py-6">Sin promociones registradas.</p>
        <?php endif; ?>
    </div>

    
    <div class="bg-surface-container-lowest rounded-2xl shadow-sm border border-stone-100/50 p-5">
        <h5 class="font-headline text-sm font-bold text-on-surface mb-3">Productos</h5>
        <?php
            $pAct = $breakdowns['productos_por_estado']['activos'];
            $pIna = $breakdowns['productos_por_estado']['inactivos'];
            $pStock = $breakdowns['productos_por_estado']['sin_stock'];
            $pTotal2 = max(1, $pAct + $pIna + $pStock);
        ?>
        <?php if(($pAct + $pIna + $pStock) > 0): ?>
            <div class="space-y-2">
                <?php $__currentLoopData = [['label' => 'Activos', 'value' => $pAct, 'color' => 'bg-green-500', 'text' => 'text-green-600'],
                            ['label' => 'Sin stock', 'value' => $pStock, 'color' => 'bg-red-500', 'text' => 'text-red-500'],
                            ['label' => 'Inactivos', 'value' => $pIna, 'color' => 'bg-stone-400', 'text' => 'text-stone-400']]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $bar): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-bold <?php echo e($bar['text']); ?> w-16 shrink-0"><?php echo e($bar['label']); ?></span>
                        <div class="flex-1 h-4 bg-stone-100 rounded-full overflow-hidden">
                            <div class="h-full rounded-full <?php echo e($bar['color']); ?> transition-all" style="width: <?php echo e(round(($bar['value'] / $pTotal2) * 100)); ?>%"></div>
                        </div>
                        <span class="text-xs font-bold text-stone-600 w-8 text-right"><?php echo e($bar['value']); ?></span>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        <?php else: ?>
            <p class="text-sm text-stone-400 text-center py-6">Sin productos registrados.</p>
        <?php endif; ?>
    </div>
</div>


<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
    <div class="bg-surface-container-lowest rounded-2xl shadow-sm border border-stone-100/50 p-5">
        <h4 class="font-headline text-sm font-bold text-on-surface mb-4 flex items-center gap-2">
            <span class="material-symbols-outlined text-primary text-base">trending_up</span>
            Top Platos más Reseñados
        </h4>
        <?php $maxRank = count($tables['top_platos_resenas'] ?? []) > 0 ? max(array_map(fn($i) => $i->total ?? $i['total'] ?? 0, $tables['top_platos_resenas'])) : 1; ?>
        <?php if(count($tables['top_platos_resenas'] ?? []) > 0): ?>
            <div class="space-y-2.5">
                <?php $__currentLoopData = $tables['top_platos_resenas']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php $total = $item->total ?? $item['total'] ?? 0; $pct = round(($total / $maxRank) * 100, 1); $nombre = $item->nombre ?? $item['nombre'] ?? ''; ?>
                    <div>
                        <div class="flex items-center justify-between mb-0.5">
                            <div class="flex items-center gap-2 min-w-0">
                                <span class="text-xs font-bold text-stone-400 w-4 shrink-0"><?php echo e($i + 1); ?></span>
                                <span class="text-sm font-medium text-on-surface truncate"><?php echo e($nombre); ?></span>
                            </div>
                            <span class="text-sm font-bold text-primary shrink-0 ml-2"><?php echo e($total); ?></span>
                        </div>
                        <div class="w-full h-1.5 bg-stone-100 rounded-full overflow-hidden">
                            <div class="h-full rounded-full bg-gradient-to-r from-indigo-400 to-indigo-600 transition-all" style="width: <?php echo e($pct); ?>%"></div>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        <?php else: ?>
            <p class="text-sm text-stone-400">Sin datos en el periodo.</p>
        <?php endif; ?>
    </div>

    <div class="bg-surface-container-lowest rounded-2xl shadow-sm border border-stone-100/50 p-5">
        <h4 class="font-headline text-sm font-bold text-on-surface mb-4 flex items-center gap-2">
            <span class="material-symbols-outlined text-green-500 text-base">stars</span>
            Mejor Calificados
        </h4>
        <?php if(count($tables['top_platos_score'] ?? []) > 0): ?>
            <div class="space-y-2">
                <?php $__currentLoopData = $tables['top_platos_score']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php $nombre = $item->nombre ?? $item['nombre'] ?? ''; $prom = $item->promedio ?? $item['promedio'] ?? 0; $tr = $item->total_resenas ?? $item['total_resenas'] ?? 0; ?>
                    <div class="flex items-center justify-between py-1.5 border-b border-stone-100 last:border-0">
                        <div class="flex items-center gap-3 min-w-0">
                            <span class="text-xs font-bold text-stone-400 w-4 shrink-0"><?php echo e($i + 1); ?></span>
                            <span class="text-sm font-medium text-on-surface truncate"><?php echo e($nombre); ?></span>
                        </div>
                        <span class="text-sm font-bold text-green-600 shrink-0 ml-2"><?php echo e(number_format($prom, 1)); ?> <span class="text-xs text-stone-400">(<?php echo e($tr); ?>)</span></span>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        <?php else: ?>
            <p class="text-sm text-stone-400">Sin datos suficientes.</p>
        <?php endif; ?>
    </div>

    <div class="bg-surface-container-lowest rounded-2xl shadow-sm border border-stone-100/50 p-5">
        <h4 class="font-headline text-sm font-bold text-on-surface mb-4 flex items-center gap-2">
            <span class="material-symbols-outlined text-red-500 text-base">inventory_2</span>
            Productos sin Stock
        </h4>
        <?php if(count($tables['productos_sin_stock'] ?? []) > 0): ?>
            <div class="space-y-2">
                <?php $__currentLoopData = $tables['productos_sin_stock']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php $nombre = $item['nombre'] ?? ''; ?>
                    <div class="flex items-center gap-2 py-1.5 border-b border-stone-100 last:border-0">
                        <span class="material-symbols-outlined text-red-400 text-sm">circle</span>
                        <span class="text-sm font-medium text-on-surface"><?php echo e($nombre); ?></span>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        <?php else: ?>
            <p class="text-sm text-stone-400">Todos los productos tienen stock.</p>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.restaurante', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\yovan\Proyecto-restaurant\src\resources\views/restaurante/reportes/index.blade.php ENDPATH**/ ?>