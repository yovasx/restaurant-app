@php
    $isEditing = !is_null($promocion);
    $dropzoneInputId = $formKey.'-imagen';
    $dropzonePreviewId = $formKey.'-preview';
    $dropzonePlaceholderId = $formKey.'-placeholder';
@endphp

<form action="{{ $action }}" method="POST" enctype="multipart/form-data" class="space-y-6">
    @csrf
    @if($isEditing)
        @method('PUT')
    @endif

    <input type="hidden" name="redirect_to" value="{{ $redirectTo }}">
    <input type="hidden" name="_modal" value="{{ $modalId }}">

    <div class="space-y-2">
        <label class="text-xs font-bold text-[#59413d] uppercase tracking-wider">Nombre de la Oferta</label>
        <input name="nombre" value="{{ old('nombre', $promocion->nombre ?? '') }}" required data-modal-initial-focus
            class="w-full bg-[#f3ede7] border-0 rounded-xl py-3 px-4 focus:ring-2 focus:ring-[#9e2016] transition-all"
            type="text" placeholder="Ej: Happy Hour de los Jueves">
    </div>

    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
        <div class="space-y-2">
            <label class="text-xs font-bold text-[#59413d] uppercase tracking-wider">Tipo de Descuento</label>
            <select name="tipo" class="w-full bg-[#f3ede7] border-0 rounded-xl py-3 px-4 focus:ring-2 focus:ring-[#9e2016] transition-all">
                <option value="descuento" {{ old('tipo', $promocion->tipo ?? 'descuento') == 'descuento' ? 'selected' : '' }}>Porcentaje (%)</option>
                <option value="2x1" {{ old('tipo', $promocion->tipo ?? '') == '2x1' ? 'selected' : '' }}>2x1</option>
                <option value="postre" {{ old('tipo', $promocion->tipo ?? '') == 'postre' ? 'selected' : '' }}>Postre de regalo</option>
                <option value="otro" {{ old('tipo', $promocion->tipo ?? '') == 'otro' ? 'selected' : '' }}>Otro</option>
            </select>
        </div>

        <div class="space-y-2">
            <label class="text-xs font-bold text-[#59413d] uppercase tracking-wider">Valor (%)</label>
            <input name="valor" value="{{ old('valor', $promocion->valor ?? '') }}" min="0" step="0.01"
                class="w-full bg-[#f3ede7] border-0 rounded-xl py-3 px-4 focus:ring-2 focus:ring-[#9e2016] transition-all"
                type="number" placeholder="20">
        </div>
    </div>

    <div class="space-y-2">
        <label class="text-xs font-bold text-[#59413d] uppercase tracking-wider">Condición o Detalle</label>
        <textarea name="condicion" rows="3"
            class="w-full bg-[#f3ede7] border-0 rounded-xl py-3 px-4 focus:ring-2 focus:ring-[#9e2016] transition-all"
            placeholder="Aplica en bebidas de más de 25Bs. de lunes a jueves...">{{ old('condicion', $promocion->condicion ?? '') }}</textarea>
    </div>

    <div class="grid grid-cols-1 gap-6 {{ $isEditing ? 'md:grid-cols-3' : 'md:grid-cols-2' }}">
        <div class="space-y-2">
            <label class="text-xs font-bold text-[#59413d] uppercase tracking-wider">Fecha de Inicio</label>
            <input name="fecha_inicio" value="{{ old('fecha_inicio', $promocion?->fecha_inicio?->format('Y-m-d')) }}"
                class="w-full bg-[#f3ede7] border-0 rounded-xl py-3 px-4 focus:ring-2 focus:ring-[#9e2016] transition-all"
                type="date">
        </div>

        <div class="space-y-2">
            <label class="text-xs font-bold text-[#59413d] uppercase tracking-wider">Fecha de Fin</label>
            <input name="fecha_fin" value="{{ old('fecha_fin', $promocion?->fecha_fin?->format('Y-m-d')) }}"
                class="w-full bg-[#f3ede7] border-0 rounded-xl py-3 px-4 focus:ring-2 focus:ring-[#9e2016] transition-all"
                type="date">
        </div>

        @if($isEditing)
            <div class="space-y-2">
                <label class="text-xs font-bold text-[#59413d] uppercase tracking-wider">Estado</label>
                <select name="estado" class="w-full bg-[#f3ede7] border-0 rounded-xl py-3 px-4 focus:ring-2 focus:ring-[#9e2016] transition-all">
                    <option value="activo" {{ old('estado', $promocion->estado) == 'activo' ? 'selected' : '' }}>Activo</option>
                    <option value="inactivo" {{ old('estado', $promocion->estado) == 'inactivo' ? 'selected' : '' }}>Inactivo</option>
                </select>
            </div>
        @endif
    </div>

    <div class="space-y-2">
        <label class="text-xs font-bold text-[#59413d] uppercase tracking-wider">Imagen Promocional</label>
        <div class="border-2 border-dashed border-stone-300 rounded-2xl p-6 text-center transition-all duration-200 cursor-pointer"
             data-dropzone="true"
             data-input="{{ $dropzoneInputId }}"
             data-preview="{{ $dropzonePreviewId }}"
             data-placeholder="{{ $dropzonePlaceholderId }}">
            @if($isEditing && $promocion->imagen_url)
                <img id="{{ $dropzonePreviewId }}" class="mx-auto mb-3 h-36 w-full object-cover rounded-xl shadow" src="{{ $promocion->imagen_url }}">
            @else
                <img id="{{ $dropzonePreviewId }}" class="hidden mx-auto mb-3 h-36 w-full object-cover rounded-xl shadow" src="">
            @endif

            <div id="{{ $dropzonePlaceholderId }}" class="{{ $isEditing && $promocion->imagen ? 'hidden' : '' }}">
                <span class="material-symbols-outlined text-4xl text-stone-300">add_photo_alternate</span>
                <p class="text-sm text-stone-500 font-medium mt-2">{{ $isEditing ? 'Haz clic para cambiar la imagen' : 'Sube una imagen atractiva para tu promo' }}</p>
            </div>

            <input type="file" id="{{ $dropzoneInputId }}" name="imagen" class="hidden" accept="image/*">
        </div>
    </div>

    <div class="flex flex-col-reverse gap-3 pt-2 sm:flex-row">
        <button type="button" data-modal-close class="flex-1 rounded-2xl border border-stone-200 px-5 py-3.5 font-bold text-stone-600 transition hover:bg-stone-50">
            Cancelar
        </button>
        <button type="submit" class="flex-1 rounded-2xl bg-[#9e2016] px-5 py-3.5 font-bold text-white shadow-lg shadow-red-900/20 transition hover:bg-[#b02d21]">
            {{ $submitLabel }}
        </button>
    </div>
</form>
