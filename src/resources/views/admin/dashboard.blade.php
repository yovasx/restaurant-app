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
        @if(count($rankings['top_visitas']) > 0)
            <div class="space-y-2">
                @foreach($rankings['top_visitas'] as $i => $item)
                    <div class="flex items-center justify-between py-1.5 border-b border-stone-100 last:border-0">
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-bold text-stone-400 w-5">{{ $i + 1 }}</span>
                            <span class="text-sm font-medium text-on-surface">{{ $item->nombre }}</span>
                        </div>
                        <span class="text-sm font-bold text-primary">{{ $item->total }}</span>
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

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div class="bg-surface-container-lowest rounded-2xl shadow-sm border border-stone-100/50 p-5">
        <h4 class="font-headline text-base font-bold text-on-surface mb-4 flex items-center gap-2">
            <span class="material-symbols-outlined text-amber-500 text-lg">stars</span>
            Distribución de Score
        </h4>
        @php $distTotal = array_sum($rankings['distribucion_score']); @endphp
        @if($distTotal > 0)
            <div class="space-y-2">
                @foreach($rankings['distribucion_score'] as $score => $total)
                    @php $pct = round($total / $distTotal * 100); @endphp
                    <div class="flex items-center gap-3">
                        <span class="text-xs font-bold text-stone-500 w-6">{{ $score }}★</span>
                        <div class="flex-1 h-4 bg-stone-100 rounded-full overflow-hidden">
                            <div class="h-full rounded-full {{ $score >= 4 ? 'bg-green-500' : ($score >= 3 ? 'bg-yellow-500' : 'bg-red-500') }}" style="width: {{ $pct }}%"></div>
                        </div>
                        <span class="text-xs font-bold text-stone-500 w-10 text-right">{{ $total }}</span>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-sm text-stone-400">Sin reseñas en los últimos 30 días.</p>
        @endif
    </div>

    <div class="bg-surface-container-lowest rounded-2xl shadow-sm border border-stone-100/50 p-5">
        <h4 class="font-headline text-base font-bold text-on-surface mb-4">Inventario</h4>
        <div class="grid grid-cols-2 gap-4">
            <div class="bg-green-50 rounded-xl p-4 text-center">
                <p class="text-2xl font-black text-green-600">{{ $rankings['productos']['activos'] ?? 0 }}</p>
                <p class="text-xs font-bold text-stone-500 uppercase mt-1">Productos Activos</p>
            </div>
            <div class="bg-red-50 rounded-xl p-4 text-center">
                <p class="text-2xl font-black text-red-500">{{ $rankings['productos']['sin_stock'] ?? 0 }}</p>
                <p class="text-xs font-bold text-stone-500 uppercase mt-1">Sin Stock</p>
            </div>
            <div class="bg-green-50 rounded-xl p-4 text-center">
                <p class="text-2xl font-black text-green-600">{{ count($rankings['promociones']['activas'] ?? []) }}</p>
                <p class="text-xs font-bold text-stone-500 uppercase mt-1">Promociones Activas</p>
            </div>
            <div class="bg-red-50 rounded-xl p-4 text-center">
                <p class="text-2xl font-black text-red-500">{{ count($rankings['promociones']['vencidas'] ?? []) }}</p>
                <p class="text-xs font-bold text-stone-500 uppercase mt-1">Vencidas</p>
            </div>
        </div>
    </div>
</div>
@endsection
