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
    protected $fillable = ['nombre', 'guard_name'];

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(
            config('permission.models.permission'),
            config('permission.table_names.role_has_permissions'),
            'role_id',
            'permission_id'
        );
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(
            config('auth.providers.users.model'),
            config('permission.table_names.model_has_roles'),
            'role_id',
            config('permission.column_names.model_morph_key')
        );
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
        return static::firstOrCreate(['nombre' => $name, 'guard_name' => $guardName ?? 'web']);
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
