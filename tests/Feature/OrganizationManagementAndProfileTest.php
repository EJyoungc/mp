<?php

use App\Livewire\Organizations\OrganizationProfileLivewire;
use App\Livewire\Organizations\OrganizationsLivewire;
use App\Models\AdHistory;
use App\Models\Area;
use App\Models\Day;
use App\Models\DayRange;
use App\Models\District;
use App\Models\History;
use App\Models\Invitation;
use App\Models\MessageHistory;
use App\Models\Organization;
use App\Models\PharmacyAd;
use App\Models\Role;
use App\Models\Tip;
use App\Models\Trimester;
use App\Models\User;
use App\Models\Week;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;

beforeEach(function () {
    $this->systemAdminRole = Role::firstOrCreate(['name' => 'system-admin']);
    $this->adminRole = Role::firstOrCreate(['name' => 'admin']);
    $this->doctorRole = Role::firstOrCreate(['name' => 'doctor']);
    $this->practitionerRole = Role::firstOrCreate(['name' => 'practitioner']);
    $this->motherRole = Role::firstOrCreate(['name' => 'mother']);

    $this->district = District::firstOrCreate(['name' => 'Lilongwe']);
    $this->area = Area::firstOrCreate(['name' => 'Area 25', 'district_id' => $this->district->id]);

    $this->systemAdmin = User::factory()->create([
        'role_id' => $this->systemAdminRole->id,
        'organization_id' => null,
        'organization_verify' => 'verified',
        'is_active' => true,
    ]);

    $this->organization = Organization::create([
        'name' => 'Test Health Centre',
        'email' => 'test@healthcentre.com',
        'phone' => '0999123456',
        'address' => 'Main Road, Area 25',
        'district_id' => $this->district->id,
        'area_id' => $this->area->id,
        'is_pharmacy' => false,
        'owner_id' => $this->systemAdmin->id,
    ]);
});

it('renders the organizations page with search and filter capabilities for system admin', function () {
    actingAs($this->systemAdmin)
        ->get(route('organizations'))
        ->assertOk()
        ->assertSee('Organizations')
        ->assertSee('Test Health Centre')
        ->assertSee('Clinics')
        ->assertSee('Pharmacies');

    Livewire::actingAs($this->systemAdmin)
        ->test(OrganizationsLivewire::class)
        ->assertSee('Test Health Centre')
        ->set('search', 'NonExistentOrgName')
        ->assertDontSee('Test Health Centre')
        ->set('search', 'Test Health')
        ->assertSee('Test Health Centre');
});

it('renders organization profile dashboard with details, cards, staff and mothers', function () {
    // Add staff member
    $doctor = User::factory()->create([
        'name' => 'Dr. Jane Banda',
        'email' => 'dr.banda@healthcentre.com',
        'role_id' => $this->doctorRole->id,
        'organization_id' => $this->organization->id,
        'organization_verify' => 'verified',
        'is_active' => true,
    ]);

    // Add mother
    $mother = User::factory()->create([
        'name' => 'Mary Phiri',
        'email' => 'mary.phiri@example.com',
        'phone' => '0888111222',
        'role_id' => $this->motherRole->id,
        'organization_id' => $this->organization->id,
        'organization_verify' => 'verified',
        'is_active' => true,
    ]);

    actingAs($this->systemAdmin)
        ->get(route('organizations.profile', $this->organization->id))
        ->assertOk()
        ->assertSee('Test Health Centre')
        ->assertSee('Organization Profile')
        ->assertSee('Dr. Jane Banda')
        ->assertSee('Staff');

    // Test Livewire component interactions
    Livewire::actingAs($this->systemAdmin)
        ->test(OrganizationProfileLivewire::class, ['id' => $this->organization->id])
        ->assertSee('Dr. Jane Banda')
        ->set('search', 'NonExistentDoctor')
        ->assertDontSee('Dr. Jane Banda')
        ->set('search', '')
        ->set('activeTab', 'mothers')
        ->assertSee('Mary Phiri')
        ->set('search', 'Mary')
        ->assertSee('Mary Phiri');
});

it('deletes an organization and cascades cleanly across all related tables', function () {
    // Attach a staff member
    $staff = User::factory()->create([
        'name' => 'Staff Member',
        'email' => 'staff@healthcentre.com',
        'role_id' => $this->practitionerRole->id,
        'organization_id' => $this->organization->id,
        'organization_verify' => 'verified',
        'is_active' => true,
    ]);

    // Attach a mother with medical history
    $mother = User::factory()->create([
        'name' => 'Mother Jane',
        'email' => 'mother.jane@example.com',
        'role_id' => $this->motherRole->id,
        'organization_id' => $this->organization->id,
        'organization_verify' => 'verified',
        'is_active' => true,
    ]);

    $history = History::create([
        'mother_id' => $mother->id,
        'organization_id' => $this->organization->id,
        'last_menstrual_cycle' => now()->subWeeks(12)->toDateString(),
        'infant_number' => 1,
    ]);

    // Ensure prerequisite records for Tip
    $trimester = Trimester::firstOrCreate(['trimester' => 1]);
    $week = Week::firstOrCreate(['week' => 1, 'trimester_id' => $trimester->id]);
    $day = Day::first();
    if (! $day) {
        $day = new Day;
        $day->day_number = 1;
        $day->save();
    }
    $dayRange = DayRange::firstOrCreate([
        'name' => 'Morning',
        'start_time' => '08:00',
        'end_time' => '12:00',
    ]);

    // Attach a tip
    $tip = Tip::create([
        'tip' => 'Eat nutritious food during pregnancy.',
        'organization_id' => $this->organization->id,
        'day_id' => $day->id,
        'day_range_id' => $dayRange->id,
        'week_id' => $week->id,
        'status' => 'approved',
    ]);

    // Attach a message history
    $messageHistory = MessageHistory::create([
        'mother_id' => $mother->id,
        'organization_id' => $this->organization->id,
        'tip_id' => $tip->id,
        'week_id' => $week->id,
        'day_id' => $day->id,
        'day_range_id' => $dayRange->id,
        'history_id' => $history->id,
        'message_status' => 'sent',
    ]);

    // Attach an ad
    $ad = PharmacyAd::create([
        'product_name' => 'Prenatal Vitamins',
        'ad_message' => 'Available at discounted price',
        'organization_id' => $this->organization->id,
        'is_active' => true,
    ]);

    // Attach ad history
    $adHistory = AdHistory::create([
        'mother_id' => $mother->id,
        'pharmacy_ad_id' => $ad->id,
        'organization_id' => $this->organization->id,
        'message' => 'Ad content',
        'status' => 'sent',
    ]);

    // Attach an invitation
    $invitation = Invitation::create([
        'email' => 'invitee@example.com',
        'organization_id' => $this->organization->id,
        'role_id' => $this->practitionerRole->id,
        'token' => 'test-token-12345',
        'expires_at' => now()->addDays(7),
    ]);

    // Trigger delete confirmed through Livewire component
    Livewire::actingAs($this->systemAdmin)
        ->test(OrganizationsLivewire::class)
        ->call('confirmDelete', $this->organization->id)
        ->assertSet('deleteModal', true)
        ->assertSet('confirmingDeleteId', $this->organization->id)
        ->call('deleteConfirmed')
        ->assertSet('deleteModal', false);

    // Verify all related records are deleted from their tables
    expect(Organization::find($this->organization->id))->toBeNull();
    expect(User::find($staff->id))->toBeNull();
    expect(User::find($mother->id))->toBeNull();
    expect(History::find($history->id))->toBeNull();
    expect(Tip::find($tip->id))->toBeNull();
    expect(MessageHistory::find($messageHistory->id))->toBeNull();
    expect(PharmacyAd::find($ad->id))->toBeNull();
    expect(AdHistory::find($adHistory->id))->toBeNull();
    expect(Invitation::find($invitation->id))->toBeNull();
});
