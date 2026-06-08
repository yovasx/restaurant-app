@extends('layouts.admin')

@section('content')
<header class="mb-6 flex items-center justify-between">
    <div>
        <h2 class="text-3xl font-extrabold text-on-surface tracking-tight mb-1">Auditoría</h2>
        <p class="text-on-surface-variant font-medium">Bitácora de acciones administrativas.</p>
    </div>
</header>

<form method="GET" action="{{ route('admin.auditoria.index') }}" class="bg-surface-container-lowest p-5 rounded-2xl shadow-sm border border-stone-100/50 mb-8">
    <div class="grid grid-cols-2 lg:grid-cols-5 gap-4">
        <div>
            <label class="text-[10px] font-bold text-stone-500 uppercase tracking-wider mb-1 block">Módulo</label>
            <select name="modulo" class="w-full bg-surface-container-highest border-0 rounded-lg p-3 focus:ring-2 focus:ring-primary focus:bg-surface-container-lowest transition-all font-medium text-sm">
                <option value="">Todos</option>
                @foreach($modulos as $m)
                    <option value="{{ $m }}" {{ request('modulo') === $m ? 'selected' : '' }}>{{ ucfirst($m) }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="text-[10px] font-bold text-stone-500 uppercase tracking-wider mb-1 block">Acción</label>
            <select name="accion" class="w-full bg-surface-container-highest border-0 rounded-lg p-3 focus:ring-2 focus:ring-primary focus:bg-surface-container-lowest transition-all font-medium text-sm">
                <option value="">Todas</option>
                @foreach($acciones as $a)
                    <option value="{{ $a }}" {{ request('accion') === $a ? 'selected' : '' }}>{{ str_replace('_', ' ', ucfirst($a)) }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="text-[10px] font-bold text-stone-500 uppercase tracking-wider mb-1 block">Admin</label>
            <select name="usuario_id" class="w-full bg-surface-container-highest border-0 rounded-lg p-3 focus:ring-2 focus:ring-primary focus:bg-surface-container-lowest transition-all font-medium text-sm">
                <option value="">Todos</option>
                @foreach($admins as $admin)
                    <option value="{{ $admin->id }}" {{ request('usuario_id') == $admin->id ? 'selected' : '' }}>{{ $admin->nombre }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="text-[10px] font-bold text-stone-500 uppercase tracking-wider mb-1 block">Desde</label>
            <input type="date" name="from" value="{{ request('from') }}" class="w-full bg-surface-container-highest border-0 rounded-lg p-3 focus:ring-2 focus:ring-primary focus:bg-surface-container-lowest transition-all font-medium text-sm">
        </div>
        <div>
            <label class="text-[10px] font-bold text-stone-500 uppercase tracking-wider mb-1 block">Hasta</label>
            <input type="date" name="to" value="{{ request('to') }}" class="w-full bg-surface-container-highest border-0 rounded-lg p-3 focus:ring-2 focus:ring-primary focus:bg-surface-container-lowest transition-all font-medium text-sm">
        </div>
    </div>
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mt-4">
        <div class="lg:col-span-2">
            <label class="text-[10px] font-bold text-stone-500 uppercase tracking-wider mb-1 block">Búsqueda</label>
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Buscar en descripción o metadatos..." class="w-full bg-surface-container-highest border-0 rounded-lg p-3 focus:ring-2 focus:ring-primary focus:bg-surface-container-lowest transition-all font-medium text-sm">
        </div>
        <div>
            <label class="text-[10px] font-bold text-stone-500 uppercase tracking-wider mb-1 block">Entidad</label>
            <select name="entidad" class="w-full bg-surface-container-highest border-0 rounded-lg p-3 focus:ring-2 focus:ring-primary focus:bg-surface-container-lowest transition-all font-medium text-sm">
                <option value="">Todas</option>
                @foreach($entidades as $ent)
                    <option value="{{ $ent }}" {{ request('entidad') === $ent ? 'selected' : '' }}>{{ ucfirst($ent) }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="text-[10px] font-bold text-stone-500 uppercase tracking-wider mb-1 block">ID Entidad</label>
            <input type="text" name="entidad_id" value="{{ request('entidad_id') }}" placeholder="Ej: 42" class="w-full bg-surface-container-highest border-0 rounded-lg p-3 focus:ring-2 focus:ring-primary focus:bg-surface-container-lowest transition-all font-medium text-sm">
        </div>
    </div>
    <div class="flex gap-3 mt-4">
        <button type="submit" class="flex-1 lg:flex-none bg-primary text-on-primary px-6 py-3 rounded-xl font-bold text-sm shadow-lg shadow-primary/20 hover:bg-primary-container transition-all">Filtrar</button>
        <a href="{{ route('admin.auditoria.index') }}" class="flex-1 lg:flex-none text-center bg-stone-100 text-stone-600 px-6 py-3 rounded-xl font-bold text-sm hover:bg-stone-200 transition-all">Limpiar</a>
    </div>
</form>

<div class="bg-surface-container-lowest rounded-2xl shadow-sm border border-stone-100/50 overflow-hidden">
    @if($eventos->count() > 0)
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-stone-50 text-left">
                        <th class="px-5 py-4 text-[10px] font-bold text-stone-500 uppercase tracking-wider">Fecha</th>
                        <th class="px-5 py-4 text-[10px] font-bold text-stone-500 uppercase tracking-wider">Admin</th>
                        <th class="px-5 py-4 text-[10px] font-bold text-stone-500 uppercase tracking-wider">Módulo</th>
                        <th class="px-5 py-4 text-[10px] font-bold text-stone-500 uppercase tracking-wider">Acción</th>
                        <th class="px-5 py-4 text-[10px] font-bold text-stone-500 uppercase tracking-wider">Entidad</th>
                        <th class="px-5 py-4 text-[10px] font-bold text-stone-500 uppercase tracking-wider">Descripción</th>
                        <th class="px-5 py-4 text-[10px] font-bold text-stone-500 uppercase tracking-wider w-10"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100">
                    @foreach($eventos as $e)
                    <tr class="hover:bg-stone-50/50 transition-colors cursor-pointer" onclick="const r=this.nextElementSibling; if(r)r.classList.toggle('hidden')">
                        <td class="px-5 py-4 text-xs text-stone-600 whitespace-nowrap">{{ $e->created_at->format('d/m/Y H:i') }}</td>
                        <td class="px-5 py-4 text-sm font-medium text-on-surface whitespace-nowrap">{{ $e->usuario?->nombre ?? '—' }}</td>
                        <td class="px-5 py-4 whitespace-nowrap">
                            <span class="inline-block px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider 
                                {{ $e->modulo === 'backups' ? 'bg-purple-100 text-purple-700' : '' }}
                                {{ $e->modulo === 'reportes' ? 'bg-blue-100 text-blue-700' : '' }}
                                {{ $e->modulo === 'restaurantes' ? 'bg-orange-100 text-orange-700' : '' }}
                                {{ $e->modulo === 'comensales' ? 'bg-teal-100 text-teal-700' : '' }}
                                {{ $e->modulo === 'categorias' ? 'bg-pink-100 text-pink-700' : '' }}
                                {{ $e->modulo === 'roles' ? 'bg-indigo-100 text-indigo-700' : '' }}
                                {{ $e->modulo === 'usuarios' ? 'bg-stone-100 text-stone-700' : '' }}
                                {{ $e->modulo === 'auth' ? 'bg-red-100 text-red-700' : '' }}
                            ">{{ $e->modulo }}</span>
                        </td>
                        <td class="px-5 py-4 text-sm text-stone-700 whitespace-nowrap">{{ $e->accionLabel() }}</td>
                        <td class="px-5 py-4 text-sm text-stone-600 whitespace-nowrap">{{ $e->entidad ?? '—' }} {{ $e->entidad_id ? "#{$e->entidad_id}" : '' }}</td>
                        <td class="px-5 py-4 text-sm text-stone-600 max-w-xs truncate" title="{{ $e->descripcion }}">{{ $e->descripcion ?? '—' }}</td>
                        <td class="px-5 py-4 text-center">
                            <span class="text-stone-400 text-xs font-bold">+</span>
                        </td>
                    </tr>
                    <tr class="hidden">
                        <td colspan="7" class="px-5 py-4 bg-stone-50/70">
                            <div class="grid grid-cols-2 gap-x-6 gap-y-2 text-sm">
                                <div><span class="font-semibold text-stone-500 text-[10px] uppercase tracking-wider">Fecha exacta</span><br><span class="text-stone-700">{{ $e->created_at->format('d/m/Y H:i:s') }}</span></div>
                                <div><span class="font-semibold text-stone-500 text-[10px] uppercase tracking-wider">IP</span><br><span class="text-stone-700">{{ $e->meta['ip'] ?? '—' }}</span></div>
                                <div><span class="font-semibold text-stone-500 text-[10px] uppercase tracking-wider">Admin</span><br><span class="text-stone-700">{{ $e->usuario?->nombre ?? '—' }} (ID {{ $e->usuario_id }})</span></div>
                                <div><span class="font-semibold text-stone-500 text-[10px] uppercase tracking-wider">Ruta</span><br><span class="text-stone-700">{{ $e->meta['ruta'] ?? '—' }}</span></div>
                                <div><span class="font-semibold text-stone-500 text-[10px] uppercase tracking-wider">Entidad</span><br><span class="text-stone-700">{{ $e->entidad ?? '—' }} {{ $e->entidad_id ? "#{$e->entidad_id}" : '' }}</span></div>
                                <div><span class="font-semibold text-stone-500 text-[10px] uppercase tracking-wider">Route name</span><br><span class="text-stone-700">{{ $e->meta['route_name'] ?? '—' }}</span></div>
                                @if($e->descripcion)
                                <div class="col-span-2"><span class="font-semibold text-stone-500 text-[10px] uppercase tracking-wider">Descripción</span><br><span class="text-stone-700">{{ $e->descripcion }}</span></div>
                                @endif
                                @php $cambios = $e->cambios(); @endphp
                                @if(!empty($cambios))
                                <div class="col-span-2 mt-2">
                                    <span class="font-semibold text-stone-500 text-[10px] uppercase tracking-wider">Cambios</span>
                                    <div class="mt-1 space-y-1">
                                        @foreach($cambios as $c)
                                        <div class="text-xs flex items-center gap-2 bg-white px-3 py-1.5 rounded-lg border border-stone-100">
                                            <span class="font-medium text-stone-600 min-w-[100px]">{{ $c['campo'] }}:</span>
                                            <span class="line-through text-red-500">{{ is_scalar($c['antes']) ? $c['antes'] : (is_null($c['antes']) ? '—' : json_encode($c['antes'])) }}</span>
                                            <span class="text-stone-300">→</span>
                                            <span class="text-green-600 font-medium">{{ is_scalar($c['despues']) ? $c['despues'] : (is_null($c['despues']) ? '—' : json_encode($c['despues'])) }}</span>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-5 py-4 border-t border-stone-100">
            {{ $eventos->appends(request()->query())->onEachSide(1)->links() }}
        </div>
    @else
        <div class="text-center py-12">
            <span class="material-symbols-outlined text-4xl text-stone-300 mb-3">history</span>
            <p class="text-stone-500 font-medium">No se encontraron eventos de auditoría.</p>
        </div>
    @endif
</div>
@endsection
