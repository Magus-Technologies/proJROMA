<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Subcategoria extends Model
{
    protected $table      = 'subcategorias';
    protected $primaryKey = 'id_subcategoria';
    public    $timestamps = false;
    protected $fillable   = ['nombre', 'descripcion', 'id_categoria', 'id_empresa', 'estado'];
}
