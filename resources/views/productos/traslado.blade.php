@extends('layouts.app')
@section('title','Traslado de Stock')
@section('page-title','Traslado de Stock')
@section('breadcrumb','Inventario / Traslado de Stock')

@section('content')
<div class="card">
    <div class="card-body text-center py-12">
        <i class="ti ti-arrows-exchange text-5xl text-brand-400 mb-4 block"></i>
        <h2 class="text-xl font-bold text-gray-700 mb-1">Traslado de Stock</h2>
        <p class="text-sm text-gray-400 max-w-md mx-auto">
            Mover existencias entre tus propios almacenes (de Almacén origen → Almacén destino).
            Cada traslado queda registrado como movimiento en el Kardex.
        </p>
        <p class="mt-4 text-xs font-semibold uppercase tracking-widest text-gray-300">Módulo en construcción</p>
    </div>
</div>
@endsection
