@extends('layouts.restaurante')

@section('title', 'Panel de Control')
@section('page-title', 'Panel de Control')

@section('content')
@php $redirectTo = request()->fullUrl(); @endphp

<div class="space-y-8">
    <!-- KPI Strip -->
    <section class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-6 gap-4">
        <div class="bg-surface-container-lowest p-4 rounded-2xl shadow-sm border border-stone-100/50">
            <p class="text-[10px] font-bold text-stone-500 uppercase tracking-wider mb-1">Platos Activos</p>
            <h3 class="text-2xl font-black text-on-surface">{{ $kpis['productos_activos'] }}</h3>
            <p class="text-xs text-stone-400 mt-1">{{ $kpis['productos_sin_stock'] }} sin stock</p>
        </div>
        <div class="bg-surface-container-lowest p-4 rounded-2xl shadow-sm border border-stone-100/50">
            <p class="text-[10px] font-bold text-stone-500 uppercase tracking-wider mb-1">Promociones</p>
            <h3 class="text-2xl font-black text-on-surface">{{ $kpis['promociones_activas'] }}</h3>
            <p class="text-xs text-stone-400 mt-1">activas</p>
        </div>
        <div class="bg-surface-container-lowest p-4 rounded-2xl shadow-sm border border-stone-100/50">
            <p class="text-[10px] font-bold text-stone-500 uppercase tracking-wider mb-1">Visitas (30d)</p>
            <h3 class="text-2xl font-black text-on-surface">{{ $kpis['visitas_30d'] }}</h3>
            <p class="text-xs text-stone-400 mt-1">clientes registrados</p>
        </div>
        <div class="bg-surface-container-lowest p-4 rounded-2xl shadow-sm border border-stone-100/50">
            <p class="text-[10px] font-bold text-stone-500 uppercase tracking-wider mb-1">Reseñas (30d)</p>
            <h3 class="text-2xl font-black text-on-surface">{{ $kpis['resenas_30d'] }}</h3>
            <p class="text-xs text-stone-400 mt-1">opiniones recibidas</p>
        </div>
        <div class="bg-surface-container-lowest p-4 rounded-2xl shadow-sm border border-stone-100/50">
            <p class="text-[10px] font-bold text-stone-500 uppercase tracking-wider mb-1">Score Promedio</p>
            <h3 class="text-2xl font-black {{ $kpis['promedio_score_30d'] >= 4 ? 'text-green-600' : ($kpis['promedio_score_30d'] >= 3 ? 'text-amber-600' : 'text-red-500') }}">
                {{ number_format($kpis['promedio_score_30d'], 1) }}
            </h3>
            <p class="text-xs text-stone-400 mt-1">/ 5.0</p>
        </div>
        <div class="bg-primary text-on-primary p-4 rounded-2xl shadow-lg shadow-primary/20">
            <p class="text-[10px] font-bold text-on-primary/70 uppercase tracking-wider mb-1">Total Platos</p>
            <h3 class="text-2xl font-black">{{ $totalProductos }}</h3>
            <p class="text-xs text-on-primary/60 mt-1">en el catálogo</p>
        </div>
    </section>

    <!-- Alerts -->
    @if(count($alerts) > 0)
    <section class="bg-amber-50 border border-amber-200 rounded-2xl p-5">
        <div class="flex items-center gap-2 mb-3">
            <span class="material-symbols-outlined text-amber-600 text-lg">warning</span>
            <h4 class="font-headline font-bold text-sm text-amber-800">Atención requerida</h4>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-3">
            @foreach($alerts as $alert)
            <div class="flex items-center gap-2 bg-white/80 rounded-xl px-4 py-3 text-sm">
                <span class="material-symbols-outlined text-{{ $alert['severity'] === 'warning' ? 'amber-600' : 'blue-500' }} text-lg">
                    {{ $alert['severity'] === 'warning' ? 'error' : 'info' }}
                </span>
                <span class="font-medium text-stone-700">{{ $alert['label'] }}</span>
                @if($alert['count'] > 1)
                <span class="ml-auto bg-stone-200 text-stone-700 text-xs font-bold px-2 py-0.5 rounded-full">{{ $alert['count'] }}</span>
                @endif
            </div>
            @endforeach
        </div>
    </section>
    @endif

    <!-- Bento Grid: Status + Config -->
    <section class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="md:col-span-2 bg-surface-container-lowest p-6 rounded-xl shadow-sm flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
            <div class="flex items-center gap-6">
                <div class="relative w-20 h-20 rounded-2xl overflow-hidden shadow-lg bg-stone-100 shrink-0">
                    @if($restaurante && $restaurante->foto_portada_url)
                        <img class="w-full h-full object-cover" src="{{ $restaurante->foto_portada_url }}" alt="Logo">
                    @else
                        <div class="w-full h-full flex items-center justify-center bg-primary-fixed text-primary font-black text-3xl">
                            {{ substr($usuario->nombre, 0, 1) }}
                        </div>
                    @endif
                </div>
                <div>
                    <p class="text-stone-500 text-xs font-semibold uppercase tracking-widest mb-1">Estado del Servicio</p>
                    <h3 class="font-headline font-extrabold text-2xl text-on-surface">
                        {{ $restaurante ? ucfirst($restaurante->estado) : 'Sin configurar' }}
                    </h3>
                    <div class="flex items-center gap-2 mt-2">
                        <span class="flex h-2 w-2 rounded-full {{ $restaurante && $restaurante->estado === 'activo' ? 'bg-green-500' : 'bg-amber-400' }}"></span>
                        <p class="text-sm text-stone-600 font-medium">
                            {{ $restaurante ? $restaurante->nombre : $usuario->nombre }}
                        </p>
                    </div>
                    @if($restaurante)
                    <div class="flex items-center gap-2 mt-1">
                        @if($restaurante->es_principal)
                        <span class="text-[10px] font-bold bg-amber-100 text-amber-800 px-2 py-0.5 rounded-full uppercase tracking-wider">Principal</span>
                        @endif
                        <span class="text-[10px] text-stone-400">Sucursal activa</span>
                    </div>
                    @endif
                </div>
            </div>
            <div class="flex items-center gap-4 bg-surface-container p-3 rounded-lg w-full md:w-auto">
                <div class="flex-1 md:flex-none">
                    <p class="text-[10px] font-bold text-stone-500 uppercase">Total Platos</p>
                    <p class="text-2xl font-black text-on-surface">{{ $totalProductos }}</p>
                </div>
                <button type="button" data-modal-open="restaurante-producto-create-modal" class="bg-primary text-white px-4 py-2 rounded-lg text-xs font-bold hover:bg-[#c0392b] transition-colors">
                    + Añadir
                </button>
            </div>
        </div>
        <div class="bg-primary-container text-on-primary-container p-6 rounded-xl shadow-lg relative overflow-hidden group">
            <div class="relative z-10">
                <p class="text-[10px] font-bold opacity-80 uppercase tracking-tighter">Perfil del Local</p>
                <h4 class="font-headline font-bold text-xl mt-1">
                    {{ $restaurante ? 'Configurado' : '¡Configura tu local!' }}
                </h4>
                <p class="text-sm opacity-90 mt-2">
                    {{ $restaurante && $restaurante->zona ? 'Zona: '.$restaurante->zona : 'Completa tu perfil público.' }}
                </p>
                <a href="{{ route('restaurante.configuracion') }}" class="mt-4 inline-block text-xs font-bold underline decoration-2 underline-offset-4">
                    Ver Ajustes →
                </a>
            </div>
            <span class="material-symbols-outlined absolute -bottom-4 -right-4 text-8xl opacity-10 rotate-12 group-hover:scale-110 transition-transform duration-500">settings</span>
        </div>
    </section>

    <!-- Latest Reviews + Score Distribution + Promo Summary -->
    <section class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <div class="xl:col-span-2 bg-surface-container-lowest rounded-2xl shadow-sm border border-stone-100/50 p-5">
            <div class="flex items-center justify-between mb-4">
                <h4 class="font-headline font-bold text-base text-on-surface flex items-center gap-2">
                    <span class="material-symbols-outlined text-amber-500 text-lg">rate_review</span>
                    Últimas Reseñas
                </h4>
                <a href="{{ route('restaurante.resenas') }}" class="text-xs font-bold text-primary hover:underline">Ver todas</a>
            </div>
            @if(count($latestReviews) > 0)
            <div class="space-y-3">
                @foreach($latestReviews as $r)
                <div class="flex items-start gap-3 py-2 border-b border-stone-100 last:border-0">
                    <div class="w-9 h-9 rounded-full bg-primary-fixed flex items-center justify-center text-primary font-bold text-sm shrink-0">
                        {{ substr($r['comensal']['nombre'] ?? 'C', 0, 1) }}
                    </div>
                    <div class="min-w-0 flex-1">
                        <div class="flex items-center gap-2">
                            <p class="text-sm font-bold text-on-surface">{{ $r['comensal']['nombre'] ?? 'Comensal' }}</p>
                            <div class="flex items-center gap-0.5">
                                @for($i = 1; $i <= 5; $i++)
                                <span class="material-symbols-outlined text-[14px] {{ $i <= $r['score'] ? 'text-amber-400' : 'text-stone-200' }}" style="font-variation-settings: 'FILL' 1;">star</span>
                                @endfor
                            </div>
                        </div>
                        <p class="text-xs text-stone-400">{{ $r['menu']['nombre'] ?? '—' }} · {{ \Carbon\Carbon::parse($r['created_at'])->diffForHumans() }}</p>
                        @if($r['comentario'])
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
        <div class="space-y-4">
            <!-- Score Distribution -->
            <div class="bg-surface-container-lowest rounded-2xl shadow-sm border border-stone-100/50 p-5">
                <h4 class="font-headline font-bold text-sm text-on-surface mb-3 flex items-center gap-2">
                    <span class="material-symbols-outlined text-amber-500 text-lg">stars</span>
                    Distribución Score
                </h4>
                @php $distTotal = array_sum($scoreDistribution); @endphp
                @if($distTotal > 0)
                <div class="space-y-1.5">
                    @foreach($scoreDistribution as $score => $total)
                    @php $pct = $distTotal > 0 ? round($total / $distTotal * 100) : 0; @endphp
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-bold text-stone-500 w-5">{{ $score }}</span>
                        <div class="flex-1 h-3 bg-stone-100 rounded-full overflow-hidden">
                            <div class="h-full rounded-full {{ $score >= 4 ? 'bg-green-500' : ($score >= 3 ? 'bg-yellow-500' : 'bg-red-500') }}" style="width: {{ $pct }}%"></div>
                        </div>
                        <span class="text-xs font-bold text-stone-500 w-6 text-right">{{ $total }}</span>
                    </div>
                    @endforeach
                </div>
                @else
                <p class="text-xs text-stone-400">Sin datos</p>
                @endif
            </div>
            <!-- Promo Summary -->
            <div class="bg-surface-container-lowest rounded-2xl shadow-sm border border-stone-100/50 p-5">
                <h4 class="font-headline font-bold text-sm text-on-surface mb-3 flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary text-lg">local_offer</span>
                    Promociones
                </h4>
                <div class="grid grid-cols-3 gap-2 text-center">
                    <div class="bg-green-50 rounded-xl py-3">
                        <p class="text-lg font-black text-green-600">{{ $promoSummary['activas'] }}</p>
                        <p class="text-[10px] font-bold text-stone-500 uppercase mt-1">Activas</p>
                    </div>
                    <div class="bg-amber-50 rounded-xl py-3">
                        <p class="text-lg font-black text-amber-600">{{ $promoSummary['vencidas'] }}</p>
                        <p class="text-[10px] font-bold text-stone-500 uppercase mt-1">Vencidas</p>
                    </div>
                    <div class="bg-stone-50 rounded-xl py-3">
                        <p class="text-lg font-black text-stone-500">{{ $promoSummary['inactivas'] }}</p>
                        <p class="text-[10px] font-bold text-stone-500 uppercase mt-1">Inactivas</p>
                    </div>
                </div>
                <a href="{{ route('restaurante.promociones.index') }}" class="mt-3 block text-center text-xs font-bold text-primary hover:underline">Gestionar promociones</a>
            </div>
            <!-- Branch Summary Mini -->
            @if($branchSummary)
            <div class="bg-surface-container-lowest rounded-2xl shadow-sm border border-stone-100/50 p-5">
                <h4 class="font-headline font-bold text-sm text-on-surface mb-3 flex items-center gap-2">
                    <span class="material-symbols-outlined text-secondary text-lg">store</span>
                    {{ $branchSummary->nombre }}
                </h4>
                <div class="space-y-2 text-sm text-stone-600">
                    @if($branchSummary->zona)
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-stone-400 text-[16px]">location_on</span>
                        <span>{{ $branchSummary->zona }}{{ $branchSummary->direccion ? ' - '.$branchSummary->direccion : '' }}</span>
                    </div>
                    @endif
                    @if($branchSummary->horario_apertura)
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-stone-400 text-[16px]">schedule</span>
                        <span>{{ $branchSummary->horario_apertura }} – {{ $branchSummary->horario_cierre }}</span>
                    </div>
                    @endif
                    @if($branchSummary->telefono)
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-stone-400 text-[16px]">call</span>
                        <span>{{ $branchSummary->telefono }}</span>
                    </div>
                    @endif
                </div>
                <div class="mt-3 flex gap-2">
                    <a href="{{ route('restaurante.configuracion') }}" class="flex-1 text-center text-[10px] font-bold bg-stone-100 py-2 rounded-lg hover:bg-stone-200 transition-colors">Editar</a>
                    <a href="{{ route('restaurante.sucursales.index') }}" class="flex-1 text-center text-[10px] font-bold bg-stone-100 py-2 rounded-lg hover:bg-stone-200 transition-colors">Sucursales</a>
                </div>
            </div>
            @endif
        </div>
    </section>

    <!-- Menu Table -->
    <section class="grid grid-cols-1 xl:grid-cols-3 gap-8">
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
                                        @if($producto->descripcion)
                                        <p class="text-[10px] text-stone-400 truncate max-w-[150px]">{{ $producto->descripcion }}</p>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-stone-600">{{ $producto->categoria->nombre_categoria ?? 'Sin categoría' }}</td>
                            <td class="px-6 py-4 text-sm font-bold text-on-surface">{{ number_format($producto->precio, 2) }}Bs.</td>
                            <td class="px-6 py-4">
                                <form action="{{ route('productos.toggle', $producto) }}" method="POST">
                                    @csrf
                                    <button type="submit"
                                        class="relative inline-flex h-5 w-9 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 focus:outline-none {{ $producto->activo ? 'bg-primary' : 'bg-stone-300' }}"
                                        title="{{ $producto->activo ? 'Deshabilitar' : 'Habilitar' }}">
                                        <span class="{{ $producto->activo ? 'translate-x-4' : 'translate-x-0' }} pointer-events-none inline-block h-4 w-4 transform rounded-full bg-white shadow ring-0 transition duration-200"></span>
                                    </button>
                                </form>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex justify-end gap-1">
                                    <button type="button" data-modal-open="producto-edit-{{ $producto->id }}" class="p-1.5 text-stone-400 hover:text-primary transition-colors">
                                        <span class="material-symbols-outlined text-lg">edit</span>
                                    </button>
                                    <button type="button" data-modal-open="producto-delete-{{ $producto->id }}" class="p-1.5 text-stone-400 hover:text-red-600 transition-colors">
                                        <span class="material-symbols-outlined text-lg">delete</span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-stone-500">
                                <span class="material-symbols-outlined text-5xl mb-3 opacity-40">restaurant_menu</span>
                                <p class="font-semibold">Aún no tienes platos agregados.</p>
                                <button type="button" data-modal-open="restaurante-producto-create-modal" class="mt-3 inline-block text-primary font-bold underline text-sm">Crear primer plato</button>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
                @if($productos->hasPages())
                <div class="bg-surface-container-low/30 px-6 py-4 flex justify-between items-center">
                    <div class="w-full">{{ $productos->links() }}</div>
                </div>
                @endif
            </div>
        </div>
        <div class="space-y-4">
            <h3 class="font-headline font-bold text-lg text-on-surface px-2">Mi Restaurante</h3>
            <div class="bg-surface-container-lowest p-6 rounded-2xl shadow-sm space-y-5">
                @if($restaurante)
                <div class="space-y-3 text-sm">
                    @if($restaurante->direccion)
                    <div class="flex items-start gap-3">
                        <span class="material-symbols-outlined text-secondary text-[18px] mt-0.5">location_on</span>
                        <span class="text-on-surface-variant">{{ $restaurante->direccion }}{{ $restaurante->zona ? ', '.$restaurante->zona : '' }}</span>
                    </div>
                    @endif
                    @if($restaurante->horario_apertura)
                    <div class="flex items-center gap-3">
                        <span class="material-symbols-outlined text-secondary text-[18px]">schedule</span>
                        <span class="text-on-surface-variant">{{ $restaurante->horario_apertura }} – {{ $restaurante->horario_cierre }}</span>
                    </div>
                    @endif
                    @if($restaurante->telefono)
                    <div class="flex items-center gap-3">
                        <span class="material-symbols-outlined text-secondary text-[18px]">call</span>
                        <span class="text-on-surface-variant">{{ $restaurante->telefono }}</span>
                    </div>
                    @endif
                    @if($restaurante->email_reservas)
                    <div class="flex items-center gap-3">
                        <span class="material-symbols-outlined text-secondary text-[18px]">mail</span>
                        <span class="text-on-surface-variant">{{ $restaurante->email_reservas }}</span>
                    </div>
                    @endif
                    @if($restaurante->instagram)
                    <div class="flex items-center gap-3">
                        <span class="material-symbols-outlined text-secondary text-[18px]">alternate_email</span>
                        <span class="text-on-surface-variant">{{ $restaurante->instagram }}</span>
                    </div>
                    @endif
                    @if($restaurante->facebook_url)
                    <div class="flex items-center gap-3">
                        <span class="material-symbols-outlined text-secondary text-[18px]">language</span>
                        <span class="text-on-surface-variant">{{ $restaurante->facebook_url }}</span>
                    </div>
                    @endif
                </div>
                @else
                <p class="text-sm text-stone-400 text-center py-4">No has completado tu configuración aún.</p>
                @endif
                <a href="{{ route('restaurante.configuracion') }}" class="w-full mt-2 flex items-center justify-center gap-2 bg-surface-container py-3 rounded-xl font-bold text-sm text-on-surface hover:bg-surface-container-high transition-colors">
                    <span class="material-symbols-outlined text-sm">edit</span>
                    Editar Sucursal
                </a>
                <a href="{{ route('restaurante.sucursales.index') }}" class="w-full flex items-center justify-center gap-2 bg-surface-container py-3 rounded-xl font-bold text-sm text-on-surface hover:bg-surface-container-high transition-colors">
                    <span class="material-symbols-outlined text-sm">store</span>
                    Administrar Sucursales
                </a>
            </div>
        </div>
    </section>
</div>

@include('productos._table-modals', [
    'productos' => $productos,
    'categorias' => $categorias,
    'redirectTo' => $redirectTo,
])
@endsection
