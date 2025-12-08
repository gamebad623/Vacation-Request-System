<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VacationType extends Model
{
    
    protected $fillable = [
        'name',
        'is_paid',
        'max_days_per_year'
    ];

    public function  vacationRequest() : HasMany {
        return $this->hasMany(VacationRequest::class);
    }
}
