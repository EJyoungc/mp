<?php

namespace App\Livewire\Trimesters\Weeks;

use App\Models\Trimester;
use App\Models\Week;
use Livewire\Component;

class WeeksLivewire extends Component
{

    public $modal = false;
    public $trimester_id;
    public $trimester;


    public function mount($id){
        $this->trimester_id = $id;
        $this->trimester = Trimester::find($id);

    }


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

        $week = Week::where('trimester_id', $this->trimester_id)->get();
        
        return view('livewire.trimesters.weeks.weeks-livewire')->with('weeks', $week);
    }
}
