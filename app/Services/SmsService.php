<?php

namespace App\Services;

use AfricasTalking\SDK\AfricasTalking;
use App\Models\MessageHistory;
use Illuminate\Support\Facades\Log;

class SmsService
{
    protected AfricasTalking $at;

    protected string $from;

    public function __construct()
    {
        $username = config('services.africastalking.username');
        $apiKey = config('services.africastalking.api_key');
        $this->from = config('services.africastalking.from', 'Maasms');

        $this->at = new AfricasTalking($username, $apiKey);
    }

    /**
     * Send an SMS and update any message history model (MessageHistory or AdHistory).
     */
    public function sendSmsGeneric($historyModel, string $phoneNumber, string $message): bool
    {
        $sms = $this->at->sms();
        $now = now()->toDateTimeString();

        try {
            $formattedPhone = $this->formatPhoneNumber($phoneNumber);

            $result = $sms->send([
                'to' => $formattedPhone,
                'message' => $message,
                'from' => $this->from,
            ]);

            // Log raw response for debugging
            Log::debug("Africa's Talking Raw Response (Generic):", ['result' => $result]);

            $data = json_decode(json_encode($result), true);

            $status = 'Unknown';
            $statusCode = 'Unknown';

            if (isset($data['data']['SMSMessageData']['Recipients'][0])) {
                $recipient = $data['data']['SMSMessageData']['Recipients'][0];
                $status = $recipient['status'] ?? 'Unknown';
                $statusCode = $recipient['statusCode'] ?? 'Unknown';
            } elseif (isset($data['SMSMessageData']['Recipients'][0])) {
                $recipient = $data['SMSMessageData']['Recipients'][0];
                $status = $recipient['status'] ?? 'Unknown';
                $statusCode = $recipient['statusCode'] ?? 'Unknown';
            }

            $historyModel->api_response = [
                'timestamp' => $now,
                'status' => $status,
                'status_code' => $statusCode,
                'full_response' => $data,
            ];

            if ($status === 'Success' || $status === 'Sent' || $statusCode == 101 || $statusCode == 100) {
                // Determine status field name (MessageHistory uses message_status, AdHistory uses status)
                $statusField = isset($historyModel->message_status) ? 'message_status' : 'status';
                $historyModel->$statusField = 'sent';

                Log::info("SMS sent successfully to {$phoneNumber}", ['history_id' => $historyModel->id]);
                $historyModel->save();

                return true;
            }

            $statusField = isset($historyModel->message_status) ? 'message_status' : 'status';
            $historyModel->$statusField = 'failed';
            Log::error("SMS failed to {$phoneNumber}. Status: {$status}", ['response' => $data]);

        } catch (\Throwable $e) {
            $statusField = isset($historyModel->message_status) ? 'message_status' : 'status';
            $historyModel->$statusField = 'failed';
            $historyModel->api_response = [
                'timestamp' => $now,
                'error' => $e->getMessage(),
                'trace' => substr($e->getTraceAsString(), 0, 500),
            ];
            Log::error("SMS Exception for history ID {$historyModel->id}: ".$e->getMessage());
        }

        $historyModel->save();

        return false;
    }

    /**
     * Send an SMS and update message history.
     */
    public function sendSms(MessageHistory $messageHistory, string $phoneNumber, string $message): bool
    {
        return $this->sendSmsGeneric($messageHistory, $phoneNumber, $message);
    }

    /**
     * Formats phone number to international format (specifically for Malawi +265).
     */
    public function formatPhoneNumber(string $phoneNumber): string
    {
        $phoneNumber = trim($phoneNumber);

        if (str_starts_with($phoneNumber, '0')) {
            return '+265'.substr($phoneNumber, 1);
        }

        if (! str_starts_with($phoneNumber, '+')) {
            return '+'.$phoneNumber;
        }

        return $phoneNumber;
    }
}
