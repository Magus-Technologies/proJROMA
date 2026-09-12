<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * La compra pasa a reflejar la factura del proveedor tal cual viene:
 * descuento por línea, desglose de valor de venta e IGV, y los regímenes
 * de retención y percepción con su total a pagar referencial.
 *
 * Antes solo se guardaba el total, así que el documento impreso no podía
 * cuadrar con el del proveedor.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('productos_compras', function ($table): void {
            if (! Schema::hasColumn('productos_compras', 'unidad')) {
                $table->string('unidad', 20)->nullable()->after('cantidad');
            }
            if (! Schema::hasColumn('productos_compras', 'descuento')) {
                $table->decimal('descuento', 12, 2)->default(0)->after('costo');
            }
        });

        Schema::table('compras', function ($table): void {
            foreach ([
                'subtotal'        => 'total',
                'descuento_total' => 'subtotal',
                'igv'             => 'descuento_total',
            ] as $columna => $despues) {
                if (! Schema::hasColumn('compras', $columna)) {
                    $table->decimal($columna, 12, 2)->default(0)->after($despues);
                }
            }

            if (! Schema::hasColumn('compras', 'igv_porcentaje')) {
                $table->decimal('igv_porcentaje', 5, 2)->default(18)->after('igv');
            }

            // Régimen de retenciones (RS 037-2002/SUNAT).
            if (! Schema::hasColumn('compras', 'sujeto_retencion')) {
                $table->boolean('sujeto_retencion')->default(false)->after('igv_porcentaje');
                $table->decimal('retencion_porcentaje', 5, 2)->default(3)->after('sujeto_retencion');
                $table->decimal('retencion_monto', 12, 2)->default(0)->after('retencion_porcentaje');
            }

            // Régimen de percepciones del IGV.
            if (! Schema::hasColumn('compras', 'sujeto_percepcion')) {
                $table->boolean('sujeto_percepcion')->default(false)->after('retencion_monto');
                $table->decimal('percepcion_porcentaje', 5, 2)->default(2)->after('sujeto_percepcion');
                $table->decimal('percepcion_monto', 12, 2)->default(0)->after('percepcion_porcentaje');
                $table->decimal('total_referencial', 12, 2)->default(0)->after('percepcion_monto');
            }
        });

        // Las compras que ya existen solo tienen el total: se reparte con el
        // IGV de 18% para que el desglose no salga en cero.
        DB::statement('
            UPDATE compras
            SET subtotal = ROUND(total / 1.18, 2),
                igv      = ROUND(total - (total / 1.18), 2),
                total_referencial = total
            WHERE subtotal = 0 AND total > 0
        ');
    }

    public function down(): void
    {
        Schema::table('productos_compras', function ($table): void {
            $table->dropColumn(['unidad', 'descuento']);
        });

        Schema::table('compras', function ($table): void {
            $table->dropColumn([
                'subtotal', 'descuento_total', 'igv', 'igv_porcentaje',
                'sujeto_retencion', 'retencion_porcentaje', 'retencion_monto',
                'sujeto_percepcion', 'percepcion_porcentaje', 'percepcion_monto', 'total_referencial',
            ]);
        });
    }
};
