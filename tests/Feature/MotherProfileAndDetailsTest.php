<?php

use App\Helper\StandardData as SD;
use App\Livewire\Mothers\MotherLivewire;
use App\Models\Area;
use App\Models\Day;
use App\Models\DayRange;
use App\Models\District;
use App\Models\History;
use App\Models\MessageHistory;
use App\Models\Organization;
use App\Models\Role;
use App\Models\Tip;
use App\Models\Trimester;
use App\Models\User;
use App\Models\Week;
use App\Services\SmsService;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;

beforeEach(function () {
    $this->systemAdminRole = Role::firstOrCreate(['name' => 'system-admin']);
    $this->doctorRole = Role::firstOrCreate(['name' => 'doctor']);
    $this->motherRole = Role::firstOrCreate(['name' => 'mother']);

    $this->district = District::firstOrCreate(['name' => 'Lilongwe']);
    $this->area = Area::firstOrCreate(['name' => 'Area 18', 'district_id' => $this->district->id]);

    $this->systemAdmin = User::factory()->create([
        'role_id' => $this->systemAdminRole->id,
        'organization_id' => null,
        'organization_verify' => 'verified',
        'is_active' => true,
    ]);

    $this->organization = Organization::create([
        'name' => 'Bwaila Hospital',
        'email' => 'contact@bwaila.org',
        'phone' => '0999123123',
        'district_id' => $this->district->id,
        'area_id' => $this->area->id,
        'is_pharmacy' => false,
        'owner_id' => $this->systemAdmin->id,
    ]);

    $this->mother = User::factory()->create([
        'name' => 'Amina Banda',
        'email' => 'amina.banda@example.com',
        'phone' => '0999888999',
        'role_id' => $this->motherRole->id,
        'organization_id' => $this->organization->id,
        'organization_verify' => 'verified',
        'district_id' => $this->district->id,
        'area_id' => $this->area->id,
        'traditional_authority' => 'Tsabango',
        'address' => 'House 42, Area 18',
        'age' => 24,
        'marital_status' => 'married',
        'religion' => 'Christian',
        'level_of_education' => 'Secondary',
        'occupation' => 'Business',
        'next_of_kin' => 'John Banda',
        'next_of_kin_mobile' => '0888777666',
        'height' => 162,
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
        'pre_eclampsia' => 'no',
        'hypertension' => 'no',
        'menstrual_cycle' => 'regular',
        'is_active' => true,
    ]);
});

it('renders mother clinical profile with personal, pregnancy, and obstetric details', function () {
    // Create pregnancy session 16 weeks ago (2nd trimester)
    $session = History::create([
        'mother_id' => $this->mother->id,
        'infant_number' => 1,
        'last_menstrual_cycle' => now()->subWeeks(16)->format('Y-m-d'),
        'organization_id' => $this->organization->id,
    ]);

    actingAs($this->systemAdmin)
        ->get(route('mothers.show', SD::encrypt($this->mother->id)))
        ->assertOk()
        ->assertSee('Amina Banda')
        ->assertSee('Bwaila Hospital')
        ->assertSee('Infant #1')
        ->assertSee('2nd Trimester')
        ->assertSee('Week 16');

    Livewire::actingAs($this->systemAdmin)
        ->test(MotherLivewire::class, ['mother_id' => $this->mother->id])
        ->assertSee('Amina Banda')
        ->assertSee('Infant #1')
        ->set('activeTab', 'profile')
        ->assertSee('Secondary')
        ->assertSee('John Banda')
        ->assertSee('Tsabango');
});

it('allows doctors and admins to create, edit, and delete pregnancy sessions for a mother', function () {
    Livewire::actingAs($this->systemAdmin)
        ->test(MotherLivewire::class, ['mother_id' => $this->mother->id])
        ->call('create')
        ->assertSet('modal', true)
        ->set('infant_number', 1)
        ->set('last_menstrual_cycle', now()->subWeeks(10)->format('Y-m-d'))
        ->call('store')
        ->assertHasNoErrors()
        ->assertSet('modal', false);

    $session = History::where('mother_id', $this->mother->id)->first();
    expect($session)->not->toBeNull();
    expect($session->infant_number)->toBe(1);

    // Edit session
    Livewire::actingAs($this->systemAdmin)
        ->test(MotherLivewire::class, ['mother_id' => $this->mother->id])
        ->call('create', $session->id)
        ->assertSet('modal', true)
        ->assertSet('history_id', $session->id)
        ->set('infant_number', 2)
        ->call('store')
        ->assertHasNoErrors()
        ->assertSet('modal', false);

    $session->refresh();
    expect($session->infant_number)->toBe(2);

    // Delete session
    Livewire::actingAs($this->systemAdmin)
        ->test(MotherLivewire::class, ['mother_id' => $this->mother->id])
        ->call('confirmDeleteSession', $session->id)
        ->assertSet('deleteModal', true)
        ->call('deleteSession')
        ->assertSet('deleteModal', false);

    expect(History::find($session->id))->toBeNull();
});

it('renders messages received section with filtering and details modal', function () {
    $trimester = Trimester::firstOrCreate(['trimester' => 2]);
    $week = Week::firstOrCreate(['week' => 18, 'trimester_id' => $trimester->id]);
    $day = Day::firstOrCreate(['day_number' => 1]);
    $dayRange = DayRange::firstOrCreate(['name' => 'Morning', 'start_time' => '08:00', 'end_time' => '12:00']);

    $session = History::create([
        'mother_id' => $this->mother->id,
        'infant_number' => 1,
        'last_menstrual_cycle' => now()->subWeeks(18)->format('Y-m-d'),
        'organization_id' => $this->organization->id,
    ]);

    $tip = Tip::create([
        'tip' => 'Eat nutritious foods rich in iron during your second trimester.',
        'week_id' => $week->id,
        'day_id' => $day->id,
        'day_range_id' => $dayRange->id,
        'organization_id' => $this->organization->id,
        'status' => Tip::STATUS_APPROVED,
    ]);

    $message = MessageHistory::create([
        'mother_id' => $this->mother->id,
        'tip_id' => $tip->id,
        'week_id' => $week->id,
        'day_id' => $day->id,
        'day_range_id' => $dayRange->id,
        'history_id' => $session->id,
        'message_status' => 'Delivered',
        'api_response' => ['status' => 'Success', 'cost' => '0.02 USD'],
        'organization_id' => $this->organization->id,
    ]);

    Livewire::actingAs($this->systemAdmin)
        ->test(MotherLivewire::class, ['mother_id' => $this->mother->id])
        ->set('activeTab', 'messages')
        ->assertSee('Eat nutritious foods rich in iron')
        ->assertSee('Delivered')
        ->call('viewMessage', $message->id, 'tip')
        ->assertSet('viewMessageModal', true)
        ->assertSee('SMS Message Delivery Details')
        ->call('cancel')
        ->assertSet('viewMessageModal', false);
});

it('can send custom quick SMS directly to mother', function () {
    $smsServiceMock = Mockery::mock(SmsService::class);
    $smsServiceMock->shouldReceive('sendSmsGeneric')
        ->once()
        ->andReturn(true);

    app()->instance(SmsService::class, $smsServiceMock);

    Livewire::actingAs($this->systemAdmin)
        ->test(MotherLivewire::class, ['mother_id' => $this->mother->id])
        ->call('openCustomSmsModal')
        ->assertSet('customSmsModal', true)
        ->set('customMessageText', 'Reminder: Please visit clinic tomorrow for routine checkup.')
        ->call('sendCustomSms')
        ->assertHasNoErrors()
        ->assertSet('customSmsModal', false);

    $sentMessage = MessageHistory::where('mother_id', $this->mother->id)->latest()->first();
    expect($sentMessage)->not->toBeNull();
    expect($sentMessage->message_status)->toBe('Sent');
});

it('strictly adheres to modal cancel method convention', function () {
    Livewire::actingAs($this->systemAdmin)
        ->test(MotherLivewire::class, ['mother_id' => $this->mother->id])
        ->call('create')
        ->assertSet('modal', true)
        ->call('cancel')
        ->assertSet('modal', false)
        ->call('openCustomSmsModal')
        ->assertSet('customSmsModal', true)
        ->call('cancel')
        ->assertSet('customSmsModal', false);
});
