<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Audit extends Model
{
    // La auditoría vive en la misma base MySQL del sistema.
    protected $table = 'audits';

    protected $fillable = [
        'user_id',
        'user_name',
        'user_rol',
        'empresa_id',
        'event',
        'model_type',
        'model_id',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent',
        'url',
        'method',
    ];

    protected $casts = [
        'old_values'  => 'array',
        'new_values'  => 'array',
        'created_at'  => 'datetime',
    ];
}
