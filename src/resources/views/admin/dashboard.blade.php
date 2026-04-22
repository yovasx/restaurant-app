@extends('layouts.admin')

@section('content')
<!-- Welcome Header -->
<header class="mb-10">
    <h2 class="text-3xl font-extrabold text-on-surface tracking-tight mb-2">Resumen del Panel</h2>
    <p class="text-on-surface-variant font-medium">Monitoreando el pulso gastronómico.</p>
</header>

<!-- KPI Cards Grid -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
    <!-- Total Restaurants -->
    <div class="bg-surface-container-lowest p-6 rounded-2xl shadow-sm border border-stone-100/50 group hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 rounded-xl bg-primary-fixed flex items-center justify-center text-primary">
                <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">restaurant</span>
            </div>
        </div>
        <p class="text-sm font-semibold text-stone-500 uppercase tracking-wider mb-1">Total de Restaurantes</p>
        <h3 class="text-3xl font-black text-on-surface">{{ $totalRestaurantes }}</h3>
    </div>
    <!-- Active Users -->
    <div class="bg-surface-container-lowest p-6 rounded-2xl shadow-sm border border-stone-100/50 group hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 rounded-xl bg-secondary-fixed flex items-center justify-center text-secondary">
                <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">person</span>
            </div>
        </div>
        <p class="text-sm font-semibold text-stone-500 uppercase tracking-wider mb-1">Comensales Registrados</p>
        <h3 class="text-3xl font-black text-on-surface">{{ $totalComensales }}</h3>
    </div>
    <!-- Pending Reviews -->
    <div class="bg-surface-container-lowest p-6 rounded-2xl shadow-sm border border-stone-100/50 group hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 rounded-xl bg-tertiary-fixed flex items-center justify-center text-tertiary">
                <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">pending_actions</span>
            </div>
        </div>
        <p class="text-sm font-semibold text-stone-500 uppercase tracking-wider mb-1">Reseñas Muestra</p>
        <h3 class="text-3xl font-black text-on-surface">--</h3>
    </div>
    <!-- Monthly Revenue -->
    <div class="bg-primary text-on-primary p-6 rounded-2xl shadow-xl shadow-primary/20 group hover:scale-[1.02] transition-transform">
        <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 rounded-xl bg-white/20 backdrop-blur-md flex items-center justify-center text-white">
                <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">payments</span>
            </div>
            <span class="text-xs font-bold text-white/80 bg-white/10 px-2 py-1 rounded-full">Mes Actual</span>
        </div>
        <p class="text-sm font-semibold text-white/70 uppercase tracking-wider mb-1">Visitas (Bs.)</p>
        <h3 class="text-3xl font-black">--</h3>
    </div>
</div>

<div class="bg-surface-container-low rounded-3xl p-8 items-center overflow-hidden">
    <span class="text-xs font-bold text-primary uppercase tracking-[0.2em] mb-4 block">Bienvenido</span>
    <h3 class="text-4xl font-extrabold text-on-surface mb-6 leading-tight">Gestión del Sistema<br/>GastroGuía</h3>
    <p class="text-on-surface-variant leading-relaxed max-w-xl">
        Utiliza el panel lateral para navegar entre la gestión de usuarios, restaurantes y aprobaciones.
    </p>
</div>
@endsection
