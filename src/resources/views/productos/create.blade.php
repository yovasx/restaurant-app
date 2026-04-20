@extends('layouts.restaurante')

@section('title', 'Nuevo Plato')
@section('page-title', 'Nuevo Plato')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="mb-8">
        <a href="{{ route('restaurante.dashboard') }}" class="text-sm font-bold text-stone-500 hover:text-[#9e2016] flex items-center gap-1 mb-4">
            <span class="material-symbols-outlined text-[18px]">arrow_back</span> Volver al Panel
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-stone-200 p-8">
        <h2 class="text-2xl font-headline font-bold text-on-surface mb-6 flex items-center gap-2">
            <span class="material-symbols-outlined text-primary">restaurant</span>
            Añadir Nuevo Plato
        </h2>

        <form action="{{ route('productos.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <!-- Foto del plato -->
            <div class="space-y-2">
                <label class="text-xs font-bold text-[#59413d] uppercase tracking-wider">Foto del Plato</label>
                <div class="border-2 border-dashed border-stone-300 rounded-xl p-8 text-center transition-all duration-200 cursor-pointer"
                     data-dropzone="true" data-input="foto" data-preview="foto-preview" data-placeholder="foto-placeholder">
                    <img id="foto-preview" class="hidden mx-auto mb-4 h-40 w-40 object-cover rounded-xl shadow" src="">
                    <div id="foto-placeholder">
                        <span class="material-symbols-outlined text-4xl text-stone-300 mb-2">add_photo_alternate</span>
                        <p class="text-sm text-stone-500 font-medium">Haz clic o arrastra la imagen del plato aquí</p>
                        <p class="text-xs text-stone-400 mt-1">PNG, JPG o WEBP — máx. 5MB</p>
                    </div>
                    <input type="file" id="foto" name="foto" class="hidden" accept="image/*">
                </div>
            </div>

            <!-- Nombre -->
            <div class="space-y-2">
                <label class="text-xs font-bold text-[#59413d] uppercase tracking-wider">Nombre del Plato</label>
                <input name="nombre" value="{{ old('nombre') }}" required
                    class="w-full bg-[#f3ede7] border-0 rounded-lg py-3 px-4 focus:ring-2 focus:ring-[#9e2016] transition-all"
                    type="text" placeholder="Ej: Tartar de Atún">
            </div>

            <!-- Descripción -->
            <div class="space-y-2">
                <label class="text-xs font-bold text-[#59413d] uppercase tracking-wider">Descripción</label>
                <textarea name="descripcion"
                    class="w-full bg-[#f3ede7] border-0 rounded-lg py-3 px-4 focus:ring-2 focus:ring-[#9e2016] transition-all"
                    rows="3" placeholder="Marinado con cítricos, servido frío...">{{ old('descripcion') }}</textarea>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Precio -->
                <div class="space-y-2">
                    <label class="text-xs font-bold text-[#59413d] uppercase tracking-wider">Precio (Bs.)</label>
                    <input name="precio" value="{{ old('precio') }}" required min="0" step="0.01"
                        class="w-full bg-[#f3ede7] border-0 rounded-lg py-3 px-4 focus:ring-2 focus:ring-[#9e2016] transition-all"
                        type="number" placeholder="18.50">
                </div>
                <!-- Stock -->
                <div class="space-y-2">
                    <label class="text-xs font-bold text-[#59413d] uppercase tracking-wider">Stock (unidades)</label>
                    <input name="stock" value="{{ old('stock', 0) }}" required min="0"
                        class="w-full bg-[#f3ede7] border-0 rounded-lg py-3 px-4 focus:ring-2 focus:ring-[#9e2016] transition-all"
                        type="number" placeholder="50">
                </div>
            </div>

            <!-- Categoría -->
            <div class="space-y-2">
                <label class="text-xs font-bold text-[#59413d] uppercase tracking-wider">Categoría</label>
                <select name="categoria_id"
                    class="w-full bg-[#f3ede7] border-0 rounded-lg py-3 px-4 focus:ring-2 focus:ring-[#9e2016] transition-all">
                    <option value="">Sin categoría</option>
                    @foreach($categorias as $cat)
                        <option value="{{ $cat->id }}" {{ old('categoria_id') == $cat->id ? 'selected' : '' }}>
                            {{ $cat->nombre_categoria }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="pt-4">
                <button type="submit"
                    class="w-full bg-[#9e2016] text-white py-4 rounded-xl font-bold text-lg shadow-lg hover:scale-[1.02] active:scale-95 transition-all">
                    Guardar Plato
                </button>
            </div>
        </form>
    </div>
</div>

@include('partials.dropzone')
@endsection
