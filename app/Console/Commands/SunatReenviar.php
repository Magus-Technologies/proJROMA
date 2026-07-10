<?php

namespace App\Console\Commands;

use App\Models\Venta;
use App\Services\VentaSunatService;
use Illuminate\Console\Command;

/**
 * Regenera el XML y reenvía a SUNAT las boletas/facturas no aceptadas.
 *
 *   php artisan sunat:reenviar --empresa=12            # todas las pendientes/rechazadas
 *   php artisan sunat:reenviar --empresa=12 --dry      # solo listar, sin enviar
 *   php artisan sunat:reenviar --empresa=12 --desde=2026-07-09 --limit=20
 */
class SunatReenviar extends Command
{
    protected $signature = 'sunat:reenviar
        {--empresa= : id_empresa (obligatorio)}
        {--desde= : solo ventas emitidas desde esta fecha (Y-m-d)}
        {--limit=50 : máximo de comprobantes a procesar}
        {--dry : listar lo que se enviaría, sin enviar}';

    protected $description = 'Regenera el XML y reenvía a SUNAT las ventas pendientes o rechazadas (boletas y facturas)';

    public function handle(VentaSunatService $svc): int
    {
        $empresa = (int) $this->option('empresa');
        if (! $empresa) {
            $this->error('Indica la empresa: --empresa=12');

            return self::FAILURE;
        }

        $ventas = Venta::with('productosVenta')
            ->where('id_empresa', $empresa)
            ->whereIn('id_tido', [1, 2])          // solo boleta y factura
            ->where('estado', '<>', '0')          // no anuladas
            ->where(fn ($q) => $q
                ->whereNull('sunat_estado')
                ->orWhere('sunat_estado', '<>', 'aceptado'))
            ->when($this->option('desde'), fn ($q, $d) => $q->whereDate('fecha_emision', '>=', $d))
            ->orderBy('id_venta')
            ->limit((int) $this->option('limit'))
            ->get();

        if ($ventas->isEmpty()) {
            $this->warn('No hay comprobantes pendientes ni rechazados para reenviar.');

            return self::SUCCESS;
        }

        $this->info($ventas->count() . ' comprobante(s) a procesar.');

        if ($this->option('dry')) {
            foreach ($ventas as $v) {
                $this->line("  {$v->serie}-{$v->numero} · S/ " . number_format((float) $v->total, 2)
                    . ' · estado SUNAT: ' . ($v->sunat_estado ?: 'sin enviar'));
            }
            $this->warn('Modo --dry: no se envió nada.');

            return self::SUCCESS;
        }

        $ok = 0;
        $fallo = 0;

        foreach ($ventas as $venta) {
            // Regenerar SIEMPRE el XML: el guardado puede tener datos
            // inválidos (ej. unidad fuera del catálogo 03).
            $gen = $svc->generarXml($venta);
            if (! ($gen['ok'] ?? false)) {
                $fallo++;
                $this->warn("✗ {$venta->serie}-{$venta->numero} (generar): " . ($gen['msg'] ?? 'error'));

                continue;
            }

            $res = $svc->enviar($venta->refresh());
            if ($res['ok'] ?? false) {
                $ok++;
                $this->info("✓ {$venta->serie}-{$venta->numero}: " . ($res['msg'] ?? 'aceptado'));
            } else {
                $fallo++;
                $this->warn("✗ {$venta->serie}-{$venta->numero}: " . ($res['msg'] ?? 'error'));
            }
        }

        $this->newLine();
        $this->info("Resultado: {$ok} aceptadas · {$fallo} con error.");

        return $fallo === 0 ? self::SUCCESS : self::FAILURE;
    }
}
