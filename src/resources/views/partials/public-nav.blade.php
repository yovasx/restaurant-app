@props([
    'ctaMode' => 'full',
])

@php
    $isComensal = auth()->guard('comensal')->check();
@endphp

<header class="sticky top-0 z-50 border-b border-stone-200/70 bg-[#FFF8F2]/92 backdrop-blur-md">
    <div class="mx-auto flex w-full max-w-7xl items-center justify-between gap-4 px-4 py-4 sm:px-6 lg:px-8">
        <a href="{{ $isComensal ? route('comensal.inicio') : route('home') }}" class="flex items-center gap-3">
            <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-[#c0392b] text-white shadow-lg shadow-red-900/20">
                <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">restaurant_menu</span>
            </div>
            <div>
                <p class="font-headline text-2xl font-black tracking-tight text-[#9e2016]">GastroGuía</p>
                <p class="text-[10px] font-bold uppercase tracking-[0.3em] text-stone-500">Sabores de Bolivia</p>
            </div>
        </a>

        @if($isComensal)
            <nav class="hidden items-center gap-8 md:flex">
                <a href="{{ route('comensal.inicio') }}" class="font-headline text-sm font-bold tracking-wide transition-colors {{ request()->routeIs('comensal.inicio') ? 'text-[#9e2016]' : 'text-[#59413d] hover:text-[#9e2016]' }}">Inicio</a>
                <a href="{{ route('comensal.explorar') }}" class="font-headline text-sm font-bold tracking-wide transition-colors {{ request()->routeIs('comensal.explorar') ? 'text-[#9e2016]' : 'text-[#59413d] hover:text-[#9e2016]' }}">Explorar</a>
                <a href="{{ route('comensal.perfil') }}" class="font-headline text-sm font-bold tracking-wide transition-colors {{ request()->routeIs('comensal.perfil') ? 'text-[#9e2016]' : 'text-[#59413d] hover:text-[#9e2016]' }}">Perfil</a>
            </nav>

            <div class="hidden items-center gap-3 md:flex">
                <a href="{{ route('comensal.perfil') }}" class="rounded-full border border-stone-200 bg-white px-5 py-2.5 text-sm font-bold text-[#1e1b18] transition hover:border-red-200 hover:text-[#9e2016]">
                    Hola, {{ auth()->guard('comensal')->user()->nombre }}
                </a>
                <form method="POST" action="{{ route('logout.comensal') }}" class="inline">@csrf<button type="submit" class="rounded-full bg-[#9e2016] px-5 py-2.5 text-sm font-bold text-white shadow-lg shadow-red-900/20 transition hover:bg-[#c0392b]">Cerrar sesión</button></form>
            </div>
        @else
            <nav class="hidden items-center gap-8 md:flex">
                <a href="{{ route('home') }}" class="font-headline text-sm font-bold tracking-wide {{ request()->routeIs('home') ? 'text-[#9e2016]' : 'text-[#59413d] hover:text-[#9e2016]' }} transition-colors">Inicio</a>
                <a href="{{ request()->routeIs('home') ? '#descubrir' : route('home').'#descubrir' }}" class="font-headline text-sm font-bold tracking-wide text-[#59413d] transition-colors hover:text-[#9e2016]">Explorar</a>
                <a href="{{ request()->routeIs('home') ? '#beneficios' : route('home').'#beneficios' }}" class="font-headline text-sm font-bold tracking-wide text-[#59413d] transition-colors hover:text-[#9e2016]">Beneficios</a>
            </nav>

            <div class="hidden items-center gap-3 md:flex">
                <a href="{{ route('login') }}" class="rounded-full border border-stone-200 bg-white px-5 py-2.5 text-sm font-bold text-[#1e1b18] transition hover:border-red-200 hover:text-[#9e2016]">
                    Iniciar sesión
                </a>

                <a href="{{ route('register.comensal') }}" class="rounded-full bg-[#9e2016] px-5 py-2.5 text-sm font-bold text-white shadow-lg shadow-red-900/20 transition hover:bg-[#c0392b]">
                    Crear cuenta
                </a>

                <details class="group relative">
                    <summary class="flex cursor-pointer list-none items-center gap-2 rounded-full border border-amber-200 bg-[#ffdcc5] px-4 py-2.5 text-sm font-bold text-[#663100] transition hover:bg-[#ffb783]">
                        <span class="material-symbols-outlined text-[18px]">storefront</span>
                        Soy restaurante
                        <span class="material-symbols-outlined text-[18px] text-[#663100]/70">expand_more</span>
                    </summary>

                    <div class="absolute right-0 mt-3 w-72 overflow-hidden rounded-3xl border border-stone-200 bg-white p-3 shadow-2xl shadow-stone-900/10">
                        <a href="{{ route('login', ['role' => 'restaurante']) }}" class="flex items-start gap-3 rounded-2xl px-4 py-3 transition hover:bg-stone-50">
                            <span class="material-symbols-outlined mt-0.5 text-[#9e2016]">login</span>
                            <span>
                                <span class="block font-headline text-sm font-extrabold text-[#1e1b18]">Iniciar sesión restaurante</span>
                                <span class="block text-xs text-stone-500">Accede a tu panel de negocio.</span>
                            </span>
                        </a>
                        <a href="{{ route('register.restaurante') }}" class="mt-2 flex items-start gap-3 rounded-2xl px-4 py-3 transition hover:bg-stone-50">
                            <span class="material-symbols-outlined mt-0.5 text-[#9e2016]">add_business</span>
                            <span>
                                <span class="block font-headline text-sm font-extrabold text-[#1e1b18]">Crear cuenta restaurante</span>
                                <span class="block text-xs text-stone-500">Registra tu local y empieza a destacar.</span>
                            </span>
                        </a>
                    </div>
                </details>
            </div>
        @endif
    </div>
</header>
