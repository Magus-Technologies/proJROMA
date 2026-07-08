<x-filament-panels::page>
    <div>
        @php
            $tabs = [
                'bancos'    => 'Bancos',
                'cuentas'   => 'Cuentas Bancarias',
                'tarjetas'  => 'Tarjetas',
                'billeteras'=> 'Billeteras Digitales',
            ];
        @endphp

        <div class="mb-4 flex flex-wrap gap-1 border-b border-gray-200 dark:border-gray-700">
            @foreach($tabs as $key => $label)
                <button
                    wire:click="$set('tab', '{{ $key }}')"
                    @class([
                        '-mb-px border-b-2 px-4 py-2 text-xs font-semibold transition-colors',
                        'border-primary-600 text-primary-700 dark:text-primary-400' => $this->tab === $key,
                        'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300' => $this->tab !== $key,
                    ])>
                    {{ $label }}
                </button>
            @endforeach
        </div>

        {{ $this->table }}
    </div>
</x-filament-panels::page>
