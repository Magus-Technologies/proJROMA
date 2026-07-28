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
                // Los movimientos de conciliación (ej. AJUSTE por faltante al
                // aprobar un cierre) deben aplicarse aunque dejen la caja en
                // negativo: registran que el dinero realmente no está.
                $permitirNegativo = (bool) ($data['permitir_negativo'] ?? false);
                if (!$permitirNegativo && $monto > 0 && $saldoPosterior < 0) {
                    throw new \RuntimeException(
                        "Saldo insuficiente en la caja \"{$caja->nombre}\". Disponible: S/ "
                        . number_format($saldoAnterior, 2)
                        . ' — se intentó egresar S/ ' . number_format($monto, 2) . '.'
                    );
                }
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
                'referencia'       => $data['referencia'] ?? null,
                'saldo_anterior'   => $saldoAnterior,
                'saldo_posterior'  => $saldoPosterior,
                'origen_tipo'      => $data['origen_tipo'] ?? null,
                'origen_id'        => $data['origen_id'] ?? null,
                'id_usuario'       => $data['id_usuario'],
                'estado'           => 'CONFIRMADO',
                'created_at'       => now(),
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

            // Revertir solo el DELTA de este movimiento sobre el saldo actual.
            // (Restaurar saldo_anterior pisaría los movimientos posteriores.)
            $delta = $mov->tipo === 'INGRESO' ? -(float) $mov->monto : +(float) $mov->monto;

            DB::table('cajas')->where('id', $mov->id_caja)->update([
                'saldo_actual' => round((float) $caja->saldo_actual + $delta, 2),
            ]);

            DB::table('caja_movimientos')->where('id', $idMovimiento)->update([
                'estado' => 'ANULADO',
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
     * Asignar fondo desde una caja principal (bóveda) hacia una caja hija.
     * El dinero sale de la bóveda EN ESTE MOMENTO (el sobre ya se preparó):
     * así nadie puede asignar dos veces el mismo efectivo — la validación de
     * saldo insuficiente lo impide sola.
     */
    public function asignarFondo(int $idCajaOrigen, int $idCajaDestino, float $monto, int $idUsuarioAsigna, ?string $observaciones = null): int
    {
        return DB::transaction(function () use ($idCajaOrigen, $idCajaDestino, $monto, $idUsuarioAsigna, $observaciones) {
            if ($monto <= 0) throw new \RuntimeException('El monto debe ser mayor a 0.');
            if ($idCajaOrigen === $idCajaDestino) throw new \RuntimeException('La caja origen y destino no pueden ser la misma.');

            $origen = DB::table('cajas')->where('id', $idCajaOrigen)->first();
            $destino = DB::table('cajas')->where('id', $idCajaDestino)->first();
            if (!$origen || !$destino) throw new \RuntimeException('Caja no encontrada.');
            if ($destino->id_caja_padre === null) throw new \RuntimeException('El destino debe ser una caja hija (las principales no aperturan turno).');

            // El cajero responsable es el de la caja destino: no se elige a mano
            $idCajero = (int) ($destino->id_usuario_responsable ?? 0);
            if ($idCajero === 0) throw new \RuntimeException('La caja destino no tiene responsable asignado. Configúralo primero en Gestión de Cajas.');

            $yaAsignada = DB::table('transferencias_fondo')
                ->where('id_caja_destino', $idCajaDestino)
                ->where('estado', 'ASIGNADA')
                ->exists();
            if ($yaAsignada) throw new \RuntimeException('La caja destino ya tiene una asignación pendiente de aplicar.');

            $abierta = DB::table('caja_aperturas')
                ->where('id_caja', $idCajaDestino)
                ->where('estado', 'ABIERTA')
                ->orderByDesc('id')
                ->first();
            if ($abierta) {
                throw new \RuntimeException('La caja "' . $destino->nombre . '" tiene un turno abierto desde el '
                    . \Carbon\Carbon::parse($abierta->created_at)->format('d/m/Y H:i')
                    . ' (apertura #' . $abierta->id . '). Ciérralo antes de asignarle un nuevo fondo.');
            }

            // Un cierre pendiente aún puede ser RECHAZADO (lo que reabre el
            // turno); no se asigna fondo nuevo hasta que se apruebe o rechace.
            $cierrePendiente = DB::table('cierre_caja')
                ->where('id_caja', $idCajaDestino)
                ->where('estado', 'PENDIENTE')
                ->orderByDesc('id')
                ->first();
            if ($cierrePendiente) {
                throw new \RuntimeException('La caja "' . $destino->nombre . '" tiene el cierre #' . $cierrePendiente->id
                    . ' pendiente de aprobación. Apruébalo o recházalo en Cierres y Cuadre antes de asignar un nuevo fondo.');
            }

            $idTransferencia = DB::table('transferencias_fondo')->insertGetId([
                'id_caja_origen' => $idCajaOrigen,
                'id_caja_destino' => $idCajaDestino,
                'id_usuario_asigna' => $idUsuarioAsigna,
                'id_usuario_cajero' => $idCajero,
                'monto' => $monto,
                'estado' => 'ASIGNADA',
                'observaciones' => $observaciones,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // El egreso valida saldo suficiente de la bóveda por sí solo
            $idMovimiento = $this->registrarMovimiento([
                'id_caja' => $idCajaOrigen,
                'tipo' => 'EGRESO',
                'categoria' => 'TRANSFERENCIA',
                'descripcion' => 'Asignación de fondo a ' . $destino->nombre . ' (asignación #' . $idTransferencia . ')',
                'monto' => $monto,
                'instrumento_tipo' => 'EFECTIVO',
                'origen_tipo' => 'TRANSFERENCIA_FONDO',
                'origen_id' => $idTransferencia,
                'id_usuario' => $idUsuarioAsigna,
            ]);

            DB::table('transferencias_fondo')->where('id', $idTransferencia)
                ->update(['id_movimiento_egreso' => $idMovimiento, 'updated_at' => now()]);

            // Avisar al cajero responsable en su campana de notificaciones
            try {
                $cajero = \App\Models\User::where('usuario_id', $idCajero)->first();
                if ($cajero) {
                    \App\Services\AlertaStockService::enviar($cajero, \Filament\Notifications\Notification::make()
                        ->info()
                        ->icon('heroicon-o-banknotes')
                        ->title('Fondo asignado a tu caja "' . $destino->nombre . '"')
                        ->body('S/ ' . number_format($monto, 2) . ' desde "' . $origen->nombre
                            . '". Cuenta el efectivo recibido y apertura tu caja para aplicarlo.'),
                        \App\Filament\Pages\MiCaja::getUrl());
                }
            } catch (\Throwable $e) {
                report($e);
            }

            return $idTransferencia;
        });
    }

    /**
     * Anular (admin) o rechazar (cajero) una asignación aún no aplicada:
     * el efectivo regresa a la bóveda con un movimiento de reversa.
     */
    public function revertirAsignacion(int $idTransferencia, int $idUsuario, string $nuevoEstado = 'ANULADA', ?string $motivo = null): void
    {
        DB::transaction(function () use ($idTransferencia, $idUsuario, $nuevoEstado, $motivo) {
            $tr = DB::table('transferencias_fondo')->where('id', $idTransferencia)->lockForUpdate()->first();
            if (!$tr) throw new \RuntimeException('Asignación no encontrada.');
            if ($tr->estado !== 'ASIGNADA') throw new \RuntimeException('Solo se puede revertir una asignación pendiente.');

            $this->registrarMovimiento([
                'id_caja' => $tr->id_caja_origen,
                'tipo' => 'INGRESO',
                'categoria' => 'TRANSFERENCIA',
                'descripcion' => 'Reversa de asignación #' . $idTransferencia . ($motivo ? ' — ' . $motivo : ''),
                'monto' => (float) $tr->monto,
                'instrumento_tipo' => 'EFECTIVO',
                'origen_tipo' => 'TRANSFERENCIA_FONDO',
                'origen_id' => $idTransferencia,
                'id_usuario' => $idUsuario,
            ]);

            $estadoFinal = in_array($nuevoEstado, ['ANULADA', 'RECHAZADA'], true) ? $nuevoEstado : 'ANULADA';

            DB::table('transferencias_fondo')->where('id', $idTransferencia)->update([
                'estado' => $estadoFinal,
                'observaciones' => trim(($tr->observaciones ? $tr->observaciones . ' | ' : '') . ($motivo ?? '')) ?: $tr->observaciones,
                'updated_at' => now(),
            ]);

            // Avisar al cajero que la asignación quedó sin efecto
            try {
                $cajero = \App\Models\User::where('usuario_id', (int) $tr->id_usuario_cajero)->first();
                if ($cajero) {
                    \App\Services\AlertaStockService::enviar($cajero, \Filament\Notifications\Notification::make()
                        ->warning()
                        ->icon('heroicon-o-arrow-uturn-left')
                        ->title('Asignación de fondo ' . ($estadoFinal === 'RECHAZADA' ? 'rechazada' : 'anulada'))
                        ->body('La asignación #' . $idTransferencia . ' de S/ ' . number_format((float) $tr->monto, 2)
                            . ' quedó sin efecto' . ($motivo ? ': ' . $motivo : '.')),
                        \App\Filament\Pages\MiCaja::getUrl());
                }
            } catch (\Throwable $e) {
                report($e);
            }
        });
    }

    /**
     * El cajero cuenta el efectivo recibido y apertura la caja contra la
     * asignación. Si el conteo difiere del monto asignado, la caja abre con
     * lo CONTADO y queda una discrepancia pendiente de resolver por un
     * supervisor (el faltante no es deuda del cajero: recién recibe).
     */
    public function abrirCajaConFondo(int $idTransferencia, float $montoContado, array $detalles, int $idUsuario, ?string $fecha = null, ?string $observaciones = null): int
    {
        return DB::transaction(function () use ($idTransferencia, $montoContado, $detalles, $idUsuario, $fecha, $observaciones) {
            $tr = DB::table('transferencias_fondo')->where('id', $idTransferencia)->lockForUpdate()->first();
            if (!$tr || $tr->estado !== 'ASIGNADA') throw new \RuntimeException('La asignación ya no está disponible.');
            if ($montoContado <= 0) throw new \RuntimeException('El conteo del efectivo debe ser mayor a 0.');

            $yaAbierta = DB::table('caja_aperturas')
                ->where('id_caja', $tr->id_caja_destino)
                ->where('estado', 'ABIERTA')
                ->exists();
            if ($yaAbierta) throw new \RuntimeException('La caja ya tiene una apertura abierta.');

            $idApertura = DB::table('caja_aperturas')->insertGetId([
                'id_caja' => $tr->id_caja_destino,
                'id_transferencia' => $tr->id,
                'fecha' => $fecha ?? now()->toDateString(),
                'monto_total' => $montoContado,
                'estado' => 'ABIERTA',
                'id_usuario_apertura' => $idUsuario,
                'observaciones' => $observaciones,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            if ($detalles !== []) {
                DB::table('caja_apertura_detalles')->insert(array_map(fn (array $d): array => [
                    'id_apertura' => $idApertura,
                    'denominacion' => $d['denominacion'],
                    'tipo' => $d['tipo'],
                    'cantidad' => $d['cantidad'],
                    'subtotal' => $d['subtotal'],
                ], $detalles));
            }

            $this->registrarMovimiento([
                'id_caja' => $tr->id_caja_destino,
                'fecha' => $fecha ?? now()->toDateString(),
                'tipo' => 'INGRESO',
                'categoria' => 'APERTURA',
                'descripcion' => 'Apertura de caja (fondo asignado #' . $tr->id . ')',
                'monto' => $montoContado,
                'instrumento_tipo' => 'EFECTIVO',
                'origen_tipo' => 'APERTURA',
                'origen_id' => $idApertura,
                'id_usuario' => $idUsuario,
            ]);

            $diferencia = round($montoContado - (float) $tr->monto, 2);

            DB::table('transferencias_fondo')->where('id', $tr->id)->update([
                'estado' => 'APLICADA',
                'monto_contado' => $montoContado,
                'discrepancia_estado' => abs($diferencia) > 0.001 ? 'PENDIENTE' : null,
                'updated_at' => now(),
            ]);

            return $idApertura;
        });
    }

    /**
     * Un supervisor resuelve la discrepancia de una apertura con fondo asignado.
     * - AJUSTE_BOVEDA: el sobre se preparó mal. Faltante → el dinero sigue en
     *   bóveda (INGRESO de ajuste); sobrante → salió de más (EGRESO de ajuste).
     * - PERDIDA: el dinero se perdió en tránsito; queda documentado sin ajuste.
     */
    public function resolverDiscrepancia(int $idTransferencia, string $resolucion, int $idUsuario, ?string $observaciones = null): void
    {
        DB::transaction(function () use ($idTransferencia, $resolucion, $idUsuario, $observaciones) {
            $tr = DB::table('transferencias_fondo')->where('id', $idTransferencia)->lockForUpdate()->first();
            if (!$tr) throw new \RuntimeException('Asignación no encontrada.');
            if ($tr->discrepancia_estado !== 'PENDIENTE') throw new \RuntimeException('Esta asignación no tiene discrepancia pendiente.');

            $diferencia = round((float) $tr->monto_contado - (float) $tr->monto, 2);

            if ($resolucion === 'AJUSTE_BOVEDA' && abs($diferencia) > 0.001) {
                $this->registrarMovimiento([
                    'id_caja' => $tr->id_caja_origen,
                    'tipo' => $diferencia < 0 ? 'INGRESO' : 'EGRESO',
                    'categoria' => 'AJUSTE',
                    'descripcion' => ($diferencia < 0
                        ? 'Ajuste: fondo asignado no entregado (asignación #'
                        : 'Ajuste: fondo entregado de más (asignación #') . $tr->id . ')',
                    'monto' => abs($diferencia),
                    'instrumento_tipo' => 'EFECTIVO',
                    'origen_tipo' => 'TRANSFERENCIA_FONDO',
                    'origen_id' => $tr->id,
                    'id_usuario' => $idUsuario,
                    // Conciliación: se aplica aunque la bóveda quede en negativo
                    'permitir_negativo' => true,
                ]);
            }

            DB::table('transferencias_fondo')->where('id', $tr->id)->update([
                'discrepancia_estado' => 'RESUELTA',
                'discrepancia_resolucion' => $resolucion === 'AJUSTE_BOVEDA' ? 'AJUSTE_BOVEDA' : 'PERDIDA',
                'id_usuario_resuelve' => $idUsuario,
                'observaciones' => trim(($tr->observaciones ? $tr->observaciones . ' | ' : '') . ($observaciones ?? '')) ?: $tr->observaciones,
                'updated_at' => now(),
            ]);
        });
    }

    /**
     * Registrar el cierre diario de una caja.
     */
    public function cerrarCaja(int $idCaja, float $saldoDeclarado, array $desglose, int $idUsuario): int
    {
        return DB::transaction(function () use ($idCaja, $saldoDeclarado, $desglose, $idUsuario) {
            $caja = DB::table('cajas')->where('id', $idCaja)->lockForUpdate()->first();
            if (!$caja) throw new \RuntimeException('Caja no encontrada.');

            // El cierre de turno es solo de cajas hijas: la caja principal
            // consolida, no cierra turnos.
            if (!$caja->id_caja_padre) {
                throw new \RuntimeException('Solo las cajas hijas realizan cierre de turno.');
            }

            $apertura = DB::table('caja_aperturas')
                ->where('id_caja', $idCaja)
                ->where('estado', 'ABIERTA')
                ->orderByDesc('id')
                ->lockForUpdate()
                ->first();
            if (!$apertura) {
                throw new \RuntimeException('No hay una apertura abierta para esta caja: apertura la caja antes de cerrarla.');
            }

            // El cuadre es del TURNO, no del histórico de la caja:
            // fondo de apertura + ingresos − egresos desde la apertura.
            // (El fondo inicial ya está incluido: se registra como INGRESO
            // de categoría APERTURA al aperturar.)
            $saldoSistema = (float) DB::table('caja_movimientos')
                ->where('id_caja', $idCaja)
                ->where('estado', 'CONFIRMADO')
                ->where('created_at', '>=', $apertura->created_at)
                ->selectRaw("COALESCE(SUM(CASE WHEN tipo = 'INGRESO' THEN monto ELSE -monto END), 0) as s")
                ->value('s');

            // La diferencia NO se ajusta aquí: el AJUSTE (y la deuda del
            // trabajador si hay faltante) se generan al APROBAR el cierre.
            // Así un cierre rechazado no deja rastros que revertir.
            //
            // El cierre NO genera movimiento en caja_movimientos: no mueve
            // dinero. Su registro vive únicamente en cierre_caja (pantalla
            // Cierres y Cuadre). Solo el AJUSTE por diferencia (al aprobar)
            // es un movimiento real.

            // Insertar el registro de cierre
            $idCierre = DB::table('cierre_caja')->insertGetId([
                'id_caja' => $idCaja,
                'id_apertura' => $apertura->id,
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
                $diferencia = round((float) $cierre->saldo_declarado - (float) $cierre->saldo_sistema, 2);

                // Ajustar el saldo de la caja a lo realmente declarado
                if (abs($diferencia) > 0.001) {
                    $this->registrarMovimiento([
                        'id_caja'     => $cierre->id_caja,
                        'fecha'       => now()->toDateString(),
                        'tipo'        => $diferencia > 0 ? 'INGRESO' : 'EGRESO',
                        'categoria'   => 'AJUSTE',
                        'descripcion' => ($diferencia > 0 ? 'Ajuste por sobrante' : 'Ajuste por faltante') . ' en cierre #' . $idCierre,
                        'monto'       => abs($diferencia),
                        'origen_tipo' => 'CIERRE',
                        'origen_id'   => $idCierre,
                        'id_usuario'  => $idUsuarioAprueba,
                        // Conciliación: se aplica aunque la caja quede en negativo
                        'permitir_negativo' => true,
                    ]);
                }

                // El faltante queda como deuda del trabajador que cerró
                if ($diferencia < -0.001) {
                    DB::table('caja_cierre_deudas')->insert([
                        'id_cierre'           => $idCierre,
                        'id_caja'             => $cierre->id_caja,
                        'id_usuario'          => $cierre->id_usuario_cierra,
                        'monto'               => abs($diferencia),
                        'estado'              => 'PENDIENTE',
                        'observaciones'       => 'Faltante en cierre de caja del ' . $cierre->fecha,
                        'id_usuario_registra' => $idUsuarioAprueba,
                        'created_at'          => now(),
                        'updated_at'          => now(),
                    ]);
                }

                // (El "cuadre" en la caja padre tampoco genera movimiento:
                // era una marca de monto 0 que solo ensuciaba el historial.)
            }

            if ($nuevoEstado === 'RECHAZADO') {
                // Reabrir la apertura para que el trabajador corrija y vuelva a cerrar
                $apertura = DB::table('caja_aperturas')
                    ->where('id_caja', $cierre->id_caja)
                    ->where('estado', 'CERRADA')
                    ->orderByDesc('id')
                    ->first();
                if ($apertura) {
                    DB::table('caja_aperturas')->where('id', $apertura->id)
                        ->update(['estado' => 'ABIERTA', 'updated_at' => now()]);
                }
            }
        });
    }

    /**
     * Mapear tipo de pago al instrumento_tipo de caja.
     */
    /** Cache por request de las opciones de método de pago. */
    protected static ?array $opcionesMetodoPago = null;

    /**
     * Métodos de pago disponibles según Métodos de Pago: Efectivo (fijo) +
     * billeteras registradas + cuentas bancarias (transferencia/depósito).
     * Las claves caben en ventas.metodo_pago (varchar 20).
     */
    public static function opcionesMetodoPago(): array
    {
        if (static::$opcionesMetodoPago !== null) {
            return static::$opcionesMetodoPago;
        }

        $empresa  = (int) session('id_empresa');
        $opciones = ['EFECTIVO' => 'Efectivo'];

        DB::table('billeteras_digitales as bd')
            ->join('billetera_tipos as bt', 'bt.id', '=', 'bd.id_billetera_tipo')
            ->where('bd.id_empresa', $empresa)
            ->where('bd.estado', 1)
            ->orderBy('bt.nombre')
            ->get(['bd.id_billetera', 'bt.nombre', 'bd.telefono', 'bd.titular'])
            ->each(function ($b) use (&$opciones): void {
                $opciones["BILLETERA|{$b->id_billetera}"] = trim("{$b->nombre} · {$b->telefono} · {$b->titular}", ' ·');
            });

        DB::table('cuentas_bancarias as c')
            ->join('bancos as b', 'b.id_banco', '=', 'c.id_banco')
            ->where('c.id_empresa', $empresa)
            ->where('c.estado', '1')
            ->orderBy('b.nombre')
            ->get(['c.id_cuenta', 'b.nombre as banco', 'c.numero_cuenta', 'c.titular'])
            ->each(function ($c) use (&$opciones): void {
                $opciones["CUENTA|{$c->id_cuenta}"] = trim("Transferencia {$c->banco} · {$c->numero_cuenta} · {$c->titular}", ' ·');
            });

        return static::$opcionesMetodoPago = $opciones;
    }

    /** Etiqueta legible de un método de pago (incluye valores legados). */
    public static function etiquetaMetodoPago(?string $valor): string
    {
        if (blank($valor)) {
            return '—';
        }

        $legados = [
            'EFECTIVO' => 'Efectivo', 'YAPE' => 'Yape', 'PLIN' => 'Plin',
            'TRANSFERENCIA' => 'Transferencia', 'DEPOSITO' => 'Depósito',
        ];

        return static::opcionesMetodoPago()[$valor]
            ?? $legados[strtoupper($valor)]
            ?? $valor;
    }

    /** Etiqueta de un instrumento de caja (con detalle si hay id registrado). */
    public static function etiquetaInstrumento(?string $tipo, ?int $id): string
    {
        $base = match ($tipo) {
            'EFECTIVO'          => 'Efectivo',
            'TRANSFERENCIA'     => 'Transferencia',
            'BILLETERA_DIGITAL' => 'Billetera digital',
            default             => $tipo ?: '—',
        };

        if (! $id) {
            return $base;
        }

        $detalle = match ($tipo) {
            'BILLETERA_DIGITAL' => static::opcionesMetodoPago()["BILLETERA|{$id}"] ?? null,
            'TRANSFERENCIA'     => static::opcionesMetodoPago()["CUENTA|{$id}"] ?? null,
            default             => null,
        };

        return $detalle ?? $base;
    }

    /** [instrumento_tipo, instrumento_id] para el movimiento de caja. */
    public static function mapInstrumento(string $tipoPago): array
    {
        if (str_starts_with($tipoPago, 'BILLETERA|')) {
            return ['BILLETERA_DIGITAL', (int) substr($tipoPago, 10)];
        }
        if (str_starts_with($tipoPago, 'CUENTA|')) {
            return ['TRANSFERENCIA', (int) substr($tipoPago, 7)];
        }

        $tipo = match (strtoupper($tipoPago)) {
            'YAPE', 'PLIN', 'TUNKI', 'AGORA', 'BIM', 'OTRO' => 'BILLETERA_DIGITAL',
            'TRANSFERENCIA', 'DEPOSITO'                     => 'TRANSFERENCIA',
            default                                         => 'EFECTIVO',
        };

        return [$tipo, null];
    }

    public function mapInstrumentoTipo(string $tipoPago): string
    {
        return static::mapInstrumento($tipoPago)[0];
    }

    /**
     * Registrar un cobro como ingreso en la caja del usuario.
     * Busca la caja activa del usuario que cobró; si no tiene, omite.
     */
    public function registrarCobro(
        int     $idUsuario,
        float   $monto,
        string  $tipoPago,
        int     $idVenta,
        string  $documento,
        ?int    $idDiasVenta = null,
        string  $categoria = 'VENTA',
        ?string $referencia = null
    ): ?int
    {
        $caja = DB::table('cajas')
            ->where('id_empresa', (int) session('id_empresa'))
            ->where('id_usuario_responsable', $idUsuario)
            ->where('estado', 'ACTIVA')
            ->orderByRaw('CASE WHEN id_caja_padre IS NOT NULL THEN 0 ELSE 1 END')
            ->first();

        if (!$caja) return null;

        [$instrumentoTipo, $instrumentoId] = static::mapInstrumento($tipoPago);

        return $this->registrarMovimiento([
            'id_caja'          => $caja->id,
            'fecha'            => now()->toDateString(),
            'tipo'             => 'INGRESO',
            'categoria'        => $categoria,
            'descripcion'      => $categoria === 'VENTA'
                ? "Cobro venta {$documento}"
                : "Cobro {$documento}",
            'monto'            => $monto,
            'instrumento_tipo' => $instrumentoTipo,
            'instrumento_id'   => $instrumentoId,
            'referencia'       => $referencia,
            'origen_tipo'      => $idDiasVenta ? 'DiasVenta' : 'Venta',
            'origen_id'        => $idDiasVenta ?? $idVenta,
            'id_usuario'       => $idUsuario,
        ]);
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
