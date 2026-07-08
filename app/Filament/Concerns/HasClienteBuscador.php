<?php

namespace App\Filament\Concerns;

use App\Models\Cliente;
use App\Models\TmsMercado;
use Filament\Actions\Action;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\HtmlString;

/**
 * Buscador de cliente reutilizable estilo "escribir y elegir" (como el de
 * productos): un input, resultados clickeables debajo, tarjeta del cliente
 * elegido y botón "+" para crear uno nuevo al vuelo.
 *
 * La página que lo use debe tener un campo `id_cliente` en su form data.
 * Insertá los componentes con: ...static::clienteBuscadorSchema()
 */
trait HasClienteBuscador
{
    /**
     * Campos del formulario de cliente — fuente única compartida entre el
     * botón "+" del buscador y la vista de Clientes. Incluye consulta SUNAT/RENIEC.
     *
     * @return array<int, \Filament\Forms\Components\Field>
     */
    public static function clienteFormFields(): array
    {
        return [
            TextInput::make('documento')
                ->label('RUC / DNI')
                ->maxLength(15)
                ->suffixAction(
                    Action::make('consultar_doc')
                        ->icon('heroicon-m-magnifying-glass')
                        ->tooltip('Consultar SUNAT / RENIEC')
                        ->action(function ($state, callable $set): void {
                            $doc   = trim((string) $state);
                            $len   = strlen($doc);
                            $url   = config('apisperu.url');
                            $token = config('apisperu.token');

                            if (! in_array($len, [8, 11])) {
                                Notification::make()->warning()->title('Ingresá 8 dígitos (DNI) o 11 dígitos (RUC).')->send();

                                return;
                            }

                            try {
                                if ($len === 8) {
                                    $data = Http::timeout(8)->get("{$url}/dni/{$doc}", ['token' => $token])->json();
                                    $nombre = trim(implode(' ', array_filter([
                                        $data['nombres'] ?? '', $data['apellidoPaterno'] ?? '', $data['apellidoMaterno'] ?? '',
                                    ])));
                                    if (! $nombre) {
                                        Notification::make()->warning()->title($data['message'] ?? 'DNI no encontrado.')->send();

                                        return;
                                    }
                                    $set('datos', $nombre);
                                    Notification::make()->success()->title('Datos cargados desde RENIEC')->send();
                                } else {
                                    $data = Http::timeout(8)->get("{$url}/ruc/{$doc}", ['token' => $token])->json();
                                    if (empty($data['razonSocial'])) {
                                        Notification::make()->warning()->title('RUC no encontrado.')->send();

                                        return;
                                    }
                                    $dir = collect([
                                        $data['direccion'] ?? '', $data['distrito'] ?? '',
                                        $data['provincia'] ?? '', $data['departamento'] ?? '',
                                    ])->filter()->implode(', ');
                                    $set('datos', $data['razonSocial']);
                                    $set('direccion', $dir);
                                    Notification::make()->success()->title('Datos cargados desde SUNAT')->send();
                                }
                            } catch (\Throwable) {
                                Notification::make()->warning()->title('Error al consultar. Intentá de nuevo.')->send();
                            }
                        })
                ),

            TextInput::make('datos')->label('Razón Social / Nombre')->required()->maxLength(200)->columnSpanFull(),
            TextInput::make('direccion')->label('Dirección')->maxLength(200)->columnSpanFull(),
            TextInput::make('distrito')->label('Distrito')->maxLength(100),

            Select::make('mercado')
                ->label('Mercado / Zona (TMS)')
                ->options(fn () => TmsMercado::where('id_empresa', (int) session('id_empresa'))
                    ->orderBy('nombre')->pluck('nombre', 'id'))
                ->searchable()
                ->nullable()
                ->helperText('Zona/mercado del cliente. Se usa para armar los despachos.'),

            TextInput::make('telefono')
                ->label('Teléfono')
                ->tel()
                ->mask('99999999999999999999')
                ->maxLength(20)
                ->regex('/^[0-9]*$/')
                ->validationMessages(['regex' => 'El teléfono solo puede contener números.']),

            TextInput::make('email')->label('Email')->email()->maxLength(100),
        ];
    }

    /** @return array<int, \Filament\Schemas\Components\Component> */
    public static function clienteBuscadorSchema(): array
    {
        return [
            Hidden::make('id_cliente'),

            TextInput::make('buscador_cliente')
                ->label('Cliente')
                ->placeholder('🔍 Buscar cliente por nombre o documento…')
                ->autocomplete(false)
                ->dehydrated(false)
                ->live(debounce: 300)
                ->visible(fn (callable $get): bool => blank($get('id_cliente')))
                ->suffixAction(
                    Action::make('nuevo_cliente')
                        ->icon('heroicon-m-user-plus')
                        ->tooltip('Crear cliente nuevo')
                        ->modalHeading('Nuevo cliente')
                        ->modalWidth('lg')
                        ->form(static::clienteFormFields())
                        ->action(function (array $data, callable $set): void {
                            $cliente = Cliente::create(array_merge($data, [
                                'id_empresa' => (int) session('id_empresa'),
                            ]));
                            $set('id_cliente', $cliente->id_cliente);
                            $set('buscador_cliente', null);
                        })
                )
                ->columnSpanFull(),

            Placeholder::make('cliente_resultados')
                ->hiddenLabel()
                ->columnSpanFull()
                ->visible(fn (callable $get): bool => filled($get('buscador_cliente')) && blank($get('id_cliente')))
                ->content(function (callable $get): HtmlString {
                    $busqueda = trim((string) $get('buscador_cliente'));
                    if ($busqueda === '') {
                        return new HtmlString('');
                    }

                    $clientes = Cliente::where('id_empresa', (int) session('id_empresa'))
                        ->where(fn ($q) => $q
                            ->where('datos', 'like', "%{$busqueda}%")
                            ->orWhere('documento', 'like', "%{$busqueda}%"))
                        ->limit(8)
                        ->get();

                    if ($clientes->isEmpty()) {
                        return new HtmlString(
                            '<div style="padding:12px 14px;border:1px dashed rgba(148,163,184,.5);border-radius:12px;'
                            . 'color:#94a3b8;font-size:.85rem;text-align:center">'
                            . 'Sin coincidencias · tocá el botón <strong>+</strong> para crear uno nuevo</div>'
                        );
                    }

                    $filas = $clientes->map(fn (Cliente $c): string =>
                        '<button type="button" wire:click="seleccionarCliente(' . $c->id_cliente . ')" '
                        . 'onmouseover="this.style.background=\'rgba(59,130,246,.10)\'" '
                        . 'onmouseout="this.style.background=\'transparent\'" '
                        . 'style="display:flex;justify-content:space-between;align-items:center;gap:12px;width:100%;text-align:left;'
                        . 'padding:10px 14px;border:0;border-bottom:1px solid rgba(148,163,184,.18);background:transparent;'
                        . 'cursor:pointer;font-size:.875rem;transition:background .12s">'
                        . '<span style="font-weight:600">' . e($c->datos) . '</span>'
                        . '<span style="white-space:nowrap;opacity:.6;font-family:monospace;font-size:.8rem">' . e($c->documento ?: '—') . '</span>'
                        . '</button>'
                    )->implode('');

                    return new HtmlString(
                        '<div style="max-height:320px;overflow-y:auto;'
                        . 'border:1px solid rgba(148,163,184,.35);border-radius:12px;overflow:hidden;'
                        . 'box-shadow:0 4px 12px rgba(0,0,0,.06)">' . $filas . '</div>'
                    );
                }),

            Placeholder::make('cliente_elegido')
                ->hiddenLabel()
                ->columnSpanFull()
                ->visible(fn (callable $get): bool => filled($get('id_cliente')))
                ->content(function (callable $get): HtmlString {
                    $cliente = Cliente::find($get('id_cliente'));
                    $inicial = mb_strtoupper(mb_substr($cliente?->datos ?? 'C', 0, 1));

                    return new HtmlString(
                        '<div style="display:flex;align-items:center;gap:12px;'
                        . 'padding:12px 14px;border-radius:12px;border:1px solid rgba(59,130,246,.35);background:rgba(59,130,246,.07)">'
                        . '<div style="flex-shrink:0;width:38px;height:38px;border-radius:50%;background:rgb(59,130,246);'
                        . 'color:#fff;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:1rem">'
                        . e($inicial) . '</div>'
                        . '<div style="flex:1;min-width:0">'
                        . '<div style="font-weight:700;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">' . e($cliente?->datos ?? 'Cliente') . '</div>'
                        . ($cliente?->documento
                            ? '<div style="opacity:.6;font-size:.8rem;font-family:monospace">' . e($cliente->documento) . '</div>'
                            : '')
                        . '</div>'
                        . '<button type="button" wire:click="limpiarCliente" '
                        . 'style="flex-shrink:0;font-size:.78rem;font-weight:600;color:rgb(220,38,38);white-space:nowrap;cursor:pointer;'
                        . 'padding:5px 10px;border-radius:8px;border:1px solid rgba(220,38,38,.3);background:transparent">✕ Cambiar</button>'
                        . '</div>'
                    );
                }),
        ];
    }

    public function seleccionarCliente(int $idCliente): void
    {
        $existe = Cliente::where('id_empresa', (int) session('id_empresa'))
            ->where('id_cliente', $idCliente)
            ->exists();

        if ($existe) {
            $this->data['id_cliente']       = $idCliente;
            $this->data['buscador_cliente'] = null;
        }
    }

    public function limpiarCliente(): void
    {
        $this->data['id_cliente']       = null;
        $this->data['buscador_cliente'] = null;
    }
}
