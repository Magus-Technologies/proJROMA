<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class DocumentoEmpresa extends Model
{
    protected $table      = 'documentos_empresas';
    protected $primaryKey = 'id_tido';
    public    $timestamps = false;
    protected $fillable   = ['id_empresa','tipo_doc','serie','numero','sucursal'];
}
