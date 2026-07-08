<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Banco extends Model
{
    protected $table = 'bancos';
    protected $primaryKey = 'id_banco';
    public $timestamps = true;
    protected $fillable = ['id_empresa', 'nombre', 'codigo_sunat', 'estado'];
}
