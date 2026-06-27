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

        $idPadre = DB::table('cajas')->insertGetId([
            'id_empresa' => $idEmpresa,
            'sucursal'   => $sucursal,
            'nombre'     => 'Caja Principal',
            'saldo_actual' => 0,
            'moneda'     => 'PEN',
            'estado'     => 'ACTIVA',
        ]);

        DB::table('cajas')->insert([
            'id_empresa' => $idEmpresa,
            'sucursal'   => $sucursal,
            'nombre'     => 'Caja Chica',
            'id_caja_padre' => $idPadre,
            'saldo_actual' => 0,
            'moneda'     => 'PEN',
            'estado'     => 'ACTIVA',
        ]);
    }

    /**
     * Registrar el cierre diario de una caja.
     */
    public function cerrarCaja(int $idCaja, float $saldoDeclarado, array $desglose, int $idUsuario): int
    {
        return DB::transaction(function () use ($idCaja, $saldoDeclarado, $desglose, $idUsuario) {
            $caja = DB::table('cajas')->where('id', $idCaja)->lockForUpdate()->first();
            if (!$caja) throw new \RuntimeException('Caja no encontrada.');

            $saldoSistema = (float) $caja->saldo_actual;
            $diferencia = $saldoDeclarado - $saldoSistema;

            // Registrar movimiento de AJUSTE si hay diferencia
            if (abs($diferencia) > 0.001) {
                $tipoAjuste = $diferencia > 0 ? 'INGRESO' : 'EGRESO';
                $montoAjuste = abs($diferencia);
                $this->registrarMovimiento([
                    'id_caja' => $idCaja,
                    'fecha' => now()->toDateString(),
                    'tipo' => $tipoAjuste,
                    'categoria' => 'AJUSTE',
                    'descripcion' => $diferencia > 0 ? 'Ajuste por sobrante en cierre' : 'Ajuste por faltante en cierre',
                    'monto' => $montoAjuste,
                    'id_usuario' => $idUsuario,
                ]);
            }

            // Registrar el movimiento de CIERRE (monto 0 para no alterar saldo)
            $this->registrarMovimiento([
                'id_caja' => $idCaja,
                'fecha' => now()->toDateString(),
                'tipo' => 'EGRESO',
                'categoria' => 'CIERRE',
                'descripcion' => 'Cierre de caja diario',
                'monto' => 0,
                'id_usuario' => $idUsuario,
            ]);

            // Insertar el registro de cierre
            $idCierre = DB::table('cierre_caja')->insertGetId([
                'id_caja' => $idCaja,
                'fecha' => now()->toDateString(),
                'saldo_declarado' => $saldoDeclarado,
                'saldo_sistema' => $saldoSistema,
                'desglose_instrumentos' => json_encode($desglose),
                'estado' => 'PENDIENTE',
                'id_usuario_cierra' => $idUsuario,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return $idCierre;
        });
    }

    /**
     * Aprobar o rechazar el cierre de caja.
     */
    public function aprobarCierre(int $idCierre, int $idUsuarioAprueba, string $nuevoEstado = 'APROBADO', ?string $observaciones = null): void
    {
        DB::transaction(function () use ($idCierre, $idUsuarioAprueba, $nuevoEstado, $observaciones) {
            $cierre = DB::table('cierre_caja')->where('id', $idCierre)->lockForUpdate()->first();
            if (!$cierre) throw new \RuntimeException('Cierre no encontrado.');
            if ($cierre->estado !== 'PENDIENTE') return;

            DB::table('cierre_caja')->where('id', $idCierre)->update([
                'estado' => $nuevoEstado,
                'id_usuario_aprueba' => $idUsuarioAprueba,
                'observaciones' => $observaciones,
                'updated_at' => now(),
            ]);

            if ($nuevoEstado === 'APROBADO') {
                // Generar movimiento de CUADRE en la caja principal (padre) si existe
                $cajaHija = DB::table('cajas')->where('id', $cierre->id_caja)->first();
                if ($cajaHija && $cajaHija->id_caja_padre) {
                    $this->registrarMovimiento([
                        'id_caja' => $cajaHija->id_caja_padre,
                        'fecha' => now()->toDateString(),
                        'tipo' => 'INGRESO',
                        'categoria' => 'CUADRE',
                        'descripcion' => 'Cuadre consolidado de caja: ' . $cajaHija->nombre . ' (Cierre #' . $idCierre . ')',
                        'monto' => 0,
                        'id_usuario' => $idUsuarioAprueba,
                    ]);
                }
            }
        });
    }

    /**
     * Consolidar el estado de las cajas hijas de una caja principal.
     */
    public function consolidadoCajasHijas(int $idCajaPadre, string $fecha): array
    {
        $hijas = DB::table('cajas')->where('id_caja_padre', $idCajaPadre)->get();
        $idsHijas = $hijas->pluck('id')->toArray();

        // Incluir también la propia caja padre en la consulta
        $ids = empty($idsHijas) ? [$idCajaPadre] : $idsHijas;

        $cierres = DB::table('cierre_caja as cc')
            ->join('cajas as c', 'c.id', '=', 'cc.id_caja')
            ->leftJoin('usuarios as uc', 'uc.usuario_id', '=', 'cc.id_usuario_cierra')
            ->leftJoin('usuarios as ua', 'ua.usuario_id', '=', 'cc.id_usuario_aprueba')
            ->whereIn('cc.id_caja', $ids)
            ->where('cc.fecha', $fecha)
            ->select(
                'cc.*',
                'c.nombre as caja_nombre',
                DB::raw("COALESCE(NULLIF(CONCAT_WS(' ', uc.nombres, uc.apellidos), ''), '-') as usuario_cierra_nombre"),
                DB::raw("COALESCE(NULLIF(CONCAT_WS(' ', ua.nombres, ua.apellidos), ''), '-') as usuario_aprueba_nombre")
            )
            ->get();

        $totalDeclarado = $cierres->sum('saldo_declarado');
        $totalSistema = $cierres->sum('saldo_sistema');

        return [
            'total_declarado' => (float) $totalDeclarado,
            'total_sistema' => (float) $totalSistema,
            'diferencia' => (float) ($totalDeclarado - $totalSistema),
            'cierres' => $cierres,
        ];
    }
}
