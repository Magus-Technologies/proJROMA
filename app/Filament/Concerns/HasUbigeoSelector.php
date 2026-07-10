<?php

namespace App\Filament\Concerns;

use Closure;
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
    /**
     * @param  ?Closure  $alElegir  Se ejecuta al elegir distrito, con ($state, $set, $get).
     * @return array<int, Select>
     */
    public static function ubigeoSelector(
        string $campo,
        string $etiqueta = 'Distrito',
        bool $requerido = true,
        ?Closure $alElegir = null,
    ): array {
        $depto = "{$campo}_departamento";
        $prov  = "{$campo}_provincia";

        return [
            // Departamento y provincia son solo ayudas de navegación: no se guardan.
            // Al editar un registro se reconstruyen desde el ubigeo ya almacenado.
            Select::make($depto)
                ->label('Departamento')
                ->options(fn (): array => static::departamentos())
                ->searchable()
                ->live()
                ->dehydrated(false)
                ->required($requerido)
                ->afterStateHydrated(fn (Select $componente, callable $get) => $componente->state(
                    static::partesDeUbigeo($get($campo))['departamento']
                ))
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
                ->afterStateHydrated(fn (Select $componente, callable $get) => $componente->state(
                    static::partesDeUbigeo($get($campo))['provincia']
                ))
                ->afterStateUpdated(fn (callable $set) => $set($campo, null)),

            // Este es el campo real: su valor ES el ubigeo de 6 dígitos.
            //
            // PHP convierte a entero las claves numéricas de un array, así que
            // "150128" llega como int 150128 mientras que "020504" conserva el
            // cero inicial y sigue siendo string. Normalizamos siempre a string
            // de 6 caracteres: SUNAT valida el ubigeo como cadena.
            Select::make($campo)
                ->label($etiqueta)
                ->options(fn (callable $get): array => static::distritos($get($depto), $get($prov)))
                ->searchable()
                ->live()
                ->required($requerido)
                ->dehydrateStateUsing(fn ($state): ?string => static::normalizaUbigeo($state))
                ->disabled(fn (callable $get): bool => blank($get($prov)))
                ->afterStateUpdated(function ($state, callable $set, callable $get) use ($alElegir): void {
                    if ($alElegir) {
                        $alElegir($state, $set, $get);
                    }
                })
                ->helperText(fn (callable $get): ?string => filled($get($campo)) ? "Ubigeo: {$get($campo)}" : null),
        ];
    }

    /** Devuelve el ubigeo como string de 6 dígitos, o null si no es válido. */
    public static function normalizaUbigeo(mixed $ubigeo): ?string
    {
        if (blank($ubigeo)) {
            return null;
        }

        $limpio = preg_replace('/\D/', '', (string) $ubigeo);

        return strlen($limpio) <= 6 ? str_pad($limpio, 6, '0', STR_PAD_LEFT) : null;
    }

    /** Nombre del distrito a partir del ubigeo completo, para campos de texto derivados. */
    public static function nombreDeDistrito(?string $ubigeo): ?string
    {
        $partes = static::partesDeUbigeo($ubigeo);

        if (blank($partes['departamento'])) {
            return null;
        }

        return static::distritos($partes['departamento'], $partes['provincia'])[$ubigeo] ?? null;
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
