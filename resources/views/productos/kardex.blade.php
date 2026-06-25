@extends('layouts.app')
@section('title','Kardex')
@section('page-title','Kardex')
@section('breadcrumb','Inventario / Kardex')

@section('content')
<div class="card">
    <div class="card-body text-center py-12">
        <i class="ti ti-history text-5xl text-brand-400 mb-4 block"></i>
        <h2 class="text-xl font-bold text-gray-700 mb-1">Kardex — Movimientos</h2>
        <p class="text-sm text-gray-400 max-w-md mx-auto">
            Historial de movimientos por producto: entradas (compras), salidas (ventas) y
            traslados, con saldo acumulado.
        </p>
        <p class="mt-4 text-xs font-semibold uppercase tracking-widest text-gray-300">Módulo en construcción</p>
    </div>
</div>
@endsection
