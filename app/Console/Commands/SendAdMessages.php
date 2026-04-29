<?php

namespace App\Console\Commands;

use App\Jobs\SendSmsAdJob;
use App\Models\AdHistory;
use App\Models\PharmacyAd;
use App\Models\User;
use Illuminate\Console\Command;

class SendAdMessages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ads:send';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send active pharmacy ads to mothers in their respective areas';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting Ad delivery process...');

        $activeAds = PharmacyAd::where('is_active', true)
            ->with('organization')
            ->get();

        if ($activeAds->isEmpty()) {
            $this->info('No active ads found.');

            return;
        }

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
                // Check if we already sent this specific ad to this mother recently (e.g., today)
                $alreadySent = AdHistory::where('mother_id', $mother->id)
                    ->where('pharmacy_ad_id', $ad->id)
                    ->whereDate('created_at', now()->today())
                    ->exists();

                if ($alreadySent) {
                    continue;
                }

                $adHistory = AdHistory::create([
                    'mother_id' => $mother->id,
                    'pharmacy_ad_id' => $ad->id,
                    'organization_id' => $organization->id,
                    'message' => $ad->ad_message,
                    'status' => 'pending',
                ]);

                SendSmsAdJob::dispatch($adHistory, $mother->phone, $ad->ad_message);

                $ad->increment('total_sent');
            }

            $this->info("Dispatched ads for Ad ID {$ad->id} to {$mothers->count()} mothers in Area: ".(optional($organization->area)->name ?? $organization->area_id));
        }

        $this->info('Ad delivery process completed.');
    }
}
