@foreach($promociones as $promo)
    <x-modal
        id="promocion-edit-{{ $promo->id }}"
        title="Editar promoción"
        subtitle="Mantén la campaña actualizada sin abandonar la tabla."
        max-width="max-w-3xl"
        :auto-open="old('_modal') === 'promocion-edit-'.$promo->id"
    >
        @include('restaurante.promociones._modal-form', [
            'action' => route('restaurante.promociones.update', $promo),
            'promocion' => $promo,
            'submitLabel' => 'Guardar cambios',
            'redirectTo' => $redirectTo,
            'modalId' => 'promocion-edit-'.$promo->id,
            'formKey' => 'promocion-edit-'.$promo->id,
        ])
    </x-modal>

    <x-modal
        id="promocion-delete-{{ $promo->id }}"
        title="Archivar promoción"
        subtitle="La promoción dejará de mostrarse como activa."
        max-width="max-w-lg"
    >
        <div class="space-y-6">
            <div class="rounded-3xl bg-amber-50 p-5 text-amber-900">
                <p class="font-headline text-xl font-extrabold">{{ $promo->nombre }}</p>
                <p class="mt-2 text-sm text-amber-900/80">Se moverá al estado inactivo para mantener el historial.</p>
            </div>

            <form action="{{ route('restaurante.promociones.destroy', $promo) }}" method="POST" class="flex flex-col-reverse gap-3 sm:flex-row">
                @csrf
                @method('DELETE')
                <input type="hidden" name="redirect_to" value="{{ $redirectTo }}">

                <button type="button" data-modal-close class="flex-1 rounded-2xl border border-stone-200 px-5 py-3.5 font-bold text-stone-600 transition hover:bg-stone-50">
                    Cancelar
                </button>
                <button type="submit" class="flex-1 rounded-2xl bg-amber-600 px-5 py-3.5 font-bold text-white shadow-lg shadow-amber-900/20 transition hover:bg-amber-700">
                    Archivar promoción
                </button>
            </form>
        </div>
    </x-modal>
@endforeach
