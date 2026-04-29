<?php

namespace App\Console\Commands;

use App\Models\AdHistory;
use App\Models\History;
use App\Models\PharmacyAd;
use App\Models\Setting;
use App\Models\User;
use App\Services\SmsService;
use Illuminate\Console\Command;

class SendPharmacyAdsBroadcast extends Command
{
    protected $signature = 'app:send-pharmacy-ads-broadcast';

    protected $description = 'Send pharmacy ads to all mothers (no location filtering)';

    public function handle(SmsService $smsService)
    {
        $timezone = Setting::get('app_timezone', config('app.timezone'));
        $this->info('🚀 Starting Pharmacy Ad broadcast ('.$timezone.'): '.now($timezone));

        $activeAds = PharmacyAd::active()->get();

        if ($activeAds->isEmpty()) {
            $this->info('No active pharmacy ads found.');

            return;
        }

        // 🔥 Get ALL mothers
        User::where('role_id', 4)
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

                        // 🎯 Keep relevance logic
                        $isRelevant = false;

                        if ($ad->trimester_id) {
                            $isRelevant = $ad->trimester_id == $currentTrimester;
                        } elseif ($ad->target_week_start && $ad->target_week_end) {
                            $isRelevant = $currentWeek >= $ad->target_week_start &&
                                          $currentWeek <= $ad->target_week_end;
                        } else {
                            $isRelevant = true;
                        }

                        if (! $isRelevant) {
                            continue;
                        }

                        // 🚀 Direct send
                        $this->dispatchAd($mother, $ad, $smsService);
                    }
                }
            });

        $this->info('✅ Pharmacy Ad broadcast finished.');
    }

    private function dispatchAd($mother, $ad, $smsService)
    {
        $alreadySent = AdHistory::where('mother_id', $mother->id)
            ->where('pharmacy_ad_id', $ad->id)
            ->where('status', 'sent')
            ->exists();

        if ($alreadySent) {
            return;
        }

        $message = "PROMO: {$ad->product_name}. {$ad->ad_message}";

        $adHistory = AdHistory::create([
            'mother_id' => $mother->id,
            'pharmacy_ad_id' => $ad->id,
            'organization_id' => $ad->organization_id,
            'message' => $message,
            'status' => 'pending',
        ]);

        $this->info("Sending '{$ad->product_name}' to {$mother->name} [Broadcast Mode]");

        $success = $smsService->sendSmsGeneric($adHistory, $mother->phone, $message);

        if ($success) {
            $ad->increment('total_sent');
        }
    }
}
