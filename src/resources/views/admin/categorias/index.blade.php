@extends('layouts.admin')

@section('content')
<div class="px-4">
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-headline font-bold text-on-surface tracking-tight">Gestión de Categorías</h1>
            <p class="text-on-surface-variant font-medium mt-1">Administra los tipos de gastronomía y opciones</p>
        </div>
    </div>

    <!-- Create Category Form -->
    <div class="bg-white rounded-xl shadow-sm border border-stone-200 p-6 mb-8">
        <h2 class="text-xl font-bold mb-4 flex items-center gap-2 text-[#9e2016]">
            <span class="material-symbols-outlined">add_circle</span>
            Nueva Categoría
        </h2>
        <form action="{{ route('admin.categorias.store') }}" method="POST" class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
            @csrf
            <div class="space-y-2">
                <label class="text-xs font-bold text-stone-500 uppercase tracking-widest">Nombre</label>
                <input name="nombre_categoria" class="w-full bg-[#f3ede7] border-0 rounded-lg py-3 px-4 focus:ring-2 focus:ring-[#9e2016] transition-all" placeholder="Ej: Pizzería, Sushi..." type="text" required>
            </div>
            <div class="space-y-2">
                <label class="text-xs font-bold text-stone-500 uppercase tracking-widest">Descripción</label>
                <input name="descripcion" class="w-full bg-[#f3ede7] border-0 rounded-lg py-3 px-4 focus:ring-2 focus:ring-[#9e2016] transition-all" placeholder="Detalles extra (opcional)" type="text">
            </div>
            <div>
                <button type="submit" class="w-full bg-[#9e2016] text-white py-3 px-4 rounded-lg font-bold shadow hover:bg-red-800 transition-colors">
                    Crear Categoría
                </button>
            </div>
        </form>
    </div>

    <!-- Tabs -->
    <div class="flex border-b border-stone-200 mb-6 gap-8">
        <a href="{{ route('admin.categorias.index', ['tab' => 'activos']) }}" class="pb-3 text-sm font-bold tracking-wide {{ $tab === 'activos' ? 'text-[#C0392B] border-b-2 border-[#C0392B]' : 'text-stone-500 hover:text-stone-700' }}">
            Categorías Activas
        </a>
        <a href="{{ route('admin.categorias.index', ['tab' => 'inactivos']) }}" class="pb-3 text-sm font-bold tracking-wide {{ $tab === 'inactivos' ? 'text-stone-400 border-b-2 border-stone-400' : 'text-stone-500 hover:text-stone-700' }}">
            Archivo (Inactivas)
        </a>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-xl shadow-sm border border-stone-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-stone-50 border-b border-stone-200">
                        <th class="px-6 py-4 text-xs font-bold text-stone-500 uppercase tracking-wider">ID</th>
                        <th class="px-6 py-4 text-xs font-bold text-stone-500 uppercase tracking-wider">Categoría</th>
                        <th class="px-6 py-4 text-xs font-bold text-stone-500 uppercase tracking-wider">Descripción</th>
                        <th class="px-6 py-4 text-xs font-bold text-stone-500 uppercase tracking-wider">Fecha Creación</th>
                        <th class="px-6 py-4 text-xs font-bold text-stone-500 uppercase tracking-wider text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100">
                    @forelse($categorias as $cat)
                    <tr class="hover:bg-stone-50/50 transition-colors">
                        <td class="px-6 py-4 text-sm font-bold text-stone-500">#{{ $cat->id }}</td>
                        <td class="px-6 py-4 text-sm font-bold text-on-surface">{{ $cat->nombre_categoria }}</td>
                        <td class="px-6 py-4 text-sm text-stone-600">{{ $cat->descripcion ?? 'N/A' }}</td>
                        <td class="px-6 py-4 text-sm text-stone-500">{{ $cat->created_at->format('d M Y') }}</td>
                        <td class="px-6 py-4 text-right space-x-2">
                            <a href="{{ route('admin.categorias.edit', $cat->id) }}" class="inline-flex p-2 text-stone-400 hover:text-primary transition-colors bg-stone-100 rounded-lg hover:bg-red-50" title="Editar">
                                <span class="material-symbols-outlined text-[20px]">edit</span>
                            </a>
                            @if($cat->estado !== 'inactivo')
                            <form action="{{ route('admin.categorias.destroy', $cat->id) }}" method="POST" class="inline-block" onsubmit="return confirm('¿Inactivar esta categoría?');">
                                @csrf
                                <button type="submit" class="inline-flex p-2 text-stone-400 hover:text-red-600 transition-colors bg-stone-100 rounded-lg hover:bg-red-50" title="Eliminar/Archivar">
                                    <span class="material-symbols-outlined text-[20px]">archive</span>
                                </button>
                            </form>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-stone-500">
                            <span class="material-symbols-outlined text-4xl mb-2 opacity-50">category</span>
                            <p>No hay categorías {{ $tab === 'inactivos' ? 'inactivas' : 'activas' }}.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-stone-200">
            {{ $categorias->links() }}
        </div>
    </div>
</div>
@endsection
