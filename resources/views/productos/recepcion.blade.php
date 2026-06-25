@extends('layouts.app')
@section('title','Recepción')
@section('page-title','Recepción')
@section('breadcrumb','Inventario / Recepción')

@section('content')
<div class="card">
    <div class="card-body text-center py-12">
        <i class="ti ti-package-import text-5xl text-brand-400 mb-4 block"></i>
        <h2 class="text-xl font-bold text-gray-700 mb-1">Recepción de Compras</h2>
        <p class="text-sm text-gray-400 max-w-md mx-auto">
            Recepción física de la mercadería de una compra. Al recepcionar (total o parcial),
            <strong>entra al almacén</strong> (suma <code>productos.cantidad</code>) y se registra
            el <strong>movimiento de entrada en el Kardex</strong>.
        </p>
        <p class="mt-4 text-xs font-semibold uppercase tracking-widest text-gray-300">Módulo en construcción</p>
    </div>
</div>
@endsection
