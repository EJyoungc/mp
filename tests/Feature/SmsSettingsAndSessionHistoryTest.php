<?php

use App\Jobs\SendSmsTipJob;
use App\Livewire\Mothers\MotherSessionHistoryLivewire;
use App\Livewire\Settings\SmsSettingsLivewire;
use App\Models\DayRange;
use App\Models\History;
use App\Models\MessageHistory;
use App\Models\Organization;
use App\Models\Tip;
use App\Models\Trimester;
use App\Models\User;
use App\Models\Week;
use App\Services\SmsService;
use Database\Seeders\DaySeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Support\Facades\Queue;
use Livewire\Livewire;

beforeEach(function () {
    $this->seed(RoleSeeder::class);
    $this->seed(DaySeeder::class);

    // Organization with acronym
    $this->org = Organization::factory()->create([
        'name' => 'Uchembere Wabwino Clinic',
        'acronym' => 'uchembere',
        'is_pharmacy' => false,
    ]);

    $this->secondOrg = Organization::factory()->create([
        'name' => 'Bwaila District Hospital',
        'acronym' => 'Bwaila',
        'is_pharmacy' => false,
    ]);

    // Users
    $this->systemAdmin = User::factory()->create(['role_id' => 1]); // System Admin
    $this->orgAdmin = User::factory()->create(['role_id' => 2, 'organization_id' => $this->org->id]); // Org Admin
    $this->doctor = User::factory()->create(['role_id' => 3, 'organization_id' => $this->org->id]); // Doctor

    $this->mother = User::factory()->create([
        'role_id' => 4,
        'name' => 'Chikondi Phiri',
        'phone' => '0995936887',
        'organization_id' => $this->org->id,
    ]);

    $this->session = History::create([
        'mother_id' => $this->mother->id,
        'infant_number' => 1,
        'last_menstrual_cycle' => now()->subDays(28)->toDateString(),
        'organization_id' => $this->org->id,
    ]);

    $this->trimester = Trimester::firstOrCreate(['trimester' => 1]);
    $this->week = Week::firstOrCreate(['week' => 4, 'trimester_id' => $this->trimester->id]);
    $this->dayRange = DayRange::firstOrCreate(
        ['name' => 'All Day'],
        ['start_time' => '00:00:00', 'end_time' => '23:59:59']
    );
});

// ========================================================
// 1. ACRONYM FORMATTING TESTS
// ========================================================

it('formats messages with organization acronym correctly', function () {
    $text = 'Drink water every 3 hours';
    $formatted = SmsService::formatWithAcronym($text, 'uchembere');
    expect($formatted)->toBe('uchembere: Drink water every 3 hours');

    // Does not duplicate if already prefixed
    $alreadyPrefixed = 'uchembere: Drink water every 3 hours';
    expect(SmsService::formatWithAcronym($alreadyPrefixed, 'uchembere'))->toBe('uchembere: Drink water every 3 hours');

    // Returns original message if acronym is null or empty
    expect(SmsService::formatWithAcronym($text, null))->toBe($text);
    expect(SmsService::formatWithAcronym($text, ''))->toBe($text);
});

it('formats acronym into automated scheduled check-messages tips', function () {
    Queue::fake();

    $weekData = $this->session->calculate_weekv2();
    $weekNum = (int) $weekData['weeks'];
    $dayNum = (int) $weekData['days'];

    $tip = Tip::create([
        'tip' => 'Eat nutritious vegetables daily.',
        'week_id' => $weekNum,
        'day_id' => $dayNum,
        'day_range_id' => $this->dayRange->id,
        'organization_id' => $this->org->id,
        'status' => Tip::STATUS_APPROVED,
    ]);

    $this->artisan('app:check-messages')->assertSuccessful();

    Queue::assertPushed(SendSmsTipJob::class, function ($job) {
        return str_starts_with($job->message, 'uchembere: Eat nutritious vegetables daily.');
    });
});

// ========================================================
// 2. SMS SETTINGS LIVEWIRE TESTS
// ========================================================

it('allows system admin to view all organizations and edit their acronyms in SMS Settings', function () {
    Livewire::actingAs($this->systemAdmin)
        ->test(SmsSettingsLivewire::class)
        ->assertSee('Uchembere Wabwino Clinic')
        ->assertSee('uchembere')
        ->call('editOrgAcronym', $this->secondOrg->id)
        ->assertSet('editingOrgId', $this->secondOrg->id)
        ->assertSet('acronym', 'Bwaila')
        ->set('acronym', 'BHC')
        ->call('saveOrgAcronym')
        ->assertHasNoErrors()
        ->assertDispatched('alert');

    expect($this->secondOrg->fresh()->acronym)->toBe('BHC');
});

it('allows organization admin to update their own organization acronym in SMS Settings', function () {
    Livewire::actingAs($this->orgAdmin)
        ->test(SmsSettingsLivewire::class)
        ->assertSee('Uchembere Wabwino Clinic')
        ->assertSet('acronym', 'uchembere')
        ->set('acronym', 'UW-Clinic')
        ->call('saveMyOrgAcronym')
        ->assertHasNoErrors()
        ->assertDispatched('alert');

    expect($this->org->fresh()->acronym)->toBe('UW-Clinic');
});

it('prevents regular mothers from accessing SMS Settings', function () {
    Livewire::actingAs($this->mother)
        ->test(SmsSettingsLivewire::class)
        ->assertForbidden();
});

// ========================================================
// 3. ENHANCED SESSION HISTORY LIVEWIRE TESTS
// ========================================================

it('displays sent, unsent, and failed messages with organization acronym in Session History', function () {
    $tip1 = Tip::create([
        'tip' => 'Visit clinic for checkup.',
        'week_id' => 4,
        'day_id' => 1,
        'day_range_id' => $this->dayRange->id,
        'organization_id' => $this->org->id,
        'status' => Tip::STATUS_APPROVED,
    ]);

    $sentMsg = MessageHistory::create([
        'tip_id' => $tip1->id,
        'week_id' => 4,
        'day_id' => 1,
        'day_range_id' => $this->dayRange->id,
        'mother_id' => $this->mother->id,
        'history_id' => $this->session->id,
        'message_status' => 'sent',
        'organization_id' => $this->org->id,
        'api_response' => ['status' => 'Success', 'timestamp' => '2026-11-09 10:00:00'],
    ]);

    $unsentMsg = MessageHistory::create([
        'tip_id' => $tip1->id,
        'week_id' => 4,
        'day_id' => 1,
        'day_range_id' => $this->dayRange->id,
        'mother_id' => $this->mother->id,
        'history_id' => $this->session->id,
        'message_status' => 'unsent',
        'organization_id' => $this->org->id,
    ]);

    Livewire::actingAs($this->orgAdmin)
        ->test(MotherSessionHistoryLivewire::class, [
            'mother_id' => $this->mother->id,
            'session_id' => $this->session->id,
        ])
        ->assertSee('Session #1 SMS History')
        ->assertSee('Visit clinic for checkup.')
        ->assertSee('uchembere')
        ->assertSee('SENT')
        ->assertSee('UNSENT');
});

it('filters messages by organization in Session History', function () {
    $tip1 = Tip::create([
        'tip' => 'Org 1 Tip',
        'week_id' => 4,
        'day_id' => 1,
        'day_range_id' => $this->dayRange->id,
        'organization_id' => $this->org->id,
        'status' => Tip::STATUS_APPROVED,
    ]);

    $msgOrg1 = MessageHistory::create([
        'tip_id' => $tip1->id,
        'week_id' => 4,
        'day_id' => 1,
        'day_range_id' => $this->dayRange->id,
        'mother_id' => $this->mother->id,
        'history_id' => $this->session->id,
        'message_status' => 'sent',
        'organization_id' => $this->org->id,
    ]);

    $msgOrg2 = MessageHistory::create([
        'tip_id' => $tip1->id,
        'week_id' => 4,
        'day_id' => 1,
        'day_range_id' => $this->dayRange->id,
        'mother_id' => $this->mother->id,
        'history_id' => $this->session->id,
        'message_status' => 'sent',
        'organization_id' => $this->secondOrg->id,
    ]);

    Livewire::actingAs($this->systemAdmin)
        ->test(MotherSessionHistoryLivewire::class, [
            'mother_id' => $this->mother->id,
            'session_id' => $this->session->id,
        ])
        ->set('organizationFilter', $this->secondOrg->id)
        ->assertSee('Bwaila')
        ->set('organizationFilter', $this->org->id)
        ->assertSee('uchembere');
});

it('allows resending a message and formats with organization acronym', function () {
    $tip = Tip::create([
        'tip' => 'Hydrate frequently.',
        'week_id' => 4,
        'day_id' => 1,
        'day_range_id' => $this->dayRange->id,
        'organization_id' => $this->org->id,
        'status' => Tip::STATUS_APPROVED,
    ]);

    $msg = MessageHistory::create([
        'tip_id' => $tip->id,
        'week_id' => 4,
        'day_id' => 1,
        'day_range_id' => $this->dayRange->id,
        'mother_id' => $this->mother->id,
        'history_id' => $this->session->id,
        'message_status' => 'failed',
        'organization_id' => $this->org->id,
    ]);

    $mockSmsService = Mockery::mock(SmsService::class);
    $mockSmsService->shouldReceive('sendSmsGeneric')
        ->once()
        ->withArgs(function ($historyModel, $phone, $message) {
            return $phone === '0995936887'
                && str_starts_with($message, 'uchembere: Hydrate frequently.');
        })
        ->andReturn(true);

    app()->instance(SmsService::class, $mockSmsService);

    Livewire::actingAs($this->orgAdmin)
        ->test(MotherSessionHistoryLivewire::class, [
            'mother_id' => $this->mother->id,
            'session_id' => $this->session->id,
        ])
        ->call('resend', $msg->id)
        ->assertDispatched('alert');

    expect($msg->fresh()->message_status)->toBe('sent');
});

it('allows sending modified text based on tip or custom SMS with multi-mother selection and acronym prefix', function () {
    $secondMother = User::factory()->create([
        'role_id' => 4,
        'name' => 'Tadala Banda',
        'phone' => '0888123456',
        'organization_id' => $this->org->id,
    ]);

    $tip = Tip::create([
        'tip' => 'Rest on your left side to improve blood flow.',
        'week_id' => 4,
        'day_id' => 1,
        'day_range_id' => $this->dayRange->id,
        'organization_id' => $this->org->id,
        'status' => Tip::STATUS_APPROVED,
    ]);

    $msg = MessageHistory::create([
        'tip_id' => $tip->id,
        'week_id' => 4,
        'day_id' => 1,
        'day_range_id' => $this->dayRange->id,
        'mother_id' => $this->mother->id,
        'history_id' => $this->session->id,
        'message_status' => 'sent',
        'organization_id' => $this->org->id,
    ]);

    $mockSmsService = Mockery::mock(SmsService::class);
    $mockSmsService->shouldReceive('sendSmsGeneric')
        ->twice() // 2 mothers selected
        ->withArgs(function ($historyModel, $phone, $message) {
            return in_array($phone, ['0995936887', '0888123456'])
                && str_starts_with($message, 'uchembere: Modified: Rest on your left side.');
        })
        ->andReturn(true);

    app()->instance(SmsService::class, $mockSmsService);

    Livewire::actingAs($this->systemAdmin)
        ->test(MotherSessionHistoryLivewire::class, [
            'mother_id' => $this->mother->id,
            'session_id' => $this->session->id,
        ])
        ->call('openModifyModal', $msg->id)
        ->assertSet('sendModal', true)
        ->assertSet('isCustom', false)
        ->assertSet('selectedMotherIds', [$this->mother->id])
        ->set('messageBody', 'Modified: Rest on your left side.')
        ->call('toggleMotherSelection', $secondMother->id)
        ->assertSet('selectedMotherIds', [$this->mother->id, $secondMother->id])
        ->set('selectedOrgId', $this->org->id)
        ->call('sendModifiedSms')
        ->assertHasNoErrors()
        ->assertSet('sendModal', false)
        ->assertDispatched('alert');

    // Both mothers received a new message history record
    expect(MessageHistory::where('mother_id', $this->mother->id)->count())->toBeGreaterThan(1);
    expect(MessageHistory::where('mother_id', $secondMother->id)->count())->toBe(1);
});
