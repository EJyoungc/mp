<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\History;
use App\Models\MessageHistory;
use App\Models\Tip;

use Livewire\Component;
use AfricasTalking\SDK\AfricasTalking;
use Carbon\Carbon;

class CheckMessages extends Command
{

    private $username   = "DumiKaliati";
    private $apiKey     = "atsk_1b93e9047e28c4f2d33a74bc73e65782aa5bd04e8390cae5cb491861b37c05ddbbb59876";

    protected $signature = 'app:check-messages';
    protected $description = 'Check user histories and send scheduled tips via SMS';

    public function handle()
    {
        $this->test(); // Run your message check logic
    }

    public function test()
    {
        $this->info('✅ running >> ' . now());

        $histories = History::all();
        $this->info('Total histories: ' . $histories->count());

        foreach ($histories as $history) {
            $weekdata = $history->calculate_weekv2();
            $this->info("History ID: {$history->id}, Mother ID: {$history->mother_id}, Week: {$weekdata['weeks']}, Day: {$weekdata['days']}");

            $tips = Tip::where('week_id', (int)$weekdata['weeks'])
                ->where('day_id', (int)$weekdata['days'])
                ->get();

            $this->info("Found " . $tips->count() . " tips for week {$weekdata['weeks']} and day {$weekdata['days']}");

            $now = Carbon::now("GMT+2");

            foreach ($tips as $t) {
                $messagehistory = MessageHistory::where('tip_id', $t->id)
                    ->where('week_id', $weekdata['weeks'])
                    ->where('day_range_id', $t->day_range_id)
                    ->where('mother_id', $history->mother_id)
                    ->where('history_id', $history->id)
                    ->first();

                if (!$messagehistory) {
                    $messagehistory = MessageHistory::create([
                        'tip_id' => $t->id,
                        'week_id' => $weekdata['weeks'],
                        'day_id' => $t->day_id,
                        'day_range_id' => $t->day_range_id,
                        'mother_id' => $history->mother_id,
                        'history_id' => $history->id,
                        'message_status' => 'unsent',
                    ]);

                    $this->info("Created new MessageHistory ID: {$messagehistory->id}");
                } else {
                    $this->info("Existing MessageHistory ID: {$messagehistory->id}, Status: {$messagehistory->message_status}");
                }

                $isTime = $now->between($t->day_range->start_time, $t->day_range->end_time);
                $this->info("Now: {$now}, Tip Start: {$t->day_range->start_time}, Tip End: {$t->day_range->end_time}, Within Range: " . ($isTime ? 'Yes' : 'No'));

                if ($messagehistory->message_status === 'unsent' && $isTime) {
                    $this->info("Sending message to {$history->mother->name} ({$history->mother->phone}): {$t->tip}");
                    $this->sendMessage(
                        $messagehistory,
                        $history->mother->name,
                        $t->tip,
                        $history->mother->phone
                    );
                }
            }
        }

        $this->info('✅ CheckMessages command finished at ' . now());
    }

    private function sendMessage($messagehistory, $motherName, $tip, $mobile_number)
    {
        $AT = new AfricasTalking($this->username, $this->apiKey);
        $sms = $AT->sms();

        $result = $sms->send([
            'to'      => $this->formatPhoneNumber($mobile_number),
            'message' => $tip,
            'from'    => 'Maasms',
        ]);

        $this->info('Raw AfricasTalking response: ' . json_encode($result));

        $status = $result['data']['SMSMessageData']['Recipients'][0]['status'] ?? null;

        if ($status === "Success") {
            $messagehistory->update(['message_status' => 'sent']);
            $this->info('Message sent to ' . $motherName . ': ' . $tip);
        } else {
            $messagehistory->update(['message_status' => 'failed']);
            $this->error('Message failed for ' . $motherName);
        }
    }

    private function formatPhoneNumber($phoneNumber)
    {
        if (substr($phoneNumber, 0, 1) === '0') {
            return '+265' . substr($phoneNumber, 1);
        }
        return $phoneNumber;
    }
}
