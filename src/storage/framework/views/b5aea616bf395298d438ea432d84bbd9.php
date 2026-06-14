<?php $__env->startSection('title', 'Reseñas'); ?>
<?php $__env->startSection('page-title', 'Reseñas de Clientes'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <div>
        <h2 class="text-2xl font-headline font-bold text-on-surface">Reseñas de Clientes</h2>
        <p class="text-stone-500 text-sm mt-1">Opiniones que los comensales dejaron sobre tu restaurante</p>
    </div>

    <?php $__empty_1 = true; $__currentLoopData = $resenas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $resena): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
    <div class="bg-white rounded-xl shadow-sm border border-stone-100 p-6 flex flex-col md:flex-row gap-6">
        <!-- Avatar & Score -->
        <div class="shrink-0 flex flex-col items-center gap-2">
            <div class="w-12 h-12 rounded-full bg-primary-fixed flex items-center justify-center text-primary font-black text-xl">
                <?php echo e(substr($resena->comensal->nombre ?? 'C', 0, 1)); ?>

            </div>
            <div class="flex items-center gap-0.5">
                <?php for($i = 1; $i <= 5; $i++): ?>
                <span class="material-symbols-outlined text-[18px] <?php echo e($i <= $resena->score ? 'text-amber-400' : 'text-stone-200'); ?>" style="font-variation-settings: 'FILL' 1;">star</span>
                <?php endfor; ?>
            </div>
            <span class="text-xs font-black text-on-surface"><?php echo e($resena->score); ?>/5</span>
        </div>
        <!-- Content -->
        <div class="flex-1">
            <div class="flex items-start justify-between">
                <div>
                    <p class="font-bold text-on-surface"><?php echo e($resena->comensal->nombre ?? 'Comensal'); ?></p>
                    <p class="text-xs text-stone-400"><?php echo e($resena->menu->nombre ?? '—'); ?> · <?php echo e($resena->created_at->format('d M Y')); ?></p>
                </div>
            </div>
            <?php if($resena->comentario): ?>
            <p class="text-stone-700 mt-3 leading-relaxed"><?php echo e($resena->comentario); ?></p>
            <?php endif; ?>
            <?php if($resena->respuesta_restaurante): ?>
            <div class="mt-4 bg-surface-container-low p-4 rounded-lg border-l-4 border-primary">
                <p class="text-xs font-bold text-primary uppercase tracking-wider mb-1">Tu respuesta</p>
                <p class="text-sm text-stone-700"><?php echo e($resena->respuesta_restaurante); ?></p>
            </div>
            <?php endif; ?>
        </div>
    </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
    <div class="bg-white rounded-xl shadow-sm border border-stone-100 p-16 text-center text-stone-500">
        <span class="material-symbols-outlined text-5xl mb-3 opacity-40">rate_review</span>
        <p class="font-semibold text-lg">Aún no tienes reseñas</p>
        <p class="text-sm mt-1">Las reseñas aparecerán aquí cuando los comensales visiten tu restaurante.</p>
    </div>
    <?php endif; ?>

    <?php if($resenas->hasPages()): ?>
    <div class="mt-4"><?php echo e($resenas->links()); ?></div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.restaurante', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\yovan\Proyecto-restaurant\src\resources\views/restaurante/resenas.blade.php ENDPATH**/ ?>