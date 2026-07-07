<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Audit extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'audits';

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
        'old_values' => 'array',
        'new_values' => 'array',
        'created_at' => 'datetime',
    ];
}
