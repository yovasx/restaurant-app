<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'label' => '',
    'severity' => 'info',
    'count' => null,
    'href' => null,
    'icon' => null,
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
    'label' => '',
    'severity' => 'info',
    'count' => null,
    'href' => null,
    'icon' => null,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $map = [
        'warning' => ['icon' => 'error', 'text' => 'text-amber-700', 'bg' => 'bg-amber-50', 'badge' => 'bg-amber-200 text-amber-800'],
        'info'    => ['icon' => 'info', 'text' => 'text-blue-700', 'bg' => 'bg-blue-50', 'badge' => 'bg-blue-200 text-blue-800'],
        'danger'  => ['icon' => 'error', 'text' => 'text-red-700', 'bg' => 'bg-red-50', 'badge' => 'bg-red-200 text-red-800'],
        'success' => ['icon' => 'check_circle', 'text' => 'text-green-700', 'bg' => 'bg-green-50', 'badge' => 'bg-green-200 text-green-800'],
    ];
    $style = $map[$severity] ?? $map['info'];
    $icon ??= $style['icon'];
?>

<?php if($href): ?>
<a href="<?php echo e($href); ?>" class="<?php echo e($style['bg']); ?> rounded-xl p-3 block hover:brightness-95 transition-all">
<?php else: ?>
<div class="<?php echo e($style['bg']); ?> rounded-xl p-3">
<?php endif; ?>
    <div class="flex items-center justify-between">
        <span class="text-xs font-bold <?php echo e($style['text']); ?> flex items-center gap-1">
            <span class="material-symbols-outlined text-sm"><?php echo e($icon); ?></span>
            <?php echo e($label); ?>

        </span>
        <?php if($count !== null && $count > 0): ?>
            <span class="text-xs font-bold <?php echo e($style['badge']); ?> px-2 py-0.5 rounded-full"><?php echo e($count); ?></span>
        <?php endif; ?>
    </div>
<?php if($href): ?>
</a>
<?php else: ?>
</div>
<?php endif; ?>
<?php /**PATH C:\Users\yovan\Proyecto-restaurant\src\resources\views/components/alert-chip.blade.php ENDPATH**/ ?>