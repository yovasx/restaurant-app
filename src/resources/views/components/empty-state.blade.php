@props([
    'icon' => 'inbox',
    'title' => 'Sin datos',
    'message' => 'No hay información disponible en este periodo.',
    'action' => null,
    'actionLabel' => null,
    'iconSize' => 'text-4xl',
])

<div class="flex flex-col items-center justify-center py-8 text-stone-400">
    <span class="material-symbols-outlined {{ $iconSize }} mb-2">{{ $icon }}</span>
    <p class="text-sm font-medium">{{ $title }}</p>
    <p class="text-xs">{{ $message }}</p>
    @if ($action && $actionLabel)
        <a href="{{ $action }}" class="mt-3 text-xs font-bold text-primary hover:underline">{{ $actionLabel }}</a>
    @endif
</div>
