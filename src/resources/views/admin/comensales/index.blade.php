@extends('layouts.admin')

@section('content')
<div class="flex justify-between items-end mb-6">
    <div>
        <h2 class="text-4xl font-extrabold text-on-surface tracking-tight mb-2">Gestión de Comensales</h2>
        <p class="text-on-surface-variant font-medium">Revisa y actualiza la información de los usuarios registrados.</p>
    </div>
</div>

<div class="flex gap-4 mb-6">
    <a href="{{ route('admin.comensales.index', ['tab' => 'activos']) }}" class="px-6 py-2 rounded-full font-bold text-sm transition-all {{ $tab === 'activos' ? 'bg-primary text-white shadow-md' : 'bg-surface-container-high text-on-surface-variant hover:bg-surface-dim' }}">
        Usuarios Activos
    </a>
    <a href="{{ route('admin.comensales.index', ['tab' => 'inactivos']) }}" class="px-6 py-2 rounded-full font-bold text-sm transition-all {{ $tab === 'inactivos' ? 'bg-primary text-white shadow-md' : 'bg-surface-container-high text-on-surface-variant hover:bg-surface-dim' }}">
        Inactivos / Eliminados
    </a>
</div>

<div class="bg-surface-container-lowest rounded-3xl overflow-hidden shadow-sm">
    <table class="w-full text-left border-collapse">
        <thead>
            <tr class="bg-surface-container-low text-on-surface-variant text-[11px] uppercase tracking-widest font-bold">
                <th class="px-6 py-4">Comensal</th>
                <th class="px-6 py-4">Contacto</th>
                <th class="px-6 py-4">Estado</th>
                <th class="px-6 py-4 text-right">Rol</th>
                <th class="px-6 py-4 text-right">Edición</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-surface-container-low">
            @foreach($comensales as $comensal)
            <tr class="hover:bg-surface-container-low/30 transition-colors group">
                <td class="px-6 py-5">
                    <p class="font-bold text-on-surface">{{ $comensal->nombre }}</p>
                    <p class="text-xs text-stone-400">ID: #{{ $comensal->id }}</p>
                </td>
                <td class="px-6 py-5">
                    <div class="space-y-1">
                        <p class="text-sm font-medium text-on-surface-variant">{{ $comensal->email }}</p>
                        <p class="text-xs text-stone-500">{{ $comensal->telefono }}</p>
                    </div>
                </td>
                <td class="px-6 py-5">
                    @if($comensal->estado === 'activo')
                        <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-[10px] font-black uppercase tracking-tight">Activo</span>
                    @else
                        <span class="px-3 py-1 bg-red-100 text-red-700 rounded-full text-[10px] font-black uppercase tracking-tight">{{ $comensal->estado }}</span>
                    @endif
                </td>
                <td class="px-6 py-5 text-right">
                    <form action="{{ route('admin.roles.change') }}" method="POST" class="inline-flex items-center gap-2">
                        @csrf
                        <input type="hidden" name="user_type" value="comensal">
                        <input type="hidden" name="user_id" value="{{ $comensal->id }}">
                        <select name="new_role" class="text-xs border-stone-300 rounded p-1 bg-surface-container-low" onchange="if(confirm('¿Migrar Comensal a otro rol? Esto moverá sus datos.')) this.form.submit()">
                            <option value="comensal" selected>Comensal</option>
                            <option value="2">Restaurante</option>
                            <option value="1">Admin</option>
                        </select>
                    </form>
                </td>
                <td class="px-6 py-5 text-right">
                    <div class="flex items-center justify-end gap-3">
                        <a href="{{ route('admin.comensales.edit', $comensal->id) }}" class="p-2 rounded-lg bg-surface-container-highest text-primary hover:bg-primary-container hover:text-white transition-all shadow-sm" title="Editar Información">
                            <span class="material-symbols-outlined text-[20px]">edit</span>
                        </a>
                        @if($comensal->estado !== 'inactivo')
                        <form action="{{ route('admin.comensales.destroy', $comensal->id) }}" method="POST" onsubmit="return confirm('¿Seguro que deseas mover este usuario a inactivos?');" class="inline">
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
        {{ $comensales->links() }}
    </div>
</div>
@endsection
