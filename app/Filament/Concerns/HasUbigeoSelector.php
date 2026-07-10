<?php

namespace App\Filament\Concerns;

use Filament\Forms\Components\Select;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/**
 * Selector de ubigeo en cascada: Departamento → Provincia → Distrito.
 * El valor final del campo es el código de 6 dígitos que exige SUNAT.
 *
 * La tabla `ubigeo_inei` es jerárquica:
 *   provincia='00' y distrito='00'  → departamento
 *   distrito='00'                    → provincia
 *   el resto                         → distrito
 * El ubigeo son los tres códigos concatenados (ej. 15|01|28 = 150128).
 *
 * Uso — el nombre del campo destino define el de los auxiliares, así se pueden
 * poner varios selectores en un mismo formulario sin que choquen:
 *
 *     ...static::ubigeoSelector('ubigeo', 'Ubigeo de llegada')
 *     ...static::ubigeoSelector('ubigeo_partida', 'Ubigeo de partida')
 */
trait HasUbigeoSelector
{
    /** @return array<int, Select> */
    public static function ubigeoSelector(string $campo, string $etiqueta = 'Distrito', bool $requerido = true): array
    {
        $depto = "{$campo}_departamento";
        $prov  = "{$campo}_provincia";

        return [
            // Departamento y provincia son solo ayudas de navegación: no se guardan.
            Select::make($depto)
                ->label('Departamento')
                ->options(fn (): array => static::departamentos())
                ->searchable()
                ->live()
                ->dehydrated(false)
                ->required($requerido)
                ->afterStateUpdated(function (callable $set) use ($prov, $campo): void {
                    $set($prov, null);
                    $set($campo, null);
                }),

            Select::make($prov)
                ->label('Provincia')
                ->options(fn (callable $get) => static::provincias($get($depto)))
                ->searchable()
                ->live()
                ->dehydrated(false)
                ->required($requerido)
                ->disabled(fn (callable $get): bool => blank($get($depto)))
                ->afterStateUpdated(fn (callable $set) => $set($campo, null)),

            // Este es el campo real: su valor ES el ubigeo de 6 dígitos.
            Select::make($campo)
                ->label($etiqueta)
                ->options(fn (callable $get): array => static::distritos($get($depto), $get($prov)))
                ->searchable()
                ->required($requerido)
                ->disabled(fn (callable $get): bool => blank($get($prov)))
                ->helperText(fn (callable $get): ?string => filled($get($campo)) ? "Ubigeo: {$get($campo)}" : null),
        ];
    }

    /**
     * Deriva departamento y provincia desde un ubigeo de 6 dígitos, para
     * precargar los selectores al editar o al usar valores por defecto.
     *
     * @return array{departamento: ?string, provincia: ?string}
     */
    public static function partesDeUbigeo(?string $ubigeo): array
    {
        $ubigeo = preg_replace('/\D/', '', (string) $ubigeo);

        if (strlen($ubigeo) !== 6) {
            return ['departamento' => null, 'provincia' => null];
        }

        return [
            'departamento' => substr($ubigeo, 0, 2),
            'provincia'    => substr($ubigeo, 2, 2),
        ];
    }

    /** @return array<string, string> */
    protected static function departamentos(): array
    {
        return Cache::remember('ubigeo.departamentos', now()->addDay(), fn (): array => DB::table('ubigeo_inei')
            ->where('provincia', '00')
            ->where('distrito', '00')
            ->orderBy('nombre')
            ->pluck('nombre', 'departamento')
            ->toArray());
    }

    /** @return array<string, string> */
    protected static function provincias(?string $departamento): array
    {
        if (blank($departamento)) {
            return [];
        }

        return Cache::remember("ubigeo.provincias.{$departamento}", now()->addDay(), fn (): array => DB::table('ubigeo_inei')
            ->where('departamento', $departamento)
            ->where('provincia', '!=', '00')
            ->where('distrito', '00')
            ->orderBy('nombre')
            ->pluck('nombre', 'provincia')
            ->toArray());
    }

    /** Distritos con el ubigeo completo (6 dígitos) como valor. @return array<string, string> */
    protected static function distritos(?string $departamento, ?string $provincia): array
    {
        if (blank($departamento) || blank($provincia)) {
            return [];
        }

        return Cache::remember(
            "ubigeo.distritos.{$departamento}.{$provincia}",
            now()->addDay(),
            fn (): array => DB::table('ubigeo_inei')
                ->where('departamento', $departamento)
                ->where('provincia', $provincia)
                ->where('distrito', '!=', '00')
                ->orderBy('nombre')
                ->get(['departamento', 'provincia', 'distrito', 'nombre'])
                ->mapWithKeys(fn ($d): array => [
                    $d->departamento . $d->provincia . $d->distrito => $d->nombre,
                ])
                ->toArray()
        );
    }
}
