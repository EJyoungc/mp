<?php

use App\Livewire\Mothers\MothersManagementLivewire;
use App\Livewire\Users\UserCreateLivewire;
use App\Livewire\Users\UserEditLivewire;
use App\Models\Area;
use App\Models\District;
use App\Models\History;
use App\Models\Organization;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Livewire\Livewire;

it('allows system admin to bulk reassign mothers', function () {
    $this->seed(RoleSeeder::class);
    $org1 = Organization::factory()->create(['name' => 'Org 1']);
    $org2 = Organization::factory()->create(['name' => 'Org 2']);

    $admin = User::factory()->create(['role_id' => 1, 'organization_id' => $org1->id]);
    $mothers = User::factory()->count(3)->create(['role_id' => 4, 'organization_id' => $org1->id]);

    $this->actingAs($admin);

    Livewire::test(MothersManagementLivewire::class)
        ->set('selectedMothers', $mothers->pluck('id')->map(fn ($id) => (string) $id)->toArray())
        ->set('bulkOrganizationId', $org2->id)
        ->call('confirmReassign')
        ->assertHasNoErrors()
        ->assertDispatched('alert');

    foreach ($mothers as $mother) {
        expect($mother->fresh()->organization_id)->toBe($org2->id);
    }
});

it('excludes pharmacies from reassign organization list', function () {
    $this->seed(RoleSeeder::class);
    $admin = User::factory()->create(['role_id' => 1]);

    $clinic = Organization::factory()->create(['is_pharmacy' => false, 'name' => 'Clinic']);
    $pharmacy = Organization::factory()->create(['is_pharmacy' => true, 'name' => 'Pharmacy']);

    $this->actingAs($admin);

    Livewire::test(MothersManagementLivewire::class)
        ->assertViewHas('organizations', function ($orgs) use ($clinic, $pharmacy) {
            return $orgs->contains($clinic) && ! $orgs->contains($pharmacy);
        });
});

it('can select all mothers', function () {
    $this->seed(RoleSeeder::class);
    $admin = User::factory()->create(['role_id' => 1]);
    $mothers = User::factory()->count(5)->create(['role_id' => 4]);

    $this->actingAs($admin);

    Livewire::test(MothersManagementLivewire::class)
        ->set('selectAll', true)
        ->assertSet('selectedMothers', $mothers->pluck('id')->map(fn ($id) => (string) $id)->toArray());
});

it('shows bulk reassign button only when mothers are selected', function () {
    $this->seed(RoleSeeder::class);
    $admin = User::factory()->create(['role_id' => 1]);

    $this->actingAs($admin);

    Livewire::test(MothersManagementLivewire::class)
        ->assertDontSee('Reassign (')
        ->set('selectedMothers', ['1'])
        ->assertSee('Reassign (1)');
});

it('allows registering a new mother without an email address in MothersManagementLivewire', function () {
    $this->seed(RoleSeeder::class);
    $org = Organization::factory()->create(['name' => 'Bwaila Health Center', 'is_pharmacy' => false]);
    $admin = User::factory()->create(['role_id' => 1, 'organization_id' => $org->id]);
    $district = District::create(['name' => 'Lilongwe']);
    $area = Area::create(['name' => 'Area 25', 'district_id' => $district->id]);

    $this->actingAs($admin);

    Livewire::test(MothersManagementLivewire::class)
        ->call('create')
        ->set('name', 'Chikondi Phiri')
        ->set('phone', '0995936887')
        ->set('email', '') // Leaving email empty (optional)
        ->set('date_of_birth', '1998-05-15')
        ->set('marital_status', 'Married')
        ->set('religion', 'Christianity')
        ->set('level_of_education', 'Secondary')
        ->set('occupation', 'Business')
        ->set('address', 'Area 25 Sector 4')
        ->set('district_id', $district->id)
        ->set('area_id', $area->id)
        ->set('traditional_authority', 'T/A Chitukula')
        ->set('next_of_kin', 'John Phiri')
        ->set('next_of_kin_mobile', '0888123456')
        ->set('height', 162)
        ->set('last_menstrual_cycle', '2026-09-15')
        ->set('deliveries', 1)
        ->set('abortions', 0)
        ->call('store')
        ->assertHasNoErrors()
        ->assertDispatched('alert');

    $mother = User::where('phone', '0995936887')->first();
    expect($mother)->not->toBeNull();
    expect($mother->name)->toBe('Chikondi Phiri');
    expect($mother->role_id)->toBe(4);
    expect($mother->email)->toContain('@mother.com');

    $history = History::where('mother_id', $mother->id)->first();
    expect($history)->not->toBeNull();
    expect($history->last_menstrual_cycle)->toBe('2026-09-15');
});

it('allows updating an existing mother in MothersManagementLivewire without failing email uniqueness', function () {
    $this->seed(RoleSeeder::class);
    $org = Organization::factory()->create(['is_pharmacy' => false]);
    $admin = User::factory()->create(['role_id' => 1, 'organization_id' => $org->id]);
    $district = District::create(['name' => 'Zomba']);
    $area = Area::create(['name' => 'Sadzi', 'district_id' => $district->id]);

    $mother = User::create([
        'name' => 'Mary Banda',
        'email' => 'mary.banda@example.com',
        'phone' => '0999111222',
        'password' => bcrypt('password'),
        'role_id' => 4,
        'organization_id' => $org->id,
        'date_of_birth' => '1995-02-10',
        'age' => 31,
        'marital_status' => 'Married',
        'religion' => 'Christianity',
        'level_of_education' => 'Primary',
        'occupation' => 'Farmer',
        'address' => 'Zomba Town',
        'district_id' => $district->id,
        'area_id' => $area->id,
        'traditional_authority' => 'T/A Chikowi',
        'next_of_kin' => 'Peter Banda',
        'next_of_kin_mobile' => '0888222333',
        'height' => 158,
        'deliveries' => 2,
        'abortions' => 0,
    ]);

    History::create([
        'mother_id' => $mother->id,
        'last_menstrual_cycle' => '2026-08-01',
        'infant_number' => 1,
        'organization_id' => $org->id,
    ]);

    $this->actingAs($admin);

    Livewire::test(MothersManagementLivewire::class)
        ->call('edit', $mother->id)
        ->set('name', 'Mary Banda Updated')
        ->set('last_menstrual_cycle', '2026-08-10')
        ->call('store')
        ->assertHasNoErrors()
        ->assertDispatched('alert');

    expect($mother->fresh()->name)->toBe('Mary Banda Updated');
    expect(History::where('mother_id', $mother->id)->first()->last_menstrual_cycle)->toBe('2026-08-10');
});

it('registers a mother with pregnancy history via UserCreateLivewire', function () {
    $this->seed(RoleSeeder::class);
    $org = Organization::factory()->create(['is_pharmacy' => false]);
    $admin = User::factory()->create(['role_id' => 1, 'organization_id' => $org->id]);
    $district = District::create(['name' => 'Blantyre']);
    $area = Area::create(['name' => 'Ndirande', 'district_id' => $district->id]);

    $this->actingAs($admin);

    Livewire::test(UserCreateLivewire::class, ['role' => 'mother'])
        ->set('name', 'Tadala Gondwe')
        ->set('phone', '0991234567')
        ->set('date_of_birth', '2000-01-01')
        ->set('age', 26)
        ->set('marital_status', 'Single')
        ->set('religion', 'Christianity')
        ->set('level_of_education', 'Tertiary')
        ->set('occupation', 'Teacher')
        ->set('address', 'Ndirande Ring Road')
        ->set('district_id', $district->id)
        ->set('area_id', $area->id)
        ->set('traditional_authority', 'T/A Kapeni')
        ->set('next_of_kin', 'Grace Gondwe')
        ->set('next_of_kin_mobile', '0888765432')
        ->set('height', 165)
        ->set('last_menstrual_cycle', '2026-09-01')
        ->set('deliveries', 0)
        ->set('abortions', 0)
        ->call('store')
        ->assertHasNoErrors()
        ->assertRedirect(route('users'));

    $createdMother = User::where('phone', '0991234567')->first();
    expect($createdMother)->not->toBeNull();
    expect($createdMother->role_id)->toBe(4);

    $history = History::where('mother_id', $createdMother->id)->first();
    expect($history)->not->toBeNull();
    expect($history->last_menstrual_cycle)->toBe('2026-09-01');
});

it('updates a mother and her pregnancy history via UserEditLivewire', function () {
    $this->seed(RoleSeeder::class);
    $org = Organization::factory()->create(['is_pharmacy' => false]);
    $admin = User::factory()->create(['role_id' => 1, 'organization_id' => $org->id]);
    $district = District::create(['name' => 'Mzuzu']);
    $area = Area::create(['name' => 'Luwinga', 'district_id' => $district->id]);

    $mother = User::create([
        'name' => 'Mercy Chirwa',
        'email' => 'mercy.chirwa@example.com',
        'phone' => '0994445556',
        'password' => bcrypt('password'),
        'role_id' => 4,
        'organization_id' => $org->id,
        'date_of_birth' => '1999-04-12',
        'age' => 27,
        'marital_status' => 'Married',
        'religion' => 'Christianity',
        'level_of_education' => 'Secondary',
        'occupation' => 'Tailor',
        'address' => 'Luwinga Block 3',
        'district_id' => $district->id,
        'area_id' => $area->id,
        'traditional_authority' => 'T/A Mtwalo',
        'next_of_kin' => 'Isaac Chirwa',
        'next_of_kin_mobile' => '0888555666',
        'height' => 160,
        'leg_or_spine' => 'No',
        'deformity' => 'No',
        'deliveries' => 1,
        'abortions' => 0,
        'still_births' => 'No',
        'c_section' => 'No',
        'vacum' => 'No',
        'multiple' => 'No',
        'tuberculosis' => 'No',
        'asthma' => 'No',
        'menstrual_cycle' => 'Regular',
    ]);

    History::create([
        'mother_id' => $mother->id,
        'last_menstrual_cycle' => '2026-08-15',
        'infant_number' => 1,
        'organization_id' => $org->id,
    ]);

    $this->actingAs($admin);

    Livewire::test(UserEditLivewire::class, ['role' => 'mother', 'user_id' => $mother->id])
        ->assertSet('last_menstrual_cycle', '2026-08-15')
        ->set('name', 'Mercy Chirwa Kaunda')
        ->set('last_menstrual_cycle', '2026-08-20')
        ->call('store')
        ->assertHasNoErrors()
        ->assertRedirect(route('users'));

    expect($mother->fresh()->name)->toBe('Mercy Chirwa Kaunda');
    expect(History::where('mother_id', $mother->id)->first()->last_menstrual_cycle)->toBe('2026-08-20');
});
