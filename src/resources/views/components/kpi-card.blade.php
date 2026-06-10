@props([
    'label' => '',
    'value' => '—',
    'icon' => 'dashboard',
    'color' => 'text-primary',
    'delta' => null,
    'deltaLabel' => '%',
    'sparkline' => null,
    'sparklineColor' => '#6366f1',
    'href' => null,
])

@php
    $deltaClass = $delta > 0 ? 'text-green-600' : ($delta < 0 ? 'text-red-500' : 'text-stone-400');
    $deltaIcon = $delta > 0 ? 'arrow_upward' : ($delta < 0 ? 'arrow_downward' : 'remove');
    $classes = 'bg-surface-container-lowest rounded-2xl shadow-sm border border-stone-100/50 p-4 relative overflow-hidden group hover:shadow-md transition-all';
@endphp

@if ($href)
<a href="{{ $href }}" class="{{ $classes }} block">
@else
<div class="{{ $classes }}">
@endif
    <div class="flex items-center justify-between mb-2">
        <span class="material-symbols-outlined {{ $color }} text-lg">{{ $icon }}</span>
        @if ($delta !== null)
            <span class="text-xs font-bold {{ $deltaClass }} flex items-center gap-0.5">
                <span class="material-symbols-outlined text-sm">{{ $deltaIcon }}</span>
                {{ $delta >= 0 ? '+' : '' }}{{ $delta }}{{ $deltaLabel }}
            </span>
        @endif
    </div>
    <p class="text-[10px] font-bold text-stone-500 uppercase tracking-wider">{{ $label }}</p>
    <h3 class="text-2xl font-black text-on-surface mt-0.5">{{ $value }}</h3>
    @if ($sparkline)
        <div class="mt-2 h-8 opacity-60 group-hover:opacity-100 transition-opacity"
             data-sparkline='@json($sparkline)'
             data-color="{{ $sparklineColor }}">
        </div>
    @endif
@if ($href)
</a>
@else
</div>
@endif
