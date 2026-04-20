@extends('layouts.restaurante')

@section('title', 'Reseñas')
@section('page-title', 'Reseñas de Clientes')

@section('content')
<div class="space-y-6">
    <div>
        <h2 class="text-2xl font-headline font-bold text-on-surface">Reseñas de Clientes</h2>
        <p class="text-stone-500 text-sm mt-1">Opiniones que los comensales dejaron sobre tu restaurante</p>
    </div>

    @forelse($resenas as $resena)
    <div class="bg-white rounded-xl shadow-sm border border-stone-100 p-6 flex flex-col md:flex-row gap-6">
        <!-- Avatar & Score -->
        <div class="shrink-0 flex flex-col items-center gap-2">
            <div class="w-12 h-12 rounded-full bg-primary-fixed flex items-center justify-center text-primary font-black text-xl">
                {{ substr($resena->comensal->nombre ?? 'C', 0, 1) }}
            </div>
            <div class="flex items-center gap-0.5">
                @for($i = 1; $i <= 5; $i++)
                <span class="material-symbols-outlined text-[18px] {{ $i <= $resena->score ? 'text-amber-400' : 'text-stone-200' }}" style="font-variation-settings: 'FILL' 1;">star</span>
                @endfor
            </div>
            <span class="text-xs font-black text-on-surface">{{ $resena->score }}/5</span>
        </div>
        <!-- Content -->
        <div class="flex-1">
            <div class="flex items-start justify-between">
                <div>
                    <p class="font-bold text-on-surface">{{ $resena->comensal->nombre ?? 'Comensal' }}</p>
                    <p class="text-xs text-stone-400">{{ $resena->menu->nombre ?? '—' }} · {{ $resena->created_at->format('d M Y') }}</p>
                </div>
            </div>
            @if($resena->comentario)
            <p class="text-stone-700 mt-3 leading-relaxed">{{ $resena->comentario }}</p>
            @endif
            @if($resena->respuesta_restaurante)
            <div class="mt-4 bg-surface-container-low p-4 rounded-lg border-l-4 border-primary">
                <p class="text-xs font-bold text-primary uppercase tracking-wider mb-1">Tu respuesta</p>
                <p class="text-sm text-stone-700">{{ $resena->respuesta_restaurante }}</p>
            </div>
            @endif
        </div>
    </div>
    @empty
    <div class="bg-white rounded-xl shadow-sm border border-stone-100 p-16 text-center text-stone-500">
        <span class="material-symbols-outlined text-5xl mb-3 opacity-40">rate_review</span>
        <p class="font-semibold text-lg">Aún no tienes reseñas</p>
        <p class="text-sm mt-1">Las reseñas aparecerán aquí cuando los comensales visiten tu restaurante.</p>
    </div>
    @endforelse

    @if($resenas->hasPages())
    <div class="mt-4">{{ $resenas->links() }}</div>
    @endif
</div>
@endsection
