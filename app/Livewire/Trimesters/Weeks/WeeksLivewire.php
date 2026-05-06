<?php

namespace App\Livewire\Trimesters\Weeks;

use App\Models\Day;
use App\Models\DayRange;
use App\Models\Tip;
use App\Models\Trimester;
use App\Models\Week;
use Illuminate\Support\Facades\DB;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class WeeksLivewire extends Component
{
    use LivewireAlert;

    public $modal = false;

    public $trimester_id;

    public $trimester;

    // Bulk Tip Properties
    public $selectedWeeks = [];

    public $selectedDays = [];

    public $selectedRanges = [];

    public $tipContent;

    public $selectAllWeeks = false;

    public $selectAllDays = false;

    public $selectAllRanges = false;

    public $week_id;

    public $week_number;

    public function mount($id)
    {
        $this->trimester_id = $id;
        $this->trimester = Trimester::find($id);
    }

    public function editWeek($id)
    {
        $week = Week::findOrFail($id);
        $this->week_id = $week->id;
        $this->week_number = $week->week;
        $this->modal = false; // Close bulk modal if open
        // I might need a separate modal for editing a week or repurpose the modal variable
        // For now let's assume a single modal variable is used and I'll toggle it in the view
        $this->dispatch('open-edit-week-modal');
    }

    public function updateWeek()
    {
        $this->validate([
            'week_number' => 'required|integer',
        ]);

        $week = Week::findOrFail($this->week_id);
        $week->update(['week' => $this->week_number]);

        $this->alert('success', 'Week updated successfully');
        $this->dispatch('close-edit-week-modal');
    }

    public function updatedSelectAllWeeks($value)
    {
        if ($value) {
            $this->selectedWeeks = Week::where('trimester_id', $this->trimester_id)->pluck('id')->map(fn ($id) => (string) $id)->toArray();
        } else {
            $this->selectedWeeks = [];
        }
    }

    public function updatedSelectAllDays($value)
    {
        if ($value) {
            $this->selectedDays = Day::pluck('id')->map(fn ($id) => (string) $id)->toArray();
        } else {
            $this->selectedDays = [];
        }
    }

    public function updatedSelectAllRanges($value)
    {
        if ($value) {
            $this->selectedRanges = DayRange::pluck('id')->map(fn ($id) => (string) $id)->toArray();
        } else {
            $this->selectedRanges = [];
        }
    }

    public function create()
    {
        $this->resetBulkForm();
        $this->modal = true;
    }

    public function storeBulk()
    {
        $this->validate([
            'selectedWeeks' => 'required|array|min:1',
            'selectedDays' => 'required|array|min:1',
            'selectedRanges' => 'required|array|min:1',
            'tipContent' => 'required|string',
        ], [
            'selectedWeeks.required' => 'Please select at least one week.',
            'selectedDays.required' => 'Please select at least one day.',
            'selectedRanges.required' => 'Please select at least one time range.',
            'tipContent.required' => 'The tip content cannot be empty.',
        ]);

        try {
            DB::transaction(function () {
                $user = auth()->user();
                $orgId = $user->organization_id;

                foreach ($this->selectedWeeks as $weekId) {
                    foreach ($this->selectedDays as $dayId) {
                        foreach ($this->selectedRanges as $rangeId) {
                            Tip::create([
                                'week_id' => $weekId,
                                'day_id' => $dayId,
                                'day_range_id' => $rangeId,
                                'tip' => $this->tipContent,
                                'organization_id' => $orgId,
                                'created_by' => $user->id,
                                'status' => Tip::STATUS_APPROVED, // Auto-approve bulk tips for convenience
                                'is_template' => true,
                            ]);
                        }
                    }
                }
            });

            $this->alert('success', 'Bulk tips created successfully.');
            $this->modal = false;
            $this->resetBulkForm();
        } catch (\Exception $e) {
            $this->alert('error', 'Failed to create tips: '.$e->getMessage());
        }
    }

    private function resetBulkForm()
    {
        $this->reset([
            'selectedWeeks', 'selectedDays', 'selectedRanges', 'tipContent',
            'selectAllWeeks', 'selectAllDays', 'selectAllRanges',
        ]);
    }

    public function cancel()
    {
        $this->modal = false;
        $this->resetBulkForm();
    }

    public function render()
    {
        $weeks = Week::where('trimester_id', $this->trimester_id)->with('tips')->get();

        return view('livewire.trimesters.weeks.weeks-livewire', [
            'weeks' => $weeks,
            'availableDays' => Day::all(),
            'availableRanges' => DayRange::all(),
        ]);
    }
}
