<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes, HasRoles;

    protected $fillable = [
        'name',
        'email',
        'password',
        'employee_id',
        'role',
        'phone',
        'avatar',
        'date_of_joining',
        'date_of_birth',
        'gender',
        'department_id',
        'designation_id',
        'shift_id',
        'location',
        'manager_id',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'date_of_joining' => 'date',
        'date_of_birth' => 'date',
        'is_active' => 'boolean',
        'password' => 'hashed',
    ];

    // Relationships
    public function profile()
    {
        return $this->hasOne(EmployeeProfile::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function designation()
    {
        return $this->belongsTo(Designation::class);
    }

    public function shift()
    {
        return $this->belongsTo(Shift::class);
    }

    public function manager()
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    public function team()
    {
        return $this->hasMany(User::class, 'manager_id');
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function leaveRequests()
    {
        return $this->hasMany(LeaveRequest::class);
    }

    public function leaveBalances()
    {
        return $this->hasMany(LeaveBalance::class);
    }

    // Accessors
    public function getAvatarUrlAttribute()
    {
        if ($this->avatar) {
            return asset('storage/' . $this->avatar);
        }

        $name = trim($this->name);
        $initials = '';
        $words = explode(' ', $name);
        if (count($words) >= 2) {
            $initials = mb_strtoupper(mb_substr($words[0], 0, 1) . mb_substr($words[1], 0, 1));
        } else {
            $initials = mb_strtoupper(mb_substr($name, 0, 2));
        }

        $bgColor = '#ccfbf1';
        $textColor = '#0d9488';

        $svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100">
            <rect width="100" height="100" fill="' . $bgColor . '"/>
            <text x="50" y="50" font-family="Arial, sans-serif" font-size="40" font-weight="bold" fill="' . $textColor . '" text-anchor="middle" dominant-baseline="central">' . htmlspecialchars($initials) . '</text>
        </svg>';

        return 'data:image/svg+xml;base64,' . base64_encode($svg);
    }
}
