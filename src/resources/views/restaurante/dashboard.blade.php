@extends('layouts.restaurante')

@section('title', 'Panel de Control')
@section('page-title', 'Panel de Control')

@section('content')
@php
    $deltaClass = fn($d) => $d > 0 ? 'text-green-600' : ($d < 0 ? 'text-red-500' : 'text-stone-400');
    $deltaIcon = fn($d) => $d > 0 ? 'arrow_upward' : ($d < 0 ? 'arrow_downward' : 'remove');
@endphp

{{-- Toolbar --}}
<div class="mb-6 flex flex-wrap items-center justify-between gap-4">
    <div class="flex flex-wrap items-center gap-2">
        <form method="GET" action="{{ route('restaurante.dashboard') }}" class="flex items-center gap-3">
            <span class="material-symbols-outlined text-stone-400 text-lg">schedule</span>
            @foreach ([7, 14, 30, 60, 90] as $r)
                <button type="submit" name="range" value="{{ $r }}"
                        class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all cursor-pointer
                               {{ $selectedRange == $r ? 'bg-primary text-white shadow-sm' : 'bg-stone-100 text-stone-600 hover:bg-stone-200' }}">
                    {{ $r }}d
                </button>
            @endforeach
            <input type="hidden" name="scope" value="{{ $scope }}">
        </form>
        <form method="GET" action="{{ route('restaurante.dashboard') }}" class="flex items-center gap-2 ml-2">
            <label class="text-xs font-bold text-stone-500 uppercase tracking-wider">Alcance</label>
            <select name="scope" onchange="this.form.submit()" class="bg-surface-container-highest border-0 rounded-lg py-1.5 px-3 text-xs font-bold text-on-surface focus:ring-2 focus:ring-primary">
                <option value="sucursal_activa" {{ ($scope ?? 'sucursal_activa') === 'sucursal_activa' ? 'selected' : '' }}>Sucursal activa</option>
                <option value="todas_mis_sucursales" {{ ($scope ?? '') === 'todas_mis_sucursales' ? 'selected' : '' }}>Todas mis sucursales</option>
            </select>
            <input type="hidden" name="range" value="{{ $selectedRange }}">
            <noscript><button type="submit" class="bg-primary text-white px-3 py-1.5 rounded-lg text-xs font-bold">Ir</button></noscript>
        </form>
    </div>
    <div class="flex items-center gap-3 text-xs text-stone-500">
        <span class="material-symbols-outlined text-stone-400 text-sm">compare_arrows</span>
        <span>{{ $range['from'] }} — {{ $range['to'] }}</span>
        <span class="text-stone-300 mx-1">|</span>
        <span class="text-stone-400">vs {{ $range['prev_from'] }} — {{ $range['prev_to'] }}</span>
        <a href="{{ route('restaurante.reportes.index') }}" class="flex items-center gap-1.5 bg-stone-100 hover:bg-stone-200 text-stone-600 px-3 py-1.5 rounded-lg text-xs font-bold transition-all ml-2">
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
<div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-6 gap-4 mb-8">
    @php
        $kpiCards = [
            ['key' => 'platosActivos', 'label' => 'Platos Activos', 'icon' => 'restaurant', 'color' => 'text-primary', 'temporal' => false],
            ['key' => 'promocionesActivas', 'label' => 'Promociones', 'icon' => 'local_offer', 'color' => 'text-green-600', 'temporal' => false],
            ['key' => 'visitas', 'label' => 'Visitas', 'icon' => 'footprint', 'color' => 'text-[#C0392B]', 'temporal' => true, 'sparklineColor' => '#6366f1'],
            ['key' => 'resenas', 'label' => 'Reseñas', 'icon' => 'rate_review', 'color' => 'text-tertiary', 'temporal' => true, 'sparklineColor' => '#10b981'],
            ['key' => 'promedioScore', 'label' => 'Score Prom.', 'icon' => 'star', 'color' => 'text-amber-600', 'temporal' => true, 'sparklineColor' => '#f59e0b'],
            ['key' => 'sinStock', 'label' => 'Sin Stock', 'icon' => 'inventory_2', 'color' => 'text-red-500', 'temporal' => false],
        ];
    @endphp
    @foreach ($kpiCards as $card)
        @php
            $k = $kpis[$card['key']];
            $value = $card['key'] === 'promedioScore' ? number_format($k['current'], 1) : number_format($k['current']);
        @endphp
        <x-kpi-card
            :label="$card['label']"
            :value="$value"
            :icon="$card['icon']"
            :color="$card['color']"
            :delta="$card['temporal'] ? $k['delta'] : null"
            :sparkline="$card['temporal'] && count($sparklines[$card['key']] ?? []) > 0 ? $sparklines[$card['key']] : null"
            :sparklineColor="$card['sparklineColor'] ?? '#6366f1'"
        />
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
            $chartCategories = collect($series['visitas_por_dia'] ?? [])->pluck('fecha')->map(fn($d) => \Carbon\Carbon::parse($d)->format('d/m'))->toArray();
        @endphp
        <div id="mainChart"
             data-dashboard-chart
             data-default-tab="actividad"
             data-series-actividad='@json($chartSeriesActividad)'
             data-series-calidad='@json($chartSeriesCalidad)'
             data-categories='@json($chartCategories)'
             class="w-full">
        </div>
    </div>

    <div class="bg-surface-container-lowest rounded-2xl shadow-sm border border-stone-100/50 p-5">
        <h4 class="font-headline text-base font-bold text-on-surface mb-4 flex items-center gap-2">
            <span class="material-symbols-outlined text-amber-500 text-lg">warning</span>
            Alertas
        </h4>
        @if (count($alerts) > 0)
            <div class="space-y-3">
                @foreach ($alerts as $alert)
                    @php
                        $severityMap = ['warning' => 'warning', 'info' => 'info'];
                        $alertUrl = match($alert['type']) {
                            'sin_stock' => route('productos.index'),
                            'promos_vencidas' => route('restaurante.promociones.index'),
                            'perfil_incompleto' => route('restaurante.configuracion', ['#perfil']),
                            'sin_visitas' => route('restaurante.reportes.index', ['range' => 30]),
                            'sin_resenas' => route('restaurante.resenas'),
                            default => null,
                        };
                    @endphp
                    <x-alert-chip
                        :label="$alert['label']"
                        :severity="$severityMap[$alert['severity']] ?? 'info'"
                        :count="$alert['count'] > 1 ? $alert['count'] : null"
                        :href="$alertUrl"
                    />
                @endforeach
            </div>
        @else
            <x-empty-state icon="check_circle" title="Sin novedades" message="Todo en orden." iconSize="text-4xl" />
        @endif
    </div>
</div>

{{-- Distribution Row --}}
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    {{-- Score Distribution --}}
    <div class="bg-surface-container-lowest rounded-2xl shadow-sm border border-stone-100/50 p-5">
        <h5 class="font-headline text-sm font-bold text-on-surface mb-3">Distribución de Score</h5>
        @php
            $dist = $scoreDistribution ?? [];
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
            $pActivas = $promoSummary['activas'] ?? 0;
            $pVencidas = $promoSummary['vencidas'] ?? 0;
            $pInactivas = $promoSummary['inactivas'] ?? 0;
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
            $pAct = $breakdowns['productos_por_estado']['activos'] ?? 0;
            $pIna = $breakdowns['productos_por_estado']['inactivos'] ?? 0;
            $pStock = $breakdowns['productos_por_estado']['sin_stock'] ?? 0;
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

{{-- Rankings + Latest Reviews --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
    {{-- Top platos reseñados --}}
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

    {{-- Mejor calificados --}}
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

    {{-- Productos sin stock --}}
    <div class="bg-surface-container-lowest rounded-2xl shadow-sm border border-stone-100/50 p-5">
        <h4 class="font-headline text-sm font-bold text-on-surface mb-4 flex items-center gap-2">
            <span class="material-symbols-outlined text-red-500 text-base">inventory_2</span>
            Productos sin Stock
        </h4>
        @if (count($tables['productos_sin_stock'] ?? []) > 0)
            <div class="space-y-2">
                @foreach ($tables['productos_sin_stock'] as $item)
                    @php $nombre = $item->nombre ?? $item['nombre'] ?? ''; @endphp
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

{{-- Timeline --}}
@if (count($timeline ?? []) > 0)
<div class="bg-surface-container-lowest rounded-2xl shadow-sm border border-stone-100/50 p-5 mb-8">
    <h4 class="font-headline text-base font-bold text-on-surface mb-4 flex items-center gap-2">
        <span class="material-symbols-outlined text-stone-500 text-lg">timeline</span>
        Actividad Reciente
    </h4>
    <div class="relative">
        <div class="absolute left-4 top-2 bottom-2 w-0.5 bg-stone-200 rounded-full"></div>
        <div class="space-y-4">
            @foreach ($timeline as $event)
                <div class="flex items-start gap-4 pl-0 relative">
                    <div class="relative z-10 w-8 h-8 rounded-full bg-white border-2 border-stone-200 flex items-center justify-center shrink-0">
                        <span class="material-symbols-outlined text-sm {{ $event['iconColor'] }}">{{ $event['icon'] }}</span>
                    </div>
                    <div class="min-w-0 flex-1 pt-1">
                        <p class="text-sm font-bold text-on-surface">{{ $event['label'] }}</p>
                        <p class="text-xs text-stone-500">{{ $event['detail'] }}</p>
                        <p class="text-[10px] text-stone-400 mt-0.5">{{ \Carbon\Carbon::parse($event['time'])->diffForHumans() }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endif

{{-- Latest Reviews --}}
<div class="bg-surface-container-lowest rounded-2xl shadow-sm border border-stone-100/50 p-5 mb-8">
    <div class="flex items-center justify-between mb-4">
        <h4 class="font-headline text-base font-bold text-on-surface flex items-center gap-2">
            <span class="material-symbols-outlined text-amber-500 text-lg">rate_review</span>
            Últimas Reseñas
        </h4>
        <a href="{{ route('restaurante.resenas') }}" class="text-xs font-bold text-primary hover:underline">Ver todas</a>
    </div>
    @if (count($latestReviews) > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            @foreach ($latestReviews as $r)
                <div class="flex items-start gap-3 py-3 px-4 bg-stone-50/50 rounded-xl">
                    <div class="w-9 h-9 rounded-full bg-primary-fixed flex items-center justify-center text-primary font-bold text-sm shrink-0">
                        {{ substr($r['comensal']['nombre'] ?? 'C', 0, 1) }}
                    </div>
                    <div class="min-w-0 flex-1">
                        <div class="flex items-center gap-2">
                            <p class="text-sm font-bold text-on-surface">{{ $r['comensal']['nombre'] ?? 'Comensal' }}</p>
                            <div class="flex items-center gap-0.5">
                                @for ($i = 1; $i <= 5; $i++)
                                    <span class="material-symbols-outlined text-[14px] {{ $i <= $r['score'] ? 'text-amber-400' : 'text-stone-200' }}" style="font-variation-settings: 'FILL' 1;">star</span>
                                @endfor
                            </div>
                        </div>
                        <p class="text-xs text-stone-400">{{ $r['menu']['nombre'] ?? '—' }} · {{ \Carbon\Carbon::parse($r['created_at'])->diffForHumans() }}</p>
                        @if ($r['comentario'])
                            <p class="text-sm text-stone-600 mt-1 line-clamp-2">{{ $r['comentario'] }}</p>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <p class="text-sm text-stone-400 text-center py-6">Aún no tienes reseñas. Invita a tus clientes a dejar su opinión.</p>
    @endif
</div>

{{-- Status Card + Quick Actions --}}
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <div class="md:col-span-2 bg-surface-container-lowest rounded-2xl shadow-sm border border-stone-100/50 p-5 flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
        <div class="flex items-center gap-6">
            <div class="relative w-20 h-20 rounded-2xl overflow-hidden shadow-lg bg-stone-100 shrink-0">
                @if ($restaurante && $restaurante->foto_portada_url)
                    <img class="w-full h-full object-cover" src="{{ $restaurante->foto_portada_url }}" alt="Logo">
                @else
                    <div class="w-full h-full flex items-center justify-center bg-primary-fixed text-primary font-black text-3xl">
                        {{ substr($usuario->nombre, 0, 1) }}
                    </div>
                @endif
            </div>
            <div>
                <p class="text-stone-500 text-xs font-semibold uppercase tracking-widest mb-1">Estado del Servicio</p>
                <h3 class="font-headline font-extrabold text-2xl text-on-surface">{{ $restaurante ? ucfirst($restaurante->estado) : 'Sin configurar' }}</h3>
                <div class="flex items-center gap-2 mt-2">
                    <span class="flex h-2 w-2 rounded-full {{ $restaurante && $restaurante->estado === 'activo' ? 'bg-green-500' : 'bg-amber-400' }}"></span>
                    <p class="text-sm text-stone-600 font-medium">{{ $restaurante ? $restaurante->nombre : $usuario->nombre }}</p>
                </div>
                @if ($restaurante)
                <div class="flex items-center gap-2 mt-1">
                    @if ($restaurante->es_principal)
                        <span class="text-[10px] font-bold bg-amber-100 text-amber-800 px-2 py-0.5 rounded-full uppercase tracking-wider">Principal</span>
                    @endif
                    <span class="text-[10px] text-stone-400">Sucursal activa</span>
                </div>
                @endif
            </div>
        </div>
        <div class="flex items-center gap-3 w-full md:w-auto">
            <button type="button" data-modal-open="restaurante-producto-create-modal" class="flex-1 md:flex-none bg-primary text-white px-4 py-2 rounded-lg text-xs font-bold hover:bg-[#c0392b] transition-colors text-center">+ Añadir Plato</button>
        </div>
    </div>
    <div class="bg-primary-container text-on-primary-container rounded-2xl shadow-sm p-5 relative overflow-hidden">
        <div class="relative z-10">
            <p class="text-[10px] font-bold opacity-80 uppercase tracking-tighter">Perfil del Local</p>
            <h4 class="font-headline font-bold text-lg mt-1">{{ $restaurante ? 'Configurado' : '¡Configura tu local!' }}</h4>
            <p class="text-sm opacity-90 mt-1">{{ $restaurante && $restaurante->zona ? 'Zona: ' . $restaurante->zona : 'Completa tu perfil público.' }}</p>
            <a href="{{ route('restaurante.configuracion') }}" class="mt-3 inline-block text-xs font-bold underline decoration-2 underline-offset-4">Ver Ajustes →</a>
        </div>
        <span class="material-symbols-outlined absolute -bottom-4 -right-4 text-8xl opacity-10 rotate-12">settings</span>
    </div>
</div>

{{-- Branch Summary --}}
@if ($branchSummary)
<div class="bg-surface-container-lowest rounded-2xl shadow-sm border border-stone-100/50 p-5 mb-8">
    <h4 class="font-headline text-sm font-bold text-on-surface mb-3 flex items-center gap-2">
        <span class="material-symbols-outlined text-secondary text-lg">store</span>
        {{ $branchSummary->nombre }}
    </h4>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm text-stone-600">
        @if ($branchSummary->zona)
        <div class="flex items-center gap-2">
            <span class="material-symbols-outlined text-stone-400 text-[16px]">location_on</span>
            <span>{{ $branchSummary->zona }}{{ $branchSummary->direccion ? ' - ' . $branchSummary->direccion : '' }}</span>
        </div>
        @endif
        @if ($branchSummary->horario_apertura)
        <div class="flex items-center gap-2">
            <span class="material-symbols-outlined text-stone-400 text-[16px]">schedule</span>
            <span>{{ $branchSummary->horario_apertura }} – {{ $branchSummary->horario_cierre }}</span>
        </div>
        @endif
        @if ($branchSummary->telefono)
        <div class="flex items-center gap-2">
            <span class="material-symbols-outlined text-stone-400 text-[16px]">call</span>
            <span>{{ $branchSummary->telefono }}</span>
        </div>
        @endif
    </div>
    <div class="mt-3 flex gap-3">
        <a href="{{ route('restaurante.configuracion') }}" class="text-xs font-bold bg-stone-100 px-4 py-2 rounded-lg hover:bg-stone-200 transition-colors">Editar</a>
        <a href="{{ route('restaurante.sucursales.index') }}" class="text-xs font-bold bg-stone-100 px-4 py-2 rounded-lg hover:bg-stone-200 transition-colors">Sucursales</a>
        <a href="{{ route('restaurante.reportes.index') }}" class="text-xs font-bold bg-stone-100 px-4 py-2 rounded-lg hover:bg-stone-200 transition-colors">Reportes</a>
    </div>
</div>
@endif

{{-- Menu Table --}}
<div class="grid grid-cols-1 xl:grid-cols-3 gap-8">
    <div class="xl:col-span-2 space-y-4">
        <div class="flex justify-between items-end px-2">
            <div>
                <h3 class="font-headline font-bold text-lg text-on-surface">Gestión de Menú</h3>
                <p class="text-sm text-stone-500">Administra la visibilidad y precios de tus platos</p>
            </div>
            <button type="button" data-modal-open="restaurante-producto-create-modal" class="flex items-center gap-2 bg-primary text-white py-2 px-4 rounded-lg font-headline font-bold text-sm shadow-md hover:bg-[#c0392b] transition-colors">
                <span class="material-symbols-outlined text-sm">add</span> Nuevo Plato
            </button>
        </div>
        <div class="bg-surface-container-lowest rounded-2xl shadow-sm overflow-hidden">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-surface-container-low/50">
                        <th class="px-6 py-4 text-xs font-bold text-stone-500 uppercase tracking-wider">Plato</th>
                        <th class="px-6 py-4 text-xs font-bold text-stone-500 uppercase tracking-wider">Categoría</th>
                        <th class="px-6 py-4 text-xs font-bold text-stone-500 uppercase tracking-wider">Precio</th>
                        <th class="px-6 py-4 text-xs font-bold text-stone-500 uppercase tracking-wider">Estado</th>
                        <th class="px-6 py-4 text-xs font-bold text-stone-500 uppercase tracking-wider text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100">
                    @forelse($productos as $producto)
                    <tr class="hover:bg-stone-50/50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-lg overflow-hidden shrink-0 bg-surface-container flex items-center justify-center">
                                    @if($producto->foto_url)
                                        <img class="w-full h-full object-cover" src="{{ $producto->foto_url }}" alt="{{ $producto->nombre }}">
                                    @else
                                        <span class="text-primary font-bold text-lg">{{ substr($producto->nombre, 0, 1) }}</span>
                                    @endif
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-on-surface">{{ $producto->nombre }}</p>
                                    @if($producto->stock !== null)
                                        <p class="text-[10px] text-stone-400">Stock: {{ $producto->stock }}</p>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-stone-600">{{ $producto->categoria?->nombre_categoria ?? '—' }}</td>
                        <td class="px-6 py-4 text-sm font-bold text-on-surface">Bs {{ number_format($producto->precio, 2) }}</td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center gap-1 text-xs font-bold {{ $producto->activo ? 'text-green-600' : 'text-stone-400' }}">
                                <span class="flex h-1.5 w-1.5 rounded-full {{ $producto->activo ? 'bg-green-500' : 'bg-stone-300' }}"></span>
                                {{ $producto->activo ? 'Activo' : 'Inactivo' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <button type="button" data-modal-open="restaurante-producto-edit-modal-{{ $producto->id }}" class="text-xs font-bold text-primary hover:underline">Editar</button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-sm text-stone-400">Aún no tienes platos agregados. ¡Crea tu primer plato!</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($productos->hasPages())
            <div class="px-2">{{ $productos->links() }}</div>
        @endif
    </div>

    {{-- Sidebar: quick stats & links --}}
    <div class="space-y-4">
        <div class="bg-surface-container-lowest rounded-2xl shadow-sm border border-stone-100/50 p-5">
            <h4 class="font-headline text-sm font-bold text-on-surface mb-3">Resumen Rápido</h4>
            <div class="space-y-3 text-sm">
                <div class="flex justify-between">
                    <span class="text-stone-500">Total platos</span>
                    <span class="font-bold text-on-surface">{{ $totalProductos }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-stone-500">Activos</span>
                    <span class="font-bold text-green-600">{{ $breakdowns['productos_por_estado']['activos'] ?? 0 }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-stone-500">Sin stock</span>
                    <span class="font-bold text-red-500">{{ $breakdowns['productos_por_estado']['sin_stock'] ?? 0 }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-stone-500">Promociones activas</span>
                    <span class="font-bold text-green-600">{{ $promoSummary['activas'] ?? 0 }}</span>
                </div>
            </div>
        </div>
        <div class="bg-surface-container-lowest rounded-2xl shadow-sm border border-stone-100/50 p-5">
            <h4 class="font-headline text-sm font-bold text-on-surface mb-3">Accesos Rápidos</h4>
            <div class="space-y-2">
                <a href="{{ route('restaurante.configuracion') }}" class="w-full flex items-center gap-2 bg-surface-container py-3 px-4 rounded-xl font-bold text-sm text-on-surface hover:bg-surface-container-high transition-colors">
                    <span class="material-symbols-outlined text-stone-500 text-lg">settings</span> Configuración
                </a>
                <a href="{{ route('restaurante.sucursales.index') }}" class="w-full flex items-center gap-2 bg-surface-container py-3 px-4 rounded-xl font-bold text-sm text-on-surface hover:bg-surface-container-high transition-colors">
                    <span class="material-symbols-outlined text-stone-500 text-lg">store</span> Sucursales
                </a>
                <a href="{{ route('restaurante.promociones.index') }}" class="w-full flex items-center gap-2 bg-surface-container py-3 px-4 rounded-xl font-bold text-sm text-on-surface hover:bg-surface-container-high transition-colors">
                    <span class="material-symbols-outlined text-stone-500 text-lg">local_offer</span> Promociones
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
