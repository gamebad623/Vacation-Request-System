<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VacationBalance extends Model
{

    protected $fillable = [
        'year',
        'balance',
        'used',
        'remaining',
        'user_id',
        'vacation_type_id'
    ];

    public function user(): BelongsTo{
        return $this->belongsTo(User::class);
    }
    public function vacationType(): BelongsTo{
        return $this->belongsTo(VacationType::class);
    }
}
