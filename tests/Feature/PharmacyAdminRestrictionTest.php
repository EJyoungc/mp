<?php

use App\Models\Organization;
use App\Models\Role;
use App\Models\User;

use function Pest\Laravel\actingAs;

beforeEach(function () {
    // Ensure roles exist
    $this->systemAdminRole = Role::firstOrCreate(['name' => 'system-admin']);
    $this->adminRole = Role::firstOrCreate(['name' => 'admin']);
    $this->practitionerRole = Role::firstOrCreate(['name' => 'practitioner']);

    // Create a pharmacy organization
    $this->pharmacy = Organization::factory()->create([
        'is_pharmacy' => true,
        'name' => 'Test Pharmacy',
    ]);

    // Create a regular organization
    $this->clinic = Organization::factory()->create([
        'is_pharmacy' => false,
        'name' => 'Test Clinic',
    ]);

    // Create a pharmacy admin
    $this->pharmacyAdmin = User::factory()->create([
        'role_id' => $this->adminRole->id,
        'organization_id' => $this->pharmacy->id,
        'organization_verify' => 'verified',
    ]);

    // Create a regular admin
    $this->clinicAdmin = User::factory()->create([
        'role_id' => $this->adminRole->id,
        'organization_id' => $this->clinic->id,
        'organization_verify' => 'verified',
    ]);
});

it('restricts pharmacy admin from accessing restricted routes', function () {
    actingAs($this->pharmacyAdmin)
        ->get(route('mothers'))
        ->assertRedirect(route('access-denied'));

    actingAs($this->pharmacyAdmin)
        ->get(route('organizations'))
        ->assertRedirect(route('access-denied'));

    actingAs($this->pharmacyAdmin)
        ->get(route('days'))
        ->assertRedirect(route('access-denied'));
});

it('allows regular admin to access restricted routes', function () {
    actingAs($this->clinicAdmin)
        ->get(route('mothers'))
        ->assertOk();

    actingAs($this->clinicAdmin)
        ->get(route('organizations'))
        ->assertOk();
});

it('restricts pharmacy admin to create only practitioners', function () {
    actingAs($this->pharmacyAdmin)
        ->get(route('users.create', 'doctor'))
        ->assertRedirect(route('access-denied'));

    actingAs($this->pharmacyAdmin)
        ->get(route('users.create', 'practitioner'))
        ->assertOk();
});
