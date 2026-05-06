<?php

use App\Models\AdHistory;
use App\Models\History;
use App\Models\MessageHistory;
use App\Models\Organization;
use App\Models\PharmacyAd;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('automatically deletes residual data when a mother is deleted', function () {
    // Setup
    $motherRole = Role::create(['name' => 'mother']);
    $org = Organization::factory()->create();
    $mother = User::factory()->create([
        'role_id' => $motherRole->id,
        'organization_id' => $org->id,
    ]);

    // Create residual data
    $history = History::create([
        'mother_id' => $mother->id,
        'infant_number' => 1,
        'last_menstrual_cycle' => now()->subWeeks(10)->format('Y-m-d'),
        'organization_id' => $org->id,
    ]);

    $messageHistory = MessageHistory::create([
        'tip_id' => 1,
        'week_id' => 1,
        'day_range_id' => 1,
        'day_id' => 1,
        'mother_id' => $mother->id,
        'history_id' => $history->id,
        'message_status' => 'sent',
        'organization_id' => $org->id,
    ]);

    $pharmacyAd = PharmacyAd::create([
        'product_name' => 'Test Product',
        'ad_message' => 'Test Message',
        'organization_id' => $org->id,
        'is_active' => true,
    ]);
    $adHistory = AdHistory::create([
        'mother_id' => $mother->id,
        'pharmacy_ad_id' => $pharmacyAd->id,
        'organization_id' => $org->id,
        'message' => 'Test Ad',
        'status' => 'sent',
    ]);

    // Verify existence
    $this->assertDatabaseHas('users', ['id' => $mother->id]);
    $this->assertDatabaseHas('histories', ['id' => $history->id]);
    $this->assertDatabaseHas('message_histories', ['id' => $messageHistory->id]);
    $this->assertDatabaseHas('ad_histories', ['id' => $adHistory->id]);

    // Action: Delete the mother
    $mother->delete();

    // Verification: Everything should be gone
    $this->assertDatabaseMissing('users', ['id' => $mother->id]);
    $this->assertDatabaseMissing('histories', ['id' => $history->id]);
    $this->assertDatabaseMissing('message_histories', ['id' => $messageHistory->id]);
    $this->assertDatabaseMissing('ad_histories', ['id' => $adHistory->id]);
});
