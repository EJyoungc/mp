<?php

namespace App\Livewire\Dashboard;

use App\Models\PharmacyAd;
use App\Models\Trimester;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class PharmacyAdsManager extends Component
{
    use LivewireAlert;

    public $adModal = false;

    public $product_name;

    public $ad_message;

    public $target_week_start = 1;

    public $target_week_end = 40;

    public $trimester_id;

    public $ad_id;

    public $schedule_type = 'daily';

    public $schedule_limit = 1;

    public function createAd()
    {
        $this->reset(['ad_id', 'product_name', 'ad_message', 'target_week_start', 'target_week_end', 'trimester_id', 'schedule_type', 'schedule_limit']);
        $this->adModal = true;
    }

    public function editAd($id)
    {
        $user = auth()->user();
        $ad = PharmacyAd::findOrFail($id);

        if (! $user->isSystemAdmin() && $ad->organization_id != $user->organization_id) {
            $this->alert('error', 'Unauthorized access.');

            return;
        }

        $this->ad_id = $ad->id;
        $this->product_name = $ad->product_name;
        $this->ad_message = $ad->ad_message;
        $this->target_week_start = $ad->target_week_start;
        $this->target_week_end = $ad->target_week_end;
        $this->trimester_id = $ad->trimester_id;
        $this->schedule_type = $ad->schedule_type ?? 'daily';
        $this->schedule_limit = $ad->schedule_limit ?? 1;

        $this->adModal = true;
    }

    public function storeAd()
    {
        $user = auth()->user();
        $this->validate([
            'product_name' => 'required|string|max:255',
            'ad_message' => 'required|string|max:160',
            'target_week_start' => 'nullable|integer|min:1',
            'target_week_end' => 'nullable|integer|max:42',
            'trimester_id' => 'nullable|exists:trimesters,id',
            'schedule_type' => 'required|string|in:daily,weekly,monthly',
            'schedule_limit' => 'required|integer|min:1|max:31',
        ]);

        $data = [
            'product_name' => $this->product_name,
            'ad_message' => $this->ad_message,
            'target_week_start' => $this->target_week_start,
            'target_week_end' => $this->target_week_end,
            'trimester_id' => $this->trimester_id,
            'schedule_type' => $this->schedule_type,
            'schedule_limit' => $this->schedule_limit,
            'organization_id' => $user->isSystemAdmin() ? null : $user->organization_id,
        ];

        if ($this->ad_id) {
            $ad = PharmacyAd::findOrFail($this->ad_id);
            if ($user->isSystemAdmin() || $ad->organization_id == $user->organization_id) {
                $ad->update($data);
                $this->alert('success', 'Ad updated successfully');
            }
        } else {
            PharmacyAd::create($data);
            $this->alert('success', 'Ad created successfully');
        }

        $this->cancel();
    }

    public function toggleAd($id)
    {
        $user = auth()->user();
        $ad = PharmacyAd::findOrFail($id);
        if ($user->isSystemAdmin() || $ad->organization_id == $user->organization_id) {
            $ad->update(['is_active' => ! $ad->is_active]);
            $this->alert('success', 'Ad status updated');
        }
    }

    public function cancel()
    {
        $this->reset(['adModal', 'product_name', 'ad_message', 'target_week_start', 'target_week_end', 'trimester_id', 'ad_id', 'schedule_type', 'schedule_limit']);
    }

    public function render()
    {
        $user = auth()->user();
        $adsQuery = PharmacyAd::latest();

        if (! $user->isSystemAdmin()) {
            $adsQuery->where('organization_id', $user->organization_id);
        }

        return view('livewire.dashboard.pharmacy-ads-manager', [
            'ads' => $adsQuery->get(),
            'trimesters' => Trimester::all(),
        ]);
    }
}
