<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Attendance;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class AttendanceSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();
        $endDate = Carbon::today();
        $startDate = $endDate->copy()->subMonths(3);

        $period = CarbonPeriod::create($startDate, $endDate);

        $statuses = ['present', 'present', 'present', 'present', 'present', 'late', 'half_day', 'absent'];

        foreach ($users as $user) {
            foreach ($period as $date) {
                if ($date->isWeekend()) {
                    Attendance::create([
                        'user_id' => $user->id,
                        'date' => $date->toDateString(),
                        'status' => 'weekend',
                        'shift_id' => $user->shift_id
                    ]);
                    continue;
                }

                $status = $statuses[array_rand($statuses)];
                $checkIn = null;
                $checkOut = null;
                $hours = 0;

                if (in_array($status, ['present', 'late', 'half_day'])) {
                    $baseCheckIn = $date->copy()->setHour(9)->setMinute(rand(0, 59));
                    if ($status == 'late') {
                        $baseCheckIn->addHours(1);
                    }
                    
                    $checkIn = $baseCheckIn;
                    
                    $outHour = $status == 'half_day' ? rand(13, 14) : rand(18, 19);
                    $checkOut = $date->copy()->setHour($outHour)->setMinute(rand(0, 59));
                    
                    $hours = $checkIn->floatDiffInHours($checkOut);
                }

                Attendance::create([
                    'user_id' => $user->id,
                    'date' => $date->toDateString(),
                    'check_in' => $checkIn,
                    'check_out' => $checkOut,
                    'working_hours' => $hours,
                    'status' => $status,
                    'shift_id' => $user->shift_id
                ]);
            }
        }
    }
}
