<?php $__env->startSection('title', 'GastroGuía | Descubre sabores en La Paz'); ?>
<?php $__env->startSection('layout-flush', 'true'); ?>

<?php $__env->startSection('content'); ?>
<div class="pb-24">
    <section class="relative h-[614px] min-h-[500px] w-full overflow-hidden">
        <div class="absolute inset-0 bg-stone-900">
            <img class="h-full w-full object-cover opacity-70" src="https://lh3.googleusercontent.com/aida-public/AB6AXuCg_iF3OdIur_RT5qkIAME3zJ5OerrBm_bSAQDK5x2DUdeZg3aKSFrDn7qt45JFUEO_lb-DnYBwqlnsornV6euEfh3oQNyERw_9g1FpFnvM88ubBKU-h3wvtdbEX0u17XcUuAtmJ74Thyv6U1cPQuISwcMmZLN1C8Z_TcPHOdF1h5UZjfbYGjlNaS8hJv_avRxYXRMHDGXdYzAJzx7G-V4kRN5fZtN85K_ypjGi3aokV2fshu5f2KirKRjGbED4YZP9MH6bApoIeo_7" alt="Restaurantes en La Paz" />
            <div class="absolute inset-0 bg-gradient-to-t from-stone-900 via-transparent to-transparent"></div>
        </div>
        <div class="relative mx-auto flex h-full max-w-5xl flex-col items-center justify-center space-y-8 px-4 text-center">
            <h1 class="font-headline text-5xl font-extrabold leading-tight tracking-tight text-white md:text-7xl">Experiencias que <span class="text-[#ffdcc5]">deleitan</span></h1>
            <div class="flex w-full max-w-2xl items-center gap-2 rounded-full bg-white p-2 shadow-xl">
                <div class="flex items-center pl-4 pr-2 text-stone-400">
                    <span class="material-symbols-outlined">location_on</span>
                    <input class="w-32 border-r border-stone-200 bg-transparent text-stone-800 outline-none focus:ring-0" placeholder="La Paz, Bolivia" type="text" />
                </div>
                <div class="flex flex-1 items-center px-2">
                    <span class="material-symbols-outlined mr-2 text-stone-400">search</span>
                    <input class="w-full bg-transparent text-stone-800 outline-none focus:ring-0" placeholder="Busca salteñas, pique macho..." type="text" />
                </div>
                <a href="#descubrir" class="rounded-full bg-[#9e2016] px-8 py-3 font-bold text-white transition-all hover:bg-[#c0392b]">Buscar</a>
            </div>
        </div>
    </section>

    <section class="relative z-10 mx-auto -mt-8 w-full max-w-7xl px-6">
        <div class="hide-scrollbar flex gap-3 overflow-x-auto pb-4">
            <a href="<?php echo e(route('home')); ?>"
               class="flex items-center gap-2 whitespace-nowrap rounded-full px-6 py-3 font-bold shadow-lg transition-all <?php echo e(!request('categoria') ? 'bg-[#9e2016] text-white' : 'border border-[#e1bfb9] bg-white text-[#59413d]'); ?>">
                <span class="material-symbols-outlined text-sm" style="font-variation-settings: 'FILL' 1;">restaurant</span> Todos
            </a>
            <?php
                $iconos = [
                    'Pizza' => 'local_pizza', 'Sushi' => 'set_meal', 'Burgers' => 'lunch_dining',
                    'Parrilla' => 'outdoor_grill', 'Comida Boliviana' => 'restaurant_menu',
                    'Café y Brunch' => 'local_cafe', 'Pastas' => 'ramen_dining',
                    'Pollos' => 'restaurant', 'Chifa' => 'takeout_dining',
                    'Mariscos' => 'set_meal', 'Postres y Panadería' => 'cake',
                    'Cocina Fusión' => 'menu_book',
                ];
            ?>
            <?php $__currentLoopData = $categorias; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <a href="<?php echo e(route('home', ['categoria' => $cat->id])); ?>"
                   class="flex items-center gap-2 whitespace-nowrap rounded-full px-6 py-3 font-semibold transition-all hover:bg-[#9e2016] hover:text-white <?php echo e(request('categoria') == $cat->id ? 'bg-[#9e2016] text-white' : 'border border-[#e1bfb9] bg-white text-[#59413d]'); ?>">
                    <span class="material-symbols-outlined text-sm"><?php echo e($iconos[$cat->nombre_categoria] ?? 'restaurant'); ?></span>
                    <?php echo e($cat->nombre_categoria); ?>

                </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </section>

    <section id="beneficios" class="mx-auto mt-12 w-full max-w-7xl px-6">
        <div class="relative flex flex-col items-center justify-between gap-6 overflow-hidden rounded-2xl bg-[#fc8f34] p-8 md:flex-row">
            <div class="relative z-10 max-w-lg text-[#663100]">
                <span class="mb-4 inline-block rounded-full bg-[#663100] px-3 py-1 text-xs font-bold uppercase tracking-widest text-[#fc8f34]">Oferta de la Semana</span>
                <h2 class="mb-2 font-headline text-3xl font-extrabold">2x1 en Cenas de Autor</h2>
                <p class="font-medium opacity-90">Reserva tu mesa para este jueves y disfruta de un menú degustación exclusivo para dos al precio de uno.</p>
            </div>
            <div class="relative z-10">
                <a href="#descubrir" class="rounded-xl bg-[#663100] px-8 py-4 font-bold text-white shadow-2xl transition-transform hover:scale-105">Ver Restaurantes</a>
            </div>
            <div class="pointer-events-none absolute right-0 top-0 p-8 opacity-10"><span class="material-symbols-outlined text-9xl">local_offer</span></div>
        </div>
    </section>

    <section id="descubrir" class="mx-auto mt-16 w-full max-w-7xl px-6">
        <div class="mb-8 flex items-end justify-between">
            <div>
                <h3 class="mb-2 text-sm font-bold uppercase tracking-[0.2em] text-[#944a00]">Descubrimientos Locales</h3>
                <h2 class="font-headline text-4xl font-extrabold tracking-tight text-[#1e1b18]">Restaurantes Cercanos</h2>
            </div>
            <a href="#descubrir" class="flex items-center gap-1 font-bold text-[#9e2016] hover:underline">Ver todo <span class="material-symbols-outlined text-sm">arrow_forward</span></a>
        </div>

        <div id="restaurantsGrid" class="grid grid-cols-1 gap-6 md:grid-cols-3">
            <?php $initial = $restaurants ?? collect(); ?>
            <?php $__empty_1 = true; $__currentLoopData = $initial; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div class="group overflow-hidden rounded-xl bg-white shadow-sm transition-all duration-300 hover:shadow-xl">
                    <a href="<?php echo e(route('restaurante.show', $r->id)); ?>" class="block">
                        <div class="relative h-64 w-full overflow-hidden">
                            <img class="h-full w-full object-cover transition-transform duration-700 group-hover:scale-110" src="<?php echo e(media_url($r->foto_portada) ?: 'https://via.placeholder.com/900x600?text=Restaurante'); ?>" alt="<?php echo e($r->nombre); ?>"/>
                            <div class="absolute right-4 top-4 flex items-center gap-1 rounded-full bg-white/90 px-3 py-1 shadow backdrop-blur"><span class="material-symbols-outlined text-sm text-yellow-500" style="font-variation-settings: 'FILL' 1;">star</span><span class="text-sm font-bold text-stone-800"><?php echo e(isset($r->avg_rating) ? number_format($r->avg_rating, 1) : '—'); ?></span></div>
                        </div>
                        <div class="p-6">
                            <div class="mb-2">
                                <h4 class="text-xl font-extrabold" style="font-family: 'Plus Jakarta Sans', sans-serif;"><?php echo e($r->nombre); ?></h4>
                            </div>
                            <p class="mb-4 text-sm text-[#59413d]"><?php echo e(\Illuminate\Support\Str::limit($r->descripcion, 80)); ?> <span class="restaurant-distance" data-lat="<?php echo e($r->latitud); ?>" data-lng="<?php echo e($r->longitud); ?>"><?php echo e($r->latitud && $r->longitud ? '• —' : ''); ?></span></p>
                        </div>
                    </a>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div class="rounded-2xl bg-white p-8 text-center text-[#59413d] shadow-sm ring-1 ring-stone-100 md:col-span-3">
                    Pronto verás aquí los restaurantes más destacados de la plataforma.
                </div>
            <?php endif; ?>
        </div>

        <script>
        document.addEventListener('DOMContentLoaded', function () {
            const grid = document.getElementById('restaurantsGrid');

            if (!grid || !navigator.geolocation) {
                return;
            }

            navigator.geolocation.getCurrentPosition(async (pos) => {
                const lat = pos.coords.latitude;
                const lng = pos.coords.longitude;

                try {
                    const resp = await fetch('<?php echo e(route('restaurantes.nearby')); ?>?lat=' + lat + '&lng=' + lng + '&ajax=1');

                    if (!resp.ok) {
                        return;
                    }

                    const json = await resp.json();
                    const items = json.data || [];

                    grid.innerHTML = items.map((r) => {
                        const foto = r.foto_portada_url || 'https://via.placeholder.com/900x600?text=Restaurante';
                        const dist = r.distance !== undefined ? (Number(r.distance).toFixed(2) + ' km') : '—';
                        const descripcion = (r.descripcion || '').substring(0, 80);

                        return `
                            <div class="bg-white rounded-xl shadow-sm hover:shadow-xl transition-all duration-300 group overflow-hidden">
                                <a href="/restaurante/${r.id}" class="block">
                                    <div class="relative h-64 w-full overflow-hidden">
                                        <img class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700" src="${foto}" alt="${r.nombre}"/>
                                        <div class="absolute top-4 right-4 bg-white/90 backdrop-blur px-3 py-1 rounded-full flex items-center gap-1 shadow"><span class="material-symbols-outlined text-yellow-500 text-sm" style="font-variation-settings: 'FILL' 1;">star</span><span class="font-bold text-sm text-stone-800">${r.avg_rating ? Number(r.avg_rating).toFixed(1) : '—'}</span></div>
                                    </div>
                                    <div class="p-6">
                                        <div class="mb-2">
                                            <h4 class="text-xl font-extrabold" style="font-family: 'Plus Jakarta Sans', sans-serif;">${r.nombre}</h4>
                                        </div>
                                        <p class="text-[#59413d] text-sm mb-4">${descripcion} • <span>${dist}</span></p>
                                    </div>
                                </a>
                            </div>
                        `;
                    }).join('');
                } catch (error) {
                    console.error(error);
                }
            }, () => {}, { enableHighAccuracy: true, timeout: 10000 });
        });
        </script>
    </section>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\yovan\Proyecto-restaurant\src\resources\views/welcome.blade.php ENDPATH**/ ?>