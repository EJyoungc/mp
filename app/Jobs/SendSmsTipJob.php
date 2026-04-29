<?php

namespace App\Jobs;

use App\Models\MessageHistory;
use App\Services\SmsService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendSmsTipJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 3;

    /**
     * The number of seconds to wait before retrying the job.
     *
     * @var int
     */
    public $backoff = [30, 60, 120];

    /**
     * Create a new job instance.
     */
    public function __construct(
        public MessageHistory $messageHistory,
        public string $phoneNumber,
        public string $message
    ) {}

    /**
     * Execute the job.
     */
    public function handle(SmsService $smsService): void
    {
        // Refresh the model to get the latest status from the DB
        $this->messageHistory->refresh();

        if ($this->messageHistory->message_status === 'sent') {
            return;
        }

        $success = $smsService->sendSms($this->messageHistory, $this->phoneNumber, $this->message);

        if (! $success) {
            $this->fail(new \Exception("Failed to send SMS to {$this->phoneNumber} via Africa's Talking."));
        }
    }
}
