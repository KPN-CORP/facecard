<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Model;


class Employees extends Model
{
    use HasFactory;
    protected $table = 'employees';
    protected $guarded = ['id'];

    public function user()
    {
        return $this->hasOne(User::class, 'employee_id', 'employee_id');
    }
    
    public function hasPermissionTo(string $permissionName): bool
    {
        if (!$this->user) {
            return false;
        }
        return $this->user->roles()->whereHas('permissions', function ($query) use ($permissionName) {
            $query->where('name', $permissionName);
        })->exists();
    }
     public function performanceAppraisals()
    {
        return $this->hasMany(PerformanceAppraisal::class, 'employee_id', 'employee_id');
    }
    public function developmentPlans()
{
    return $this->hasMany(IndividualDevelopmentPlan::class, 'employee_id', 'employee_id');
}
    
    public function resultSummary() { return $this->hasOne(ResultSummary::class, 'employee_id', 'employee_id'); }
}