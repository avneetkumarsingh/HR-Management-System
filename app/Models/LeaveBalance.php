<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeaveBalance extends Model
{
    protected $fillable = [
        'user_id',
        'leave_type_id',
        'year',
        'allocated',
        'used',
        'pending',
        'carried_forward',
    ];

    protected $casts = [
        'allocated' => 'decimal:2',
        'used' => 'decimal:2',
        'pending' => 'decimal:2',
        'carried_forward' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function type()
    {
        return $this->belongsTo(LeaveType::class, 'leave_type_id');
    }
}
