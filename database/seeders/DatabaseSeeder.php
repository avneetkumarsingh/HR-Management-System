<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            ShiftSeeder::class,
            LeaveTypeSeeder::class,
            HolidaySeeder::class,
            UserSeeder::class,
            // AttendanceSeeder and LeaveBalanceSeeder removed to ensure a completely fresh instance without dummy data
        ]);
    }
}
