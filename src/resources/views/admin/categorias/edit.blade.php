@extends('layouts.admin')

@section('content')
<div class="px-4 max-w-2xl">
    <div class="mb-8">
        <a href="{{ route('admin.categorias.index') }}" class="text-sm font-bold text-stone-500 hover:text-[#9e2016] flex items-center gap-1 mb-4">
            <span class="material-symbols-outlined text-[18px]">arrow_back</span> Volver a Categorías
        </a>
        <h1 class="text-3xl font-headline font-bold text-on-surface tracking-tight">Editar Categoría</h1>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-stone-200 p-8">
        <form action="{{ route('admin.categorias.update', $categoria->id) }}" method="POST" class="space-y-6">
            @csrf
            
            <div class="space-y-2">
                <label class="text-xs font-bold text-stone-500 uppercase tracking-widest">Nombre</label>
                <input name="nombre_categoria" value="{{ old('nombre_categoria', $categoria->nombre_categoria) }}" class="w-full bg-[#f3ede7] border-0 rounded-lg py-3 px-4 focus:ring-2 focus:ring-[#9e2016] transition-all" type="text" required>
            </div>
            
            <div class="space-y-2">
                <label class="text-xs font-bold text-stone-500 uppercase tracking-widest">Descripción</label>
                <input name="descripcion" value="{{ old('descripcion', $categoria->descripcion) }}" class="w-full bg-[#f3ede7] border-0 rounded-lg py-3 px-4 focus:ring-2 focus:ring-[#9e2016] transition-all" type="text">
            </div>

            <div class="space-y-2">
                <label class="text-xs font-bold text-stone-500 uppercase tracking-widest">Estado</label>
                <select name="estado" class="w-full bg-[#f3ede7] border-0 rounded-lg py-3 px-4 focus:ring-2 focus:ring-[#9e2016]">
                    <option value="activo" {{ $categoria->estado === 'activo' ? 'selected' : '' }}>Activo</option>
                    <option value="inactivo" {{ $categoria->estado === 'inactivo' ? 'selected' : '' }}>Inactivo</option>
                </select>
            </div>

            <div class="pt-6">
                <button type="submit" class="w-full bg-[#9e2016] text-white py-4 rounded-xl font-bold text-lg shadow-lg shadow-red-900/20 hover:scale-[1.02] active:scale-95 transition-all">
                    Guardar Cambios
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
