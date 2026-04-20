@extends('layouts.admin')

@section('content')
<div class="flex justify-between items-end mb-10">
    <div>
        <h2 class="text-4xl font-extrabold text-on-surface tracking-tight mb-2">Crear Nuevo Usuario</h2>
        <p class="text-on-surface-variant font-medium">Registra manualmente un Administrador, Restaurante o Comensal en el sistema.</p>
    </div>
</div>

<div class="bg-surface-container-lowest rounded-3xl p-8 shadow-xl max-w-3xl border-t-4 border-primary">
    <form action="{{ route('admin.usuarios.store') }}" method="POST" class="space-y-6">
        @csrf
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="space-y-2">
                <label class="block text-xs font-bold text-on-surface-variant uppercase tracking-wider mb-2">Nombre Completo</label>
                <input name="nombre" class="w-full bg-surface-container-low border-none rounded-xl py-3 px-4 focus:ring-2 focus:ring-primary font-medium text-on-surface" type="text" required value="{{ old('nombre') }}" placeholder="Nombre o Razón Social" />
            </div>

            <div class="space-y-2">
                <label class="block text-xs font-bold text-on-surface-variant uppercase tracking-wider mb-2">Correo Electrónico</label>
                <input name="email" class="w-full bg-surface-container-low border-none rounded-xl py-3 px-4 focus:ring-2 focus:ring-primary font-medium text-on-surface" type="email" required value="{{ old('email') }}" placeholder="usuario@correo.com"/>
            </div>

            <div class="space-y-2">
                <label class="block text-xs font-bold text-on-surface-variant uppercase tracking-wider mb-2">Teléfono</label>
                <input name="telefono" class="w-full bg-surface-container-low border-none rounded-xl py-3 px-4 focus:ring-2 focus:ring-primary font-medium text-on-surface" type="text" value="{{ old('telefono') }}" placeholder="70000000"/>
            </div>

            <div class="space-y-2">
                <label class="block text-xs font-bold text-on-surface-variant uppercase tracking-wider mb-2">Tipo de Rol</label>
                <select name="user_type" class="w-full bg-surface-container-low border-none rounded-xl py-3 px-4 focus:ring-2 focus:ring-primary font-medium text-on-surface" required>
                    <option value="" disabled {{ !request('role') ? 'selected' : '' }}>Selecciona un rol</option>
                    <option value="1" {{ request('role') == '1' ? 'selected' : '' }}>Administrador (staff)</option>
                    <option value="2" {{ request('role') == '2' ? 'selected' : '' }}>Restaurante (negocio)</option>
                    <option value="comensal" {{ request('role') == 'comensal' ? 'selected' : '' }}>Comensal (cliente)</option>
                </select>
            </div>

            <div class="space-y-2 md:col-span-2">
                <label class="block text-xs font-bold text-on-surface-variant uppercase tracking-wider mb-2">Contraseña Provisional</label>
                <input name="password" class="w-full bg-surface-container-low border-none rounded-xl py-3 px-4 focus:ring-2 focus:ring-primary font-medium text-on-surface" type="password" required minlength="6" placeholder="Mínimo 6 caracteres"/>
            </div>
            
            <div class="space-y-2 md:col-span-2">
                <label class="block text-xs font-bold text-on-surface-variant uppercase tracking-wider mb-2">Estado Inicial</label>
                <select name="estado" class="w-full bg-surface-container-low border-none rounded-xl py-3 px-4 focus:ring-2 focus:ring-primary font-medium text-on-surface" required>
                    <option value="activo" selected>Activo</option>
                    <option value="inactivo">Inactivo</option>
                </select>
            </div>
        </div>

        <div class="flex gap-4 pt-6">
            <a href="{{ route('admin.dashboard') }}" class="flex-1 text-center px-6 py-4 rounded-xl font-bold text-on-surface-variant hover:bg-surface-container-high transition-colors">Volver</a>
            <button class="flex-1 bg-primary text-white px-6 py-4 rounded-xl font-extrabold shadow-lg shadow-primary/20 hover:scale-[1.02] transition-transform" type="submit">Crear Usuario</button>
        </div>
    </form>
</div>
@endsection
