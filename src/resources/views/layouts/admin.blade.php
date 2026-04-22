<!DOCTYPE html>
<html class="light" lang="es">
<head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>Admin - Panel de Control</title>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<script id="tailwind-config">
tailwind.config = {
    darkMode: "class",
    theme: {
        extend: {
            "colors": {
                "primary-fixed": "#ffdad5",
                "on-tertiary-fixed-variant": "#7c2e00",
                "surface-container-highest": "#e8e1dc",
                "on-secondary-container": "#663100",
                "surface-bright": "#fff8f2",
                "surface-container-high": "#eee7e1",
                "on-primary": "#ffffff",
                "secondary": "#944a00",
                "primary-fixed-dim": "#ffb4a9",
                "secondary-fixed-dim": "#ffb783",
                "surface-tint": "#b02d21",
                "tertiary": "#8e3600",
                "on-error": "#ffffff",
                "tertiary-container": "#b54700",
                "on-tertiary": "#ffffff",
                "error": "#ba1a1a",
                "secondary-container": "#fc8f34",
                "surface-container": "#f3ede7",
                "on-tertiary-fixed": "#351000",
                "on-secondary-fixed-variant": "#713700",
                "on-primary-fixed": "#410000",
                "surface": "#fff8f2",
                "inverse-surface": "#33302c",
                "background": "#fff8f2",
                "surface-dim": "#dfd9d3",
                "outline": "#8d706c",
                "on-surface": "#1e1b18",
                "inverse-on-surface": "#f6f0ea",
                "primary": "#9e2016",
                "outline-variant": "#e1bfb9",
                "secondary-fixed": "#ffdcc5",
                "error-container": "#ffdad6",
                "on-error-container": "#93000a",
                "on-primary-fixed-variant": "#8e130c",
                "inverse-primary": "#ffb4a9",
                "tertiary-fixed-dim": "#ffb595",
                "tertiary-fixed": "#ffdbcd",
                "surface-container-lowest": "#ffffff",
                "on-surface-variant": "#59413d",
                "on-background": "#1e1b18",
                "on-secondary-fixed": "#301400",
                "surface-container-low": "#f9f2ec",
                "on-secondary": "#ffffff",
                "surface-variant": "#e8e1dc",
                "primary-container": "#c0392b",
                "on-primary-container": "#ffe5e1",
                "on-tertiary-container": "#ffe6dc"
            },
            "fontFamily": {
                "headline": ["Plus Jakarta Sans"],
                "body": ["Inter"],
                "label": ["Inter"]
            }
        },
    },
}
</script>
<style>
    .material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24; }
    body { font-family: 'Inter', sans-serif; }
    h1, h2, h3, .font-headline { font-family: 'Plus Jakarta Sans', sans-serif; }
</style>
</head>
<body class="bg-background text-on-background min-h-screen">
<!-- SideNavBar -->
<aside class="fixed left-0 top-0 h-screen w-64 bg-[#FFF8F2] dark:bg-stone-950 flex flex-col h-full py-8 space-y-2 z-50">
    <div class="px-8 mb-10">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-primary-container flex items-center justify-center text-white shadow-lg">
                <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">restaurant_menu</span>
            </div>
            <div>
                <h1 class="text-2xl font-black text-[#C0392B] tracking-tight leading-tight">Admin</h1>
                <p class="text-[10px] uppercase tracking-widest text-stone-500 font-bold">Consola de Gestión</p>
            </div>
        </div>
    </div>
    
    @php
        $route = Route::currentRouteName();
    @endphp

    <nav class="flex-1 space-y-1">
        <a class="{{ $route === 'admin.dashboard' ? 'bg-white text-[#C0392B] font-bold shadow-sm' : 'text-stone-500 hover:bg-white/50 hover:text-[#C0392B]' }} rounded-l-full ml-4 py-3 px-6 flex items-center gap-4 transition-all" href="{{ route('admin.dashboard') }}">
            <span class="material-symbols-outlined">dashboard</span>
            <span class="font-headline font-medium text-base">Panel de Control</span>
        </a>
        <a class="{{ $route === 'admin.restaurantes.index' ? 'bg-white text-[#C0392B] font-bold shadow-sm' : 'text-stone-500 hover:bg-white/50 hover:text-[#C0392B]' }} rounded-l-full ml-4 py-3 px-6 flex items-center gap-4 transition-all" href="{{ route('admin.restaurantes.index') }}">
            <span class="material-symbols-outlined">restaurant</span>
            <span class="font-headline font-medium text-base">Restaurantes</span>
        </a>
        <a class="{{ $route === 'admin.comensales.index' ? 'bg-white text-[#C0392B] font-bold shadow-sm' : 'text-stone-500 hover:bg-white/50 hover:text-[#C0392B]' }} rounded-l-full ml-4 py-3 px-6 flex items-center gap-4 transition-all" href="{{ route('admin.comensales.index') }}">
            <span class="material-symbols-outlined">group</span>
            <span class="font-headline font-medium text-base">Comensales</span>
        </a>
        <a class="{{ $route === 'admin.categorias.index' ? 'bg-white text-[#C0392B] font-bold shadow-sm' : 'text-stone-500 hover:bg-white/50 hover:text-[#C0392B]' }} rounded-l-full ml-4 py-3 px-6 flex items-center gap-4 transition-all" href="{{ route('admin.categorias.index') }}">
            <span class="material-symbols-outlined">category</span>
            <span class="font-headline font-medium text-base">Categorías</span>
        </a>
    </nav>

    <div class="px-6 mt-auto space-y-4">
        <a href="{{ route('admin.usuarios.create') }}" class="w-full bg-primary text-white py-4 rounded-xl font-bold flex items-center justify-center gap-2 shadow-lg shadow-primary/20 hover:scale-[0.98] transition-transform">
            <span class="material-symbols-outlined">add_circle</span>
            Añadir Usuario
        </a>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="w-full bg-primary text-on-primary py-4 rounded-xl font-bold shadow-lg flex items-center justify-center gap-2 hover:scale-[1.02] transition-transform">
                <span class="material-symbols-outlined">logout</span>
                Salir
            </button>
        </form>
    </div>
</aside>

<!-- TopNavBar -->
<header class="fixed top-0 right-0 left-64 bg-[#FFF8F2] dark:bg-stone-950 flex justify-between items-center px-8 py-4 z-40 shadow-sm dark:shadow-none">
    <div class="flex items-center gap-4 flex-1">
        <!-- Search omitted for now -->
    </div>
    <div class="flex items-center gap-6">
        <div class="flex items-center gap-3 pl-6 border-l border-stone-200">
            <div class="text-right">
                <p class="text-xs font-bold text-on-surface">{{ Auth::guard('usuario')->user()->nombre }}</p>
                <p class="text-[10px] text-stone-500">Super Administrador</p>
            </div>
            <div class="w-10 h-10 rounded-full bg-primary-fixed flex items-center justify-center text-primary font-bold shadow-sm">
                {{ substr(Auth::guard('usuario')->user()->nombre, 0, 1) }}
            </div>
        </div>
    </div>
</header>

<!-- Main Content -->
<main class="ml-64 pt-24 p-8 min-h-screen">
    <div class="max-w-7xl mx-auto">
        @if (session('success'))
            <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif
        @if ($errors->any())
            <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </div>
</main>
</body>
</html>
