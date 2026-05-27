@extends('layouts.admin')

@section('content')
@php($redirectTo = request()->fullUrl())

<div class="flex justify-between items-end mb-6">
    <div>
        <h2 class="text-4xl font-extrabold text-on-surface tracking-tight mb-2">Gestión de Comensales</h2>
        <p class="text-on-surface-variant font-medium">Revisa y actualiza la información de los usuarios registrados.</p>
    </div>
</div>

<div class="flex gap-4 mb-6">
    <a href="{{ route('admin.comensales.index', ['tab' => 'activos']) }}" class="px-6 py-2 rounded-full font-bold text-sm transition-all {{ $tab === 'activos' ? 'bg-primary text-white shadow-md' : 'bg-surface-container-high text-on-surface-variant hover:bg-surface-dim' }}">
        Usuarios Activos
    </a>
    <a href="{{ route('admin.comensales.index', ['tab' => 'inactivos']) }}" class="px-6 py-2 rounded-full font-bold text-sm transition-all {{ $tab === 'inactivos' ? 'bg-primary text-white shadow-md' : 'bg-surface-container-high text-on-surface-variant hover:bg-surface-dim' }}">
        Inactivos / Eliminados
    </a>
</div>

<div class="bg-surface-container-lowest rounded-3xl overflow-hidden shadow-sm">
    <table class="w-full text-left border-collapse">
        <thead>
            <tr class="bg-surface-container-low text-on-surface-variant text-[11px] uppercase tracking-widest font-bold">
                <th class="px-6 py-4">Comensal</th>
                <th class="px-6 py-4">Contacto</th>
                <th class="px-6 py-4">Estado</th>
                <th class="px-6 py-4 text-right">Rol</th>
                <th class="px-6 py-4 text-right">Edición</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-surface-container-low">
            @foreach($comensales as $comensal)
            <tr class="hover:bg-surface-container-low/30 transition-colors group">
                <td class="px-6 py-5">
                    <p class="font-bold text-on-surface">{{ $comensal->nombre }}</p>
                    <p class="text-xs text-stone-400">ID: #{{ $comensal->id }}</p>
                </td>
                <td class="px-6 py-5">
                    <div class="space-y-1">
                        <p class="text-sm font-medium text-on-surface-variant">{{ $comensal->email }}</p>
                        <p class="text-xs text-stone-500">{{ $comensal->telefono }}</p>
                    </div>
                </td>
                <td class="px-6 py-5">
                    @if($comensal->estado === 'activo')
                        <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-[10px] font-black uppercase tracking-tight">Activo</span>
                    @else
                        <span class="px-3 py-1 bg-red-100 text-red-700 rounded-full text-[10px] font-black uppercase tracking-tight">{{ $comensal->estado }}</span>
                    @endif
                </td>
                <td class="px-6 py-5 text-right">
                    <div class="inline-flex items-center gap-3">
                        <span class="rounded-full bg-surface-container-low px-3 py-1 text-xs font-bold text-on-surface-variant">Comensal</span>
                        <button type="button" data-modal-open="comensal-role-{{ $comensal->id }}" class="rounded-xl bg-surface-container-highest px-3 py-2 text-xs font-bold text-primary transition hover:bg-primary hover:text-white">
                            Cambiar rol
                        </button>
                    </div>
                </td>
                <td class="px-6 py-5 text-right">
                    <div class="flex items-center justify-end gap-3">
                        <button type="button" data-modal-open="comensal-edit-{{ $comensal->id }}" class="p-2 rounded-lg bg-surface-container-highest text-primary hover:bg-primary-container hover:text-white transition-all shadow-sm" title="Editar Información">
                            <span class="material-symbols-outlined text-[20px]">edit</span>
                        </button>
                        @if($comensal->estado !== 'inactivo')
                            <button type="button" data-modal-open="comensal-delete-{{ $comensal->id }}" class="p-2 rounded-lg bg-surface-container-highest text-red-600 hover:bg-red-600 hover:text-white transition-all shadow-sm" title="Inactivar/Eliminar">
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
        {{ $comensales->links() }}
    </div>
</div>

@foreach($comensales as $comensal)
    <x-modal
        id="comensal-edit-{{ $comensal->id }}"
        title="Editar comensal"
        subtitle="Modifica los datos del usuario sin salir de la tabla."
        max-width="max-w-3xl"
        :auto-open="old('_modal') === 'comensal-edit-'.$comensal->id"
    >
        @include('admin.comensales._modal-form', [
            'comensal' => $comensal,
            'redirectTo' => $redirectTo,
            'modalId' => 'comensal-edit-'.$comensal->id,
        ])
    </x-modal>

    <x-modal id="comensal-delete-{{ $comensal->id }}" title="Mover a inactivos" subtitle="El comensal se ocultará del listado de activos." max-width="max-w-lg">
        <div class="space-y-6">
            <div class="rounded-3xl bg-red-50 p-5 text-red-900">
                <p class="font-headline text-xl font-extrabold">{{ $comensal->nombre }}</p>
                <p class="mt-2 text-sm text-red-900/80">Esta acción no borra el historial, solo cambia el estado.</p>
            </div>

            <form action="{{ route('admin.comensales.destroy', $comensal->id) }}" method="POST" class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
                @csrf
                <input type="hidden" name="redirect_to" value="{{ $redirectTo }}">

                <button type="button" data-modal-close class="w-full rounded-2xl border border-stone-200 px-5 py-3.5 font-bold text-stone-600 transition hover:bg-stone-50 sm:w-auto sm:min-w-40">Cancelar</button>
                <button type="submit" class="w-full rounded-2xl bg-red-600 px-5 py-3.5 font-bold text-white shadow-lg shadow-red-900/20 transition hover:bg-red-700 sm:w-auto sm:min-w-40">Mover a inactivos</button>
            </form>
        </div>
    </x-modal>

    <x-modal
        id="comensal-role-{{ $comensal->id }}"
        title="Cambiar rol"
        subtitle="Si cambias el rol, el sistema trasladará su registro al nuevo tipo de usuario."
        max-width="max-w-xl"
        :auto-open="old('_modal') === 'comensal-role-'.$comensal->id"
    >
        <form action="{{ route('admin.roles.change') }}" method="POST" class="space-y-6">
            @csrf
            <input type="hidden" name="user_type" value="comensal">
            <input type="hidden" name="user_id" value="{{ $comensal->id }}">
            <input type="hidden" name="redirect_to" value="{{ $redirectTo }}">
            <input type="hidden" name="_modal" value="{{ 'comensal-role-'.$comensal->id }}">

            <div class="rounded-3xl bg-surface-container-low px-5 py-4">
                <p class="text-xs font-bold uppercase tracking-[0.3em] text-stone-500">Usuario actual</p>
                <p class="mt-1 font-headline text-xl font-extrabold text-on-surface">{{ $comensal->nombre }}</p>
            </div>

            <div class="space-y-2">
                <label class="block text-xs font-bold text-on-surface-variant uppercase tracking-wider">Nuevo rol</label>
                <select name="new_role" class="w-full rounded-xl border-none bg-surface-container-low px-4 py-3 font-medium text-on-surface focus:ring-2 focus:ring-primary" data-modal-initial-focus>
                    <option value="comensal" {{ old('new_role', 'comensal') === 'comensal' ? 'selected' : '' }}>Comensal</option>
                    <option value="2" {{ old('new_role') === '2' ? 'selected' : '' }}>Restaurante</option>
                    <option value="1" {{ old('new_role') === '1' ? 'selected' : '' }}>Admin</option>
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
