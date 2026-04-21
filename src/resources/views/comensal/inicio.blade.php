@extends('layouts.app')

@section('title', 'Inicio - GastroGuía La Paz')

@section('content')
<header class="bg-[#FFF8F2] docked full-width top-0 sticky z-50 no-border shadow-sm">
    <div class="flex justify-between items-center w-full px-6 py-4 max-w-7xl mx-auto">
        <div class="text-2xl font-black text-[#C0392B] tracking-tighter">GastroGuía</div>
        <nav class="hidden md:flex items-center space-x-8 font-['Plus_Jakarta_Sans'] font-bold text-lg tracking-tight">
            <a class="text-[#C0392B] border-b-2 border-[#C0392B] pb-1" href="{{ route('comensal.inicio') }}">Inicio</a>
            <a class="text-stone-600 hover:text-[#C0392B] transition-colors" href="{{ route('comensal.explorar') }}">Explorar</a>
            <a class="text-stone-600 hover:text-[#C0392B] transition-colors" href="{{ route('comensal.perfil') ?? '#' }}">Perfil</a>
        </nav>
        <div class="flex items-center space-x-4">
            <a href="{{ route('comensal.perfil') ?? '#' }}" class="p-2 rounded-full hover:bg-stone-100 transition-colors duration-300 scale-95 active:scale-90 hidden md:block text-stone-600 font-bold text-sm">Hola, {{ Auth::guard('comensal')->check() ? Auth::guard('comensal')->user()->nombre : 'Comensal' }}</a>
            @if(Auth::guard('comensal')->check())
            <form method="POST" action="{{ route('logout') }}" class="inline">@csrf<button type="submit" class="p-2 rounded-full hover:bg-stone-100 transition-colors flex items-center gap-1 group text-stone-600 font-bold" title="Cerrar sesión"><span class="material-symbols-outlined text-stone-600 group-hover:text-red-500">logout</span></button></form>
            @endif
        </div>
    </div>
</header>

<main class="pb-24">
    <section class="relative h-[614px] min-h-[500px] w-full overflow-hidden">
        <div class="absolute inset-0 bg-stone-900">
            <img class="w-full h-full object-cover opacity-70" src="https://lh3.googleusercontent.com/aida-public/AB6AXuCg_iF3OdIur_RT5qkIAME3zJ5OerrBm_bSAQDK5x2DUdeZg3aKSFrDn7qt45JFUEO_lb-DnYBwqlnsornV6euEfh3oQNyERw_9g1FpFnvM88ubBKU-h3wvtdbEX0u17XcUuAtmJ74Thyv6U1cPQuISwcMmZLN1C8Z_TcPHOdF1h5UZjfbYGjlNaS8hJv_avRxYXRMHDGXdYzAJzx7G-V4kRN5fZtN85K_ypjGi3aokV2fshu5f2KirKRjGbED4YZP9MH6bApoIeo_7" />
            <div class="absolute inset-0 bg-gradient-to-t from-stone-900 via-transparent to-transparent"></div>
        </div>
        <div class="relative h-full flex flex-col justify-center items-center text-center px-4 max-w-5xl mx-auto space-y-8">
            <h1 class="text-5xl md:text-7xl font-extrabold text-white tracking-tight leading-tight" style="font-family: 'Plus Jakarta Sans', sans-serif;">Experiencias que <span class="text-[#ffdcc5]">deleitan</span></h1>
            <div class="w-full max-w-2xl bg-white rounded-full p-2 shadow-xl flex items-center gap-2">
                <div class="flex items-center pl-4 pr-2 text-stone-400">
                    <span class="material-symbols-outlined">location_on</span>
                    <input class="bg-transparent border-none focus:ring-0 text-stone-800 w-32 outline-none border-r border-stone-200" placeholder="La Paz, Bolivia" type="text" />
                </div>
                <div class="flex-1 flex items-center px-2">
                    <span class="material-symbols-outlined text-stone-400 mr-2">search</span>
                    <input class="bg-transparent border-none focus:ring-0 outline-none text-stone-800 w-full" placeholder="Busca salteñas, pique macho..." type="text" />
                </div>
                <button class="bg-[#9e2016] text-white px-8 py-3 rounded-full font-bold hover:bg-[#c0392b] transition-all">Buscar</button>
            </div>
        </div>
    </section>

    <!-- Category Filter Chips -->
    <section class="max-w-7xl mx-auto px-6 -mt-8 relative z-10 w-full">
        <div class="flex gap-3 overflow-x-auto pb-4 no-scrollbar">
            <button class="flex items-center gap-2 px-6 py-3 bg-[#9e2016] text-white rounded-full font-bold shadow-lg whitespace-nowrap"><span class="material-symbols-outlined text-sm" style="font-variation-settings: 'FILL' 1;">restaurant</span> Todos</button>
            <button class="flex items-center gap-2 px-6 py-3 bg-white text-[#59413d] border border-[#e1bfb9] rounded-full font-semibold hover:bg-stone-50 whitespace-nowrap"><span class="material-symbols-outlined text-sm">local_pizza</span> Pizza</button>
            <button class="flex items-center gap-2 px-6 py-3 bg-white text-[#59413d] border border-[#e1bfb9] rounded-full font-semibold hover:bg-stone-50 whitespace-nowrap"><span class="material-symbols-outlined text-sm">set_meal</span> Sushi</button>
            <button class="flex items-center gap-2 px-6 py-3 bg-white text-[#59413d] border border-[#e1bfb9] rounded-full font-semibold hover:bg-stone-50 whitespace-nowrap"><span class="material-symbols-outlined text-sm">lunch_dining</span> Burgers</button>
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
            <div class="relative z-10"><button class="bg-[#663100] text-white px-8 py-4 rounded-xl font-bold shadow-2xl hover:scale-105 transition-transform">Ver Restaurantes</button></div>
            <div class="absolute top-0 right-0 p-8 opacity-10 pointer-events-none"><span class="material-symbols-outlined text-9xl">local_offer</span></div>
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

        <div id="restaurantsGrid" class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @php $initial = $restaurants ?? collect(); @endphp
            @foreach($initial as $r)
            <div class="bg-white rounded-xl shadow-sm hover:shadow-xl transition-all duration-300 group overflow-hidden">
                <a href="{{ route('restaurante.show', $r->id) }}" class="block">
                    <div class="relative h-64 w-full overflow-hidden">
                        <img class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700" src="{{ $r->foto_portada ? asset('storage/'.$r->foto_portada) : 'https://via.placeholder.com/900x600?text=Restaurante' }}" alt="{{ $r->nombre }}"/>
                        <div class="absolute top-4 right-4 bg-white/90 backdrop-blur px-3 py-1 rounded-full flex items-center gap-1 shadow"><span class="material-symbols-outlined text-yellow-500 text-sm" style="font-variation-settings: 'FILL' 1;">star</span><span class="font-bold text-sm text-stone-800">—</span></div>
                    </div>
                    <div class="p-6">
                        <div class="flex justify-between items-start mb-2">
                            <h4 class="text-xl font-extrabold" style="font-family: 'Plus Jakarta Sans', sans-serif;">{{ $r->nombre }}</h4>
                            <span class="text-[#9e2016] font-bold">$$</span>
                        </div>
                        <p class="text-[#59413d] text-sm mb-4">{{ \Illuminate\Support\Str::limit($r->descripcion, 80) }} • <span class="restaurant-distance" data-lat="{{ $r->latitud }}" data-lng="{{ $r->longitud }}">—</span></p>
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
                        const resp = await fetch('{{ route('restaurantes.nearby') }}?lat='+lat+'&lng='+lng);
                        if(!resp.ok) return;
                        const json = await resp.json();
                        const items = json.data || [];
                        grid.innerHTML = items.map(r => {
                            const foto = r.foto_portada ? ('/storage/'+r.foto_portada) : 'https://via.placeholder.com/900x600?text=Restaurante';
                            const dist = (r.distance !== undefined) ? (Number(r.distance).toFixed(2)+' km') : '—';
                            return `
                                <div class="bg-white rounded-xl shadow-sm hover:shadow-xl transition-all duration-300 group overflow-hidden">
                                    <a href="/restaurante/${r.id}" class="block">
                                        <div class="relative h-64 w-full overflow-hidden">
                                            <img class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700" src="${foto}" alt="${r.nombre}"/>
                                            <div class="absolute top-4 right-4 bg-white/90 backdrop-blur px-3 py-1 rounded-full flex items-center gap-1 shadow"><span class="material-symbols-outlined text-yellow-500 text-sm" style="font-variation-settings: 'FILL' 1;">star</span><span class="font-bold text-sm text-stone-800">—</span></div>
                                        </div>
                                        <div class="p-6">
                                            <div class="flex justify-between items-start mb-2">
                                                <h4 class="text-xl font-extrabold">${r.nombre}</h4>
                                                <span class="text-[#9e2016] font-bold">$$</span>
                                            </div>
                                            <p class="text-[#59413d] text-sm mb-4">${(r.descripcion||'').substring(0,80)} • <span>${dist}</span></p>
                                        </div>
                                    </a>
                                </div>
                            `;
                        }).join('');
                    }catch(e){ console.error(e); }
                }, (err) => { console.warn('Geolocation denied or unavailable', err); }, { enableHighAccuracy: true, timeout: 10000 });
            }
        });
        </script>
    </section>
</main>
@endsection
