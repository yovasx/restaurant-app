<!DOCTYPE html>
<html class="light" lang="es">
<head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>@yield('title', 'GastroGuía | Panel de Control')</title>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
<script id="tailwind-config">
      tailwind.config = {
        darkMode: "class",
        theme: {
          extend: {
            colors: {
              "secondary-fixed": "#ffdcc5",
              "on-error-container": "#93000a",
              "on-tertiary-fixed": "#351000",
              "tertiary-fixed": "#ffdbcd",
              "on-surface": "#1e1b18",
              "surface-bright": "#fff8f2",
              "primary-fixed": "#ffdad5",
              "primary": "#9e2016",
              "on-secondary-fixed-variant": "#713700",
              "inverse-surface": "#33302c",
              "on-primary-fixed": "#410000",
              "secondary-container": "#fc8f34",
              "tertiary-fixed-dim": "#ffb595",
              "surface-container-high": "#eee7e1",
              "tertiary": "#8e3600",
              "secondary-fixed-dim": "#ffb783",
              "primary-container": "#c0392b",
              "on-primary-container": "#ffe5e1",
              "background": "#fff8f2",
              "on-error": "#ffffff",
              "surface-dim": "#dfd9d3",
              "on-tertiary-container": "#ffe6dc",
              "on-secondary": "#ffffff",
              "on-background": "#1e1b18",
              "surface-tint": "#b02d21",
              "on-tertiary": "#ffffff",
              "inverse-on-surface": "#f6f0ea",
              "surface-container-highest": "#e8e1dc",
              "surface-container-lowest": "#ffffff",
              "error-container": "#ffdad6",
              "surface-variant": "#e8e1dc",
              "on-secondary-fixed": "#301400",
              "on-secondary-container": "#663100",
              "surface": "#fff8f2",
              "error": "#ba1a1a",
              "outline-variant": "#e1bfb9",
              "outline": "#8d706c",
              "secondary": "#944a00",
              "surface-container-low": "#f9f2ec",
              "on-tertiary-fixed-variant": "#7c2e00",
              "on-surface-variant": "#59413d",
              "inverse-primary": "#ffb4a9",
              "primary-fixed-dim": "#ffb4a9",
              "on-primary": "#ffffff",
              "tertiary-container": "#b54700",
              "surface-container": "#f3ede7",
              "on-primary-fixed-variant": "#8e130c"
            },
            fontFamily: {
              "headline": ["Plus Jakarta Sans"],
              "body": ["Inter"],
              "label": ["Inter"]
            },
          },
        },
      }
</script>
<style>
    body { font-family: 'Inter', sans-serif; background-color: #FFF8F2; }
    .font-headline { font-family: 'Plus Jakarta Sans', sans-serif; }
    .material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24; }
    .hide-scrollbar::-webkit-scrollbar { display: none; }
</style>
</head>
<body class="bg-background text-on-background min-h-screen flex overflow-hidden">

<!-- SideNavBar Component -->
@php
    $showRestaurantLayout = auth()->guard('usuario')->check() && (
        request()->is('restaurante*') || request()->is('productos*') || request()->routeIs('restaurante.*') || request()->routeIs('productos.*') || request()->routeIs('admin.*')
    );
@endphp

@if($showRestaurantLayout)
<aside class="hidden lg:flex flex-col h-screen w-64 border-r border-stone-200 dark:border-stone-800 bg-[#FFF8F2] dark:bg-stone-900 py-6 shrink-0">
    <div class="px-6 mb-8">
        <h1 class="font-black text-[#C0392B] text-2xl tracking-tighter">GastroGuía</h1>
        <div class="mt-6 flex items-center gap-3">
            <div class="w-10 h-10 rounded-full bg-primary-fixed flex items-center justify-center overflow-hidden">
                <img class="w-full h-full object-cover" src="https://lh3.googleusercontent.com/aida-public/AB6AXuC96_CLZtpGcoIz48YRebD_nXDvOYBJ6kodh2Huw1W9_DHlecaIZvAR_GLr8nnookFNCZIdIANCURiOoNIsF9Z7W2z1mhp0GUWn6DS0BEEynv3Dla1qC6HbFwGlo8trPx7_HvM4Me1anN6ev9X19WFFCJzBFZgynnTDeNVmKufj_LQMqTNNYhY8z0pvwGSYEbtfuICZaoQqZePtAPbptPJXUtwBkUb9IjclwgdMao0wcaG5funKApJ2sYCDQpkHzb1GHUHcrWd2rK0T"/>
            </div>
            <div>
                <p class="font-headline font-bold text-sm text-on-surface">Mi Restaurante</p>
                <p class="text-[10px] uppercase tracking-widest text-stone-500 font-semibold">Panel de Control</p>
            </div>
        </div>
    </div>
    
    <nav class="flex-1 space-y-1 px-2">
        <a class="flex items-center gap-3 px-4 py-3 text-stone-600 dark:text-stone-400 hover:bg-stone-100 dark:hover:bg-stone-800 rounded-lg mx-2 transition-transform duration-200 hover:translate-x-1 cursor-pointer" href="{{ route('productos.index') }}">
            <span class="material-symbols-outlined" data-icon="restaurant" style="font-variation-settings: 'FILL' 1;">restaurant</span>
            <span class="font-headline font-medium text-sm">Menú</span>
        </a>
    </nav>
    <div class="px-4 mt-auto">
        <a href="{{ route('productos.create') }}" class="w-full flex items-center justify-center gap-2 bg-primary text-white py-3 rounded-lg font-headline font-bold text-sm shadow-md active:scale-95 transition-transform">
            <span class="material-symbols-outlined text-sm" data-icon="add">add</span>
            Nuevo Plato
        </a>
        
        @auth
        <form method="POST" action="{{ route('logout') }}" class="mt-4">
            @csrf
            <button class="w-full flex items-center gap-3 px-4 py-3 hover:bg-stone-100 rounded-lg font-headline font-bold text-sm text-gray-700 transition-colors">
                <span class="material-symbols-outlined text-sm" data-icon="logout">logout</span>
                Cerrar Sesión
            </button>
        </form>
        @endauth
    </div>
</aside>
@endif

<!-- Main Content Area -->
<main class="flex-1 flex flex-col h-screen overflow-y-auto bg-surface-container-low">
    <!-- TopAppBar Component -->
    @if($showRestaurantLayout)
    <header class="sticky top-0 z-50 bg-[#FFF8F2] dark:bg-stone-950 w-full shadow-sm">
        <div class="flex justify-between items-center w-full px-6 py-4 max-w-7xl mx-auto">
            <div class="flex items-center gap-4 lg:hidden">
                <span class="material-symbols-outlined text-primary text-2xl" data-icon="menu">menu</span>
                <span class="font-['Plus_Jakarta_Sans'] font-bold text-lg tracking-tight text-[#C0392B]">GastroGuía</span>
            </div>
            <div class="hidden md:flex items-center gap-8">
                <h2 class="font-headline font-bold text-xl text-on-surface">Gestión del Sistema</h2>
            </div>
            
            <div class="flex items-center gap-4">
                <div class="hidden sm:flex items-center bg-surface-container-highest px-3 py-1.5 rounded-full border-none">
                    <span class="material-symbols-outlined text-stone-500 text-xl" data-icon="search">search</span>
                    <input class="bg-transparent border-none focus:ring-0 text-sm w-48 font-body" placeholder="Buscar..." type="text"/>
                </div>
                <button class="p-2 text-stone-600 dark:text-stone-400 hover:bg-stone-100 rounded-full transition-colors">
                    <span class="material-symbols-outlined" data-icon="account_circle">account_circle</span>
                </button>
            </div>
        </div>
    </header>
    @endif

    <!-- Dashboard Canvas -->
    <div class="p-6 max-w-7xl mx-auto w-full space-y-8">
        @yield('content')
    </div>
</main>
</body>
</html>
