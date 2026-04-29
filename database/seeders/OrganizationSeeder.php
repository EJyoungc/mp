<?php

namespace Database\Seeders;

use App\Models\Organization;
use Illuminate\Database\Seeder;

class OrganizationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $district = \App\Models\District::first();
        $area = \App\Models\Area::where('district_id', $district->id)->first();

        Organization::create([
            'name' => 'General Health Clinic',
            'email' => 'clinic@example.com',
            'phone' => '0123456789',
            'address' => '123 Health St',
            'is_pharmacy' => false,
            'district_id' => $district->id,
            'area_id' => $area->id,
        ]);

        Organization::create([
            'name' => 'City Central Pharmacy',
            'email' => 'pharmacy@example.com',
            'phone' => '0987654321',
            'address' => '456 Medicine Ave',
            'is_pharmacy' => true,
            'district_id' => $district->id,
            'area_id' => $area->id,
        ]);
    }
}
