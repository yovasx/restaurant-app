@extends('layouts.admin')

@section('content')
@php($redirectTo = request()->fullUrl())

<div class="px-4">
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-headline font-bold text-on-surface tracking-tight">Gestión de Categorías</h1>
            <p class="text-on-surface-variant font-medium mt-1">Administra los tipos de gastronomía y opciones</p>
        </div>
        <button type="button" data-modal-open="categoria-create-modal" class="inline-flex items-center gap-2 rounded-2xl bg-[#9e2016] px-5 py-3 font-bold text-white shadow-lg shadow-red-900/20 transition hover:bg-[#b02d21]">
            <span class="material-symbols-outlined">add_circle</span>
            Nueva Categoría
        </button>
    </div>

    <!-- Tabs -->
    <div class="flex border-b border-stone-200 mb-6 gap-8">
        <a href="{{ route('admin.categorias.index', ['tab' => 'activos']) }}" class="pb-3 text-sm font-bold tracking-wide {{ $tab === 'activos' ? 'text-[#C0392B] border-b-2 border-[#C0392B]' : 'text-stone-500 hover:text-stone-700' }}">
            Categorías Activas
        </a>
        <a href="{{ route('admin.categorias.index', ['tab' => 'inactivos']) }}" class="pb-3 text-sm font-bold tracking-wide {{ $tab === 'inactivos' ? 'text-stone-400 border-b-2 border-stone-400' : 'text-stone-500 hover:text-stone-700' }}">
            Archivo (Inactivas)
        </a>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-xl shadow-sm border border-stone-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-stone-50 border-b border-stone-200">
                        <th class="px-6 py-4 text-xs font-bold text-stone-500 uppercase tracking-wider">ID</th>
                        <th class="px-6 py-4 text-xs font-bold text-stone-500 uppercase tracking-wider">Categoría</th>
                        <th class="px-6 py-4 text-xs font-bold text-stone-500 uppercase tracking-wider">Descripción</th>
                        <th class="px-6 py-4 text-xs font-bold text-stone-500 uppercase tracking-wider">Fecha Creación</th>
                        <th class="px-6 py-4 text-xs font-bold text-stone-500 uppercase tracking-wider text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100">
                    @forelse($categorias as $cat)
                    <tr class="hover:bg-stone-50/50 transition-colors">
                        <td class="px-6 py-4 text-sm font-bold text-stone-500">#{{ $cat->id }}</td>
                        <td class="px-6 py-4 text-sm font-bold text-on-surface">{{ $cat->nombre_categoria }}</td>
                        <td class="px-6 py-4 text-sm text-stone-600">{{ $cat->descripcion ?? 'N/A' }}</td>
                        <td class="px-6 py-4 text-sm text-stone-500">{{ $cat->created_at->format('d M Y') }}</td>
                        <td class="px-6 py-4 text-right space-x-2">
                            <button type="button" data-modal-open="categoria-edit-{{ $cat->id }}" class="inline-flex p-2 text-stone-400 hover:text-primary transition-colors bg-stone-100 rounded-lg hover:bg-red-50" title="Editar">
                                <span class="material-symbols-outlined text-[20px]">edit</span>
                            </button>
                            @if($cat->estado !== 'inactivo')
                                <button type="button" data-modal-open="categoria-delete-{{ $cat->id }}" class="inline-flex p-2 text-stone-400 hover:text-red-600 transition-colors bg-stone-100 rounded-lg hover:bg-red-50" title="Eliminar/Archivar">
                                    <span class="material-symbols-outlined text-[20px]">archive</span>
                                </button>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-stone-500">
                            <span class="material-symbols-outlined text-4xl mb-2 opacity-50">category</span>
                            <p>No hay categorías {{ $tab === 'inactivos' ? 'inactivas' : 'activas' }}.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-stone-200">
            {{ $categorias->links() }}
        </div>
    </div>
</div>

<x-modal
    id="categoria-create-modal"
    title="Nueva categoría"
    subtitle="Crea una categoría sin abandonar el panel."
    max-width="max-w-2xl"
    :auto-open="old('_modal') === 'categoria-create-modal'"
>
    @include('admin.categorias._modal-form', [
        'action' => route('admin.categorias.store'),
        'categoria' => null,
        'submitLabel' => 'Crear Categoría',
        'redirectTo' => $redirectTo,
        'modalId' => 'categoria-create-modal',
    ])
</x-modal>

@foreach($categorias as $cat)
    <x-modal
        id="categoria-edit-{{ $cat->id }}"
        title="Editar categoría"
        subtitle="Actualiza nombre, descripción o estado sin salir de la tabla."
        max-width="max-w-2xl"
        :auto-open="old('_modal') === 'categoria-edit-'.$cat->id"
    >
        @include('admin.categorias._modal-form', [
            'action' => route('admin.categorias.update', $cat->id),
            'categoria' => $cat,
            'submitLabel' => 'Guardar Cambios',
            'redirectTo' => $redirectTo,
            'modalId' => 'categoria-edit-'.$cat->id,
        ])
    </x-modal>

    <x-modal id="categoria-delete-{{ $cat->id }}" title="Archivar categoría" subtitle="La categoría se moverá al archivo de inactivas." max-width="max-w-lg">
        <div class="space-y-6">
            <div class="rounded-3xl bg-amber-50 p-5 text-amber-900">
                <p class="font-headline text-xl font-extrabold">{{ $cat->nombre_categoria }}</p>
                <p class="mt-2 text-sm text-amber-900/80">Podrás verla luego en la pestaña de archivo.</p>
            </div>

            <form action="{{ route('admin.categorias.destroy', $cat->id) }}" method="POST" class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
                @csrf
                <input type="hidden" name="redirect_to" value="{{ $redirectTo }}">

                <button type="button" data-modal-close class="w-full rounded-2xl border border-stone-200 px-5 py-3.5 font-bold text-stone-600 transition hover:bg-stone-50 sm:w-auto sm:min-w-40">Cancelar</button>
                <button type="submit" class="w-full rounded-2xl bg-amber-600 px-5 py-3.5 font-bold text-white shadow-lg shadow-amber-900/20 transition hover:bg-amber-700 sm:w-auto sm:min-w-40">Archivar categoría</button>
            </form>
        </div>
    </x-modal>
@endforeach
@endsection
