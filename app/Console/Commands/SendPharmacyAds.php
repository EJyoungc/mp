<?php

namespace App\Console\Commands;

use App\Models\AdHistory;
use App\Models\History;
use App\Models\PharmacyAd;
use App\Models\Setting;
use App\Models\User;
use App\Services\SmsService;
use Illuminate\Console\Command;

class SendPharmacyAds extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-pharmacy-ads';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send location-based pharmacy advertisements to mothers';

    /**
     * Execute the console command.
     */
    public function handle(SmsService $smsService)
    {
        $timezone = Setting::get('app_timezone', config('app.timezone'));
        $this->info('🚀 Starting Pharmacy Ad dispatch ('.$timezone.'): '.now($timezone));

        $activeAds = PharmacyAd::active()->with('organization')->get();

        if ($activeAds->isEmpty()) {
            $this->info('No active pharmacy ads found.');

            return;
        }

        // Process mothers who have an area assigned
        User::where('role_id', 4) // Mother role
            ->whereNotNull('area_id')
            ->chunk(100, function ($mothers) use ($activeAds, $smsService, $timezone) {
                foreach ($mothers as $mother) {
                    $history = History::where('mother_id', $mother->id)->latest()->first();
                    if (! $history) {
                        continue;
                    }

                    $weekData = $history->calculate_weekv2($timezone);
                    $currentWeek = (int) $weekData['weeks'];
                    $currentTrimester = $weekData['trimester_id'] ?? null;

                    foreach ($activeAds as $ad) {
                        // Check if ad is relevant for this trimester OR week range
                        $isRelevant = false;

                        if ($ad->trimester_id) {
                            if ($ad->trimester_id == $currentTrimester) {
                                $isRelevant = true;
                            }
                        } elseif ($ad->target_week_start && $ad->target_week_end) {
                            if ($currentWeek >= $ad->target_week_start && $currentWeek <= $ad->target_week_end) {
                                $isRelevant = true;
                            }
                        } else {
                            // If neither is set, it's a general ad
                            $isRelevant = true;
                        }

                        if (! $isRelevant) {
                            continue;
                        }

                        // Ensure ad has an organization with an area assigned
                        if (! $ad->organization || ! $ad->organization->area_id) {
                            continue;
                        }

                        // Area-based targeting: Check if mother is in the same area as the pharmacy organization
                        if ($mother->area_id == $ad->organization->area_id) {
                            $this->dispatchAd($mother, $ad, $smsService);
                        }
                    }
                }
            });

        $this->info('✅ Pharmacy Ad dispatch finished.');
    }

    private function dispatchAd($mother, $ad, $smsService)
    {
        // Check if ad already sent to this mother
        $alreadySent = AdHistory::where('mother_id', $mother->id)
            ->where('pharmacy_ad_id', $ad->id)
            ->where('status', 'sent')
            ->exists();

        if ($alreadySent) {
            return;
        }

        $message = "PROMO: {$ad->product_name}. {$ad->ad_message}";

        // Create Ad history record
        $adHistory = AdHistory::create([
            'mother_id' => $mother->id,
            'pharmacy_ad_id' => $ad->id,
            'organization_id' => $ad->organization_id,
            'message' => $message,
            'status' => 'pending',
        ]);

        $this->info("Sending Ad '{$ad->product_name}' to {$mother->name} (Area: ".($mother->area->name ?? 'Unknown').')');

        $success = $smsService->sendSmsGeneric($adHistory, $mother->phone, $message);

        if ($success) {
            $ad->increment('total_sent');
        }
    }
}
