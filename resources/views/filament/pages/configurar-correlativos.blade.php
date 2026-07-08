<x-filament-panels::page>
    <div class="rounded-xl border border-amber-300 bg-amber-50 dark:border-amber-500/40 dark:bg-amber-500/10 p-4 text-sm text-amber-800 dark:text-amber-200">
        <strong>Importante:</strong> el número que cargás es el <em>último emitido</em>. El próximo documento
        usará ese número + 1. Al migrar datos, poné acá dónde se quedó el sistema anterior de cada serie.
    </div>

    {{ $this->form }}
</x-filament-panels::page>
