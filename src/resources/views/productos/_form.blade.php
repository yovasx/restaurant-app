@php
    $selectedCategoria = old('categoria_id', $producto->categoria_id ?? '');
@endphp

<div class="grid gap-4 sm:grid-cols-2">
    <div>
        <label for="nombre" class="mb-2 block text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC]">Nombre</label>
        <input
            id="nombre"
            name="nombre"
            type="text"
            value="{{ old('nombre', $producto->nombre ?? '') }}"
            class="w-full rounded-sm border border-gray-300 bg-white px-3 py-2 text-sm text-[#1b1b18] shadow-sm outline-none focus:border-blue-300 focus:ring focus:ring-blue-200/50 dark:border-gray-700 dark:bg-[#161615] dark:text-[#EDEDEC]"
        />
        @error('nombre')
            <p class="mt-1 text-sm text-[#f53003]">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="precio" class="mb-2 block text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC]">Precio</label>
        <input
            id="precio"
            name="precio"
            type="number"
            step="0.01"
            min="0"
            value="{{ old('precio', $producto->precio ?? '') }}"
            class="w-full rounded-sm border border-gray-300 bg-white px-3 py-2 text-sm text-[#1b1b18] shadow-sm outline-none focus:border-blue-300 focus:ring focus:ring-blue-200/50 dark:border-gray-700 dark:bg-[#161615] dark:text-[#EDEDEC]"
        />
        @error('precio')
            <p class="mt-1 text-sm text-[#f53003]">{{ $message }}</p>
        @enderror
    </div>
</div>

<div class="grid gap-4 sm:grid-cols-2">
    <div>
        <label for="stock" class="mb-2 block text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC]">Stock</label>
        <input
            id="stock"
            name="stock"
            type="number"
            min="0"
            value="{{ old('stock', $producto->stock ?? '') }}"
            class="w-full rounded-sm border border-gray-300 bg-white px-3 py-2 text-sm text-[#1b1b18] shadow-sm outline-none focus:border-blue-300 focus:ring focus:ring-blue-200/50 dark:border-gray-700 dark:bg-[#161615] dark:text-[#EDEDEC]"
        />
        @error('stock')
            <p class="mt-1 text-sm text-[#f53003]">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="categoria_id" class="mb-2 block text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC]">Categoría</label>
        <select
            id="categoria_id"
            name="categoria_id"
            class="w-full rounded-sm border border-gray-300 bg-white px-3 py-2 text-sm text-[#1b1b18] shadow-sm outline-none focus:border-blue-300 focus:ring focus:ring-blue-200/50 dark:border-gray-700 dark:bg-[#161615] dark:text-[#EDEDEC]"
        >
            <option value="">Selecciona una categoría</option>
            @foreach($categorias as $categoria)
                <option value="{{ $categoria->id }}" {{ (string) $selectedCategoria === (string) $categoria->id ? 'selected' : '' }}>
                    {{ $categoria->nombre }}
                </option>
            @endforeach
        </select>
        @error('categoria_id')
            <p class="mt-1 text-sm text-[#f53003]">{{ $message }}</p>
        @enderror
    </div>
</div>

<div class="flex flex-wrap gap-3 items-center pt-4">
    <button type="submit" class="button-primary inline-flex items-center justify-center rounded-sm px-5 py-1.5 text-sm font-medium">
        {{ $buttonText }}
    </button>

    <a href="{{ route('productos.index') }}" class="button-secondary inline-flex items-center justify-center rounded-sm px-5 py-1.5 text-sm font-medium">
        Volver
    </a>
</div>
