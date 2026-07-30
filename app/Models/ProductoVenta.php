<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class ProductoVenta extends Model
{
    protected $table      = 'productos_ventas';
    protected $primaryKey = 'id';
    public    $timestamps = false;

    protected $fillable = [
        'id_venta','id_producto','descripcion',
        'cantidad','precio','total','igv_prod','descuento','costo',
    ];

    protected $casts = ['precio'=>'float','total'=>'float','cantidad'=>'float'];

    protected static function booted(): void
    {
        // Alerta en la campana si la línea se vendió por debajo del costo
        static::created(fn (ProductoVenta $pv) => \App\Services\AlertaStockService::evaluarVentaBajoCosto($pv));
    }

    public function venta()    { return $this->belongsTo(Venta::class,'id_venta','id_venta'); }
    public function producto() { return $this->belongsTo(Producto::class,'id_producto','id_producto'); }

    /**
     * Expresión SQL del total real de la línea. Algunas rutas de venta
     * guardan `total = 0` (ej. boletas del POS), por lo que se recalcula
     * precio × cantidad − descuento como respaldo. Usar SIEMPRE esta
     * expresión al sumar ventas por línea en reportes.
     */
    public static function sqlTotalLinea(string $alias = 'productos_ventas'): string
    {
        return "(CASE WHEN {$alias}.total > 0 THEN {$alias}.total ELSE ({$alias}.precio * {$alias}.cantidad) - COALESCE({$alias}.descuento, 0) END)";
    }
}
