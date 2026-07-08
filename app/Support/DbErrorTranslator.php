<?php

namespace App\Support;

use Illuminate\Database\QueryException;

/**
 * Traduce errores crudos de MySQL a mensajes legibles en español.
 * Convierte cosas como "Field 'x' doesn't have a default value" en
 * "Falta completar un dato obligatorio (x)".
 */
class DbErrorTranslator
{
    public static function translate(QueryException $e): string
    {
        $msg = $e->getMessage();

        // 1364 — Field 'x' doesn't have a default value
        if (preg_match("/Field '([^']+)' doesn't have a default value/i", $msg, $m)) {
            return "Falta completar un dato obligatorio: «{$m[1]}».";
        }

        // 1048 — Column 'x' cannot be null
        if (preg_match("/Column '([^']+)' cannot be null/i", $msg, $m)) {
            return "El campo «{$m[1]}» es obligatorio y no puede quedar vacío.";
        }

        // 1062 — Duplicate entry 'y' for key 'z'
        if (preg_match("/Duplicate entry '([^']+)'/i", $msg, $m)) {
            return "Ya existe un registro con ese valor: «{$m[1]}».";
        }

        // 1406 — Data too long for column 'x'
        if (preg_match("/Data too long for column '([^']+)'/i", $msg, $m)) {
            return "El valor de «{$m[1]}» es demasiado largo.";
        }

        // 1451/1452 — foreign key constraint fails
        if (stripos($msg, 'foreign key constraint fails') !== false) {
            return 'La operación afecta a un registro relacionado. Verificá los datos vinculados.';
        }

        // 1054 — Unknown column 'x'
        if (preg_match("/Unknown column '([^']+)'/i", $msg, $m)) {
            return "Error de configuración: la columna «{$m[1]}» no existe en la base de datos.";
        }

        // 2002 / connection
        if (stripos($msg, 'could not find driver') !== false
            || stripos($msg, 'Connection refused') !== false) {
            return 'No se pudo conectar con la base de datos. Intentá de nuevo en unos segundos.';
        }

        return 'Ocurrió un error al guardar en la base de datos.';
    }
}
