<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employees extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $connection = 'kpncorp';
    protected $table = 'employees';
    protected $guarded = ['id'];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'language_ability' => 'array', 
    ];

    public function scopePublicData($query)
    {
        return $query->select('id', 'employee_id', 'fullname', 'gender', 'email', 'group_company', 'designation_code','designation_name', 'job_level', 'company_name', 'contribution_level_code', 'work_area_code', 'office_area', 'manager_l1_id', 'manager_l2_id', 'employee_type', 'unit', 'personal_email', 'date_of_birth', 'nationality', 'marital_status', 'homebase', 'permanent_city');
    }

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