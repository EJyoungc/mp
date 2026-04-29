<?php

namespace App\Livewire\Mothers;

use App\Helper\StandardData as SD;
use App\Models\History;
use App\Models\MessageHistory;
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
        $user = User::findOrFail($id);

        // Ensure the logged in user can access this mother
        if (! auth()->user()->isSystemAdmin() && $user->organization_id !== auth()->user()->organization_id) {
            return redirect()->route('access-denied');
        }

        $this->mother_id = $user;
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
                'last_menstrual_cycle' => $this->last_menstrual_cycle,
                'organization_id' => auth()->user()->organization_id,
            ]);

            $this->alert('success', 'Successfully saved');
        } else {
            $this->history_id->update([
                'mother_id' => $this->mother_id->id,
                'infant_number' => $this->infant_number,
                'last_menstrual_cycle' => $this->last_menstrual_cycle,
            ]);
            $this->alert('success', 'Successfully saved');
        }
        $this->cancel();
    }

    public function cancel()
    {
        $this->reset([
            'modal',
            'infant_number',
            'last_menstrual_cycle',
        ]);
    }

    public function render()
    {
        // History and MessageHistory are auto-scoped via BelongsToOrganization trait
        $h = History::where('mother_id', $this->mother_id->id)->get();
        $messges = MessageHistory::where('mother_id', $this->mother_id->id)->get();

        return view('livewire.mothers.mother-livewire')
            ->with('history', $h)
            ->with('messages', $messges);
    }
}
