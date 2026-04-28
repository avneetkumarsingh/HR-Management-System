<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\LeaveType;
use App\Models\LeaveBalance;
use Carbon\Carbon;

class LeaveBalanceSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();
        $leaveTypes = LeaveType::all();
        $year = Carbon::now()->year;

        foreach ($users as $user) {
            foreach ($leaveTypes as $type) {
                $used = rand(0, min(5, $type->days_allowed));
                LeaveBalance::create([
                    'user_id' => $user->id,
                    'leave_type_id' => $type->id,
                    'year' => $year,
                    'allocated' => $type->days_allowed,
                    'used' => $used,
                    'pending' => $type->days_allowed - $used,
                    'carried_forward' => 0,
                ]);
            }
        }
    }
}
