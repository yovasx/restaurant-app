@extends('layouts.app')

@section('title', 'Explorar - GastroGuía')

@section('content')
<!-- Leaflet CSS/JS (loaded here) -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<style>
    .restaurant-marker{ width:48px; height:48px; border-radius:50%; overflow:hidden; box-shadow:0 4px 10px rgba(0,0,0,0.2); border:2px solid #fff; }
    .restaurant-marker img{ width:100%; height:100%; object-fit:cover; display:block; }
    .me-marker{ width:36px; height:36px; border-radius:50%; display:flex; align-items:center; justify-content:center; box-shadow:0 3px 8px rgba(0,0,0,0.25); border:2px solid #fff; background:#9e2016; color:#fff; }
    .me-marker svg{ display:block; width:18px; height:18px; }
</style>

<!-- Top Navigation Shell -->
<nav class="bg-[#FFF8F2] dark:bg-stone-900 opacity-95 backdrop-blur-md shadow-sm dark:shadow-none flex justify-between items-center w-full px-6 py-4 sticky top-0 z-50">
    <div class="flex items-center gap-6">
        <a href="{{ route('comensal.inicio') }}" class="text-sm bg-white px-3 py-1 rounded-md shadow-sm hover:bg-stone-50">Volver al inicio</a>
        <h1 class="text-2xl font-bold tracking-tight text-[#C0392B]">El Comensal</h1>
        <div class="hidden md:flex items-center space-gap-6 gap-6">
            <a class="text-[#C0392B] font-bold border-b-2 border-[#C0392B] pb-1" href="{{ route('comensal.explorar') }}">Explorar</a>
            <a class="text-stone-600 font-medium hover:bg-stone-100 transition-colors duration-300 px-3 py-1 rounded-lg" href="#">Favoritos</a>
            <a class="text-stone-600 font-medium hover:bg-stone-100 transition-colors duration-300 px-3 py-1 rounded-lg" href="#">Reservas</a>
        </div>
    </div>
    <div class="flex items-center gap-4">
        <div class="hidden sm:flex items-center bg-surface-container-highest px-4 py-2 rounded-full gap-2 w-64">
            <span class="material-symbols-outlined text-on-surface-variant text-sm">search</span>
            <input id="globalSearch" class="bg-transparent border-none focus:ring-0 text-sm w-full placeholder:text-on-surface-variant/60" placeholder="Buscar en La Paz..." type="text"/>
        </div>
        <button class="material-symbols-outlined text-on-surface-variant p-2 hover:bg-stone-100 rounded-full">notifications</button>
        <button id="openFilters" class="material-symbols-outlined text-on-surface-variant p-2 hover:bg-stone-100 rounded-full">tune</button>
    </div>
</nav>

<main class="flex h-[calc(100vh-72px)] overflow-hidden">
    <!-- Left Side: List + Filters -->
    <section class="w-full md:w-[480px] lg:w-[560px] flex flex-col bg-surface-container-low overflow-hidden">
        <div class="p-6 bg-surface-container-low border-b border-outline-variant/20">
            <div class="flex items-end justify-between mb-4">
                <div>
                    <p class="text-primary font-bold text-xs tracking-widest uppercase mb-1">Cerca de ti</p>
                    <h2 class="font-headline text-2xl font-bold text-on-background">Explorar restaurantes</h2>
                </div>
                <span id="resultCount" class="bg-secondary-container/20 text-on-secondary-container px-3 py-1 rounded-full text-xs font-bold">-- Restaurantes</span>
            </div>

            <div class="flex gap-2 overflow-x-auto pb-2 scrollbar-hide">
                <button class="filter-pill flex items-center gap-2 bg-primary text-on-primary px-4 py-2 rounded-full text-sm font-semibold whitespace-nowrap shadow-md" data-sort="distance">
                    <span class="material-symbols-outlined text-sm">near_me</span> Menor Distancia
                </button>
                <button class="filter-pill flex items-center gap-2 bg-surface-container-lowest text-on-surface-variant px-4 py-2 rounded-full text-sm font-semibold whitespace-nowrap hover:bg-stone-100" data-sort="rating">
                    <span class="material-symbols-outlined text-sm">star</span> Mejor Calificación
                </button>
                <button class="filter-pill flex items-center gap-2 bg-surface-container-lowest text-on-surface-variant px-4 py-2 rounded-full text-sm font-semibold whitespace-nowrap hover:bg-stone-100" data-sort="price">
                    <span class="material-symbols-outlined text-sm">payments</span> Precio
                </button>
                <button id="openAdvanced" class="flex items-center gap-2 bg-surface-container-lowest text-on-surface-variant px-4 py-2 rounded-full text-sm font-semibold whitespace-nowrap hover:bg-stone-100">
                    <span class="material-symbols-outlined text-sm">filter_list</span> Filtros
                </button>
            </div>
        </div>

        <div id="listContainer" class="flex-1 overflow-y-auto p-6 space-y-6">
            <!-- Cards will be injected here -->
        </div>
    </section>

    <!-- Right Side: Map -->
    <section class="hidden md:flex flex-1 relative bg-surface-container-highest">
        <div id="map" class="absolute inset-0"></div>
    </section>
</main>

<!-- Advanced filters modal (simple) -->
<div id="advancedFilters" class="fixed inset-0 bg-black/40 hidden items-center justify-center z-50">
    <div class="bg-white rounded-xl p-6 w-11/12 max-w-md">
        <h3 class="font-bold mb-3">Filtros avanzados</h3>
        <label class="text-sm">Máxima distancia (km)</label>
        <input id="maxDistance" type="range" min="0.5" max="50" step="0.5" value="5" class="w-full" />
        <div class="flex justify-between text-xs text-stone-500 mt-1"><span>0.5 km</span><span id="maxDistanceValue">5 km</span><span>50 km</span></div>
        <div class="mt-4 flex justify-end gap-2">
            <button id="closeAdvanced" class="px-4 py-2 rounded-lg bg-stone-100">Cancelar</button>
            <button id="applyAdvanced" class="px-4 py-2 rounded-lg bg-[#9e2016] text-white">Aplicar</button>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function(){
    const nearbyUrl = '{{ route('restaurantes.nearby') }}';
    const detailBase = '{{ url('restaurante') }}';
    let restaurants = @json($restaurants ?? []);
    let currentMarkers = L.layerGroup();

    // Elements
    const maxDistanceInput = document.getElementById('maxDistance');
    const maxDistanceValue = document.getElementById('maxDistanceValue');

    // Initialize map
    const defaultLat = -16.489689, defaultLng = -68.119294; // La Paz
    const map = L.map('map', { zoomControl: true }).setView([defaultLat, defaultLng], 13);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { attribution: '&copy; OpenStreetMap contributors' }).addTo(map);
    currentMarkers.addTo(map);

    let searchCircle = null;
    let meMarker = null;
    let accuracyCircle = null;

    function renderList(items){
        const container = document.getElementById('listContainer');
        document.getElementById('resultCount').innerText = (items.length || 0) + ' Restaurantes';
        if(!items || items.length === 0){ container.innerHTML = '<p class="text-sm text-stone-500">No se encontraron restaurantes.</p>'; return; }
        container.innerHTML = items.map(r => {
            const rating = r.avg_rating ? Number(r.avg_rating).toFixed(1) : '—';
            const price = r.avg_price ? ('Bs ' + Number(r.avg_price).toFixed(0)) : '—';
            const dist = r.distance ? (Number(r.distance).toFixed(2) + ' km') : '—';
            const foto = r.foto_portada ? ('/storage/' + r.foto_portada) : 'https://via.placeholder.com/900x600?text=Restaurante';
            return `
                <article class="group relative bg-surface-container-lowest rounded-2xl overflow-hidden shadow-sm hover:shadow-xl transition-all duration-300">
                    <div class="relative h-44 overflow-hidden">
                        <img class="w-full h-full object-cover" src="${foto}" alt="${r.nombre}">
                        <div class="absolute top-4 right-4 glass-card px-3 py-1.5 rounded-xl flex items-center gap-1.5"><span class="material-symbols-outlined text-yellow-500">star</span><span class="font-bold text-sm">${rating}</span></div>
                        <div class="absolute bottom-4 left-4 flex gap-2"><span class="bg-green-500/90 text-white text-[10px] font-bold uppercase tracking-widest px-2.5 py-1 rounded-md">${r.estado || 'Abierto'}</span><span class="bg-primary/90 text-white text-[10px] font-bold uppercase tracking-widest px-2.5 py-1 rounded-md">${dist}</span></div>
                    </div>
                    <div class="p-4">
                        <div class="flex justify-between items-start mb-1"><h3 class="font-headline text-lg font-extrabold">${r.nombre}</h3><span class="text-tertiary font-bold">${price}</span></div>
                        <p class="text-on-surface-variant text-sm mb-3">${(r.descripcion||'').substring(0,80)}</p>
                        <div class="flex items-center justify-between pt-2">
                            <button data-id="${r.id}" class="open-detail text-primary font-bold text-sm">Ver restaurante</button>
                            <span class="text-xs text-stone-500">${r.zona || ''}</span>
                        </div>
                    </div>
                </article>
            `;
        }).join('');

        // attach detail click
        container.querySelectorAll('.open-detail').forEach(btn => {
            btn.addEventListener('click', () => { window.location.href = detailBase + '/' + btn.dataset.id; });
        });
    }

    function renderMarkers(items){
        currentMarkers.clearLayers();
        items.forEach(r => {
            if(!r.latitud || !r.longitud) return;
            const foto = r.foto_portada ? ('/storage/' + r.foto_portada) : 'https://via.placeholder.com/900x600?text=Restaurante';
            const html = `<div class="restaurant-marker"><img src="${foto}" alt="${r.nombre}"/></div>`;
            const icon = L.divIcon({ html, className: '', iconSize: [48,48], iconAnchor: [24,24] });
            const m = L.marker([r.latitud, r.longitud], { icon });
            const popup = `<div class="font-bold">${r.nombre}</div><div class="text-sm">${(r.descripcion||'').substring(0,60)}...</div><div class="mt-2"><a href="${detailBase}/${r.id}" class="text-primary font-bold">Ver</a></div>`;
            m.bindPopup(popup);
            currentMarkers.addLayer(m);
        });
        const latlngs = items.filter(x=>x.latitud && x.longitud).map(x=>[x.latitud, x.longitud]);
        if(latlngs.length) map.fitBounds(latlngs, {padding: [60,60]});
    }

    async function fetchNearby(lat, lng){
        try{
            const resp = await fetch(`${nearbyUrl}?lat=${lat}&lng=${lng}`);
            if(!resp.ok) return;
            const json = await resp.json();
            restaurants = json.data || [];
            renderMarkers(restaurants);
            renderList(restaurants);
        }catch(e){ console.error(e); }
    }

    // Sorting/filter handlers
    document.querySelectorAll('.filter-pill').forEach(btn => {
        btn.addEventListener('click', () => {
            const sort = btn.dataset.sort;
            let items = [...restaurants];
            if(sort === 'distance') items.sort((a,b)=>(a.distance||0)-(b.distance||0));
            if(sort === 'rating') items.sort((a,b)=>(b.avg_rating||0)-(a.avg_rating||0));
            if(sort === 'price') items.sort((a,b)=>(a.avg_price||0)-(b.avg_price||0));
            renderMarkers(items);
            renderList(items);
        });
    });

    // Advanced filters (max distance) — live circle update
    document.getElementById('openAdvanced').addEventListener('click', ()=> document.getElementById('advancedFilters').classList.remove('hidden'));
    document.getElementById('closeAdvanced').addEventListener('click', ()=> document.getElementById('advancedFilters').classList.add('hidden'));

    maxDistanceInput.addEventListener('input', (e) => {
        const val = Number(e.target.value);
        maxDistanceValue.innerText = val + ' km';
        if(searchCircle) searchCircle.setRadius(val * 1000);
    });

    document.getElementById('applyAdvanced').addEventListener('click', ()=> {
        const maxD = Number(maxDistanceInput.value);
        const filtered = restaurants.filter(r => (r.distance || 99999) <= maxD);
        renderMarkers(filtered);
        renderList(filtered);
        document.getElementById('advancedFilters').classList.add('hidden');
    });

    // Initial load: try geolocation
    if(navigator.geolocation){
        navigator.geolocation.getCurrentPosition((pos)=>{
            const lat = pos.coords.latitude, lng = pos.coords.longitude;
            const accuracy = pos.coords.accuracy || 0;
            fetchNearby(lat, lng);

            // person marker (precise) with SVG
            const meSvg = `<svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="12" cy="12" r="12" fill="#9e2016"/><path d="M12 12a3 3 0 1 0 0-6 3 3 0 0 0 0 6z" fill="#fff"/><path d="M6 20c0-2.667 5.333-4 6-4s6 1.333 6 4v0H6z" fill="#fff"/></svg>`;
            const meIcon = L.divIcon({ html: `<div class="me-marker">${meSvg}</div>`, className:'', iconSize:[36,36], iconAnchor:[18,18] });
            if(meMarker) map.removeLayer(meMarker);
            meMarker = L.marker([lat,lng], { icon: meIcon }).addTo(map);

            // accuracy circle (thin) and search circle (red translucent)
            if(accuracy && accuracy > 0){
                if(accuracyCircle) map.removeLayer(accuracyCircle);
                accuracyCircle = L.circle([lat,lng], { radius: accuracy, color:'#1976d2', fillColor:'#1976d2', fillOpacity:0.06, weight:1 }).addTo(map);
            }

            const initialRadius = Number(maxDistanceInput.value || 5) * 1000;
            if(searchCircle) map.removeLayer(searchCircle);
            searchCircle = L.circle([lat,lng], { radius: initialRadius, color:'#9e2016', fillColor:'#9e2016', fillOpacity:0.12, weight:1, opacity:0.9 }).addTo(map);
            map.setView([lat,lng], 13);
        }, ()=>{
            renderMarkers(restaurants);
            renderList(restaurants);
        }, { enableHighAccuracy: true, timeout: 10000 });
    } else {
        renderMarkers(restaurants);
        renderList(restaurants);
    }

    // Global search
    document.getElementById('globalSearch').addEventListener('input', (e)=>{
        const q = e.target.value.toLowerCase().trim();
        const filtered = restaurants.filter(r => (r.nombre||'').toLowerCase().includes(q) || (r.descripcion||'').toLowerCase().includes(q));
        renderMarkers(filtered);
        renderList(filtered);
    });
});
</script>

@endsection
