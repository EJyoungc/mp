<?php

namespace Database\Seeders;

use App\Models\Week;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class WeekSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        //first trimester
        for($i = 1; $i < 13; $i++){
            Week::create([
                'week' => $i,
                'trimester_id' => 1
            ]);
        }
        //
        //second trimester
        for($i = 13; $i < 27; $i++){
            Week::create([
                'week' => $i,
                'trimester_id' => 2
            ]);
        }

        //third trimester
        for($i = 27; $i < 41; $i++){
            Week::create([
                'week' => $i,
                'trimester_id' => 3,
            ]);
        }
    }
}
