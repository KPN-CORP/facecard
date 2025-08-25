<?php

namespace App\Rules;

use App\Models\DevelopmentModel;
use Illuminate\Contracts\Validation\Rule;

class SumPercentageCheck implements Rule
{
    protected $ignoreId;

    public function __construct($ignoreId = null)
    {
        $this->ignoreId = $ignoreId;
    }

    public function passes($attribute, $value)
    {
        $query = DevelopmentModel::query();
        if ($this->ignoreId) {
            $query->where('id', '!=', $this->ignoreId);
        }

        $currentSum = $query->sum('percentage');

        return ($currentSum + $value) <= 100;
    }

    public function message()
    {
        return 'The total percentage of all development models cannot exceed 100%.';
    }
}