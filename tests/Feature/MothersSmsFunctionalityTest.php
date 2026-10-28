<?php

use AfricasTalking\SDK\AfricasTalking;
use App\Jobs\SendSmsTipJob;
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
use Carbon\Carbon;
use Illuminate\Support\Facades\Queue;
use Livewire\Livewire;

beforeEach(function () {
    $this->systemAdminRole = Role::firstOrCreate(['name' => 'system-admin']);
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
        'name' => 'Area 25 Health Centre',
        'email' => 'area25@healthcentre.mw',
        'phone' => '0999111222',
        'district_id' => $this->district->id,
        'area_id' => $this->area->id,
        'is_pharmacy' => false,
        'owner_id' => $this->systemAdmin->id,
    ]);

    $this->mother = User::factory()->create([
        'name' => 'Chifuniro Phiri',
        'email' => 'chifuniro@example.com',
        'phone' => '0999888777',
        'role_id' => $this->motherRole->id,
        'organization_id' => $this->organization->id,
        'organization_verify' => 'verified',
        'district_id' => $this->district->id,
        'area_id' => $this->area->id,
        'is_active' => true,
    ]);
});

it('formats Malawian phone numbers correctly for Africa Talking SMS gateway', function () {
    $smsService = new SmsService;

    // Local 099... format -> +26599...
    expect($smsService->formatPhoneNumber('0999888777'))->toBe('+265999888777');
    expect($smsService->formatPhoneNumber('0888123456'))->toBe('+265888123456');

    // Already with international plus
    expect($smsService->formatPhoneNumber('+265999888777'))->toBe('+265999888777');

    // Without leading zero or plus
    expect($smsService->formatPhoneNumber('265999888777'))->toBe('+265999888777');
});

it('dispatches queued SMS jobs for mothers when check-messages runs during active time range', function () {
    Queue::fake();

    // 14 days ago is Week 2, Day 14
    $lmpDate = Carbon::now()->subDays(14)->toDateString();

    $session = History::create([
        'mother_id' => $this->mother->id,
        'infant_number' => 1,
        'last_menstrual_cycle' => $lmpDate,
        'organization_id' => $this->organization->id,
    ]);

    $weekData = $session->calculate_weekv2();
    $weekNum = (int) $weekData['weeks'];
    $dayNum = (int) $weekData['days'];

    // Setup day range covering current time
    $dayRange = DayRange::create([
        'name' => 'All Day Window',
        'start_time' => '00:00:00',
        'end_time' => '23:59:59',
    ]);

    $day = Day::firstOrCreate(['day_number' => $dayNum]);
    $trimester = Trimester::firstOrCreate(['trimester' => 1]);
    $week = Week::firstOrCreate(['week' => $weekNum, 'trimester_id' => $trimester->id]);

    $tip = Tip::create([
        'tip' => 'Ensure you take your iron and folic acid supplements today.',
        'week_id' => $weekNum,
        'day_id' => $dayNum,
        'day_range_id' => $dayRange->id,
        'organization_id' => $this->organization->id,
        'status' => Tip::STATUS_APPROVED,
    ]);

    $this->artisan('app:check-messages')
        ->assertSuccessful();

    // Verify SMS Job was dispatched for this mother
    Queue::assertPushed(SendSmsTipJob::class, function ($job) use ($tip) {
        return $job->phoneNumber === $this->mother->phone &&
               $job->message === $tip->tip;
    });

    // Verify MessageHistory was created
    $messageHistory = MessageHistory::where('mother_id', $this->mother->id)
        ->where('tip_id', $tip->id)
        ->first();

    expect($messageHistory)->not->toBeNull();
    expect($messageHistory->week_id)->toBe($weekNum);
    expect($messageHistory->day_id)->toBe($dayNum);
});

it('skips dispatching SMS when mother has no phone number registered', function () {
    Queue::fake();

    $motherNoPhone = User::factory()->create([
        'name' => 'Mother Without Phone',
        'email' => 'nophone@example.com',
        'phone' => null,
        'role_id' => $this->motherRole->id,
        'organization_id' => $this->organization->id,
        'organization_verify' => 'verified',
        'is_active' => true,
    ]);

    $lmpDate = Carbon::now()->subDays(14)->toDateString();

    $session = History::create([
        'mother_id' => $motherNoPhone->id,
        'infant_number' => 1,
        'last_menstrual_cycle' => $lmpDate,
        'organization_id' => $this->organization->id,
    ]);

    $weekData = $session->calculate_weekv2();
    $weekNum = (int) $weekData['weeks'];
    $dayNum = (int) $weekData['days'];

    $dayRange = DayRange::create([
        'name' => 'All Day Window 2',
        'start_time' => '00:00:00',
        'end_time' => '23:59:59',
    ]);

    $day = Day::firstOrCreate(['day_number' => $dayNum]);
    $trimester = Trimester::firstOrCreate(['trimester' => 1]);
    $week = Week::firstOrCreate(['week' => $weekNum, 'trimester_id' => $trimester->id]);

    $tip = Tip::create([
        'tip' => 'Healthy eating is vital for your baby.',
        'week_id' => $weekNum,
        'day_id' => $dayNum,
        'day_range_id' => $dayRange->id,
        'organization_id' => $this->organization->id,
        'status' => Tip::STATUS_APPROVED,
    ]);

    $this->artisan('app:check-messages')
        ->assertSuccessful();

    // Verify no job was pushed for this mother
    Queue::assertNotPushed(SendSmsTipJob::class, function ($job) use ($motherNoPhone) {
        return $job->messageHistory->mother_id === $motherNoPhone->id;
    });
});

it('updates message history status to sent when SendSmsTipJob succeeds', function () {
    $dayRange = DayRange::create(['name' => 'Morning', 'start_time' => '08:00', 'end_time' => '12:00']);
    $day = Day::firstOrCreate(['day_number' => 1]);
    $trimester = Trimester::firstOrCreate(['trimester' => 1]);
    $week = Week::firstOrCreate(['week' => 1, 'trimester_id' => $trimester->id]);

    $session = History::create([
        'mother_id' => $this->mother->id,
        'infant_number' => 1,
        'last_menstrual_cycle' => now()->subWeeks(4)->toDateString(),
        'organization_id' => $this->organization->id,
    ]);

    $tip = Tip::create([
        'tip' => 'Hydrate well with clean safe drinking water.',
        'week_id' => $week->id,
        'day_id' => $day->id,
        'day_range_id' => $dayRange->id,
        'organization_id' => $this->organization->id,
        'status' => Tip::STATUS_APPROVED,
    ]);

    $messageHistory = MessageHistory::create([
        'tip_id' => $tip->id,
        'week_id' => $week->id,
        'day_id' => $day->id,
        'day_range_id' => $dayRange->id,
        'mother_id' => $this->mother->id,
        'history_id' => $session->id,
        'message_status' => 'unsent',
    ]);

    // Mock SmsService to return success
    $mockSmsService = Mockery::mock(SmsService::class);
    $mockSmsService->shouldReceive('sendSms')
        ->once()
        ->andReturnUsing(function ($model, $phone, $msg) {
            $model->update(['message_status' => 'sent']);

            return true;
        });

    $job = new SendSmsTipJob($messageHistory, $this->mother->phone, $tip->tip);
    $job->handle($mockSmsService);

    $messageHistory->refresh();
    expect($messageHistory->message_status)->toBe('sent');
});

it('allows clinical staff to resend failed SMS tips to a mother from the UI', function () {
    $dayRange = DayRange::create(['name' => 'Afternoon', 'start_time' => '12:00', 'end_time' => '16:00']);
    $day = Day::firstOrCreate(['day_number' => 2]);
    $trimester = Trimester::firstOrCreate(['trimester' => 1]);
    $week = Week::firstOrCreate(['week' => 2, 'trimester_id' => $trimester->id]);

    $session = History::create([
        'mother_id' => $this->mother->id,
        'infant_number' => 1,
        'last_menstrual_cycle' => now()->subWeeks(5)->toDateString(),
        'organization_id' => $this->organization->id,
    ]);

    $tip = Tip::create([
        'tip' => 'Attend your scheduled antenatal consultation.',
        'week_id' => $week->id,
        'day_id' => $day->id,
        'day_range_id' => $dayRange->id,
        'organization_id' => $this->organization->id,
        'status' => Tip::STATUS_APPROVED,
    ]);

    $messageHistory = MessageHistory::create([
        'tip_id' => $tip->id,
        'week_id' => $week->id,
        'day_id' => $day->id,
        'day_range_id' => $dayRange->id,
        'mother_id' => $this->mother->id,
        'history_id' => $session->id,
        'message_status' => 'Failed',
    ]);

    // Mock SmsService to succeed upon resend
    $mockSmsService = Mockery::mock(SmsService::class);
    $mockSmsService->shouldReceive('sendSmsGeneric')
        ->once()
        ->andReturn(true);

    app()->instance(SmsService::class, $mockSmsService);

    Livewire::actingAs($this->systemAdmin)
        ->test(MotherLivewire::class, ['mother_id' => $this->mother->id])
        ->set('activeTab', 'messages')
        ->call('resendTipSms', $messageHistory->id);

    $messageHistory->refresh();
    expect($messageHistory->message_status)->toBe('Sent');
});

it('allows clinical staff to send custom instant SMS to mother and logs delivery record', function () {
    $session = History::create([
        'mother_id' => $this->mother->id,
        'infant_number' => 1,
        'last_menstrual_cycle' => now()->subWeeks(8)->toDateString(),
        'organization_id' => $this->organization->id,
    ]);

    $customText = 'Urgent reminder: Your lab test results are ready for pickup at Area 25 clinic.';

    $mockSmsService = Mockery::mock(SmsService::class);
    $mockSmsService->shouldReceive('sendSmsGeneric')
        ->once()
        ->withArgs(function ($historyModel, $phone, $message) use ($customText) {
            return $phone === $this->mother->phone && $message === $customText;
        })
        ->andReturn(true);

    app()->instance(SmsService::class, $mockSmsService);

    Livewire::actingAs($this->systemAdmin)
        ->test(MotherLivewire::class, ['mother_id' => $this->mother->id])
        ->call('openCustomSmsModal')
        ->assertSet('customSmsModal', true)
        ->set('customMessageText', $customText)
        ->call('sendCustomSms')
        ->assertHasNoErrors()
        ->assertSet('customSmsModal', false);

    $loggedMsg = MessageHistory::where('mother_id', $this->mother->id)->latest()->first();
    expect($loggedMsg)->not->toBeNull();
    expect($loggedMsg->message_status)->toBe('Sent');
    expect($loggedMsg->tip->tip)->toBe($customText);
});

it('gracefully handles and logs gateway exceptions without crashing the application', function () {
    $dayRange = DayRange::create(['name' => 'General', 'start_time' => '08:00', 'end_time' => '17:00']);
    $day = Day::firstOrCreate(['day_number' => 1]);
    $trimester = Trimester::firstOrCreate(['trimester' => 1]);
    $week = Week::firstOrCreate(['week' => 1, 'trimester_id' => $trimester->id]);

    $session = History::create([
        'mother_id' => $this->mother->id,
        'infant_number' => 1,
        'last_menstrual_cycle' => now()->subWeeks(8)->toDateString(),
        'organization_id' => $this->organization->id,
    ]);

    $tip = Tip::create([
        'tip' => 'Test tip content',
        'week_id' => $week->id,
        'day_id' => $day->id,
        'day_range_id' => $dayRange->id,
        'organization_id' => $this->organization->id,
        'status' => Tip::STATUS_APPROVED,
    ]);

    $messageHistory = MessageHistory::create([
        'tip_id' => $tip->id,
        'week_id' => $week->id,
        'day_id' => $day->id,
        'day_range_id' => $dayRange->id,
        'mother_id' => $this->mother->id,
        'history_id' => $session->id,
        'message_status' => 'unsent',
    ]);

    $mockAt = Mockery::mock(AfricasTalking::class);
    $mockSms = Mockery::mock();
    $mockSms->shouldReceive('send')->andThrow(new Exception('Connection timed out'));
    $mockAt->shouldReceive('sms')->andReturn($mockSms);

    $smsService = new SmsService;
    $ref = new ReflectionProperty($smsService, 'at');
    $ref->setValue($smsService, $mockAt);

    $result = $smsService->sendSmsGeneric($messageHistory, $this->mother->phone, 'Test SMS');

    expect($result)->toBeFalse();
    $messageHistory->refresh();
    expect($messageHistory->message_status)->toBe('failed');
    expect($messageHistory->api_response)->toBeArray();
    expect($messageHistory->api_response)->toHaveKey('error');
    expect($messageHistory->api_response['error'])->toBe('Connection timed out');
});
