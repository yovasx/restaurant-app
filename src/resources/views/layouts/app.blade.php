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
    $showPublicNav = !auth()->guard('comensal')->check() && !auth()->guard('usuario')->check();
    $flushContent = trim($__env->yieldContent('layout-flush')) === 'true';
@endphp

@if($showPublicNav)
    @include('partials.public-nav')
@endif

<main class="{{ $flushContent ? '' : 'mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8' }}">
    @yield('content')
</main>

@include('partials.screen-toast')
</body>
</html>
