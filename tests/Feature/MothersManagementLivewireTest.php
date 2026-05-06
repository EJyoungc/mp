<?php

use App\Livewire\Mothers\MothersManagementLivewire;
use App\Models\Organization;
use App\Models\Role;
use App\Models\User;
use Livewire\Livewire;

it('allows system admin to bulk reassign mothers', function () {
    $this->seed(\Database\Seeders\RoleSeeder::class);
    $org1 = Organization::factory()->create(['name' => 'Org 1']);
    $org2 = Organization::factory()->create(['name' => 'Org 2']);

    $admin = User::factory()->create(['role_id' => 1, 'organization_id' => $org1->id]);
    $mothers = User::factory()->count(3)->create(['role_id' => 4, 'organization_id' => $org1->id]);

    $this->actingAs($admin);

    Livewire::test(MothersManagementLivewire::class)
        ->set('selectedMothers', $mothers->pluck('id')->map(fn($id) => (string)$id)->toArray())
        ->set('bulkOrganizationId', $org2->id)
        ->call('confirmReassign')
        ->assertHasNoErrors()
        ->assertDispatched('alert');

    foreach ($mothers as $mother) {
        expect($mother->fresh()->organization_id)->toBe($org2->id);
    }
});

it('excludes pharmacies from reassign organization list', function () {
    $this->seed(\Database\Seeders\RoleSeeder::class);
    $admin = User::factory()->create(['role_id' => 1]);
    
    $clinic = Organization::factory()->create(['is_pharmacy' => false, 'name' => 'Clinic']);
    $pharmacy = Organization::factory()->create(['is_pharmacy' => true, 'name' => 'Pharmacy']);

    $this->actingAs($admin);

    Livewire::test(MothersManagementLivewire::class)
        ->assertViewHas('organizations', function ($orgs) use ($clinic, $pharmacy) {
            return $orgs->contains($clinic) && !$orgs->contains($pharmacy);
        });
});

it('can select all mothers', function () {
    $this->seed(\Database\Seeders\RoleSeeder::class);
    $admin = User::factory()->create(['role_id' => 1]);
    $mothers = User::factory()->count(5)->create(['role_id' => 4]);

    $this->actingAs($admin);

    Livewire::test(MothersManagementLivewire::class)
        ->set('selectAll', true)
        ->assertSet('selectedMothers', $mothers->pluck('id')->map(fn($id) => (string)$id)->toArray());
});

it('shows bulk reassign button only when mothers are selected', function () {
    $this->seed(\Database\Seeders\RoleSeeder::class);
    $admin = User::factory()->create(['role_id' => 1]);

    $this->actingAs($admin);

    Livewire::test(MothersManagementLivewire::class)
        ->assertDontSee('Reassign (')
        ->set('selectedMothers', ['1'])
        ->assertSee('Reassign (1)');
});
