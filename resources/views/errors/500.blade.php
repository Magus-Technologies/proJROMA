@extends('errors.base')

@section('code', '500')
@section('emoji', '🛠️')
@section('title', 'Algo salió mal')
@section('message', 'Ocurrió un error inesperado al procesar tu solicitud. Podés intentar de nuevo; si el problema continúa, mostrale el detalle técnico al soporte.')

@if (isset($exception) && $exception->getMessage())
    @section('detail', $exception->getMessage())
@endif
