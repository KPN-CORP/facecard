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
        return $this->setConnection('mysql')->roles()->whereHas('permissions', function ($query) use ($permissionName) {
            $query->where('name', $permissionName);
        })->exists();
    }
    public function isManager(): bool
    {
        // Jika user tidak punya employee_id, dia bukan manajer.
        if (!$this->employee_id) {
            return false;
        }
        // Cek ke tabel employees, jika ada minimal 1 karyawan
        // yang manager_l1_id-nya adalah employee_id user ini, maka dia manajer.
        return Employees::where('manager_l1_id', $this->employee_id)->exists();
    }
}
