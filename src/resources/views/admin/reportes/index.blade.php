@extends('layouts.admin')

@section('content')
<header class="mb-8">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-3xl font-extrabold text-on-surface tracking-tight mb-1">Reportes Globales</h2>
            <p class="text-on-surface-variant font-medium">{{ $filters['from']->format('d/m/Y') }} — {{ $filters['to']->format('d/m/Y') }}</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('admin.reportes.export.excel', request()->only(['from', 'to', 'restaurante_id', 'estado_restaurante', 'score'])) }}" class="inline-flex items-center gap-2 bg-primary text-on-primary px-5 py-2.5 rounded-xl font-bold text-sm shadow-lg shadow-primary/20 hover:bg-primary-container transition-all">
                <span class="material-symbols-outlined" style="font-size: 18px;">table</span>
                Exportar Excel
            </a>
            <a href="{{ route('admin.reportes.export.pdf', request()->only(['from', 'to', 'restaurante_id', 'estado_restaurante', 'score'])) }}" class="inline-flex items-center gap-2 bg-[#C0392B] text-white px-5 py-2.5 rounded-xl font-bold text-sm shadow-lg shadow-[#C0392B]/20 hover:bg-[#a32e22] transition-all">
                <span class="material-symbols-outlined" style="font-size: 18px;">picture_as_pdf</span>
                Exportar PDF
            </a>
        </div>
    </div>
</header>

<form method="GET" action="{{ route('admin.reportes.index') }}" class="bg-surface-container-lowest p-5 rounded-2xl shadow-sm border border-stone-100/50 mb-8">
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
            <label class="text-xs font-bold text-stone-500 uppercase tracking-wider mb-1 block">Restaurante</label>
            <select name="restaurante_id" class="w-full bg-surface-container-highest border-0 rounded-lg p-3 text-sm focus:ring-2 focus:ring-primary focus:bg-surface-container-lowest transition-all">
                <option value="">Todos</option>
                @foreach($restaurantes as $r)
                    <option value="{{ $r->id }}" {{ request('restaurante_id') == $r->id ? 'selected' : '' }}>{{ $r->nombre }} ({{ $r->estado }})</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="text-xs font-bold text-stone-500 uppercase tracking-wider mb-1 block">Estado</label>
            <select name="estado_restaurante" class="w-full bg-surface-container-highest border-0 rounded-lg p-3 text-sm focus:ring-2 focus:ring-primary focus:bg-surface-container-lowest transition-all">
                <option value="">Todos</option>
                <option value="activo" {{ request('estado_restaurante') === 'activo' ? 'selected' : '' }}>Activos</option>
                <option value="inactivo" {{ request('estado_restaurante') === 'inactivo' ? 'selected' : '' }}>Inactivos</option>
                <option value="baneado" {{ request('estado_restaurante') === 'baneado' ? 'selected' : '' }}>Baneados</option>
            </select>
        </div>
        <div>
            <label class="text-xs font-bold text-stone-500 uppercase tracking-wider mb-1 block">Score</label>
            <select name="score" class="w-full bg-surface-container-highest border-0 rounded-lg p-3 text-sm focus:ring-2 focus:ring-primary focus:bg-surface-container-lowest transition-all">
                <option value="">Todos</option>
                @foreach(range(1, 5) as $s)
                    <option value="{{ $s }}" {{ request('score') == $s ? 'selected' : '' }}>{{ $s }} estrella{{ $s > 1 ? 's' : '' }}</option>
                @endforeach
            </select>
        </div>
        <div class="flex gap-2">
            <button type="submit" class="flex-1 bg-primary text-on-primary px-4 py-3 rounded-xl font-bold text-sm shadow-sm hover:bg-primary-container transition-all">Aplicar</button>
            <a href="{{ route('admin.reportes.index') }}" class="flex-1 text-center bg-stone-100 text-stone-600 px-4 py-3 rounded-xl font-bold text-sm hover:bg-stone-200 transition-all">Limpiar</a>
        </div>
    </div>
</form>

<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
    <div class="bg-surface-container-lowest p-5 rounded-2xl shadow-sm border border-stone-100/50">
        <p class="text-xs font-bold text-stone-500 uppercase tracking-wider mb-1">Cuentas Restaurante</p>
        <h3 class="text-2xl font-black text-on-surface">{{ $kpis['cuentasRestaurante'] }}</h3>
    </div>
    <div class="bg-surface-container-lowest p-5 rounded-2xl shadow-sm border border-stone-100/50">
        <p class="text-xs font-bold text-stone-500 uppercase tracking-wider mb-1">Sucursales Activas</p>
        <h3 class="text-2xl font-black text-on-surface">{{ $kpis['sucursalesActivas'] }}</h3>
    </div>
    <div class="bg-surface-container-lowest p-5 rounded-2xl shadow-sm border border-stone-100/50">
        <p class="text-xs font-bold text-stone-500 uppercase tracking-wider mb-1">Comensales</p>
        <h3 class="text-2xl font-black text-on-surface">{{ $kpis['totalComensales'] }}</h3>
    </div>
    <div class="bg-surface-container-lowest p-5 rounded-2xl shadow-sm border border-stone-100/50">
        <p class="text-xs font-bold text-stone-500 uppercase tracking-wider mb-1">Visitas (rango)</p>
        <h3 class="text-2xl font-black text-on-surface">{{ $kpis['visitasRango'] }}</h3>
    </div>
    <div class="bg-surface-container-lowest p-5 rounded-2xl shadow-sm border border-stone-100/50">
        <p class="text-xs font-bold text-stone-500 uppercase tracking-wider mb-1">Reseñas (rango)</p>
        <h3 class="text-2xl font-black text-on-surface">{{ $kpis['resenasRango'] }}</h3>
    </div>
    <div class="bg-surface-container-lowest p-5 rounded-2xl shadow-sm border border-stone-100/50">
        <p class="text-xs font-bold text-stone-500 uppercase tracking-wider mb-1">Promedio Score</p>
        <h3 class="text-2xl font-black text-on-surface">{{ number_format($kpis['promedioScore'] ?? 0, 1) }}</h3>
    </div>
    <div class="bg-surface-container-lowest p-5 rounded-2xl shadow-sm border border-stone-100/50">
        <p class="text-xs font-bold text-stone-500 uppercase tracking-wider mb-1">Promociones Activas</p>
        <h3 class="text-2xl font-black text-on-surface">{{ $kpis['promocionesActivas'] }}</h3>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div class="bg-surface-container-lowest rounded-2xl shadow-sm border border-stone-100/50 p-5">
        <h4 class="text-lg font-bold text-on-surface mb-4">Top Restaurantes por Visitas</h4>
        @if(count($tables['top_restaurantes_visitas']) > 0)
            <div class="space-y-2">
                @foreach($tables['top_restaurantes_visitas'] as $i => $item)
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
        <h4 class="text-lg font-bold text-on-surface mb-4">Mejor Calificación</h4>
        @if(count($tables['top_restaurantes_rating']) > 0)
            <div class="space-y-2">
                @foreach($tables['top_restaurantes_rating'] as $i => $item)
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
        <h4 class="text-lg font-bold text-on-surface mb-4">Peor Calificación</h4>
        @if(count($tables['bottom_restaurantes_rating']) > 0)
            <div class="space-y-2">
                @foreach($tables['bottom_restaurantes_rating'] as $i => $item)
                    <div class="flex items-center justify-between py-2 border-b border-stone-100 last:border-0">
                        <div class="flex items-center gap-3">
                            <span class="text-xs font-bold text-stone-400 w-5">{{ $i + 1 }}</span>
                            <span class="font-medium text-on-surface text-sm">{{ $item->nombre }}</span>
                        </div>
                        <span class="text-sm font-bold text-red-600">{{ number_format($item->promedio, 1) }} ({{ $item->total_resenas }})</span>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-sm text-stone-400">Sin datos suficientes.</p>
        @endif
    </div>

    <div class="bg-surface-container-lowest rounded-2xl shadow-sm border border-stone-100/50 p-5">
        <h4 class="text-lg font-bold text-on-surface mb-4">Distribución de Reseñas por Score</h4>
        @if(array_sum($tables['resenas_por_score']) > 0)
            <div class="space-y-2">
                @foreach($tables['resenas_por_score'] as $score => $total)
                    @php $pct = $tables['resenas_por_score'] ? round($total / array_sum($tables['resenas_por_score']) * 100) : 0; @endphp
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
                <p class="text-2xl font-black text-green-600">{{ count($tables['promociones_por_estado']['activas']) }}</p>
                <p class="text-xs font-bold text-stone-500 uppercase mt-1">Activas</p>
            </div>
            <div class="bg-stone-50 rounded-xl p-4">
                <p class="text-2xl font-black text-stone-500">{{ count($tables['promociones_por_estado']['inactivas']) }}</p>
                <p class="text-xs font-bold text-stone-500 uppercase mt-1">Inactivas</p>
            </div>
            <div class="bg-red-50 rounded-xl p-4">
                <p class="text-2xl font-black text-red-500">{{ count($tables['promociones_por_estado']['vencidas']) }}</p>
                <p class="text-xs font-bold text-stone-500 uppercase mt-1">Vencidas</p>
            </div>
        </div>
    </div>

    <div class="bg-surface-container-lowest rounded-2xl shadow-sm border border-stone-100/50 p-5">
        <h4 class="text-lg font-bold text-on-surface mb-4">Productos</h4>
        <div class="grid grid-cols-3 gap-3 text-center">
            <div class="bg-green-50 rounded-xl p-4">
                <p class="text-2xl font-black text-green-600">{{ $tables['productos_por_estado']['activos'] }}</p>
                <p class="text-xs font-bold text-stone-500 uppercase mt-1">Activos</p>
            </div>
            <div class="bg-stone-50 rounded-xl p-4">
                <p class="text-2xl font-black text-stone-500">{{ $tables['productos_por_estado']['inactivos'] }}</p>
                <p class="text-xs font-bold text-stone-500 uppercase mt-1">Inactivos</p>
            </div>
            <div class="bg-red-50 rounded-xl p-4">
                <p class="text-2xl font-black text-red-500">{{ $tables['productos_por_estado']['sin_stock'] }}</p>
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
                    <th class="pb-3">Restaurante</th>
                </tr>
            </thead>
            <tbody>
                @foreach($tables['productos_sin_stock'] as $item)
                <tr class="border-t border-stone-100">
                    <td class="py-2 font-medium text-on-surface">{{ $item->nombre }}</td>
                    <td class="py-2 text-stone-500">{{ $item->restaurante }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
</div>
@endsection
