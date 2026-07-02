<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class DocumentoSunat extends Model
{
    protected $table      = 'documentos_sunat';
    protected $primaryKey = 'id_tido';
    public    $timestamps = false;
    protected $fillable   = ['nombre'];
}
