<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OvertimeRequest extends Model
{
    protected $fillable = [
        'user_id',
        'date',
        'from_time',
        'to_time',
        'hours',
        'reason',
        'status',
        'approved_by',
        'approved_at',
        'rejection_reason',
    ];

    protected $casts = [
        'date' => 'date',
        'hours' => 'decimal:2',
        'approved_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
