<?php

namespace App\Livewire\Trimesters\Weeks;

use App\Models\Day;
use App\Models\DayRange;
use App\Models\SignSymptom;
use App\Models\Tip;
use App\Models\Trimester;
use App\Models\Week;
use Illuminate\Support\Collection;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Attributes\Computed;
use Livewire\Component;

class WeekLivewire extends Component
{
    use LivewireAlert;
    public $modal = false;
    public $week_id;
    public $tip_id;
    public $trimester_id;

    public $week;
    public $trimester;

    public $day;
    public $time;
    public $tip;



    public function mount($trimester_id, $week_id)
    {

        $this->trimester_id = $trimester_id;
        $this->trimester = Trimester::find($trimester_id);
        $this->week_id = $week_id;
        $this->week = Week::findOrFail($week_id);
    }

    public function store()
    {

        $this->validate([
            'tip' => 'required|string',
            'day' => 'required',
            'time' => 'required'
        ]);

        if (empty($this->tip_id->id)) {
            Tip::create([
                'tip' => $this->tip,
                'week_id' => $this->week_id,
                'day_range_id' => $this->time,
                'day_id' => $this->day
            ]);

            $this->alert('success', 'successfully saved');
            $this->cancel();
        } else {
            $this->tip_id->day_range_id = $this->time;
            $this->tip_id->day_id = $this->day;
            $this->tip_id->tip = $this->tip;
            $this->tip_id->save();
            $this->alert('success', 'successfully updated');
            $this->cancel();
        }
    }


    public function create($id = null)
    {
        if (empty($id)) {
            $this->modal = true;
        } else {
            $this->tip_id = Tip::findOrFail($id);
            $this->time = $this->tip_id->day_range_id;
            $this->day = $this->tip_id->day_id;
            $this->tip = $this->tip_id->tip;
            $this->modal = true;
        }
    }

    public function cancel()
    {
        $this->reset([
            'modal',
            'tip',
            'day',
            'time',
            'tip_id',
        ]);
    }
    #[Computed]
    public function getWeekFromDay($day)
    {
        return (int) ceil($day / 7);
    }




    public function render()
    {
        $tips = Tip::get();

        $week_number = $this->week->week;
        $days_in_week = range(($week_number * 7) - 6, ($week_number * 7));
        // dd($days_in_week);

        $day_ranges = DayRange::all();
        return view('livewire.trimesters.weeks.week-livewire')->with('tips', $tips)->with('days', $days_in_week)->with('day_ranges', $day_ranges);
    }
}
