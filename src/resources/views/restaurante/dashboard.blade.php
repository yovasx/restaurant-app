@extends('layouts.restaurante')

@section('title', 'Panel de Control')
@section('page-title', 'Gestión del Menú')

@section('content')
<div class="space-y-8">
    <!-- Stats Bento Grid -->
    <section class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Status Card -->
        <div class="md:col-span-2 bg-surface-container-lowest p-6 rounded-xl shadow-sm flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
            <div class="flex items-center gap-6">
                <div class="relative w-20 h-20 rounded-2xl overflow-hidden shadow-lg bg-stone-100 shrink-0">
                    @if($restaurante && $restaurante->foto_portada)
                        <img class="w-full h-full object-cover" src="{{ asset('storage/'.$restaurante->foto_portada) }}" alt="Logo">
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
                </div>
            </div>
            <div class="flex items-center gap-4 bg-surface-container p-3 rounded-lg w-full md:w-auto">
                <div class="flex-1 md:flex-none">
                    <p class="text-[10px] font-bold text-stone-500 uppercase">Total Platos</p>
                    <p class="text-2xl font-black text-on-surface">{{ $totalProductos }}</p>
                </div>
                <a href="{{ route('productos.create') }}" class="bg-primary text-white px-4 py-2 rounded-lg text-xs font-bold hover:bg-[#c0392b] transition-colors">
                    + Añadir
                </a>
            </div>
        </div>
        <!-- Quick Config Card -->
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

    <!-- Menu Table Section -->
    <section class="grid grid-cols-1 xl:grid-cols-3 gap-8">
        <div class="xl:col-span-2 space-y-4">
            <div class="flex justify-between items-end px-2">
                <div>
                    <h3 class="font-headline font-bold text-lg text-on-surface">Gestión de Menú</h3>
                    <p class="text-sm text-stone-500">Administra la visibilidad y precios de tus platos</p>
                </div>
                <a href="{{ route('productos.create') }}" class="flex items-center gap-2 bg-primary text-white py-2 px-4 rounded-lg font-headline font-bold text-sm shadow-md hover:bg-[#c0392b] transition-colors">
                    <span class="material-symbols-outlined text-sm">add</span> Nuevo Plato
                </a>
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
                                        @if($producto->foto)
                                            <img class="w-full h-full object-cover" src="{{ asset('storage/'.$producto->foto) }}" alt="{{ $producto->nombre }}">
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
                                    <a href="{{ route('productos.edit', $producto) }}" class="p-1.5 text-stone-400 hover:text-primary transition-colors">
                                        <span class="material-symbols-outlined text-lg">edit</span>
                                    </a>
                                    <form action="{{ route('productos.destroy', $producto) }}" method="POST" class="inline-block" onsubmit="return confirm('¿Eliminar este plato?');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="p-1.5 text-stone-400 hover:text-red-600 transition-colors">
                                            <span class="material-symbols-outlined text-lg">delete</span>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center text-stone-500">
                                <span class="material-symbols-outlined text-5xl mb-3 opacity-40">restaurant_menu</span>
                                <p class="font-semibold">Aún no tienes platos agregados.</p>
                                <a href="{{ route('productos.create') }}" class="mt-3 inline-block text-primary font-bold underline text-sm">Crear primer plato</a>
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

        <!-- Right Panel: My Info Summary -->
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
                    @if($restaurante->instagram)
                    <div class="flex items-center gap-3">
                        <span class="material-symbols-outlined text-secondary text-[18px]">alternate_email</span>
                        <span class="text-on-surface-variant">{{ $restaurante->instagram }}</span>
                    </div>
                    @endif
                </div>
                @else
                <p class="text-sm text-stone-400 text-center py-4">No has completado tu configuración aún.</p>
                @endif
                <a href="{{ route('restaurante.configuracion') }}" class="w-full mt-4 flex items-center justify-center gap-2 bg-surface-container py-3 rounded-xl font-bold text-sm text-on-surface hover:bg-surface-container-high transition-colors">
                    <span class="material-symbols-outlined text-sm">edit</span>
                    Editar Información
                </a>
            </div>
        </div>
    </section>
</div>
@endsection
