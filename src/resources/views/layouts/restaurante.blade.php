<!DOCTYPE html>
<html class="light" lang="es">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>@yield('title', 'Panel de Control') - GastroGuía</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f9f2ec; }
        .font-headline { font-family: 'Plus Jakarta Sans', sans-serif; }
        .material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24; }
    </style>
</head>
<body class="bg-surface-container-low text-on-surface min-h-screen overflow-hidden">

@php
    $route = Route::currentRouteName();
    $sideRestaurante = \App\Models\Restaurante::where('usuario_id', Auth::guard('restaurante')->id())->first();
    $sidebarCategorias = \App\Models\Categoria::where('estado', 'activo')->orderBy('nombre_categoria')->get();
@endphp

<div class="flex min-h-screen w-full">
    <!-- SideNavBar -->
    <aside class="hidden lg:flex sticky top-0 h-screen w-72 shrink-0 flex-col border-r border-stone-200 bg-[#FFF8F2] py-6">
        <div class="px-4 pb-8">
            <div class="flex items-center gap-3">
                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-primary-container text-white shadow-lg shadow-primary/20">
                    <span class="material-symbols-outlined">restaurant_menu</span>
                </div>
                <div class="min-w-0">
                    <h1 class="font-black text-[#C0392B] text-2xl tracking-tighter font-headline">GastroGuía</h1>
                    <p class="text-[10px] uppercase tracking-[0.3em] text-stone-500 font-semibold">Panel de Control</p>
                </div>
            </div>

            <div class="mt-5 flex items-center gap-3">
                <div class="w-12 h-12 rounded-full overflow-hidden bg-primary-fixed flex items-center justify-center text-primary font-black text-lg shadow-sm shrink-0">
                    @if($sideRestaurante && $sideRestaurante->foto_portada)
                        <img class="w-full h-full object-cover" src="{{ asset('storage/'.$sideRestaurante->foto_portada) }}" alt="Foto Restaurante">
                    @else
                        {{ substr(Auth::guard('restaurante')->user()->nombre ?? 'R', 0, 1) }}
                    @endif
                </div>
                <div class="min-w-0">
                    <p class="font-headline font-bold text-sm text-on-surface leading-tight truncate">{{ Auth::guard('restaurante')->user()->nombre ?? 'Restaurante' }}</p>
                    <p class="text-[10px] uppercase tracking-[0.3em] text-stone-500 font-semibold">Panel de Control</p>
                </div>
            </div>
        </div>

        <nav class="flex-1 space-y-1 px-3">
            <a href="{{ route('restaurante.dashboard') }}"
               class="{{ str_starts_with($route, 'restaurante.dashboard') ? 'bg-[#C0392B] text-white shadow-sm' : 'text-stone-500 hover:bg-stone-100 hover:text-[#C0392B]' }} flex items-center gap-3 rounded-2xl px-4 py-3 transition-all">
                <span class="material-symbols-outlined shrink-0">dashboard</span>
                <span class="font-headline font-medium text-sm">Panel</span>
            </a>
            <a href="{{ route('productos.index') }}"
               class="{{ str_starts_with($route, 'productos') ? 'bg-[#C0392B] text-white shadow-sm' : 'text-stone-500 hover:bg-stone-100 hover:text-[#C0392B]' }} flex items-center gap-3 rounded-2xl px-4 py-3 transition-all">
                <span class="material-symbols-outlined shrink-0">restaurant</span>
                <span class="font-headline font-medium text-sm">Menú</span>
            </a>
            <a href="{{ route('restaurante.promociones.index') }}"
               class="{{ str_starts_with($route, 'restaurante.promociones') ? 'bg-[#C0392B] text-white shadow-sm' : 'text-stone-500 hover:bg-stone-100 hover:text-[#C0392B]' }} flex items-center gap-3 rounded-2xl px-4 py-3 transition-all">
                <span class="material-symbols-outlined shrink-0">local_offer</span>
                <span class="font-headline font-medium text-sm">Promociones</span>
            </a>
            <a href="{{ route('restaurante.resenas') }}"
               class="{{ $route === 'restaurante.resenas' ? 'bg-[#C0392B] text-white shadow-sm' : 'text-stone-500 hover:bg-stone-100 hover:text-[#C0392B]' }} flex items-center gap-3 rounded-2xl px-4 py-3 transition-all">
                <span class="material-symbols-outlined shrink-0">star</span>
                <span class="font-headline font-medium text-sm">Reseñas</span>
            </a>
            <a href="{{ route('restaurante.configuracion') }}"
               class="{{ str_starts_with($route, 'restaurante.configuracion') ? 'bg-[#C0392B] text-white shadow-sm' : 'text-stone-500 hover:bg-stone-100 hover:text-[#C0392B]' }} flex items-center gap-3 rounded-2xl px-4 py-3 transition-all">
                <span class="material-symbols-outlined shrink-0">settings</span>
                <span class="font-headline font-medium text-sm">Ajustes</span>
            </a>
        </nav>

        <div class="px-3 mt-auto space-y-3">
            <button type="button" data-modal-open="restaurante-producto-create-modal" class="w-full flex items-center gap-3 bg-primary text-white py-3 px-4 rounded-2xl font-headline font-bold text-sm shadow-md transition hover:bg-[#c0392b]">
                <span class="material-symbols-outlined text-sm shrink-0">add</span>
                <span>Nuevo Plato</span>
            </button>
            <form method="POST" action="{{ route('logout.restaurante') }}">
                @csrf
                <button class="w-full flex items-center gap-3 py-3 px-4 rounded-2xl font-headline font-medium text-sm text-stone-500 hover:bg-stone-100 hover:text-red-600 transition-all">
                    <span class="material-symbols-outlined text-sm shrink-0">logout</span>
                    <span>Cerrar Sesión</span>
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 min-w-0 flex flex-col h-screen overflow-y-auto pb-20 lg:pb-0">
        <!-- Top Header -->
        <header class="sticky top-0 z-30 bg-[#FFF8F2]/95 w-full border-b border-stone-200/80 shadow-sm backdrop-blur">
            <div class="flex justify-between items-center w-full px-6 py-4">
                <div class="flex items-center gap-4 lg:hidden">
                    <span class="font-headline font-bold text-lg tracking-tight text-[#C0392B]">GastroGuía</span>
                </div>
                <div class="hidden md:flex items-center gap-2">
                    <h2 class="font-headline font-bold text-xl text-on-surface">@yield('page-title', 'Panel de Control')</h2>
                </div>
                <div class="flex items-center gap-4">
                    <div class="hidden sm:flex items-center bg-surface-container-highest px-3 py-1.5 rounded-full">
                        <span class="material-symbols-outlined text-stone-500 text-xl">search</span>
                        <input class="bg-transparent border-none focus:ring-0 text-sm w-40 font-body outline-none" placeholder="Buscar platos..." type="text"/>
                    </div>
                    <a href="{{ route('restaurante.configuracion') }}" class="p-2 text-stone-600 hover:bg-stone-100 rounded-full transition-colors">
                        <span class="material-symbols-outlined">account_circle</span>
                    </a>
                </div>
            </div>
        </header>

        <div class="flex-1 p-6 max-w-7xl mx-auto w-full">
            @yield('content')
        </div>
    </main>
</div>

<x-modal
    id="restaurante-producto-create-modal"
    title="Añadir nuevo plato"
    subtitle="Crea un plato sin salir del panel actual."
    max-width="max-w-3xl"
    :auto-open="old('_modal') === 'restaurante-producto-create-modal'"
>
    @include('productos._modal-form', [
        'action' => route('productos.store'),
        'producto' => null,
        'categorias' => $sidebarCategorias,
        'submitLabel' => 'Guardar plato',
        'redirectTo' => request()->fullUrl(),
        'modalId' => 'restaurante-producto-create-modal',
        'formKey' => 'restaurante-producto-create',
    ])
</x-modal>

@include('partials.screen-toast')

<!-- Mobile Bottom Nav -->
<nav class="lg:hidden fixed bottom-0 left-0 w-full z-50 flex justify-around items-center px-4 pb-safe pt-3 bg-white/80 backdrop-blur-md shadow-[0_-4px_20px_rgba(0,0,0,0.05)] rounded-t-2xl">
    <a href="{{ route('restaurante.dashboard') }}" class="flex flex-col items-center text-{{ str_starts_with(Route::currentRouteName(), 'restaurante.dashboard') ? '[#C0392B]' : 'stone-400' }}">
        <span class="material-symbols-outlined">dashboard</span>
        <span class="text-[10px] font-semibold uppercase tracking-widest mt-1">Panel</span>
    </a>
    <a href="{{ route('productos.index') }}" class="flex flex-col items-center text-{{ str_starts_with(Route::currentRouteName(), 'productos') ? '[#C0392B]' : 'stone-400' }}">
        <span class="material-symbols-outlined">restaurant</span>
        <span class="text-[10px] font-semibold uppercase tracking-widest mt-1">Menú</span>
    </a>
    <a href="{{ route('restaurante.configuracion') }}" class="flex flex-col items-center text-{{ str_starts_with(Route::currentRouteName(), 'restaurante.') ? '[#C0392B]' : 'stone-400' }}">
        <span class="material-symbols-outlined">settings</span>
        <span class="text-[10px] font-semibold uppercase tracking-widest mt-1">Ajustes</span>
    </a>
</nav>

</body>
</html>
