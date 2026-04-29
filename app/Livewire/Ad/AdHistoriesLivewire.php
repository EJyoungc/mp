<?php

namespace App\Livewire\Ad;

use App\Jobs\SendSmsAdJob;
use App\Models\AdHistory;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use Livewire\WithPagination;

class AdHistoriesLivewire extends Component
{
    use LivewireAlert, WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $modal = false;

    public $search = '';

    public $status = '';

    public $perPage = 10;

    public $selected_ad = null;

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatus()
    {
        $this->resetPage();
    }

    public function viewResponse($id)
    {
        $this->selected_ad = AdHistory::find($id);
        $this->modal = true;
    }

    public function resend($id)
    {
        $adHistory = AdHistory::find($id);
        if ($adHistory && $adHistory->mother) {
            // Reset status for resending
            $adHistory->update(['status' => 'pending']);

            SendSmsAdJob::dispatch($adHistory, $adHistory->mother->phone, $adHistory->message);

            $this->alert('success', "Ad message for {$adHistory->mother->name} queued for resending.");
        } else {
            $this->alert('error', 'Failed to resend: Record not found.');
        }
    }

    public function cancel()
    {
        $this->reset([
            'modal',
            'selected_ad',
        ]);
    }

    public function render()
    {
        $ads = AdHistory::with(['mother', 'pharmacyAd', 'organization'])
            ->when($this->search, function ($query) {
                $query->whereHas('mother', function ($q) {
                    $q->where('name', 'like', '%'.$this->search.'%')
                        ->orWhere('phone', 'like', '%'.$this->search.'%');
                })->orWhereHas('pharmacyAd', function ($q) {
                    $q->where('product_name', 'like', '%'.$this->search.'%');
                })->orWhere('message', 'like', '%'.$this->search.'%');
            })
            ->when($this->status, function ($query) {
                $query->where('status', $this->status);
            })
            ->latest()
            ->paginate($this->perPage);

        return view('livewire.ad.ad-histories-livewire', [
            'ads' => $ads,
        ]);
    }
}
