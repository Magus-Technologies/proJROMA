<?php
namespace App\Services;

use Illuminate\Support\Facades\DB;

class CajaService
{
    /**
     * Registrar un movimiento en una caja y actualizar su saldo.
     *
     * @param array  $data  id_caja, fecha, tipo, categoria, descripcion, monto,
     *                      instrumento_tipo, instrumento_id, origen_tipo, origen_id, id_usuario
     * @return int ID del movimiento creado
     */
    public function registrarMovimiento(array $data): int
    {
        return DB::transaction(function () use ($data) {
            // 1. Obtener saldo actual de la caja
            $caja = DB::table('cajas')->where('id', $data['id_caja'])->lockForUpdate()->first();
            if (!$caja) throw new \RuntimeException('Caja no encontrada.');

            $saldoAnterior = $caja->saldo_actual;
            $monto = (float) $data['monto'];
            $tipo = $data['tipo'];

            if ($tipo === 'INGRESO') {
                $saldoPosterior = $saldoAnterior + $monto;
            } else {
                $saldoPosterior = $saldoAnterior - $monto;
            }

            // 2. Insertar movimiento
            $id = DB::table('caja_movimientos')->insertGetId([
                'id_caja'          => $data['id_caja'],
                'fecha'            => $data['fecha'] ?? now()->toDateString(),
                'tipo'             => $tipo,
                'categoria'        => $data['categoria'] ?? 'MANUAL',
                'descripcion'      => $data['descripcion'] ?? null,
                'monto'            => $monto,
                'instrumento_tipo' => $data['instrumento_tipo'] ?? null,
                'instrumento_id'   => $data['instrumento_id'] ?? null,
                'saldo_anterior'   => $saldoAnterior,
                'saldo_posterior'  => $saldoPosterior,
                'origen_tipo'      => $data['origen_tipo'] ?? null,
                'origen_id'        => $data['origen_id'] ?? null,
                'id_usuario'       => $data['id_usuario'],
                'estado'           => 'CONFIRMADO',
            ]);

            // 3. Actualizar saldo de la caja
            DB::table('cajas')->where('id', $data['id_caja'])->update([
                'saldo_actual' => $saldoPosterior,
            ]);

            return $id;
        });
    }

    /**
     * Anular un movimiento (restaurar saldo anterior).
     */
    public function anularMovimiento(int $idMovimiento): void
    {
        DB::transaction(function () use ($idMovimiento) {
            $mov = DB::table('caja_movimientos')->where('id', $idMovimiento)->lockForUpdate()->first();
            if (!$mov || $mov->estado === 'ANULADO') return;

            $caja = DB::table('cajas')->where('id', $mov->id_caja)->lockForUpdate()->first();

            // Restaurar saldo anterior
            DB::table('cajas')->where('id', $mov->id_caja)->update([
                'saldo_actual' => $mov->saldo_anterior,
            ]);

            DB::table('caja_movimientos')->where('id', $idMovimiento)->update([
                'estado' => 'ANULADO',
                'saldo_posterior' => $mov->saldo_anterior,
            ]);
        });
    }

    /**
     * Crear caja por defecto para una empresa/sucursal (GENERAL + CHICA).
     */
    public function crearCajasDefault(int $idEmpresa, int $sucursal): void
    {
        $existe = DB::table('cajas')
            ->where('id_empresa', $idEmpresa)
            ->where('sucursal', $sucursal)
            ->exists();

        if ($existe) return;

        DB::table('cajas')->insert([
            [
                'id_empresa' => $idEmpresa,
                'sucursal'   => $sucursal,
                'nombre'     => 'Caja Principal',
                'tipo'       => 'GENERAL',
                'saldo_actual' => 0,
                'moneda'     => 'PEN',
                'estado'     => 'ACTIVA',
            ],
            [
                'id_empresa' => $idEmpresa,
                'sucursal'   => $sucursal,
                'nombre'     => 'Caja Chica',
                'tipo'       => 'CHICA',
                'saldo_actual' => 0,
                'moneda'     => 'PEN',
                'estado'     => 'ACTIVA',
            ],
        ]);
    }
}
