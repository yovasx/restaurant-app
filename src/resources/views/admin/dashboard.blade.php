@extends('layouts.admin')

@section('content')
@php
    $deltaClass = fn($d) => $d > 0 ? 'text-green-600' : ($d < 0 ? 'text-red-500' : 'text-stone-400');
    $deltaIcon = fn($d) => $d > 0 ? 'arrow_upward' : ($d < 0 ? 'arrow_downward' : 'remove');
@endphp

{{-- Toolbar --}}
<div class="mb-6 flex flex-wrap items-center justify-between gap-4">
    <div class="flex items-center gap-2">
        <span class="material-symbols-outlined text-stone-400 text-lg">schedule</span>
        @foreach ([7, 14, 30, 60, 90] as $r)
            <a href="{{ route('admin.dashboard', array_merge(request()->only(['estado']), ['range' => $r])) }}"
               class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all
                      {{ $selectedRange == $r ? 'bg-primary text-white shadow-sm' : 'bg-stone-100 text-stone-600 hover:bg-stone-200' }}">
                {{ $r }}d
            </a>
        @endforeach
    </div>
    <div class="flex items-center gap-3">
        <div class="flex items-center gap-2 text-xs text-stone-500">
            <span class="material-symbols-outlined text-stone-400 text-sm">compare_arrows</span>
            <span>{{ $range['from'] }} — {{ $range['to'] }}</span>
            <span class="text-stone-300 mx-1">|</span>
            <span class="text-stone-400">vs {{ $range['prev_from'] }} — {{ $range['prev_to'] }}</span>
        </div>
        <a href="{{ route('admin.reportes.index') }}" class="flex items-center gap-1.5 bg-stone-100 hover:bg-stone-200 text-stone-600 px-3 py-1.5 rounded-lg text-xs font-bold transition-all">
            <span class="material-symbols-outlined text-sm">analytics</span>
            Reportes
        </a>
    </div>
</div>

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
    $kpiCards = [
        ['key' => 'restaurantesActivos', 'label' => 'Restaurantes', 'icon' => 'restaurant', 'color' => 'text-primary'],
        ['key' => 'comensalesActivos', 'label' => 'Comensales', 'icon' => 'group', 'color' => 'text-secondary'],
        ['key' => 'visitas', 'label' => 'Visitas', 'icon' => 'footprint', 'color' => 'text-[#C0392B]'],
        ['key' => 'resenas', 'label' => 'Reseñas', 'icon' => 'rate_review', 'color' => 'text-tertiary'],
        ['key' => 'promedioScore', 'label' => 'Score Prom.', 'icon' => 'star', 'color' => 'text-amber-600'],
        ['key' => 'promocionesActivas', 'label' => 'Promociones', 'icon' => 'local_offer', 'color' => 'text-green-600'],
    ];
@endphp

<div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-6 gap-4 mb-8">
    @foreach ($kpiCards as $card)
        @php $k = $kpis[$card['key']]; @endphp
        <div class="bg-surface-container-lowest rounded-2xl shadow-sm border border-stone-100/50 p-4 relative overflow-hidden group hover:shadow-md transition-all">
            <div class="flex items-center justify-between mb-2">
                <span class="material-symbols-outlined {{ $card['color'] }} text-lg">{{ $card['icon'] }}</span>
                <span class="text-xs font-bold {{ $deltaClass($k['delta']) }} flex items-center gap-0.5">
                    <span class="material-symbols-outlined text-sm">{{ $deltaIcon($k['delta']) }}</span>
                    {{ $k['delta'] >= 0 ? '+' : '' }}{{ $k['delta'] }}%
                </span>
            </div>
            <p class="text-[10px] font-bold text-stone-500 uppercase tracking-wider">{{ $card['label'] }}</p>
            <h3 class="text-2xl font-black text-on-surface mt-0.5">
                @if ($card['key'] === 'promedioScore')
                    {{ number_format($k['current'], 1) }}
                @else
                    {{ number_format($k['current']) }}
                @endif
            </h3>
            @php $slKey = ['visitas' => 'visitas', 'resenas' => 'resenas', 'restaurantesActivos' => 'altas_restaurantes', 'comensalesActivos' => 'altas_comensales']; @endphp
            @if (isset($slKey[$card['key']]) && count($sparklines[$slKey[$card['key']]] ?? []) > 0)
                <div class="mt-2 h-8 opacity-60 group-hover:opacity-100 transition-opacity"
                     data-sparkline='@json($sparklines[$slKey[$card['key']]])'
                     data-color="{{ match($card['key']) { 'visitas' => '#6366f1', 'resenas' => '#10b981', 'restaurantesActivos' => '#9e2016', 'comensalesActivos' => '#7c3aed', default => '#a1a1aa' } }}">
                </div>
            @endif
        </div>
    @endforeach
</div>

{{-- Main Chart + Alerts --}}
<div class="grid grid-cols-1 xl:grid-cols-3 gap-6 mb-8">
    <div class="xl:col-span-2 bg-surface-container-lowest rounded-2xl shadow-sm border border-stone-100/50 p-5">
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center gap-3">
                <span class="material-symbols-outlined text-indigo-500 text-lg">monitoring</span>
                <h4 class="font-headline text-base font-bold text-on-surface">Actividad</h4>
            </div>
            <div class="flex gap-1" data-chart-tabs>
                <button data-tab="actividad" class="px-3 py-1.5 rounded-lg text-xs font-bold bg-indigo-100 text-indigo-700">Actividad</button>
                <button data-tab="crecimiento" class="px-3 py-1.5 rounded-lg text-xs font-bold bg-stone-100 text-stone-500 hover:bg-stone-200">Crecimiento</button>
            </div>
        </div>
        @php
            $chartSeriesActividad = [
                ['name' => 'Visitas', 'data' => collect($series['visitas_por_dia'] ?? [])->pluck('total')->toArray()],
                ['name' => 'Reseñas', 'data' => collect($series['resenas_por_dia'] ?? [])->pluck('total')->toArray()],
            ];
            $chartSeriesCrecimiento = [
                ['name' => 'Altas Restaurantes', 'data' => collect($series['altas_restaurantes'] ?? [])->pluck('total')->toArray()],
                ['name' => 'Altas Comensales', 'data' => collect($series['altas_comensales'] ?? [])->pluck('total')->toArray()],
            ];
            $chartCategories = collect($series['visitas_por_dia'] ?? [])->pluck('fecha')->map(fn($d) => \Carbon\Carbon::parse($d)->format('d/m'))->toArray();
        @endphp
        <div id="mainChart"
             data-dashboard-chart
             data-default-tab="actividad"
             data-series-actividad='@json($chartSeriesActividad)'
             data-series-crecimiento='@json($chartSeriesCrecimiento)'
             data-categories='@json($chartCategories)'
             class="w-full">
        </div>
    </div>

    <div class="bg-surface-container-lowest rounded-2xl shadow-sm border border-stone-100/50 p-5">
        <h4 class="font-headline text-base font-bold text-on-surface mb-4 flex items-center gap-2">
            <span class="material-symbols-outlined text-amber-500 text-lg">warning</span>
            Alertas
        </h4>
        @php
            $alertBlocks = [
                'sinStock' => ['label' => 'Productos sin stock', 'icon' => 'inventory_2', 'color' => 'text-red-500', 'bg' => 'bg-red-50'],
                'promocionesVencidas' => ['label' => 'Promos vencidas', 'icon' => 'confirmation_number', 'color' => 'text-orange-500', 'bg' => 'bg-orange-50'],
                'baneados' => ['label' => 'Restaurantes baneados', 'icon' => 'block', 'color' => 'text-stone-500', 'bg' => 'bg-stone-100'],
                'sinVisitas' => ['label' => 'Sin visitas 30d', 'icon' => 'visibility_off', 'color' => 'text-blue-500', 'bg' => 'bg-blue-50'],
            ];
            $alertUrls = ['baneados' => route('admin.restaurantes.index'), 'sinVisitas' => route('admin.reportes.index')];
            $hasAlerts = collect($alertBlocks)->contains(fn($cfg, $key) => ($alerts[$key]['total'] ?? 0) > 0);
        @endphp
        @if ($hasAlerts)
            <div class="space-y-3">
                @foreach ($alertBlocks as $key => $cfg)
                    @if (($alerts[$key]['total'] ?? 0) > 0)
                        @php $url = $alertUrls[$key] ?? null; @endphp
                        @if ($url)
                        <a href="{{ $url }}" class="{{ $cfg['bg'] }} rounded-xl p-3 block hover:brightness-95 transition-all">
                        @else
                        <div class="{{ $cfg['bg'] }} rounded-xl p-3">
                        @endif
                            <div class="flex items-center justify-between mb-1">
                                <span class="text-xs font-bold {{ $cfg['color'] }} flex items-center gap-1">
                                    <span class="material-symbols-outlined text-sm">{{ $cfg['icon'] }}</span>
                                    {{ $cfg['label'] }}
                                </span>
                                <span class="text-lg font-black {{ $cfg['color'] }}">{{ $alerts[$key]['total'] }}</span>
                            </div>
                            @if (count($alerts[$key]['items']) > 0)
                                <div class="text-xs text-stone-600 mt-1 space-y-0.5">
                                    @foreach ($alerts[$key]['items'] as $item)
                                        <div>• {{ $item->nombre }}</div>
                                    @endforeach
                                </div>
                            @endif
                        @if ($url)
                        </a>
                        @else
                        </div>
                        @endif
                    @endif
                @endforeach
            </div>
        @else
            <div class="flex flex-col items-center justify-center py-8 text-stone-400">
                <span class="material-symbols-outlined text-4xl mb-2">check_circle</span>
                <p class="text-sm font-medium">Sin novedades</p>
                <p class="text-xs">Todo en orden.</p>
            </div>
        @endif
    </div>
</div>

{{-- Distribution Row --}}
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    {{-- Score Distribution as bars --}}
    <div class="bg-surface-container-lowest rounded-2xl shadow-sm border border-stone-100/50 p-5">
        <h5 class="font-headline text-sm font-bold text-on-surface mb-3">Distribución de Score</h5>
        @php
            $dist = $rankings['distribucion_score'] ?? [];
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

    {{-- Promotions health --}}
    <div class="bg-surface-container-lowest rounded-2xl shadow-sm border border-stone-100/50 p-5">
        <h5 class="font-headline text-sm font-bold text-on-surface mb-3">Promociones</h5>
        @php
            $pData = $rankings['promociones'] ?? [];
            $pActivas = count($pData['activas'] ?? []);
            $pVencidas = count($pData['vencidas'] ?? []);
            $pInactivas = count($pData['inactivas'] ?? []);
            $pTotal = max(1, $pActivas + $pVencidas + $pInactivas);
        @endphp
        @if (($pActivas + $pVencidas + $pInactivas) > 0)
            <div class="space-y-2">
                <div class="flex items-center gap-2">
                    <span class="text-xs font-bold text-green-600 w-16 shrink-0">Activas</span>
                    <div class="flex-1 h-4 bg-stone-100 rounded-full overflow-hidden">
                        <div class="h-full rounded-full bg-green-500 transition-all" style="width: {{ round(($pActivas / $pTotal) * 100) }}%"></div>
                    </div>
                    <span class="text-xs font-bold text-stone-600 w-8 text-right">{{ $pActivas }}</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-xs font-bold text-red-500 w-16 shrink-0">Vencidas</span>
                    <div class="flex-1 h-4 bg-stone-100 rounded-full overflow-hidden">
                        <div class="h-full rounded-full bg-red-500 transition-all" style="width: {{ round(($pVencidas / $pTotal) * 100) }}%"></div>
                    </div>
                    <span class="text-xs font-bold text-stone-600 w-8 text-right">{{ $pVencidas }}</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-xs font-bold text-stone-400 w-16 shrink-0">Inactivas</span>
                    <div class="flex-1 h-4 bg-stone-100 rounded-full overflow-hidden">
                        <div class="h-full rounded-full bg-stone-400 transition-all" style="width: {{ round(($pInactivas / $pTotal) * 100) }}%"></div>
                    </div>
                    <span class="text-xs font-bold text-stone-600 w-8 text-right">{{ $pInactivas }}</span>
                </div>
            </div>
        @else
            <p class="text-sm text-stone-400 text-center py-6">Sin promociones registradas.</p>
        @endif
    </div>

    {{-- Products health --}}
    <div class="bg-surface-container-lowest rounded-2xl shadow-sm border border-stone-100/50 p-5">
        <h5 class="font-headline text-sm font-bold text-on-surface mb-3">Productos</h5>
        @php
            $prodData = $rankings['productos'] ?? [];
            $pAct = $prodData['activos'] ?? 0;
            $pIna = $prodData['inactivos'] ?? 0;
            $pStock = $prodData['sin_stock'] ?? 0;
            $pTotal2 = max(1, $pAct + $pIna + $pStock);
        @endphp
        @if (($pAct + $pIna + $pStock) > 0)
            <div class="space-y-2">
                <div class="flex items-center gap-2">
                    <span class="text-xs font-bold text-green-600 w-16 shrink-0">Activos</span>
                    <div class="flex-1 h-4 bg-stone-100 rounded-full overflow-hidden">
                        <div class="h-full rounded-full bg-green-500 transition-all" style="width: {{ round(($pAct / $pTotal2) * 100) }}%"></div>
                    </div>
                    <span class="text-xs font-bold text-stone-600 w-8 text-right">{{ $pAct }}</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-xs font-bold text-red-500 w-16 shrink-0">Sin stock</span>
                    <div class="flex-1 h-4 bg-stone-100 rounded-full overflow-hidden">
                        <div class="h-full rounded-full bg-red-500 transition-all" style="width: {{ round(($pStock / $pTotal2) * 100) }}%"></div>
                    </div>
                    <span class="text-xs font-bold text-stone-600 w-8 text-right">{{ $pStock }}</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-xs font-bold text-stone-400 w-16 shrink-0">Inactivos</span>
                    <div class="flex-1 h-4 bg-stone-100 rounded-full overflow-hidden">
                        <div class="h-full rounded-full bg-stone-400 transition-all" style="width: {{ round(($pIna / $pTotal2) * 100) }}%"></div>
                    </div>
                    <span class="text-xs font-bold text-stone-600 w-8 text-right">{{ $pIna }}</span>
                </div>
            </div>
            @if ($pStock > 0)
                <a href="{{ route('admin.reportes.index') }}" class="mt-3 inline-block text-xs font-bold text-red-600 hover:underline">Ver productos sin stock →</a>
            @endif
        @else
            <p class="text-sm text-stone-400 text-center py-6">Sin productos registrados.</p>
        @endif
    </div>
</div>

{{-- Rankings Row --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
    {{-- Top Visitas --}}
    <div class="bg-surface-container-lowest rounded-2xl shadow-sm border border-stone-100/50 p-5">
        <div class="flex items-center justify-between mb-4">
            <h4 class="font-headline text-sm font-bold text-on-surface flex items-center gap-2">
                <span class="material-symbols-outlined text-primary text-base">trending_up</span>
                Top Visitas
            </h4>
        </div>
        @php $maxVis = count($rankings['top_visitas']) > 0 ? max(array_map(fn($i) => $i->total, $rankings['top_visitas'])) : 1; @endphp
        @if (count($rankings['top_visitas']) > 0)
            <div class="space-y-2.5">
                @foreach ($rankings['top_visitas'] as $i => $item)
                    @php $pct = round(($item->total / $maxVis) * 100, 1); @endphp
                    <div>
                        <div class="flex items-center justify-between mb-0.5">
                            <div class="flex items-center gap-2 min-w-0">
                                <span class="text-xs font-bold text-stone-400 w-4 shrink-0">{{ $i + 1 }}</span>
                                <span class="text-sm font-medium text-on-surface truncate">{{ $item->nombre }}</span>
                            </div>
                            <span class="text-sm font-bold text-primary shrink-0 ml-2">{{ $item->total }}</span>
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

    {{-- Mejor Calificados --}}
    <div class="bg-surface-container-lowest rounded-2xl shadow-sm border border-stone-100/50 p-5">
        <div class="flex items-center justify-between mb-4">
            <h4 class="font-headline text-sm font-bold text-on-surface flex items-center gap-2">
                <span class="material-symbols-outlined text-green-500 text-base">stars</span>
                Mejores
            </h4>
        </div>
        @if (count($rankings['top_rating'] ?? []) > 0)
            <div class="space-y-2">
                @foreach ($rankings['top_rating'] as $i => $item)
                    <div class="flex items-center justify-between py-1.5 border-b border-stone-100 last:border-0">
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-bold text-stone-400 w-4">{{ $i + 1 }}</span>
                            <span class="text-sm font-medium text-on-surface">{{ $item->nombre }}</span>
                        </div>
                        <span class="text-sm font-bold text-green-600">{{ number_format($item->promedio, 1) }}</span>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-sm text-stone-400">Sin datos suficientes.</p>
        @endif
    </div>

    {{-- Peor Calificados --}}
    <div class="bg-surface-container-lowest rounded-2xl shadow-sm border border-stone-100/50 p-5">
        <div class="flex items-center justify-between mb-4">
            <h4 class="font-headline text-sm font-bold text-on-surface flex items-center gap-2">
                <span class="material-symbols-outlined text-red-500 text-base">trending_down</span>
                Peores
            </h4>
        </div>
        @if (count($rankings['peor_rating']) > 0)
            <div class="space-y-2">
                @foreach ($rankings['peor_rating'] as $i => $item)
                    <div class="flex items-center justify-between py-1.5 border-b border-stone-100 last:border-0">
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-bold text-stone-400 w-4">{{ $i + 1 }}</span>
                            <span class="text-sm font-medium text-on-surface">{{ $item->nombre }}</span>
                        </div>
                        <span class="text-sm font-bold text-red-500">{{ number_format($item->promedio, 1) }}</span>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-sm text-stone-400">Sin datos suficientes.</p>
        @endif
    </div>
</div>

{{-- Pending Management --}}
@php
    $pendingRestaurantes ??= collect([]);
    $pendingComensales ??= collect([]);
@endphp
<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
    <div class="bg-surface-container-lowest rounded-2xl shadow-sm border border-stone-100/50 p-5">
        <h4 class="font-headline text-sm font-bold text-on-surface mb-3 flex items-center gap-2">
            <span class="material-symbols-outlined text-orange-500 text-base">store</span>
            Últimos Restaurantes Registrados
        </h4>
        @if (count($pendingRestaurantes) > 0)
            <div class="space-y-2">
                @foreach ($pendingRestaurantes as $r)
                    <div class="flex items-center justify-between py-1.5 border-b border-stone-100 last:border-0">
                        <div class="min-w-0 flex-1">
                            <p class="text-sm font-bold text-on-surface truncate">{{ $r->nombre }}</p>
                            <p class="text-[10px] text-stone-500">{{ $r->email }} · {{ $r->created_at->diffForHumans() }}</p>
                        </div>
                        <a href="{{ route('admin.restaurantes.edit', $r->id) }}" class="text-xs font-bold text-primary hover:underline shrink-0 ml-2">Gestionar</a>
                    </div>
                @endforeach
            </div>
            <a href="{{ route('admin.restaurantes.index') }}" class="mt-3 inline-block text-xs font-bold text-primary hover:underline">Ver todos →</a>
        @else
            <p class="text-sm text-stone-400 text-center py-6">Sin restaurantes registrados.</p>
        @endif
    </div>
    <div class="bg-surface-container-lowest rounded-2xl shadow-sm border border-stone-100/50 p-5">
        <h4 class="font-headline text-sm font-bold text-on-surface mb-3 flex items-center gap-2">
            <span class="material-symbols-outlined text-teal-500 text-base">group</span>
            Últimos Comensales Registrados
        </h4>
        @if (count($pendingComensales ?? []) > 0)
            <div class="space-y-2">
                @foreach ($pendingComensales as $c)
                    <div class="flex items-center justify-between py-1.5 border-b border-stone-100 last:border-0">
                        <div class="min-w-0 flex-1">
                            <p class="text-sm font-bold text-on-surface truncate">{{ $c->nombre_completo }}</p>
                            <p class="text-[10px] text-stone-500">{{ $c->email }} · {{ $c->created_at->diffForHumans() }}</p>
                        </div>
                        <a href="{{ route('admin.comensales.edit', $c->id) }}" class="text-xs font-bold text-primary hover:underline shrink-0 ml-2">Gestionar</a>
                    </div>
                @endforeach
            </div>
            <a href="{{ route('admin.comensales.index') }}" class="mt-3 inline-block text-xs font-bold text-primary hover:underline">Ver todos →</a>
        @else
            <p class="text-sm text-stone-400 text-center py-6">Sin comensales registrados.</p>
        @endif
    </div>
</div>

{{-- Audit Row --}}
<div class="bg-surface-container-lowest rounded-2xl shadow-sm border border-stone-100/50 p-5">
    <div class="flex items-center justify-between mb-4">
        <h4 class="font-headline text-base font-bold text-on-surface flex items-center gap-2">
            <span class="material-symbols-outlined text-stone-500 text-lg">history</span>
            Últimos eventos
        </h4>
        <a href="{{ route('admin.auditoria.index') }}" class="text-xs font-bold text-primary hover:underline">Ver todo</a>
    </div>
    @if (count($latestAudits) > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-2">
            @foreach ($latestAudits as $e)
                <div class="flex items-start gap-2 p-2.5 rounded-xl bg-stone-50/50 border border-stone-100">
                    <span class="inline-block px-2 py-0.5 rounded-full text-[9px] font-bold uppercase tracking-wider shrink-0 mt-0.5
                        {{ $e['modulo'] === 'backups' ? 'bg-purple-100 text-purple-700' : '' }}
                        {{ $e['modulo'] === 'reportes' ? 'bg-blue-100 text-blue-700' : '' }}
                        {{ $e['modulo'] === 'restaurantes' ? 'bg-orange-100 text-orange-700' : '' }}
                        {{ $e['modulo'] === 'comensales' ? 'bg-teal-100 text-teal-700' : '' }}
                        {{ $e['modulo'] === 'categorias' ? 'bg-pink-100 text-pink-700' : '' }}
                        {{ $e['modulo'] === 'roles' ? 'bg-indigo-100 text-indigo-700' : '' }}
                        {{ $e['modulo'] === 'usuarios' ? 'bg-stone-100 text-stone-700' : '' }}
                        {{ $e['modulo'] === 'auth' ? 'bg-red-100 text-red-700' : '' }}
                    ">{{ $e['modulo'] }}</span>
                    <div class="min-w-0">
                        <p class="text-xs font-medium text-on-surface truncate">{{ $e['descripcion'] ?? '—' }}</p>
                        <p class="text-[10px] text-stone-500 mt-0.5">
                            {{ $e['usuario']['nombre'] ?? '—' }} · {{ \Carbon\Carbon::parse($e['created_at'])->diffForHumans() }}
                        </p>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <p class="text-sm text-stone-400">Sin eventos registrados.</p>
    @endif
</div>
@endsection
