<?php

namespace App\Filament\Concerns;

use App\Services\CajaService;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;

/**
 * Repetidor reutilizable de "líneas de pago" para el pago mixto: cada línea es
 * un método con su monto, su N° de operación y de 1 a 3 comprobantes.
 *
 * Se usa igual en el contado (la suma debe dar el total de la venta) y en cada
 * cuota de crédito pagada al momento (la suma debe dar el monto de la cuota).
 */
trait ArmaPagosVenta
{
    /** @return array<string, string> */
    protected static function opcionesMetodoPago(): array
    {
        return CajaService::opcionesMetodoPago();
    }

    protected static function repetidorPagos(
        string $campo,
        string $etiqueta = 'Métodos de pago',
        string $etiquetaAgregar = 'Agregar método',
    ): Repeater {
        return Repeater::make($campo)
            ->label($etiqueta)
            ->addActionLabel($etiquetaAgregar)
            ->columns(2)
            ->defaultItems(1)
            ->minItems(1)
            ->reorderable(false)
            ->schema([
                Select::make('metodo_pago')
                    ->label('Método')
                    ->options(fn (): array => static::opcionesMetodoPago())
                    ->default('EFECTIVO')
                    ->live()
                    ->required(),

                TextInput::make('monto')
                    ->label('Monto (S/)')
                    ->numeric()
                    ->minValue(0.01)
                    ->prefix('S/')
                    ->required(),

                // N° de operación y comprobantes solo tienen sentido si no es efectivo.
                TextInput::make('referencia')
                    ->label('N° de operación')
                    ->placeholder('Código del comprobante del pago')
                    ->maxLength(60)
                    ->visible(fn (callable $get): bool => static::noEsEfectivo($get('metodo_pago')))
                    ->columnSpanFull(),

                FileUpload::make('comprobantes')
                    ->label('Comprobantes del pago')
                    ->helperText('Hasta 3 imágenes (captura del Yape, voucher de la transferencia, etc.).')
                    ->image()
                    ->multiple()
                    ->maxFiles(3)
                    ->disk('public')
                    ->directory('vouchers')
                    ->imagePreviewHeight('90')
                    ->maxSize(4096)
                    ->visible(fn (callable $get): bool => static::noEsEfectivo($get('metodo_pago')))
                    ->columnSpanFull(),
            ]);
    }

    /** Efectivo no necesita referencia ni comprobante; todo lo demás sí. */
    protected static function noEsEfectivo(?string $metodo): bool
    {
        return filled($metodo) && $metodo !== 'EFECTIVO';
    }

    /** Suma de una lista de líneas de pago. */
    protected static function sumaPagos(?array $pagos): float
    {
        return round(collect($pagos ?? [])->sum(fn (array $p): float => (float) ($p['monto'] ?? 0)), 2);
    }
}
