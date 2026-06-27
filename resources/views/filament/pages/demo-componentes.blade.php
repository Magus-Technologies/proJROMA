<div>
    {{ $this->form }}

    <div class="mt-6 border-t pt-4">
        <h2 class="text-lg font-semibold mb-4">Acciones con Modales</h2>
        <div class="flex flex-wrap gap-2">
            {{ $this->abrirModalConfirmacionAction }}
            {{ $this->abrirModalFormularioAction }}
            {{ $this->abrirModalSlideOverAction }}
        </div>
    </div>

    <div class="mt-6 border-t pt-4">
        <h2 class="text-lg font-semibold mb-4">Notificaciones</h2>
        <div class="flex flex-wrap gap-2">
            {{ $this->notificarSuccessAction }}
            {{ $this->notificarErrorAction }}
            {{ $this->notificarInfoAction }}
            {{ $this->notificarWarningAction }}
        </div>
    </div>
</div>
