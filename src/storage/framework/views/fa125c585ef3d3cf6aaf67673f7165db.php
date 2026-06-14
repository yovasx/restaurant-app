<?php $__env->startSection('title', 'Pronósticos'); ?>
<?php $__env->startSection('page-title', 'Pronósticos'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $confLabel = ['alta' => 'Alta', 'media' => 'Media', 'baja' => 'Baja'];
    function pctBar($pct) { return match(true) { $pct >= 80 => 'bg-orange-500', $pct >= 60 => 'bg-orange-400', $pct >= 40 => 'bg-amber-400', $pct >= 20 => 'bg-yellow-300', default => 'bg-stone-200' }; }
?>


<div class="mb-6 flex flex-wrap items-center justify-between gap-4">
    <div>
        <p class="text-on-surface-variant text-sm flex items-center gap-2">
            <span class="material-symbols-outlined text-stone-400 text-sm">schedule</span>
            Pronóstico basado en cadenas de Markov con datos de los últimos <?php echo e(\App\Services\Restaurante\RestaurantForecastService::TRAINING_DAYS); ?> días
        </p>
    </div>
    <form method="GET" action="<?php echo e(route('restaurante.pronosticos.index')); ?>" class="flex items-center gap-2">
        <label class="text-xs font-bold text-stone-500 uppercase tracking-wider">Alcance</label>
        <select name="scope" onchange="this.form.submit()" class="bg-surface-container-highest border-0 rounded-lg py-1.5 px-3 text-xs font-bold text-on-surface focus:ring-2 focus:ring-primary">
            <option value="sucursal_activa" <?php echo e(($scope ?? 'sucursal_activa') === 'sucursal_activa' ? 'selected' : ''); ?>>Sucursal activa</option>
            <option value="todas_mis_sucursales" <?php echo e(($scope ?? '') === 'todas_mis_sucursales' ? 'selected' : ''); ?>>Todas mis sucursales</option>
        </select>
        <noscript><button type="submit" class="bg-primary text-white px-3 py-1.5 rounded-lg text-xs font-bold">Ir</button></noscript>
    </form>
</div>

<?php if(count($probabilities) === 0): ?>
    <div class="bg-surface-container-lowest rounded-2xl shadow-sm border border-stone-100/50 p-12 text-center">
        <span class="material-symbols-outlined text-5xl text-stone-300 mb-4">insights</span>
        <h3 class="font-headline font-bold text-lg text-on-surface mb-2">Sin datos suficientes</h3>
        <p class="text-sm text-stone-500 max-w-md mx-auto">
            No hay suficientes pedidos completados en los últimos <?php echo e(\App\Services\Restaurante\RestaurantForecastService::TRAINING_DAYS); ?> días para generar un pronóstico confiable.
            Los pedidos se registrarán automáticamente a medida que los comensales hagan órdenes.
        </p>
    </div>
<?php else: ?>
    
    <?php
        $best = $highlights['best_today'] ?? null;
        $top = $highlights['top_peak'] ?? null;
        $conf = $highlights['overall_confidence'] ?? 'media';
    ?>
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
        <div class="bg-surface-container-lowest rounded-2xl shadow-sm border border-stone-100/50 p-4">
            <span class="material-symbols-outlined text-primary text-lg mb-1">star</span>
            <p class="text-[10px] font-bold text-stone-500 uppercase tracking-wider">Mejor hora hoy</p>
            <h3 class="text-xl font-black text-on-surface mt-0.5"><?php echo e($best ? $best['hour_label'] : '—'); ?></h3>
            <?php if($best): ?>
                <span class="text-xs font-bold text-orange-600"><?php echo e($best['pct']); ?>% probabilidad</span>
            <?php endif; ?>
        </div>
        <div class="bg-surface-container-lowest rounded-2xl shadow-sm border border-stone-100/50 p-4">
            <span class="material-symbols-outlined text-amber-500 text-lg mb-1">local_fire_department</span>
            <p class="text-[10px] font-bold text-stone-500 uppercase tracking-wider">Pico semanal</p>
            <h3 class="text-xl font-black text-on-surface mt-0.5"><?php echo e($top ? ($top['day_name'] . ' ' . $top['hour_label']) : '—'); ?></h3>
            <?php if($top): ?>
                <span class="text-xs font-bold text-amber-600"><?php echo e($top['pct']); ?>% · <?php echo e($confLabel[$top['confidence']] ?? '—'); ?></span>
            <?php endif; ?>
        </div>
        <div class="bg-surface-container-lowest rounded-2xl shadow-sm border border-stone-100/50 p-4">
            <span class="material-symbols-outlined text-indigo-500 text-lg mb-1">monitoring</span>
            <p class="text-[10px] font-bold text-stone-500 uppercase tracking-wider">Confianza general</p>
            <h3 class="text-xl font-black text-on-surface mt-0.5 capitalize"><?php echo e($confLabel[$conf] ?? $conf); ?></h3>
            <span class="text-xs font-bold text-stone-400">Basado en datos históricos</span>
        </div>
        <div class="bg-surface-container-lowest rounded-2xl shadow-sm border border-stone-100/50 p-4">
            <span class="material-symbols-outlined text-tertiary text-lg mb-1">calendar_month</span>
            <p class="text-[10px] font-bold text-stone-500 uppercase tracking-wider">Pronóstico</p>
            <h3 class="text-xl font-black text-on-surface mt-0.5">7 días</h3>
            <span class="text-xs font-bold text-stone-400">Markov · últimos <?php echo e(\App\Services\Restaurante\RestaurantForecastService::TRAINING_DAYS); ?> días</span>
        </div>
    </div>

    
    <?php $wi = $weekly_insights ?? []; ?>
    <?php if($wi && ($wi['busiestDay'] || $wi['nextAlert'] || $wi['bestOpportunity'])): ?>
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
        <div class="bg-surface-container-lowest rounded-2xl shadow-sm border border-stone-100/50 p-4">
            <span class="material-symbols-outlined text-amber-500 text-lg mb-1">local_fire_department</span>
            <p class="text-[10px] font-bold text-stone-500 uppercase tracking-wider">Día más exigente</p>
            <h3 class="text-sm font-black text-on-surface mt-0.5"><?php echo e($wi['busiestDay']['day_name'] ?? '—'); ?></h3>
            <?php if($wi['busiestDay']): ?>
            <span class="text-xs font-bold text-amber-600"><?php echo e($wi['busiestDay']['window']); ?> · <?php echo e($wi['busiestDay']['pct']); ?>%</span>
            <?php endif; ?>
        </div>
        <div class="bg-surface-container-lowest rounded-2xl shadow-sm border border-stone-100/50 p-4">
            <span class="material-symbols-outlined text-[#C0392B] text-lg mb-1">notifications_active</span>
            <p class="text-[10px] font-bold text-stone-500 uppercase tracking-wider">Próxima alerta</p>
            <h3 class="text-sm font-black text-on-surface mt-0.5"><?php echo e($wi['nextAlert']['day_name'] ?? '—'); ?></h3>
            <?php if($wi['nextAlert']): ?>
            <span class="text-xs font-bold text-orange-600"><?php echo e($wi['nextAlert']['window']); ?> · <?php echo e($wi['nextAlert']['pct']); ?>%</span>
            <?php endif; ?>
        </div>
        <div class="bg-surface-container-lowest rounded-2xl shadow-sm border border-stone-100/50 p-4">
            <span class="material-symbols-outlined text-emerald-500 text-lg mb-1">campaign</span>
            <p class="text-[10px] font-bold text-stone-500 uppercase tracking-wider">Mejor oportunidad</p>
            <h3 class="text-sm font-black text-on-surface mt-0.5"><?php echo e($wi['bestOpportunity']['day_name'] ?? '—'); ?></h3>
            <?php if($wi['bestOpportunity']): ?>
            <span class="text-xs font-bold text-emerald-600"><?php echo e($wi['bestOpportunity']['window']); ?> · <?php echo e($wi['bestOpportunity']['pct']); ?>%</span>
            <?php endif; ?>
        </div>
        <div class="bg-surface-container-lowest rounded-2xl shadow-sm border border-stone-100/50 p-4">
            <span class="material-symbols-outlined text-indigo-500 text-lg mb-1">monitoring</span>
            <p class="text-[10px] font-bold text-stone-500 uppercase tracking-wider">Confianza general</p>
            <h3 class="text-sm font-black text-on-surface mt-0.5 capitalize"><?php echo e($confLabel[$conf] ?? $conf); ?></h3>
            <span class="text-xs font-bold text-stone-400">Basado en datos históricos</span>
        </div>
    </div>
    <?php endif; ?>

    
    <div class="bg-surface-container-lowest rounded-2xl shadow-sm border border-stone-100/50 p-5 mb-8">
        <div class="flex items-center gap-3 mb-4">
            <span class="material-symbols-outlined text-[#C0392B] text-lg">grid_on</span>
            <h4 class="font-headline text-base font-bold text-on-surface">Mapa de calor semanal</h4>
            <span class="text-xs text-stone-400 ml-auto">Intensidad = P(alta) · Cadena de Markov</span>
        </div>
        <?php if(count($heatmap['series'] ?? []) > 0): ?>
             <div id="heatmapChart"
                 data-heatmap='<?php echo json_encode($heatmap['series'], 15, 512) ?>'
                 data-categories='<?php echo json_encode($heatmap['categories'], 15, 512) ?>'
                 class="w-full">
            </div>
        <?php else: ?>
            <p class="text-sm text-stone-400 text-center py-8">No hay datos suficientes para el heatmap.</p>
        <?php endif; ?>
    </div>

    
    <?php if(count($highlights['upcoming_hours'] ?? []) > 0): ?>
    <div class="bg-surface-container-lowest rounded-2xl shadow-sm border border-stone-100/50 p-5 mb-8">
        <h4 class="font-headline text-base font-bold text-on-surface mb-4 flex items-center gap-2">
            <span class="material-symbols-outlined text-[#C0392B] text-lg">schedule</span>
            Próximas horas · <span class="text-sm font-medium text-stone-500">Hoy</span>
        </h4>
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
            <?php $__currentLoopData = $highlights['upcoming_hours']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $h): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="bg-stone-50 rounded-xl p-3 text-center border border-stone-100">
                    <p class="text-lg font-black text-on-surface"><?php echo e($h['hour_label']); ?></p>
                    <div class="mt-1 w-full h-2 bg-stone-200 rounded-full overflow-hidden">
                        <div class="h-full rounded-full <?php echo e(pctBar($h['pct'])); ?>" style="width: <?php echo e(min(100, $h['pct'])); ?>%"></div>
                    </div>
                    <p class="text-xs font-bold text-stone-500 mt-1"><?php echo e($h['pct']); ?>%</p>
                    <span class="text-[10px] font-bold uppercase tracking-wider <?php echo e(match($h['confidence']) { 'alta' => 'text-indigo-600', 'media' => 'text-amber-600', default => 'text-stone-400' }); ?>"><?php echo e($confLabel[$h['confidence']] ?? $h['confidence']); ?></span>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
    <?php endif; ?>

    
    <?php
        $statusBadge = [
            'alta' => 'bg-orange-50 text-orange-800 border border-orange-200',
            'media' => 'bg-amber-50 text-amber-700 border border-amber-200',
            'baja' => 'bg-emerald-50 text-emerald-700 border border-emerald-200',
            'sin_evidencia' => 'bg-stone-100 text-stone-500 border border-stone-200',
        ];
    ?>
    <div class="mb-8">
        <h4 class="font-headline text-base font-bold text-on-surface flex items-center gap-2 mb-5">
            <span class="material-symbols-outlined text-tertiary text-lg">date_range</span>
            Pronóstico semanal
            <span class="text-xs font-medium text-stone-400 ml-auto">Lo más probable para los próximos 7 días</span>
        </h4>
        <div class="grid md:grid-cols-2 xl:grid-cols-3 gap-4">
            <?php $__currentLoopData = $forecast; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $day): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="bg-surface-container-lowest rounded-2xl shadow-sm border border-stone-100/50 p-5 flex flex-col">
                
                <div class="flex items-center justify-between mb-3">
                    <h5 class="font-headline font-bold text-sm text-on-surface"><?php echo e($day['day_name']); ?></h5>
                    <span class="inline-flex items-center rounded-full px-3 py-1 text-[11px] font-bold <?php echo e($statusBadge[$day['status']] ?? 'bg-stone-100 text-stone-500'); ?>">
                        <?php echo e($day['status_label']); ?>

                    </span>
                </div>
                <?php if($day['has_data']): ?>
                    
                    <p class="text-sm text-stone-600 mb-4"><?php echo e($day['summary']); ?></p>
                    
                    <?php if($day['primary_window']): ?>
                    <div class="mb-2">
                        <p class="text-[11px] uppercase tracking-wider font-bold text-stone-400 mb-0.5">Franja principal</p>
                        <p class="text-sm font-black text-on-surface"><?php echo e($day['primary_window']['label']); ?> · <span class="text-orange-600"><?php echo e($day['primary_window']['pct']); ?>%</span></p>
                    </div>
                    <?php endif; ?>
                    
                    <div class="mb-4">
                        <p class="text-[11px] uppercase tracking-wider font-bold text-stone-400 mb-0.5">Franja secundaria</p>
                        <?php if($day['secondary_window']): ?>
                        <p class="text-sm font-black text-on-surface"><?php echo e($day['secondary_window']['label']); ?> · <span class="text-stone-500"><?php echo e($day['secondary_window']['pct']); ?>%</span></p>
                        <?php else: ?>
                        <p class="text-xs text-stone-400">Sin segunda franja relevante</p>
                        <?php endif; ?>
                    </div>
                    
                    <div class="rounded-xl bg-stone-50 border border-stone-100 p-3 mt-auto">
                        <p class="text-[11px] uppercase tracking-wider font-bold text-stone-400 mb-0.5">Acción sugerida</p>
                        <p class="text-sm font-bold text-on-surface"><?php echo e($day['recommendation']); ?></p>
                    </div>
                    
                    <div class="mt-4 pt-3 border-t border-stone-100 flex items-center justify-between text-xs text-stone-500">
                        <span>Confianza <strong><?php echo e($day['confidence_label']); ?></strong></span>
                        <span><?php echo e($day['observations_total']); ?> muestras</span>
                    </div>
                <?php else: ?>
                    <p class="text-sm text-stone-400 mb-4">Aún no hay datos suficientes para este día.</p>
                    <div class="rounded-xl bg-stone-50 border border-stone-100 p-3 mt-auto">
                        <p class="text-xs font-bold text-stone-400">Tómalo con cautela y valida con la operación real.</p>
                    </div>
                <?php endif; ?>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>

    
    <div class="bg-surface-container-lowest rounded-2xl shadow-sm border border-stone-100/50 p-4 mb-8">
        <div class="flex flex-wrap items-center gap-6 text-xs text-stone-500">
            <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-full bg-orange-200 border border-orange-300"></span> Alta afluencia (&ge;60%)</span>
            <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-full bg-amber-200 border border-amber-300"></span> Demanda media (30-59%)</span>
            <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-full bg-emerald-200 border border-emerald-300"></span> Demanda baja (&lt;30%)</span>
            <span class="flex items-center gap-1.5"><span class="material-symbols-outlined text-sm text-indigo-500">check_circle</span> Confianza alta (12+ muestras)</span>
            <span class="flex items-center gap-1.5"><span class="material-symbols-outlined text-sm text-amber-600">warning</span> Confianza media (6-11)</span>
            <span class="flex items-center gap-1.5"><span class="material-symbols-outlined text-sm text-stone-400">info</span> Confianza baja (&lt;6)</span>
        </div>
    </div>
<?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.restaurante', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\yovan\Proyecto-restaurant\src\resources\views/restaurante/pronosticos/index.blade.php ENDPATH**/ ?>