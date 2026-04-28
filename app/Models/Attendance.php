<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $fillable = [
        'user_id',
        'date',
        'check_in',
        'check_out',
        'working_hours',
        'status',
        'check_in_ip',
        'check_out_ip',
        'check_in_lat',
        'check_in_lng',
        'check_out_lat',
        'check_out_lng',
        'notes',
        'is_regularized',
        'shift_id',
    ];

    protected $casts = [
        'date' => 'date',
        'check_in' => 'datetime',
        'check_out' => 'datetime',
        'is_regularized' => 'boolean',
        'working_hours' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function shift()
    {
        return $this->belongsTo(Shift::class);
    }
}
