@foreach($productos as $producto)
    <x-modal
        id="producto-edit-{{ $producto->id }}"
        title="Editar plato"
        subtitle="Actualiza precios, stock e imagen sin cambiar de pantalla."
        max-width="max-w-3xl"
        :auto-open="old('_modal') === 'producto-edit-'.$producto->id"
    >
        @include('productos._modal-form', [
            'action' => route('productos.update', $producto),
            'producto' => $producto,
            'categorias' => $categorias,
            'submitLabel' => 'Actualizar plato',
            'redirectTo' => $redirectTo,
            'modalId' => 'producto-edit-'.$producto->id,
            'formKey' => 'producto-edit-'.$producto->id,
        ])
    </x-modal>

    <x-modal
        id="producto-delete-{{ $producto->id }}"
        title="Eliminar plato"
        subtitle="Este cambio quitará el plato del listado actual."
        max-width="max-w-lg"
    >
        <div class="space-y-6">
            <div class="rounded-3xl bg-red-50 p-5 text-red-900">
                <p class="font-headline text-xl font-extrabold">{{ $producto->nombre }}</p>
                <p class="mt-2 text-sm text-red-800/80">Confirma si deseas eliminar este plato del menú.</p>
            </div>

            <form action="{{ route('productos.destroy', $producto) }}" method="POST" class="flex flex-col-reverse gap-3 sm:flex-row">
                @csrf
                @method('DELETE')
                <input type="hidden" name="redirect_to" value="{{ $redirectTo }}">

                <button type="button" data-modal-close class="flex-1 rounded-2xl border border-stone-200 px-5 py-3.5 font-bold text-stone-600 transition hover:bg-stone-50">
                    Cancelar
                </button>
                <button type="submit" class="flex-1 rounded-2xl bg-red-600 px-5 py-3.5 font-bold text-white shadow-lg shadow-red-900/20 transition hover:bg-red-700">
                    Eliminar plato
                </button>
            </form>
        </div>
    </x-modal>
@endforeach
