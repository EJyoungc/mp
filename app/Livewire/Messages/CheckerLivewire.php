<?php

namespace App\Livewire\Messages;

use App\Models\History;
use App\Models\MessageHistory;
use App\Models\Tip;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use AfricasTalking\SDK\AfricasTalking;

class CheckerLivewire extends Component
{

    use LivewireAlert;
    public $modal = false;
    public $count;

    private $username   = "DumiKaliati";
    private $apiKey     = "atsk_1b93e9047e28c4f2d33a74bc73e65782aa5bd04e8390cae5cb491861b37c05ddbbb59876";


    public function create(){
        $this->modal = true;
        
    }

    public function test()
    {
        $this->count++;
        $histories = History::all();
    
        foreach ($histories as $history) {
            $weekdata = $history->calculate_week();
            $tips = Tip::where('week_id', (int)$weekdata['weeks'])
                       ->where('day_id', (int)$weekdata['day_of_week'])
                       ->get();
    
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
                }
    
                if ($messagehistory->message_status === 'unsent') {
                    $this->sendMessage($messagehistory, $history->mother->name, $t->tip, $history->mother->phone);
                }
            }
        }
    }
    private function sendMessage($messagehistory, $motherName, $tip, $mobile_number)
    {
        
        $AT = new AfricasTalking($this->username, $this->apiKey);
        $sms = $AT->sms();

        // dd();

       
        $result   = $sms->send([
            'to'      => $this->formatPhoneNumber($mobile_number),
            'message' => $tip,
             "from" => 'Maasms'
        ]);
        // dd($result['status']);
        if ($result['status'] == "success" ) {
            
           
            $messagehistory->message_status = 'sent';
            $messagehistory->save();
            $this->alert('info', 'Dear: ' . $motherName . ' ' . $tip);
        } else {
            $messagehistory->message_status = 'failed';
            $messagehistory->save();
            $this->alert('error', 'Message not sent');
        }
    }

    function formatPhoneNumber($phoneNumber)
{
    // Check if the number starts with '0'
    if (substr($phoneNumber, 0, 1) === '0') {
        // Remove the '0' and prepend '+265'
        return '+265' . substr($phoneNumber, 1);
    }
    return $phoneNumber;
}
    

    public function cancel(){
        $this->reset([
            'modal',
            
        ]);
    }
    public function render()
    {
        return view('livewire.messages.checker-livewire');
    }
}
