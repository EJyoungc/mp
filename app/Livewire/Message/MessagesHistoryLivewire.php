<?php

namespace App\Livewire\Message;

use Livewire\Component;

class MessagesHistoryLivewire extends Component
{

    public $modal = false;
    


    public function create(){
        $this->modal = true;
        
    }

    public function cancel(){
        $this->reset([
            'modal',
            
        ]);
    }
    public function render()
    {
        return view('livewire.message.messages-history-livewire');
    }
}
