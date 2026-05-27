@extends('layouts.restaurante')

@section('title', 'Gestión de Menú')
@section('page-title', 'Gestión de Menú')

@section('content')
@php($redirectTo = request()->fullUrl())

<!-- Workspace Grid -->
<div class="space-y-4">
    <div class="flex justify-between items-end px-2">
        <div>
            <h3 class="font-headline font-bold text-lg text-on-surface">Manejo de Productos</h3>
            <p class="text-sm text-stone-500">Administra tus platos, categorías y precios</p>
        </div>
        <div class="flex gap-2">
            <button type="button" data-modal-open="restaurante-producto-create-modal" class="flex items-center gap-2 bg-primary text-white py-2 px-4 rounded-lg font-headline font-bold text-sm shadow-md hover:bg-primary-container transition-colors">
                <span class="material-symbols-outlined text-sm" data-icon="add">add</span> Nuevo Plato
            </button>
        </div>
    </div>

    <div class="bg-surface-container-lowest rounded-2xl shadow-sm overflow-hidden border-none">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-surface-container-low/50">
                    <th class="px-6 py-4 text-xs font-bold text-stone-500 uppercase tracking-wider">Nombre del Producto</th>
                    <th class="px-6 py-4 text-xs font-bold text-stone-500 uppercase tracking-wider">Categoría</th>
                    <th class="px-6 py-4 text-xs font-bold text-stone-500 uppercase tracking-wider">Precio</th>
                    <th class="px-6 py-4 text-xs font-bold text-stone-500 uppercase tracking-wider">Stock</th>
                    <th class="px-6 py-4 text-xs font-bold text-stone-500 uppercase tracking-wider text-right">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-stone-100">
                @forelse($productos as $producto)
                <tr class="hover:bg-stone-50/50 transition-colors">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg bg-surface-container overflow-hidden shrink-0 flex items-center justify-center text-primary font-bold">
                                {{ substr($producto->nombre, 0, 1) }}
                            </div>
                            <div>
                                <p class="text-sm font-bold text-on-surface">{{ $producto->nombre }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-sm text-stone-600">{{ $producto->categoria->nombre_categoria ?? 'Sin categoría' }}</td>
                    <td class="px-6 py-4 text-sm font-bold text-on-surface">{{ number_format($producto->precio, 2) }}Bs.</td>
                    <td class="px-6 py-4 text-sm text-stone-600">{{ $producto->stock }} unids.</td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex justify-end gap-1">
                            <button type="button" data-modal-open="producto-edit-{{ $producto->id }}" class="p-1.5 text-stone-400 hover:text-primary transition-colors">
                                <span class="material-symbols-outlined text-lg" data-icon="edit">edit</span>
                            </button>
                            <button type="button" data-modal-open="producto-delete-{{ $producto->id }}" class="p-1.5 text-stone-400 hover:text-red-600 transition-colors">
                                <span class="material-symbols-outlined text-lg" data-icon="delete">delete</span>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-8 text-center text-stone-500 text-sm">
                        No hay productos registrados en el sistema.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        @if($productos->hasPages())
        <div class="bg-surface-container-low/30 px-6 py-4 flex justify-between items-center">
            <div class="w-full">
                {{ $productos->links() }}
            </div>
        </div>
        @endif
    </div>
</div>

@include('productos._table-modals', [
    'productos' => $productos,
    'categorias' => $categorias,
    'redirectTo' => $redirectTo,
])
@endsection
