<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

/**
 * La serie y el correlativo que usa una empresa para cada tipo de documento.
 *
 * El NOMBRE del documento no vive acá sino en documentos_sunat, que es el
 * catálogo común a todas las empresas. Esta tabla solo dice "para el tipo 1
 * uso la serie B001".
 */
class DocumentoEmpresa extends Model
{
    protected $table      = 'documentos_empresas';
    protected $primaryKey = 'id_tido';
    public    $timestamps = false;
    protected $fillable   = ['id_empresa','serie','numero','sucursal'];

    /** Catálogo de tipos de documento, cacheado: son una docena de filas fijas. */
    private static ?array $nombres = null;

    /**
     * Nombre del tipo de documento ("BOLETA DE VENTA", "FACTURA", ...).
     *
     * Se venía leyendo como columna de esta tabla, que no la tiene: por eso
     * salía vacío en todos lados y los comprobantes caían siempre al texto
     * de respaldo "NOTA DE VENTA", aunque fueran boletas.
     */
    public function getTipoDocAttribute(): ?string
    {
        self::$nombres ??= DocumentoSunat::pluck('nombre', 'id_tido')->all();

        return self::$nombres[$this->id_tido] ?? null;
    }
}
