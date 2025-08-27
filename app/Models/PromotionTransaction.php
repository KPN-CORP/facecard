<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class PromotionTransaction extends Model {
    protected $connection = 'kpncorp';

    protected $guarded = ['id'];
}