<?php

namespace Database\Seeders;

use App\Models\Area;
use App\Models\District;
use Illuminate\Database\Seeder;

class LocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $locations = [
            'Blantyre' => ['Limbe', 'Chichiri', 'Soche', 'Ndirande', 'Mbayani', 'Bangwe', 'Chilomoni'],
            'Lilongwe' => ['Area 1', 'Area 2', 'Area 18', 'Area 25', 'Area 47', 'Area 49', 'Kawale', 'Kanengo'],
            'Mzuzu' => ['Luwinga', 'Chasefu', 'Kaning\'ina', 'Viphya', 'Zolozolo'],
            'Zomba' => ['Chikanda', 'Mpondabwino', 'Sadzi', 'Matawale'],
            'Mangochi' => ['Town', 'Monkey Bay', 'Namwera', 'Nansenga'],
            'Dedza' => ['Boma', 'Linthipe', 'Mayani', 'Kaphuka'],
            'Nkhotakota' => ['Boma', 'Dwangwa', 'Benga', 'Mkaika'],
            'Mchinji' => ['Boma', 'Mkanda', 'Guillime', 'Waliranji'],
            'Ntcheu' => ['Boma', 'Lizulu', 'Tsangano', 'Kasinje'],
            'Salima' => ['Boma', 'Senga Bay', 'Khombedza', 'Lifidzi'],
            'Mulanje' => ['Boma', 'Chitakale', 'Limbuli', 'Thuchila'],
            'Chiradzulu' => ['Boma', 'Thumbwe', 'Namitambo', 'Ndunde'],
            'Chikwawa' => ['Boma', 'Nchalo', 'Ngabu', 'East Bank'],
            'Nsanje' => ['Boma', 'Bangula', 'Marka', 'Tengani'],
            'Karonga' => ['Boma', 'Songwe', 'Chilumba', 'Nyungwe'],
            'Rumphi' => ['Boma', 'Bolero', 'Livingstonia', 'Mlowe'],
            'Chitipa' => ['Boma', 'Misuku', 'Kameme', 'Nthalane'],
            'Kasungu' => ['Boma', 'Nkhamenya', 'Chulu', 'Santhe'],
            'Machinga' => ['Liwonde', 'Boma', 'Nayuchi', 'Ntaja'],
            'Balaka' => ['Boma', 'Phalula', 'Bazale', 'Utale'],
            'Thyolo' => ['Boma', 'Luchenza', 'Bvumbwe', 'Goliati'],
            'Phalombe' => ['Boma', 'Chiringa', 'Muloza'],
            'Neno' => ['Boma', 'Mwanza Border', 'Ligowe'],
            'Dowa' => ['Boma', 'Mponela', 'Dzaleka', 'Madisi'],
            'Ntchisi' => ['Boma', 'Malomo', 'Kansonga'],
            'Likoma' => ['Boma', 'Chizumulu'],
        ];

        foreach ($locations as $districtName => $areas) {
            $district = District::firstOrCreate(['name' => $districtName]);

            foreach ($areas as $areaName) {
                Area::firstOrCreate([
                    'district_id' => $district->id,
                    'name' => $areaName,
                ]);
            }
        }
    }
}
