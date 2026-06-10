<?php $__env->startSection('content'); ?>
<?php
    $deltaClass = fn($d) => $d > 0 ? 'text-green-600' : ($d < 0 ? 'text-red-500' : 'text-stone-400');
    $deltaIcon = fn($d) => $d > 0 ? 'arrow_upward' : ($d < 0 ? 'arrow_downward' : 'remove');
?>


<div class="mb-6 flex flex-wrap items-center justify-between gap-4">
    <div class="flex items-center gap-2">
        <span class="material-symbols-outlined text-stone-400 text-lg">schedule</span>
        <?php $__currentLoopData = [7, 14, 30, 60, 90]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <a href="<?php echo e(route('admin.dashboard', array_merge(request()->only(['estado']), ['range' => $r]))); ?>"
               class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all
                      <?php echo e($selectedRange == $r ? 'bg-primary text-white shadow-sm' : 'bg-stone-100 text-stone-600 hover:bg-stone-200'); ?>">
                <?php echo e($r); ?>d
            </a>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
    <div class="flex items-center gap-3">
        <div class="flex items-center gap-2 text-xs text-stone-500">
            <span class="material-symbols-outlined text-stone-400 text-sm">compare_arrows</span>
            <span><?php echo e($range['from']); ?> — <?php echo e($range['to']); ?></span>
            <span class="text-stone-300 mx-1">|</span>
            <span class="text-stone-400">vs <?php echo e($range['prev_from']); ?> — <?php echo e($range['prev_to']); ?></span>
        </div>
        <a href="<?php echo e(route('admin.reportes.index')); ?>" class="flex items-center gap-1.5 bg-stone-100 hover:bg-stone-200 text-stone-600 px-3 py-1.5 rounded-lg text-xs font-bold transition-all">
            <span class="material-symbols-outlined text-sm">analytics</span>
            Reportes
        </a>
    </div>
</div>


<?php if(count($insights) > 0): ?>
<div class="mb-6 flex flex-wrap gap-2">
    <?php $__currentLoopData = $insights; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $insight): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <span class="inline-flex items-center gap-1.5 bg-amber-50 border border-amber-200 text-amber-800 text-xs font-bold px-3 py-1.5 rounded-full"><?php echo e($insight); ?></span>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>
<?php endif; ?>


<?php
    $kpiCards = [
        ['key' => 'restaurantesActivos', 'label' => 'Restaurantes', 'icon' => 'restaurant', 'color' => 'text-primary'],
        ['key' => 'comensalesActivos', 'label' => 'Comensales', 'icon' => 'group', 'color' => 'text-secondary'],
        ['key' => 'visitas', 'label' => 'Visitas', 'icon' => 'footprint', 'color' => 'text-[#C0392B]'],
        ['key' => 'resenas', 'label' => 'Reseñas', 'icon' => 'rate_review', 'color' => 'text-tertiary'],
        ['key' => 'promedioScore', 'label' => 'Score Prom.', 'icon' => 'star', 'color' => 'text-amber-600'],
        ['key' => 'promocionesActivas', 'label' => 'Promociones', 'icon' => 'local_offer', 'color' => 'text-green-600'],
    ];
?>

<div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-6 gap-4 mb-8">
    <?php $__currentLoopData = $kpiCards; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $card): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php $k = $kpis[$card['key']]; ?>
        <div class="bg-surface-container-lowest rounded-2xl shadow-sm border border-stone-100/50 p-4 relative overflow-hidden group hover:shadow-md transition-all">
            <div class="flex items-center justify-between mb-2">
                <span class="material-symbols-outlined <?php echo e($card['color']); ?> text-lg"><?php echo e($card['icon']); ?></span>
                <span class="text-xs font-bold <?php echo e($deltaClass($k['delta'])); ?> flex items-center gap-0.5">
                    <span class="material-symbols-outlined text-sm"><?php echo e($deltaIcon($k['delta'])); ?></span>
                    <?php echo e($k['delta'] >= 0 ? '+' : ''); ?><?php echo e($k['delta']); ?>%
                </span>
            </div>
            <p class="text-[10px] font-bold text-stone-500 uppercase tracking-wider"><?php echo e($card['label']); ?></p>
            <h3 class="text-2xl font-black text-on-surface mt-0.5">
                <?php if($card['key'] === 'promedioScore'): ?>
                    <?php echo e(number_format($k['current'], 1)); ?>

                <?php else: ?>
                    <?php echo e(number_format($k['current'])); ?>

                <?php endif; ?>
            </h3>
            <?php $slKey = ['visitas' => 'visitas', 'resenas' => 'resenas', 'restaurantesActivos' => 'altas_restaurantes', 'comensalesActivos' => 'altas_comensales']; ?>
            <?php if(isset($slKey[$card['key']]) && count($sparklines[$slKey[$card['key']]] ?? []) > 0): ?>
                <div class="mt-2 h-8 opacity-60 group-hover:opacity-100 transition-opacity"
                     data-sparkline='<?php echo json_encode($sparklines[$slKey[$card['key']]], 15, 512) ?>'
                     data-color="<?php echo e(match($card['key']) { 'visitas' => '#6366f1', 'resenas' => '#10b981', 'restaurantesActivos' => '#9e2016', 'comensalesActivos' => '#7c3aed', default => '#a1a1aa' }); ?>">
                </div>
            <?php endif; ?>
        </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>


<div class="grid grid-cols-1 xl:grid-cols-3 gap-6 mb-8">
    <div class="xl:col-span-2 bg-surface-container-lowest rounded-2xl shadow-sm border border-stone-100/50 p-5">
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center gap-3">
                <span class="material-symbols-outlined text-indigo-500 text-lg">monitoring</span>
                <h4 class="font-headline text-base font-bold text-on-surface">Actividad</h4>
            </div>
            <div class="flex gap-1" id="chartTabs">
                <button data-tab="actividad" class="px-3 py-1.5 rounded-lg text-xs font-bold bg-indigo-100 text-indigo-700">Actividad</button>
                <button data-tab="crecimiento" class="px-3 py-1.5 rounded-lg text-xs font-bold bg-stone-100 text-stone-500 hover:bg-stone-200">Crecimiento</button>
            </div>
        </div>
        <?php
            $chartSeriesActividad = [
                ['name' => 'Visitas', 'data' => collect($series['visitas_por_dia'] ?? [])->pluck('total')->toArray()],
                ['name' => 'Reseñas', 'data' => collect($series['resenas_por_dia'] ?? [])->pluck('total')->toArray()],
            ];
            $chartSeriesCrecimiento = [
                ['name' => 'Altas Restaurantes', 'data' => collect($series['altas_restaurantes'] ?? [])->pluck('total')->toArray()],
                ['name' => 'Altas Comensales', 'data' => collect($series['altas_comensales'] ?? [])->pluck('total')->toArray()],
            ];
            $chartCategories = collect($series['visitas_por_dia'] ?? [])->pluck('fecha')->map(fn($d) => \Carbon\Carbon::parse($d)->format('d/m'))->toArray();
        ?>
        <div id="mainChart"
             data-series-actividad='<?php echo json_encode($chartSeriesActividad, 15, 512) ?>'
             data-series-crecimiento='<?php echo json_encode($chartSeriesCrecimiento, 15, 512) ?>'
             data-categories='<?php echo json_encode($chartCategories, 15, 512) ?>'
             class="w-full">
        </div>
    </div>

    <div class="bg-surface-container-lowest rounded-2xl shadow-sm border border-stone-100/50 p-5">
        <h4 class="font-headline text-base font-bold text-on-surface mb-4 flex items-center gap-2">
            <span class="material-symbols-outlined text-amber-500 text-lg">warning</span>
            Alertas
        </h4>
        <?php
            $alertBlocks = [
                'sinStock' => ['label' => 'Productos sin stock', 'icon' => 'inventory_2', 'color' => 'text-red-500', 'bg' => 'bg-red-50'],
                'promocionesVencidas' => ['label' => 'Promos vencidas', 'icon' => 'confirmation_number', 'color' => 'text-orange-500', 'bg' => 'bg-orange-50'],
                'baneados' => ['label' => 'Restaurantes baneados', 'icon' => 'block', 'color' => 'text-stone-500', 'bg' => 'bg-stone-100'],
                'sinVisitas' => ['label' => 'Sin visitas 30d', 'icon' => 'visibility_off', 'color' => 'text-blue-500', 'bg' => 'bg-blue-50'],
            ];
            $hasAlerts = collect($alertBlocks)->contains(fn($cfg, $key) => ($alerts[$key]['total'] ?? 0) > 0);
        ?>
        <?php if($hasAlerts): ?>
            <div class="space-y-3">
                <?php $__currentLoopData = $alertBlocks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $cfg): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php if(($alerts[$key]['total'] ?? 0) > 0): ?>
                        <div class="<?php echo e($cfg['bg']); ?> rounded-xl p-3">
                            <div class="flex items-center justify-between mb-1">
                                <span class="text-xs font-bold <?php echo e($cfg['color']); ?> flex items-center gap-1">
                                    <span class="material-symbols-outlined text-sm"><?php echo e($cfg['icon']); ?></span>
                                    <?php echo e($cfg['label']); ?>

                                </span>
                                <span class="text-lg font-black <?php echo e($cfg['color']); ?>"><?php echo e($alerts[$key]['total']); ?></span>
                            </div>
                            <?php if(count($alerts[$key]['items']) > 0): ?>
                                <div class="text-xs text-stone-600 mt-1 space-y-0.5">
                                    <?php $__currentLoopData = $alerts[$key]['items']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <div>• <?php echo e($item->nombre); ?></div>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        <?php else: ?>
            <div class="flex flex-col items-center justify-center py-8 text-stone-400">
                <span class="material-symbols-outlined text-4xl mb-2">check_circle</span>
                <p class="text-sm font-medium">Sin novedades</p>
                <p class="text-xs">Todo en orden.</p>
            </div>
        <?php endif; ?>
    </div>
</div>


<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    
    <div class="bg-surface-container-lowest rounded-2xl shadow-sm border border-stone-100/50 p-5">
        <h5 class="font-headline text-sm font-bold text-on-surface mb-3">Distribución de Score</h5>
        <?php
            $dist = $rankings['distribucion_score'] ?? [];
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
            $pData = $rankings['promociones'] ?? [];
            $pActivas = count($pData['activas'] ?? []);
            $pVencidas = count($pData['vencidas'] ?? []);
            $pInactivas = count($pData['inactivas'] ?? []);
            $pTotal = max(1, $pActivas + $pVencidas + $pInactivas);
        ?>
        <?php if(($pActivas + $pVencidas + $pInactivas) > 0): ?>
            <div class="space-y-2">
                <div class="flex items-center gap-2">
                    <span class="text-xs font-bold text-green-600 w-16 shrink-0">Activas</span>
                    <div class="flex-1 h-4 bg-stone-100 rounded-full overflow-hidden">
                        <div class="h-full rounded-full bg-green-500 transition-all" style="width: <?php echo e(round(($pActivas / $pTotal) * 100)); ?>%"></div>
                    </div>
                    <span class="text-xs font-bold text-stone-600 w-8 text-right"><?php echo e($pActivas); ?></span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-xs font-bold text-red-500 w-16 shrink-0">Vencidas</span>
                    <div class="flex-1 h-4 bg-stone-100 rounded-full overflow-hidden">
                        <div class="h-full rounded-full bg-red-500 transition-all" style="width: <?php echo e(round(($pVencidas / $pTotal) * 100)); ?>%"></div>
                    </div>
                    <span class="text-xs font-bold text-stone-600 w-8 text-right"><?php echo e($pVencidas); ?></span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-xs font-bold text-stone-400 w-16 shrink-0">Inactivas</span>
                    <div class="flex-1 h-4 bg-stone-100 rounded-full overflow-hidden">
                        <div class="h-full rounded-full bg-stone-400 transition-all" style="width: <?php echo e(round(($pInactivas / $pTotal) * 100)); ?>%"></div>
                    </div>
                    <span class="text-xs font-bold text-stone-600 w-8 text-right"><?php echo e($pInactivas); ?></span>
                </div>
            </div>
        <?php else: ?>
            <p class="text-sm text-stone-400 text-center py-6">Sin promociones registradas.</p>
        <?php endif; ?>
    </div>

    
    <div class="bg-surface-container-lowest rounded-2xl shadow-sm border border-stone-100/50 p-5">
        <h5 class="font-headline text-sm font-bold text-on-surface mb-3">Productos</h5>
        <?php
            $prodData = $rankings['productos'] ?? [];
            $pAct = $prodData['activos'] ?? 0;
            $pIna = $prodData['inactivos'] ?? 0;
            $pStock = $prodData['sin_stock'] ?? 0;
            $pTotal2 = max(1, $pAct + $pIna + $pStock);
        ?>
        <?php if(($pAct + $pIna + $pStock) > 0): ?>
            <div class="space-y-2">
                <div class="flex items-center gap-2">
                    <span class="text-xs font-bold text-green-600 w-16 shrink-0">Activos</span>
                    <div class="flex-1 h-4 bg-stone-100 rounded-full overflow-hidden">
                        <div class="h-full rounded-full bg-green-500 transition-all" style="width: <?php echo e(round(($pAct / $pTotal2) * 100)); ?>%"></div>
                    </div>
                    <span class="text-xs font-bold text-stone-600 w-8 text-right"><?php echo e($pAct); ?></span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-xs font-bold text-red-500 w-16 shrink-0">Sin stock</span>
                    <div class="flex-1 h-4 bg-stone-100 rounded-full overflow-hidden">
                        <div class="h-full rounded-full bg-red-500 transition-all" style="width: <?php echo e(round(($pStock / $pTotal2) * 100)); ?>%"></div>
                    </div>
                    <span class="text-xs font-bold text-stone-600 w-8 text-right"><?php echo e($pStock); ?></span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-xs font-bold text-stone-400 w-16 shrink-0">Inactivos</span>
                    <div class="flex-1 h-4 bg-stone-100 rounded-full overflow-hidden">
                        <div class="h-full rounded-full bg-stone-400 transition-all" style="width: <?php echo e(round(($pIna / $pTotal2) * 100)); ?>%"></div>
                    </div>
                    <span class="text-xs font-bold text-stone-600 w-8 text-right"><?php echo e($pIna); ?></span>
                </div>
            </div>
            <?php if($pStock > 0): ?>
                <a href="<?php echo e(route('admin.reportes.index')); ?>" class="mt-3 inline-block text-xs font-bold text-red-600 hover:underline">Ver productos sin stock →</a>
            <?php endif; ?>
        <?php else: ?>
            <p class="text-sm text-stone-400 text-center py-6">Sin productos registrados.</p>
        <?php endif; ?>
    </div>
</div>


<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
    
    <div class="bg-surface-container-lowest rounded-2xl shadow-sm border border-stone-100/50 p-5">
        <div class="flex items-center justify-between mb-4">
            <h4 class="font-headline text-sm font-bold text-on-surface flex items-center gap-2">
                <span class="material-symbols-outlined text-primary text-base">trending_up</span>
                Top Visitas
            </h4>
        </div>
        <?php $maxVis = count($rankings['top_visitas']) > 0 ? max(array_map(fn($i) => $i->total, $rankings['top_visitas'])) : 1; ?>
        <?php if(count($rankings['top_visitas']) > 0): ?>
            <div class="space-y-2.5">
                <?php $__currentLoopData = $rankings['top_visitas']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php $pct = round(($item->total / $maxVis) * 100, 1); ?>
                    <div>
                        <div class="flex items-center justify-between mb-0.5">
                            <div class="flex items-center gap-2 min-w-0">
                                <span class="text-xs font-bold text-stone-400 w-4 shrink-0"><?php echo e($i + 1); ?></span>
                                <span class="text-sm font-medium text-on-surface truncate"><?php echo e($item->nombre); ?></span>
                            </div>
                            <span class="text-sm font-bold text-primary shrink-0 ml-2"><?php echo e($item->total); ?></span>
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
        <div class="flex items-center justify-between mb-4">
            <h4 class="font-headline text-sm font-bold text-on-surface flex items-center gap-2">
                <span class="material-symbols-outlined text-green-500 text-base">stars</span>
                Mejores
            </h4>
        </div>
        <?php if(count($rankings['top_rating'] ?? []) > 0): ?>
            <div class="space-y-2">
                <?php $__currentLoopData = $rankings['top_rating']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="flex items-center justify-between py-1.5 border-b border-stone-100 last:border-0">
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-bold text-stone-400 w-4"><?php echo e($i + 1); ?></span>
                            <span class="text-sm font-medium text-on-surface"><?php echo e($item->nombre); ?></span>
                        </div>
                        <span class="text-sm font-bold text-green-600"><?php echo e(number_format($item->promedio, 1)); ?></span>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        <?php else: ?>
            <p class="text-sm text-stone-400">Sin datos suficientes.</p>
        <?php endif; ?>
    </div>

    
    <div class="bg-surface-container-lowest rounded-2xl shadow-sm border border-stone-100/50 p-5">
        <div class="flex items-center justify-between mb-4">
            <h4 class="font-headline text-sm font-bold text-on-surface flex items-center gap-2">
                <span class="material-symbols-outlined text-red-500 text-base">trending_down</span>
                Peores
            </h4>
        </div>
        <?php if(count($rankings['peor_rating']) > 0): ?>
            <div class="space-y-2">
                <?php $__currentLoopData = $rankings['peor_rating']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="flex items-center justify-between py-1.5 border-b border-stone-100 last:border-0">
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-bold text-stone-400 w-4"><?php echo e($i + 1); ?></span>
                            <span class="text-sm font-medium text-on-surface"><?php echo e($item->nombre); ?></span>
                        </div>
                        <span class="text-sm font-bold text-red-500"><?php echo e(number_format($item->promedio, 1)); ?></span>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        <?php else: ?>
            <p class="text-sm text-stone-400">Sin datos suficientes.</p>
        <?php endif; ?>
    </div>
</div>


<div class="bg-surface-container-lowest rounded-2xl shadow-sm border border-stone-100/50 p-5">
    <div class="flex items-center justify-between mb-4">
        <h4 class="font-headline text-base font-bold text-on-surface flex items-center gap-2">
            <span class="material-symbols-outlined text-stone-500 text-lg">history</span>
            Últimos eventos
        </h4>
        <a href="<?php echo e(route('admin.auditoria.index')); ?>" class="text-xs font-bold text-primary hover:underline">Ver todo</a>
    </div>
    <?php if(count($latestAudits) > 0): ?>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-2">
            <?php $__currentLoopData = $latestAudits; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="flex items-start gap-2 p-2.5 rounded-xl bg-stone-50/50 border border-stone-100">
                    <span class="inline-block px-2 py-0.5 rounded-full text-[9px] font-bold uppercase tracking-wider shrink-0 mt-0.5
                        <?php echo e($e['modulo'] === 'backups' ? 'bg-purple-100 text-purple-700' : ''); ?>

                        <?php echo e($e['modulo'] === 'reportes' ? 'bg-blue-100 text-blue-700' : ''); ?>

                        <?php echo e($e['modulo'] === 'restaurantes' ? 'bg-orange-100 text-orange-700' : ''); ?>

                        <?php echo e($e['modulo'] === 'comensales' ? 'bg-teal-100 text-teal-700' : ''); ?>

                        <?php echo e($e['modulo'] === 'categorias' ? 'bg-pink-100 text-pink-700' : ''); ?>

                        <?php echo e($e['modulo'] === 'roles' ? 'bg-indigo-100 text-indigo-700' : ''); ?>

                        <?php echo e($e['modulo'] === 'usuarios' ? 'bg-stone-100 text-stone-700' : ''); ?>

                        <?php echo e($e['modulo'] === 'auth' ? 'bg-red-100 text-red-700' : ''); ?>

                    "><?php echo e($e['modulo']); ?></span>
                    <div class="min-w-0">
                        <p class="text-xs font-medium text-on-surface truncate"><?php echo e($e['descripcion'] ?? '—'); ?></p>
                        <p class="text-[10px] text-stone-500 mt-0.5">
                            <?php echo e($e['usuario']['nombre'] ?? '—'); ?> · <?php echo e(\Carbon\Carbon::parse($e['created_at'])->diffForHumans()); ?>

                        </p>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    <?php else: ?>
        <p class="text-sm text-stone-400">Sin eventos registrados.</p>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\yovan\Proyecto-restaurant\src\resources\views/admin/dashboard.blade.php ENDPATH**/ ?>