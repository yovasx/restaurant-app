<?php $__env->startSection('title', 'Explorar - GastroGuía'); ?>
<?php $__env->startSection('layout-flush', 'true'); ?>

<?php $__env->startSection('content'); ?>
<!-- Leaflet CSS/JS (se cargan aqui) -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<?php
    $iconos = [
        'Pizza' => 'local_pizza', 'Sushi' => 'set_meal', 'Burgers' => 'lunch_dining',
        'Parrilla' => 'outdoor_grill', 'Comida Boliviana' => 'restaurant_menu',
        'Café y Brunch' => 'local_cafe', 'Pastas' => 'ramen_dining',
        'Pollos' => 'restaurant', 'Chifa' => 'takeout_dining',
        'Mariscos' => 'set_meal', 'Postres y Panadería' => 'cake',
        'Cocina Fusión' => 'menu_book',
    ];
    $activeCategoria = request('categoria');
    $searchQuery = request('q');
?>
 
<style>
    .restaurant-marker{ width:48px; height:48px; border-radius:50%; overflow:hidden; box-shadow:0 4px 10px rgba(0,0,0,0.2); border:2px solid #fff; }
    .restaurant-marker img{ width:100%; height:100%; object-fit:cover; display:block; }
    .me-marker{ width:36px; height:36px; border-radius:50%; display:flex; align-items:center; justify-content:center; box-shadow:0 3px 8px rgba(0,0,0,0.25); border:2px solid #fff; background:#9e2016; color:#fff; }
    .me-marker svg{ display:block; width:18px; height:18px; }

    /* Route styles */
    .route-panel { border-top: 2px solid #9e2016; }
    .route-panel-enter { animation: slideDown 0.2s ease-out; }
    @keyframes slideDown { from { max-height: 0; opacity: 0; } to { max-height: 300px; opacity: 1; } }
    #mobileRouteMap { min-height: 180px; }
    .route-step-icon { display:inline-flex; align-items:center; justify-content:center; width:2rem; height:2rem; border-radius:1rem; background:#fff; box-shadow:0 1px 3px rgba(0,0,0,0.1); }
    .route-btn-chip { display:inline-flex; align-items:center; gap:0.25rem; font-size:0.75rem; font-weight:700; color:#9e2016; }
    .route-btn-chip:hover { color:#7a1910; }
    .distance-label { background:#fff; border:1px solid #9e2016; color:#9e2016; font-size:11px; font-weight:800; padding:2px 8px; border-radius:999px; box-shadow:0 2px 6px rgba(0,0,0,0.1); white-space:nowrap; line-height:1.4; }
</style>

<!-- Top Navigation -->
<nav class="bg-surface/95 backdrop-blur-md shadow-sm flex justify-between items-center w-full px-6 py-3 sticky top-0 z-50 border-b border-stone-200/60">
    <div class="flex items-center gap-6">
        <div class="flex items-center gap-2">
            <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-primary text-white shadow-sm">
                <span class="material-symbols-outlined text-sm">restaurant_menu</span>
            </div>
            <span class="text-base font-black text-primary tracking-tighter font-headline hidden sm:block">GastroGuía</span>
        </div>
        <div class="hidden md:flex items-center gap-4 font-headline font-bold text-sm">
            <a class="text-stone-500 hover:text-primary transition-colors" href="<?php echo e(route('comensal.inicio')); ?>">Inicio</a>
            <a class="text-primary border-b-2 border-primary pb-1" href="<?php echo e(route('comensal.explorar')); ?>">Explorar</a>
            <a class="text-stone-500 hover:text-primary transition-colors" href="<?php echo e(route('comensal.perfil')); ?>">Perfil</a>
        </div>
    </div>
    <div class="flex items-center gap-3">
        <div class="hidden sm:flex items-center bg-stone-100 px-3 py-1.5 rounded-full gap-1.5 w-56">
            <span class="material-symbols-outlined text-stone-400 text-sm">search</span>
            <input id="globalSearch" class="bg-transparent border-none focus:ring-0 text-sm w-full placeholder:text-stone-400 outline-none" placeholder="Buscar en La Paz..." type="text" value="<?php echo e($searchQuery); ?>"/>
        </div>
        <button id="openFilters" class="material-symbols-outlined text-stone-500 p-1.5 hover:bg-stone-100 rounded-full">tune</button>
    </div>
</nav>

<main class="flex h-[calc(100vh-72px)] overflow-hidden">
    <!-- Left Side: List + Filters -->
    <section class="w-full md:w-[480px] lg:w-[560px] flex flex-col bg-surface-container-low overflow-hidden">
        <div class="px-6 pt-5 pb-3 bg-surface-container-low border-b border-outline-variant/20 space-y-3">
            <div class="flex items-end justify-between">
                <div>
                    <p class="text-primary font-bold text-xs tracking-widest uppercase mb-1">Cerca de ti</p>
                    <h2 class="font-headline text-2xl font-bold text-on-background">Explorar restaurantes</h2>
                </div>
                <span id="resultCount" class="bg-secondary-container/20 text-on-secondary-container px-3 py-1 rounded-full text-xs font-bold">-- Restaurantes</span>
            </div>

            <!-- sort pills -->
            <div class="flex gap-2 overflow-x-auto scrollbar-hide">
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

            <!-- quick filter toggles -->
            <div class="flex gap-2 overflow-x-auto scrollbar-hide">
                <button class="quick-filter flex items-center gap-1 bg-white border border-stone-200 text-stone-600 px-3 py-1.5 rounded-full text-xs font-bold whitespace-nowrap hover:bg-stone-50" data-filter="nearby">
                    <span class="material-symbols-outlined text-sm">near_me</span> Cerca de ti
                </button>
                <button class="quick-filter flex items-center gap-1 bg-white border border-stone-200 text-stone-600 px-3 py-1.5 rounded-full text-xs font-bold whitespace-nowrap hover:bg-stone-50" data-filter="openNow">
                    <span class="material-symbols-outlined text-sm">schedule</span> Abiertos ahora
                </button>
                <button class="quick-filter flex items-center gap-1 bg-white border border-stone-200 text-stone-600 px-3 py-1.5 rounded-full text-xs font-bold whitespace-nowrap hover:bg-stone-50" data-filter="economico">
                    <span class="material-symbols-outlined text-sm">payments</span> Económicos
                </button>
            </div>

            <!-- categories carousel -->
            <div class="flex gap-2 overflow-x-auto scrollbar-hide">
                <a href="<?php echo e(route('comensal.explorar')); ?>"
                   class="cat-chip flex items-center gap-1 px-3 py-1.5 rounded-full text-xs font-bold whitespace-nowrap <?php echo e(!$activeCategoria ? 'bg-primary text-white' : 'bg-white text-stone-600 border border-stone-200'); ?>">
                    <span class="material-symbols-outlined text-sm">restaurant</span> Todas
                </a>
                <?php $__currentLoopData = $categorias ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <a href="<?php echo e(route('comensal.explorar', ['categoria' => $cat->id, 'q' => $searchQuery])); ?>"
                       class="cat-chip flex items-center gap-1 px-3 py-1.5 rounded-full text-xs font-bold whitespace-nowrap <?php echo e(request('categoria') == $cat->id ? 'bg-primary text-white' : 'bg-white text-stone-600 border border-stone-200 hover:bg-stone-50'); ?>">
                        <span class="material-symbols-outlined text-sm"><?php echo e($iconos[$cat->nombre_categoria] ?? 'restaurant'); ?></span>
                        <?php echo e($cat->nombre_categoria); ?>

                    </a>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>

            <!-- active filter chips -->
            <div id="activeFilterChips" class="hidden flex gap-2 flex-wrap items-center">
                <span class="text-[10px] font-bold text-stone-400 uppercase tracking-wider">Filtros:</span>
                <div id="filterChipsList" class="flex gap-1.5 flex-wrap"></div>
                <button id="clearFilters" class="text-[10px] font-bold text-primary hover:underline hidden">Limpiar todo</button>
            </div>
        </div>

        <!-- Desktop route panel -->
        <div id="desktopRoutePanel" class="hidden route-panel bg-white px-6 py-4 route-panel-enter">
            <div class="flex items-center justify-between mb-2">
                <div>
                    <div class="font-headline text-base font-extrabold" id="routeRestaurantName">Cómo llegar</div>
                    <div class="text-xs text-stone-500" id="routeStatus">Selecciona un restaurante</div>
                </div>
                <button id="closeRoutePanel" class="material-symbols-outlined text-stone-400 hover:text-stone-600">close</button>
            </div>
            <div class="flex gap-2 mb-2">
                <button class="route-mode-btn bg-primary text-white px-3 py-1.5 rounded-full text-xs font-bold" data-mode="walking">A pie</button>
                <button class="route-mode-btn bg-stone-100 text-stone-600 px-3 py-1.5 rounded-full text-xs font-bold" data-mode="driving">En auto</button>
            </div>
            <div id="routeSummary" class="hidden flex gap-4 text-sm mb-3">
                <span>Distancia: <strong id="routeDistance">--</strong></span>
                <span>Duración: <strong id="routeDuration">--</strong></span>
            </div>
            <div class="flex gap-3 mb-2 hidden" id="routeActions">
                <button id="refreshRoute" class="text-xs text-primary font-bold">Actualizar ruta</button>
                <button id="recenterRoute" class="text-xs text-primary font-bold">Recentrar mapa</button>
            </div>
            <details class="text-sm">
                <summary class="text-primary font-bold cursor-pointer">Ver indicaciones</summary>
                <ol id="routeStepsList" class="mt-2 space-y-2"></ol>
                <p id="routeStepsEmpty" class="text-stone-500 hidden mt-2">No hay pasos disponibles.</p>
            </details>
        </div>

        <div id="listContainer" class="flex-1 overflow-y-auto p-6 space-y-6">
            <!-- Las tarjetas se inyectan aquí -->
        </div>
    </section>

    <!-- Right Side: Map -->
    <section class="hidden md:flex flex-1 relative bg-surface-container-highest">
        <div id="map" class="absolute inset-0"></div>
    </section>
</main>

<!-- filtros avanzados -->
<div id="advancedFilters" class="fixed inset-0 bg-black/40 hidden items-center justify-center z-50">
    <div class="bg-white rounded-xl p-6 w-11/12 max-w-md max-h-[90vh] overflow-y-auto">
        <h3 class="font-headline font-extrabold text-lg mb-1">Filtros avanzados</h3>
        <p class="text-xs text-stone-500 mb-4">Ajusta los criterios para encontrar los mejores restaurantes.</p>

        <div class="space-y-4">
            <div>
                <label class="text-sm font-bold">Distancia máxima</label>
                <input id="advMaxDistance" type="range" min="0.5" max="50" step="0.5" value="5" class="w-full" />
                <div class="flex justify-between text-xs text-stone-500 mt-1"><span>0.5 km</span><span id="advMaxDistanceValue">5 km</span><span>50 km</span></div>
            </div>

            <div>
                <label class="text-sm font-bold">Zona</label>
                <select id="advZone" class="w-full bg-stone-100 border-0 rounded-lg p-2.5 text-sm mt-1">
                    <option value="">Todas las zonas</option>
                    <option value="Centro">Centro</option>
                    <option value="Sopocachi">Sopocachi</option>
                    <option value="Zona Sur">Zona Sur</option>
                    <option value="Calacoto">Calacoto</option>
                    <option value="Miraflores">Miraflores</option>
                    <option value="Obrajes">Obrajes</option>
                    <option value="San Pedro">San Pedro</option>
                </select>
            </div>

            <div>
                <label class="text-sm font-bold">Calificación mínima</label>
                <div class="flex gap-2 mt-1">
                    <?php for($i = 1; $i <= 5; $i++): ?>
                        <button class="rating-star-btn w-9 h-9 rounded-full border border-stone-200 text-xs font-bold text-stone-500 hover:bg-amber-50 hover:border-amber-300" data-rating="<?php echo e($i); ?>"><?php echo e($i); ?>+</button>
                    <?php endfor; ?>
                </div>
            </div>

            <div>
                <label class="text-sm font-bold">Rango de precio</label>
                <div class="flex gap-3 mt-1">
                    <label class="flex items-center gap-2 bg-stone-100 px-3 py-2 rounded-lg text-xs"><input type="radio" name="advPrice" value="" checked class="accent-primary"/> Cualquier precio</label>
                    <label class="flex items-center gap-2 bg-stone-100 px-3 py-2 rounded-lg text-xs"><input type="radio" name="advPrice" value="bajo" class="accent-primary"/> Bajo</label>
                    <label class="flex items-center gap-2 bg-stone-100 px-3 py-2 rounded-lg text-xs"><input type="radio" name="advPrice" value="medio" class="accent-primary"/> Medio</label>
                    <label class="flex items-center gap-2 bg-stone-100 px-3 py-2 rounded-lg text-xs"><input type="radio" name="advPrice" value="alto" class="accent-primary"/> Alto</label>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <input id="advOpenNow" type="checkbox" class="w-5 h-5 rounded border-stone-300 text-primary focus:ring-primary" />
                <label for="advOpenNow" class="text-sm font-bold">Solo abiertos ahora</label>
            </div>
        </div>

        <div class="mt-6 flex justify-end gap-2">
            <button id="clearAdvanced" class="px-4 py-2 rounded-lg bg-stone-100 text-sm font-bold">Limpiar</button>
            <button id="applyAdvanced" class="px-4 py-2 rounded-lg bg-[#9e2016] text-white text-sm font-bold">Aplicar filtros</button>
        </div>
    </div>
</div>

<!-- Mobile route modal -->
<div id="exploreRouteModal" class="fixed inset-0 z-[80] hidden md:hidden" data-open="false">
    <div class="absolute inset-0 bg-stone-950/55 backdrop-blur-[2px]" data-close-route-modal></div>
    <div class="relative w-full h-[80vh] bg-white rounded-t-[1.75rem] border border-stone-200 overflow-hidden flex flex-col">
        <div class="flex items-center justify-between px-5 pt-4 pb-2 border-b border-stone-100">
            <div>
                <h3 class="font-headline text-lg font-extrabold text-on-surface" id="mobileRouteTitle">Cómo llegar</h3>
                <p class="text-xs text-stone-500" id="mobileRouteStatus">Selecciona un restaurante</p>
            </div>
            <button data-close-route-modal class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-stone-100 text-stone-500 transition hover:bg-stone-200">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        <div class="flex-1 min-h-0 flex flex-col">
            <div id="mobileRouteMap" class="h-48 shrink-0 bg-stone-100"></div>
            <div class="p-4 flex-1 overflow-y-auto">
                <div class="flex gap-2 mb-3">
                    <button class="route-mode-btn-mobile bg-primary text-white px-3 py-1.5 rounded-full text-xs font-bold" data-mode="walking">A pie</button>
                    <button class="route-mode-btn-mobile bg-stone-100 text-stone-600 px-3 py-1.5 rounded-full text-xs font-bold" data-mode="driving">En auto</button>
                </div>
                <div id="mobileRouteSummary" class="hidden flex gap-4 text-sm mb-3">
                    <span>Distancia: <strong id="mobileRouteDistance">--</strong></span>
                    <span>Duración: <strong id="mobileRouteDuration">--</strong></span>
                </div>
                <div class="flex gap-3 mb-3 hidden" id="mobileRouteActions">
                    <button id="refreshRouteMobile" class="text-xs text-primary font-bold">Actualizar ruta</button>
                    <button id="recenterRouteMobile" class="text-xs text-primary font-bold">Recentrar mapa</button>
                </div>
                <details class="text-sm">
                    <summary class="text-primary font-bold cursor-pointer">Ver indicaciones</summary>
                    <ol id="mobileRouteStepsList" class="mt-2 space-y-2"></ol>
                    <p id="mobileRouteStepsEmpty" class="text-stone-500 hidden mt-2">No hay pasos disponibles.</p>
                </details>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function(){
    const nearbyUrl = '<?php echo e(route('restaurantes.nearby')); ?>?ajax=1';
    const detailBase = '<?php echo e(url('restaurante')); ?>';
    const allRestaurants = <?php echo json_encode($restaurants ?? [], 15, 512) ?>;
    const categoriasData = <?php echo json_encode($categorias ?? [], 15, 512) ?>;
    let nearbyRestaurants = [];
    let visibleRestaurants = [];
    let currentMarkers = L.layerGroup();

    // Route state (unchanged)
    let activeRouteRestaurant = null;
    let desktopRouteLayer = null;
    let routeRequestController = null;
    let userLatLng = null;
    let mobileRouteMap = null;
    let mobileRouteLayer = null;
    let routeMode = 'walking';
    let isRouteActive = false;

    // Filter state
    let filterState = {
        query: '<?php echo e($searchQuery); ?>' || '',
        sort: 'distance',
        useNearby: false,
        openNow: false,
        maxDistance: 5,
        zone: '',
        ratingMin: 0,
        priceRange: 'all',
        categoria: '<?php echo e($activeCategoria); ?>' || '',
    };
    let hasNearbyResults = false;

    // Elementos
    const maxDistanceInput = document.getElementById('maxDistance');
    const maxDistanceValue = document.getElementById('maxDistanceValue');

    // Inicializar mapa
    const defaultLat = -16.489689, defaultLng = -68.119294;
    const map = L.map('map', {
        zoomControl: true,
        scrollWheelZoom: true,
        dragging: true,
        doubleClickZoom: true,
        touchZoom: true,
        boxZoom: true,
        keyboard: true,
    }).setView([defaultLat, defaultLng], 13);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { attribution: '&copy; OpenStreetMap contributors' }).addTo(map);
    currentMarkers.addTo(map);

    let searchCircle = null;
    let distanceLabel = null;
    let meMarker = null;
    let accuracyCircle = null;

    function renderList(items){
        const container = document.getElementById('listContainer');
        document.getElementById('resultCount').innerText = (items.length || 0) + ' Restaurantes';
        if(!items || items.length === 0){ container.innerHTML = '<p class="text-sm text-stone-500">No se encontraron restaurantes.</p>'; return; }
        container.innerHTML = items.map(r => {
            const rating = r.avg_rating ? Number(r.avg_rating).toFixed(1) : '—';
            const dist = r.distance ? (Number(r.distance).toFixed(2) + ' km') : '—';
            const foto = r.foto_portada_url || 'https://via.placeholder.com/900x600?text=Restaurante';
            return `
                <article class="group relative bg-surface-container-lowest rounded-2xl overflow-hidden shadow-sm hover:shadow-xl transition-all duration-300">
                    <div class="relative h-44 overflow-hidden">
                        <img class="w-full h-full object-cover" src="${foto}" alt="${r.nombre}">
                        <div class="absolute top-4 right-4 glass-card px-3 py-1.5 rounded-xl flex items-center gap-1.5"><span class="material-symbols-outlined text-yellow-500">star</span><span class="font-bold text-sm">${rating}</span></div>
                        <div class="absolute bottom-4 left-4 flex gap-2"><span class="${isOpenNow(r) ? 'bg-green-500/90' : 'bg-stone-400/90'} text-white text-[10px] font-bold uppercase tracking-widest px-2.5 py-1 rounded-md">${isOpenNow(r) ? 'Abierto ahora' : 'Cerrado'}</span><span class="bg-primary/90 text-white text-[10px] font-bold uppercase tracking-widest px-2.5 py-1 rounded-md">${dist}</span></div>
                    </div>
                    <div class="p-4">
                        <div class="mb-1"><h3 class="font-headline text-lg font-extrabold">${r.nombre}</h3></div>
                        <p class="text-on-surface-variant text-sm mb-3">${(r.descripcion||'').substring(0,80)}</p>
                        <div class="flex items-center justify-between pt-2">
                            <div class="flex gap-3">
                                <button data-id="${r.id}" class="open-detail text-primary font-bold text-sm">Ver restaurante</button>
                                ${(r.latitud && r.longitud) ? `<button data-id="${r.id}" class="open-route route-btn-chip"><span class="material-symbols-outlined text-sm">directions</span> Cómo llegar</button>` : ''}
                            </div>
                            <span class="text-xs text-stone-500">${r.zona || ''}</span>
                        </div>
                    </div>
                </article>
            `;
        }).join('');

        container.querySelectorAll('.open-detail').forEach(btn => {
            btn.addEventListener('click', () => { window.location.href = detailBase + '/' + btn.dataset.id; });
        });
    }

    function renderMarkers(items){
        currentMarkers.clearLayers();
        items.forEach(r => {
            if(!r.latitud || !r.longitud) return;
            const foto = r.foto_portada_url || 'https://via.placeholder.com/900x600?text=Restaurante';
            const html = `<div class="restaurant-marker"><img src="${foto}" alt="${r.nombre}"/></div>`;
            const icon = L.divIcon({ html, className: '', iconSize: [48,48], iconAnchor: [24,24] });
            const m = L.marker([r.latitud, r.longitud], { icon });
            const routeBtnHtml = (r.latitud && r.longitud) ? `<button onclick="window.dispatchEvent(new CustomEvent('open-route',{detail:{id:${r.id}}}))" class="route-btn-chip"><span class="material-symbols-outlined text-sm">directions</span> Cómo llegar</button>` : '';
            const popup = `<div class="font-bold">${r.nombre}</div><div class="text-sm">${(r.descripcion||'').substring(0,60)}...</div><div class="mt-2 flex gap-3"><a href="${detailBase}/${r.id}" class="text-primary font-bold">Ver</a>${routeBtnHtml}</div>`;
            m.bindPopup(popup);
            currentMarkers.addLayer(m);
        });
        const latlngs = items.filter(x=>x.latitud && x.longitud).map(x=>[x.latitud, x.longitud]);
        if(latlngs.length && !isRouteActive) map.fitBounds(latlngs, {padding: [60,60]});
    }

    // --- Filter pipeline ---

    function isOpenNow(r) {
        const now = new Date();
        const d = now.getDay();
        const t = now.getHours() * 60 + now.getMinutes();
        let open, close;
        if (d === 0) { open = r.hora_apertura_domingo; close = r.hora_cierre_domingo; }
        else if (d === 6) { open = r.hora_apertura_sabado; close = r.hora_cierre_sabado; }
        else { open = r.horario_apertura; close = r.horario_cierre; }
        if (!open || !close) return false;
        const [oh, om] = open.split(':').map(Number);
        const [ch, cm] = close.split(':').map(Number);
        const oM = oh*60+om, cM = ch*60+cm;
        return cM < oM ? (t >= oM || t <= cM) : (t >= oM && t <= cM);
    }

    function matchesPriceRange(r, range) {
        const p = r.avg_price || 0;
        if (range === 'bajo') return p <= 30;
        if (range === 'medio') return p > 30 && p <= 80;
        if (range === 'alto') return p > 80;
        return true;
    }

    function applyFiltersAndRender() {
        let source = (filterState.useNearby && hasNearbyResults) ? nearbyRestaurants : allRestaurants;

        if (filterState.query) {
            const q = filterState.query.toLowerCase();
            source = source.filter(r =>
                (r.nombre||'').toLowerCase().includes(q) ||
                (r.descripcion||'').toLowerCase().includes(q) ||
                (r.zona||'').toLowerCase().includes(q) ||
                (r.categorias||[]).some(c => (c.nombre_categoria||'').toLowerCase().includes(q))
            );
        }

        if (filterState.zone) {
            source = source.filter(r => (r.zona||'').toLowerCase() === filterState.zone.toLowerCase());
        }

        if (filterState.categoria) {
            const catId = String(filterState.categoria);
            source = source.filter(r => (r.categorias||[]).some(c => String(c.id) === catId));
        }

        if (filterState.openNow) {
            source = source.filter(r => isOpenNow(r));
        }

        if (filterState.ratingMin > 0) {
            source = source.filter(r => (r.avg_rating||0) >= filterState.ratingMin);
        }

        if (filterState.priceRange !== 'all') {
            source = source.filter(r => matchesPriceRange(r, filterState.priceRange));
        }

        if (filterState.useNearby && hasNearbyResults) {
            source = source.filter(r => (r.distance||999) <= filterState.maxDistance);
        }

        if (filterState.sort === 'distance') {
            source.sort((a,b) => (a.distance||0) - (b.distance||0));
        } else if (filterState.sort === 'rating') {
            source.sort((a,b) => (b.avg_rating||0) - (a.avg_rating||0));
        } else if (filterState.sort === 'price') {
            source.sort((a,b) => (a.avg_price||0) - (b.avg_price||0));
        }

        visibleRestaurants = source;
        renderMarkers(visibleRestaurants);
        renderList(visibleRestaurants);
        updateActiveChips();
        syncDistanceOverlay();
    }

    function updateActiveChips() {
        const chips = [];
        if (filterState.useNearby) chips.push({key:'nearby',label:'Cerca de ti'});
        if (filterState.openNow) chips.push({key:'openNow',label:'Abiertos ahora'});
        if (filterState.zone) chips.push({key:'zone',label:'Zona: '+filterState.zone});
        if (filterState.ratingMin > 0) chips.push({key:'rating',label:filterState.ratingMin+'+ estrellas'});
        if (filterState.priceRange !== 'all') {
            const lbl = {bajo:'Económicos',medio:'Precio medio',alto:'Precio alto'};
            chips.push({key:'price',label: lbl[filterState.priceRange]||filterState.priceRange});
        }
        const container = document.getElementById('activeFilterChips');
        const list = document.getElementById('filterChipsList');
        const clearBtn = document.getElementById('clearFilters');
        list.innerHTML = chips.map(c =>
            `<button class="inline-flex items-center gap-1 bg-primary/10 text-primary px-2 py-0.5 rounded-full text-[10px] font-bold" data-chip="${c.key}">
                ${c.label} <span class="material-symbols-outlined text-xs">close</span>
            </button>`
        ).join('');
        if (chips.length > 0) {
            container.classList.remove('hidden');
            clearBtn.classList.remove('hidden');
        } else {
            container.classList.add('hidden');
            clearBtn.classList.add('hidden');
        }
        // Remove chip on click
        list.querySelectorAll('[data-chip]').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const key = btn.dataset.chip;
                if (key === 'nearby') filterState.useNearby = false;
                else if (key === 'openNow') filterState.openNow = false;
                else if (key === 'zone') filterState.zone = '';
                else if (key === 'rating') filterState.ratingMin = 0;
                else if (key === 'price') filterState.priceRange = 'all';
                applyFiltersAndRender();
                syncQuickFilterUI();
                syncRatingStarsUI();
                syncAdvForm();
            });
        });
    }

    function syncQuickFilterUI() {
        document.querySelectorAll('.quick-filter').forEach(btn => {
            const f = btn.dataset.filter;
            const isActive = (f === 'nearby' && filterState.useNearby) ||
                            (f === 'openNow' && filterState.openNow) ||
                            (f === 'economico' && filterState.priceRange === 'bajo');
            if (isActive) {
                btn.dataset.active = 'true';
                btn.classList.remove('bg-white','border-stone-200','text-stone-600','hover:bg-stone-50');
                btn.classList.add('bg-primary','text-white');
            } else {
                btn.dataset.active = 'false';
                btn.classList.remove('bg-primary','text-white');
                btn.classList.add('bg-white','border-stone-200','text-stone-600','hover:bg-stone-50');
            }
        });
    }

    function syncRatingStarsUI() {
        document.querySelectorAll('.rating-star-btn').forEach(btn => {
            const val = Number(btn.dataset.rating);
            if (filterState.ratingMin >= val) {
                btn.classList.add('bg-amber-100','border-amber-400','text-amber-700');
                btn.classList.remove('bg-white','border-stone-200','text-stone-500');
            } else {
                btn.classList.remove('bg-amber-100','border-amber-400','text-amber-700');
                btn.classList.add('bg-white','border-stone-200','text-stone-500');
            }
        });
    }

    function syncAdvForm() {
        document.getElementById('advMaxDistance').value = filterState.maxDistance;
        document.getElementById('advMaxDistanceValue').textContent = filterState.maxDistance + ' km';
        document.getElementById('advZone').value = filterState.zone;
        document.getElementById('advOpenNow').checked = filterState.openNow;
        document.querySelectorAll('input[name="advPrice"]').forEach(r => {
            r.checked = r.value === filterState.priceRange;
        });
    }

    // --- Nearby ---

    async function fetchNearby(lat, lng){
        try{
            const resp = await fetch(`${nearbyUrl}&lat=${lat}&lng=${lng}`);
            if(!resp.ok) return;
            const json = await resp.json();
            const data = json.data || [];
            if (data.length > 0) {
                nearbyRestaurants = data;
                hasNearbyResults = true;
            } else {
                nearbyRestaurants = [];
                hasNearbyResults = false;
            }
            if (filterState.useNearby) {
                applyFiltersAndRender();
            }
        }catch(e){ console.error(e); }
    }

    // --- Route functions ---

    function startRouteForRestaurant(id) {
        const restaurant = (allRestaurants.concat(nearbyRestaurants)).find(r => r.id === id);
        if (!restaurant || !restaurant.latitud || !restaurant.longitud) return;

        activeRouteRestaurant = restaurant;
        isRouteActive = true;

        if (window.innerWidth < 768) {
            openMobileRoute();
        } else {
            showDesktopRoute();
        }

        if (userLatLng) {
            calculateRoute(userLatLng.lat, userLatLng.lng);
        } else {
            requestUserLocationForRoute();
        }
    }

    function showDesktopRoute() {
        const r = activeRouteRestaurant;
        document.getElementById('routeRestaurantName').textContent = r.nombre + ' - Cómo llegar';
        document.getElementById('routeStatus').textContent = 'Calculando ruta\u2026';
        document.getElementById('desktopRoutePanel').classList.remove('hidden');
    }

    function openMobileRoute() {
        const r = activeRouteRestaurant;
        document.getElementById('mobileRouteTitle').textContent = 'C\u00f3mo llegar a ' + r.nombre;
        document.getElementById('mobileRouteStatus').textContent = 'Calculando ruta\u2026';

        const modal = document.getElementById('exploreRouteModal');
        modal.classList.remove('hidden');
        modal.dataset.open = 'true';

        if (!mobileRouteMap) {
            mobileRouteMap = L.map('mobileRouteMap', {
                zoomControl: true,
                scrollWheelZoom: true,
                dragging: true,
                doubleClickZoom: true,
                touchZoom: true,
                boxZoom: true,
                keyboard: true,
            }).setView([defaultLat, defaultLng], 13);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { attribution: '&copy; OpenStreetMap contributors' }).addTo(mobileRouteMap);
            mobileRouteLayer = L.layerGroup().addTo(mobileRouteMap);
        }

        setTimeout(() => { mobileRouteMap.invalidateSize(); }, 300);
    }

    function closeRoute() {
        isRouteActive = false;
        activeRouteRestaurant = null;

        document.getElementById('desktopRoutePanel').classList.add('hidden');
        const modal = document.getElementById('exploreRouteModal');
        modal.classList.add('hidden');
        modal.dataset.open = 'false';

        if (desktopRouteLayer) {
            map.removeLayer(desktopRouteLayer);
            desktopRouteLayer = null;
        }

        if (mobileRouteLayer) {
            mobileRouteLayer.clearLayers();
        }

        document.getElementById('routeSummary').classList.add('hidden');
        document.getElementById('routeActions').classList.add('hidden');
        document.getElementById('mobileRouteSummary').classList.add('hidden');
        document.getElementById('mobileRouteActions').classList.add('hidden');
        document.getElementById('routeStatus').textContent = 'Selecciona un restaurante';
        document.getElementById('mobileRouteStatus').textContent = 'Selecciona un restaurante';

        const all = (visibleRestaurants.length > 0 ? visibleRestaurants : allRestaurants);
        const latlngs = all.filter(x=>x.latitud && x.longitud).map(x=>[x.latitud, x.longitud]);
        if (latlngs.length) map.fitBounds(latlngs, {padding: [60,60]});
    }

    function requestUserLocationForRoute() {
        if (!navigator.geolocation) {
            setRouteStatus('Tu navegador no soporta geolocalizaci\u00f3n.');
            return;
        }

        setRouteStatus('Buscando tu ubicaci\u00f3n\u2026');

        navigator.geolocation.getCurrentPosition((position) => {
            userLatLng = { lat: position.coords.latitude, lng: position.coords.longitude };

            const meSvg = '<svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="12" cy="12" r="12" fill="#9e2016"/><path d="M12 12a3 3 0 1 0 0-6 3 3 0 0 0 0 6z" fill="#fff"/><path d="M6 20c0-2.667 5.333-4 6-4s6 1.333 6 4v0H6z" fill="#fff"/></svg>';
            const meIcon = L.divIcon({ html: '<div class="me-marker">' + meSvg + '</div>', className: '', iconSize: [36,36], iconAnchor: [18,18] });
            if (meMarker) map.removeLayer(meMarker);
            meMarker = L.marker([userLatLng.lat, userLatLng.lng], { icon: meIcon }).addTo(map);

            calculateRoute(userLatLng.lat, userLatLng.lng);
        }, (error) => {
            setRouteStatus(geolocationErrorMessage(error));
        }, { enableHighAccuracy: true, timeout: 10000, maximumAge: 30000 });
    }

    function setRouteStatus(text) {
        document.getElementById('routeStatus').textContent = text;
        document.getElementById('mobileRouteStatus').textContent = text;
    }

    async function calculateRoute(originLat, originLng) {
        if (routeRequestController) {
            routeRequestController.abort();
        }

        if (!activeRouteRestaurant) return;

        routeRequestController = new AbortController();
        setRouteStatus('Calculando ruta\u2026');

        try {
            const profile = routeMode === 'driving' ? 'driving' : 'walking';
            const url = 'https://router.project-osrm.org/route/v1/' + profile + '/' + originLng + ',' + originLat + ';' + activeRouteRestaurant.longitud + ',' + activeRouteRestaurant.latitud + '?overview=full&geometries=geojson&steps=true&alternatives=false';
            const response = await fetch(url, { signal: routeRequestController.signal });

            if (!response.ok) throw new Error('route_request_failed');

            const data = await response.json();
            const route = data.routes && data.routes[0] ? data.routes[0] : null;

            if (!route || !route.geometry || !Array.isArray(route.geometry.coordinates) || route.geometry.coordinates.length === 0) {
                throw new Error('route_not_found');
            }

            drawRoute(route.geometry.coordinates);
            renderRouteSummary(route.distance, route.duration);
            renderRouteSteps(route.legs || []);
            renderRouteStepsMobile(route.legs || []);

            setRouteStatus('Ruta lista');
            document.getElementById('routeActions').classList.remove('hidden');
            document.getElementById('routeSummary').classList.remove('hidden');
            document.getElementById('mobileRouteActions').classList.remove('hidden');
            document.getElementById('mobileRouteSummary').classList.remove('hidden');
        } catch (error) {
            if (error.name === 'AbortError') return;
            renderRouteSummary(null, null);
            renderRouteSteps([]);
            renderRouteStepsMobile([]);
            setRouteStatus('No se pudo calcular la ruta. Intenta nuevamente.');
        }
    }

    function drawRoute(coordinates) {
        const latLngs = coordinates.map((point) => [point[1], point[0]]);

        if (desktopRouteLayer) {
            map.removeLayer(desktopRouteLayer);
        }

        desktopRouteLayer = L.polyline(latLngs, {
            color: '#9e2016',
            weight: 6,
            opacity: 0.9,
            lineCap: 'round',
            lineJoin: 'round',
        }).addTo(map);

        recenterRoute();

        if (mobileRouteLayer) {
            mobileRouteLayer.clearLayers();
            const mobileLine = L.polyline(latLngs, {
                color: '#9e2016',
                weight: 6,
                opacity: 0.9,
                lineCap: 'round',
                lineJoin: 'round',
            });
            mobileRouteLayer.addLayer(mobileLine);
            recenterMobileRoute();
        }
    }

    function recenterRoute() {
        if (!activeRouteRestaurant) return;
        const bounds = L.latLngBounds([[activeRouteRestaurant.latitud, activeRouteRestaurant.longitud]]);
        if (userLatLng) {
            bounds.extend([userLatLng.lat, userLatLng.lng]);
        }
        if (desktopRouteLayer) {
            bounds.extend(desktopRouteLayer.getBounds());
        }
        map.fitBounds(bounds.pad(0.18), { animate: true });
    }

    function recenterMobileRoute() {
        if (!activeRouteRestaurant || !mobileRouteMap) return;
        const bounds = L.latLngBounds([[activeRouteRestaurant.latitud, activeRouteRestaurant.longitud]]);
        if (userLatLng) {
            bounds.extend([userLatLng.lat, userLatLng.lng]);
        }
        if (mobileRouteLayer) {
            const lb = mobileRouteLayer.getBounds();
            if (lb.isValid()) bounds.extend(lb);
        }
        mobileRouteMap.fitBounds(bounds.pad(0.18), { animate: true });
    }

    function renderRouteSummary(distance, duration) {
        document.getElementById('routeDistance').textContent = formatDistance(distance);
        document.getElementById('routeDuration').textContent = formatDuration(duration);
        document.getElementById('mobileRouteDistance').textContent = formatDistance(distance);
        document.getElementById('mobileRouteDuration').textContent = formatDuration(duration);
    }

    function renderRouteSteps(legs) {
        const list = document.getElementById('routeStepsList');
        const empty = document.getElementById('routeStepsEmpty');
        const steps = legs.flatMap((leg) => Array.isArray(leg.steps) ? leg.steps : []);

        list.innerHTML = '';
        if (!steps.length) {
            list.classList.add('hidden');
            empty.classList.remove('hidden');
            return;
        }
        empty.classList.add('hidden');
        list.classList.remove('hidden');

        steps.forEach((step, index) => {
            const item = document.createElement('li');
            item.className = 'rounded-2xl border border-stone-200 bg-stone-50 px-4 py-3 text-sm';
            const instruction = describeStep(step, activeRouteRestaurant ? activeRouteRestaurant.nombre : '', index === steps.length - 1);
            const distanceLabel = formatDistance(step.distance);
            item.innerHTML = '<div class="flex items-start gap-3"><div class="mt-0.5 flex h-8 w-8 shrink-0 items-center justify-center rounded-2xl bg-white text-[#9e2016] shadow-sm"><span class="material-symbols-outlined text-lg">' + stepIcon(step) + '</span></div><div class="min-w-0 flex-1"><p class="text-sm font-bold leading-6">' + escapeHtml(instruction) + '</p><p class="mt-1 text-xs font-semibold uppercase tracking-[0.2em] text-stone-500">Tramo: ' + escapeHtml(distanceLabel) + '</p></div></div>';
            list.appendChild(item);
        });
    }

    function renderRouteStepsMobile(legs) {
        const list = document.getElementById('mobileRouteStepsList');
        const empty = document.getElementById('mobileRouteStepsEmpty');
        const steps = legs.flatMap((leg) => Array.isArray(leg.steps) ? leg.steps : []);

        list.innerHTML = '';
        if (!steps.length) {
            list.classList.add('hidden');
            empty.classList.remove('hidden');
            return;
        }
        empty.classList.add('hidden');
        list.classList.remove('hidden');

        steps.forEach((step, index) => {
            const item = document.createElement('li');
            item.className = 'rounded-2xl border border-stone-200 bg-stone-50 px-4 py-3 text-sm';
            const instruction = describeStep(step, activeRouteRestaurant ? activeRouteRestaurant.nombre : '', index === steps.length - 1);
            const distanceLabel = formatDistance(step.distance);
            item.innerHTML = '<div class="flex items-start gap-3"><div class="mt-0.5 flex h-8 w-8 shrink-0 items-center justify-center rounded-2xl bg-white text-[#9e2016] shadow-sm"><span class="material-symbols-outlined text-lg">' + stepIcon(step) + '</span></div><div class="min-w-0 flex-1"><p class="text-sm font-bold leading-6">' + escapeHtml(instruction) + '</p><p class="mt-1 text-xs font-semibold uppercase tracking-[0.2em] text-stone-500">Tramo: ' + escapeHtml(distanceLabel) + '</p></div></div>';
            list.appendChild(item);
        });
    }

    function describeStep(step, restaurantName, isLastStep) {
        const maneuver = step.maneuver || {};
        const modifier = maneuver.modifier || '';
        const type = maneuver.type || '';
        const roadName = step.name ? ' por ' + step.name : '';

        if (isLastStep || type === 'arrive') {
            return 'Has llegado a ' + (restaurantName || 'tu destino') + '.';
        }

        if (type === 'depart') {
            return 'Sal desde tu ubicaci\u00f3n y contin\u00faa' + roadName + '.';
        }

        if (type === 'roundabout' || type === 'rotary' || type === 'roundabout turn') {
            return 'En la rotonda, toma la salida indicada y contin\u00faa' + roadName + '.';
        }

        if (type === 'merge') {
            return 'Incorp\u00f3rate y sigue' + roadName + '.';
        }

        if (type === 'fork') {
            return 'Mantente en la bifurcaci\u00f3n y contin\u00faa' + roadName + '.';
        }

        if (type === 'end of road') {
            return 'Al final de la v\u00eda, ' + lowerFirst(turnInstruction(modifier, roadName));
        }

        if (type === 'continue' || type === 'new name') {
            return 'Contin\u00faa recto' + roadName + '.';
        }

        if (type === 'turn' || type === 'on ramp' || type === 'off ramp' || type === 'use lane') {
            return turnInstruction(modifier, roadName);
        }

        return 'Sigue el camino indicado' + roadName + '.';
    }

    function turnInstruction(modifier, roadName) {
        const suffix = roadName ? roadName + '.' : '.';
        switch (modifier) {
            case 'left': return 'Gira a la izquierda' + suffix;
            case 'right': return 'Gira a la derecha' + suffix;
            case 'slight left': return 'Gira levemente a la izquierda' + suffix;
            case 'slight right': return 'Gira levemente a la derecha' + suffix;
            case 'sharp left': return 'Gira pronunciadamente a la izquierda' + suffix;
            case 'sharp right': return 'Gira pronunciadamente a la derecha' + suffix;
            case 'uturn': return 'Haz un giro en U' + suffix;
            default: return 'Contin\u00faa hacia adelante' + suffix;
        }
    }

    function stepIcon(step) {
        const maneuver = step.maneuver || {};
        const type = maneuver.type || '';
        const modifier = maneuver.modifier || '';
        if (type === 'arrive') return 'flag';
        if (type === 'roundabout' || type === 'rotary' || type === 'roundabout turn') return 'sync';
        if (modifier === 'left' || modifier === 'slight left' || modifier === 'sharp left') return 'turn_left';
        if (modifier === 'right' || modifier === 'slight right' || modifier === 'sharp right') return 'turn_right';
        if (modifier === 'uturn') return 'u_turn_left';
        return type === 'depart' ? 'my_location' : 'straight';
    }

    function formatDistance(distance) {
        if (distance === null || distance === undefined || Number.isNaN(distance)) return '--';
        if (distance < 1000) return Math.round(distance) + ' m';
        return (distance / 1000).toFixed(distance >= 10000 ? 0 : 1) + ' km';
    }

    function formatDuration(duration) {
        if (duration === null || duration === undefined || Number.isNaN(duration)) return '--';
        const minutes = Math.round(duration / 60);
        if (minutes < 60) return minutes + ' min';
        const hours = Math.floor(minutes / 60);
        const remainingMinutes = minutes % 60;
        return remainingMinutes === 0 ? hours + ' h' : hours + ' h ' + remainingMinutes + ' min';
    }

    function showDistanceOverlay(radiusKm) {
        if (!userLatLng) return;
        const radiusM = radiusKm * 1000;
        const center = [userLatLng.lat, userLatLng.lng];
        if (!searchCircle) {
            searchCircle = L.circle(center, {
                radius: radiusM, color:'#9e2016', fillColor:'#9e2016',
                fillOpacity:0.04, weight:1.5, opacity:0.5, dashArray:'6 8'
            }).addTo(map);
        } else {
            if (!map.hasLayer(searchCircle)) map.addLayer(searchCircle);
            searchCircle.setLatLng(center);
            searchCircle.setRadius(radiusM);
        }
        if (!distanceLabel) {
            distanceLabel = L.marker(center, {
                icon: L.divIcon({ html: '<div class="distance-label">' + radiusKm + ' km</div>', className:'', iconSize:[0,0], iconAnchor:[0,0] }),
                interactive: false, zIndexOffset: 1000
            }).addTo(map);
        } else {
            if (!map.hasLayer(distanceLabel)) map.addLayer(distanceLabel);
            distanceLabel.setLatLng(center);
            distanceLabel.setIcon(L.divIcon({ html: '<div class="distance-label">' + radiusKm + ' km</div>', className:'', iconSize:[0,0], iconAnchor:[0,0] }));
        }
    }
    function hideDistanceOverlay() {
        if (searchCircle && map.hasLayer(searchCircle)) map.removeLayer(searchCircle);
        if (distanceLabel && map.hasLayer(distanceLabel)) map.removeLayer(distanceLabel);
    }
    function syncDistanceOverlay() {
        if (filterState.useNearby && userLatLng) {
            showDistanceOverlay(filterState.maxDistance);
        } else {
            hideDistanceOverlay();
        }
    }

    function geolocationErrorMessage(error) {
        switch (error.code) {
            case 1: return 'No autorizaste el acceso a tu ubicaci\u00f3n. Si cambias de idea, puedes intentarlo nuevamente.';
            case 2: return 'No pudimos determinar tu ubicaci\u00f3n actual. Revisa tu GPS o tu conexi\u00f3n e int\u00e9ntalo otra vez.';
            case 3: return 'La b\u00fasqueda de tu ubicaci\u00f3n tard\u00f3 demasiado. Intenta nuevamente en unos segundos.';
            default: return 'Ocurri\u00f3 un problema al obtener tu ubicaci\u00f3n actual. Vuelve a intentarlo.';
        }
    }

    function escapeHtml(value) {
        return String(value)
            .replaceAll('&', '&amp;')
            .replaceAll('<', '&lt;')
            .replaceAll('>', '&gt;')
            .replaceAll('"', '&quot;')
            .replaceAll("'", '&#39;');
    }

    function lowerFirst(text) {
        return text.charAt(0).toLowerCase() + text.slice(1);
    }

    // --- Initial render (immediate) ---
    applyFiltersAndRender();

    // --- Sort pills ---
    document.querySelectorAll('.filter-pill').forEach(btn => {
        btn.addEventListener('click', () => {
            document.querySelectorAll('.filter-pill').forEach(b => {
                b.classList.remove('bg-primary', 'text-on-primary', 'shadow-md');
                b.classList.add('bg-surface-container-lowest', 'text-on-surface-variant');
            });
            btn.classList.remove('bg-surface-container-lowest', 'text-on-surface-variant');
            btn.classList.add('bg-primary', 'text-on-primary', 'shadow-md');
            filterState.sort = btn.dataset.sort;
            applyFiltersAndRender();
            syncQuickFilterUI();
        });
    });

    // --- Quick filters ---
    document.querySelectorAll('.quick-filter').forEach(btn => {
        btn.addEventListener('click', () => {
            const f = btn.dataset.filter;
            if (f === 'economico') {
                filterState.priceRange = filterState.priceRange === 'bajo' ? 'all' : 'bajo';
            }
            if (f === 'nearby') {
                filterState.useNearby = !filterState.useNearby;
            }
            if (f === 'openNow') {
                filterState.openNow = !filterState.openNow;
            }
            applyFiltersAndRender();
            syncQuickFilterUI();
        });
    });

    // --- Global search ---
    document.getElementById('globalSearch').addEventListener('input', (e)=>{
        filterState.query = e.target.value;
        applyFiltersAndRender();
    });

    // --- Advanced filters modal ---
    document.getElementById('openAdvanced').addEventListener('click', ()=> {
        syncAdvForm();
        syncRatingStarsUI();
        document.getElementById('advancedFilters').classList.remove('hidden');
    });

    document.getElementById('advMaxDistance').addEventListener('input', (e) => {
        const val = Number(e.target.value);
        document.getElementById('advMaxDistanceValue').innerText = val + ' km';
        if (!filterState.useNearby) {
            filterState.useNearby = true;
            syncQuickFilterUI();
        }
        if (userLatLng) {
            showDistanceOverlay(val);
        }
    });

    document.querySelectorAll('.rating-star-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            const val = Number(btn.dataset.rating);
            filterState.ratingMin = filterState.ratingMin === val ? 0 : val;
            syncRatingStarsUI();
        });
    });

    document.getElementById('advZone').addEventListener('change', (e) => {
        filterState.zone = e.target.value;
    });

    document.getElementById('advOpenNow').addEventListener('change', (e) => {
        filterState.openNow = e.target.checked;
    });

    document.querySelectorAll('input[name="advPrice"]').forEach(r => {
        r.addEventListener('change', () => {
            filterState.priceRange = r.value;
        });
    });

    document.getElementById('applyAdvanced').addEventListener('click', ()=> {
        filterState.maxDistance = Number(document.getElementById('advMaxDistance').value);
        filterState.zone = document.getElementById('advZone').value;
        filterState.openNow = document.getElementById('advOpenNow').checked;
        document.querySelectorAll('input[name="advPrice"]').forEach(r => {
            if (r.checked) filterState.priceRange = r.value;
        });
        if (filterState.maxDistance < 50 && !filterState.useNearby) {
            filterState.useNearby = true;
        }
        document.getElementById('advancedFilters').classList.add('hidden');
        applyFiltersAndRender();
        syncQuickFilterUI();
    });

    document.getElementById('clearAdvanced').addEventListener('click', ()=> {
        filterState.maxDistance = 5;
        filterState.zone = '';
        filterState.ratingMin = 0;
        filterState.priceRange = 'all';
        filterState.openNow = false;
        syncAdvForm();
        syncRatingStarsUI();
    });

    // Clear all filters
    document.getElementById('clearFilters').addEventListener('click', () => {
        filterState.useNearby = false;
        filterState.openNow = false;
        filterState.zone = '';
        filterState.ratingMin = 0;
        filterState.priceRange = 'all';
        filterState.maxDistance = 5;
        filterState.query = '';
        document.getElementById('globalSearch').value = '';
        applyFiltersAndRender();
        syncQuickFilterUI();
        syncRatingStarsUI();
        syncAdvForm();
    });

    // --- Initial load: try geolocation ---
    if(navigator.geolocation){
        navigator.geolocation.getCurrentPosition((pos)=>{
            var lat = pos.coords.latitude, lng = pos.coords.longitude;
            userLatLng = { lat: lat, lng: lng };
            userLatLng = userLatLng;
            const accuracy = pos.coords.accuracy || 0;
            fetchNearby(lat, lng);

            const meSvg = '<svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="12" cy="12" r="12" fill="#9e2016"/><path d="M12 12a3 3 0 1 0 0-6 3 3 0 0 0 0 6z" fill="#fff"/><path d="M6 20c0-2.667 5.333-4 6-4s6 1.333 6 4v0H6z" fill="#fff"/></svg>';
            const meIcon = L.divIcon({ html: '<div class="me-marker">' + meSvg + '</div>', className:'', iconSize:[36,36], iconAnchor:[18,18] });
            if(meMarker) map.removeLayer(meMarker);
            meMarker = L.marker([lat,lng], { icon: meIcon }).addTo(map);

            if(accuracy && accuracy > 0){
                if(accuracyCircle) map.removeLayer(accuracyCircle);
                accuracyCircle = L.circle([lat,lng], { radius: accuracy, color:'#1976d2', fillColor:'#1976d2', fillOpacity:0.03, weight:0.5, opacity:0.3 }).addTo(map);
            }

            map.setView([lat,lng], 13);
        }, ()=>{
            // Geolocation denied — list already showing base
        }, { enableHighAccuracy: true, timeout: 10000 });
    }

    // --- Route event listeners ---
    document.getElementById('listContainer').addEventListener('click', function(e) {
        const routeBtn = e.target.closest('.open-route');
        if (routeBtn) {
            e.stopPropagation();
            startRouteForRestaurant(Number(routeBtn.dataset.id));
        }
    });

    window.addEventListener('open-route', (e) => {
        startRouteForRestaurant(e.detail.id);
    });

    document.getElementById('closeRoutePanel').addEventListener('click', closeRoute);

    document.querySelectorAll('[data-close-route-modal]').forEach(el => {
        el.addEventListener('click', closeRoute);
    });

    document.querySelectorAll('.route-mode-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            document.querySelectorAll('.route-mode-btn').forEach(b => {
                b.classList.remove('bg-primary', 'text-white');
                b.classList.add('bg-stone-100', 'text-stone-600');
            });
            btn.classList.add('bg-primary', 'text-white');
            btn.classList.remove('bg-stone-100', 'text-stone-600');
            routeMode = btn.dataset.mode;
            if (activeRouteRestaurant && userLatLng) {
                calculateRoute(userLatLng.lat, userLatLng.lng);
            }
        });
    });

    document.querySelectorAll('.route-mode-btn-mobile').forEach(btn => {
        btn.addEventListener('click', () => {
            document.querySelectorAll('.route-mode-btn-mobile').forEach(b => {
                b.classList.remove('bg-primary', 'text-white');
                b.classList.add('bg-stone-100', 'text-stone-600');
            });
            btn.classList.add('bg-primary', 'text-white');
            btn.classList.remove('bg-stone-100', 'text-stone-600');
            routeMode = btn.dataset.mode;
            if (activeRouteRestaurant && userLatLng) {
                calculateRoute(userLatLng.lat, userLatLng.lng);
            }
        });
    });

    document.getElementById('refreshRoute').addEventListener('click', () => {
        if (userLatLng) {
            calculateRoute(userLatLng.lat, userLatLng.lng);
        } else {
            requestUserLocationForRoute();
        }
    });

    document.getElementById('recenterRoute').addEventListener('click', recenterRoute);

    document.getElementById('refreshRouteMobile').addEventListener('click', () => {
        if (userLatLng) {
            calculateRoute(userLatLng.lat, userLatLng.lng);
        } else {
            requestUserLocationForRoute();
        }
    });

    document.getElementById('recenterRouteMobile').addEventListener('click', recenterMobileRoute);
});
</script>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\yovan\Proyecto-restaurant\src\resources\views/comensal/explorar.blade.php ENDPATH**/ ?>