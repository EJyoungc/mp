<?php

namespace App\Livewire\Days;

use App\Models\DayRange;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class DayRangesLivewire extends Component
{


    use LivewireAlert;
    public $modal = false;
    public $name;
    public $day_range;
    public $start_time;
    public $end_time;
    


    public function create($id = null)
    {
        if (!empty($id)) {
            $this->modal = true;
            
            $this->day_range = DayRange::findOrFail($id);
            $this->name = $this->day_range->name; 
            $this->start_time = $this->day_range->start_time; 
            $this->end_time = $this->day_range ->end_time;
        }else{
            $this->modal = true;
        }
    }

    public function store()
    {
        $this->validate([
            'name'=>'required|string',
            'start_time' => 'required',
            'end_time' => 'required'
        ]);

        if(empty($this->day_range->id)){

            DayRange::create([
                'name'=>$this->name,
                'start_time' => $this->start_time,
                'end_time' => $this->end_time
            ]);
            $this->alert('success', 'successfully saved');
            $this->cancel();
        }else{
            $this->day_range->name = $this->name;
            $this->day_range->start_time = $this->start_time;
            $this->day_range->end_time = $this->end_time;
            $this->day_range->save();
            $this->alert('success','succesfully updated');
            $this->cancel();
        }
       
    }

    public function cancel()
    {
        $this->reset([
            'day_range',
            'modal',
            'name',
            'start_time',
            'end_time'
        ]);
    }
    public function render()
    {
        $day_range = DayRange::get();
        return view('livewire.days.day-ranges-livewire')->with('ranges', $day_range);
    }
}
