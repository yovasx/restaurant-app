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
@extends('layouts.app')

@section('title', 'Inicio - GastroGuía La Paz')

@section('content')
<header class="bg-[#FFF8F2] docked full-width top-0 sticky z-50 no-border shadow-sm">
    <div class="flex justify-between items-center w-full px-6 py-4 max-w-7xl mx-auto">
        <div class="text-2xl font-black text-[#C0392B] tracking-tighter">
            GastroGuía
        </div>
        <nav class="hidden md:flex items-center space-x-8 font-['Plus_Jakarta_Sans'] font-bold text-lg tracking-tight">
            <a class="text-[#C0392B] border-b-2 border-[#C0392B] pb-1" href="{{ route('comensal.inicio') }}">Inicio</a>
            <a class="text-stone-600 hover:text-[#C0392B] transition-colors" href="#">Explorar</a>
            <a class="text-stone-600 hover:text-[#C0392B] transition-colors" href="{{ route('comensal.perfil') ?? '#' }}">Perfil</a>
        </nav>
        
        <div class="flex items-center space-x-4">
            <a href="{{ route('comensal.perfil') ?? '#' }}" class="p-2 rounded-full hover:bg-stone-100 transition-colors duration-300 scale-95 active:scale-90 hidden md:block text-stone-600 font-bold text-sm">
                Hola, {{ Auth::guard('comensal')->check() ? Auth::guard('comensal')->user()->nombre : 'Comensal' }}
            </a>
            @if(Auth::guard('comensal')->check())
            <form method="POST" action="{{ route('logout') }}" class="inline">
                @csrf
                <button type="submit" class="p-2 rounded-full hover:bg-stone-100 transition-colors flex items-center gap-1 group text-stone-600 font-bold" title="Cerrar sesión">
                    <span class="material-symbols-outlined text-stone-600 group-hover:text-red-500">logout</span>
                </button>
            </form>
            @endif
        </div>
    </div>
</header>

<main class="pb-24">
    <section class="relative h-[614px] min-h-[500px] w-full overflow-hidden">
        <div class="absolute inset-0 bg-stone-900">
            <img class="w-full h-full object-cover opacity-70" src="https://lh3.googleusercontent.com/aida-public/AB6AXuCg_iF3OdIur_RT5qkIAME3zJ5OerrBm_bSAQDK5x2DUdeZg3aKSFrDn7qt45JFUEO_lb-DnYBwqlnsornV6euEfh3oQNyERw_9g1FpFnvM88ubBKU-h3wvtdbEX0u17XcUuAtmJ74Thyv6U1cPQuISwcMmZLN1C8Z_TcPHOdF1h5UZjfbYGjlNaS8hJv_avRxYXRMHDGXdYzAJzx7G-V4kRN5fZtN85K_ypjGi3aokV2fshu5f2KirKRjGbED4YZP9MH6bApoIeo_7"/>
            <div class="absolute inset-0 bg-gradient-to-t from-stone-900 via-transparent to-transparent"></div>
        </div>
        <div class="relative h-full flex flex-col justify-center items-center text-center px-4 max-w-5xl mx-auto space-y-8">
            <h1 class="text-5xl md:text-7xl font-extrabold text-white tracking-tight leading-tight" style="font-family: 'Plus Jakarta Sans', sans-serif;">
                Experiencias que <span class="text-[#ffdcc5]">deleitan</span>
            </h1>
            <div class="w-full max-w-2xl bg-white rounded-full p-2 shadow-xl flex items-center gap-2">
                <div class="flex items-center pl-4 pr-2 text-stone-400">
                    <span class="material-symbols-outlined">location_on</span>
                    <input class="bg-transparent border-none focus:ring-0 text-stone-800 w-32 outline-none border-r border-stone-200" placeholder="La Paz, Bolivia" type="text"/>
                </div>
                <div class="flex-1 flex items-center px-2">
                    <span class="material-symbols-outlined text-stone-400 mr-2">search</span>
                    <input class="bg-transparent border-none focus:ring-0 outline-none text-stone-800 w-full" placeholder="Busca salteñas, pique macho..." type="text"/>
                </div>
                <button class="bg-[#9e2016] text-white px-8 py-3 rounded-full font-bold hover:bg-[#c0392b] transition-all">Buscar</button>
            </div>
        </div>
    </section>

    <!-- Category Filter Chips -->
    <section class="max-w-7xl mx-auto px-6 -mt-8 relative z-10 w-full">
        <div class="flex gap-3 overflow-x-auto pb-4 no-scrollbar">
            <button class="flex items-center gap-2 px-6 py-3 bg-[#9e2016] text-white rounded-full font-bold shadow-lg whitespace-nowrap">
                <span class="material-symbols-outlined text-sm" style="font-variation-settings: 'FILL' 1;">restaurant</span> Todos
            </button>
            <button class="flex items-center gap-2 px-6 py-3 bg-white text-[#59413d] border border-[#e1bfb9] rounded-full font-semibold hover:bg-stone-50 whitespace-nowrap">
                <span class="material-symbols-outlined text-sm">local_pizza</span> Pizza
            </button>
            <button class="flex items-center gap-2 px-6 py-3 bg-white text-[#59413d] border border-[#e1bfb9] rounded-full font-semibold hover:bg-stone-50 whitespace-nowrap">
                <span class="material-symbols-outlined text-sm">set_meal</span> Sushi
            </button>
            <button class="flex items-center gap-2 px-6 py-3 bg-white text-[#59413d] border border-[#e1bfb9] rounded-full font-semibold hover:bg-stone-50 whitespace-nowrap">
                <span class="material-symbols-outlined text-sm">lunch_dining</span> Burgers
            </button>
        </div>
    </section>

    <!-- Promotional Banner Strip -->
    <section class="max-w-7xl mx-auto px-6 mt-12 w-full">
        <div class="bg-[#fc8f34] rounded-2xl p-8 flex flex-col md:flex-row items-center justify-between gap-6 overflow-hidden relative">
            <div class="relative z-10 text-[#663100] max-w-lg">
                <span class="inline-block px-3 py-1 bg-[#663100] text-[#fc8f34] rounded-full text-xs font-bold tracking-widest uppercase mb-4">Oferta de la Semana</span>
                <h2 class="text-3xl font-extrabold mb-2" style="font-family: 'Plus Jakarta Sans', sans-serif;">2x1 en Cenas de Autor</h2>
                <p class="opacity-90 font-medium">Reserva tu mesa para este jueves y disfruta de un menú degustación exclusivo para dos al precio de uno.</p>
            </div>
            <div class="relative z-10">
                <button class="bg-[#663100] text-white px-8 py-4 rounded-xl font-bold shadow-2xl hover:scale-105 transition-transform">Ver Restaurantes</button>
            </div>
            <div class="absolute top-0 right-0 p-8 opacity-10 pointer-events-none">
                <span class="material-symbols-outlined text-9xl">local_offer</span>
            </div>
        </div>
    </section>

    <!-- Near You Section -->
    <section class="max-w-7xl mx-auto px-6 mt-16 w-full">
        <div class="flex justify-between items-end mb-8">
            <div>
                <h3 class="text-sm uppercase tracking-[0.2em] text-[#944a00] font-bold mb-2">Descubrimientos Locales</h3>
                <h2 class="text-4xl font-extrabold text-[#1e1b18] tracking-tight" style="font-family: 'Plus Jakarta Sans', sans-serif;">Restaurantes Cercanos</h2>
            </div>
            <button class="text-[#9e2016] font-bold flex items-center gap-1 hover:underline">Ver todo <span class="material-symbols-outlined text-sm">arrow_forward</span></button>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Card 1 -->
            <div class="bg-white rounded-xl shadow-sm hover:shadow-xl transition-all duration-300 group overflow-hidden">
                <div class="relative h-64 w-full overflow-hidden">
                    <img class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700" src="https://lh3.googleusercontent.com/aida-public/AB6AXuBSXgQG9jTV3hNb7o-c7vZD3ivJBr4rmwD7ZiivNfUg0oAv_vnWYvWxp7x2delqx-okTDDg1kvidFz1ZjXGA8R3pbVLl1oDqSkIezE4WZOBR--U4y1NTZad1QiRZJH74J28qXtDm-kGJb7C1mVnSc1qj0YTK3zDh4VHay6DRObnnnV9KM-gN7yI8NuynfPNtn67Sg46ajTCy0oK6SEKPE7ei9yimH2iLi0-zriNVbzno0XEZpfdpK_wX1Jfd1TpRR3dO_ewW6kInjg6"/>
                    <div class="absolute top-4 right-4 bg-white/90 backdrop-blur px-3 py-1 rounded-full flex items-center gap-1 shadow">
                        <span class="material-symbols-outlined text-yellow-500 text-sm" style="font-variation-settings: 'FILL' 1;">star</span>
                        <span class="font-bold text-sm text-stone-800">4.9</span>
                    </div>
                </div>
                <div class="p-6">
                    <div class="flex justify-between items-start mb-2">
                        <h4 class="text-xl font-extrabold" style="font-family: 'Plus Jakarta Sans', sans-serif;">Oishii Premium</h4>
                        <span class="text-[#9e2016] font-bold">$$$</span>
                    </div>
                    <p class="text-[#59413d] text-sm mb-4">Cocina Nikkei • 1.2 km</p>
                    <div class="flex gap-2">
                        <span class="px-3 py-1 bg-[#f3ede7] rounded-lg text-xs font-bold text-stone-600">Sashimi</span>
                        <span class="px-3 py-1 bg-[#f3ede7] rounded-lg text-xs font-bold text-stone-600">Terraza</span>
                    </div>
                </div>
            </div>
            <!-- Card 2 -->
            <div class="bg-white rounded-xl shadow-sm hover:shadow-xl transition-all duration-300 group overflow-hidden">
                <div class="relative h-64 w-full overflow-hidden">
                    <img class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700" src="https://lh3.googleusercontent.com/aida-public/AB6AXuBaGP1lK-0p1wd7Ikh8PUnuwl0wB0FqpfEoieU0wemdFmvOvdBEjRcvGA-wKckz_IFEwnYnFc9VFwqzavEPEHxkVtcHGCWf5UKG59c6XXHTmkZ6yhUsHFWs5IOAybtcm6P5R4i-YGdcxzohhOTic2ISiylt66wMjP3j8oRiLrH_Dt--BsvFvpiPXfuKg496G1l5pu1pK0BQdz24lyYMlKNXlk03WbtD9EA34CtaNkcQw3f43ICvcuaWxEJI31p5Z6xdnCv7kp29vhs-"/>
                    <div class="absolute top-4 right-4 bg-white/90 backdrop-blur px-3 py-1 rounded-full flex items-center gap-1 shadow">
                        <span class="material-symbols-outlined text-yellow-500 text-sm" style="font-variation-settings: 'FILL' 1;">star</span>
                        <span class="font-bold text-sm text-stone-800">4.7</span>
                    </div>
                </div>
                <div class="p-6">
                    <div class="flex justify-between items-start mb-2">
                        <h4 class="text-xl font-extrabold" style="font-family: 'Plus Jakarta Sans', sans-serif;">The Grill House</h4>
                        <span class="text-[#9e2016] font-bold">$$</span>
                    </div>
                    <p class="text-[#59413d] text-sm mb-4">American Grill • 0.8 km</p>
                    <div class="flex gap-2">
                        <span class="px-3 py-1 bg-[#f3ede7] rounded-lg text-xs font-bold text-stone-600">Artesanal</span>
                        <span class="px-3 py-1 bg-[#f3ede7] rounded-lg text-xs font-bold text-stone-600">Craft Beer</span>
                    </div>
                </div>
            </div>
            <!-- Card 3 -->
            <div class="bg-white rounded-xl shadow-sm hover:shadow-xl transition-all duration-300 group overflow-hidden">
                <div class="relative h-64 w-full overflow-hidden">
                    <img class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700" src="https://lh3.googleusercontent.com/aida-public/AB6AXuDZov9Ln4MOZFc45CzvmBFTTS8d9A7bh0oDricuYorc8LxcvNYxHFKz3WAK5SmcJHdesevaRXXXklRu4bjh-4DRQmILEqebLyg5qHT8EYIF_xb3s-5ICHOQWa6alxI1Cp6spMyKkF4ccY3iDsYoSZU3HaKML8hj4pBjpE8VrodD0CGoNpTs7HmwA5dQgyeqcjxyqsiinqa4TrRj409BLz7_irlvO60RhsdrMlL3FG2F-oaTgNNmoKkbK6ZPyhMIMa_lydnnpQRDd4JD"/>
                    <div class="absolute top-4 right-4 bg-white/90 backdrop-blur px-3 py-1 rounded-full flex items-center gap-1 shadow">
                        <span class="material-symbols-outlined text-yellow-500 text-sm" style="font-variation-settings: 'FILL' 1;">star</span>
                        <span class="font-bold text-sm text-stone-800">4.8</span>
                    </div>
                </div>
                <div class="p-6">
                    <div class="flex justify-between items-start mb-2">
                        <h4 class="text-xl font-extrabold" style="font-family: 'Plus Jakarta Sans', sans-serif;">Napoli Vera</h4>
                        <span class="text-[#9e2016] font-bold">$$</span>
                    </div>
                    <p class="text-[#59413d] text-sm mb-4">Italiano • 2.4 km</p>
                    <div class="flex gap-2">
                        <span class="px-3 py-1 bg-[#f3ede7] rounded-lg text-xs font-bold text-stone-600">Leña</span>
                        <span class="px-3 py-1 bg-[#f3ede7] rounded-lg text-xs font-bold text-stone-600">Vino</span>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>
@endsection
