@extends('errors.base')

@section('code', '500')
@section('emoji', '🛠️')
@section('title', $mensajeAmigable ?? 'Algo salió mal')
@section('message', ($mensajeAmigable ?? null)
    ? 'Revisá los datos e intentá de nuevo. Si el problema continúa, mostrale el detalle técnico al soporte.'
    : 'Ocurrió un error inesperado al procesar tu solicitud. Podés intentar de nuevo; si el problema continúa, mostrale el detalle técnico al soporte.')

@if (isset($exception) && $exception->getMessage())
    @section('detail', $exception->getMessage())
@endif
