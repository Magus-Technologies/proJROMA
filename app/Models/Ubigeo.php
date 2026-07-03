<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ubigeo extends Model
{
    protected $table      = 'ubigeo_inei';
    protected $primaryKey = 'id_ubigeo';
    public    $timestamps = false;
    protected $guarded    = [];

    /** Departamentos: provincia y distrito en '00'. */
    public static function departamentos()
    {
        return static::where('provincia', '00')->where('distrito', '00')
            ->orderBy('nombre')->pluck('nombre', 'departamento');
    }

    /** Provincias de un departamento: distrito en '00'. */
    public static function provincias(?string $dep)
    {
        if (blank($dep)) return collect();

        return static::where('departamento', $dep)
            ->where('provincia', '<>', '00')->where('distrito', '00')
            ->orderBy('nombre')->pluck('nombre', 'provincia');
    }

    /** Distritos de una provincia, keyed por código ubigeo completo (6 dígitos). */
    public static function distritos(?string $dep, ?string $prov)
    {
        if (blank($dep) || blank($prov)) return collect();

        return static::where('departamento', $dep)->where('provincia', $prov)
            ->where('distrito', '<>', '00')
            ->orderBy('nombre')
            ->get()
            ->mapWithKeys(fn (self $u) => [$u->departamento . $u->provincia . $u->distrito => $u->nombre]);
    }

    /** Nombre del distrito a partir del código ubigeo de 6 dígitos. */
    public static function nombreDistrito(?string $codigo): ?string
    {
        if (blank($codigo) || strlen($codigo) !== 6) return null;

        return static::where('departamento', substr($codigo, 0, 2))
            ->where('provincia', substr($codigo, 2, 2))
            ->where('distrito', substr($codigo, 4, 2))
            ->value('nombre');
    }
}
