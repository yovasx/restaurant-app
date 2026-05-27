@extends('layouts.restaurante')

@section('title', 'Promociones')
@section('page-title', 'Mis Promociones')

@section('content')
@php($redirectTo = request()->fullUrl())

<div class="space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-headline font-bold text-on-surface">Gestión de Promociones</h2>
            <p class="text-stone-500 text-sm mt-1">Crea y administra ofertas especiales de tu restaurante</p>
        </div>
        <button type="button" data-modal-open="promocion-create-modal"
           class="flex items-center gap-2 bg-primary text-white py-2.5 px-5 rounded-lg font-bold text-sm shadow-md hover:bg-[#c0392b] transition-colors">
             <span class="material-symbols-outlined text-sm">add</span> Nueva Promoción
        </button>
    </div>

    <div class="bg-white rounded-2xl shadow-sm overflow-hidden border border-stone-100">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-surface-container-low/60 border-b border-stone-100">
                    <th class="px-6 py-4 text-xs font-bold text-stone-500 uppercase tracking-wider">Nombre</th>
                    <th class="px-6 py-4 text-xs font-bold text-stone-500 uppercase tracking-wider">Tipo</th>
                    <th class="px-6 py-4 text-xs font-bold text-stone-500 uppercase tracking-wider">Valor</th>
                    <th class="px-6 py-4 text-xs font-bold text-stone-500 uppercase tracking-wider">Vigencia</th>
                    <th class="px-6 py-4 text-xs font-bold text-stone-500 uppercase tracking-wider">Estado</th>
                    <th class="px-6 py-4 text-xs font-bold text-stone-500 uppercase tracking-wider text-right">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-stone-100">
                @forelse($promociones as $promo)
                <tr class="hover:bg-stone-50/50 transition-colors">
                    <td class="px-6 py-4">
                        <p class="text-sm font-bold text-on-surface">{{ $promo->nombre }}</p>
                        @if($promo->condicion)
                        <p class="text-xs text-stone-400 mt-0.5">{{ Str::limit($promo->condicion, 50) }}</p>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-100 text-amber-800">
                            {{ ucfirst($promo->tipo) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-sm font-bold text-on-surface">
                        {{ $promo->valor ? number_format($promo->valor, 2).'%' : '—' }}
                    </td>
                    <td class="px-6 py-4 text-sm text-stone-600">
                        @if($promo->fecha_inicio)
                            {{ $promo->fecha_inicio->format('d/m/y') }} – {{ $promo->fecha_fin?->format('d/m/y') ?? '∞' }}
                        @else
                            <span class="text-stone-400">Sin límite</span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold {{ $promo->estado === 'activo' ? 'bg-green-100 text-green-800' : 'bg-stone-100 text-stone-500' }}">
                            <span class="h-1.5 w-1.5 rounded-full {{ $promo->estado === 'activo' ? 'bg-green-500' : 'bg-stone-400' }}"></span>
                            {{ ucfirst($promo->estado) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex justify-end gap-1">
                            <button type="button" data-modal-open="promocion-edit-{{ $promo->id }}"
                               class="p-1.5 text-stone-400 hover:text-primary transition-colors">
                                <span class="material-symbols-outlined text-lg">edit</span>
                            </button>
                            @if($promo->estado === 'activo')
                                <button type="button" data-modal-open="promocion-delete-{{ $promo->id }}" class="p-1.5 text-stone-400 hover:text-red-600 transition-colors">
                                    <span class="material-symbols-outlined text-lg">archive</span>
                                </button>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-16 text-center text-stone-500">
                        <span class="material-symbols-outlined text-5xl mb-3 opacity-40">local_offer</span>
                        <p class="font-semibold">No tienes promociones activas.</p>
                        <button type="button" data-modal-open="promocion-create-modal" class="mt-2 inline-block text-primary font-bold underline text-sm">Crear primera promoción</button>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        @if($promociones->hasPages())
        <div class="px-6 py-4 border-t border-stone-100">{{ $promociones->links() }}</div>
        @endif
    </div>
</div>

<x-modal
    id="promocion-create-modal"
    title="Crear promoción"
    subtitle="Lanza nuevas campañas sin salir del panel."
    max-width="max-w-3xl"
    :auto-open="old('_modal') === 'promocion-create-modal'"
>
    @include('restaurante.promociones._modal-form', [
        'action' => route('restaurante.promociones.store'),
        'promocion' => null,
        'submitLabel' => 'Lanzar promoción',
        'redirectTo' => $redirectTo,
        'modalId' => 'promocion-create-modal',
        'formKey' => 'promocion-create',
    ])
</x-modal>

@include('restaurante.promociones._table-modals', [
    'promociones' => $promociones,
    'redirectTo' => $redirectTo,
])
@endsection
