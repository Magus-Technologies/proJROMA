<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

/**
 * Saldos de los metodos de pago.
 *
 * Regla madre: el dinero vive UNICAMENTE en cuentas_bancarias.
 *   - Banco              -> suma de sus cuentas (no tiene saldo propio).
 *   - Cuenta bancaria    -> unico contenedor: saldo_inicial + movimientos.
 *   - Billetera digital  -> HEREDA el saldo de su cuenta vinculada.
 *   - Tarjeta de debito  -> HEREDA el saldo de su cuenta vinculada.
 *   - Tarjeta de credito -> no tiene saldo (es deuda, no fondos).
 *   - Efectivo           -> no toca banco, vive en cajas.saldo_actual.
 *
 * Por eso los saldos de billeteras y tarjetas NUNCA se suman a los de las
 * cuentas: seria contar el mismo dinero dos o tres veces.
 *
 * Todo el sistema opera en soles.
 */
class SaldoService
{
    /** Instrumentos que representan dinero en una cuenta bancaria. */
    private const INSTRUMENTOS_BANCARIOS = ['TRANSFERENCIA', 'BILLETERA_DIGITAL', 'TARJETA'];

    /**
     * Saldo de todas las cuentas bancarias de la empresa, en UNA sola consulta.
     *
     * @return array<int, array{saldo_inicial: float, movido: float, saldo: float}>
     *         Indexado por id_cuenta.
     */
    public function saldosPorCuenta(int $idEmpresa): array
    {
        $movimientos = $this->movimientosResueltosPorCuenta($idEmpresa);

        return DB::table('cuentas_bancarias as cb')
            ->leftJoinSub($movimientos, 'm', function ($join) {
                $join->on('m.id_cuenta', '=', 'cb.id_cuenta')
                    ->whereRaw('(cb.fecha_corte IS NULL OR m.fecha >= cb.fecha_corte)');
            })
            ->where('cb.id_empresa', $idEmpresa)
            ->groupBy('cb.id_cuenta', 'cb.saldo_inicial')
            ->selectRaw('cb.id_cuenta, cb.saldo_inicial, COALESCE(SUM(m.neto), 0) as movido')
            ->get()
            ->mapWithKeys(function ($r): array {
                $inicial = (float) $r->saldo_inicial;
                $movido  = (float) $r->movido;

                return [(int) $r->id_cuenta => [
                    'saldo_inicial' => round($inicial, 2),
                    'movido'        => round($movido, 2),
                    'saldo'         => round($inicial + $movido, 2),
                ]];
            })
            ->toArray();
    }

    /**
     * Saldo consolidado por banco = suma de sus cuentas.
     *
     * @return array<int, float> Indexado por id_banco.
     */
    public function saldosPorBanco(int $idEmpresa): array
    {
        $porCuenta = $this->saldosPorCuenta($idEmpresa);

        $bancoDeCuenta = DB::table('cuentas_bancarias')
            ->where('id_empresa', $idEmpresa)
            ->pluck('id_banco', 'id_cuenta');

        $totales = [];
        foreach ($porCuenta as $idCuenta => $datos) {
            $idBanco = (int) ($bancoDeCuenta[$idCuenta] ?? 0);
            if ($idBanco === 0) {
                continue;
            }
            $totales[$idBanco] = round(($totales[$idBanco] ?? 0) + $datos['saldo'], 2);
        }

        return $totales;
    }

    /**
     * Cuanto dinero fluyo POR CADA BILLETERA (no es su saldo: es su movimiento).
     *
     * A diferencia del saldo, este numero SI es propio del canal y se puede
     * sumar sin duplicar, porque filtra por el instrumento exacto.
     *
     * @return array<int, float> Indexado por id_billetera.
     */
    public function movidoPorBilletera(int $idEmpresa): array
    {
        return DB::table('caja_movimientos as m')
            ->join('cajas as c', 'c.id', '=', 'm.id_caja')
            ->join('billeteras_digitales as bd', 'bd.id_billetera', '=', 'm.instrumento_id')
            ->where('c.id_empresa', $idEmpresa)
            ->where('bd.id_empresa', $idEmpresa)
            ->where('m.estado', 'CONFIRMADO')
            ->where('m.instrumento_tipo', 'BILLETERA_DIGITAL')
            ->groupBy('bd.id_billetera')
            ->selectRaw("bd.id_billetera, SUM(CASE WHEN m.tipo = 'INGRESO' THEN m.monto ELSE -m.monto END) as neto")
            ->pluck('neto', 'id_billetera')
            ->map(fn ($v): float => round((float) $v, 2))
            ->toArray();
    }

    /**
     * Movimientos bancarios ya normalizados a la cuenta que les corresponde.
     * Una billetera o una tarjeta se resuelven a su cuenta vinculada; el
     * efectivo queda fuera porque no representa dinero en banco.
     */
    private function movimientosResueltosPorCuenta(int $idEmpresa)
    {
        return DB::table('caja_movimientos as m')
            ->join('cajas as c', 'c.id', '=', 'm.id_caja')
            ->leftJoin('billeteras_digitales as bd', function ($join) {
                $join->on('bd.id_billetera', '=', 'm.instrumento_id')
                    ->where('m.instrumento_tipo', '=', 'BILLETERA_DIGITAL');
            })
            ->leftJoin('tarjetas as t', function ($join) {
                $join->on('t.id_tarjeta', '=', 'm.instrumento_id')
                    ->where('m.instrumento_tipo', '=', 'TARJETA');
            })
            ->where('c.id_empresa', $idEmpresa)
            ->where('m.estado', 'CONFIRMADO')
            ->whereIn('m.instrumento_tipo', self::INSTRUMENTOS_BANCARIOS)
            ->whereNotNull('m.instrumento_id')
            ->groupBy('id_cuenta', 'm.fecha')
            ->selectRaw("
                CASE m.instrumento_tipo
                    WHEN 'TRANSFERENCIA'     THEN m.instrumento_id
                    WHEN 'BILLETERA_DIGITAL' THEN bd.id_cuenta_bancaria
                    WHEN 'TARJETA'           THEN t.id_cuenta_bancaria
                END as id_cuenta,
                m.fecha,
                SUM(CASE WHEN m.tipo = 'INGRESO' THEN m.monto ELSE -m.monto END) as neto
            ")
            ->havingRaw('id_cuenta IS NOT NULL');
    }
}
