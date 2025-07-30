<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class User extends Authenticatable
{
    use HasFactory, Notifiable;
    protected $guarded = ['id'];
    protected $hidden = ['password', 'remember_token'];

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class);
    }

    public function employee(): HasOne
    {
        return $this->hasOne(Employees::class,  'employee_id', 'employee_id');
    }

    public function hasPermissionTo(string $permissionName): bool
    {
        return $this->roles()->whereHas('permissions', function ($query) use ($permissionName) {
            $query->where('name', $permissionName);
        })->exists();
    }
}
