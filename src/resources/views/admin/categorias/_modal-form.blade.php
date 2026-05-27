@php
    $isEditing = !is_null($categoria);
@endphp

<form action="{{ $action }}" method="POST" class="space-y-6">
    @csrf

    <input type="hidden" name="redirect_to" value="{{ $redirectTo }}">
    <input type="hidden" name="_modal" value="{{ $modalId }}">

    <div class="space-y-2">
        <label class="text-xs font-bold text-stone-500 uppercase tracking-widest">Nombre</label>
        <input name="nombre_categoria" value="{{ old('nombre_categoria', $categoria->nombre_categoria ?? '') }}" class="w-full bg-[#f3ede7] border-0 rounded-xl py-3 px-4 focus:ring-2 focus:ring-[#9e2016] transition-all" type="text" required data-modal-initial-focus>
    </div>

    <div class="space-y-2">
        <label class="text-xs font-bold text-stone-500 uppercase tracking-widest">Descripción</label>
        <input name="descripcion" value="{{ old('descripcion', $categoria->descripcion ?? '') }}" class="w-full bg-[#f3ede7] border-0 rounded-xl py-3 px-4 focus:ring-2 focus:ring-[#9e2016] transition-all" type="text">
    </div>

    @if($isEditing)
        <div class="space-y-2">
            <label class="text-xs font-bold text-stone-500 uppercase tracking-widest">Estado</label>
            <select name="estado" class="w-full bg-[#f3ede7] border-0 rounded-xl py-3 px-4 focus:ring-2 focus:ring-[#9e2016] transition-all">
                <option value="activo" {{ old('estado', $categoria->estado) === 'activo' ? 'selected' : '' }}>Activo</option>
                <option value="inactivo" {{ old('estado', $categoria->estado) === 'inactivo' ? 'selected' : '' }}>Inactivo</option>
            </select>
        </div>
    @endif

    <div class="flex flex-col-reverse gap-3 pt-2 sm:flex-row sm:justify-end">
        <button type="button" data-modal-close class="w-full rounded-2xl border border-stone-200 px-5 py-3.5 font-bold text-stone-600 transition hover:bg-stone-50 sm:w-auto sm:min-w-40">
            Cancelar
        </button>
        <button type="submit" class="w-full rounded-2xl bg-[#9e2016] px-5 py-3.5 font-bold text-white shadow-lg shadow-red-900/20 transition hover:bg-[#b02d21] sm:w-auto sm:min-w-40">
            {{ $submitLabel }}
        </button>
    </div>
</form>
