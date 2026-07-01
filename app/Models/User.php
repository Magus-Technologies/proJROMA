<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasAvatar;
use Filament\Panel;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

// ── Laravel 13: atributos PHP en lugar de propiedades de clase ────────
#[Table('usuarios')]
class User extends Authenticatable implements FilamentUser, HasAvatar
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    // En Laravel 13 podemos usar #[Table] arriba, pero mantenemos estas
    // propiedades para compatibilidad con paquetes externos
    protected $table      = 'usuarios';
    protected $primaryKey = 'usuario_id';
    public    $timestamps = false;

    protected $fillable = [
        'id_empresa', 'id_rol', 'num_doc', 'usuario', 'clave',
        'email', 'nombres', 'apellidos', 'rubro', 'sucursal',
        'telefono', 'foto', 'estado', 'mensaje', 'rotativo', 'available_status',
    ];

    protected $hidden = [
        'clave', 'token_reset', 'remember_token',
    ];

    protected $casts = [
        'estado'           => 'string',
        'available_status' => 'boolean',
        'rotativo'         => 'boolean',
    ];

    // ── Auth override (campo "clave" en lugar de "password") ─────────────
    public function getAuthPassword(): string
    {
        return $this->clave;
    }

    public function getAuthPasswordName(): string
    {
        return 'clave';
    }

    // ── Relaciones ────────────────────────────────────────────────────────
    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'id_empresa', 'id_empresa');
    }

    public function rol()
    {
        return $this->belongsTo(Rol::class, 'id_rol', 'rol_id');
    }

    public function rutas()
    {
        return $this->hasMany(RutaVendedor::class, 'id_usuario', 'usuario_id');
    }

    // ── Scopes ────────────────────────────────────────────────────────────
    public function scopeActivos($query)
    {
        return $query->where('estado', '1')->where('available_status', 1);
    }

    public function scopeDeEmpresa($query, int $idEmpresa)
    {
        return $query->where('id_empresa', $idEmpresa);
    }

    public function scopeVendedores($query)
    {
        return $query->where('id_rol', 3);
    }

    // ── Accessors / helpers ───────────────────────────────────────────────
    public function getNombreCompletoAttribute(): string
    {
        return trim("{$this->nombres} {$this->apellidos}");
    }

    public function esAdmin(): bool    { return $this->id_rol == 1; }
    public function esVendedor(): bool { return $this->id_rol == 3; }
    public function esCajero(): bool   { return $this->id_rol == 4; }

    // ── Filament ──────────────────────────────────────────────────────
    public function getFilamentAvatarUrl(): ?string
    {
        if ($this->foto && Storage::disk('public')->exists($this->foto)) {
            return Storage::disk('public')->url($this->foto);
        }
        return null;
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->estado === '1' && $this->available_status;
    }

    public function getFilamentName(): string
    {
        return $this->nombre_completo ?: $this->usuario ?: 'Usuario';
    }

    public function getNameAttribute(): string
    {
        return $this->getFilamentName();
    }
}
