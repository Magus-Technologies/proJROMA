<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Contracts\Permission as PermissionContract;
use Spatie\Permission\Contracts\Role as RoleContract;
use Spatie\Permission\Traits\RefreshesPermissionCache;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Rol extends Model implements RoleContract
{
    use RefreshesPermissionCache;

    protected $table = 'roles';
    protected $primaryKey = 'rol_id';
    public $timestamps = false;
    // La tabla legacy solo tiene rol_id y nombre (no existe guard_name)
    // y rol_id no es auto-incremental: se asigna a mano al crear.
    public $incrementing = false;
    protected $fillable = ['nombre'];

    protected static function booted(): void
    {
        static::creating(function (self $rol) {
            $rol->rol_id ??= (int) static::max('rol_id') + 1;
        });
    }

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(
            config('permission.models.permission'),
            config('permission.table_names.role_has_permissions'),
            'role_id',
            'permission_id'
        );
    }

    public function users(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        // Los usuarios se vinculan por la columna legacy usuarios.id_rol,
        // no por la tabla pivote de Spatie (model_has_roles, vacía).
        return $this->hasMany(User::class, 'id_rol', 'rol_id');
    }

    public static function findByName(string $name, $guardName = null): RoleContract
    {
        return static::where('nombre', $name)->firstOrFail();
    }

    public static function findById(int|string $id, $guardName = null): RoleContract
    {
        return static::where('rol_id', $id)->firstOrFail();
    }

    public static function findOrCreate(string $name, $guardName = null): RoleContract
    {
        return static::firstOrCreate(['nombre' => $name]);
    }

    /**
     * Deja el rol exactamente con los permisos indicados (nombres, ids o
     * modelos). Una lista vacía le quita todos los permisos.
     */
    public function syncPermissions(...$permissions): static
    {
        $permissionClass = config('permission.models.permission');

        $ids = collect($permissions)
            ->flatten()
            ->filter()
            ->map(function ($permission) use ($permissionClass) {
                if ($permission instanceof PermissionContract) {
                    return $permission->id;
                }
                if (is_numeric($permission)) {
                    return (int) $permission;
                }

                return $permissionClass::where('name', $permission)
                    ->where('guard_name', $this->guard_name ?? 'web')
                    ->value('id');
            })
            ->filter()
            ->unique()
            ->values()
            ->all();

        $this->permissions()->sync($ids);
        $this->unsetRelation('permissions');

        app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();

        return $this;
    }

    public function hasPermissionTo($permission, $guardName = null): bool
    {
        if (is_string($permission)) {
            $permission = app(PermissionContract::class)
                ->where('name', $permission)
                ->where('guard_name', $guardName ?? 'web')
                ->first();
            if (! $permission) return false;
        }

        return $this->permissions->contains('id', $permission->id);
    }
}
