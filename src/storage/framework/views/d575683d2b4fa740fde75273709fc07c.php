<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'id',
    'title' => null,
    'subtitle' => null,
    'maxWidth' => 'max-w-3xl',
    'autoOpen' => false,
]));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter(([
    'id',
    'title' => null,
    'subtitle' => null,
    'maxWidth' => 'max-w-3xl',
    'autoOpen' => false,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<div
    id="<?php echo e($id); ?>"
    data-modal
    data-open="false"
    data-modal-auto-open="<?php echo e($autoOpen ? 'true' : 'false'); ?>"
    class="fixed inset-0 z-[80] hidden"
    aria-modal="true"
    role="dialog"
>
    <div class="absolute inset-0 bg-stone-950/55 backdrop-blur-[2px]" data-modal-close></div>

    <div class="relative flex min-h-full items-center justify-center p-4 sm:p-6">
        <div class="relative w-full <?php echo e($maxWidth); ?> rounded-[1.75rem] border border-stone-200 bg-white shadow-2xl shadow-stone-950/20">
            <div class="flex items-start justify-between gap-4 border-b border-stone-100 px-6 py-5 sm:px-7">
                <div>
                    <?php if($title): ?>
                        <h3 class="font-headline text-2xl font-extrabold text-on-surface"><?php echo e($title); ?></h3>
                    <?php endif; ?>
                    <?php if($subtitle): ?>
                        <p class="mt-1 text-sm text-stone-500"><?php echo e($subtitle); ?></p>
                    <?php endif; ?>
                </div>

                <button
                    type="button"
                    data-modal-close
                    class="inline-flex h-11 w-11 items-center justify-center rounded-full bg-stone-100 text-stone-500 transition hover:bg-stone-200 hover:text-stone-700"
                    aria-label="Cerrar"
                >
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>

            <div class="max-h-[calc(100vh-10rem)] overflow-y-auto px-6 py-6 sm:px-7">
                <?php echo e($slot); ?>

            </div>
        </div>
    </div>
</div>
<?php /**PATH C:\Users\yovan\Proyecto-restaurant\src\resources\views/components/modal.blade.php ENDPATH**/ ?>