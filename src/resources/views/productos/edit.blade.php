@extends('layouts.restaurante')

@section('title', 'Editar Plato')
@section('page-title', 'Editar Plato')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="mb-8">
        <a href="{{ route('restaurante.dashboard') }}" class="text-sm font-bold text-stone-500 hover:text-[#9e2016] flex items-center gap-1 mb-4">
            <span class="material-symbols-outlined text-[18px]">arrow_back</span> Volver al Panel
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-stone-200 p-8">
        <h2 class="text-2xl font-headline font-bold text-on-surface mb-6 flex items-center gap-2">
            <span class="material-symbols-outlined text-primary">edit</span>
            Editar Plato
        </h2>

        <form action="{{ route('productos.update', $producto) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Foto actual -->
            <div class="space-y-2">
                <label class="text-xs font-bold text-[#59413d] uppercase tracking-wider">Foto del Plato</label>
                <div class="border-2 border-dashed border-stone-300 rounded-xl p-6 text-center transition-all duration-200 cursor-pointer"
                     data-dropzone="true" data-input="foto" data-preview="foto-preview" data-placeholder="foto-placeholder">
                    @if($producto->foto)
                        <img id="foto-preview" class="mx-auto mb-3 h-40 w-40 object-cover rounded-xl shadow" src="{{ asset('storage/'.$producto->foto) }}">
                    @else
                        <img id="foto-preview" class="hidden mx-auto mb-3 h-40 w-40 object-cover rounded-xl shadow" src="">
                    @endif
                    <div id="foto-placeholder" class="{{ $producto->foto ? 'hidden' : '' }}">
                        <span class="material-symbols-outlined text-4xl text-stone-300">add_photo_alternate</span>
                        <p class="text-sm text-stone-500 font-medium mt-2">Haz clic o arrastra para cambiar imagen</p>
                    </div>
                    <p class="text-xs text-stone-400 mt-2">{{ $producto->foto ? 'Haz clic o arrastra para cambiar' : 'PNG, JPG o WEBP — máx. 5MB' }}</p>
                    <input type="file" id="foto" name="foto" class="hidden" accept="image/*">
                </div>
            </div>

            <!-- Nombre -->
            <div class="space-y-2">
                <label class="text-xs font-bold text-[#59413d] uppercase tracking-wider">Nombre del Plato</label>
                <input name="nombre" value="{{ old('nombre', $producto->nombre) }}" required
                    class="w-full bg-[#f3ede7] border-0 rounded-lg py-3 px-4 focus:ring-2 focus:ring-[#9e2016] transition-all"
                    type="text">
            </div>

            <!-- Descripción -->
            <div class="space-y-2">
                <label class="text-xs font-bold text-[#59413d] uppercase tracking-wider">Descripción</label>
                <textarea name="descripcion"
                    class="w-full bg-[#f3ede7] border-0 rounded-lg py-3 px-4 focus:ring-2 focus:ring-[#9e2016] transition-all"
                    rows="3">{{ old('descripcion', $producto->descripcion) }}</textarea>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Precio -->
                <div class="space-y-2">
                    <label class="text-xs font-bold text-[#59413d] uppercase tracking-wider">Precio (Bs.)</label>
                    <input name="precio" value="{{ old('precio', $producto->precio) }}" required min="0" step="0.01"
                        class="w-full bg-[#f3ede7] border-0 rounded-lg py-3 px-4 focus:ring-2 focus:ring-[#9e2016] transition-all"
                        type="number">
                </div>
                <!-- Stock -->
                <div class="space-y-2">
                    <label class="text-xs font-bold text-[#59413d] uppercase tracking-wider">Stock</label>
                    <input name="stock" value="{{ old('stock', $producto->stock) }}" required min="0"
                        class="w-full bg-[#f3ede7] border-0 rounded-lg py-3 px-4 focus:ring-2 focus:ring-[#9e2016] transition-all"
                        type="number">
                </div>
            </div>

            <!-- Categoría -->
            <div class="space-y-2">
                <label class="text-xs font-bold text-[#59413d] uppercase tracking-wider">Categoría</label>
                <select name="categoria_id"
                    class="w-full bg-[#f3ede7] border-0 rounded-lg py-3 px-4 focus:ring-2 focus:ring-[#9e2016] transition-all">
                    <option value="">Sin categoría</option>
                    @foreach($categorias as $cat)
                        <option value="{{ $cat->id }}" {{ old('categoria_id', $producto->categoria_id) == $cat->id ? 'selected' : '' }}>
                            {{ $cat->nombre_categoria }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="pt-4 flex gap-3">
                <a href="{{ route('restaurante.dashboard') }}"
                    class="flex-1 border-2 border-stone-300 text-stone-600 py-4 rounded-xl font-bold text-center hover:bg-stone-50 transition-all">
                    Cancelar
                </a>
                <button type="submit"
                    class="flex-1 bg-[#9e2016] text-white py-4 rounded-xl font-bold shadow-lg hover:scale-[1.02] active:scale-95 transition-all">
                    Actualizar Plato
                </button>
            </div>
        </form>
    </div>
</div>

@include('partials.dropzone')
@endsection
