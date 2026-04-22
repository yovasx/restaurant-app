@extends('layouts.app')

@section('title', $restaurante->nombre)

@section('content')
<section class="max-w-7xl mx-auto px-6 pb-12">
    <div class="mt-6 mb-6">
        <a href="{{ route('comensal.inicio') }}" class="inline-flex items-center gap-2 text-sm text-[#9e2016] font-bold hover:underline">
            <span class="material-symbols-outlined">arrow_back</span> Volver
        </a>
    </div>

    <div class="bg-white rounded-xl overflow-hidden shadow-sm">
        <div class="h-64 w-full overflow-hidden">
            <img class="w-full h-full object-cover" src="{{ $restaurante->foto_portada ? asset('storage/'.$restaurante->foto_portada) : 'https://via.placeholder.com/1600x600?text=Restaurante' }}" alt="{{ $restaurante->nombre }}" />
        </div>
        <div class="p-8">
            <div class="flex justify-between items-start mb-6">
                <div>
                    <h1 class="text-3xl font-extrabold">{{ $restaurante->nombre }}</h1>
                    <p class="text-sm text-stone-600 mt-2">{{ $restaurante->direccion }} {{ $restaurante->zona ? '· '.$restaurante->zona : '' }}</p>
                </div>
                <div class="text-right">
                    <p class="font-bold text-lg">Promociones</p>
                    <p class="text-sm text-stone-500">{{ $promociones->count() }} activas</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="md:col-span-2">
                    <h2 class="text-xl font-bold mb-4">Menú</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach($productos as $p)
                        <div class="p-4 bg-surface-container-lowest rounded-lg flex gap-4 items-center">
                            <div class="w-20 h-20 rounded-lg overflow-hidden">
                                <img src="{{ $p->foto ? asset('storage/'.$p->foto) : 'https://via.placeholder.com/240x160?text=Plato' }}" class="w-full h-full object-cover" alt="{{ $p->nombre }}" />
                            </div>
                            <div class="flex-1">
                                <div class="flex justify-between items-start">
                                    <h3 class="font-bold">{{ $p->nombre }}</h3>
                                    <span class="font-black">{{ $p->precio }} Bs.</span>
                                </div>
                                <p class="text-sm text-stone-500">{{ \Illuminate\Support\Str::limit($p->descripcion, 80) }}</p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                <aside class="md:col-span-1">
                    <h2 class="text-xl font-bold mb-4">Promociones</h2>
                    @forelse($promociones as $promo)
                    <div class="p-4 bg-[#fff8f2] rounded-lg mb-4">
                        <h3 class="font-bold">{{ $promo->nombre }}</h3>
                        <p class="text-sm text-stone-600">{{ $promo->tipo }} · {{ $promo->valor }}</p>
                        @if($promo->imagen)
                        <div class="mt-3">
                            <img src="{{ asset('storage/'.$promo->imagen) }}" class="w-full h-32 object-cover rounded-lg" />
                        </div>
                        @endif
                    </div>
                    @empty
                    <p class="text-sm text-stone-500">No hay promociones activas.</p>
                    @endforelse
                </aside>
            </div>
        </div>
    </div>
</section>
@endsection
