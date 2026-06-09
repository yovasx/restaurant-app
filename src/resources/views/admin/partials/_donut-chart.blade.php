@php
    $size = $size ?? 140;
    $strokeWidth = $strokeWidth ?? 18;
    $radius = ($size - $strokeWidth) / 2;
    $circumference = 2 * pi() * $radius;
    $center = $size / 2;

    $segments = $segments ?? [];
    $total = $total ?? array_sum(array_column($segments, 'value'));
    $total = max(1, $total);

    $cumulative = 0;
    $arcs = [];
    foreach ($segments as $seg) {
        $len = ($seg['value'] / $total) * $circumference;
        $arcs[] = [
            'label' => $seg['label'],
            'value' => $seg['value'],
            'color' => $seg['color'],
            'dasharray' => max(0.5, $len) . ' ' . max(0.5, $circumference - $len),
            'offset' => -$cumulative,
            'pct' => round(($seg['value'] / $total) * 100),
        ];
        $cumulative += $len;
    }
@endphp

<div class="flex flex-col items-center">
    <svg width="{{ $size }}" height="{{ $size }}" viewBox="0 0 {{ $size }} {{ $size }}">
        <g transform="rotate(-90, {{ $center }}, {{ $center }})">
            <circle cx="{{ $center }}" cy="{{ $center }}" r="{{ $radius }}" fill="none" stroke="#f0f0f0" stroke-width="{{ $strokeWidth }}"/>
            @foreach ($arcs as $arc)
                <circle cx="{{ $center }}" cy="{{ $center }}" r="{{ $radius }}" fill="none" stroke="{{ $arc['color'] }}" stroke-width="{{ $strokeWidth }}" stroke-dasharray="{{ $arc['dasharray'] }}" stroke-dashoffset="{{ $arc['offset'] }}" stroke-linecap="butt"/>
            @endforeach
        </g>
        <text x="{{ $center }}" y="{{ $center }}" text-anchor="middle" dominant-baseline="central" font-size="20" font-weight="800" fill="#1e1b18" font-family="inherit">{{ $total }}</text>
    </svg>

    <div class="flex items-center justify-center gap-3 mt-2 flex-wrap">
        @foreach ($arcs as $arc)
            <span class="flex items-center gap-1 text-[10px] font-semibold" style="color: {{ $arc['color'] }}">
                <span class="inline-block w-2 h-2 rounded-full" style="background-color: {{ $arc['color'] }}"></span>
                {{ $arc['label'] }} <span class="font-black ml-0.5">{{ $arc['value'] }}</span>
            </span>
        @endforeach
    </div>
</div>
