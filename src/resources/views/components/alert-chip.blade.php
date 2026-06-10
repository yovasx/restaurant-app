@props([
    'label' => '',
    'severity' => 'info',
    'count' => null,
    'href' => null,
    'icon' => null,
])

@php
    $map = [
        'warning' => ['icon' => 'error', 'text' => 'text-amber-700', 'bg' => 'bg-amber-50', 'badge' => 'bg-amber-200 text-amber-800'],
        'info'    => ['icon' => 'info', 'text' => 'text-blue-700', 'bg' => 'bg-blue-50', 'badge' => 'bg-blue-200 text-blue-800'],
        'danger'  => ['icon' => 'error', 'text' => 'text-red-700', 'bg' => 'bg-red-50', 'badge' => 'bg-red-200 text-red-800'],
        'success' => ['icon' => 'check_circle', 'text' => 'text-green-700', 'bg' => 'bg-green-50', 'badge' => 'bg-green-200 text-green-800'],
    ];
    $style = $map[$severity] ?? $map['info'];
    $icon ??= $style['icon'];
@endphp

@if ($href)
<a href="{{ $href }}" class="{{ $style['bg'] }} rounded-xl p-3 block hover:brightness-95 transition-all">
@else
<div class="{{ $style['bg'] }} rounded-xl p-3">
@endif
    <div class="flex items-center justify-between">
        <span class="text-xs font-bold {{ $style['text'] }} flex items-center gap-1">
            <span class="material-symbols-outlined text-sm">{{ $icon }}</span>
            {{ $label }}
        </span>
        @if ($count !== null && $count > 0)
            <span class="text-xs font-bold {{ $style['badge'] }} px-2 py-0.5 rounded-full">{{ $count }}</span>
        @endif
    </div>
@if ($href)
</a>
@else
</div>
@endif
