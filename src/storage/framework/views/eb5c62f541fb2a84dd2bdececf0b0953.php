<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'label' => '',
    'value' => '—',
    'icon' => 'dashboard',
    'color' => 'text-primary',
    'delta' => null,
    'deltaLabel' => '%',
    'sparkline' => null,
    'sparklineColor' => '#6366f1',
    'href' => null,
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
    'value' => '—',
    'icon' => 'dashboard',
    'color' => 'text-primary',
    'delta' => null,
    'deltaLabel' => '%',
    'sparkline' => null,
    'sparklineColor' => '#6366f1',
    'href' => null,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $deltaClass = $delta > 0 ? 'text-green-600' : ($delta < 0 ? 'text-red-500' : 'text-stone-400');
    $deltaIcon = $delta > 0 ? 'arrow_upward' : ($delta < 0 ? 'arrow_downward' : 'remove');
    $classes = 'bg-surface-container-lowest rounded-2xl shadow-sm border border-stone-100/50 p-4 relative overflow-hidden group hover:shadow-md transition-all';
?>

<?php if($href): ?>
<a href="<?php echo e($href); ?>" class="<?php echo e($classes); ?> block">
<?php else: ?>
<div class="<?php echo e($classes); ?>">
<?php endif; ?>
    <div class="flex items-center justify-between mb-2">
        <span class="material-symbols-outlined <?php echo e($color); ?> text-lg"><?php echo e($icon); ?></span>
        <?php if($delta !== null): ?>
            <span class="text-xs font-bold <?php echo e($deltaClass); ?> flex items-center gap-0.5">
                <span class="material-symbols-outlined text-sm"><?php echo e($deltaIcon); ?></span>
                <?php echo e($delta >= 0 ? '+' : ''); ?><?php echo e($delta); ?><?php echo e($deltaLabel); ?>

            </span>
        <?php endif; ?>
    </div>
    <p class="text-[10px] font-bold text-stone-500 uppercase tracking-wider"><?php echo e($label); ?></p>
    <h3 class="text-2xl font-black text-on-surface mt-0.5"><?php echo e($value); ?></h3>
    <?php if($sparkline): ?>
        <div class="mt-2 h-8 opacity-60 group-hover:opacity-100 transition-opacity"
             data-sparkline='<?php echo json_encode($sparkline, 15, 512) ?>'
             data-color="<?php echo e($sparklineColor); ?>">
        </div>
    <?php endif; ?>
<?php if($href): ?>
</a>
<?php else: ?>
</div>
<?php endif; ?>
<?php /**PATH C:\Users\yovan\Proyecto-restaurant\src\resources\views/components/kpi-card.blade.php ENDPATH**/ ?>