@php
    $w = 600;
    $h = 220;
    $padL = 35;
    $padR = 50;
    $padT = 10;
    $padB = 25;
    $plotW = $w - $padL - $padR;
    $plotH = $h - $padT - $padB;

    $series = array_filter([
        'visitas' => ['data' => $visitas, 'color' => '#6366f1', 'label' => 'Visitas'],
        'resenas' => ['data' => $resenas, 'color' => '#10b981', 'label' => 'Reseñas'],
    ], fn($s) => count($s['data']) > 0);

    $max = 5;
    $count = 0;
    foreach ($series as $s) {
        $c = count($s['data']);
        if ($c > $count) $count = $c;
        foreach ($s['data'] as $v) {
            if ($v->total > $max) $max = $v->total;
        }
    }
    if ($max < 5) $max = 5;

    $seriesPoints = [];
    $totalsBySeries = [];
    foreach ($series as $key => $s) {
        $points = [];
        $total = 0;
        foreach ($s['data'] as $i => $v) {
            $x = $padL + ($count > 1 ? ($i / max($count - 1, 1)) * $plotW : $plotW / 2);
            $y = $padT + $plotH - (($v->total / $max) * $plotH);
            $points[] = round($x, 1) . ',' . round($y, 1);
            $total += $v->total;
        }
        $lastVal = count($s['data']) > 0 ? $s['data'][count($s['data']) - 1]->total : 0;
        $lastX = $padL + ($count > 1 ? ((count($s['data']) - 1) / max($count - 1, 1)) * $plotW : $plotW / 2);
        $lastY = $padT + $plotH - (($lastVal / $max) * $plotH);
        $seriesPoints[$key] = [
            'points' => $points,
            'total' => $total,
            'lastX' => round($lastX, 1),
            'lastY' => round($lastY, 1),
            'lastVal' => $lastVal,
        ];
        $totalsBySeries[$key] = $total;
    }

    $labelStep = max(1, intdiv($count, 12));
    $dateLabels = [];
    $firstSeries = array_key_first($series);
    if ($firstSeries !== null) {
        foreach ($series[$firstSeries]['data'] as $i => $v) {
            if ($i % $labelStep === 0) {
                $lx = $padL + ($count > 1 ? ($i / max($count - 1, 1)) * $plotW : $plotW / 2);
                $dateLabels[] = ['x' => round($lx, 1), 'label' => \Carbon\Carbon::parse($v->fecha)->format('d/m')];
            }
        }
    }

    $gridStep = $max > 10 ? ceil($max / 4 / 5) * 5 : ($max > 5 ? ceil($max / 4) : 1);
    $gridLines = range(0, $max, $gridStep);
@endphp

<div class="w-full">
    @if (count($series) > 0)
        <div class="flex flex-wrap gap-x-5 gap-y-1 mb-2">
            @foreach ($series as $key => $s)
                <span class="flex items-center gap-1.5 text-xs font-bold" style="color: {{ $s['color'] }}">
                    <span class="inline-block w-3 h-[3px] rounded-full" style="background-color: {{ $s['color'] }}"></span>
                    {{ $s['label'] }}
                    <span class="ml-0.5 font-black">{{ $totalsBySeries[$key] }}</span>
                </span>
            @endforeach
        </div>
    @endif

    <svg viewBox="0 0 {{ $w }} {{ $h }}" class="w-full h-auto" preserveAspectRatio="xMidYMid meet">
        <rect x="0" y="0" width="{{ $w }}" height="{{ $h }}" fill="transparent"/>

        @foreach ($gridLines as $gv)
            @php $gy = $padT + $plotH - (($gv / $max) * $plotH); @endphp
            <line x1="{{ $padL }}" y1="{{ $gy }}" x2="{{ $w - $padR }}" y2="{{ $gy }}" stroke="#e5e5e5" stroke-width="1"/>
            <text x="{{ $padL - 6 }}" y="{{ $gy + 4 }}" text-anchor="end" font-size="10" fill="#999" font-family="inherit">{{ $gv }}</text>
        @endforeach

        @foreach ($dateLabels as $dl)
            <text x="{{ $dl['x'] }}" y="{{ $h - 4 }}" text-anchor="middle" font-size="8" fill="#aaa" font-family="inherit">{{ $dl['label'] }}</text>
        @endforeach

        @foreach ($series as $key => $s)
            @php $sp = $seriesPoints[$key]; @endphp
            @if (count($sp['points']) > 0)
                <polyline points="{{ implode(' ', $sp['points']) }}" fill="none" stroke="{{ $s['color'] }}" stroke-width="2" stroke-linejoin="round" stroke-linecap="round"/>
                @foreach ($sp['points'] as $pt)
                    @php list($px, $py) = explode(',', $pt); @endphp
                    <circle cx="{{ $px }}" cy="{{ $py }}" r="2" fill="{{ $s['color'] }}"/>
                @endforeach
                <text x="{{ $sp['lastX'] + 5 }}" y="{{ $sp['lastY'] + 4 }}" font-size="11" font-weight="bold" fill="{{ $s['color'] }}" font-family="inherit">{{ $sp['lastVal'] }}</text>
            @endif
        @endforeach
    </svg>
</div>
