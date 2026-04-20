@extends('layouts.restaurante')

@section('title', 'Editar Promoción')
@section('page-title', 'Editar Promoción')

@section('content')
<div class="max-w-2xl mx-auto">
    <a href="{{ route('restaurante.promociones.index') }}" class="text-sm font-bold text-stone-500 hover:text-primary flex items-center gap-1 mb-6">
        <span class="material-symbols-outlined text-[18px]">arrow_back</span> Volver a Promociones
    </a>

    <div class="bg-white rounded-xl shadow-sm border border-stone-200 p-8">
        <h2 class="text-2xl font-headline font-bold text-on-surface mb-6 flex items-center gap-2">
            <span class="material-symbols-outlined text-primary">edit</span>
            Editar Promoción
        </h2>

        <form action="{{ route('restaurante.promociones.update', $promocion) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf @method('PUT')

            <div class="space-y-2">
                <label class="text-xs font-bold text-[#59413d] uppercase tracking-wider">Nombre de la Oferta</label>
                <input name="nombre" value="{{ old('nombre', $promocion->nombre) }}" required
                    class="w-full bg-[#f3ede7] border-0 rounded-lg py-3 px-4 focus:ring-2 focus:ring-[#9e2016] transition-all"
                    type="text">
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-2">
                    <label class="text-xs font-bold text-[#59413d] uppercase tracking-wider">Tipo de Descuento</label>
                    <select name="tipo" class="w-full bg-[#f3ede7] border-0 rounded-lg py-3 px-4 focus:ring-2 focus:ring-[#9e2016] transition-all">
                        <option value="descuento" {{ old('tipo', $promocion->tipo) == 'descuento' ? 'selected' : '' }}>Porcentaje (%)</option>
                        <option value="2x1"       {{ old('tipo', $promocion->tipo) == '2x1'       ? 'selected' : '' }}>2x1</option>
                        <option value="postre"    {{ old('tipo', $promocion->tipo) == 'postre'    ? 'selected' : '' }}>Postre de regalo</option>
                        <option value="otro"      {{ old('tipo', $promocion->tipo) == 'otro'      ? 'selected' : '' }}>Otro</option>
                    </select>
                </div>
                <div class="space-y-2">
                    <label class="text-xs font-bold text-[#59413d] uppercase tracking-wider">Valor (%)</label>
                    <input name="valor" value="{{ old('valor', $promocion->valor) }}" min="0" step="0.01"
                        class="w-full bg-[#f3ede7] border-0 rounded-lg py-3 px-4 focus:ring-2 focus:ring-[#9e2016] transition-all"
                        type="number">
                </div>
            </div>

            <div class="space-y-2">
                <label class="text-xs font-bold text-[#59413d] uppercase tracking-wider">Condición o Detalle</label>
                <textarea name="condicion" rows="3"
                    class="w-full bg-[#f3ede7] border-0 rounded-lg py-3 px-4 focus:ring-2 focus:ring-[#9e2016] transition-all">{{ old('condicion', $promocion->condicion) }}</textarea>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="space-y-2">
                    <label class="text-xs font-bold text-[#59413d] uppercase tracking-wider">Fecha de Inicio</label>
                    <input name="fecha_inicio" value="{{ old('fecha_inicio', $promocion->fecha_inicio?->format('Y-m-d')) }}"
                        class="w-full bg-[#f3ede7] border-0 rounded-lg py-3 px-4 focus:ring-2 focus:ring-[#9e2016] transition-all"
                        type="date">
                </div>
                <div class="space-y-2">
                    <label class="text-xs font-bold text-[#59413d] uppercase tracking-wider">Fecha de Fin</label>
                    <input name="fecha_fin" value="{{ old('fecha_fin', $promocion->fecha_fin?->format('Y-m-d')) }}"
                        class="w-full bg-[#f3ede7] border-0 rounded-lg py-3 px-4 focus:ring-2 focus:ring-[#9e2016] transition-all"
                        type="date">
                </div>
                <div class="space-y-2">
                    <label class="text-xs font-bold text-[#59413d] uppercase tracking-wider">Estado</label>
                    <select name="estado" class="w-full bg-[#f3ede7] border-0 rounded-lg py-3 px-4 focus:ring-2 focus:ring-[#9e2016] transition-all">
                        <option value="activo"   {{ old('estado', $promocion->estado) == 'activo'   ? 'selected' : '' }}>Activo</option>
                        <option value="inactivo" {{ old('estado', $promocion->estado) == 'inactivo' ? 'selected' : '' }}>Inactivo</option>
                    </select>
                </div>
            </div>

            <!-- Imagen de la promo -->
            <div class="space-y-2">
                <label class="text-xs font-bold text-[#59413d] uppercase tracking-wider">Imagen Promocional</label>
                <div class="border-2 border-dashed border-stone-300 rounded-xl p-6 text-center transition-all duration-200 cursor-pointer"
                     data-dropzone="true" data-input="imagen" data-preview="promo-preview" data-placeholder="promo-placeholder">
                    @if($promocion->imagen)
                        <img id="promo-preview" class="mx-auto mb-3 h-36 w-full object-cover rounded-xl shadow" src="{{ asset('storage/'.$promocion->imagen) }}">
                    @else
                        <img id="promo-preview" class="hidden mx-auto mb-3 h-36 w-full object-cover rounded-xl shadow" src="">
                    @endif
                    <div id="promo-placeholder" class="{{ $promocion->imagen ? 'hidden' : '' }}">
                        <span class="material-symbols-outlined text-4xl text-stone-300">add_photo_alternate</span>
                        <p class="text-sm text-stone-500 font-medium mt-2">{{ $promocion->imagen ? 'Haz clic para cambiar imagen' : 'Sube imagen para la promo' }}</p>
                    </div>
                    <input type="file" id="imagen" name="imagen" class="hidden" accept="image/*">
                </div>
            </div>

            <div class="pt-4 flex gap-3">
                <a href="{{ route('restaurante.promociones.index') }}"
                    class="flex-1 border-2 border-stone-300 text-stone-600 py-4 rounded-xl font-bold text-center hover:bg-stone-50 transition-all">
                    Cancelar
                </a>
                <button type="submit"
                    class="flex-1 bg-[#9e2016] text-white py-4 rounded-xl font-bold shadow-lg hover:scale-[1.02] active:scale-95 transition-all">
                    Guardar Cambios
                </button>
            </div>
        </form>
    </div>
</div>

@include('partials.dropzone')
@endsection
