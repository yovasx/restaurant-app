@extends('layouts.admin')

@section('content')
<header class="mb-8">
    <h2 class="text-3xl font-extrabold text-on-surface tracking-tight mb-1">Panel de Control</h2>
    <p class="text-on-surface-variant font-medium">Monitoreando el pulso gastronómico.</p>
</header>

<div class="grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4 mb-8">
    <div class="bg-surface-container-lowest p-4 rounded-2xl shadow-sm border border-stone-100/50">
        <div class="flex items-center justify-between mb-2">
            <span class="material-symbols-outlined text-primary">restaurant</span>
        </div>
        <p class="text-[10px] font-bold text-stone-500 uppercase tracking-wider">Restaurantes Activos</p>
        <h3 class="text-2xl font-black text-on-surface">{{ $kpis['restaurantesActivos'] }}</h3>
    </div>
    <div class="bg-surface-container-lowest p-4 rounded-2xl shadow-sm border border-stone-100/50">
        <div class="flex items-center justify-between mb-2">
            <span class="material-symbols-outlined text-secondary">group</span>
        </div>
        <p class="text-[10px] font-bold text-stone-500 uppercase tracking-wider">Comensales Activos</p>
        <h3 class="text-2xl font-black text-on-surface">{{ $kpis['comensalesActivos'] }}</h3>
    </div>
    <div class="bg-surface-container-lowest p-4 rounded-2xl shadow-sm border border-stone-100/50">
        <div class="flex items-center justify-between mb-2">
            <span class="material-symbols-outlined text-[#C0392B]">footprint</span>
        </div>
        <p class="text-[10px] font-bold text-stone-500 uppercase tracking-wider">Visitas (30d)</p>
        <h3 class="text-2xl font-black text-on-surface">{{ $kpis['visitas30d'] }}</h3>
    </div>
    <div class="bg-surface-container-lowest p-4 rounded-2xl shadow-sm border border-stone-100/50">
        <div class="flex items-center justify-between mb-2">
            <span class="material-symbols-outlined text-tertiary">rate_review</span>
        </div>
        <p class="text-[10px] font-bold text-stone-500 uppercase tracking-wider">Reseñas (30d)</p>
        <h3 class="text-2xl font-black text-on-surface">{{ $kpis['resenas30d'] }}</h3>
    </div>
    <div class="bg-surface-container-lowest p-4 rounded-2xl shadow-sm border border-stone-100/50">
        <div class="flex items-center justify-between mb-2">
            <span class="material-symbols-outlined text-amber-600">star</span>
        </div>
        <p class="text-[10px] font-bold text-stone-500 uppercase tracking-wider">Promedio Score</p>
        <h3 class="text-2xl font-black text-on-surface">{{ number_format($kpis['promedioScore'] ?? 0, 1) }}</h3>
    </div>
    <div class="bg-primary text-on-primary p-4 rounded-2xl shadow-lg shadow-primary/20">
        <div class="flex items-center justify-between mb-2">
            <span class="material-symbols-outlined text-on-primary/80">backup</span>
        </div>
        <p class="text-[10px] font-bold text-on-primary/70 uppercase tracking-wider">Backups</p>
        <h3 class="text-2xl font-black">{{ $kpis['backups'] }}</h3>
    </div>
</div>

<div class="mb-8">
    <div class="bg-surface-container-lowest rounded-2xl shadow-sm border border-stone-100/50 p-5">
        <h4 class="font-headline text-base font-bold text-on-surface mb-4 flex items-center gap-2">
            <span class="material-symbols-outlined text-indigo-500 text-lg">monitoring</span>
            Actividad 30 días
        </h4>
        @if(count($series['visitas_por_dia'] ?? []) > 0 || count($series['resenas_por_dia'] ?? []) > 0)
            @include('admin.partials._activity-chart', [
                'visitas' => $series['visitas_por_dia'] ?? [],
                'resenas' => $series['resenas_por_dia'] ?? [],
            ])
        @else
            <p class="text-sm text-stone-400">Sin datos de actividad en los últimos 30 días.</p>
        @endif
    </div>
</div>

{{-- Donuts row --}}
@php
    $scoreSegments = [];
    $distTotal = array_sum($rankings['distribucion_score'] ?? []);
    $scoreColors = [1 => '#ef4444', 2 => '#f97316', 3 => '#eab308', 4 => '#84cc16', 5 => '#22c55e'];
    foreach (($rankings['distribucion_score'] ?? []) as $score => $total) {
        if ($total > 0) {
            $scoreSegments[] = ['label' => $score . '★', 'value' => $total, 'color' => $scoreColors[$score] ?? '#a1a1aa'];
        }
    }

    $promoSegments = [];
    $promoMap = ['activas' => ['label' => 'Activas', 'color' => '#22c55e'], 'inactivas' => ['label' => 'Inactivas', 'color' => '#a1a1aa'], 'vencidas' => ['label' => 'Vencidas', 'color' => '#ef4444']];
    foreach ($promoMap as $key => $cfg) {
        $count = $key === 'activas' ? count($rankings['promociones']['activas'] ?? []) : ($key === 'vencidas' ? count($rankings['promociones']['vencidas'] ?? []) : count($rankings['promociones']['inactivas'] ?? []));
        if ($count > 0) {
            $promoSegments[] = ['label' => $cfg['label'], 'value' => $count, 'color' => $cfg['color']];
        }
    }

    $prodSegments = [];
    $prodMap = ['activos' => ['label' => 'Activos', 'color' => '#22c55e'], 'inactivos' => ['label' => 'Inactivos', 'color' => '#a1a1aa'], 'sin_stock' => ['label' => 'Sin stock', 'color' => '#ef4444']];
    foreach ($prodMap as $key => $cfg) {
        $count = (int) ($rankings['productos'][$key] ?? 0);
        if ($count > 0) {
            $prodSegments[] = ['label' => $cfg['label'], 'value' => $count, 'color' => $cfg['color']];
        }
    }
@endphp

<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <div class="bg-surface-container-lowest rounded-2xl shadow-sm border border-stone-100/50 p-4">
        <h5 class="font-headline text-sm font-bold text-on-surface mb-3 text-center">Distribución de Score</h5>
        @if (count($scoreSegments) > 0)
            @include('admin.partials._donut-chart', ['segments' => $scoreSegments])
        @else
            <p class="text-sm text-stone-400 text-center">Sin reseñas en los últimos 30 días.</p>
        @endif
    </div>
    <div class="bg-surface-container-lowest rounded-2xl shadow-sm border border-stone-100/50 p-4">
        <h5 class="font-headline text-sm font-bold text-on-surface mb-3 text-center">Promociones</h5>
        @if (count($promoSegments) > 0)
            @include('admin.partials._donut-chart', ['segments' => $promoSegments])
        @else
            <p class="text-sm text-stone-400 text-center">Sin promociones registradas.</p>
        @endif
    </div>
    <div class="bg-surface-container-lowest rounded-2xl shadow-sm border border-stone-100/50 p-4">
        <h5 class="font-headline text-sm font-bold text-on-surface mb-3 text-center">Productos</h5>
        @if (count($prodSegments) > 0)
            @include('admin.partials._donut-chart', ['segments' => $prodSegments])
        @else
            <p class="text-sm text-stone-400 text-center">Sin productos registrados.</p>
        @endif
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    <div class="bg-surface-container-lowest rounded-2xl shadow-sm border border-stone-100/50 p-5">
        <h4 class="font-headline text-base font-bold text-on-surface mb-4 flex items-center gap-2">
            <span class="material-symbols-outlined text-amber-600 text-lg">warning</span>
            Atención Requerida
        </h4>
        @php $hasAlerts = $alerts['sinStock']['total'] > 0 || $alerts['promocionesVencidas']['total'] > 0 || $alerts['baneados']['total'] > 0 || $alerts['sinVisitas']['total'] > 0; @endphp
        @if($hasAlerts)
            <div class="space-y-4">
                @if($alerts['sinStock']['total'] > 0)
                <div>
                    <p class="text-xs font-bold text-red-600 uppercase tracking-wider mb-1">{{ $alerts['sinStock']['total'] }} producto(s) sin stock</p>
                    @foreach($alerts['sinStock']['items'] as $item)
                        <p class="text-sm text-stone-600 ml-2">• {{ $item->nombre }}</p>
                    @endforeach
                </div>
                @endif
                @if($alerts['promocionesVencidas']['total'] > 0)
                <div>
                    <p class="text-xs font-bold text-red-600 uppercase tracking-wider mb-1">{{ $alerts['promocionesVencidas']['total'] }} promoción(es) vencida(s)</p>
                    @foreach($alerts['promocionesVencidas']['items'] as $item)
                        <p class="text-sm text-stone-600 ml-2">• {{ $item->nombre }}</p>
                    @endforeach
                </div>
                @endif
                @if($alerts['baneados']['total'] > 0)
                <div>
                    <p class="text-xs font-bold text-red-600 uppercase tracking-wider mb-1">{{ $alerts['baneados']['total'] }} restaurante(s) baneado(s)</p>
                    @foreach($alerts['baneados']['items'] as $item)
                        <p class="text-sm text-stone-600 ml-2">• {{ $item->nombre }}</p>
                    @endforeach
                </div>
                @endif
                @if($alerts['sinVisitas']['total'] > 0)
                <div>
                    <p class="text-xs font-bold text-amber-600 uppercase tracking-wider mb-1">{{ $alerts['sinVisitas']['total'] }} restaurante(s) sin visitas en 30 días</p>
                    @foreach($alerts['sinVisitas']['items'] as $item)
                        <p class="text-sm text-stone-600 ml-2">• {{ $item->nombre }}</p>
                    @endforeach
                </div>
                @endif
            </div>
        @else
            <p class="text-sm text-stone-400">Sin novedades. Todo en orden.</p>
        @endif
    </div>

    <div class="bg-surface-container-lowest rounded-2xl shadow-sm border border-stone-100/50 p-5">
        <div class="flex items-center justify-between mb-4">
            <h4 class="font-headline text-base font-bold text-on-surface flex items-center gap-2">
                <span class="material-symbols-outlined text-stone-500 text-lg">history</span>
                Últimos eventos de auditoría
            </h4>
            <a href="{{ route('admin.auditoria.index') }}" class="text-xs font-bold text-primary hover:underline">Ver todo</a>
        </div>
        <div class="space-y-3">
            @if(count($latestAudits) > 0)
                @foreach($latestAudits as $e)
                <div class="flex items-start gap-3 py-2 border-b border-stone-100 last:border-0">
                    <div class="shrink-0 mt-0.5">
                        <span class="inline-block px-2 py-0.5 rounded-full text-[9px] font-bold uppercase tracking-wider 
                            {{ $e['modulo'] === 'backups' ? 'bg-purple-100 text-purple-700' : '' }}
                            {{ $e['modulo'] === 'reportes' ? 'bg-blue-100 text-blue-700' : '' }}
                            {{ $e['modulo'] === 'restaurantes' ? 'bg-orange-100 text-orange-700' : '' }}
                            {{ $e['modulo'] === 'comensales' ? 'bg-teal-100 text-teal-700' : '' }}
                            {{ $e['modulo'] === 'categorias' ? 'bg-pink-100 text-pink-700' : '' }}
                            {{ $e['modulo'] === 'roles' ? 'bg-indigo-100 text-indigo-700' : '' }}
                            {{ $e['modulo'] === 'usuarios' ? 'bg-stone-100 text-stone-700' : '' }}
                            {{ $e['modulo'] === 'auth' ? 'bg-red-100 text-red-700' : '' }}
                        ">{{ $e['modulo'] }}</span>
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-sm font-medium text-on-surface truncate">{{ $e['descripcion'] ?? '—' }}</p>
                        <p class="text-xs text-stone-500 mt-0.5">
                            {{ $e['usuario']['nombre'] ?? '—' }} · {{ \Carbon\Carbon::parse($e['created_at'])->diffForHumans() }}
                        </p>
                    </div>
                </div>
                @endforeach
            @else
                <p class="text-sm text-stone-400">Sin eventos de auditoría registrados.</p>
            @endif
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    <div class="bg-surface-container-lowest rounded-2xl shadow-sm border border-stone-100/50 p-5">
        <h4 class="font-headline text-base font-bold text-on-surface mb-4 flex items-center gap-2">
            <span class="material-symbols-outlined text-primary text-lg">trending_up</span>
            Top Restaurantes por Visitas
        </h4>
        @php $maxVisitas = count($rankings['top_visitas']) > 0 ? max(array_map(fn($i) => $i->total, $rankings['top_visitas'])) : 1; @endphp
        @if(count($rankings['top_visitas']) > 0)
            <div class="space-y-3">
                @foreach($rankings['top_visitas'] as $i => $item)
                    @php $pct = round($item->total / $maxVisitas * 100, 1); @endphp
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <div class="flex items-center gap-2 min-w-0">
                                <span class="text-xs font-bold text-stone-400 w-5 shrink-0">{{ $i + 1 }}</span>
                                <span class="text-sm font-medium text-on-surface truncate">{{ $item->nombre }}</span>
                            </div>
                            <span class="text-sm font-bold text-primary shrink-0 ml-2">{{ $item->total }}</span>
                        </div>
                        <div class="w-full h-2 bg-stone-100 rounded-full overflow-hidden">
                            <div class="h-full rounded-full bg-gradient-to-r from-indigo-400 to-indigo-600 transition-all" style="width: {{ $pct }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-sm text-stone-400">Sin datos en los últimos 30 días.</p>
        @endif
    </div>

    <div class="bg-surface-container-lowest rounded-2xl shadow-sm border border-stone-100/50 p-5">
        <h4 class="font-headline text-base font-bold text-on-surface mb-4 flex items-center gap-2">
            <span class="material-symbols-outlined text-red-500 text-lg">trending_down</span>
            Peor Calificados
        </h4>
        @if(count($rankings['peor_rating']) > 0)
            <div class="space-y-2">
                @foreach($rankings['peor_rating'] as $i => $item)
                    <div class="flex items-center justify-between py-1.5 border-b border-stone-100 last:border-0">
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-bold text-stone-400 w-5">{{ $i + 1 }}</span>
                            <span class="text-sm font-medium text-on-surface">{{ $item->nombre }}</span>
                        </div>
                        <span class="text-sm font-bold text-red-500">{{ number_format($item->promedio, 1) }} ({{ $item->total_resenas }})</span>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-sm text-stone-400">Sin datos suficientes.</p>
        @endif
    </div>
</div>
@endsection
