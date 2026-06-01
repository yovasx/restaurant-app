@php
    $roleLabels = [
        '1' => 'Administrador',
        '2' => 'Restaurante',
        'comensal' => 'Comensal',
    ];

    $selectedRole = old('user_type', $fixedRole);
@endphp

<form action="{{ route('admin.usuarios.store') }}" method="POST" class="space-y-6">
    @csrf

    <input type="hidden" name="redirect_to" value="{{ $redirectTo }}">
    <input type="hidden" name="_modal" value="{{ $modalId }}">

    @if($fixedRole)
        <input type="hidden" name="user_type" value="{{ $fixedRole }}">
        <div class="rounded-3xl bg-primary-fixed px-5 py-4 text-primary">
            <p class="text-xs font-bold uppercase tracking-[0.3em]">Tipo de alta</p>
            <p class="mt-1 font-headline text-xl font-extrabold">{{ $roleLabels[$fixedRole] ?? 'Usuario' }}</p>
        </div>
    @endif

    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
        <div class="space-y-2">
            <label class="block text-xs font-bold text-on-surface-variant uppercase tracking-wider">Nombres</label>
            <input name="nombre" class="w-full bg-surface-container-low border-none rounded-xl py-3 px-4 focus:ring-2 focus:ring-primary font-medium text-on-surface" type="text" required data-modal-initial-focus value="{{ old('nombre') }}" placeholder="Nombre o Razón Social" />
        </div>

        <div class="space-y-2 apellido-field">
            <label class="block text-xs font-bold text-on-surface-variant uppercase tracking-wider">Apellido Paterno</label>
            <input name="apellido_paterno" class="w-full bg-surface-container-low border-none rounded-xl py-3 px-4 focus:ring-2 focus:ring-primary font-medium text-on-surface" type="text" value="{{ old('apellido_paterno') }}" placeholder="Solo para comensal"/>
        </div>

        <div class="space-y-2 apellido-field">
            <label class="block text-xs font-bold text-on-surface-variant uppercase tracking-wider">Apellido Materno</label>
            <input name="apellido_materno" class="w-full bg-surface-container-low border-none rounded-xl py-3 px-4 focus:ring-2 focus:ring-primary font-medium text-on-surface" type="text" value="{{ old('apellido_materno') }}" placeholder="Solo para comensal"/>
        </div>

        <div class="space-y-2">
            <label class="block text-xs font-bold text-on-surface-variant uppercase tracking-wider">Correo Electrónico</label>
            <input name="email" class="w-full bg-surface-container-low border-none rounded-xl py-3 px-4 focus:ring-2 focus:ring-primary font-medium text-on-surface" type="email" required value="{{ old('email') }}" placeholder="usuario@correo.com"/>
        </div>

        <div class="space-y-2">
            <label class="block text-xs font-bold text-on-surface-variant uppercase tracking-wider">Teléfono</label>
            <input name="telefono" class="w-full bg-surface-container-low border-none rounded-xl py-3 px-4 focus:ring-2 focus:ring-primary font-medium text-on-surface" type="text" required value="{{ old('telefono') }}" placeholder="70000000"/>
        </div>

        <div class="space-y-2 nit-restaurante-field">
            <label class="block text-xs font-bold text-on-surface-variant uppercase tracking-wider">NIT</label>
            <input name="nit" class="w-full bg-surface-container-low border-none rounded-xl py-3 px-4 focus:ring-2 focus:ring-primary font-medium text-on-surface" type="text" value="{{ old('nit') }}" placeholder="Solo para restaurante"/>
        </div>

        @if(!$fixedRole)
            <div class="space-y-2">
                <label class="block text-xs font-bold text-on-surface-variant uppercase tracking-wider">Tipo de Rol</label>
                <select name="user_type" class="w-full bg-surface-container-low border-none rounded-xl py-3 px-4 focus:ring-2 focus:ring-primary font-medium text-on-surface" required>
                    <option value="" disabled {{ !$selectedRole ? 'selected' : '' }}>Selecciona un rol</option>
                    <option value="1" {{ $selectedRole == '1' ? 'selected' : '' }}>Administrador (staff)</option>
                    <option value="2" {{ $selectedRole == '2' ? 'selected' : '' }}>Restaurante (negocio)</option>
                    <option value="comensal" {{ $selectedRole == 'comensal' ? 'selected' : '' }}>Comensal (cliente)</option>
                </select>
            </div>
        @endif

        <div class="space-y-2 {{ $fixedRole ? 'md:col-span-1' : 'md:col-span-2' }}">
            <label class="block text-xs font-bold text-on-surface-variant uppercase tracking-wider">Contraseña Provisional</label>
            <input name="password" class="w-full bg-surface-container-low border-none rounded-xl py-3 px-4 focus:ring-2 focus:ring-primary font-medium text-on-surface" type="password" required minlength="6" placeholder="Mínimo 6 caracteres"/>
        </div>

        <div class="space-y-2 {{ $fixedRole ? 'md:col-span-1' : 'md:col-span-2' }}">
            <label class="block text-xs font-bold text-on-surface-variant uppercase tracking-wider">Estado Inicial</label>
            <select name="estado" class="w-full bg-surface-container-low border-none rounded-xl py-3 px-4 focus:ring-2 focus:ring-primary font-medium text-on-surface" required>
                <option value="activo" {{ old('estado', 'activo') === 'activo' ? 'selected' : '' }}>Activo</option>
                <option value="inactivo" {{ old('estado') === 'inactivo' ? 'selected' : '' }}>Inactivo</option>
            </select>
        </div>
    </div>

    <div class="flex flex-col-reverse gap-3 pt-2 sm:flex-row sm:justify-end">
        <button type="button" data-modal-close class="w-full rounded-2xl border border-stone-200 px-5 py-3.5 font-bold text-stone-600 transition hover:bg-stone-50 sm:w-auto sm:min-w-40">
            Cancelar
        </button>
        <button class="w-full rounded-2xl bg-primary px-5 py-3.5 font-extrabold text-white shadow-lg shadow-primary/20 transition hover:bg-primary-container sm:w-auto sm:min-w-40" type="submit">
            Crear Usuario
        </button>
    </div>
</form>
