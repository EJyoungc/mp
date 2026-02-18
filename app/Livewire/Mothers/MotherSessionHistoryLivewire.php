<?php

namespace App\Livewire\Mothers;

use App\Models\MessageHistory;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use AfricasTalking\SDK\AfricasTalking;
use Livewire\Component;

class MotherSessionHistoryLivewire extends Component
{

    use LivewireAlert;
    public $modal = false;
    public $mother_id;
    public $history_id;
    public $username   = "DumiKaliati";
    public $apiKey     = "atsk_1b93e9047e28c4f2d33a74bc73e65782aa5bd04e8390cae5cb491861b37c05ddbbb59876";

    public function mount($mother_id, $session_id)
    {

        $this->mother_id = $mother_id;
        $this->history_id = $session_id;
    }



    public function resend($id)
    {
        $mh = MessageHistory::find($id);
        $mh->message_status = "unsent";
        $mh->save();
        $this->alert('success', 'Added to sending Queue');
    }

    public function test()
    {
        $AT = new AfricasTalking($this->username, $this->apiKey);
        $sms = $AT->sms();


        $result   = $sms->send([
            'to'      => '+265995936887',
            'message' => "this is a maasms test",
            "from" => 'Maasms'
        ]);

        dd($result);
    }



    public function create()
    {
        $this->modal = true;
    }

    public function cancel()
    {
        $this->reset([
            'modal',

        ]);
    }
    public function render()
    {
        // dd( $this->mother_id);
        $message_history = MessageHistory::where('mother_id', $this->mother_id)->get();
        $failed = MessageHistory::where('mother_id', $this->mother_id)->where('history_id', $this->history_id)->where('message_status', 'failed')->get();
        $unsent = MessageHistory::where('mother_id', $this->mother_id)->where('history_id', $this->history_id)->where('message_status', 'unsent')->get();
        return view('livewire.mothers.mother-session-history-livewire')
            ->with('message_history', $message_history)
            ->with('failed', $failed)
            ->with('unsent', $unsent);
    }
}
