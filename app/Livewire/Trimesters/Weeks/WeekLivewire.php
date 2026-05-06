<?php

namespace App\Livewire\Trimesters\Weeks;

use App\Models\DayRange;
use App\Models\Tip;
use App\Models\Trimester;
use App\Models\Week;
use Illuminate\Support\Facades\DB;
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

    // Bulk Tip Properties
    public $bulkModal = false;

    public $selectedDays = [];

    public $selectedRanges = [];

    public $bulkTipContent;

    public $selectAllDays = false;

    public $selectAllRanges = false;

    public $selectedTips = [];

    public $selectAllTips = false;

    public function mount($trimester_id, $week_id)
    {
        $this->trimester_id = $trimester_id;
        $this->trimester = Trimester::find($trimester_id);
        $this->week_id = $week_id;
        $this->week = Week::findOrFail($week_id);
    }

    public function updatedSelectAllTips($value)
    {
        if ($value) {
            $this->selectedTips = Tip::where('week_id', $this->week_id)
                ->pluck('id')
                ->map(fn ($id) => (string) $id)
                ->toArray();
        } else {
            $this->selectedTips = [];
        }
    }

    public function delete($id)
    {
        $tip = Tip::findOrFail($id);
        $tip->delete();
        $this->alert('success', 'Tip deleted successfully');
    }

    public function deleteSelected()
    {
        if (empty($this->selectedTips)) {
            $this->alert('warning', 'Please select at least one tip.');

            return;
        }

        Tip::whereIn('id', $this->selectedTips)->delete();
        $this->selectedTips = [];
        $this->selectAllTips = false;
        $this->alert('success', 'Selected tips deleted successfully');
    }

    public function updatedSelectAllDays($value)
    {
        if ($value) {
            $week_number = $this->week->week;
            $this->selectedDays = range(($week_number * 7) - 6, ($week_number * 7));
            $this->selectedDays = array_map('strval', $this->selectedDays);
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

    public function createBulk()
    {
        $this->resetBulkForm();
        $this->bulkModal = true;
    }

    public function storeBulk()
    {
        $this->validate([
            'selectedDays' => 'required|array|min:1',
            'selectedRanges' => 'required|array|min:1',
            'bulkTipContent' => 'required|string|min:5|max:160',
        ], [
            'selectedDays.required' => 'Please select at least one day.',
            'selectedRanges.required' => 'Please select at least one time range.',
            'bulkTipContent.required' => 'The tip content cannot be empty.',
        ]);

        try {
            DB::transaction(function () {
                $user = auth()->user();
                $status = $user->isDoctor() ? Tip::STATUS_APPROVED : Tip::STATUS_PENDING;

                foreach ($this->selectedDays as $dayId) {
                    foreach ($this->selectedRanges as $rangeId) {
                        Tip::create([
                            'week_id' => $this->week_id,
                            'day_id' => $dayId,
                            'day_range_id' => $rangeId,
                            'tip' => $this->bulkTipContent,
                            'organization_id' => $user->organization_id,
                            'created_by' => $user->id,
                            'status' => $status,
                        ]);
                    }
                }
            });

            $this->alert('success', 'Bulk tips created successfully.');
            $this->bulkModal = false;
            $this->resetBulkForm();
        } catch (\Exception $e) {
            $this->alert('error', 'Failed to create tips: '.$e->getMessage());
        }
    }

    private function resetBulkForm()
    {
        $this->reset([
            'selectedDays', 'selectedRanges', 'bulkTipContent',
            'selectAllDays', 'selectAllRanges', 'bulkModal',
        ]);
    }

    public function cancelBulk()
    {
        $this->resetBulkForm();
    }

    public function store()
    {
        $this->validate([
            'tip' => 'required|string|min:5|max:160',
            'day' => 'required',
            'time' => 'required',
        ]);

        $user = auth()->user();
        $status = $user->isDoctor() ? Tip::STATUS_APPROVED : Tip::STATUS_PENDING;

        if (empty($this->tip_id->id)) {
            Tip::create([
                'tip' => $this->tip,
                'week_id' => $this->week_id,
                'day_range_id' => $this->time,
                'day_id' => $this->day,
                'created_by' => $user->id,
                'status' => $status,
                'organization_id' => $user->organization_id,
            ]);

            $this->alert('success', 'Tip successfully saved'.($status === Tip::STATUS_PENDING ? ' and pending approval' : ''));
            $this->cancel();
        } else {
            $this->tip_id->day_range_id = $this->time;
            $this->tip_id->day_id = $this->day;
            $this->tip_id->tip = $this->tip;
            $this->tip_id->save();
            $this->alert('success', 'Tip successfully updated');
            $this->cancel();
        }
    }

    public function approve($id)
    {
        if (! auth()->user()->isDoctor()) {
            $this->alert('error', 'Unauthorized action');

            return;
        }

        $tip = Tip::findOrFail($id);
        $tip->update([
            'status' => Tip::STATUS_APPROVED,
            'approved_by' => auth()->id(),
        ]);

        $this->alert('success', 'Tip approved successfully');
    }

    public function markAsTemplate($id)
    {
        if (! auth()->user()->isSystemAdmin()) {
            $this->alert('error', 'Only System Admin can mark tips as templates');

            return;
        }

        $tip = Tip::findOrFail($id);
        $tip->update(['is_template' => ! $tip->is_template]);
        $this->alert('success', 'Tip template status updated');
    }

    public function useTemplate($id)
    {
        $template = Tip::findOrFail($id);

        Tip::create([
            'tip' => $template->tip,
            'week_id' => $template->week_id,
            'day_range_id' => $template->day_range_id,
            'day_id' => $template->day_id,
            'organization_id' => auth()->user()->organization_id,
            'created_by' => auth()->id(),
            'status' => Tip::STATUS_PENDING,
        ]);

        $this->alert('success', 'Template copied to your organization as a draft');
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
        $tips = Tip::with(['creator', 'approver'])->where('week_id', $this->week_id)->get();

        // Templates available to all organizations
        $templates = Tip::withoutGlobalScope('organization')
            ->where('is_template', true)
            ->where('week_id', $this->week_id)
            ->get();

        $week_number = $this->week->week;
        $days_in_week = range(($week_number * 7) - 6, ($week_number * 7));
        // dd($days_in_week);

        $day_ranges = DayRange::all();

        return view('livewire.trimesters.weeks.week-livewire')
            ->with('tips', $tips)
            ->with('templates', $templates)
            ->with('days', $days_in_week)
            ->with('day_ranges', $day_ranges);
    }
}
