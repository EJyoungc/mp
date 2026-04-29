<?php

namespace Database\Seeders;

use App\Models\Organization;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $systemAdminRole = Role::where('name', 'system-admin')->first();
        $adminRole = Role::where('name', 'admin')->first();
        $doctorRole = Role::where('name', 'doctor')->first();
        $motherRole = Role::where('name', 'mother')->first();
        $practitionerRole = Role::where('name', 'practitioner')->first();

        $clinic = Organization::where('is_pharmacy', false)->first();
        $pharmacy = Organization::where('is_pharmacy', true)->first();

        // 1. System Admin
        User::create([
            'name' => 'System Administrator',
            'email' => 'system@admin.com',
            'role_id' => $systemAdminRole->id,
            'password' => Hash::make('root'),
            'organization_verify' => 'verified',
        ]);

        // 2. Clinic Admin
        User::create([
            'name' => 'Clinic Administrator',
            'email' => 'admin@clinic.com',
            'role_id' => $adminRole->id,
            'organization_id' => $clinic->id,
            'password' => Hash::make('root'),
            'organization_verify' => 'verified',
        ]);

        // 3. Pharmacy Admin
        User::create([
            'name' => 'Pharmacy Administrator',
            'email' => 'admin@pharmacy.com',
            'role_id' => $adminRole->id,
            'organization_id' => $pharmacy->id,
            'password' => Hash::make('root'),
            'organization_verify' => 'verified',
        ]);

        // 4. Doctor (Clinic)
        User::create([
            'name' => 'Dr. Jane Smith',
            'email' => 'doctor@clinic.com',
            'role_id' => $doctorRole->id,
            'organization_id' => $clinic->id,
            'password' => Hash::make('root'),
            'organization_verify' => 'verified',
        ]);

        // 5. Practitioner (Clinic)
        User::create([
            'name' => 'John Practitioner',
            'email' => 'pract@clinic.com',
            'role_id' => $practitionerRole->id,
            'organization_id' => $clinic->id,
            'password' => Hash::make('root'),
            'organization_verify' => 'verified',
        ]);

        // 6. Practitioner (Pharmacy)
        User::create([
            'name' => 'Sarah Pharm Practitioner',
            'email' => 'pract@pharmacy.com',
            'role_id' => $practitionerRole->id,
            'organization_id' => $pharmacy->id,
            'password' => Hash::make('root'),
            'organization_verify' => 'verified',
        ]);

        // 7. Mother (Clinic) - Full Details
        User::create([
            'name' => 'Mary Mother',
            'email' => 'mother@example.com',
            'role_id' => $motherRole->id,
            'organization_id' => $clinic->id,
            'password' => Hash::make('root'),
            'organization_verify' => 'verified',
            'phone' => '0995936887',
            'date_of_birth' => '1995-05-15',
            'age' => 31,
            'religion' => 'Christianity',
            'marital_status' => 'Married',
            'level_of_education' => 'Secondary',
            'occupation' => 'Teacher',
            'address' => 'Machinjiri Area 2',
            'traditional_authority' => 'Machinjiri',
            'next_of_kin' => 'Lebogang Smith',
            'next_of_kin_mobile' => '0884348727',
            'height' => 165,
            'leg_or_spine' => 'no',
            'deformity' => 'no',
            'deliveries' => 2,
            'abortions' => 0,
            'still_births' => 'no',
            'c_section' => 'no',
            'vacum' => 'no',
            'multiple' => 'no',
            'tuberculosis' => 'no',
            'asthma' => 'no',
            'hypertension' => 'no',
            'diabetes' => 'no',
            'epilepsy' => 'no',
            'renal_disease' => 'no',
            'fistula_repair' => 'no',
            'menstrual_cycle' => 'regular',
            'is_active' => 1,
        ]);
    }
}
