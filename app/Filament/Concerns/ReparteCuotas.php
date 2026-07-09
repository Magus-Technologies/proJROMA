<?php

namespace App\Filament\Concerns;

use Carbon\Carbon;

/**
 * Reparte un total entre N cuotas en partes iguales, y completa las fechas
 * faltantes a 30 días de la anterior. Solo se llama cuando cambia la CANTIDAD
 * de cuotas — así nunca pisa un monto que el usuario editó a mano.
 */
trait ReparteCuotas
{
    /**
     * @param  array<string, array<string, mixed>>  $cuotas  estado del repeater (claves = uuid)
     * @return array<string, array<string, mixed>>
     */
    protected static function repartirCuotas(array $cuotas, float $total): array
    {
        $claves = array_keys($cuotas);
        $n      = count($claves);

        if ($n === 0 || $total <= 0) {
            return $cuotas;
        }

        // Base truncada a 2 decimales; la última cuota absorbe el redondeo
        // para que la suma dé exactamente el total (SUNAT no perdona centavos).
        $base      = floor(($total / $n) * 100) / 100;
        $acumulado = 0.0;
        $fechaPrev = null;

        foreach ($claves as $i => $clave) {
            $esUltima = $i === $n - 1;
            $monto    = $esUltima ? round($total - $acumulado, 2) : $base;
            $acumulado += $monto;

            $cuotas[$clave]['monto'] = number_format($monto, 2, '.', '');

            if (blank($cuotas[$clave]['tipo_pago'] ?? null)) {
                $cuotas[$clave]['tipo_pago'] = 'EFECTIVO';
            }

            // Fecha vacía (cuota recién agregada): 30 días después de la anterior.
            if (blank($cuotas[$clave]['fecha'] ?? null)) {
                $cuotas[$clave]['fecha'] = Carbon::parse($fechaPrev ?? now())
                    ->addDays(30)
                    ->toDateString();
            }

            $fechaPrev = $cuotas[$clave]['fecha'];
        }

        return $cuotas;
    }
}
