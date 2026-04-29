<?php

namespace App\Jobs;

use App\Models\AdHistory;
use App\Services\SmsService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendSmsAdJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;

    public $backoff = [30, 60, 120];

    public function __construct(
        public AdHistory $adHistory,
        public string $phoneNumber,
        public string $message
    ) {}

    public function handle(SmsService $smsService): void
    {
        $this->adHistory->refresh();

        if ($this->adHistory->status === 'sent') {
            return;
        }

        $success = $smsService->sendSmsGeneric($this->adHistory, $this->phoneNumber, $this->message);

        if (! $success) {
            $this->fail(new \Exception("Failed to send Ad SMS to {$this->phoneNumber} via Africa's Talking."));
        }
    }
}
