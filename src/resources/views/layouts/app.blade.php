<!DOCTYPE html>
<html class="light" lang="es">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>@yield('title', 'GastroGuía | Panel de Control')</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #FFF8F2; }
        .font-headline { font-family: 'Plus Jakarta Sans', sans-serif; }
        .material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24; }
        .hide-scrollbar::-webkit-scrollbar { display: none; }
    </style>
</head>
<body class="min-h-screen bg-background text-on-background">
@php
    $routeName = request()->route() ? request()->route()->getName() : '';
    $isComensalRoute = str_starts_with($routeName, 'comensal.');
    $flushContent = trim($__env->yieldContent('layout-flush')) === 'true';
@endphp

@if(!$isComensalRoute)
    @include('partials.public-nav')
@endif

<main class="{{ $flushContent ? '' : 'mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8' }}{{ $isComensalRoute ? ' pb-24 lg:pb-0' : '' }}">
    @yield('content')
</main>

@if($isComensalRoute && Auth::guard('comensal')->check())
<nav class="lg:hidden fixed bottom-0 left-0 w-full z-50 bg-white/80 backdrop-blur-md shadow-[0_-4px_20px_rgba(0,0,0,0.05)] rounded-t-2xl safe-area-pb">
    <div class="flex items-center justify-around overflow-x-auto px-1 py-2 gap-0.5">
        <a href="{{ route('comensal.inicio') }}"
           class="flex flex-col items-center px-2 py-0.5 shrink-0 {{ $routeName === 'comensal.inicio' ? 'text-[#C0392B]' : 'text-stone-400' }}">
            <span class="material-symbols-outlined text-xl">home</span>
            <span class="text-[9px] font-bold uppercase tracking-wider mt-0.5 whitespace-nowrap">Inicio</span>
        </a>
        <a href="{{ route('comensal.explorar') }}"
           class="flex flex-col items-center px-2 py-0.5 shrink-0 {{ $routeName === 'comensal.explorar' ? 'text-[#C0392B]' : 'text-stone-400' }}">
            <span class="material-symbols-outlined text-xl">explore</span>
            <span class="text-[9px] font-bold uppercase tracking-wider mt-0.5 whitespace-nowrap">Explorar</span>
        </a>
        <a href="{{ route('comensal.inicio') }}#favoritos"
           class="flex flex-col items-center px-2 py-0.5 shrink-0 text-stone-400">
            <span class="material-symbols-outlined text-xl">favorite</span>
            <span class="text-[9px] font-bold uppercase tracking-wider mt-0.5 whitespace-nowrap">Favoritos</span>
        </a>
        <a href="{{ route('comensal.perfil') }}"
           class="flex flex-col items-center px-2 py-0.5 shrink-0 {{ $routeName === 'comensal.perfil' ? 'text-[#C0392B]' : 'text-stone-400' }}">
            <span class="material-symbols-outlined text-xl">person</span>
            <span class="text-[9px] font-bold uppercase tracking-wider mt-0.5 whitespace-nowrap">Perfil</span>
        </a>
    </div>
</nav>
@endif

@include('partials.screen-toast')
</body>
</html>
