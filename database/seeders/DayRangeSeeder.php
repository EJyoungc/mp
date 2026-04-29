<?php

namespace Database\Seeders;

use App\Models\DayRange;
use Illuminate\Database\Seeder;

class DayRangeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $ranges = [
            ['name' => 'Morning', 'start_time' => '06:00:00', 'end_time' => '11:59:59'],
            ['name' => 'Afternoon', 'start_time' => '12:00:00', 'end_time' => '17:59:59'],
            ['name' => 'Evening', 'start_time' => '18:00:00', 'end_time' => '23:59:59'],
            ['name' => 'Night', 'start_time' => '00:00:00', 'end_time' => '05:59:59'],
        ];

        foreach ($ranges as $range) {
            DayRange::create($range);
        }
    }
}
