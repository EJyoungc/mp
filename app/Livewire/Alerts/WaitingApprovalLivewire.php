<?php

namespace App\Livewire\Alerts;

use Livewire\Component;

class WaitingApprovalLivewire extends Component
{
    public $modal = false;

    public function create()
    {
        $this->modal = true;

    }

    public function cancel()
    {
        $this->reset([
            'modal',

        ]);
    }

    public function render()
    {
        return view('livewire.alerts.waiting-approval-livewire');
    }
}
