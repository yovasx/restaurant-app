<!DOCTYPE html>
<html class="light" lang="es">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Admin - Panel de Control</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24; }
        body { font-family: 'Inter', sans-serif; }
        h1, h2, h3, .font-headline { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-background text-on-background min-h-screen overflow-hidden">
    @php
        $route = Route::currentRouteName();
    @endphp
    <div class="flex min-h-screen w-full">
        <aside class="hidden lg:flex sticky top-0 h-screen w-72 shrink-0 flex-col border-r border-stone-200 bg-[#FFF8F2] py-6 shadow-[inset_-1px_0_0_rgba(231,218,208,0.9)]">
            <div class="px-4 pb-8">
                <div class="flex items-center gap-3">
                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-primary-container text-white shadow-lg shadow-primary/20">
                        <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">restaurant_menu</span>
                    </div>
                    <div class="min-w-0">
                        <h1 class="font-headline text-2xl font-black leading-tight tracking-tight text-[#C0392B]">Admin</h1>
                        <p class="text-[10px] font-bold uppercase tracking-[0.3em] text-stone-500">Consola de Gestión</p>
                    </div>
                </div>

                <div class="mt-5 flex items-center gap-3">
                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-primary-fixed font-bold text-primary shadow-sm">
                        {{ substr(Auth::guard('usuario')->user()->nombre, 0, 1) }}
                    </div>
                    <div class="min-w-0">
                        <p class="truncate font-headline text-sm font-bold text-on-surface">{{ Auth::guard('usuario')->user()->nombre }}</p>
                        <p class="text-[10px] font-semibold uppercase tracking-[0.3em] text-stone-500">Super Admin</p>
                    </div>
                </div>
            </div>

            <nav class="flex-1 space-y-1 px-3">
                <a class="{{ request()->routeIs('admin.dashboard') ? 'bg-white text-[#C0392B] font-bold shadow-sm' : 'text-stone-500 hover:bg-white/80 hover:text-[#C0392B]' }} flex items-center gap-3 rounded-2xl px-4 py-3 transition-all" href="{{ route('admin.dashboard') }}">
                    <span class="material-symbols-outlined shrink-0">dashboard</span>
                    <span class="font-headline text-sm font-medium">Panel de Control</span>
                </a>
                <a class="{{ request()->routeIs('admin.restaurantes.*') ? 'bg-white text-[#C0392B] font-bold shadow-sm' : 'text-stone-500 hover:bg-white/80 hover:text-[#C0392B]' }} flex items-center gap-3 rounded-2xl px-4 py-3 transition-all" href="{{ route('admin.restaurantes.index') }}">
                    <span class="material-symbols-outlined shrink-0">restaurant</span>
                    <span class="font-headline text-sm font-medium">Restaurantes</span>
                </a>
                <a class="{{ request()->routeIs('admin.comensales.*') ? 'bg-white text-[#C0392B] font-bold shadow-sm' : 'text-stone-500 hover:bg-white/80 hover:text-[#C0392B]' }} flex items-center gap-3 rounded-2xl px-4 py-3 transition-all" href="{{ route('admin.comensales.index') }}">
                    <span class="material-symbols-outlined shrink-0">group</span>
                    <span class="font-headline text-sm font-medium">Comensales</span>
                </a>
                <a class="{{ request()->routeIs('admin.categorias.*') ? 'bg-white text-[#C0392B] font-bold shadow-sm' : 'text-stone-500 hover:bg-white/80 hover:text-[#C0392B]' }} flex items-center gap-3 rounded-2xl px-4 py-3 transition-all" href="{{ route('admin.categorias.index') }}">
                    <span class="material-symbols-outlined shrink-0">category</span>
                    <span class="font-headline text-sm font-medium">Categorías</span>
                </a>
            </nav>

            <div class="mt-auto space-y-3 px-3">
                <button type="button" data-modal-open="admin-user-create-modal" class="flex w-full items-center gap-3 rounded-2xl bg-primary px-4 py-3.5 font-headline text-sm font-bold text-white shadow-lg shadow-primary/20 transition hover:bg-primary-container">
                    <span class="material-symbols-outlined shrink-0">add_circle</span>
                    <span>Añadir Usuario</span>
                </button>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="flex w-full items-center gap-3 rounded-2xl px-4 py-3.5 font-headline text-sm font-medium text-stone-500 transition-all hover:bg-stone-100 hover:text-red-600">
                        <span class="material-symbols-outlined shrink-0">logout</span>
                        <span>Cerrar Sesión</span>
                    </button>
                </form>
            </div>
        </aside>

        <main class="flex-1 min-w-0 flex h-screen flex-col overflow-y-auto">
            <header class="sticky top-0 z-30 w-full border-b border-stone-200/80 bg-[#FFF8F2]/95 shadow-sm backdrop-blur">
                <div class="flex items-center justify-between px-6 py-4">
                    <div class="flex min-w-0 items-center gap-4">
                        <div class="lg:hidden">
                            <span class="font-headline text-lg font-bold tracking-tight text-[#C0392B]">Admin</span>
                        </div>
                        <div class="min-w-0">
                            <p class="text-[10px] font-bold uppercase tracking-[0.3em] text-stone-500">Administrador</p>
                            <h2 class="truncate font-headline text-xl font-extrabold text-on-surface sm:text-2xl">Panel de gestión</h2>
                        </div>
                    </div>

                    <div class="flex items-center gap-3 border-l border-stone-200 pl-5">
                        <div class="text-right">
                            <p class="text-xs font-bold text-on-surface">{{ Auth::guard('usuario')->user()->nombre }}</p>
                            <p class="text-[10px] text-stone-500">Super Administrador</p>
                        </div>
                        <div class="flex h-11 w-11 items-center justify-center rounded-full bg-primary-fixed font-bold text-primary shadow-sm">
                            {{ substr(Auth::guard('usuario')->user()->nombre, 0, 1) }}
                        </div>
                    </div>
                </div>
            </header>

            <div class="flex-1 w-full max-w-7xl mx-auto p-6">
                @yield('content')
            </div>
        </main>
    </div>

    <x-modal
        id="admin-user-create-modal"
        title="Crear nuevo usuario"
        subtitle="Registra administradores, restaurantes o comensales sin salir de la vista actual."
        max-width="max-w-3xl"
        :auto-open="old('_modal') === 'admin-user-create-modal'"
    >
        @include('admin.usuarios._modal-form', [
            'redirectTo' => request()->fullUrl(),
            'modalId' => 'admin-user-create-modal',
            'fixedRole' => null,
        ])
    </x-modal>

    @include('partials.screen-toast')
</body>
</html>
