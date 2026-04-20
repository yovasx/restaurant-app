@extends('layouts.restaurante')

@section('title', 'Nueva Promoción')
@section('page-title', 'Nueva Promoción')

@section('content')
<div class="max-w-2xl mx-auto">
    <a href="{{ route('restaurante.promociones.index') }}" class="text-sm font-bold text-stone-500 hover:text-primary flex items-center gap-1 mb-6">
        <span class="material-symbols-outlined text-[18px]">arrow_back</span> Volver a Promociones
    </a>

    <div class="bg-white rounded-xl shadow-sm border border-stone-200 p-8">
        <h2 class="text-2xl font-headline font-bold text-on-surface mb-6 flex items-center gap-2">
            <span class="material-symbols-outlined text-primary">local_offer</span>
            Crear Promoción
        </h2>

        <form action="{{ route('restaurante.promociones.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <div class="space-y-2">
                <label class="text-xs font-bold text-[#59413d] uppercase tracking-wider">Nombre de la Oferta</label>
                <input name="nombre" value="{{ old('nombre') }}" required
                    class="w-full bg-[#f3ede7] border-0 rounded-lg py-3 px-4 focus:ring-2 focus:ring-[#9e2016] transition-all"
                    type="text" placeholder="Ej: Happy Hour de los Jueves">
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-2">
                    <label class="text-xs font-bold text-[#59413d] uppercase tracking-wider">Tipo de Descuento</label>
                    <select name="tipo" class="w-full bg-[#f3ede7] border-0 rounded-lg py-3 px-4 focus:ring-2 focus:ring-[#9e2016] transition-all">
                        <option value="descuento" {{ old('tipo') == 'descuento' ? 'selected' : '' }}>Porcentaje (%)</option>
                        <option value="2x1"       {{ old('tipo') == '2x1'       ? 'selected' : '' }}>2x1</option>
                        <option value="postre"    {{ old('tipo') == 'postre'    ? 'selected' : '' }}>Postre de regalo</option>
                        <option value="otro"      {{ old('tipo') == 'otro'      ? 'selected' : '' }}>Otro</option>
                    </select>
                </div>
                <div class="space-y-2">
                    <label class="text-xs font-bold text-[#59413d] uppercase tracking-wider">Valor (%)</label>
                    <input name="valor" value="{{ old('valor') }}" min="0" step="0.01"
                        class="w-full bg-[#f3ede7] border-0 rounded-lg py-3 px-4 focus:ring-2 focus:ring-[#9e2016] transition-all"
                        type="number" placeholder="20">
                </div>
            </div>

            <div class="space-y-2">
                <label class="text-xs font-bold text-[#59413d] uppercase tracking-wider">Condición o Detalle</label>
                <textarea name="condicion" rows="3"
                    class="w-full bg-[#f3ede7] border-0 rounded-lg py-3 px-4 focus:ring-2 focus:ring-[#9e2016] transition-all"
                    placeholder="Aplica en bebidas de más de 25Bs. de lunes a jueves...">{{ old('condicion') }}</textarea>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-2">
                    <label class="text-xs font-bold text-[#59413d] uppercase tracking-wider">Fecha de Inicio</label>
                    <input name="fecha_inicio" value="{{ old('fecha_inicio') }}"
                        class="w-full bg-[#f3ede7] border-0 rounded-lg py-3 px-4 focus:ring-2 focus:ring-[#9e2016] transition-all"
                        type="date">
                </div>
                <div class="space-y-2">
                    <label class="text-xs font-bold text-[#59413d] uppercase tracking-wider">Fecha de Fin</label>
                    <input name="fecha_fin" value="{{ old('fecha_fin') }}"
                        class="w-full bg-[#f3ede7] border-0 rounded-lg py-3 px-4 focus:ring-2 focus:ring-[#9e2016] transition-all"
                        type="date">
                </div>
            </div>

            <!-- Imagen de la promo -->
            <div class="space-y-2">
                <label class="text-xs font-bold text-[#59413d] uppercase tracking-wider">Imagen Promocional (Opcional)</label>
                <div class="border-2 border-dashed border-stone-300 rounded-xl p-6 text-center transition-all duration-200 cursor-pointer"
                     data-dropzone="true" data-input="imagen" data-preview="promo-preview" data-placeholder="promo-placeholder">
                    <img id="promo-preview" class="hidden mx-auto mb-3 h-36 w-full object-cover rounded-xl shadow" src="">
                    <div id="promo-placeholder">
                        <span class="material-symbols-outlined text-4xl text-stone-300">add_photo_alternate</span>
                        <p class="text-sm text-stone-500 font-medium mt-2">Sube una imagen atractiva para tu promo</p>
                    </div>
                    <input type="file" id="imagen" name="imagen" class="hidden" accept="image/*">
                </div>
            </div>

            <div class="pt-4">
                <button type="submit"
                    class="w-full bg-[#9e2016] text-white py-4 rounded-xl font-bold text-lg shadow-lg hover:scale-[1.02] active:scale-95 transition-all">
                    Lanzar Promoción
                </button>
            </div>
        </form>
    </div>
</div>

@include('partials.dropzone')
@endsection
