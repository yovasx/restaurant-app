@extends('layouts.app')

@section('title', 'Mi Perfil - GastroGuía')

@section('content')
<!-- TopAppBar -->
<header class="fixed top-0 left-0 w-full flex justify-between items-center px-6 py-4 bg-orange-50/80 backdrop-blur-md shadow-sm z-50">
    <div class="flex items-center gap-2">
        <span class="text-2xl font-black text-red-800 tracking-tight">GastroGuía</span>
    </div>
    <div class="flex items-center gap-4">
        <button class="p-2 rounded-full hover:bg-orange-100/50 transition-colors duration-300">
            <span class="material-symbols-outlined text-stone-500">notifications</span>
        </button>
        <button class="p-2 rounded-full hover:bg-orange-100/50 transition-colors duration-300">
            <span class="material-symbols-outlined text-stone-500">more_vert</span>
        </button>
    </div>
</header>
<!-- Content Separation Logic -->
<div class="h-[72px]"></div>
<main class="max-w-3xl mx-auto px-6 py-12">
    <!-- Header Section -->
    <section class="mb-12">
        <h1 class="text-4xl font-extrabold text-on-surface mb-2 tracking-tight">Ajustes de Perfil</h1>
        <p class="text-on-surface-variant font-medium">Gestiona tu identidad gastronómica.</p>
        
        @if(session('success'))
        <div class="mt-4 p-4 bg-green-100 text-green-800 rounded-xl font-bold">
            {{ session('success') }}
        </div>
        @endif
        @if($errors->any())
        <div class="mt-4 p-4 bg-red-100 text-red-800 rounded-xl font-bold">
            Hubo problemas al guardar tu información. Revisa los campos.
        </div>
        @endif
    </section>

    <!-- Profile Hero Card (Bento Style) -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
        <div class="md:col-span-1 bg-surface-container-lowest rounded-xl p-8 flex flex-col items-center justify-center shadow-sm relative overflow-hidden group">
            <div class="absolute inset-0 bg-gradient-to-br from-primary/5 to-transparent opacity-50"></div>
            <div class="relative w-32 h-32 mb-4">
                <img alt="Avatar" class="w-full h-full rounded-full object-cover border-4 border-surface-container shadow-lg" src="https://ui-avatars.com/api/?name={{ urlencode(Auth::guard('comensal')->user()->nombre) }}&background=bb2b1a&color=fff&size=200"/>
                <button class="absolute bottom-1 right-1 bg-primary text-white p-2 rounded-full shadow-md hover:scale-110 transition-transform">
                    <span class="material-symbols-outlined text-sm">edit</span>
                </button>
            </div>
            <p class="text-xs font-bold uppercase tracking-widest text-[#c0392b] mb-1">Comensal Gourmet</p>
            <h2 class="text-xl font-bold text-on-surface text-center">{{ Auth::guard('comensal')->user()->nombre }}</h2>
        </div>
        <div class="md:col-span-2 bg-[#f9f2ec] rounded-xl p-8 flex flex-col justify-between border-l-4 border-[#9e2016]">
            <div>
                <h3 class="text-lg font-bold text-on-surface mb-4">Ubicación Actual</h3>
                <div class="flex items-center gap-3 mb-6">
                    <span class="material-symbols-outlined text-[#9e2016]">location_on</span>
                    <span class="text-[#59413d] font-medium">La Paz, Bolivia</span>
                </div>
            </div>
            <div class="bg-white rounded-lg p-4 flex justify-between items-center shadow-sm">
                <div class="flex flex-col">
                    <span class="text-[10px] uppercase tracking-tighter text-stone-400 font-bold">Nivel de Socio</span>
                    <span class="text-on-surface font-semibold">Oro • Miembro Activo</span>
                </div>
                <span class="material-symbols-outlined text-[#fc8f34]" style="font-variation-settings: 'FILL' 1;">stars</span>
            </div>
        </div>
    </div>

    <!-- Settings Form -->
    <form action="{{ route('comensal.perfil.update') ?? '#' }}" method="POST">
        @csrf
        <section class="space-y-8 bg-[#f9f2ec] rounded-xl p-8 md:p-10">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Name -->
                <div class="flex flex-col gap-2">
                    <label class="text-xs font-bold uppercase tracking-widest text-[#59413d] ml-1">Nombre Completo</label>
                    <div class="relative">
                        <input name="nombre" class="w-full bg-white border-0 rounded-lg px-4 py-3 text-on-surface focus:ring-2 focus:ring-[#9e2016] transition-all" type="text" value="{{ old('nombre', Auth::guard('comensal')->user()->nombre) }}" required/>
                        <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 text-stone-300">person</span>
                    </div>
                </div>
                
                <!-- Email -->
                <div class="flex flex-col gap-2">
                    <label class="text-xs font-bold uppercase tracking-widest text-[#59413d] ml-1">Correo Electrónico</label>
                    <div class="relative">
                        <input name="email" class="w-full bg-white border-0 rounded-lg px-4 py-3 text-on-surface focus:ring-2 focus:ring-[#9e2016] transition-all" type="email" value="{{ old('email', Auth::guard('comensal')->user()->email) }}" required/>
                        <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 text-stone-300">mail</span>
                    </div>
                </div>

                <!-- Password -->
                <div class="flex flex-col gap-2 md:col-span-2">
                    <label class="text-xs font-bold uppercase tracking-widest text-[#59413d] ml-1">Nueva Contraseña (Opcional)</label>
                    <div class="relative flex-grow">
                        <input name="password" class="w-full bg-white border-0 rounded-lg px-4 py-3 text-on-surface focus:ring-2 focus:ring-[#9e2016]" type="password" placeholder="Déjalo en blanco para mantener la actual"/>
                        <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 text-stone-400">lock</span>
                    </div>
                </div>

                <!-- Phone -->
                <div class="flex flex-col gap-2 md:col-span-2">
                    <label class="text-xs font-bold uppercase tracking-widest text-[#59413d] ml-1">Teléfono</label>
                    <div class="flex gap-0 overflow-hidden rounded-lg shadow-sm">
                        <div class="bg-[#eee7e1] px-4 flex items-center text-[#59413d] font-bold border-r border-[#e1bfb9]/20">
                            +591
                        </div>
                        <input name="telefono" class="w-full bg-white border-0 px-4 py-3 text-on-surface focus:ring-2 focus:ring-[#9e2016] transition-all outline-none" type="tel" value="{{ old('telefono', Auth::guard('comensal')->user()->telefono) }}"/>
                    </div>
                </div>
            </div>

            <!-- Save Button -->
            <div class="pt-6 flex justify-end">
                <button type="submit" class="bg-[#9e2016] text-white font-bold px-10 py-4 rounded-lg shadow-xl shadow-red-900/20 hover:scale-[1.02] active:scale-95 transition-all duration-300">
                    Guardar Cambios
                </button>
            </div>
        </section>
    </form>

    <div class="mt-8">
        <form method="POST" action="{{ route('logout') }}" class="flex justify-center">
            @csrf
            <button type="submit" class="font-bold text-red-600 underline hover:text-red-800 transition-colors">
                Cerrar Sesión Activa
            </button>
        </form>
    </div>

    <!-- Footer Visual Decor -->
    <div class="mt-12 opacity-30 flex justify-center">
        <span class="material-symbols-outlined text-6xl text-stone-300">restaurant_menu</span>
    </div>
</main>
@endsection
