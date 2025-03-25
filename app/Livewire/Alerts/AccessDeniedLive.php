<?php

namespace App\Livewire\Alerts;

use Livewire\Component;

class AccessDeniedLive extends Component
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
        return view('livewire.alerts.access-denied-live');
    }
}
