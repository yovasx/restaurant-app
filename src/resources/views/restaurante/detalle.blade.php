@extends('layouts.app')

@section('title', $restaurante->nombre)
@section('layout-flush', 'true')

@php
    $theme = $restaurante->resolvedTheme();
    $hasCoordinates = is_numeric($restaurante->latitud) && is_numeric($restaurante->longitud);
@endphp

@section('page-theme')
<style>
    :root {
        --brand-primary: {{ $theme['primary'] }};
        --brand-secondary: {{ $theme['secondary'] }};
        --brand-accent: {{ $theme['accent'] }};
    }

    .route-map-shell {
        min-height: 360px;
    }

    .route-map {
        min-height: 360px;
        height: 100%;
        width: 100%;
        background: linear-gradient(135deg, #fff8f2 0%, #f8ede8 100%);
    }

    .route-chip {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        border-radius: 9999px;
        border: 1px solid #e7e5e4;
        background: #ffffff;
        color: var(--brand-primary);
        padding: 0.55rem 0.9rem;
        font-size: 0.875rem;
        font-weight: 700;
        line-height: 1;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.06);
        transition: border-color 180ms ease, color 180ms ease, background-color 180ms ease;
    }

    .route-chip:hover {
        border-color: #fecaca;
        background-color: #fffaf6;
    }

    .route-directions details[open] summary .route-disclosure-icon {
        transform: rotate(180deg);
    }

    .route-directions summary {
        list-style: none;
    }

    .route-directions summary::-webkit-details-marker {
        display: none;
    }

    .route-status-pill[data-tone="idle"] {
        background-color: #fef3c7;
        color: #92400e;
    }

    .route-status-pill[data-tone="loading"] {
        background-color: #fee2e2;
        color: #9f1239;
    }

    .route-status-pill[data-tone="success"] {
        background-color: #dcfce7;
        color: #166534;
    }

    .route-status-pill[data-tone="error"] {
        background-color: #e5e7eb;
        color: #374151;
    }

    .route-mode-button[data-active="true"] {
        background: linear-gradient(135deg, var(--brand-primary), var(--brand-secondary));
        color: #fff;
        border-color: transparent;
        box-shadow: 0 10px 25px rgba(158, 32, 22, 0.18);
    }

    .route-mode-button[data-active="false"] {
        background-color: #fff;
        color: #57534e;
        border-color: #e7e5e4;
    }

    .route-marker {
        width: 44px;
        height: 44px;
        border-radius: 9999px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        border: 3px solid rgba(255, 255, 255, 0.92);
        box-shadow: 0 10px 24px rgba(0, 0, 0, 0.18);
        font-size: 19px;
        font-weight: 700;
    }

    .route-marker--restaurant {
        background: linear-gradient(135deg, var(--brand-primary), var(--brand-secondary));
    }

    .route-marker--user {
        background: linear-gradient(135deg, #0f766e, #14b8a6);
    }

    @media (max-width: 640px) {
        .route-map-shell,
        .route-map {
            min-height: 300px;
        }
    }
</style>
@if ($hasCoordinates)
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
@endif
@endsection

@section('content')
<section class="max-w-7xl mx-auto px-4 pb-16 sm:px-6 lg:px-8">
    <div class="mb-6 mt-6">
        <a href="{{ auth()->guard('comensal')->check() ? route('comensal.inicio') : route('home') }}" class="inline-flex items-center gap-2 text-sm font-bold hover:underline" style="color:var(--brand-primary)">
            <span class="material-symbols-outlined">arrow_back</span> Volver
        </a>
    </div>

    <div class="overflow-hidden rounded-[28px] bg-white shadow-sm ring-1 ring-stone-100">
        <div class="relative h-64 w-full overflow-hidden bg-stone-200 sm:h-72 lg:h-80">
            @if($restaurante->logo_url_resolved)
            <img class="absolute left-4 top-4 h-14 w-14 rounded-2xl bg-white/85 object-contain p-1.5 shadow-sm backdrop-blur sm:h-16 sm:w-16" src="{{ $restaurante->logo_url_resolved }}" alt="{{ $restaurante->nombre }}" />
            @endif
            <img class="h-full w-full object-cover" src="{{ media_url($restaurante->foto_portada) ?: 'https://via.placeholder.com/1600x600?text=Restaurante' }}" alt="{{ $restaurante->nombre }}" />
            <div class="absolute inset-x-0 bottom-0 h-32 bg-gradient-to-t from-black/60 to-transparent"></div>
        </div>

        <div class="p-5 sm:p-8">
            <div class="mb-6 flex flex-col gap-5 lg:flex-row lg:items-start lg:justify-between">
                <div class="max-w-3xl">
                    <div class="mb-3 inline-flex items-center gap-2 rounded-full bg-[color:var(--brand-accent)]/18 px-3 py-1 text-xs font-bold uppercase tracking-[0.25em] text-[color:var(--brand-secondary)]">
                        <span class="material-symbols-outlined text-base">place</span>
                        Destino destacado
                    </div>
                    <h1 class="font-headline text-3xl font-extrabold tracking-tight text-on-surface sm:text-4xl">{{ $restaurante->nombre }}</h1>
                    <p class="mt-3 text-sm leading-6 text-stone-600 sm:text-base">{{ $restaurante->direccion ?: 'Dirección no disponible' }}{{ $restaurante->zona ? ' · '.$restaurante->zona : '' }}</p>
                    <div class="mt-3 flex flex-wrap items-center gap-3">
                        @if ($hasCoordinates)
                            <button
                                id="openRouteModalButton"
                                type="button"
                                data-modal-open="restaurant-route-modal"
                                class="route-chip"
                            >
                                <span class="material-symbols-outlined text-base">near_me</span>
                                Cómo llegar
                            </button>
                        @else
                            <span class="inline-flex items-center gap-2 rounded-full border border-dashed border-stone-300 bg-white px-3 py-2 text-xs font-bold uppercase tracking-[0.18em] text-stone-500">
                                <span class="material-symbols-outlined text-base">location_off</span>
                                Ubicación pendiente
                            </span>
                        @endif
                    </div>
                </div>
                <div class="rounded-3xl bg-surface-container-low px-5 py-4 text-left shadow-inner ring-1 ring-stone-100 lg:min-w-[220px] lg:text-right">
                    <p class="text-xs font-bold uppercase tracking-[0.3em] text-stone-500">Promociones</p>
                    <p class="mt-1 text-3xl font-black text-on-surface">{{ $promociones->count() }}</p>
                    <p class="text-sm text-stone-500">activas ahora</p>
                </div>
            </div>

            <div class="mb-10 flex flex-wrap gap-3 text-sm text-stone-600">
                @if($restaurante->telefono)
                    <span class="inline-flex items-center gap-2 rounded-full border border-stone-200 bg-white px-4 py-2 shadow-sm"><span class="material-symbols-outlined text-base" style="color:var(--brand-primary)">call</span> +591 {{ $restaurante->telefono }}</span>
                @endif
                @if($restaurante->email_reservas)
                    <span class="inline-flex items-center gap-2 rounded-full border border-stone-200 bg-white px-4 py-2 shadow-sm"><span class="material-symbols-outlined text-base" style="color:var(--brand-primary)">mail</span> {{ $restaurante->email_reservas }}</span>
                @endif
                @if($restaurante->instagram)
                    <span class="inline-flex items-center gap-2 rounded-full border border-stone-200 bg-white px-4 py-2 shadow-sm"><span class="material-symbols-outlined text-base" style="color:var(--brand-primary)">camera_alt</span> {{ $restaurante->instagram }}</span>
                @endif
                @if($restaurante->facebook_url)
                    <span class="inline-flex items-center gap-2 rounded-full border border-stone-200 bg-white px-4 py-2 shadow-sm"><span class="material-symbols-outlined text-base" style="color:var(--brand-primary)">language</span> {{ $restaurante->facebook_url }}</span>
                @endif
            </div>

            <div class="mb-8 rounded-2xl bg-white p-5 ring-1 ring-stone-100 shadow-sm">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <div class="flex items-center gap-0.5">
                            @for ($i = 1; $i <= 5; $i++)
                                <span class="material-symbols-outlined text-2xl {{ $i <= round($promedio ?? 0) ? 'text-amber-400' : 'text-stone-200' }}" style="font-variation-settings: 'FILL' 1;">star</span>
                            @endfor
                        </div>
                        <div>
                            <span class="text-2xl font-black text-on-surface">{{ $promedio ? number_format($promedio, 1) : '—' }}</span>
                            <span class="text-sm text-stone-500"> · {{ $totalResenas }} reseña{{ $totalResenas !== 1 ? 's' : '' }}</span>
                        </div>
                    </div>
                    @auth('comensal')
                        @if ($miResena)
                            <button id="editReviewBtn" data-score="{{ $miResena->score ?? 5 }}" data-comentario="{{ $miResena->comentario ?? '' }}" class="inline-flex items-center gap-2 rounded-full bg-primary text-white px-5 py-2 text-sm font-bold shadow-sm hover:brightness-110 transition-all">
                                <span class="material-symbols-outlined text-sm">edit</span> Editar mi reseña
                            </button>
                        @else
                            <button id="leaveReviewBtn" class="inline-flex items-center gap-2 rounded-full bg-primary text-white px-5 py-2 text-sm font-bold shadow-sm hover:brightness-110 transition-all">
                                <span class="material-symbols-outlined text-sm">rate_review</span> Dejar reseña
                            </button>
                        @endif
                    @endauth
                </div>
            </div>

            <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
                <div class="md:col-span-2">
                    <h2 class="mb-4 text-xl font-bold">Menú</h2>
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        @forelse($menus as $menu)
                        <div class="flex flex-col gap-3 rounded-2xl bg-surface-container-lowest p-4 ring-1 ring-stone-100" data-menu-name="{{ $menu->nombre }}">
                            <div class="flex items-center gap-4">
                                <div class="h-20 w-20 shrink-0 overflow-hidden rounded-xl bg-stone-100">
                                    <img src="{{ media_url($menu->foto_plato) ?: 'https://via.placeholder.com/240x160?text=Plato' }}" class="h-full w-full object-cover" alt="{{ $menu->nombre }}" />
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-start justify-between gap-4">
                                        <h3 class="font-bold text-on-surface truncate">{{ $menu->nombre }}</h3>
                                        <span class="shrink-0 font-black text-on-surface">{{ $menu->precio }} Bs.</span>
                                    </div>
                                    <p class="mt-1 text-sm text-stone-500">{{ \Illuminate\Support\Str::limit($menu->descripcion, 80) }}</p>
                                </div>
                            </div>
                            @auth('comensal')
                            <div class="flex justify-end">
                                @php $menuResena = $miResenaPlatos->get($menu->id); @endphp
                                @if ($menuResena)
                                    <button type="button" class="menu-review-btn inline-flex items-center gap-1.5 rounded-full border border-amber-300 bg-amber-50 px-3.5 py-1.5 text-xs font-bold text-amber-700 transition-colors hover:bg-amber-100" data-menu-id="{{ $menu->id }}" data-score="{{ $menuResena->score }}" data-comentario="{{ $menuResena->comentario ?? '' }}" data-has-review="true">
                                        <span class="material-symbols-outlined text-sm" style="font-variation-settings:'FILL' 1;">star</span> Editar reseña
                                    </button>
                                @else
                                    <button type="button" class="menu-review-btn inline-flex items-center gap-1.5 rounded-full border border-stone-200 bg-white px-3.5 py-1.5 text-xs font-bold text-stone-600 transition-colors hover:border-primary/30 hover:text-primary" data-menu-id="{{ $menu->id }}" data-score="5" data-comentario="" data-has-review="false">
                                        <span class="material-symbols-outlined text-sm">rate_review</span> Reseñar plato
                                    </button>
                                @endif
                            </div>
                            @endauth
                        </div>
                        @empty
                        <div class="rounded-2xl border border-dashed border-stone-200 bg-stone-50 px-5 py-8 text-center text-sm text-stone-500 md:col-span-2">
                            Este restaurante aún no publicó platos activos.
                        </div>
                        @endforelse
                    </div>
                </div>
                <aside class="md:col-span-1">
                    <h2 class="mb-4 text-xl font-bold">Promociones</h2>
                    @forelse($promociones as $promo)
                    <div class="mb-4 rounded-2xl bg-[#fff8f2] p-4 ring-1 ring-[color:var(--brand-accent)]/10">
                        <h3 class="font-bold text-on-surface">{{ $promo->nombre }}</h3>
                        <p class="mt-1 text-sm text-stone-600">{{ $promo->tipo }} · {{ $promo->valor }}</p>
                        @if($promo->imagen)
                        <div class="mt-3">
                            <img src="{{ media_url($promo->imagen) }}" class="h-32 w-full rounded-xl object-cover" alt="{{ $promo->nombre }}" />
                        </div>
                        @endif
                    </div>
                    @empty
                    <p class="rounded-2xl border border-dashed border-stone-200 bg-stone-50 px-5 py-8 text-center text-sm text-stone-500">No hay promociones activas.</p>
                    @endforelse
                </aside>
            </div>

            {{-- Reseñas --}}
            <div class="mt-8">
                <h2 class="mb-4 text-xl font-bold">Reseñas</h2>
                @if ($resenasRecientes->count() > 0)
                    <div class="space-y-4">
                        @foreach ($resenasRecientes as $resena)
                            <div class="rounded-2xl bg-white p-4 ring-1 ring-stone-100">
                                <div class="flex items-center justify-between mb-2">
                                    <div class="flex items-center gap-2">
                                        <div class="h-8 w-8 rounded-full bg-primary/10 flex items-center justify-center">
                                            <span class="material-symbols-outlined text-primary text-sm">person</span>
                                        </div>
                                        <div>
                                            <p class="text-sm font-bold text-on-surface">{{ $resena->comensal->nombre ?? 'Comensal' }}</p>
                                            <p class="text-[10px] text-stone-400">{{ $resena->created_at->diffForHumans() }}</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-0.5">
                                        @for ($i = 1; $i <= 5; $i++)
                                            <span class="material-symbols-outlined text-sm {{ $i <= $resena->score ? 'text-amber-400' : 'text-stone-200' }}" style="font-variation-settings: 'FILL' 1;">star</span>
                                        @endfor
                                    </div>
                                </div>
                                @if ($resena->comentario)
                                    <p class="text-sm text-stone-600 leading-relaxed">{{ $resena->comentario }}</p>
                                @endif
                                @if ($resena->menu_id && $resena->menu)
                                    <p class="mt-2 text-xs text-stone-400 italic">Reseñó el plato: {{ $resena->menu->nombre }}</p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="rounded-2xl border border-dashed border-stone-200 bg-stone-50 px-5 py-8 text-center text-sm text-stone-500">
                        Aún no hay reseñas para este restaurante.
                    </div>
                @endif
            </div>

            {{-- Review form modal --}}
            @auth('comensal')
            <div id="reviewModal" class="fixed inset-0 bg-black/40 hidden items-center justify-center z-50"
                 data-title-edit="Editar mi reseña"
                 data-title-new="Dejar reseña"
                 data-title-dish-edit="Editar reseña del plato"
                 data-title-dish-new="Reseñar plato"
                 data-subtitle-restaurant="Califica tu experiencia en {{ $restaurante->nombre }}."
                 data-subtitle-restaurant="Califica tu experiencia en {{ $restaurante->nombre }}.">
                <div class="bg-white rounded-xl p-6 w-11/12 max-w-md">
                    <h3 id="reviewModalTitle" class="font-headline font-extrabold text-lg mb-1">{{ $miResena ? 'Editar mi reseña' : 'Dejar reseña' }}</h3>
                    <p id="reviewModalSubtitle" class="text-xs text-stone-500 mb-4">Califica tu experiencia en {{ $restaurante->nombre }}.</p>
                    <form method="POST" action="{{ route('comensal.resena.save', $restaurante->id) }}">
                        @csrf
                        <input type="hidden" name="menu_id" id="reviewMenuId" value="">
                        <div class="mb-4">
                            <label class="text-sm font-bold block mb-2">Puntuación</label>
                            <div class="flex gap-2" id="reviewStarPicker">
                                @for ($i = 1; $i <= 5; $i++)
                                    <button type="button" class="review-star material-symbols-outlined text-3xl text-stone-300 hover:text-amber-400 transition-colors" data-value="{{ $i }}" style="font-variation-settings: 'FILL' 1;">star</button>
                                @endfor
                            </div>
                            <input type="hidden" name="score" id="reviewScore" value="5">
                            @error('score') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div class="mb-4">
                            <label class="text-sm font-bold block mb-2">Comentario <span class="text-stone-400 font-normal">(opcional)</span></label>
                            <textarea name="comentario" id="reviewComentario" rows="4" class="w-full bg-stone-100 border-0 rounded-lg p-3 text-sm resize-none" placeholder="Cuenta tu experiencia..." maxlength="1000"></textarea>
                            @error('comentario') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div class="flex justify-end gap-2">
                            <button type="button" id="closeReviewModal" class="px-4 py-2 rounded-full text-sm font-bold text-stone-500 hover:bg-stone-100 transition-colors">Cancelar</button>
                            <button type="submit" class="px-6 py-2 rounded-full bg-primary text-white text-sm font-bold shadow-sm hover:brightness-110 transition-all">Guardar reseña</button>
                        </div>
                    </form>
                </div>
            </div>
            @endauth

            @if ($hasCoordinates)
                <x-modal
                    id="restaurant-route-modal"
                    title="Cómo llegar"
                    subtitle="{{ $restaurante->nombre }} · {{ $restaurante->direccion ?: 'Dirección no disponible' }}"
                    max-width="max-w-6xl"
                >
                    <section class="overflow-hidden rounded-[28px] border border-[color:var(--brand-accent)]/20 bg-[#fffaf6] shadow-sm" aria-labelledby="route-section-title">
                        <div class="grid grid-cols-1 lg:grid-cols-[360px_minmax(0,1fr)]">
                            <aside class="border-b border-stone-200/70 p-5 sm:p-6 lg:border-b-0 lg:border-r">
                                <div class="flex items-start justify-between gap-4">
                                    <div>
                                        <p class="text-xs font-bold uppercase tracking-[0.3em] text-[color:var(--brand-secondary)]">Ruta interactiva</p>
                                        <h2 id="route-section-title" class="mt-2 font-headline text-2xl font-extrabold text-on-surface">Ubicación y ruta</h2>
                                    </div>
                                    <span id="routeStatusPill" class="route-status-pill rounded-full px-3 py-1 text-xs font-bold" data-tone="idle">Mapa listo</span>
                                </div>

                                <p class="mt-3 text-sm leading-6 text-stone-600">Visualiza el restaurante en el mapa y, cuando quieras, traza una ruta desde tu ubicación sin salir del sistema.</p>

                                <div class="mt-5 rounded-3xl bg-white p-4 shadow-sm ring-1 ring-stone-100">
                                    <div class="flex items-start gap-3">
                                        <div class="mt-0.5 flex h-10 w-10 items-center justify-center rounded-2xl text-white" style="background:linear-gradient(135deg, var(--brand-primary), var(--brand-secondary));">
                                            <span class="material-symbols-outlined text-xl">restaurant</span>
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <p class="text-xs font-bold uppercase tracking-[0.25em] text-stone-500">Destino</p>
                                            <p class="mt-1 text-sm font-extrabold text-on-surface">{{ $restaurante->nombre }}</p>
                                            <p class="mt-1 text-sm leading-6 text-stone-600">{{ $restaurante->direccion ?: 'Dirección no disponible' }}</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-5 space-y-3">
                                    <button id="traceRouteButton" type="button" class="inline-flex w-full items-center justify-center gap-2 rounded-2xl px-4 py-3 text-sm font-bold text-white shadow-lg transition hover:brightness-110 disabled:cursor-not-allowed disabled:opacity-60" style="background:linear-gradient(135deg, var(--brand-primary), var(--brand-secondary));">
                                        <span class="material-symbols-outlined text-lg">near_me</span>
                                        Trazar ruta desde mi ubicación
                                    </button>

                                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-1 xl:grid-cols-2">
                                        <button id="refreshRouteButton" type="button" class="inline-flex items-center justify-center gap-2 rounded-2xl border border-stone-200 bg-white px-4 py-3 text-sm font-bold text-stone-700 shadow-sm transition hover:border-stone-300 hover:bg-stone-50 disabled:cursor-not-allowed disabled:opacity-60" disabled>
                                            <span class="material-symbols-outlined text-lg">refresh</span>
                                            Actualizar ruta
                                        </button>
                                        <button id="recenterMapButton" type="button" class="inline-flex items-center justify-center gap-2 rounded-2xl border border-stone-200 bg-white px-4 py-3 text-sm font-bold text-stone-700 shadow-sm transition hover:border-stone-300 hover:bg-stone-50">
                                            <span class="material-symbols-outlined text-lg">center_focus_strong</span>
                                            Recentrar mapa
                                        </button>
                                    </div>
                                </div>

                                <div class="mt-5">
                                    <p class="mb-3 text-xs font-bold uppercase tracking-[0.25em] text-stone-500">Modo de viaje</p>
                                    <div class="grid grid-cols-2 gap-3">
                                        <button type="button" class="route-mode-button rounded-2xl border px-4 py-3 text-sm font-bold transition" data-route-mode="walking" data-active="true">
                                            A pie
                                        </button>
                                        <button type="button" class="route-mode-button rounded-2xl border px-4 py-3 text-sm font-bold transition" data-route-mode="driving" data-active="false">
                                            En auto
                                        </button>
                                    </div>
                                </div>

                                <div class="mt-5 grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-2 xl:grid-cols-3">
                                    <div class="rounded-3xl bg-white p-4 shadow-sm ring-1 ring-stone-100">
                                        <p class="text-[11px] font-bold uppercase tracking-[0.25em] text-stone-500">Distancia</p>
                                        <p id="routeDistanceValue" class="mt-2 text-lg font-black text-on-surface">--</p>
                                    </div>
                                    <div class="rounded-3xl bg-white p-4 shadow-sm ring-1 ring-stone-100">
                                        <p class="text-[11px] font-bold uppercase tracking-[0.25em] text-stone-500">Tiempo estimado</p>
                                        <p id="routeDurationValue" class="mt-2 text-lg font-black text-on-surface">--</p>
                                    </div>
                                    <div class="rounded-3xl bg-white p-4 shadow-sm ring-1 ring-stone-100 col-span-2 sm:col-span-1 lg:col-span-2 xl:col-span-1">
                                        <p class="text-[11px] font-bold uppercase tracking-[0.25em] text-stone-500">Modo</p>
                                        <p id="routeModeValue" class="mt-2 text-lg font-black text-on-surface">A pie</p>
                                    </div>
                                </div>

                                <div class="route-directions mt-5 rounded-3xl bg-white p-4 shadow-sm ring-1 ring-stone-100">
                                    <details id="routeDirectionsDetails">
                                        <summary class="flex cursor-pointer items-center justify-between gap-4">
                                            <div>
                                                <p class="text-xs font-bold uppercase tracking-[0.25em] text-stone-500">Ver indicaciones</p>
                                                <p class="mt-1 text-sm text-stone-600">Ábrelas solo si quieres seguir los pasos detallados.</p>
                                            </div>
                                            <div class="flex items-center gap-3">
                                                <span id="routeStepsCount" class="rounded-full bg-stone-100 px-3 py-1 text-xs font-bold text-stone-500">0 pasos</span>
                                                <span class="route-disclosure-icon material-symbols-outlined text-stone-400 transition-transform">expand_more</span>
                                            </div>
                                        </summary>
                                        <div class="mt-4 border-t border-stone-100 pt-4">
                                            <div id="routeStepsEmpty" class="rounded-2xl border border-dashed border-stone-200 bg-stone-50 px-4 py-5 text-sm leading-6 text-stone-500">
                                                Pulsa <span class="font-bold text-stone-700">Trazar ruta desde mi ubicación</span> para ver el camino recomendado hasta este restaurante.
                                            </div>
                                            <ol id="routeStepsList" class="mt-4 hidden space-y-3"></ol>
                                        </div>
                                    </details>
                                </div>
                            </aside>

                            <div class="route-map-shell relative bg-stone-100">
                                <div id="restaurantRouteMap" class="route-map"></div>
                                <div class="pointer-events-none absolute inset-x-0 top-0 flex justify-center p-4">
                                    <div id="routeBanner" class="max-w-md rounded-full bg-white/92 px-4 py-2 text-center text-xs font-semibold text-stone-600 shadow-lg backdrop-blur">
                                        El mapa está listo. Activa tu ubicación cuando quieras para dibujar la ruta.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
                </x-modal>
            @endif
        </div>
    </div>
</section>

@if ($hasCoordinates)
<script>
document.addEventListener('DOMContentLoaded', function () {
    const routeData = {
        restaurantName: @json($restaurante->nombre),
        address: @json($restaurante->direccion ?: 'Dirección no disponible'),
        zone: @json($restaurante->zona),
        lat: Number(@json((float) $restaurante->latitud)),
        lng: Number(@json((float) $restaurante->longitud)),
        brandPrimary: @json($theme['primary']),
        brandSecondary: @json($theme['secondary']),
    };

    const elements = {
        openModalButton: document.getElementById('openRouteModalButton'),
        modal: document.getElementById('restaurant-route-modal'),
        statusPill: document.getElementById('routeStatusPill'),
        banner: document.getElementById('routeBanner'),
        traceButton: document.getElementById('traceRouteButton'),
        refreshButton: document.getElementById('refreshRouteButton'),
        recenterButton: document.getElementById('recenterMapButton'),
        distance: document.getElementById('routeDistanceValue'),
        duration: document.getElementById('routeDurationValue'),
        modeValue: document.getElementById('routeModeValue'),
        stepsList: document.getElementById('routeStepsList'),
        stepsEmpty: document.getElementById('routeStepsEmpty'),
        stepsCount: document.getElementById('routeStepsCount'),
    };

    const modeLabels = {
        walking: 'A pie',
        driving: 'En auto',
    };

    let currentMode = 'walking';
    let userPosition = null;
    let routeLayer = null;
    let userMarker = null;
    let requestController = null;

    const restaurantIcon = L.divIcon({
        html: '<div class="route-marker route-marker--restaurant"><span class="material-symbols-outlined">restaurant</span></div>',
        className: '',
        iconSize: [44, 44],
        iconAnchor: [22, 22],
    });

    const userIcon = L.divIcon({
        html: '<div class="route-marker route-marker--user"><span class="material-symbols-outlined">person_pin_circle</span></div>',
        className: '',
        iconSize: [44, 44],
        iconAnchor: [22, 22],
    });

    const map = L.map('restaurantRouteMap', {
        zoomControl: true,
        scrollWheelZoom: true,
        dragging: true,
        doubleClickZoom: true,
        touchZoom: true,
        boxZoom: true,
        keyboard: true,
    }).setView([routeData.lat, routeData.lng], 15);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors',
    }).addTo(map);

    const restaurantMarker = L.marker([routeData.lat, routeData.lng], { icon: restaurantIcon }).addTo(map);
    restaurantMarker.bindPopup('<div style="min-width:180px"><strong>' + escapeHtml(routeData.restaurantName) + '</strong><br><span>' + escapeHtml(routeData.address) + '</span></div>');

    window.setTimeout(() => map.invalidateSize(), 250);

    elements.openModalButton?.addEventListener('click', () => {
        window.setTimeout(() => {
            map.invalidateSize();
            recenterMap();
        }, 180);
    });

    if (elements.modal) {
        const observer = new MutationObserver(() => {
            if (!elements.modal.classList.contains('hidden')) {
                window.setTimeout(() => {
                    map.invalidateSize();
                    recenterMap();
                }, 120);
            }
        });

        observer.observe(elements.modal, { attributes: true, attributeFilter: ['class'] });
    }

    setStatus('Mapa listo', 'idle', 'El mapa está listo. Activa tu ubicación cuando quieras para dibujar la ruta.');
    updateModeButtons();

    elements.traceButton.addEventListener('click', () => locateUserAndTrace(true));
    elements.refreshButton.addEventListener('click', () => locateUserAndTrace(true));
    elements.recenterButton.addEventListener('click', recenterMap);

    document.querySelectorAll('[data-route-mode]').forEach((button) => {
        button.addEventListener('click', () => {
            const nextMode = button.dataset.routeMode;
            if (!nextMode || nextMode === currentMode) {
                return;
            }

            currentMode = nextMode;
            elements.modeValue.textContent = modeLabels[currentMode];
            updateModeButtons();

            if (userPosition) {
                calculateRoute(userPosition.lat, userPosition.lng);
            }
        });
    });

    function setStatus(label, tone, bannerMessage) {
        elements.statusPill.textContent = label;
        elements.statusPill.dataset.tone = tone;
        elements.banner.textContent = bannerMessage;
    }

    function setLoadingState(loading) {
        elements.traceButton.disabled = loading;
        elements.refreshButton.disabled = loading || !userPosition;
        document.querySelectorAll('[data-route-mode]').forEach((button) => {
            button.disabled = loading;
        });
    }

    function updateModeButtons() {
        document.querySelectorAll('[data-route-mode]').forEach((button) => {
            const isActive = button.dataset.routeMode === currentMode;
            button.dataset.active = isActive ? 'true' : 'false';
        });
        elements.modeValue.textContent = modeLabels[currentMode];
    }

    function locateUserAndTrace(forceRefresh) {
        if (!navigator.geolocation) {
            setStatus('Ubicación no compatible', 'error', 'Tu navegador no soporta geolocalización en tiempo real.');
            return;
        }

        if (userPosition && !forceRefresh) {
            calculateRoute(userPosition.lat, userPosition.lng);
            return;
        }

        setLoadingState(true);
        setStatus('Buscando tu ubicación', 'loading', 'Estamos buscando tu ubicación actual para iniciar la ruta.');

        navigator.geolocation.getCurrentPosition((position) => {
            userPosition = {
                lat: position.coords.latitude,
                lng: position.coords.longitude,
            };

            renderUserMarker();
            calculateRoute(userPosition.lat, userPosition.lng);
        }, (error) => {
            setLoadingState(false);
            setStatus('No pudimos ubicarte', 'error', geolocationErrorMessage(error));
        }, {
            enableHighAccuracy: true,
            timeout: 10000,
            maximumAge: 30000,
        });
    }

    async function calculateRoute(originLat, originLng) {
        if (requestController) {
            requestController.abort();
        }

        requestController = new AbortController();
        setLoadingState(true);
        setStatus('Calculando ruta', 'loading', 'Calculando la mejor ruta para llegar a ' + routeData.restaurantName + '.');

        try {
            const profile = currentMode === 'driving' ? 'driving' : 'walking';
            const url = 'https://router.project-osrm.org/route/v1/' + profile + '/' + originLng + ',' + originLat + ';' + routeData.lng + ',' + routeData.lat + '?overview=full&geometries=geojson&steps=true&alternatives=false';
            const response = await fetch(url, { signal: requestController.signal });

            if (!response.ok) {
                throw new Error('route_request_failed');
            }

            const data = await response.json();
            const route = data.routes && data.routes[0] ? data.routes[0] : null;

            if (!route || !route.geometry || !Array.isArray(route.geometry.coordinates) || route.geometry.coordinates.length === 0) {
                throw new Error('route_not_found');
            }

            drawRoute(route.geometry.coordinates);
            renderRouteSummary(route.distance, route.duration);
            renderRouteSteps(route.legs || []);
            setStatus('Ruta lista', 'success', 'Tu ruta está lista. Revisa el mapa y sigue los pasos para llegar al restaurante.');
            elements.refreshButton.disabled = false;
        } catch (error) {
            if (error.name === 'AbortError') {
                return;
            }

            renderRouteSummary(null, null);
            renderRouteSteps([]);
            setStatus('Ruta no disponible', 'error', 'No se pudo calcular la ruta en este momento. Intenta nuevamente en unos segundos.');
        } finally {
            setLoadingState(false);
        }
    }

    function drawRoute(coordinates) {
        const latLngs = coordinates.map((point) => [point[1], point[0]]);

        if (routeLayer) {
            map.removeLayer(routeLayer);
        }

        routeLayer = L.polyline(latLngs, {
            color: routeData.brandPrimary,
            weight: 6,
            opacity: 0.9,
            lineCap: 'round',
            lineJoin: 'round',
        }).addTo(map);

        recenterMap();
    }

    function renderUserMarker() {
        if (!userPosition) {
            return;
        }

        if (userMarker) {
            map.removeLayer(userMarker);
        }

        userMarker = L.marker([userPosition.lat, userPosition.lng], { icon: userIcon }).addTo(map);
        userMarker.bindPopup('<strong>Tu ubicación</strong>');
    }

    function recenterMap() {
        const bounds = L.latLngBounds([[routeData.lat, routeData.lng]]);

        if (userPosition) {
            bounds.extend([userPosition.lat, userPosition.lng]);
        }

        if (routeLayer) {
            bounds.extend(routeLayer.getBounds());
        }

        map.fitBounds(bounds.pad(0.18), { animate: true });
    }

    function renderRouteSummary(distance, duration) {
        elements.distance.textContent = formatDistance(distance);
        elements.duration.textContent = formatDuration(duration);
        elements.modeValue.textContent = modeLabels[currentMode];
    }

    function renderRouteSteps(legs) {
        const steps = legs.flatMap((leg) => Array.isArray(leg.steps) ? leg.steps : []);
        elements.stepsList.innerHTML = '';

        if (!steps.length) {
            elements.stepsList.classList.add('hidden');
            elements.stepsEmpty.classList.remove('hidden');
            elements.stepsEmpty.textContent = 'No hay pasos disponibles para esta ruta todavía. Puedes intentar actualizarla.';
            elements.stepsCount.textContent = '0 pasos';
            return;
        }

        elements.stepsEmpty.classList.add('hidden');
        elements.stepsList.classList.remove('hidden');
        elements.stepsCount.textContent = steps.length + ' ' + (steps.length === 1 ? 'paso' : 'pasos');

        steps.forEach((step, index) => {
            const item = document.createElement('li');
            item.className = 'rounded-2xl border border-stone-200 bg-stone-50 px-4 py-3';

            const instruction = describeStep(step, index === steps.length - 1);
            const distanceLabel = formatDistance(step.distance);

            item.innerHTML = '' +
                '<div class="flex items-start gap-3">' +
                    '<div class="mt-0.5 flex h-8 w-8 shrink-0 items-center justify-center rounded-2xl bg-white text-[color:var(--brand-primary)] shadow-sm">' +
                        '<span class="material-symbols-outlined text-lg">' + stepIcon(step) + '</span>' +
                    '</div>' +
                    '<div class="min-w-0 flex-1">' +
                        '<p class="text-sm font-bold leading-6 text-on-surface">' + escapeHtml(instruction) + '</p>' +
                        '<p class="mt-1 text-xs font-semibold uppercase tracking-[0.2em] text-stone-500">Tramo aproximado: ' + escapeHtml(distanceLabel) + '</p>' +
                    '</div>' +
                '</div>';

            elements.stepsList.appendChild(item);
        });
    }

    function describeStep(step, isLastStep) {
        const maneuver = step.maneuver || {};
        const modifier = maneuver.modifier || '';
        const type = maneuver.type || '';
        const roadName = step.name ? ' por ' + step.name : '';

        if (isLastStep || type === 'arrive') {
            return 'Has llegado a ' + routeData.restaurantName + '.';
        }

        if (type === 'depart') {
            return 'Sal desde tu ubicación y continúa' + roadName + '.';
        }

        if (type === 'roundabout' || type === 'rotary' || type === 'roundabout turn') {
            return 'En la rotonda, toma la salida indicada y continúa' + roadName + '.';
        }

        if (type === 'merge') {
            return 'Incorpórate y sigue' + roadName + '.';
        }

        if (type === 'fork') {
            return 'Mantente en la bifurcación y continúa' + roadName + '.';
        }

        if (type === 'end of road') {
            return 'Al final de la vía, ' + lowerFirst(turnInstruction(modifier, roadName));
        }

        if (type === 'continue' || type === 'new name') {
            return 'Continúa recto' + roadName + '.';
        }

        if (type === 'turn' || type === 'on ramp' || type === 'off ramp' || type === 'use lane') {
            return turnInstruction(modifier, roadName);
        }

        return 'Sigue el camino indicado' + roadName + '.';
    }

    function turnInstruction(modifier, roadName = '') {
        const suffix = roadName ? roadName + '.' : '.';

        switch (modifier) {
            case 'left':
                return 'Gira a la izquierda' + suffix;
            case 'right':
                return 'Gira a la derecha' + suffix;
            case 'slight left':
                return 'Gira levemente a la izquierda' + suffix;
            case 'slight right':
                return 'Gira levemente a la derecha' + suffix;
            case 'sharp left':
                return 'Gira pronunciadamente a la izquierda' + suffix;
            case 'sharp right':
                return 'Gira pronunciadamente a la derecha' + suffix;
            case 'uturn':
                return 'Haz un giro en U' + suffix;
            default:
                return 'Continúa hacia adelante' + suffix;
        }
    }

    function lowerFirst(text) {
        return text.charAt(0).toLowerCase() + text.slice(1);
    }

    function stepIcon(step) {
        const maneuver = step.maneuver || {};
        const type = maneuver.type || '';
        const modifier = maneuver.modifier || '';

        if (type === 'arrive') {
            return 'flag';
        }

        if (type === 'roundabout' || type === 'rotary' || type === 'roundabout turn') {
            return 'sync';
        }

        if (modifier === 'left' || modifier === 'slight left' || modifier === 'sharp left') {
            return 'turn_left';
        }

        if (modifier === 'right' || modifier === 'slight right' || modifier === 'sharp right') {
            return 'turn_right';
        }

        if (modifier === 'uturn') {
            return 'u_turn_left';
        }

        return type === 'depart' ? 'my_location' : 'straight';
    }

    function formatDistance(distance) {
        if (distance === null || distance === undefined || Number.isNaN(distance)) {
            return '--';
        }

        if (distance < 1000) {
            return Math.round(distance) + ' m';
        }

        return (distance / 1000).toFixed(distance >= 10000 ? 0 : 1) + ' km';
    }

    function formatDuration(duration) {
        if (duration === null || duration === undefined || Number.isNaN(duration)) {
            return '--';
        }

        const minutes = Math.round(duration / 60);
        if (minutes < 60) {
            return minutes + ' min';
        }

        const hours = Math.floor(minutes / 60);
        const remainingMinutes = minutes % 60;
        return remainingMinutes === 0 ? hours + ' h' : hours + ' h ' + remainingMinutes + ' min';
    }

    function geolocationErrorMessage(error) {
        switch (error.code) {
            case 1:
                return 'No autorizaste el acceso a tu ubicación. Si cambias de idea, puedes intentarlo nuevamente.';
            case 2:
                return 'No pudimos determinar tu ubicación actual. Revisa tu GPS o tu conexión e inténtalo otra vez.';
            case 3:
                return 'La búsqueda de tu ubicación tardó demasiado. Intenta nuevamente en unos segundos.';
            default:
                return 'Ocurrió un problema al obtener tu ubicación actual. Vuelve a intentarlo.';
        }
    }

    function escapeHtml(value) {
        return String(value)
            .replaceAll('&', '&amp;')
            .replaceAll('<', '&lt;')
            .replaceAll('>', '&gt;')
            .replaceAll('"', '&quot;')
            .replaceAll("'", '&#39;');
    }
});
</script>
@endif

@auth('comensal')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('reviewModal');
    const closeBtn = document.getElementById('closeReviewModal');
    const stars = document.querySelectorAll('.review-star');
    const scoreInput = document.getElementById('reviewScore');
    const menuIdInput = document.getElementById('reviewMenuId');
    const comentarioInput = document.getElementById('reviewComentario');
    const modalTitle = document.getElementById('reviewModalTitle');
    const modalSubtitle = document.getElementById('reviewModalSubtitle');
    const restaurantName = @json($restaurante->nombre);

    function openReviewModal(menuId, score, comentario, hasReview, menuName) {
        menuIdInput.value = menuId || '';
        scoreInput.value = score || 5;

        stars.forEach((st, i) => {
            const val = Number(scoreInput.value);
            if (i < val) {
                st.classList.add('text-amber-400');
                st.classList.remove('text-stone-300');
            } else {
                st.classList.remove('text-amber-400');
                st.classList.add('text-stone-300');
            }
        });

        comentarioInput.value = comentario || '';

        if (menuId) {
            modalTitle.textContent = hasReview ? modal.dataset.titleDishEdit : modal.dataset.titleDishNew;
            modalSubtitle.textContent = 'Califica ' + (menuName || 'este plato') + ' en ' + restaurantName + '.';
        } else {
            modalTitle.textContent = hasReview ? modal.dataset.titleEdit : modal.dataset.titleNew;
            modalSubtitle.textContent = modal.dataset.subtitleRestaurant;
        }

        modal.classList.remove('hidden');
    }

    // Restaurant review buttons
    const leaveBtn = document.getElementById('leaveReviewBtn');
    const editBtn = document.getElementById('editReviewBtn');
    if (leaveBtn) {
        leaveBtn.addEventListener('click', () => openReviewModal('', 5, '', false, ''));
    }
    if (editBtn) {
        editBtn.addEventListener('click', () => {
            const score = Number(editBtn.dataset.score) || 5;
            const comentario = editBtn.dataset.comentario || '';
            openReviewModal('', score, comentario, true, '');
        });
    }

    // Dish review buttons
    document.querySelectorAll('.menu-review-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            const menuId = btn.dataset.menuId;
            const score = Number(btn.dataset.score) || 5;
            const comentario = btn.dataset.comentario || '';
            const hasReview = btn.dataset.hasReview === 'true';
            const menuName = btn.closest('[data-menu-name]')?.dataset.menuName || '';
            openReviewModal(menuId, score, comentario, hasReview, menuName);
        });
    });

    if (closeBtn && modal) {
        closeBtn.addEventListener('click', () => modal.classList.add('hidden'));
    }
    if (modal) {
        modal.addEventListener('click', (e) => {
            if (e.target === modal) modal.classList.add('hidden');
        });
    }

    stars.forEach(s => {
        s.addEventListener('click', () => {
            const val = Number(s.dataset.value);
            scoreInput.value = val;
            stars.forEach((st, i) => {
                if (i < val) {
                    st.classList.add('text-amber-400');
                    st.classList.remove('text-stone-300');
                } else {
                    st.classList.remove('text-amber-400');
                    st.classList.add('text-stone-300');
                }
            });
        });
        s.addEventListener('mouseenter', () => {
            const val = Number(s.dataset.value);
            stars.forEach((st, i) => {
                if (i < val) {
                    st.classList.add('text-amber-300');
                    st.classList.remove('text-stone-300');
                } else {
                    st.classList.remove('text-amber-300');
                }
            });
        });
        s.addEventListener('mouseleave', () => {
            stars.forEach(st => st.classList.remove('text-amber-300'));
        });
    });
});
</script>
@endauth

@endsection
