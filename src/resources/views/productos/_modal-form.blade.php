@php
    $isEditing = !is_null($producto);
    $dropzoneInputId = $formKey.'-foto';
    $dropzonePreviewId = $formKey.'-foto-preview';
    $dropzonePlaceholderId = $formKey.'-foto-placeholder';
@endphp

<form action="{{ $action }}" method="POST" enctype="multipart/form-data" class="space-y-6">
    @csrf
    @if($isEditing)
        @method('PUT')
    @endif

    <input type="hidden" name="redirect_to" value="{{ $redirectTo }}">
    <input type="hidden" name="_modal" value="{{ $modalId }}">

    <div class="space-y-2">
        <label class="text-xs font-bold text-[#59413d] uppercase tracking-wider">Foto del Plato</label>
        <div class="border-2 border-dashed border-stone-300 rounded-2xl p-6 text-center transition-all duration-200 cursor-pointer"
             data-dropzone="true"
             data-input="{{ $dropzoneInputId }}"
             data-preview="{{ $dropzonePreviewId }}"
             data-placeholder="{{ $dropzonePlaceholderId }}">
            @if($isEditing && $producto->foto)
                <img id="{{ $dropzonePreviewId }}" class="mx-auto mb-3 h-40 w-40 object-cover rounded-xl shadow" src="{{ asset('storage/'.$producto->foto) }}">
            @else
                <img id="{{ $dropzonePreviewId }}" class="hidden mx-auto mb-3 h-40 w-40 object-cover rounded-xl shadow" src="">
            @endif

            <div id="{{ $dropzonePlaceholderId }}" class="{{ $isEditing && $producto->foto ? 'hidden' : '' }}">
                <span class="material-symbols-outlined text-4xl text-stone-300">add_photo_alternate</span>
                <p class="text-sm text-stone-500 font-medium mt-2">
                    {{ $isEditing ? 'Haz clic o arrastra para cambiar la imagen' : 'Haz clic o arrastra la imagen del plato aquí' }}
                </p>
                <p class="text-xs text-stone-400 mt-1">PNG, JPG o WEBP - max. 5MB</p>
            </div>

            <input type="file" id="{{ $dropzoneInputId }}" name="foto" class="hidden" accept="image/*">
        </div>
    </div>

    <div class="space-y-2">
        <label class="text-xs font-bold text-[#59413d] uppercase tracking-wider">Nombre del Plato</label>
        <input
            name="nombre"
            value="{{ old('nombre', $producto->nombre ?? '') }}"
            required
            data-modal-initial-focus
            class="w-full bg-[#f3ede7] border-0 rounded-xl py-3 px-4 focus:ring-2 focus:ring-[#9e2016] transition-all"
            type="text"
            placeholder="Ej: Tartar de Atún"
        >
    </div>

    <div class="space-y-2">
        <label class="text-xs font-bold text-[#59413d] uppercase tracking-wider">Descripción</label>
        <textarea
            name="descripcion"
            class="w-full bg-[#f3ede7] border-0 rounded-xl py-3 px-4 focus:ring-2 focus:ring-[#9e2016] transition-all"
            rows="3"
            placeholder="Marinado con cítricos, servido frío..."
        >{{ old('descripcion', $producto->descripcion ?? '') }}</textarea>
    </div>

    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
        <div class="space-y-2">
            <label class="text-xs font-bold text-[#59413d] uppercase tracking-wider">Precio (Bs.)</label>
            <input
                name="precio"
                value="{{ old('precio', $producto->precio ?? '') }}"
                required
                min="0"
                step="0.01"
                class="w-full bg-[#f3ede7] border-0 rounded-xl py-3 px-4 focus:ring-2 focus:ring-[#9e2016] transition-all"
                type="number"
                placeholder="18.50"
            >
        </div>

        <div class="space-y-2">
            <label class="text-xs font-bold text-[#59413d] uppercase tracking-wider">Stock (unidades)</label>
            <input
                name="stock"
                value="{{ old('stock', $producto->stock ?? 0) }}"
                required
                min="0"
                class="w-full bg-[#f3ede7] border-0 rounded-xl py-3 px-4 focus:ring-2 focus:ring-[#9e2016] transition-all"
                type="number"
                placeholder="50"
            >
        </div>
    </div>

    <div class="space-y-2">
        <label class="text-xs font-bold text-[#59413d] uppercase tracking-wider">Categoría</label>
        <select name="categoria_id" class="w-full bg-[#f3ede7] border-0 rounded-xl py-3 px-4 focus:ring-2 focus:ring-[#9e2016] transition-all">
            <option value="">Sin categoría</option>
            @foreach($categorias as $cat)
                <option value="{{ $cat->id }}" {{ (string) old('categoria_id', $producto->categoria_id ?? '') === (string) $cat->id ? 'selected' : '' }}>
                    {{ $cat->nombre_categoria }}
                </option>
            @endforeach
        </select>
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
