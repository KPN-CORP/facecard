<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DevelopmentPlanMaster extends Model
{
    use HasFactory;

    protected $table = 'development_plan_master';
    protected $guarded = ['id'];
}