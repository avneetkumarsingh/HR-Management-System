<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeProfile extends Model
{
    protected $fillable = [
        'user_id',
        'blood_group',
        'address',
        'city',
        'state',
        'country',
        'pincode',
        'emergency_contact_name',
        'emergency_contact_phone',
        'emergency_contact_relation',
        'pan_number',
        'aadhar_number',
        'bank_account_number',
        'bank_name',
        'ifsc_code',
        'employment_type',
        'probation_end_date',
        'confirmation_date',
        'notice_period',
    ];

    protected $casts = [
        'probation_end_date' => 'date',
        'confirmation_date' => 'date',
        'notice_period' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
