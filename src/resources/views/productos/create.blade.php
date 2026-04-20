@extends('layouts.app')

@section('title', 'Crear producto')

@section('content')
    <div class="space-y-6">
        <div>
            <h1 class="mb-1 font-medium">Crear producto</h1>
            <p class="text-muted">Registra un nuevo producto con categoría y precio.</p>
        </div>

        <form action="{{ route('productos.store') }}" method="POST" class="space-y-6">
            @csrf

            @include('productos._form', [
                'producto' => null,
                'categorias' => $categorias,
                'buttonText' => 'Guardar producto',
            ])
        </form>
    </div>
@endsection
