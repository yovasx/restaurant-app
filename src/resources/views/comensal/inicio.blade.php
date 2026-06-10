@extends('layouts.app')

@section('title', 'Inicio - GastroGuía La Paz')
@section('layout-flush', 'true')

@section('content')
<header class="bg-surface/95 backdrop-blur-md full-width top-0 sticky z-50 shadow-sm border-b border-stone-200/60">
    <div class="flex justify-between items-center w-full px-6 py-3 max-w-7xl mx-auto">
        <div class="flex items-center gap-3">
            <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-primary text-white shadow-sm">
                <span class="material-symbols-outlined text-lg">restaurant_menu</span>
            </div>
            <span class="text-lg font-black text-primary tracking-tighter font-headline hidden sm:block">GastroGuía</span>
        </div>
        <nav class="hidden md:flex items-center gap-6 font-headline font-bold text-sm">
            <a class="text-primary border-b-2 border-primary pb-1" href="{{ route('comensal.inicio') }}">Inicio</a>
            <a class="text-stone-500 hover:text-primary transition-colors" href="{{ route('comensal.explorar') }}">Explorar</a>
            <a class="text-stone-500 hover:text-primary transition-colors" href="{{ route('comensal.perfil') }}">Perfil</a>
        </nav>
        <div class="flex items-center gap-3">
            <span class="hidden sm:block text-sm font-bold text-stone-600">Hola, {{ Auth::guard('comensal')->user()->nombre_completo ?: 'Comensal' }}</span>
            <form method="POST" action="{{ route('logout.comensal') }}" class="inline">@csrf<button type="submit" class="p-2 rounded-full hover:bg-stone-100 transition-colors" title="Cerrar sesión"><span class="material-symbols-outlined text-stone-500 hover:text-red-500">logout</span></button></form>
        </div>
    </div>
</header>

<main class="pb-24">
    {{-- Welcome Banner --}}
    <section class="bg-gradient-to-br from-[#9e2016] to-[#c0392b] text-white">
        <div class="max-w-7xl mx-auto px-6 py-8 md:py-10">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <h1 class="text-2xl md:text-3xl font-extrabold tracking-tight font-headline">Bienvenido, {{ Auth::guard('comensal')->user()->nombre_completo ?: 'Comensal' }}</h1>
                    <p class="text-white/80 mt-1 text-sm md:text-base">Descubre nuevos sabores y revive tus favoritos</p>
                </div>
                <form action="{{ route('comensal.explorar') }}" method="GET" class="flex items-center bg-white/20 backdrop-blur rounded-full px-4 py-2 w-full md:w-80 gap-2">
                    <span class="material-symbols-outlined text-white/70 text-sm">search</span>
                    <input name="q" class="bg-transparent border-none focus:ring-0 text-sm w-full text-white placeholder:text-white/60 outline-none" placeholder="Busca salteñas, pique macho..." type="text"/>
                </form>
            </div>
        </div>
    </section>

    {{-- KPI Row --}}
    <section class="max-w-7xl mx-auto px-6 -mt-5 relative z-10">
        <div class="grid grid-cols-3 gap-3 md:gap-4">
            <div class="bg-white rounded-xl shadow-sm border border-stone-100 p-4 text-center">
                <span class="material-symbols-outlined text-red-400 text-xl" style="font-variation-settings: 'FILL' 1;">favorite</span>
                <p class="text-2xl font-black text-on-surface mt-1">{{ number_format($totalFavoritos ?? 0) }}</p>
                <p class="text-[10px] font-bold text-stone-500 uppercase tracking-wider">Favoritos</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-stone-100 p-4 text-center">
                <span class="material-symbols-outlined text-amber-400 text-xl" style="font-variation-settings: 'FILL' 1;">rate_review</span>
                <p class="text-2xl font-black text-on-surface mt-1">{{ number_format($totalResenas ?? 0) }}</p>
                <p class="text-[10px] font-bold text-stone-500 uppercase tracking-wider">Reseñas</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-stone-100 p-4 text-center">
                <span class="material-symbols-outlined text-indigo-400 text-xl" style="font-variation-settings: 'FILL' 1;">footprint</span>
                <p class="text-2xl font-black text-on-surface mt-1">{{ number_format($totalVisitas ?? 0) }}</p>
                <p class="text-[10px] font-bold text-stone-500 uppercase tracking-wider">Visitas</p>
            </div>
        </div>
    </section>

    {{-- Quick Actions --}}
    <section class="max-w-7xl mx-auto px-6 mt-6">
        <div class="flex gap-3 overflow-x-auto pb-2 no-scrollbar">
            <a href="{{ route('comensal.explorar') }}" class="flex items-center gap-2 bg-white border border-stone-200 px-5 py-2.5 rounded-full text-sm font-bold text-stone-700 hover:bg-stone-50 transition-colors whitespace-nowrap shadow-sm">
                <span class="material-symbols-outlined text-primary text-sm">explore</span> Explorar restaurantes
            </a>
            <a href="{{ route('comensal.perfil') }}" class="flex items-center gap-2 bg-white border border-stone-200 px-5 py-2.5 rounded-full text-sm font-bold text-stone-700 hover:bg-stone-50 transition-colors whitespace-nowrap shadow-sm">
                <span class="material-symbols-outlined text-primary text-sm">person</span> Mi perfil
            </a>
        </div>
    </section>

    {{-- Recent Activity --}}
    <section class="max-w-7xl mx-auto px-6 mt-8">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            {{-- Recent Favorites --}}
            <div class="bg-white rounded-2xl shadow-sm border border-stone-100 p-5">
                <h3 class="font-headline font-bold text-base text-on-surface mb-4 flex items-center gap-2">
                    <span class="material-symbols-outlined text-red-400 text-lg" style="font-variation-settings: 'FILL' 1;">favorite</span>
                    Favoritos recientes
                </h3>
                @if (count($recentFavoritos ?? []) > 0)
                    <div class="space-y-2">
                        @foreach ($recentFavoritos as $fav)
                            <a href="{{ route('restaurante.show', $fav->restaurante_id) }}" class="flex items-center gap-3 py-2 px-3 rounded-xl hover:bg-stone-50 transition-colors">
                                <div class="w-10 h-10 rounded-lg bg-stone-100 overflow-hidden shrink-0 flex items-center justify-center">
                                    @if ($fav->restaurante && $fav->restaurante->foto_portada_url)
                                        <img class="w-full h-full object-cover" src="{{ $fav->restaurante->foto_portada_url }}" alt="">
                                    @else
                                        <span class="material-symbols-outlined text-stone-400">restaurant</span>
                                    @endif
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="text-sm font-bold text-on-surface truncate">{{ $fav->restaurante->nombre ?? 'Restaurante' }}</p>
                                    <p class="text-xs text-stone-400">{{ $fav->created_at->diffForHumans() }}</p>
                                </div>
                                <span class="material-symbols-outlined text-stone-300 text-sm">chevron_right</span>
                            </a>
                        @endforeach
                    </div>
                    <a href="{{ route('comensal.explorar') }}" class="mt-2 inline-block text-xs font-bold text-primary hover:underline">Ver todos mis favoritos →</a>
                @else
                    <div class="flex flex-col items-center py-6 text-stone-400">
                        <span class="material-symbols-outlined text-3xl mb-2">favorite_border</span>
                        <p class="text-sm font-medium">Sin favoritos aún</p>
                        <p class="text-xs">Explora y guarda tus lugares preferidos.</p>
                    </div>
                @endif
            </div>

            {{-- Recent Reviews --}}
            <div class="bg-white rounded-2xl shadow-sm border border-stone-100 p-5">
                <h3 class="font-headline font-bold text-base text-on-surface mb-4 flex items-center gap-2">
                    <span class="material-symbols-outlined text-amber-400 text-lg" style="font-variation-settings: 'FILL' 1;">rate_review</span>
                    Mis últimas reseñas
                </h3>
                @if (count($recentResenas ?? []) > 0)
                    <div class="space-y-3">
                        @foreach ($recentResenas as $resena)
                            <div class="py-2 px-3">
                                <div class="flex items-center gap-2 mb-1">
                                    <div class="flex items-center gap-0.5">
                                        @for ($i = 1; $i <= 5; $i++)
                                            <span class="material-symbols-outlined text-sm {{ $i <= $resena->score ? 'text-amber-400' : 'text-stone-200' }}" style="font-variation-settings: 'FILL' 1;">star</span>
                                        @endfor
                                    </div>
                                    <span class="text-xs text-stone-400">{{ $resena->created_at->diffForHumans() }}</span>
                                </div>
                                <p class="text-sm text-stone-600">{{ $resena->menu->nombre ?? '—' }} · {{ Str::limit($resena->comentario, 60) }}</p>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="flex flex-col items-center py-6 text-stone-400">
                        <span class="material-symbols-outlined text-3xl mb-2">edit_note</span>
                        <p class="text-sm font-medium">Sin reseñas aún</p>
                        <p class="text-xs">Comparte tu opinión después de visitar un restaurante.</p>
                    </div>
                @endif
            </div>
        </div>
    </section>

    {{-- Promo Banner --}}
    <section class="max-w-7xl mx-auto px-6 mt-8">
        <div class="bg-[#fc8f34] rounded-2xl p-6 flex flex-col md:flex-row items-center justify-between gap-4 overflow-hidden relative">
            <div class="relative z-10 text-[#663100]">
                <span class="inline-block px-3 py-1 bg-[#663100] text-[#fc8f34] rounded-full text-xs font-bold tracking-widest uppercase mb-3">Oferta de la Semana</span>
                <h2 class="text-xl md:text-2xl font-extrabold font-headline">2x1 en Cenas de Autor</h2>
                <p class="opacity-90 text-sm mt-1">Reserva tu mesa para este jueves y disfruta de un menú degustación exclusivo.</p>
            </div>
            <a href="{{ route('comensal.explorar') }}" class="relative z-10 bg-[#663100] text-white px-6 py-3 rounded-xl font-bold shadow-lg hover:scale-105 transition-transform shrink-0 text-sm">Ver Restaurantes</a>
        </div>
    </section>

    {{-- Category Pills --}}
    <section class="max-w-7xl mx-auto px-6 mt-8">
        <div class="flex items-center justify-between mb-4">
            <h2 class="font-headline font-bold text-lg text-on-surface">Descubre por categoría</h2>
            <a href="{{ route('comensal.explorar') }}" class="text-xs font-bold text-primary hover:underline">Ver todo</a>
        </div>
        <div class="flex gap-3 overflow-x-auto pb-2 no-scrollbar">
            <a href="{{ route('comensal.inicio') }}"
               class="flex items-center gap-2 px-5 py-2.5 {{ !request('categoria') ? 'bg-primary text-white' : 'bg-white text-stone-600 border border-stone-200' }} rounded-full font-bold whitespace-nowrap transition-all hover:bg-primary hover:text-white shadow-sm">
                <span class="material-symbols-outlined text-sm" style="font-variation-settings: 'FILL' 1;">restaurant</span> Todos
            </a>
            @php
                $iconos = [
                    'Pizza' => 'local_pizza', 'Sushi' => 'set_meal', 'Burgers' => 'lunch_dining',
                    'Parrilla' => 'outdoor_grill', 'Comida Boliviana' => 'restaurant_menu',
                    'Café y Brunch' => 'local_cafe', 'Pastas' => 'ramen_dining',
                    'Pollos' => 'restaurant', 'Chifa' => 'takeout_dining',
                    'Mariscos' => 'set_meal', 'Postres y Panadería' => 'cake',
                    'Cocina Fusión' => 'menu_book',
                ];
            @endphp
            @foreach($categorias as $cat)
                <a href="{{ route('comensal.inicio', ['categoria' => $cat->id]) }}"
                   class="flex items-center gap-2 px-5 py-2.5 {{ request('categoria') == $cat->id ? 'bg-primary text-white' : 'bg-white text-stone-600 border border-stone-200' }} rounded-full font-semibold whitespace-nowrap transition-all hover:bg-primary hover:text-white shadow-sm">
                    <span class="material-symbols-outlined text-sm">{{ $iconos[$cat->nombre_categoria] ?? 'restaurant' }}</span>
                    {{ $cat->nombre_categoria }}
                </a>
            @endforeach
        </div>
    </section>

    {{-- Restaurant Grid --}}
    <section class="max-w-7xl mx-auto px-6 mt-6" id="favoritos">
        <div class="flex items-center justify-between mb-4">
            <h2 class="font-headline font-bold text-lg text-on-surface">Restaurantes Cercanos</h2>
            <button class="text-xs font-bold text-primary hover:underline flex items-center gap-1 descubre-mas">Ver todo <span class="material-symbols-outlined text-sm">arrow_forward</span></button>
        </div>

        <div id="restaurantsGrid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
            @php $initial = $restaurants ?? collect(); @endphp
            @foreach($initial as $r)
            <div class="bg-white rounded-xl shadow-sm hover:shadow-lg transition-all duration-300 group overflow-hidden border border-stone-100">
                <a href="{{ route('restaurante.show', $r->id) }}" class="block">
                    <div class="relative h-48 w-full overflow-hidden">
                        <img class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" src="{{ media_url($r->foto_portada) ?: 'https://via.placeholder.com/900x600?text=Restaurante' }}" alt="{{ $r->nombre }}"/>
                        <div class="absolute top-3 right-3 bg-white/90 backdrop-blur px-2.5 py-1 rounded-full flex items-center gap-1 shadow-sm"><span class="material-symbols-outlined text-yellow-500 text-sm" style="font-variation-settings: 'FILL' 1;">star</span><span class="font-bold text-sm text-stone-800">{{ isset($r->avg_rating) ? number_format($r->avg_rating, 1) : '—' }}</span></div>
                    </div>
                    <div class="p-4">
                        <div class="flex justify-between items-start mb-1">
                            <h4 class="text-base font-extrabold font-headline">{{ $r->nombre }}</h4>
                        </div>
                        <p class="text-stone-500 text-sm truncate">{{ \Illuminate\Support\Str::limit($r->descripcion, 60) }}</p>
                        <div class="flex items-center gap-2 mt-2">
                            <span class="text-xs text-stone-400 restaurant-distance" data-lat="{{ $r->latitud }}" data-lng="{{ $r->longitud }}">—</span>
                            @if ($r->avg_price)
                                <span class="text-xs font-bold text-primary">Bs {{ number_format($r->avg_price, 0) }}</span>
                            @endif
                        </div>
                    </div>
                </a>
            </div>
            @endforeach
        </div>

        <script>
        document.addEventListener('DOMContentLoaded', function(){
            const grid = document.getElementById('restaurantsGrid');
            if(!grid) return;
            if(navigator.geolocation){
                navigator.geolocation.getCurrentPosition(async (pos) => {
                    const lat = pos.coords.latitude;
                    const lng = pos.coords.longitude;
                    try{
                        const resp = await fetch('{{ route('restaurantes.nearby') }}?lat='+lat+'&lng='+lng+'&ajax=1');
                        if(!resp.ok) return;
                        const json = await resp.json();
                        const items = json.data || [];
                        grid.innerHTML = items.map(r => {
                            const foto = r.foto_portada_url || 'https://via.placeholder.com/900x600?text=Restaurante';
                            const dist = (r.distance !== undefined) ? (Number(r.distance).toFixed(2)+' km') : '—';
                            return `
                                <div class="bg-white rounded-xl shadow-sm hover:shadow-lg transition-all duration-300 group overflow-hidden border border-stone-100">
                                    <a href="/restaurante/${r.id}" class="block">
                                        <div class="relative h-48 w-full overflow-hidden">
                                            <img class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" src="${foto}" alt="${r.nombre}"/>
                                            <div class="absolute top-3 right-3 bg-white/90 backdrop-blur px-2.5 py-1 rounded-full flex items-center gap-1 shadow-sm"><span class="material-symbols-outlined text-yellow-500 text-sm" style="font-variation-settings: 'FILL' 1;">star</span><span class="font-bold text-sm text-stone-800">${r.avg_rating ? Number(r.avg_rating).toFixed(1) : '—'}</span></div>
                                        </div>
                                        <div class="p-4">
                                            <div class="flex justify-between items-start mb-1">
                                                <h4 class="text-base font-extrabold font-headline">${r.nombre}</h4>
                                            </div>
                                            <p class="text-stone-500 text-sm truncate">${(r.descripcion||'').substring(0,60)}</p>
                                            <div class="flex items-center gap-2 mt-2">
                                                <span class="text-xs text-stone-400">${dist}</span>
                                                ${r.avg_price ? `<span class="text-xs font-bold text-primary">Bs ${Number(r.avg_price).toFixed(0)}</span>` : ''}
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            `;
                        }).join('');
                    }catch(e){ console.error(e); }
                }, (err) => { console.warn('Geolocalización no disponible o denegada', err); }, { enableHighAccuracy: true, timeout: 10000 });
            }
        });
        </script>
    </section>
</main>
@endsection