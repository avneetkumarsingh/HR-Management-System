<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Shift;

class ShiftSeeder extends Seeder
{
    public function run(): void
    {
        Shift::create([
            'name' => 'General Shift',
            'code' => 'GEN',
            'start_time' => '09:00:00',
            'end_time' => '18:00:00',
            'working_hours' => 9.0,
            'is_night_shift' => false
        ]);

        Shift::create([
            'name' => 'Morning Shift',
            'code' => 'MORN',
            'start_time' => '07:00:00',
            'end_time' => '16:00:00',
            'working_hours' => 9.0,
            'is_night_shift' => false
        ]);

        Shift::create([
            'name' => 'Night Shift',
            'code' => 'NIGHT',
            'start_time' => '22:00:00',
            'end_time' => '07:00:00',
            'working_hours' => 9.0,
            'is_night_shift' => true
        ]);
    }
}
