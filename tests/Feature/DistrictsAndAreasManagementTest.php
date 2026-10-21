<?php

use App\Livewire\Districts\DistrictsAreasLivewire;
use App\Livewire\Mothers\MothersManagementLivewire;
use App\Livewire\Organizations\OrganizationsLivewire;
use App\Models\Area;
use App\Models\District;
use App\Models\Role;
use App\Models\User;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;

beforeEach(function () {
    $this->systemAdminRole = Role::firstOrCreate(['name' => 'system-admin']);
    $this->adminRole = Role::firstOrCreate(['name' => 'admin']);
    $this->practitionerRole = Role::firstOrCreate(['name' => 'practitioner']);

    $this->systemAdmin = User::factory()->create([
        'role_id' => $this->systemAdminRole->id,
        'organization_id' => null,
        'organization_verify' => 'verified',
        'is_active' => true,
    ]);

    $this->regularUser = User::factory()->create([
        'role_id' => $this->practitionerRole->id,
        'organization_id' => null,
        'organization_verify' => 'verified',
        'is_active' => true,
    ]);
});

it('prevents non-system-admin users from accessing the districts and areas page', function () {
    actingAs($this->regularUser)
        ->get(route('districts.areas'))
        ->assertRedirect(route('access-denied'));
});

it('allows system admin to access the districts and areas management page', function () {
    $district = District::create(['name' => 'Lilongwe']);
    $area = Area::create(['district_id' => $district->id, 'name' => 'Area 25']);

    actingAs($this->systemAdmin)
        ->get(route('districts.areas'))
        ->assertOk()
        ->assertSee('Lilongwe')
        ->assertSee('Total Districts')
        ->assertSee('Total Areas');
});

it('allows system admin to create, edit, and delete districts', function () {
    // 1. Create District
    Livewire::actingAs($this->systemAdmin)
        ->test(DistrictsAreasLivewire::class)
        ->call('createDistrict')
        ->assertSet('districtModal', true)
        ->set('district_name', 'Mzimba')
        ->call('saveDistrict')
        ->assertHasNoErrors()
        ->assertSet('districtModal', false);

    $mzimba = District::where('name', 'Mzimba')->first();
    expect($mzimba)->not->toBeNull();

    // 2. Duplicate validation
    Livewire::actingAs($this->systemAdmin)
        ->test(DistrictsAreasLivewire::class)
        ->call('createDistrict')
        ->set('district_name', 'Mzimba')
        ->call('saveDistrict')
        ->assertHasErrors(['district_name']);

    // 3. Edit District
    Livewire::actingAs($this->systemAdmin)
        ->test(DistrictsAreasLivewire::class)
        ->call('editDistrict', $mzimba->id)
        ->assertSet('districtModal', true)
        ->assertSet('district_name', 'Mzimba')
        ->set('district_name', 'Mzimba North')
        ->call('saveDistrict')
        ->assertHasNoErrors()
        ->assertSet('districtModal', false);

    $mzimba->refresh();
    expect($mzimba->name)->toBe('Mzimba North');

    // 4. Delete District
    Livewire::actingAs($this->systemAdmin)
        ->test(DistrictsAreasLivewire::class)
        ->call('confirmDeleteDistrict', $mzimba->id)
        ->assertSet('confirmDeleteDistrictModal', true)
        ->call('deleteDistrict')
        ->assertSet('confirmDeleteDistrictModal', false);

    expect(District::find($mzimba->id))->toBeNull();
});

it('allows system admin to create, edit, and delete areas', function () {
    $district = District::create(['name' => 'Zomba']);

    // 1. Create Area
    Livewire::actingAs($this->systemAdmin)
        ->test(DistrictsAreasLivewire::class)
        ->set('activeTab', 'areas')
        ->call('createArea', $district->id)
        ->assertSet('areaModal', true)
        ->assertSet('area_district_id', (string) $district->id)
        ->set('area_name', 'Chinamwali')
        ->call('saveArea')
        ->assertHasNoErrors()
        ->assertSet('areaModal', false);

    $area = Area::where('name', 'Chinamwali')->where('district_id', $district->id)->first();
    expect($area)->not->toBeNull();

    // 2. Duplicate Area validation within same district
    Livewire::actingAs($this->systemAdmin)
        ->test(DistrictsAreasLivewire::class)
        ->call('createArea', $district->id)
        ->set('area_name', 'Chinamwali')
        ->call('saveArea')
        ->assertHasErrors(['area_name']);

    // 3. Edit Area
    Livewire::actingAs($this->systemAdmin)
        ->test(DistrictsAreasLivewire::class)
        ->call('editArea', $area->id)
        ->assertSet('areaModal', true)
        ->assertSet('area_name', 'Chinamwali')
        ->set('area_name', 'Chinamwali East')
        ->call('saveArea')
        ->assertHasNoErrors()
        ->assertSet('areaModal', false);

    $area->refresh();
    expect($area->name)->toBe('Chinamwali East');

    // 4. Delete Area
    Livewire::actingAs($this->systemAdmin)
        ->test(DistrictsAreasLivewire::class)
        ->call('confirmDeleteArea', $area->id)
        ->assertSet('confirmDeleteAreaModal', true)
        ->call('deleteArea')
        ->assertSet('confirmDeleteAreaModal', false);

    expect(Area::find($area->id))->toBeNull();
});

it('can filter and search districts and areas', function () {
    $d1 = District::create(['name' => 'Blantyre']);
    $d2 = District::create(['name' => 'Dedza']);

    $a1 = Area::create(['district_id' => $d1->id, 'name' => 'Chilomoni']);
    $a2 = Area::create(['district_id' => $d2->id, 'name' => 'Bembeke']);

    Livewire::actingAs($this->systemAdmin)
        ->test(DistrictsAreasLivewire::class)
        ->assertSee("district-row-{$d1->id}")
        ->assertSee("district-row-{$d2->id}")
        ->set('districtSearch', 'Blan')
        ->assertSee("district-row-{$d1->id}")
        ->assertDontSee("district-row-{$d2->id}")
        ->set('districtSearch', '')
        ->call('viewAreasForDistrict', $d1->id)
        ->assertSet('activeTab', 'areas')
        ->assertSet('areaDistrictFilter', (string) $d1->id)
        ->assertSee("area-row-{$a1->id}")
        ->assertDontSee("area-row-{$a2->id}");
});

it('supports quick adding a new district in mothers management form', function () {
    Livewire::actingAs($this->systemAdmin)
        ->test(MothersManagementLivewire::class)
        ->call('addDistrict')
        ->assertSet('districtModal', true)
        ->set('new_district_name', 'Kasungu')
        ->call('storeDistrict')
        ->assertHasNoErrors()
        ->assertSet('districtModal', false);

    $created = District::where('name', 'Kasungu')->first();
    expect($created)->not->toBeNull();
});

it('supports quick adding a new district and area in organization creation form', function () {
    Livewire::actingAs($this->systemAdmin)
        ->test(OrganizationsLivewire::class)
        ->call('addDistrict')
        ->assertSet('districtModal', true)
        ->set('new_district_name', 'Salima')
        ->call('storeDistrict')
        ->assertHasNoErrors()
        ->assertSet('districtModal', false);

    $salima = District::where('name', 'Salima')->first();
    expect($salima)->not->toBeNull();

    Livewire::actingAs($this->systemAdmin)
        ->test(OrganizationsLivewire::class)
        ->set('district_id', $salima->id)
        ->call('addArea')
        ->assertSet('areaModal', true)
        ->set('new_area_name', 'Senga Bay')
        ->call('storeArea')
        ->assertHasNoErrors()
        ->assertSet('areaModal', false);

    $sengaBay = Area::where('name', 'Senga Bay')->where('district_id', $salima->id)->first();
    expect($sengaBay)->not->toBeNull();
});

it('resets all modals properly when cancel is triggered across components', function () {
    Livewire::actingAs($this->systemAdmin)
        ->test(DistrictsAreasLivewire::class)
        ->call('createDistrict')
        ->assertSet('districtModal', true)
        ->call('cancel')
        ->assertSet('districtModal', false);

    $testDistrict = District::create(['name' => 'Test District']);

    Livewire::actingAs($this->systemAdmin)
        ->test(OrganizationsLivewire::class)
        ->call('addDistrict')
        ->assertSet('districtModal', true)
        ->call('cancel')
        ->assertSet('districtModal', false)
        ->set('district_id', $testDistrict->id)
        ->call('addArea')
        ->assertSet('areaModal', true)
        ->call('cancel')
        ->assertSet('areaModal', false);

    Livewire::actingAs($this->systemAdmin)
        ->test(MothersManagementLivewire::class)
        ->call('addDistrict')
        ->assertSet('districtModal', true)
        ->call('cancel')
        ->assertSet('districtModal', false)
        ->set('district_id', $testDistrict->id)
        ->call('addArea')
        ->assertSet('areaModal', true)
        ->call('cancel')
        ->assertSet('areaModal', false);
});
