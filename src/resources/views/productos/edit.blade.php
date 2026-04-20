@extends('layouts.app')

@section('title', 'Editar producto')

@section('content')
    <div class="space-y-6">
        <div>
            <h1 class="mb-1 font-medium">Editar producto</h1>
            <p class="text-muted">Actualiza los datos del producto seleccionado.</p>
        </div>

        <form action="{{ route('productos.update', $producto) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            @include('productos._form', [
                'producto' => $producto,
                'categorias' => $categorias,
                'buttonText' => 'Actualizar producto',
            ])
        </form>
    </div>
@endsection
