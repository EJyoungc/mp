<?php

namespace App\Livewire\Days;

use Livewire\Component;

class DaysLivewire extends Component
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
        return view('livewire.days.days-livewire');
    }
}
