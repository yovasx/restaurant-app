<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'icon' => 'inbox',
    'title' => 'Sin datos',
    'message' => 'No hay información disponible en este periodo.',
    'action' => null,
    'actionLabel' => null,
    'iconSize' => 'text-4xl',
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
    'icon' => 'inbox',
    'title' => 'Sin datos',
    'message' => 'No hay información disponible en este periodo.',
    'action' => null,
    'actionLabel' => null,
    'iconSize' => 'text-4xl',
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<div class="flex flex-col items-center justify-center py-8 text-stone-400">
    <span class="material-symbols-outlined <?php echo e($iconSize); ?> mb-2"><?php echo e($icon); ?></span>
    <p class="text-sm font-medium"><?php echo e($title); ?></p>
    <p class="text-xs"><?php echo e($message); ?></p>
    <?php if($action && $actionLabel): ?>
        <a href="<?php echo e($action); ?>" class="mt-3 text-xs font-bold text-primary hover:underline"><?php echo e($actionLabel); ?></a>
    <?php endif; ?>
</div>
<?php /**PATH C:\Users\yovan\Proyecto-restaurant\src\resources\views/components/empty-state.blade.php ENDPATH**/ ?>