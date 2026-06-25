@extends('layouts.app')
@section('title','Almacén')
@section('page-title','Almacén')
@section('breadcrumb','Inventario / Almacén')

@section('content')
<div class="card">
    <div class="card-body text-center py-12">
        <i class="ti ti-archive text-5xl text-brand-400 mb-4 block"></i>
        <h2 class="text-xl font-bold text-gray-700 mb-1">Almacén — Existencias</h2>
        <p class="text-sm text-gray-400 max-w-md mx-auto">
            Foto actual del stock por depósito (Almacén 1, 2, 3). Aquí se consultan las
            existencias; el alta/edición de productos se hace en <strong>Registro de Productos</strong>.
        </p>
        <p class="mt-4 text-xs font-semibold uppercase tracking-widest text-gray-300">Módulo en construcción</p>
    </div>
</div>
@endsection
