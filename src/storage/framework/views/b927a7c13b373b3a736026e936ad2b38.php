<?php
    $toastType = null;
    $toastMessage = null;

    if ($errorMessage = session('error')) {
        $toastType = 'error';
        $toastMessage = $errorMessage;
    } elseif ($successMessage = session('success')) {
        $toastType = 'success';
        $toastMessage = $successMessage;
    } elseif ($errors->any()) {
        $toastType = 'error';
        $toastMessage = $errors->all();
    }

    $toastTimeout = $toastType === 'error' ? 8000 : 3500;
?>

<?php if($toastType && $toastMessage): ?>
    <div
        data-screen-toast
        data-timeout="<?php echo e($toastTimeout); ?>"
        class="fixed top-4 right-4 left-4 sm:left-auto z-[90] hidden sm:max-w-sm transition-all duration-200 opacity-0 translate-x-4 scale-95"
        role="alert"
    >
        <div class="pointer-events-auto rounded-2xl border px-5 py-4 shadow-2xl
            <?php echo e($toastType === 'success' ? 'border-emerald-200 bg-emerald-50 text-emerald-900' : 'border-red-200 bg-red-50 text-red-900'); ?>"
        >
            <div class="flex items-start gap-3">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl
                    <?php echo e($toastType === 'success' ? 'bg-emerald-200/60 text-emerald-700' : 'bg-red-200/60 text-red-700'); ?>">
                    <span class="material-symbols-outlined text-2xl">
                        <?php echo e($toastType === 'success' ? 'check_circle' : 'cancel'); ?>

                    </span>
                </div>

                <div class="min-w-0 flex-1 pt-0.5">
                    <p class="font-headline text-sm font-extrabold">
                        <?php echo e($toastType === 'success' ? 'Cambio guardado' : 'No se pudo guardar'); ?>

                    </p>

                    <?php if(is_array($toastMessage)): ?>
                        <ul class="mt-1.5 space-y-1 text-sm text-current/70">
                            <?php $__currentLoopData = array_slice($toastMessage, 0, 2); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $message): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <li class="flex items-start gap-1.5">
                                    <span class="mt-0.5 shrink-0">•</span>
                                    <span><?php echo e($message); ?></span>
                                </li>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <?php if(count($toastMessage) > 2): ?>
                                <li class="text-xs opacity-60">y <?php echo e(count($toastMessage) - 2); ?> más...</li>
                            <?php endif; ?>
                        </ul>
                    <?php else: ?>
                        <p class="mt-1 text-sm text-current/70"><?php echo e($toastMessage); ?></p>
                    <?php endif; ?>
                </div>

                <button type="button" data-toast-close
                    class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full transition-colors
                        <?php echo e($toastType === 'success' ? 'hover:bg-emerald-200/60 text-emerald-600' : 'hover:bg-red-200/60 text-red-600'); ?>">
                    <span class="material-symbols-outlined text-lg">close</span>
                </button>
            </div>
        </div>
    </div>
<?php endif; ?>
<?php /**PATH C:\Users\yovan\Proyecto-restaurant\src\resources\views/partials/screen-toast.blade.php ENDPATH**/ ?>