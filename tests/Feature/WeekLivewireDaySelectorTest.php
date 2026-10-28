<?php

use App\Jobs\SendSmsTipJob;
use App\Livewire\Trimesters\Weeks\WeekLivewire;
use App\Models\Day;
use App\Models\DayRange;
use App\Models\History;
use App\Models\MessageHistory;
use App\Models\Organization;
use App\Models\Tip;
use App\Models\Trimester;
use App\Models\User;
use App\Models\Week;
use Database\Seeders\DaySeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Support\Facades\Queue;
use Livewire\Livewire;

beforeEach(function () {
    $this->seed(RoleSeeder::class);
    $this->seed(DaySeeder::class);

    $this->org = Organization::factory()->create(['is_pharmacy' => false]);
    $this->doctor = User::factory()->create([
        'role_id' => 3, // Doctor
        'organization_id' => $this->org->id,
    ]);

    $this->trimester = Trimester::firstOrCreate(['trimester' => 1]);
    $this->week10 = Week::firstOrCreate([
        'week' => 10,
        'trimester_id' => $this->trimester->id,
    ]);

    $this->dayRange = DayRange::firstOrCreate(
        ['name' => 'All Day'],
        ['start_time' => '00:00:00', 'end_time' => '23:59:59']
    );
});

it('provides 7 days (1 to 7) in the day selector on Week 10 instead of 64 to 70', function () {
    $this->actingAs($this->doctor);

    Livewire::test(WeekLivewire::class, [
        'trimester_id' => $this->trimester->id,
        'week_id' => $this->week10->id,
    ])
        ->assertViewHas('days', function ($days) {
            // Must contain 7 days
            expect($days)->toHaveCount(7);
            $dayNumbers = $days->pluck('day_number')->toArray();
            expect($dayNumbers)->toEqual([1, 2, 3, 4, 5, 6, 7]);
            // Ensure no numbers like 64-70 are present
            expect($dayNumbers)->not->toContain(64);
            expect($dayNumbers)->not->toContain(67);
            expect($dayNumbers)->not->toContain(70);

            return true;
        })
        ->assertSee('Day 1')
        ->assertSee('Day 7')
        ->assertDontSee('Day 67');
});

it('saves a new tip with day 1 to 7 successfully in Week 10', function () {
    $this->actingAs($this->doctor);

    $day4 = Day::where('day_number', 4)->first();

    Livewire::test(WeekLivewire::class, [
        'trimester_id' => $this->trimester->id,
        'week_id' => $this->week10->id,
    ])
        ->call('create')
        ->set('tip', 'Remember to rest well and stay hydrated.')
        ->set('day', $day4->id)
        ->set('time', $this->dayRange->id)
        ->call('store')
        ->assertHasNoErrors()
        ->assertDispatched('alert');

    $savedTip = Tip::where('week_id', $this->week10->id)->latest()->first();
    expect($savedTip)->not->toBeNull();
    expect($savedTip->tip)->toBe('Remember to rest well and stay hydrated.');
    expect((int) $savedTip->day_id)->toBe($day4->id);
    expect($savedTip->status)->toBe(Tip::STATUS_APPROVED);
});

it('maps legacy cumulative day numbers like 67 to Day 4 when editing a tip in Week 10', function () {
    $this->actingAs($this->doctor);

    // Create a legacy tip that was saved with cumulative day 67 (which corresponds to Week 10, Day 4)
    $legacyTip = Tip::create([
        'tip' => 'Legacy tip created with day 67',
        'week_id' => $this->week10->id,
        'day_id' => 67,
        'day_range_id' => $this->dayRange->id,
        'organization_id' => $this->org->id,
        'status' => Tip::STATUS_APPROVED,
    ]);

    Livewire::test(WeekLivewire::class, [
        'trimester_id' => $this->trimester->id,
        'week_id' => $this->week10->id,
    ])
        ->call('create', $legacyTip->id)
        ->assertSet('day', 4); // Mapped from 67 to 4
});

it('dispatches SMS tip for Week 10 Day 4 mother when tip is configured with day 4', function () {
    Queue::fake();

    // 67 days LMP corresponds to Week 10, Day 4
    $mother = User::factory()->create([
        'role_id' => 4,
        'phone' => '0995936887',
        'organization_id' => $this->org->id,
    ]);

    $history = History::create([
        'mother_id' => $mother->id,
        'infant_number' => 1,
        'last_menstrual_cycle' => now()->subDays(67)->toDateString(),
        'organization_id' => $this->org->id,
    ]);

    $tip = Tip::create([
        'tip' => 'Baby is growing fingernails this week! Eat nutritious meals.',
        'week_id' => 10,
        'day_id' => 4,
        'day_range_id' => $this->dayRange->id,
        'organization_id' => $this->org->id,
        'status' => Tip::STATUS_APPROVED,
    ]);

    $this->artisan('app:check-messages')
        ->assertSuccessful();

    Queue::assertPushed(SendSmsTipJob::class, function ($job) use ($mother, $tip) {
        return $job->phoneNumber === $mother->phone
            && $job->message === $tip->tip;
    });

    $msg = MessageHistory::where('mother_id', $mother->id)->first();
    expect($msg)->not->toBeNull();
    expect($msg->tip_id)->toBe($tip->id);
});
