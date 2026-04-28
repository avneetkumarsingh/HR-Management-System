<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Shift extends Model
{
    protected $fillable = [
        'name',
        'code',
        'start_time',
        'end_time',
        'grace_time',
        'working_hours',
        'is_night_shift',
        'working_days',
        'is_active',
    ];

    protected $casts = [
        'is_night_shift' => 'boolean',
        'is_active' => 'boolean',
        'working_days' => 'array',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
