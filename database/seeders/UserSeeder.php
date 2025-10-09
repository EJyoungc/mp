<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //


        User::create([
            'name' => 'Admin System',
            'email' => 'cyhperk@gmail.com',
            'role_id' => 1,
            'password' => bcrypt('root')
        ]);




        // User::create([
        //     'name' => 'Admin',
        //     'email' => 'admin@admin.com',
        //     'role_id' => 2,
        //     'password' => bcrypt('root')
        // ]);
        // User::create([
        //     'name' => 'Dr',
        //     'email' => 'dr@dr.com',
        //     'role_id' => 3,
        //     'password' => bcrypt('root')
        // ]);


        // User::create([
        //     'name' => 'Mom',
        //     'email' => 'mom@mom.com',
        //     'role_id' => 4,
        //     'password' => bcrypt('root')
        // ]);


        // User::create([
        //     'name' => 'Practitional',
        //     'email' => 'prat@prat.com',
        //     'role_id' => 5,
        //     'password' => bcrypt('root')
        // ]);
    }
}
