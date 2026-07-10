<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Métodos de pago de la empresa, respetando la jerarquía real:
 *
 *   BANCO ──> CUENTA BANCARIA (el "tronco": número + CCI, recibe transferencias)
 *                ├── TARJETA de débito (herramienta de acceso a la cuenta)
 *                └── BILLETERA digital (Yape/Plin, ligada a la cuenta)
 *
 * Crea: 2 bancos (BCP, BBVA) · 3 cuentas para transferencias ·
 * billeteras Yape y Plin · 1 tarjeta de débito vinculada a su cuenta.
 *
 * Idempotente: verifica cada registro antes de crear.
 * Ejecutar:  php artisan db:seed --class=MetodosPagoSeeder --force
 */
class MetodosPagoSeeder extends Seeder
{
    /** Ajustar según la empresa donde se cargará. */
    private const EMPRESA = 12;

    public function run(): void
    {
        $ahora = now();

        // ── 1. Bancos ────────────────────────────────────────────────────
        $bancos = [];
        foreach ([['BCP', '02'], ['BBVA', '11']] as [$nombre, $codigoSunat]) {
            $id = DB::table('bancos')
                ->where('id_empresa', self::EMPRESA)
                ->where('nombre', $nombre)
                ->value('id_banco');

            if (! $id) {
                $id = DB::table('bancos')->insertGetId([
                    'id_empresa'   => self::EMPRESA,
                    'nombre'       => $nombre,
                    'codigo_sunat' => $codigoSunat,
                    'estado'       => '1',
                    'created_at'   => $ahora,
                    'updated_at'   => $ahora,
                ]);
                $this->command?->info("Banco {$nombre} creado.");
            }
            $bancos[$nombre] = $id;
        }

        // ── 2. Cuentas bancarias (troncos: reciben transferencias) ───────
        // tipo_cuenta es enum: CC (corriente), CA/AHORRO (ahorros), CTS
        $cuentasDef = [
            ['banco' => 'BCP',  'tipo_cuenta' => 'AHORRO', 'numero_cuenta' => '66541223151',          'cci' => '00219300665412231512', 'titular' => 'Víctor Raúl Canchari'],
            ['banco' => 'BCP',  'tipo_cuenta' => 'CC',     'numero_cuenta' => '193-2255887-0-11',     'cci' => '00219300225588701128', 'titular' => 'ROMA DISTRIBUCIONES & SERVICIOS GENERALES S.A.C.'],
            ['banco' => 'BBVA', 'tipo_cuenta' => 'CC',     'numero_cuenta' => '0011-0057-0200334455', 'cci' => '01105700020033445529', 'titular' => 'ROMA DISTRIBUCIONES & SERVICIOS GENERALES S.A.C.'],
        ];

        $cuentas = [];
        foreach ($cuentasDef as $c) {
            $id = DB::table('cuentas_bancarias')
                ->where('id_empresa', self::EMPRESA)
                ->where('numero_cuenta', $c['numero_cuenta'])
                ->value('id_cuenta');

            if (! $id) {
                $id = DB::table('cuentas_bancarias')->insertGetId([
                    'id_empresa'    => self::EMPRESA,
                    'id_banco'      => $bancos[$c['banco']],
                    'tipo_cuenta'   => $c['tipo_cuenta'],
                    'numero_cuenta' => $c['numero_cuenta'],
                    'cci'           => $c['cci'],
                    'moneda'        => 'PEN',
                    'titular'       => $c['titular'],
                    'estado'        => '1',
                    'created_at'    => $ahora,
                    'updated_at'    => $ahora,
                ]);
                $this->command?->info("Cuenta {$c['banco']} {$c['numero_cuenta']} creada.");
            }
            $cuentas[$c['numero_cuenta']] = $id;
        }

        // ── 3. Tipos de billetera (catálogo) ─────────────────────────────
        $tipos = [];
        foreach (['Yape', 'Plin'] as $nombre) {
            $tipos[$nombre] = DB::table('billetera_tipos')
                ->where('id_empresa', self::EMPRESA)
                ->where('nombre', $nombre)
                ->value('id')
                ?? DB::table('billetera_tipos')->insertGetId([
                    'id_empresa' => self::EMPRESA,
                    'nombre'     => $nombre,
                    'estado'     => 1,
                ]);
        }

        // ── 4. Billeteras digitales (ligadas a su cuenta bancaria) ───────
        $billeterasDef = [
            ['tipo' => 'Yape', 'telefono' => '987654321', 'titular' => 'victor raul canchari', 'cuenta' => '66541223151'],
            ['tipo' => 'Plin', 'telefono' => '92670321',  'titular' => 'victor raul canchari', 'cuenta' => '0011-0057-0200334455'],
        ];

        foreach ($billeterasDef as $b) {
            $existe = DB::table('billeteras_digitales')
                ->where('id_empresa', self::EMPRESA)
                ->where('id_billetera_tipo', $tipos[$b['tipo']])
                ->where('telefono', $b['telefono'])
                ->exists();

            if (! $existe) {
                DB::table('billeteras_digitales')->insert([
                    'id_empresa'         => self::EMPRESA,
                    'id_billetera_tipo'  => $tipos[$b['tipo']],
                    'id_cuenta_bancaria' => $cuentas[$b['cuenta']],
                    'telefono'           => $b['telefono'],
                    'titular'            => $b['titular'],
                    'estado'             => 1,
                    'created_at'         => $ahora,
                    'updated_at'         => $ahora,
                ]);
                $this->command?->info("Billetera {$b['tipo']} {$b['telefono']} creada.");
            }
        }

        // ── 5. Tarjeta de débito: herramienta de acceso a la cuenta BCP ──
        // (las transferencias entran a la CUENTA; la tarjeta no recibe dinero)
        $existeTarjeta = DB::table('tarjetas')
            ->where('id_empresa', self::EMPRESA)
            ->where('ultimos_4', '6321')
            ->exists();

        if (! $existeTarjeta) {
            DB::table('tarjetas')->insert([
                'id_empresa'         => self::EMPRESA,
                'id_banco'           => $bancos['BCP'],
                'id_cuenta_bancaria' => $cuentas['66541223151'],
                'tipo'               => 'DEBITO',
                'marca'              => 'VISA',
                'ultimos_4'          => '6321',
                'titular'            => 'victor',
                'fecha_vencimiento'  => '2028-06-30',
                'estado'             => '1',
                'created_at'         => $ahora,
                'updated_at'         => $ahora,
            ]);
            $this->command?->info('Tarjeta de débito VISA *6321 (vinculada a la cuenta BCP) creada.');
        }

        $this->command?->info('Métodos de pago listos: 2 bancos, 3 cuentas, Yape + Plin, 1 tarjeta de débito.');
    }
}
