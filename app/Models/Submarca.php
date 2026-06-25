<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Submarca extends Model
{
    protected $table      = 'submarcas';
    protected $primaryKey = 'id_submarca';
    public    $timestamps = false;
    protected $fillable   = ['nombre', 'descripcion', 'id_marca', 'id_empresa', 'estado'];
}
