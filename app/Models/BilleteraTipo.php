<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class BilleteraTipo extends Model
{
    protected $table = 'billetera_tipos';
    public $timestamps = false;
    protected $fillable = ['id_empresa', 'nombre', 'estado'];
}
