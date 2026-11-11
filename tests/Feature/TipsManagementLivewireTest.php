<?php

use App\Livewire\Tips\TipsManagementLivewire;
use App\Models\Area;
use App\Models\Day;
use App\Models\DayRange;
use App\Models\District;
use App\Models\History;
use App\Models\Organization;
use App\Models\Tip;
use App\Models\Trimester;
use App\Models\User;
use App\Models\Week;
use Database\Seeders\DaySeeder;
use Database\Seeders\RoleSeeder;
use Livewire\Livewire;

beforeEach(function () {
    $this->seed(RoleSeeder::class);
    $this->seed(DaySeeder::class);

    $this->district = District::create(['name' => 'Lilongwe']);
    $this->area = Area::create(['name' => 'Area 25', 'district_id' => $this->district->id]);

    $this->dayRange = DayRange::firstOrCreate(
        ['name' => 'Morning Window'],
        ['start_time' => '08:00:00', 'end_time' => '12:00:00']
    );

    $this->trimester = Trimester::firstOrCreate(['trimester' => 1]);
    $this->week = Week::firstOrCreate(['week' => 10, 'trimester_id' => $this->trimester->id]);

    // Organizations
    $this->orgA = Organization::create([
        'name' => 'Uchembere Clinic',
        'acronym' => 'UC',
        'district_id' => $this->district->id,
        'area_id' => $this->area->id,
        'is_pharmacy' => false,
    ]);

    $this->orgB = Organization::create([
        'name' => 'Chikondi Hospital',
        'acronym' => 'CH',
        'district_id' => $this->district->id,
        'area_id' => $this->area->id,
        'is_pharmacy' => false,
    ]);

    $this->pharmacyOrg = Organization::create([
        'name' => 'City Pharmacy',
        'acronym' => 'CP',
        'district_id' => $this->district->id,
        'area_id' => $this->area->id,
        'is_pharmacy' => true,
    ]);

    // Users
    $this->systemAdmin = User::factory()->create([
        'role_id' => 1,
        'email_verified_at' => now(),
        'is_active' => true,
        'organization_verify' => 'verified',
    ]);
    $this->orgAdminA = User::factory()->create([
        'role_id' => 2,
        'organization_id' => $this->orgA->id,
        'email_verified_at' => now(),
        'is_active' => true,
        'organization_verify' => 'verified',
    ]);
    $this->doctorA = User::factory()->create([
        'role_id' => 3,
        'organization_id' => $this->orgA->id,
        'email_verified_at' => now(),
        'is_active' => true,
        'organization_verify' => 'verified',
    ]);
    $this->pharmacyAdmin = User::factory()->create([
        'role_id' => 2,
        'organization_id' => $this->pharmacyOrg->id,
        'email_verified_at' => now(),
        'is_active' => true,
        'organization_verify' => 'verified',
    ]);
    $this->motherUser = User::factory()->create([
        'role_id' => 4,
        'organization_id' => $this->orgA->id,
        'email_verified_at' => now(),
        'is_active' => true,
        'organization_verify' => 'verified',
    ]);
});

it('allows system admin, org admin, and doctor to access tips page, but restricts pharmacy admins and mothers', function () {
    $this->actingAs($this->systemAdmin);
    $this->get(route('tips'))->assertSuccessful();

    $this->actingAs($this->orgAdminA);
    $this->get(route('tips'))->assertSuccessful();

    $this->actingAs($this->doctorA);
    $this->get(route('tips'))->assertSuccessful();

    // Pharmacy Admin restricted with redirect to access-denied
    $this->actingAs($this->pharmacyAdmin);
    $this->get(route('tips'))->assertRedirect(route('access-denied'));

    // Mother restricted with 403 forbidden
    $this->actingAs($this->motherUser);
    $this->get(route('tips'))->assertForbidden();
});

it('scopes tips visibility to own organization for non-system admins', function () {
    $tipA = Tip::create([
        'tip' => 'Org A exclusive tip for healthy hydration.',
        'week_id' => 10,
        'day_id' => 2,
        'day_range_id' => $this->dayRange->id,
        'organization_id' => $this->orgA->id,
        'status' => Tip::STATUS_APPROVED,
    ]);

    $tipB = Tip::create([
        'tip' => 'Org B exclusive tip for balanced diet.',
        'week_id' => 10,
        'day_id' => 2,
        'day_range_id' => $this->dayRange->id,
        'organization_id' => $this->orgB->id,
        'status' => Tip::STATUS_APPROVED,
    ]);

    // Org Admin A sees ONLY Org A tip
    Livewire::actingAs($this->orgAdminA)
        ->test(TipsManagementLivewire::class)
        ->assertSee('Org A exclusive tip')
        ->assertDontSee('Org B exclusive tip');

    // System Admin sees BOTH tips and can filter by organization
    Livewire::actingAs($this->systemAdmin)
        ->test(TipsManagementLivewire::class)
        ->assertSee('Org A exclusive tip')
        ->assertSee('Org B exclusive tip')
        ->set('organizationFilter', (string) $this->orgA->id)
        ->assertSee('Org A exclusive tip')
        ->assertDontSee('Org B exclusive tip')
        ->set('organizationFilter', (string) $this->orgB->id)
        ->assertSee('Org B exclusive tip')
        ->assertDontSee('Org A exclusive tip');
});

it('allows clinical staff to create, edit, approve, and delete tips', function () {
    $this->actingAs($this->doctorA);

    // 1. Create a new tip
    Livewire::test(TipsManagementLivewire::class)
        ->call('openCreateModal')
        ->assertSet('modal', true)
        ->set('tip_content', 'New Doctor Tip: Rest frequently on left side.')
        ->set('week_id', 10)
        ->set('day_id', 3)
        ->set('day_range_id', $this->dayRange->id)
        ->call('store')
        ->assertHasNoErrors()
        ->assertSet('modal', false)
        ->assertDispatched('alert');

    $createdTip = Tip::where('tip', 'New Doctor Tip: Rest frequently on left side.')->first();
    expect($createdTip)->not->toBeNull();
    expect($createdTip->organization_id)->toBe($this->orgA->id);
    expect($createdTip->status)->toBe(Tip::STATUS_APPROVED);

    // 2. Edit tip
    Livewire::test(TipsManagementLivewire::class)
        ->call('openEditModal', $createdTip->id)
        ->assertSet('modal', true)
        ->assertSet('tip_content', 'New Doctor Tip: Rest frequently on left side.')
        ->set('tip_content', 'Updated Doctor Tip: Drink fresh clean boiled water.')
        ->call('store')
        ->assertHasNoErrors()
        ->assertSet('modal', false);

    expect($createdTip->fresh()->tip)->toBe('Updated Doctor Tip: Drink fresh clean boiled water.');

    // 3. Approve a pending tip
    $pendingTip = Tip::create([
        'tip' => 'Pending Staff Tip: Gentle daily exercise.',
        'week_id' => 10,
        'day_id' => 4,
        'day_range_id' => $this->dayRange->id,
        'organization_id' => $this->orgA->id,
        'status' => Tip::STATUS_PENDING,
    ]);

    Livewire::test(TipsManagementLivewire::class)
        ->call('approveTip', $pendingTip->id)
        ->assertDispatched('alert');

    expect($pendingTip->fresh()->status)->toBe(Tip::STATUS_APPROVED);

    // 4. Delete tip with cancel check
    Livewire::test(TipsManagementLivewire::class)
        ->call('confirmDelete', $createdTip->id)
        ->assertSet('deleteModal', true)
        ->call('cancel')
        ->assertSet('deleteModal', false)
        ->call('confirmDelete', $createdTip->id)
        ->call('deleteTip')
        ->assertHasNoErrors()
        ->assertSet('deleteModal', false);

    expect(Tip::find($createdTip->id))->toBeNull();
});

it('calculates daily delivery forecast and displays recipient mothers for a specific date', function () {
    // Tip for Week 10, Day 2 belonging to Org A
    $tipA = Tip::create([
        'tip' => 'Hydration reminder for week 10.',
        'week_id' => 10,
        'day_id' => 2,
        'day_range_id' => $this->dayRange->id,
        'organization_id' => $this->orgA->id,
        'status' => Tip::STATUS_APPROVED,
    ]);

    // Mother registered in Org A with gestation matching Week 10, Day 2 on today
    $targetDate = now()->startOfDay();
    $lmcDate = (clone $targetDate)->subDays(65); // 65 days = week 10, day 2

    $mother = User::factory()->create([
        'name' => 'Chikondi Phiri',
        'phone' => '0995556677',
        'role_id' => 4,
        'organization_id' => $this->orgA->id,
        'district_id' => $this->district->id,
        'area_id' => $this->area->id,
    ]);

    $history = History::create([
        'mother_id' => $mother->id,
        'last_menstrual_cycle' => $lmcDate->format('Y-m-d'),
        'infant_number' => 1,
        'organization_id' => $this->orgA->id,
    ]);

    Livewire::actingAs($this->orgAdminA)
        ->test(TipsManagementLivewire::class)
        ->set('selectedDate', $targetDate->toDateString())
        ->set('activeTab', 'forecast')
        ->assertSee('Hydration reminder for week 10.')
        ->assertSee('Chikondi Phiri')
        ->assertSee('0995556677')
        ->assertSee('1 Recipient Mothers')
        ->call('openRecipientsModal', $tipA->id)
        ->assertSet('recipientsModal', true)
        ->assertSee('Chikondi Phiri')
        ->call('cancel')
        ->assertSet('recipientsModal', false);
});

it('allows users to view template library and adopt templates into their organization', function () {
    // Create a global template tip
    $template = Tip::create([
        'tip' => 'Standard WHO guideline: Consume iron and folic acid supplements daily.',
        'week_id' => $this->week->id,
        'day_id' => 1,
        'day_range_id' => $this->dayRange->id,
        'organization_id' => null,
        'status' => Tip::STATUS_APPROVED,
        'is_template' => true,
    ]);

    // Org Admin A browses templates tab and adopts the template
    Livewire::actingAs($this->orgAdminA)
        ->test(TipsManagementLivewire::class)
        ->set('activeTab', 'templates')
        ->assertSee('Standard WHO guideline: Consume iron and folic acid supplements daily.')
        ->assertSee('Adopt Template')
        ->call('useTemplate', $template->id)
        ->assertDispatched('alert');

    // Verify tip was copied to Org A
    $copiedTip = Tip::where('organization_id', $this->orgA->id)
        ->where('tip', $template->tip)
        ->first();

    expect($copiedTip)->not->toBeNull();
    expect($copiedTip->week_id)->toBe($this->week->id);
    expect($copiedTip->day_id)->toBe(1);
    expect($copiedTip->is_template)->toBeFalse();
    expect($copiedTip->organization_id)->toBe($this->orgA->id);
});

it('allows system admin to mark tips as templates and create new template tips', function () {
    $tip = Tip::create([
        'tip' => 'Hydration guidance for early pregnancy.',
        'week_id' => $this->week->id,
        'day_id' => 5,
        'day_range_id' => $this->dayRange->id,
        'organization_id' => $this->orgA->id,
        'status' => Tip::STATUS_APPROVED,
        'is_template' => false,
    ]);

    // System admin toggles template
    Livewire::actingAs($this->systemAdmin)
        ->test(TipsManagementLivewire::class)
        ->call('toggleTemplate', $tip->id)
        ->assertDispatched('alert');

    expect($tip->fresh()->is_template)->toBeTrue();

    // System admin creates a new template tip directly via modal
    Livewire::actingAs($this->systemAdmin)
        ->test(TipsManagementLivewire::class)
        ->call('openCreateModal', true)
        ->assertSet('modal', true)
        ->assertSet('is_template', true)
        ->set('tip_content', 'New System Template: Balanced nutrition in pregnancy.')
        ->set('week_id', $this->week->id)
        ->set('day_id', 2)
        ->set('day_range_id', $this->dayRange->id)
        ->call('store')
        ->assertHasNoErrors()
        ->assertSet('modal', false);

    $createdTpl = Tip::where('tip', 'New System Template: Balanced nutrition in pregnancy.')->first();
    expect($createdTpl)->not->toBeNull();
    expect($createdTpl->is_template)->toBeTrue();
});

it('filters template tips by search query, trimester, and week', function () {
    $tri2 = Trimester::firstOrCreate(['trimester' => 2]);
    $week20 = Week::firstOrCreate(['week' => 20, 'trimester_id' => $tri2->id]);

    $tpl1 = Tip::create([
        'tip' => 'Template First: Morning nausea management tips.',
        'week_id' => $this->week->id, // Trimester 1
        'day_id' => 1,
        'day_range_id' => $this->dayRange->id,
        'status' => Tip::STATUS_APPROVED,
        'is_template' => true,
    ]);

    $tpl2 = Tip::create([
        'tip' => 'Template Second: Kick counts tracking in mid pregnancy.',
        'week_id' => $week20->id, // Trimester 2
        'day_id' => 1,
        'day_range_id' => $this->dayRange->id,
        'status' => Tip::STATUS_APPROVED,
        'is_template' => true,
    ]);

    Livewire::actingAs($this->orgAdminA)
        ->test(TipsManagementLivewire::class)
        ->set('activeTab', 'templates')
        ->assertSee('Morning nausea')
        ->assertSee('Kick counts')
        // Filter by Search
        ->set('templateSearch', 'nausea')
        ->assertSee('Morning nausea')
        ->assertDontSee('Kick counts')
        // Reset Search and filter by Trimester 2
        ->set('templateSearch', '')
        ->set('templateTrimesterFilter', (string) $tri2->id)
        ->assertSee('Kick counts')
        ->assertDontSee('Morning nausea')
        // Reset filters
        ->call('resetTemplateFilters')
        ->assertSee('Morning nausea')
        ->assertSee('Kick counts');
});
