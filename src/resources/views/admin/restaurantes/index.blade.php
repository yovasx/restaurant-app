@extends('layouts.admin')

@section('content')
<div class="flex justify-between items-end mb-6">
    <div>
        <h2 class="text-4xl font-extrabold text-on-surface tracking-tight mb-2">Directorio de Locales</h2>
        <p class="text-on-surface-variant font-medium">Gestión de restaurantes registrados.</p>
    </div>
    <div>
        <a href="{{ route('admin.usuarios.create', ['role' => '2']) }}" class="bg-primary text-white px-6 py-3 rounded-xl font-bold flex items-center justify-center gap-2 shadow-lg hover:scale-105 transition-transform">
            <span class="material-symbols-outlined">add_business</span>
            Añadir Restaurante
        </a>
    </div>
</div>

<div class="flex gap-4 mb-6">
    <a href="{{ route('admin.restaurantes.index', ['tab' => 'activos']) }}" class="px-6 py-2 rounded-full font-bold text-sm transition-all {{ $tab === 'activos' ? 'bg-primary text-white shadow-md' : 'bg-surface-container-high text-on-surface-variant hover:bg-surface-dim' }}">
        Restaurantes Activos
    </a>
    <a href="{{ route('admin.restaurantes.index', ['tab' => 'inactivos']) }}" class="px-6 py-2 rounded-full font-bold text-sm transition-all {{ $tab === 'inactivos' ? 'bg-primary text-white shadow-md' : 'bg-surface-container-high text-on-surface-variant hover:bg-surface-dim' }}">
        Inactivos / Eliminados
    </a>
</div>

<div class="bg-surface-container-lowest rounded-3xl overflow-hidden shadow-sm">
    <table class="w-full text-left border-collapse">
        <thead>
            <tr class="bg-surface-container-high/50">
                <th class="px-6 py-5 text-sm font-bold uppercase tracking-wider text-on-surface-variant">Restaurante</th>
                <th class="px-6 py-5 text-sm font-bold uppercase tracking-wider text-on-surface-variant">Contacto</th>
                <th class="px-6 py-5 text-sm font-bold uppercase tracking-wider text-on-surface-variant">Estado</th>
                <th class="px-6 py-5 text-sm font-bold uppercase tracking-wider text-on-surface-variant text-right">Rol</th>
                <th class="px-6 py-5 text-sm font-bold uppercase tracking-wider text-on-surface-variant text-right">Acciones</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-surface-container-high">
            @foreach($restaurantes as $rest)
            <tr class="group hover:bg-surface-container-low transition-colors">
                <td class="px-6 py-5">
                    <div class="font-bold text-on-surface text-lg">{{ $rest->nombre }}</div>
                    <div class="text-xs text-on-surface-variant font-medium">Registrado: {{ $rest->created_at->format('d/m/Y') }}</div>
                </td>
                <td class="px-6 py-5">
                    <div class="text-sm font-medium text-on-surface">{{ $rest->email }}</div>
                    <div class="text-xs text-stone-500">{{ $rest->telefono }}</div>
                </td>
                <td class="px-6 py-5">
                    @if($rest->estado === 'activo')
                        <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs font-bold uppercase">Activo</span>
                    @else
                        <span class="px-3 py-1 bg-amber-100 text-amber-700 rounded-full text-xs font-bold uppercase">{{ $rest->estado }}</span>
                    @endif
                </td>
                <td class="px-6 py-5 text-right">
                    <form action="{{ route('admin.roles.change') }}" method="POST" class="inline-flex items-center gap-2">
                        @csrf
                        <input type="hidden" name="user_type" value="usuario">
                        <input type="hidden" name="user_id" value="{{ $rest->id }}">
                        <select name="new_role" class="text-xs border-stone-300 rounded p-1 bg-surface-container-low" onchange="if(confirm('¿Modificar rol? Esto podría migrar los datos del usuario.')) this.form.submit()">
                            <option value="2" selected>Restaurante</option>
                            <option value="1">Administrador</option>
                            <option value="comensal">Comensal</option>
                        </select>
                    </form>
                </td>
                <td class="px-6 py-5 text-right">
                    <div class="flex items-center justify-end gap-3">
                        <a href="{{ route('admin.restaurantes.edit', $rest->id) }}" class="p-2 rounded-lg bg-surface-container-highest text-primary hover:bg-primary-container hover:text-white transition-all shadow-sm" title="Editar Información">
                            <span class="material-symbols-outlined text-[20px]">edit</span>
                        </a>
                        @if($rest->estado !== 'inactivo')
                        <form action="{{ route('admin.restaurantes.destroy', $rest->id) }}" method="POST" onsubmit="return confirm('¿Seguro que deseas mover este restaurante a inactivos?');" class="inline">
                            @csrf
                            <button type="submit" class="p-2 rounded-lg bg-surface-container-highest text-red-600 hover:bg-red-600 hover:text-white transition-all shadow-sm" title="Inactivar/Eliminar">
                                <span class="material-symbols-outlined text-[20px]">delete</span>
                            </button>
                        </form>
                        @endif
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <div class="p-6">
        {{ $restaurantes->links() }}
    </div>
</div>
@endsection
