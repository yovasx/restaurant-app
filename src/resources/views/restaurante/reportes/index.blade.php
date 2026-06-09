@extends('layouts.restaurante')

@section('title', 'Reportes')
@section('page-title', 'Reportes')

@section('content')
<header class="mb-8">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-3xl font-extrabold text-on-surface tracking-tight mb-1">Reportes</h2>
            <p class="text-on-surface-variant font-medium">{{ $filters['from']->format('d/m/Y') }} — {{ $filters['to']->format('d/m/Y') }}</p>
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
</header>

<form method="GET" action="{{ route('restaurante.reportes.index') }}" class="bg-surface-container-lowest p-5 rounded-2xl shadow-sm border border-stone-100/50 mb-8">
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

<div class="grid grid-cols-2 lg:grid-cols-6 gap-4 mb-8">
    <div class="bg-surface-container-lowest p-5 rounded-2xl shadow-sm border border-stone-100/50">
        <p class="text-xs font-bold text-stone-500 uppercase tracking-wider mb-1">Platos Activos</p>
        <h3 class="text-2xl font-black text-on-surface">{{ $kpis['productos_activos'] }}</h3>
    </div>
    <div class="bg-surface-container-lowest p-5 rounded-2xl shadow-sm border border-stone-100/50">
        <p class="text-xs font-bold text-stone-500 uppercase tracking-wider mb-1">Sin Stock</p>
        <h3 class="text-2xl font-black text-on-surface">{{ $kpis['productos_sin_stock'] }}</h3>
    </div>
    <div class="bg-surface-container-lowest p-5 rounded-2xl shadow-sm border border-stone-100/50">
        <p class="text-xs font-bold text-stone-500 uppercase tracking-wider mb-1">Promociones Activas</p>
        <h3 class="text-2xl font-black text-on-surface">{{ $kpis['promociones_activas'] }}</h3>
    </div>
    <div class="bg-surface-container-lowest p-5 rounded-2xl shadow-sm border border-stone-100/50">
        <p class="text-xs font-bold text-stone-500 uppercase tracking-wider mb-1">Visitas</p>
        <h3 class="text-2xl font-black text-on-surface">{{ $kpis['visitas'] }}</h3>
    </div>
    <div class="bg-surface-container-lowest p-5 rounded-2xl shadow-sm border border-stone-100/50">
        <p class="text-xs font-bold text-stone-500 uppercase tracking-wider mb-1">Reseñas</p>
        <h3 class="text-2xl font-black text-on-surface">{{ $kpis['resenas'] }}</h3>
    </div>
    <div class="bg-surface-container-lowest p-5 rounded-2xl shadow-sm border border-stone-100/50">
        <p class="text-xs font-bold text-stone-500 uppercase tracking-wider mb-1">Score Promedio</p>
        <h3 class="text-2xl font-black text-on-surface">{{ number_format($kpis['promedio_score'] ?? 0, 1) }}</h3>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div class="bg-surface-container-lowest rounded-2xl shadow-sm border border-stone-100/50 p-5">
        <h4 class="text-lg font-bold text-on-surface mb-4">Actividad</h4>
        @php
            $visitas = $series['visitas_por_dia'] ?? [];
            $resenas = $series['resenas_por_dia'] ?? [];
        @endphp
        @if(count($visitas) > 0 || count($resenas) > 0)
            @include('admin.partials._activity-chart', [
                'visitas' => $visitas,
                'resenas' => $resenas,
            ])
        @else
            <p class="text-sm text-stone-400">Sin datos en este rango.</p>
        @endif
    </div>

    <div class="bg-surface-container-lowest rounded-2xl shadow-sm border border-stone-100/50 p-5">
        <h4 class="text-lg font-bold text-on-surface mb-4">Top Platos más Reseñados</h4>
        @if(count($tables['top_platos_resenas']) > 0)
            <div class="space-y-2">
                @foreach($tables['top_platos_resenas'] as $i => $item)
                    <div class="flex items-center justify-between py-2 border-b border-stone-100 last:border-0">
                        <div class="flex items-center gap-3">
                            <span class="text-xs font-bold text-stone-400 w-5">{{ $i + 1 }}</span>
                            <span class="font-medium text-on-surface text-sm">{{ $item->nombre }}</span>
                        </div>
                        <span class="text-sm font-bold text-primary">{{ $item->total }}</span>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-sm text-stone-400">Sin datos en este rango.</p>
        @endif
    </div>

    <div class="bg-surface-container-lowest rounded-2xl shadow-sm border border-stone-100/50 p-5">
        <h4 class="text-lg font-bold text-on-surface mb-4">Mejor Calificados</h4>
        @if(count($tables['top_platos_score']) > 0)
            <div class="space-y-2">
                @foreach($tables['top_platos_score'] as $i => $item)
                    <div class="flex items-center justify-between py-2 border-b border-stone-100 last:border-0">
                        <div class="flex items-center gap-3">
                            <span class="text-xs font-bold text-stone-400 w-5">{{ $i + 1 }}</span>
                            <span class="font-medium text-on-surface text-sm">{{ $item->nombre }}</span>
                        </div>
                        <span class="text-sm font-bold text-green-600">{{ number_format($item->promedio, 1) }} ({{ $item->total_resenas }})</span>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-sm text-stone-400">Sin datos suficientes.</p>
        @endif
    </div>

    <div class="bg-surface-container-lowest rounded-2xl shadow-sm border border-stone-100/50 p-5">
        <h4 class="text-lg font-bold text-on-surface mb-4">Distribución Score</h4>
        @if(array_sum($breakdowns['score_distribution']) > 0)
            <div class="space-y-2">
                @foreach($breakdowns['score_distribution'] as $score => $total)
                    @php $pct = array_sum($breakdowns['score_distribution']) > 0 ? round($total / array_sum($breakdowns['score_distribution']) * 100) : 0; @endphp
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
            <p class="text-sm text-stone-400">Sin reseñas en este rango.</p>
        @endif
    </div>

    <div class="bg-surface-container-lowest rounded-2xl shadow-sm border border-stone-100/50 p-5">
        <h4 class="text-lg font-bold text-on-surface mb-4">Promociones</h4>
        <div class="grid grid-cols-3 gap-3 text-center">
            <div class="bg-green-50 rounded-xl p-4">
                <p class="text-2xl font-black text-green-600">{{ count($breakdowns['promociones_por_estado']['activas']) }}</p>
                <p class="text-xs font-bold text-stone-500 uppercase mt-1">Activas</p>
            </div>
            <div class="bg-stone-50 rounded-xl p-4">
                <p class="text-2xl font-black text-stone-500">{{ count($breakdowns['promociones_por_estado']['inactivas']) }}</p>
                <p class="text-xs font-bold text-stone-500 uppercase mt-1">Inactivas</p>
            </div>
            <div class="bg-red-50 rounded-xl p-4">
                <p class="text-2xl font-black text-red-500">{{ count($breakdowns['promociones_por_estado']['vencidas']) }}</p>
                <p class="text-xs font-bold text-stone-500 uppercase mt-1">Vencidas</p>
            </div>
        </div>
    </div>

    <div class="bg-surface-container-lowest rounded-2xl shadow-sm border border-stone-100/50 p-5">
        <h4 class="text-lg font-bold text-on-surface mb-4">Productos</h4>
        <div class="grid grid-cols-3 gap-3 text-center">
            <div class="bg-green-50 rounded-xl p-4">
                <p class="text-2xl font-black text-green-600">{{ $breakdowns['productos_por_estado']['activos'] }}</p>
                <p class="text-xs font-bold text-stone-500 uppercase mt-1">Activos</p>
            </div>
            <div class="bg-stone-50 rounded-xl p-4">
                <p class="text-2xl font-black text-stone-500">{{ $breakdowns['productos_por_estado']['inactivos'] }}</p>
                <p class="text-xs font-bold text-stone-500 uppercase mt-1">Inactivos</p>
            </div>
            <div class="bg-red-50 rounded-xl p-4">
                <p class="text-2xl font-black text-red-500">{{ $breakdowns['productos_por_estado']['sin_stock'] }}</p>
                <p class="text-xs font-bold text-stone-500 uppercase mt-1">Sin Stock</p>
            </div>
        </div>
    </div>

    @if(count($tables['productos_sin_stock']) > 0)
    <div class="bg-surface-container-lowest rounded-2xl shadow-sm border border-stone-100/50 p-5 lg:col-span-2">
        <h4 class="text-lg font-bold text-on-surface mb-4">Productos Sin Stock</h4>
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-stone-500 text-xs font-bold uppercase tracking-wider">
                    <th class="pb-3">Nombre</th>
                </tr>
            </thead>
            <tbody>
                @foreach($tables['productos_sin_stock'] as $item)
                <tr class="border-t border-stone-100">
                    <td class="py-2 font-medium text-on-surface">{{ $item['nombre'] }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
</div>
@endsection
