@extends('layouts.restaurante')

@section('title', 'Sucursales')
@section('page-title', 'Gestión de Sucursales')

@section('content')
<div class="max-w-6xl mx-auto space-y-8">
    <section class="flex flex-col md:flex-row md:items-end justify-between gap-4">
        <div>
            <h1 class="text-4xl font-extrabold font-headline text-primary tracking-tight">Sucursales</h1>
            <p class="text-on-surface-variant font-body mt-2">Administra las sucursales de tu negocio.</p>
        </div>
        <button type="button" data-modal-open="sucursal-create-modal" class="flex items-center gap-2 bg-primary text-white px-6 py-3 rounded-xl font-bold shadow-lg hover:scale-105 active:scale-95 transition-all">
            <span class="material-symbols-outlined text-sm">add</span>
            Nueva Sucursal
        </button>
    </section>

    @if(session('success'))
        <div class="bg-green-100 text-green-800 p-4 rounded-xl font-bold">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="bg-red-100 text-red-800 p-4 rounded-xl font-bold">{{ session('error') }}</div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
        @forelse($sucursales as $sucursal)
        <div class="bg-surface-container-lowest rounded-2xl shadow-sm border border-stone-100 overflow-hidden {{ $sucursal->estado !== 'activo' ? 'opacity-60' : '' }}">
            <div class="relative h-40 bg-stone-100 overflow-hidden">
                @if($sucursal->foto_portada_url)
                    <img class="w-full h-full object-cover" src="{{ $sucursal->foto_portada_url }}" alt="{{ $sucursal->nombre }}">
                @else
                    <div class="w-full h-full flex items-center justify-center bg-primary-fixed text-primary">
                        <span class="material-symbols-outlined text-5xl">store</span>
                    </div>
                @endif
                @if($sucursal->es_principal)
                    <div class="absolute top-3 left-3 bg-primary text-white text-[10px] font-bold px-2.5 py-1 rounded-full uppercase tracking-wider shadow">Principal</div>
                @endif
                @if($sucursalActiva && $sucursalActiva->id === $sucursal->id)
                    <div class="absolute top-3 right-3 bg-green-500 text-white text-[10px] font-bold px-2.5 py-1 rounded-full uppercase tracking-wider shadow">Activa</div>
                @endif
                @if($sucursal->estado !== 'activo')
                    <div class="absolute inset-0 bg-black/40 flex items-center justify-center">
                        <span class="bg-stone-800 text-white text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wider">Archivada</span>
                    </div>
                @endif
            </div>
            <div class="p-5">
                <h3 class="font-headline font-bold text-lg text-on-surface">{{ $sucursal->nombre }}</h3>
                <div class="mt-2 space-y-1 text-sm text-stone-600">
                    @if($sucursal->zona)
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-stone-400 text-[16px]">location_on</span>
                        <span>{{ $sucursal->zona }}{{ $sucursal->direccion ? ' - '.$sucursal->direccion : '' }}</span>
                    </div>
                    @endif
                    @if($sucursal->telefono)
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-stone-400 text-[16px]">call</span>
                        <span>{{ $sucursal->telefono }}</span>
                    </div>
                    @endif
                    @if($sucursal->email_reservas)
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-stone-400 text-[16px]">mail</span>
                        <span>{{ $sucursal->email_reservas }}</span>
                    </div>
                    @endif
                </div>
                @if($sucursal->estado === 'activo')
                <div class="mt-4 flex flex-wrap gap-2">
                    <button type="button" data-modal-open="sucursal-edit-{{ $sucursal->id }}" class="flex items-center gap-1 px-3 py-1.5 bg-stone-100 hover:bg-stone-200 rounded-lg text-xs font-bold text-stone-600 transition-colors">
                        <span class="material-symbols-outlined text-sm">edit</span> Editar
                    </button>
                    @unless($sucursal->es_principal)
                    <form method="POST" action="{{ route('restaurante.sucursales.set-primary', $sucursal) }}" class="inline">
                        @csrf
                        <button class="flex items-center gap-1 px-3 py-1.5 bg-amber-50 hover:bg-amber-100 rounded-lg text-xs font-bold text-amber-700 transition-colors">
                            <span class="material-symbols-outlined text-sm">star</span> Principal
                        </button>
                    </form>
                    @endunless
                    <form method="POST" action="{{ route('restaurante.sucursales.archive', $sucursal) }}" class="inline" onsubmit="return confirm('¿Archivar esta sucursal? Los datos se conservarán pero dejará de ser visible.')">
                        @csrf
                        <button class="flex items-center gap-1 px-3 py-1.5 bg-red-50 hover:bg-red-100 rounded-lg text-xs font-bold text-red-600 transition-colors">
                            <span class="material-symbols-outlined text-sm">archive</span> Archivar
                        </button>
                    </form>
                    @if(!$sucursalActiva || $sucursalActiva->id !== $sucursal->id)
                    <form method="POST" action="{{ route('restaurante.sucursales.select', $sucursal) }}" class="inline">
                        @csrf
                        <button class="flex items-center gap-1 px-3 py-1.5 bg-blue-50 hover:bg-blue-100 rounded-lg text-xs font-bold text-blue-600 transition-colors">
                            <span class="material-symbols-outlined text-sm">toggle_on</span> Seleccionar
                        </button>
                    </form>
                    @endif
                </div>
                @endif
            </div>

            <!-- Edit Modal -->
            <div id="sucursal-edit-{{ $sucursal->id }}" class="fixed inset-0 z-50 hidden" role="dialog">
                <div class="absolute inset-0 bg-black/40" onclick="document.getElementById('sucursal-edit-{{ $sucursal->id }}').classList.add('hidden')"></div>
                <div class="relative min-h-full flex items-center justify-center p-4 sm:p-6">
                    <div class="relative w-full max-w-3xl bg-surface-container-lowest rounded-2xl shadow-2xl overflow-y-auto max-h-[calc(100vh-4rem)]">
                        <div class="flex items-center justify-between border-b border-stone-200 px-6 py-4">
                            <div>
                                <h3 class="font-headline font-bold text-lg text-on-surface">Editar Sucursal</h3>
                                <p class="text-xs text-stone-500">{{ $sucursal->nombre }}</p>
                            </div>
                            <button onclick="document.getElementById('sucursal-edit-{{ $sucursal->id }}').classList.add('hidden')" class="h-11 w-11 flex items-center justify-center rounded-full bg-stone-100 text-stone-500 hover:bg-stone-200 transition-colors">
                                <span class="material-symbols-outlined">close</span>
                            </button>
                        </div>
                        <div class="px-6 py-6">
                            <form method="POST" action="{{ route('restaurante.sucursales.update', $sucursal) }}" enctype="multipart/form-data">
                                @csrf
                                @include('restaurante.sucursales._form', [
                                    'sucursal' => $sucursal,
                                    'editMode' => true,
                                ])
                                <div class="mt-6 flex justify-end gap-3">
                                    <button type="button" onclick="document.getElementById('sucursal-edit-{{ $sucursal->id }}').classList.add('hidden')" class="px-6 py-3 rounded-xl font-bold text-stone-500 bg-stone-100 hover:bg-stone-200 transition-colors">Cancelar</button>
                                    <button type="submit" class="px-8 py-3 rounded-xl bg-primary text-white font-bold shadow-lg hover:scale-[1.02] active:scale-95 transition-all">Guardar Cambios</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="md:col-span-2 xl:col-span-3 text-center py-16 text-stone-400">
            <span class="material-symbols-outlined text-6xl mb-4">store</span>
            <p class="font-bold text-lg text-stone-500">No tienes sucursales aún</p>
            <p class="text-sm mt-1">Crea tu primera sucursal para empezar a operar.</p>
        </div>
        @endforelse
    </div>
</div>

<!-- Create Modal -->
<div id="sucursal-create-modal" class="fixed inset-0 z-50 hidden" role="dialog">
    <div class="absolute inset-0 bg-black/40" onclick="document.getElementById('sucursal-create-modal').classList.add('hidden')"></div>
    <div class="relative min-h-full flex items-center justify-center p-4 sm:p-6">
        <div class="relative w-full max-w-3xl bg-surface-container-lowest rounded-2xl shadow-2xl overflow-y-auto max-h-[calc(100vh-4rem)]">
            <div class="flex items-center justify-between border-b border-stone-200 px-6 py-4">
                <div>
                    <h3 class="font-headline font-bold text-lg text-on-surface">Nueva Sucursal</h3>
                    <p class="text-xs text-stone-500">Agrega una nueva sucursal a tu negocio.</p>
                </div>
                <button onclick="document.getElementById('sucursal-create-modal').classList.add('hidden')" class="h-11 w-11 flex items-center justify-center rounded-full bg-stone-100 text-stone-500 hover:bg-stone-200 transition-colors">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
            <div class="px-6 py-6">
                <form method="POST" action="{{ route('restaurante.sucursales.store') }}" enctype="multipart/form-data">
                    @csrf
                    @include('restaurante.sucursales._form', [
                        'sucursal' => null,
                        'editMode' => false,
                    ])
                    <div class="mt-6 flex justify-end gap-3">
                        <button type="button" onclick="document.getElementById('sucursal-create-modal').classList.add('hidden')" class="px-6 py-3 rounded-xl font-bold text-stone-500 bg-stone-100 hover:bg-stone-200 transition-colors">Cancelar</button>
                        <button type="submit" class="px-8 py-3 rounded-xl bg-primary text-white font-bold shadow-lg hover:scale-[1.02] active:scale-95 transition-all">Crear Sucursal</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection