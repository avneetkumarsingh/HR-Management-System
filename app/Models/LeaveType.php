<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeaveType extends Model
{
    protected $fillable = [
        'name',
        'code',
        'color',
        'days_allowed',
        'is_paid',
        'carry_forward',
        'max_carry_forward',
        'requires_document',
        'min_days_notice',
        'applicable_for_probation',
        'is_active',
        'description',
    ];

    protected $casts = [
        'is_paid' => 'boolean',
        'carry_forward' => 'boolean',
        'requires_document' => 'boolean',
        'applicable_for_probation' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function leaveRequests()
    {
        return $this->hasMany(LeaveRequest::class);
    }

    public function leaveBalances()
    {
        return $this->hasMany(LeaveBalance::class);
    }
}
