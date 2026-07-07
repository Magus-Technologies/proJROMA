<?php

namespace App\Models\Traits;

use App\Models\Audit;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

trait Auditable
{
    protected static function bootAuditable(): void
    {
        static::created(function ($model) {
            $model->auditEvent('created');
        });

        static::updated(function ($model) {
            $model->auditEvent('updated');
        });

        static::deleted(function ($model) {
            $model->auditEvent('deleted');
        });
    }

    public function auditEvent(string $event): void
    {
        if (!app()->isProduction() && !config('app.debug')) {
            return;
        }

        $user = Auth::user();

        $data = [
            'user_id'    => $user?->usuario_id,
            'user_name'  => $user?->usuario,
            'user_rol'   => $user?->id_rol,
            'empresa_id' => session('id_empresa'),
            'event'      => $event,
            'model_type' => static::class,
            'model_id'   => $this->getKey(),
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
            'url'        => Request::fullUrl(),
            'method'     => Request::method(),
        ];

        if ($event === 'updated') {
            $data['old_values'] = $this->getOriginal();
            $data['new_values'] = $this->getDirty();
        } elseif ($event === 'created') {
            $data['new_values'] = $this->toArray();
        } elseif ($event === 'deleted') {
            $data['old_values'] = $this->toArray();
        }

        try {
            Audit::create($data);
        } catch (\Throwable) {
        }
    }
}
