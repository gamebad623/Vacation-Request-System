<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Approval extends Model
{
    protected $fillable = [
        'vacation_request_id',
        'approved_by',
        'role',
        'status',
        'note',

    ];

    public function vacationRequest(){
        return $this->belongsTo(VacationRequest::class);
    }

    public function approver(){
        return $this->belongsTo(User::class , 'approved_by');
    }
}
