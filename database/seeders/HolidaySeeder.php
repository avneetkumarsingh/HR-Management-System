<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Holiday;
use Carbon\Carbon;

class HolidaySeeder extends Seeder
{
    public function run(): void
    {
        $year = Carbon::now()->year;
        
        $holidays = [
            ['name' => 'Republic Day', 'date' => "$year-01-26", 'type' => 'national'],
            ['name' => 'Holi', 'date' => "$year-03-25", 'type' => 'regional'], // approximate
            ['name' => 'Good Friday', 'date' => "$year-03-29", 'type' => 'national'],
            ['name' => 'Eid al-Fitr', 'date' => "$year-04-10", 'type' => 'national'],
            ['name' => 'Independence Day', 'date' => "$year-08-15", 'type' => 'national'],
            ['name' => 'Gandhi Jayanti', 'date' => "$year-10-02", 'type' => 'national'],
            ['name' => 'Diwali', 'date' => "$year-11-01", 'type' => 'regional'],
            ['name' => 'Christmas', 'date' => "$year-12-25", 'type' => 'national'],
        ];

        foreach ($holidays as $h) {
            Holiday::create(array_merge($h, ['year' => $year]));
        }
    }
}
