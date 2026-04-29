<?php

namespace App\Console\Commands;

use App\Jobs\SendSmsTipJob;
use App\Models\History;
use App\Models\MessageHistory;
use App\Models\Setting;
use App\Models\Tip;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CheckMessages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-messages';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check user histories and dispatch scheduled tips via queued SMS jobs';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $timezone = Setting::get('app_timezone', config('app.timezone'));
        $this->info('🚀 Starting message check ('.$timezone.'): '.now($timezone));

        // Use chunking to handle large datasets efficiently
        History::with(['mother', 'day_range'])->chunk(100, function ($histories) use ($timezone) {
            foreach ($histories as $history) {
                $weekdata = $history->calculate_weekv2($timezone);

                $tips = Tip::with('day_range')
                    ->where('week_id', (int) $weekdata['weeks'])
                    ->where('day_id', (int) $weekdata['days'])
                    ->approved() // Only send approved tips
                    ->get();

                // Using dynamic timezone for consistency
                $now = Carbon::now($timezone);

                foreach ($tips as $tip) {
                    $this->processTip($history, $tip, $weekdata, $now);
                }
            }
        });

        $this->info('✅ CheckMessages command finished.');
    }

    /**
     * Process an individual tip for a history record.
     */
    private function processTip($history, $tip, $weekdata, $now)
    {
        // Ensure we have a valid day_range and start/end times
        if (! $tip->day_range) {
            Log::warning("Tip {$tip->id} is missing a day_range.");

            return;
        }

        $isTime = $now->between($tip->day_range->start_time, $tip->day_range->end_time);

        if (! $isTime) {
            return;
        }

        $messageHistory = MessageHistory::firstOrCreate(
            [
                'tip_id' => $tip->id,
                'week_id' => $weekdata['weeks'],
                'day_range_id' => $tip->day_range_id,
                'mother_id' => $history->mother_id,
                'history_id' => $history->id,
            ],
            [
                'day_id' => $tip->day_id,
                'message_status' => 'unsent',
            ]
        );

        if ($messageHistory->message_status !== 'unsent') {
            return;
        }

        if (empty($history->mother->phone)) {
            Log::warning("Mother {$history->mother_id} has no phone number. Skipping.");
            $this->error("Skipping Mother: {$history->mother->name} (No phone number)");

            return;
        }

        $this->info("Dispatching message for Mother: {$history->mother->name}");

        SendSmsTipJob::dispatch(
            $messageHistory,
            $history->mother->phone,
            $tip->tip
        );
    }
}
