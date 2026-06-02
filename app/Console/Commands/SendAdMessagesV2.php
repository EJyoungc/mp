<?php

namespace App\Console\Commands;

use App\Jobs\SendSmsAdJob;
use App\Models\AdHistory;
use App\Models\PharmacyAd;
use App\Models\User;
use Illuminate\Console\Command;

class SendAdMessagesV2 extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ads:send-v2';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send active pharmacy ads to mothers in their respective areas based on custom schedules';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting Ad delivery V2 process...');

        $activeAds = PharmacyAd::where('is_active', true)
            ->with('organization')
            ->get();

        if ($activeAds->isEmpty()) {
            $this->info('No active ads found.');

            return;
        }

        $sentToMothersInThisRun = [];

        foreach ($activeAds as $ad) {
            $organization = $ad->organization;

            if (! $organization || ! $organization->area_id) {
                $this->warn("Skipping Ad ID {$ad->id}: Organization has no area assigned.");

                continue;
            }

            // Find mothers in the same area as the pharmacy
            $mothers = User::where('role_id', 4) // Assuming 4 is mother role
                ->where('area_id', $organization->area_id)
                ->get();

            if ($mothers->isEmpty()) {
                $this->info("No mothers found in Area ID {$organization->area_id} for Ad ID {$ad->id}.");

                continue;
            }

            foreach ($mothers as $mother) {
                // Skip if we already sent an ad to this mother in THIS run of the command
                if (isset($sentToMothersInThisRun[$mother->id])) {
                    continue;
                }

                // First, check if we already sent THIS specific ad to this mother today
                $alreadySentToday = AdHistory::where('mother_id', $mother->id)
                    ->where('pharmacy_ad_id', $ad->id)
                    ->whereDate('created_at', now()->today())
                    ->exists();

                if ($alreadySentToday) {
                    continue;
                }

                // Check based on schedule_type
                $scheduleType = $ad->schedule_type ?? 'daily';
                $scheduleLimit = $ad->schedule_limit ?? 1;

                if ($scheduleType === 'weekly') {
                    $sentThisWeek = AdHistory::where('mother_id', $mother->id)
                        ->where('pharmacy_ad_id', $ad->id)
                        ->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])
                        ->count();

                    if ($sentThisWeek >= $scheduleLimit) {
                        continue;
                    }
                } elseif ($scheduleType === 'monthly') {
                    $sentThisMonth = AdHistory::where('mother_id', $mother->id)
                        ->where('pharmacy_ad_id', $ad->id)
                        ->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])
                        ->count();

                    if ($sentThisMonth >= $scheduleLimit) {
                        continue;
                    }
                } else {
                    // Default/daily checks
                    $sentToday = AdHistory::where('mother_id', $mother->id)
                        ->where('pharmacy_ad_id', $ad->id)
                        ->whereDate('created_at', now()->today())
                        ->count();

                    if ($sentToday >= $scheduleLimit) {
                        continue;
                    }
                }

                $fullMessage = "PROMO: {$ad->product_name} {$ad->ad_message}\n- Available at: {$organization->name}";

                $adHistory = AdHistory::create([
                    'mother_id' => $mother->id,
                    'pharmacy_ad_id' => $ad->id,
                    'organization_id' => $organization->id,
                    'message' => $fullMessage,
                    'status' => 'pending',
                ]);

                SendSmsAdJob::dispatch($adHistory, $mother->phone, $fullMessage);

                $ad->increment('total_sent');
                $sentToMothersInThisRun[$mother->id] = true;
            }

            $this->info("Dispatched ads for Ad ID {$ad->id} to {$mothers->count()} mothers in Area: ".(optional($organization->area)->name ?? $organization->area_id));
        }

        $this->info('Ad delivery V2 process completed.');
    }
}
