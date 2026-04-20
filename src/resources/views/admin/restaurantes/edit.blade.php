@extends('layouts.admin')

@section('content')
<div class="flex items-center gap-4 mb-10">
    <a href="{{ route('admin.restaurantes.index') }}" class="w-10 h-10 rounded-full bg-surface-container-high flex items-center justify-center hover:bg-primary-container hover:text-white transition-all">
        <span class="material-symbols-outlined">arrow_back</span>
    </a>
    <div>
        <h2 class="text-4xl font-extrabold text-on-surface tracking-tight mb-2">Editar Restaurante</h2>
        <p class="text-on-surface-variant font-medium">Modifica los detalles del local: <span class="font-bold border-b border-primary">{{ $usuario->nombre }}</span></p>
    </div>
</div>

<div class="bg-surface-container-lowest rounded-3xl p-8 shadow-xl max-w-3xl border-t-4 border-primary">
    <form action="{{ route('admin.restaurantes.update', $usuario->id) }}" method="POST" class="space-y-6">
        @csrf
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="space-y-2">
                <label class="block text-xs font-bold text-on-surface-variant uppercase tracking-wider mb-2">Nombre o Razón Social</label>
                <input name="nombre" class="w-full bg-surface-container-low border-none rounded-xl py-3 px-4 focus:ring-2 focus:ring-primary font-medium text-on-surface" type="text" required value="{{ old('nombre', $usuario->nombre) }}" />
            </div>

            <div class="space-y-2">
                <label class="block text-xs font-bold text-on-surface-variant uppercase tracking-wider mb-2">Correo Electrónico</label>
                <input name="email" class="w-full bg-surface-container-low border-none rounded-xl py-3 px-4 focus:ring-2 focus:ring-primary font-medium text-on-surface" type="email" required value="{{ old('email', $usuario->email) }}" />
            </div>

            <div class="space-y-2">
                <label class="block text-xs font-bold text-on-surface-variant uppercase tracking-wider mb-2">Teléfono</label>
                <input name="telefono" class="w-full bg-surface-container-low border-none rounded-xl py-3 px-4 focus:ring-2 focus:ring-primary font-medium text-on-surface" type="text" value="{{ old('telefono', $usuario->telefono) }}"/>
            </div>

            <div class="space-y-2">
                <label class="block text-xs font-bold text-on-surface-variant uppercase tracking-wider mb-2">Estado</label>
                <select name="estado" class="w-full bg-surface-container-low border-none rounded-xl py-3 px-4 focus:ring-2 focus:ring-primary font-medium text-on-surface" required>
                    <option value="activo" {{ $usuario->estado === 'activo' ? 'selected' : '' }}>Activo</option>
                    <option value="inactivo" {{ $usuario->estado === 'inactivo' ? 'selected' : '' }}>Inactivo</option>
                    <option value="baneado" {{ $usuario->estado === 'baneado' ? 'selected' : '' }}>Baneado</option>
                </select>
            </div>

            <div class="space-y-2 md:col-span-2">
                <label class="block text-xs font-bold text-on-surface-variant uppercase tracking-wider mb-2 flex justify-between">
                    <span>Nueva Contraseña</span>
                    <span class="text-[10px] text-stone-400 font-normal normal-case">(Opcional: déjala vacía para mantener la actual)</span>
                </label>
                <input name="password" class="w-full bg-surface-container-low border-none rounded-xl py-3 px-4 focus:ring-2 focus:ring-primary font-medium text-on-surface" type="password" minlength="6" placeholder="Mínimo 6 caracteres"/>
            </div>
        </div>

        <div class="flex gap-4 pt-6">
            <button class="flex-1 bg-primary text-white px-6 py-4 rounded-xl font-extrabold shadow-lg shadow-primary/20 hover:scale-[1.02] transition-transform" type="submit">Guardar Cambios</button>
        </div>
    </form>
</div>
@endsection
