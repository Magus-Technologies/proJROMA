<?php

namespace App\Filament\Concerns;

use App\Models\Cliente;
use App\Models\TmsMercado;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Http;

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


    /**
     * Selector de cliente: un Select buscable de Filament. Su desplegable
     * flota por encima del formulario (no empuja los campos de abajo, como sí
     * hacía el buscador inline). Incluye alta rápida de cliente con el mismo
     * formulario completo de la vista de Clientes (consulta SUNAT incluida).
     *
     * @return array<int, \Filament\Schemas\Components\Component>
     */
    public static function clienteBuscadorSchema(): array
    {
        return [
            Select::make('id_cliente')
                ->label('Cliente')
                ->placeholder('Buscar por nombre o documento…')
                ->searchable()
                ->required()
                ->columnSpanFull()
                ->getSearchResultsUsing(fn (string $search): array => Cliente::query()
                    ->where('id_empresa', (int) session('id_empresa'))
                    ->where(fn ($q) => $q
                        ->where('datos', 'like', "%{$search}%")
                        ->orWhere('documento', 'like', "%{$search}%"))
                    ->limit(20)
                    ->get()
                    ->mapWithKeys(fn (Cliente $c) => [
                        $c->id_cliente => $c->datos . ($c->documento ? " — {$c->documento}" : ''),
                    ])
                    ->toArray())
                ->getOptionLabelUsing(function ($value): ?string {
                    $c = Cliente::find($value);

                    return $c ? $c->datos . ($c->documento ? " — {$c->documento}" : '') : null;
                })
                // Al elegir cliente, traer su dirección al campo `direccion` del
                // documento (venta / cotización). Sigue siendo editable.
                ->live()
                ->afterStateUpdated(function ($state, callable $set): void {
                    if ($direccion = Cliente::find($state)?->direccion) {
                        $set('direccion', $direccion);
                    }
                })
                ->createOptionForm(static::clienteFormFields())
                ->createOptionUsing(fn (array $data): int => Cliente::create(array_merge($data, [
                    'id_empresa' => (int) session('id_empresa'),
                ]))->id_cliente)
                ->createOptionModalHeading('Nuevo cliente'),
        ];
    }
}
