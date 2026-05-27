@extends('layouts.admin')

@section('content')
@php($redirectTo = request()->fullUrl())

<div class="flex justify-between items-end mb-6">
    <div>
        <h2 class="text-4xl font-extrabold text-on-surface tracking-tight mb-2">Directorio de Locales</h2>
        <p class="text-on-surface-variant font-medium">Gestión de restaurantes registrados.</p>
    </div>
    <div>
        <button type="button" data-modal-open="admin-restaurant-create-modal" class="bg-primary text-white px-6 py-3 rounded-xl font-bold flex items-center justify-center gap-2 shadow-lg hover:scale-105 transition-transform">
            <span class="material-symbols-outlined">add_business</span>
            Añadir Restaurante
        </button>
    </div>
</div>

<div class="flex gap-4 mb-6">
    <a href="{{ route('admin.restaurantes.index', ['tab' => 'activos']) }}" class="px-6 py-2 rounded-full font-bold text-sm transition-all {{ $tab === 'activos' ? 'bg-primary text-white shadow-md' : 'bg-surface-container-high text-on-surface-variant hover:bg-surface-dim' }}">
        Restaurantes Activos
    </a>
    <a href="{{ route('admin.restaurantes.index', ['tab' => 'inactivos']) }}" class="px-6 py-2 rounded-full font-bold text-sm transition-all {{ $tab === 'inactivos' ? 'bg-primary text-white shadow-md' : 'bg-surface-container-high text-on-surface-variant hover:bg-surface-dim' }}">
        Inactivos / Eliminados
    </a>
</div>

<div class="bg-surface-container-lowest rounded-3xl overflow-hidden shadow-sm">
    <table class="w-full text-left border-collapse">
        <thead>
            <tr class="bg-surface-container-high/50">
                <th class="px-6 py-5 text-sm font-bold uppercase tracking-wider text-on-surface-variant">Restaurante</th>
                <th class="px-6 py-5 text-sm font-bold uppercase tracking-wider text-on-surface-variant">Contacto</th>
                <th class="px-6 py-5 text-sm font-bold uppercase tracking-wider text-on-surface-variant">Estado</th>
                <th class="px-6 py-5 text-sm font-bold uppercase tracking-wider text-on-surface-variant text-right">Rol</th>
                <th class="px-6 py-5 text-sm font-bold uppercase tracking-wider text-on-surface-variant text-right">Acciones</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-surface-container-high">
            @foreach($restaurantes as $rest)
            <tr class="group hover:bg-surface-container-low transition-colors">
                <td class="px-6 py-5">
                    <div class="font-bold text-on-surface text-lg">{{ $rest->nombre }}</div>
                    <div class="text-xs text-on-surface-variant font-medium">Registrado: {{ $rest->created_at->format('d/m/Y') }}</div>
                </td>
                <td class="px-6 py-5">
                    <div class="text-sm font-medium text-on-surface">{{ $rest->email }}</div>
                    <div class="text-xs text-stone-500">{{ $rest->telefono }}</div>
                </td>
                <td class="px-6 py-5">
                    @if($rest->estado === 'activo')
                        <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs font-bold uppercase">Activo</span>
                    @else
                        <span class="px-3 py-1 bg-amber-100 text-amber-700 rounded-full text-xs font-bold uppercase">{{ $rest->estado }}</span>
                    @endif
                </td>
                <td class="px-6 py-5 text-right">
                    <div class="inline-flex items-center gap-3">
                        <span class="rounded-full bg-surface-container-low px-3 py-1 text-xs font-bold text-on-surface-variant">Restaurante</span>
                        <button type="button" data-modal-open="restaurante-role-{{ $rest->id }}" class="rounded-xl bg-surface-container-highest px-3 py-2 text-xs font-bold text-primary transition hover:bg-primary hover:text-white">
                            Cambiar rol
                        </button>
                    </div>
                </td>
                <td class="px-6 py-5 text-right">
                    <div class="flex items-center justify-end gap-3">
                        <button type="button" data-modal-open="restaurante-edit-{{ $rest->id }}" class="p-2 rounded-lg bg-surface-container-highest text-primary hover:bg-primary-container hover:text-white transition-all shadow-sm" title="Editar Información">
                            <span class="material-symbols-outlined text-[20px]">edit</span>
                        </button>
                        @if($rest->estado !== 'inactivo')
                            <button type="button" data-modal-open="restaurante-delete-{{ $rest->id }}" class="p-2 rounded-lg bg-surface-container-highest text-red-600 hover:bg-red-600 hover:text-white transition-all shadow-sm" title="Inactivar/Eliminar">
                                <span class="material-symbols-outlined text-[20px]">delete</span>
                            </button>
                        @endif
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <div class="p-6">
        {{ $restaurantes->links() }}
    </div>
</div>

<x-modal
    id="admin-restaurant-create-modal"
    title="Añadir restaurante"
    subtitle="Registra un nuevo negocio sin salir del directorio."
    max-width="max-w-3xl"
    :auto-open="old('_modal') === 'admin-restaurant-create-modal'"
>
    @include('admin.usuarios._modal-form', [
        'redirectTo' => $redirectTo,
        'modalId' => 'admin-restaurant-create-modal',
        'fixedRole' => '2',
    ])
</x-modal>

@foreach($restaurantes as $rest)
    <x-modal
        id="restaurante-edit-{{ $rest->id }}"
        title="Editar restaurante"
        subtitle="Actualiza la ficha del local sin abrir otra pantalla."
        max-width="max-w-3xl"
        :auto-open="old('_modal') === 'restaurante-edit-'.$rest->id"
    >
        @include('admin.restaurantes._modal-form', [
            'usuario' => $rest,
            'redirectTo' => $redirectTo,
            'modalId' => 'restaurante-edit-'.$rest->id,
        ])
    </x-modal>

    <x-modal id="restaurante-delete-{{ $rest->id }}" title="Mover a inactivos" subtitle="El restaurante dejará de aparecer en la lista activa." max-width="max-w-lg">
        <div class="space-y-6">
            <div class="rounded-3xl bg-red-50 p-5 text-red-900">
                <p class="font-headline text-xl font-extrabold">{{ $rest->nombre }}</p>
                <p class="mt-2 text-sm text-red-900/80">Podrás revisarlo después en la pestaña de inactivos.</p>
            </div>

            <form action="{{ route('admin.restaurantes.destroy', $rest->id) }}" method="POST" class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
                @csrf
                <input type="hidden" name="redirect_to" value="{{ $redirectTo }}">

                <button type="button" data-modal-close class="w-full rounded-2xl border border-stone-200 px-5 py-3.5 font-bold text-stone-600 transition hover:bg-stone-50 sm:w-auto sm:min-w-40">Cancelar</button>
                <button type="submit" class="w-full rounded-2xl bg-red-600 px-5 py-3.5 font-bold text-white shadow-lg shadow-red-900/20 transition hover:bg-red-700 sm:w-auto sm:min-w-40">Mover a inactivos</button>
            </form>
        </div>
    </x-modal>

    <x-modal
        id="restaurante-role-{{ $rest->id }}"
        title="Cambiar rol"
        subtitle="Si cambias el rol, el sistema trasladará sus datos al tipo de usuario correspondiente."
        max-width="max-w-xl"
        :auto-open="old('_modal') === 'restaurante-role-'.$rest->id"
    >
        <form action="{{ route('admin.roles.change') }}" method="POST" class="space-y-6">
            @csrf
            <input type="hidden" name="user_type" value="usuario">
            <input type="hidden" name="user_id" value="{{ $rest->id }}">
            <input type="hidden" name="redirect_to" value="{{ $redirectTo }}">
            <input type="hidden" name="_modal" value="{{ 'restaurante-role-'.$rest->id }}">

            <div class="rounded-3xl bg-surface-container-low px-5 py-4">
                <p class="text-xs font-bold uppercase tracking-[0.3em] text-stone-500">Usuario actual</p>
                <p class="mt-1 font-headline text-xl font-extrabold text-on-surface">{{ $rest->nombre }}</p>
            </div>

            <div class="space-y-2">
                <label class="block text-xs font-bold text-on-surface-variant uppercase tracking-wider">Nuevo rol</label>
                <select name="new_role" class="w-full rounded-xl border-none bg-surface-container-low px-4 py-3 font-medium text-on-surface focus:ring-2 focus:ring-primary" data-modal-initial-focus>
                    <option value="2" {{ old('new_role', '2') === '2' ? 'selected' : '' }}>Restaurante</option>
                    <option value="1" {{ old('new_role') === '1' ? 'selected' : '' }}>Administrador</option>
                    <option value="comensal" {{ old('new_role') === 'comensal' ? 'selected' : '' }}>Comensal</option>
                </select>
            </div>

            <div class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
                <button type="button" data-modal-close class="w-full rounded-2xl border border-stone-200 px-5 py-3.5 font-bold text-stone-600 transition hover:bg-stone-50 sm:w-auto sm:min-w-40">Cancelar</button>
                <button type="submit" class="w-full rounded-2xl bg-primary px-5 py-3.5 font-bold text-white shadow-lg shadow-primary/20 transition hover:bg-primary-container sm:w-auto sm:min-w-40">Confirmar cambio</button>
            </div>
        </form>
    </x-modal>
@endforeach
@endsection
