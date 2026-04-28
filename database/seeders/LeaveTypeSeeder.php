<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\LeaveType;

class LeaveTypeSeeder extends Seeder
{
    public function run(): void
    {
        LeaveType::create(['name' => 'Casual Leave', 'code' => 'CL', 'color' => '#3b82f6', 'days_allowed' => 12, 'is_paid' => true]);
        LeaveType::create(['name' => 'Sick Leave', 'code' => 'SL', 'color' => '#ef4444', 'days_allowed' => 12, 'is_paid' => true]);
        LeaveType::create(['name' => 'Earned Leave', 'code' => 'EL', 'color' => '#10b981', 'days_allowed' => 18, 'is_paid' => true, 'carry_forward' => true, 'max_carry_forward' => 30]);
        LeaveType::create(['name' => 'Maternity Leave', 'code' => 'ML', 'color' => '#d946ef', 'days_allowed' => 180, 'is_paid' => true]);
        LeaveType::create(['name' => 'Paternity Leave', 'code' => 'PL', 'color' => '#8b5cf6', 'days_allowed' => 5, 'is_paid' => true]);
        LeaveType::create(['name' => 'Loss of Pay', 'code' => 'LOP', 'color' => '#64748b', 'days_allowed' => 0, 'is_paid' => false]);
    }
}
