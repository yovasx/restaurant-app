<!DOCTYPE html>
<html class="light" lang="es">
<head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>@yield('title', 'Panel de Control') - GastroGuía</title>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<script id="tailwind-config">
tailwind.config = {
    darkMode: "class",
    theme: {
        extend: {
            colors: {
                "primary": "#9e2016",
                "primary-container": "#c0392b",
                "on-primary": "#ffffff",
                "on-primary-container": "#ffe5e1",
                "primary-fixed": "#ffdad5",
                "primary-fixed-dim": "#ffb4a9",
                "on-primary-fixed": "#410000",
                "on-primary-fixed-variant": "#8e130c",
                "secondary": "#944a00",
                "secondary-container": "#fc8f34",
                "secondary-fixed": "#ffdcc5",
                "on-secondary": "#ffffff",
                "on-secondary-container": "#663100",
                "tertiary": "#8e3600",
                "tertiary-container": "#b54700",
                "on-tertiary": "#ffffff",
                "background": "#fff8f2",
                "surface": "#fff8f2",
                "surface-container-lowest": "#ffffff",
                "surface-container-low": "#f9f2ec",
                "surface-container": "#f3ede7",
                "surface-container-high": "#eee7e1",
                "surface-container-highest": "#e8e1dc",
                "on-surface": "#1e1b18",
                "on-surface-variant": "#59413d",
                "on-background": "#1e1b18",
                "outline": "#8d706c",
                "outline-variant": "#e1bfb9",
                "error": "#ba1a1a",
                "inverse-surface": "#33302c",
                "inverse-on-surface": "#f6f0ea",
                "inverse-primary": "#ffb4a9",
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
    body { font-family: 'Inter', sans-serif; background-color: #f9f2ec; }
    .font-headline { font-family: 'Plus Jakarta Sans', sans-serif; }
    .material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24; }
    .nav-active { @apply bg-[#C0392B] text-white shadow-sm; }
</style>
</head>
<body class="bg-surface-container-low text-on-surface min-h-screen flex overflow-hidden">

<!-- SideNavBar -->
<aside class="hidden lg:flex flex-col h-screen w-64 border-r border-stone-200 bg-[#FFF8F2] py-6 shrink-0 fixed left-0 top-0 z-40">
    <div class="px-6 mb-8">
        <h1 class="font-black text-[#C0392B] text-2xl tracking-tighter font-headline">GastroGuía</h1>
        @php
            $sideRestaurante = \App\Models\Restaurante::where('usuario_id', Auth::guard('usuario')->id())->first();
        @endphp
        <div class="mt-5 flex items-center gap-3">
            <div class="w-10 h-10 rounded-full overflow-hidden bg-primary-fixed flex items-center justify-center text-primary font-black text-lg shadow-sm shrink-0">
                @if($sideRestaurante && $sideRestaurante->foto_portada)
                    <img class="w-full h-full object-cover" src="{{ asset('storage/'.$sideRestaurante->foto_portada) }}" alt="Foto Restaurante">
                @else
                    {{ substr(Auth::guard('usuario')->user()->nombre ?? 'R', 0, 1) }}
                @endif
            </div>
            <div>
                <p class="font-headline font-bold text-sm text-on-surface leading-tight">{{ Auth::guard('usuario')->user()->nombre ?? 'Restaurante' }}</p>
                <p class="text-[10px] uppercase tracking-widest text-stone-500 font-semibold">Panel de Control</p>
            </div>
        </div>
    </div>

    @php $route = Route::currentRouteName(); @endphp
    
    <nav class="flex-1 space-y-1 px-2">
        <a href="{{ route('restaurante.dashboard') }}"
           class="{{ str_starts_with($route, 'restaurante.dashboard') ? 'bg-[#C0392B] text-white shadow-sm' : 'text-stone-500 hover:bg-stone-100 hover:text-[#C0392B]' }} flex items-center gap-3 px-4 py-3 rounded-lg mx-2 transition-all duration-200 hover:translate-x-1">
            <span class="material-symbols-outlined">dashboard</span>
            <span class="font-headline font-medium text-sm">Panel</span>
        </a>
        <a href="{{ route('productos.index') }}"
           class="{{ str_starts_with($route, 'productos') ? 'bg-[#C0392B] text-white shadow-sm' : 'text-stone-500 hover:bg-stone-100 hover:text-[#C0392B]' }} flex items-center gap-3 px-4 py-3 rounded-lg mx-2 transition-all duration-200 hover:translate-x-1">
            <span class="material-symbols-outlined">restaurant</span>
            <span class="font-headline font-medium text-sm">Menú</span>
        </a>
        <a href="{{ route('restaurante.promociones.index') }}"
           class="{{ str_starts_with($route, 'restaurante.promociones') ? 'bg-[#C0392B] text-white shadow-sm' : 'text-stone-500 hover:bg-stone-100 hover:text-[#C0392B]' }} flex items-center gap-3 px-4 py-3 rounded-lg mx-2 transition-all duration-200 hover:translate-x-1">
            <span class="material-symbols-outlined">local_offer</span>
            <span class="font-headline font-medium text-sm">Promociones</span>
        </a>
        <a href="{{ route('restaurante.resenas') }}"
           class="{{ $route === 'restaurante.resenas' ? 'bg-[#C0392B] text-white shadow-sm' : 'text-stone-500 hover:bg-stone-100 hover:text-[#C0392B]' }} flex items-center gap-3 px-4 py-3 rounded-lg mx-2 transition-all duration-200 hover:translate-x-1">
            <span class="material-symbols-outlined">star</span>
            <span class="font-headline font-medium text-sm">Reseñas</span>
        </a>
        <a href="{{ route('restaurante.configuracion') }}"
           class="{{ str_starts_with($route, 'restaurante.configuracion') ? 'bg-[#C0392B] text-white shadow-sm' : 'text-stone-500 hover:bg-stone-100 hover:text-[#C0392B]' }} flex items-center gap-3 px-4 py-3 rounded-lg mx-2 transition-all duration-200 hover:translate-x-1">
            <span class="material-symbols-outlined">settings</span>
            <span class="font-headline font-medium text-sm">Ajustes</span>
        </a>
    </nav>

    <div class="px-4 mt-auto space-y-3">
        <a href="{{ route('productos.create') }}" class="w-full flex items-center justify-center gap-2 bg-primary text-white py-3 rounded-lg font-headline font-bold text-sm shadow-md active:scale-95 transition-transform hover:bg-[#c0392b]">
            <span class="material-symbols-outlined text-sm">add</span>
            Nuevo Plato
        </a>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="w-full flex items-center justify-center gap-2 py-3 rounded-lg font-headline font-medium text-sm text-stone-500 hover:bg-stone-100 hover:text-red-600 transition-all">
                <span class="material-symbols-outlined text-sm">logout</span>
                Cerrar Sesión
            </button>
        </form>
    </div>
</aside>

<!-- Main Content -->
<main class="flex-1 lg:ml-64 flex flex-col h-screen overflow-y-auto">
    <!-- Top Header -->
    <header class="sticky top-0 z-30 bg-[#FFF8F2] w-full border-b border-stone-200/80 shadow-sm">
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
        @if(session('success'))
            <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-xl font-bold">
                {{ session('success') }}
            </div>
        @endif
        @if($errors->any())
            <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-xl">
                <ul class="list-disc pl-5">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </div>
</main>

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
