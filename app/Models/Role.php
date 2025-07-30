<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class Role extends Model {
    use HasFactory;
    protected $fillable = ['name', 'business_unit', 'company', 'location'];
    
    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'business_unit' => 'array',
        'company'       => 'array',
        'location'      => 'array',
    ];

    public function permissions() {
        return $this->belongsToMany(Permission::class);
    }
    public function users() {
        return $this->belongsToMany(User::class);
    }
}