<?php

namespace Database\Seeders;

use App\Models\History;
use Illuminate\Database\Seeder;

class HistorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        History::updateOrCreate(
            ['mother_id' => 7], // Mary Mother
            [
                'infant_number' => 1,
                'last_menstrual_cycle' => now()->subWeeks(10)->format('Y-m-d'),
                'organization_id' => 1, // Clinic
            ]
        );
    }
}
