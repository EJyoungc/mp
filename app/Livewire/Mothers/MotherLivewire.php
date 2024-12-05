<?php

namespace App\Livewire\Mothers;

use Hashids\Hashids;
use App\Helper\StandardData as SD;
use App\Models\History;
use App\Models\MessageHistory;
use App\Models\Tip;
use App\Models\User;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class MotherLivewire extends Component
{

    use LivewireAlert;
    public $modal = false;
    public $mother_id;
    public $infant_number;
    public $last_menstrual_cycle;
    public $history_id;
    public $count = 0;


    public function create($id = null)

    {
        if (empty($id)) {
            $this->modal = true;
        } else {


            $this->history_id = History::findOrFail($id);
            $this->infant_number = $this->history_id->infant_number;
            $this->last_menstrual_cycle = $this->history_id->last_menstrual_cycle;

            $this->modal = true;
        }
    }


    public function mount($mother_id)
    {
        $id = SD::decrypt($mother_id);
        $this->mother_id = User::findOrFail($id);
    }

    public function store()
    {
        $this->validate([
            'infant_number' => 'required|numeric',
            'last_menstrual_cycle' => 'required|date',

        ]);
        if (empty($this->history_id)) {


            $h = History::create([
                'mother_id' => $this->mother_id->id,
                'infant_number' => $this->infant_number,
                'last_menstrual_cycle' => $this->last_menstrual_cycle
            ]);


            $this->alert('success', 'Successfully saved');
        } else {
            $this->history_id->update([
                'mother_id' => $this->mother_id->id,
                'infant_number' => $this->infant_number,
                'last_menstrual_cycle' => $this->last_menstrual_cycle
            ]);
            $this->alert('success', 'Successfully saved');
        }
        $this->cancel();
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
                    $this->sendMessage($messagehistory, $history->mother->name, $t->tip);
                }
            }
        }
    }
    
    private function sendMessage($messagehistory, $motherName, $tip)
    {
        $status = 200;  // Simulating the message sending status
        if ($status == 200) {
            $messagehistory->message_status = 'sent';
            $messagehistory->save();
            $this->alert('info', 'Dear: ' . $motherName . ' ' . $tip);
        } else {
            $messagehistory->message_status = 'failed';
            $messagehistory->save();
            $this->alert('error', 'Message not sent');
        }
    }
    

    public function cancel()
    {
        $this->reset([
            'modal',
            'infant_number',
            'last_menstrual_cycle'
        ]);
    }
    public function render()
    {



        $h = History::where('mother_id', $this->mother_id->id)->get();
        $messges = MessageHistory::where('mother_id', $this->mother_id->id)->get();
        return view('livewire.mothers.mother-livewire')
        ->with('history', $h)
        ->with('messages', $messges);
    }
}
