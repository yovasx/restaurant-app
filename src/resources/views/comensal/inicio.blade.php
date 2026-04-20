<!DOCTYPE html>
<html class="light" lang="es">
<head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>GastroGuía - Descubre los mejores sabores</title>
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
    .material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24; display: inline-block; line-height: 1; vertical-align: middle; }
    .hide-scrollbar::-webkit-scrollbar { display: none; }
    .hide-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    .hero-gradient { background: linear-gradient(180deg, rgba(30,27,24,0) 0%, rgba(30,27,24,0.7) 100%); }
</style>
</head>
<body class="bg-background text-on-surface font-body selection:bg-primary/20">

<header class="bg-[#FFF8F2] dark:bg-stone-950 docked full-width top-0 sticky z-50 no-border">
    <div class="flex justify-between items-center w-full px-6 py-4 max-w-7xl mx-auto">
        <div class="text-2xl font-black text-[#C0392B] dark:text-[#E74C3C] tracking-tighter">
            GastroGuía
        </div>
        <nav class="hidden md:flex items-center space-x-8 font-['Plus_Jakarta_Sans'] font-bold text-lg tracking-tight">
            <a class="text-[#C0392B] border-b-2 border-[#C0392B] pb-1" href="#">Inicio</a>
            <a class="text-stone-600 hover:text-[#C0392B] transition-colors" href="#">Explorar</a>
            <a class="text-stone-600 hover:text-[#C0392B] transition-colors" href="#">Reservas</a>
        </nav>
        
        <div class="flex items-center space-x-4">
            <button class="p-2 rounded-full hover:bg-stone-100 dark:hover:bg-stone-800 transition-colors duration-300 scale-95 active:scale-90 transition-transform hidden md:block text-stone-600 font-bold text-sm">
                Hola, {{ Auth::guard('comensal')->user()->nombre ?? 'Comensal' }}
            </button>

            @if(Auth::guard('comensal')->check())
            <form method="POST" action="{{ route('logout') }}" class="inline">
                @csrf
                <button type="submit" class="p-2 rounded-full hover:bg-stone-100 transition-colors flex items-center gap-1 group text-stone-600 font-bold" title="Cerrar sesión">
                    <span class="material-symbols-outlined text-stone-600 group-hover:text-red-500">logout</span>
                    <span class="hidden md:block group-hover:text-red-500">Salir</span>
                </button>
            </form>
            @endif
        </div>
    </div>
</header>

<main class="pb-24">
    <section class="relative h-[614px] min-h-[500px] w-full overflow-hidden">
        <div class="absolute inset-0">
            <img class="w-full h-full object-cover" src="https://lh3.googleusercontent.com/aida-public/AB6AXuCg_iF3OdIur_RT5qkIAME3zJ5OerrBm_bSAQDK5x2DUdeZg3aKSFrDn7qt45JFUEO_lb-DnYBwqlnsornV6euEfh3oQNyERw_9g1FpFnvM88ubBKU-h3wvtdbEX0u17XcUuAtmJ74Thyv6U1cPQuISwcMmZLN1C8Z_TcPHOdF1h5UZjfbYGjlNaS8hJv_avRxYXRMHDGXdYzAJzx7G-V4kRN5fZtN85K_ypjGi3aokV2fshu5f2KirKRjGbED4YZP9MH6bApoIeo_7"/>
            <div class="absolute inset-0 hero-gradient"></div>
        </div>
        <div class="relative h-full flex flex-col justify-center items-center text-center px-4 max-w-5xl mx-auto space-y-8">
            <h1 class="font-headline text-5xl md:text-7xl font-extrabold text-white tracking-tight leading-tight">
                Experiencias que <span class="text-secondary-fixed">deleitan</span>
            </h1>
            <div class="w-full max-w-2xl bg-surface-container-lowest rounded-full p-2 shadow-xl flex items-center gap-2">
                <div class="flex items-center pl-4 pr-2 text-stone-400">
                    <span class="material-symbols-outlined">location_on</span>
                    <input class="bg-transparent border-none focus:ring-0 text-on-surface w-32 outline-none border-r border-outline-variant" placeholder="La Paz, Bolivia" type="text"/>
                </div>
                <div class="flex-1 flex items-center px-2">
                    <span class="material-symbols-outlined text-stone-400 mr-2">search</span>
                    <input class="bg-transparent border-none focus:ring-0 outline-none text-on-surface w-full" placeholder="Busca salteñas, pique macho..." type="text"/>
                </div>
                <button class="bg-primary text-white px-8 py-3 rounded-full font-bold hover:bg-primary-container transition-all">Buscar</button>
            </div>
        </div>
    </section>

    <!-- Promotional Banner Strip -->
    <section class="max-w-7xl mx-auto px-6 mt-12">
        <div class="bg-secondary-container rounded-2xl p-8 flex flex-col md:flex-row items-center justify-between gap-6 overflow-hidden relative">
            <div class="relative z-10 text-on-secondary-container max-w-lg">
                <span class="inline-block px-3 py-1 bg-on-secondary-container text-secondary-container rounded-full text-xs font-bold tracking-widest uppercase mb-4">Oferta de la Semana</span>
                <h2 class="text-3xl font-headline font-extrabold mb-2">2x1 en Cenas de Autor</h2>
                <p class="opacity-90 font-medium">Reserva tu mesa para este jueves y disfruta de un menú degustación exclusivo para dos al precio de uno.</p>
            </div>
            <div class="relative z-10">
                <button class="bg-on-secondary-container text-white px-8 py-4 rounded-xl font-bold shadow-2xl hover:scale-105 transition-transform">Ver Restaurantes</button>
            </div>
        </div>
    </section>

    <!-- Near You Section -->
    <section class="max-w-7xl mx-auto px-6 mt-16">
        <div class="flex justify-between items-end mb-8">
            <div>
                <h3 class="text-sm font-label uppercase tracking-[0.2em] text-secondary font-bold mb-2">Descubrimientos Locales</h3>
                <h2 class="text-4xl font-headline font-extrabold text-on-surface tracking-tight">Restaurantes Cercanos</h2>
            </div>
            <button class="text-primary font-bold flex items-center gap-1 hover:underline">Ver todo <span class="material-symbols-outlined text-sm">arrow_forward</span></button>
        </div>
        <div class="flex gap-6 overflow-x-auto hide-scrollbar pb-8 -mx-6 px-6">
            <!-- Card 1 -->
            <div class="min-w-[320px] md:min-w-[400px] bg-surface-container-lowest rounded-xl shadow-sm hover:shadow-xl transition-all duration-300 group">
                <div class="relative h-64 w-full overflow-hidden rounded-t-xl">
                    <img class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700" src="https://lh3.googleusercontent.com/aida-public/AB6AXuBSXgQG9jTV3hNb7o-c7vZD3ivJBr4rmwD7ZiivNfUg0oAv_vnWYvWxp7x2delqx-okTDDg1kvidFz1ZjXGA8R3pbVLl1oDqSkIezE4WZOBR--U4y1NTZad1QiRZJH74J28qXtDm-kGJb7C1mVnSc1qj0YTK3zDh4VHay6DRObnnnV9KM-gN7yI8NuynfPNtn67Sg46ajTCy0oK6SEKPE7ei9yimH2iLi0-zriNVbzno0XEZpfdpK_wX1Jfd1TpRR3dO_ewW6kInjg6"/>
                    <div class="absolute top-4 right-4 backdrop-blur-md bg-white/80 px-3 py-1 rounded-full flex items-center gap-1">
                        <span class="material-symbols-outlined text-yellow-500 text-sm" style="font-variation-settings: 'FILL' 1;">star</span>
                        <span class="font-bold text-sm">4.9</span>
                    </div>
                </div>
                <div class="p-6">
                    <div class="flex justify-between items-start mb-2">
                        <h4 class="text-xl font-headline font-extrabold">Oishii Premium</h4>
                        <span class="text-primary font-bold">$$$</span>
                    </div>
                    <p class="text-on-surface-variant text-sm mb-4">Cocina Nikkei • 1.2 km</p>
                    <div class="flex gap-2">
                        <span class="px-3 py-1 bg-surface-container rounded-lg text-xs font-bold text-stone-600">Sashimi</span>
                        <span class="px-3 py-1 bg-surface-container rounded-lg text-xs font-bold text-stone-600">Terraza</span>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>
</body>
</html>
