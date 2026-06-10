@extends('layouts.restaurante')

@section('title', 'Reportes')
@section('page-title', 'Reportes')

@section('content')
@php
    $deltaClass = fn($d) => $d > 0 ? 'text-green-600' : ($d < 0 ? 'text-red-500' : 'text-stone-400');
    $deltaIcon = fn($d) => $d > 0 ? 'arrow_upward' : ($d < 0 ? 'arrow_downward' : 'remove');
@endphp

{{-- Toolbar --}}
<div class="mb-6 flex flex-wrap items-center justify-between gap-4">
    <div>
        <h2 class="text-3xl font-extrabold text-on-surface tracking-tight mb-1">Reportes</h2>
        <p class="text-on-surface-variant font-medium text-sm flex items-center gap-2">
            <span class="material-symbols-outlined text-stone-400 text-sm">date_range</span>
            {{ $range['from'] }} — {{ $range['to'] }}
            <span class="text-stone-300 mx-1">|</span>
            <span class="text-stone-400 text-xs">vs {{ $range['prev_from'] }} — {{ $range['prev_to'] }}</span>
        </p>
    </div>
    <div class="flex gap-3">
        <a href="{{ route('restaurante.reportes.export.excel', request()->only(['from', 'to', 'scope', 'restaurante_id'])) }}" class="inline-flex items-center gap-2 bg-primary text-on-primary px-5 py-2.5 rounded-xl font-bold text-sm shadow-lg shadow-primary/20 hover:bg-primary-container transition-all">
            <span class="material-symbols-outlined" style="font-size: 18px;">table</span>
            Exportar Excel
        </a>
        <a href="{{ route('restaurante.reportes.export.pdf', request()->only(['from', 'to', 'scope', 'restaurante_id'])) }}" class="inline-flex items-center gap-2 bg-[#C0392B] text-white px-5 py-2.5 rounded-xl font-bold text-sm shadow-lg shadow-[#C0392B]/20 hover:bg-[#a32e22] transition-all">
            <span class="material-symbols-outlined" style="font-size: 18px;">picture_as_pdf</span>
            Exportar PDF
        </a>
    </div>
</div>

{{-- Quick Range + Filters --}}
<form method="GET" action="{{ route('restaurante.reportes.index') }}" class="bg-surface-container-lowest p-5 rounded-2xl shadow-sm border border-stone-100/50 mb-8">
    <div class="flex flex-wrap items-center gap-2 mb-4">
        <span class="text-xs font-bold text-stone-500 uppercase tracking-wider mr-2">Rango rápido:</span>
        @foreach ([7, 14, 30, 60, 90] as $r)
            @php
                $qFrom = now()->subDays($r)->format('Y-m-d');
                $qTo = now()->format('Y-m-d');
                $qScope = request('scope', $filters['scope']);
                $qRest = request('restaurante_id', '');
                $qUrl = route('restaurante.reportes.index', ['from' => $qFrom, 'to' => $qTo, 'scope' => $qScope, 'restaurante_id' => $qRest]);
            @endphp
            <a href="{{ $qUrl }}" class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all cursor-pointer {{ $range['days'] == $r ? 'bg-primary text-white shadow-sm' : 'bg-stone-100 text-stone-600 hover:bg-stone-200' }}">
                {{ $r }}d
            </a>
        @endforeach
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-4 items-end">
        <div>
            <label class="text-xs font-bold text-stone-500 uppercase tracking-wider mb-1 block">Desde</label>
            <input type="date" name="from" value="{{ request('from', $filters['from']->format('Y-m-d')) }}" class="w-full bg-surface-container-highest border-0 rounded-lg p-3 text-sm focus:ring-2 focus:ring-primary focus:bg-surface-container-lowest transition-all">
        </div>
        <div>
            <label class="text-xs font-bold text-stone-500 uppercase tracking-wider mb-1 block">Hasta</label>
            <input type="date" name="to" value="{{ request('to', $filters['to']->format('Y-m-d')) }}" class="w-full bg-surface-container-highest border-0 rounded-lg p-3 text-sm focus:ring-2 focus:ring-primary focus:bg-surface-container-lowest transition-all">
        </div>
        <div>
            <label class="text-xs font-bold text-stone-500 uppercase tracking-wider mb-1 block">Alcance</label>
            <select name="scope" class="w-full bg-surface-container-highest border-0 rounded-lg p-3 text-sm focus:ring-2 focus:ring-primary focus:bg-surface-container-lowest transition-all">
                <option value="sucursal_activa" {{ request('scope', $filters['scope']) === 'sucursal_activa' ? 'selected' : '' }}>Sucursal activa</option>
                <option value="todas_mis_sucursales" {{ request('scope') === 'todas_mis_sucursales' ? 'selected' : '' }}>Todas mis sucursales</option>
            </select>
        </div>
        <div>
            <label class="text-xs font-bold text-stone-500 uppercase tracking-wider mb-1 block">Sucursal</label>
            <select name="restaurante_id" class="w-full bg-surface-container-highest border-0 rounded-lg p-3 text-sm focus:ring-2 focus:ring-primary focus:bg-surface-container-lowest transition-all">
                <option value="">Automática</option>
                @foreach($sucursales as $s)
                    <option value="{{ $s->id }}" {{ request('restaurante_id') == $s->id ? 'selected' : '' }}>{{ $s->nombre }}</option>
                @endforeach
            </select>
        </div>
        <div class="flex gap-2">
            <button type="submit" class="flex-1 bg-primary text-on-primary px-4 py-3 rounded-xl font-bold text-sm shadow-sm hover:bg-primary-container transition-all">Aplicar</button>
            <a href="{{ route('restaurante.reportes.index') }}" class="flex-1 text-center bg-stone-100 text-stone-600 px-4 py-3 rounded-xl font-bold text-sm hover:bg-stone-200 transition-all">Limpiar</a>
        </div>
    </div>
</form>

{{-- Insights --}}
@if (count($insights) > 0)
<div class="mb-6 flex flex-wrap gap-2">
    @foreach ($insights as $insight)
        <span class="inline-flex items-center gap-1.5 bg-amber-50 border border-amber-200 text-amber-800 text-xs font-bold px-3 py-1.5 rounded-full">{{ $insight }}</span>
    @endforeach
</div>
@endif

{{-- KPI Row --}}
@php
    $kpiCardDefs = [
        ['key' => 'productos_activos', 'label' => 'Platos Activos', 'icon' => 'restaurant', 'color' => 'text-primary', 'temporal' => false],
        ['key' => 'promociones_activas', 'label' => 'Promociones', 'icon' => 'local_offer', 'color' => 'text-green-600', 'temporal' => false],
        ['key' => 'visitas', 'label' => 'Visitas', 'icon' => 'footprint', 'color' => 'text-[#C0392B]', 'temporal' => true],
        ['key' => 'resenas', 'label' => 'Reseñas', 'icon' => 'rate_review', 'color' => 'text-tertiary', 'temporal' => true],
        ['key' => 'promedio_score', 'label' => 'Score Prom.', 'icon' => 'star', 'color' => 'text-amber-600', 'temporal' => true],
        ['key' => 'productos_sin_stock', 'label' => 'Sin Stock', 'icon' => 'inventory_2', 'color' => 'text-red-500', 'temporal' => false],
    ];
@endphp

<div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-6 gap-4 mb-8">
    @foreach ($kpiCardDefs as $card)
        @php $k = $kpiCards[$card['key']]; @endphp
        <div class="bg-surface-container-lowest rounded-2xl shadow-sm border border-stone-100/50 p-4 relative overflow-hidden group hover:shadow-md transition-all">
            <div class="flex items-center justify-between mb-2">
                <span class="material-symbols-outlined {{ $card['color'] }} text-lg">{{ $card['icon'] }}</span>
                @if ($card['temporal'])
                    <span class="text-xs font-bold {{ $deltaClass($k['delta']) }} flex items-center gap-0.5">
                        <span class="material-symbols-outlined text-sm">{{ $deltaIcon($k['delta']) }}</span>
                        {{ $k['delta'] >= 0 ? '+' : '' }}{{ $k['delta'] }}%
                    </span>
                @endif
            </div>
            <p class="text-[10px] font-bold text-stone-500 uppercase tracking-wider">{{ $card['label'] }}</p>
            <h3 class="text-2xl font-black text-on-surface mt-0.5">
                @if ($card['key'] === 'promedio_score')
                    {{ number_format($k['current'], 1) }}
                @else
                    {{ number_format($k['current']) }}
                @endif
            </h3>
            @if ($card['temporal'])
                @php $slKey = $card['key']; @endphp
                @if (count($sparklines[$slKey] ?? []) > 0)
                    <div class="mt-2 h-8 opacity-60 group-hover:opacity-100 transition-opacity"
                         data-sparkline='@json($sparklines[$slKey])'
                         data-color="{{ match($card['key']) { 'visitas' => '#6366f1', 'resenas' => '#10b981', 'promedio_score' => '#f59e0b', default => '#a1a1aa' } }}">
                    </div>
                @endif
            @endif
        </div>
    @endforeach
</div>

{{-- Main Chart --}}
<div class="grid grid-cols-1 xl:grid-cols-3 gap-6 mb-8">
    <div class="xl:col-span-2 bg-surface-container-lowest rounded-2xl shadow-sm border border-stone-100/50 p-5">
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center gap-3">
                <span class="material-symbols-outlined text-indigo-500 text-lg">monitoring</span>
                <h4 class="font-headline text-base font-bold text-on-surface">Actividad / Calidad</h4>
            </div>
            <div class="flex gap-1" id="chartTabs">
                <button data-tab="actividad" class="px-3 py-1.5 rounded-lg text-xs font-bold bg-indigo-100 text-indigo-700">Actividad</button>
                <button data-tab="calidad" class="px-3 py-1.5 rounded-lg text-xs font-bold bg-stone-100 text-stone-500 hover:bg-stone-200">Calidad</button>
            </div>
        </div>
        @php
            $chartSeriesActividad = [
                ['name' => 'Visitas', 'data' => collect($series['visitas_por_dia'] ?? [])->pluck('total')->toArray()],
                ['name' => 'Reseñas', 'data' => collect($series['resenas_por_dia'] ?? [])->pluck('total')->toArray()],
            ];
            $chartSeriesCalidad = [
                ['name' => 'Score Prom.', 'data' => collect($series['promedio_score_por_dia'] ?? [])->pluck('total')->map(fn($v) => (float) $v)->toArray()],
            ];
        @endphp
        <div id="mainChart"
             data-series-actividad='@json($chartSeriesActividad)'
             data-series-calidad='@json($chartSeriesCalidad)'
             data-categories='@json($chartCategories)'
             class="w-full">
        </div>
    </div>

    <div class="bg-surface-container-lowest rounded-2xl shadow-sm border border-stone-100/50 p-5">
        <h4 class="font-headline text-base font-bold text-on-surface mb-4 flex items-center gap-2">
            <span class="material-symbols-outlined text-amber-500 text-lg">info</span>
            Resumen
        </h4>
        <div class="space-y-4 text-sm">
            <div class="flex justify-between items-center py-2 border-b border-stone-100">
                <span class="text-stone-500">Platos Activos</span>
                <span class="font-bold text-on-surface">{{ $kpis['productos_activos'] }}</span>
            </div>
            <div class="flex justify-between items-center py-2 border-b border-stone-100">
                <span class="text-stone-500">Sin Stock</span>
                <span class="font-bold {{ $kpis['productos_sin_stock'] > 0 ? 'text-red-500' : 'text-green-600' }}">{{ $kpis['productos_sin_stock'] }}</span>
            </div>
            <div class="flex justify-between items-center py-2 border-b border-stone-100">
                <span class="text-stone-500">Promociones Activas</span>
                <span class="font-bold text-green-600">{{ $kpis['promociones_activas'] }}</span>
            </div>
            <div class="flex justify-between items-center py-2 border-b border-stone-100">
                <span class="text-stone-500">Visitas</span>
                <span class="font-bold text-on-surface">{{ $kpis['visitas'] }}</span>
            </div>
            <div class="flex justify-between items-center py-2 border-b border-stone-100">
                <span class="text-stone-500">Reseñas</span>
                <span class="font-bold text-on-surface">{{ $kpis['resenas'] }}</span>
            </div>
            <div class="flex justify-between items-center py-2">
                <span class="text-stone-500">Score Promedio</span>
                <span class="font-bold text-amber-600">{{ number_format($kpis['promedio_score'] ?? 0, 1) }}</span>
            </div>
            <div class="pt-2 border-t border-stone-100">
                <p class="text-xs text-stone-400">Alcance: {{ $filters['scope'] === 'todas_mis_sucursales' ? 'Todas las sucursales (' . $filters['total_sucursales'] . ')' : 'Sucursal activa' }}</p>
            </div>
        </div>
    </div>
</div>

{{-- Distribution Row --}}
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    {{-- Score Distribution --}}
    <div class="bg-surface-container-lowest rounded-2xl shadow-sm border border-stone-100/50 p-5">
        <h5 class="font-headline text-sm font-bold text-on-surface mb-3">Distribución de Score</h5>
        @php
            $dist = $breakdowns['score_distribution'];
            $distTotal = array_sum($dist);
            $scoreColors = ['#ef4444', '#f97316', '#eab308', '#84cc16', '#22c55e'];
        @endphp
        @if ($distTotal > 0)
            <div class="space-y-2">
                @foreach ($dist as $score => $total)
                    @php $pct = round(($total / $distTotal) * 100); @endphp
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-bold text-stone-500 w-5 shrink-0">{{ $score }}★</span>
                        <div class="flex-1 h-4 bg-stone-100 rounded-full overflow-hidden">
                            <div class="h-full rounded-full transition-all" style="width: {{ $pct }}%; background-color: {{ $scoreColors[$score - 1] ?? '#a1a1aa' }}"></div>
                        </div>
                        <span class="text-xs font-bold text-stone-600 w-8 text-right shrink-0">{{ $total }}</span>
                        <span class="text-xs text-stone-400 w-8 shrink-0">{{ $pct }}%</span>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-sm text-stone-400 text-center py-6">Sin reseñas en el periodo.</p>
        @endif
    </div>

    {{-- Promociones --}}
    <div class="bg-surface-container-lowest rounded-2xl shadow-sm border border-stone-100/50 p-5">
        <h5 class="font-headline text-sm font-bold text-on-surface mb-3">Promociones</h5>
        @php
            $pActivas = count($breakdowns['promociones_por_estado']['activas']);
            $pVencidas = count($breakdowns['promociones_por_estado']['vencidas']);
            $pInactivas = count($breakdowns['promociones_por_estado']['inactivas']);
            $pTotal = max(1, $pActivas + $pVencidas + $pInactivas);
        @endphp
        @if (($pActivas + $pVencidas + $pInactivas) > 0)
            <div class="space-y-2">
                @foreach ([['label' => 'Activas', 'value' => $pActivas, 'color' => 'bg-green-500', 'text' => 'text-green-600'],
                            ['label' => 'Vencidas', 'value' => $pVencidas, 'color' => 'bg-red-500', 'text' => 'text-red-500'],
                            ['label' => 'Inactivas', 'value' => $pInactivas, 'color' => 'bg-stone-400', 'text' => 'text-stone-400']] as $bar)
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-bold {{ $bar['text'] }} w-16 shrink-0">{{ $bar['label'] }}</span>
                        <div class="flex-1 h-4 bg-stone-100 rounded-full overflow-hidden">
                            <div class="h-full rounded-full {{ $bar['color'] }} transition-all" style="width: {{ round(($bar['value'] / $pTotal) * 100) }}%"></div>
                        </div>
                        <span class="text-xs font-bold text-stone-600 w-8 text-right">{{ $bar['value'] }}</span>
                    </div>
                @endforeach
            </div>
            <a href="{{ route('restaurante.promociones.index') }}" class="mt-3 inline-block text-xs font-bold text-primary hover:underline">Gestionar promociones →</a>
        @else
            <p class="text-sm text-stone-400 text-center py-6">Sin promociones registradas.</p>
        @endif
    </div>

    {{-- Productos --}}
    <div class="bg-surface-container-lowest rounded-2xl shadow-sm border border-stone-100/50 p-5">
        <h5 class="font-headline text-sm font-bold text-on-surface mb-3">Productos</h5>
        @php
            $pAct = $breakdowns['productos_por_estado']['activos'];
            $pIna = $breakdowns['productos_por_estado']['inactivos'];
            $pStock = $breakdowns['productos_por_estado']['sin_stock'];
            $pTotal2 = max(1, $pAct + $pIna + $pStock);
        @endphp
        @if (($pAct + $pIna + $pStock) > 0)
            <div class="space-y-2">
                @foreach ([['label' => 'Activos', 'value' => $pAct, 'color' => 'bg-green-500', 'text' => 'text-green-600'],
                            ['label' => 'Sin stock', 'value' => $pStock, 'color' => 'bg-red-500', 'text' => 'text-red-500'],
                            ['label' => 'Inactivos', 'value' => $pIna, 'color' => 'bg-stone-400', 'text' => 'text-stone-400']] as $bar)
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-bold {{ $bar['text'] }} w-16 shrink-0">{{ $bar['label'] }}</span>
                        <div class="flex-1 h-4 bg-stone-100 rounded-full overflow-hidden">
                            <div class="h-full rounded-full {{ $bar['color'] }} transition-all" style="width: {{ round(($bar['value'] / $pTotal2) * 100) }}%"></div>
                        </div>
                        <span class="text-xs font-bold text-stone-600 w-8 text-right">{{ $bar['value'] }}</span>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-sm text-stone-400 text-center py-6">Sin productos registrados.</p>
        @endif
    </div>
</div>

{{-- Rankings + Sin Stock --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
    <div class="bg-surface-container-lowest rounded-2xl shadow-sm border border-stone-100/50 p-5">
        <h4 class="font-headline text-sm font-bold text-on-surface mb-4 flex items-center gap-2">
            <span class="material-symbols-outlined text-primary text-base">trending_up</span>
            Top Platos más Reseñados
        </h4>
        @php $maxRank = count($tables['top_platos_resenas'] ?? []) > 0 ? max(array_map(fn($i) => $i->total ?? $i['total'] ?? 0, $tables['top_platos_resenas'])) : 1; @endphp
        @if (count($tables['top_platos_resenas'] ?? []) > 0)
            <div class="space-y-2.5">
                @foreach ($tables['top_platos_resenas'] as $i => $item)
                    @php $total = $item->total ?? $item['total'] ?? 0; $pct = round(($total / $maxRank) * 100, 1); $nombre = $item->nombre ?? $item['nombre'] ?? ''; @endphp
                    <div>
                        <div class="flex items-center justify-between mb-0.5">
                            <div class="flex items-center gap-2 min-w-0">
                                <span class="text-xs font-bold text-stone-400 w-4 shrink-0">{{ $i + 1 }}</span>
                                <span class="text-sm font-medium text-on-surface truncate">{{ $nombre }}</span>
                            </div>
                            <span class="text-sm font-bold text-primary shrink-0 ml-2">{{ $total }}</span>
                        </div>
                        <div class="w-full h-1.5 bg-stone-100 rounded-full overflow-hidden">
                            <div class="h-full rounded-full bg-gradient-to-r from-indigo-400 to-indigo-600 transition-all" style="width: {{ $pct }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-sm text-stone-400">Sin datos en el periodo.</p>
        @endif
    </div>

    <div class="bg-surface-container-lowest rounded-2xl shadow-sm border border-stone-100/50 p-5">
        <h4 class="font-headline text-sm font-bold text-on-surface mb-4 flex items-center gap-2">
            <span class="material-symbols-outlined text-green-500 text-base">stars</span>
            Mejor Calificados
        </h4>
        @if (count($tables['top_platos_score'] ?? []) > 0)
            <div class="space-y-2">
                @foreach ($tables['top_platos_score'] as $i => $item)
                    @php $nombre = $item->nombre ?? $item['nombre'] ?? ''; $prom = $item->promedio ?? $item['promedio'] ?? 0; $tr = $item->total_resenas ?? $item['total_resenas'] ?? 0; @endphp
                    <div class="flex items-center justify-between py-1.5 border-b border-stone-100 last:border-0">
                        <div class="flex items-center gap-3 min-w-0">
                            <span class="text-xs font-bold text-stone-400 w-4 shrink-0">{{ $i + 1 }}</span>
                            <span class="text-sm font-medium text-on-surface truncate">{{ $nombre }}</span>
                        </div>
                        <span class="text-sm font-bold text-green-600 shrink-0 ml-2">{{ number_format($prom, 1) }} <span class="text-xs text-stone-400">({{ $tr }})</span></span>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-sm text-stone-400">Sin datos suficientes.</p>
        @endif
    </div>

    <div class="bg-surface-container-lowest rounded-2xl shadow-sm border border-stone-100/50 p-5">
        <h4 class="font-headline text-sm font-bold text-on-surface mb-4 flex items-center gap-2">
            <span class="material-symbols-outlined text-red-500 text-base">inventory_2</span>
            Productos sin Stock
        </h4>
        @if (count($tables['productos_sin_stock'] ?? []) > 0)
            <div class="space-y-2">
                @foreach ($tables['productos_sin_stock'] as $item)
                    @php $nombre = $item['nombre'] ?? ''; @endphp
                    <div class="flex items-center gap-2 py-1.5 border-b border-stone-100 last:border-0">
                        <span class="material-symbols-outlined text-red-400 text-sm">circle</span>
                        <span class="text-sm font-medium text-on-surface">{{ $nombre }}</span>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-sm text-stone-400">Todos los productos tienen stock.</p>
        @endif
    </div>
</div>
@endsection