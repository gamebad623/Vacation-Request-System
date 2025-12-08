<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VacationRequest extends Model
{
    public $timestamps = true;
    protected $casts = [
        'start_date' => 'datetime',
        'end_date'   => 'datetime',
    ];
    protected $fillable = [
        'start_date',
        'end_date',
        'total_days',
        'reason',
        'status',
        'user_id',
        'vacation_type_id',
        'manager_id',
        'hr_id'
    ];

    public function user():BelongsTo{
        return $this->belongsTo(User::class);
    }
    public function vacationType():BelongsTo{
        return $this->belongsTo(VacationType::class);
    }
    public function approval(){
        return $this->hasMany(Approval::class);
    }
    public function manager():BelongsTo{
        return $this->belongsTo(User::class , 'manager_id');
    }
    public function hr():BelongsTo{
        return $this->belongsTo(User::class , 'hr_id');
    }
}
