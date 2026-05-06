<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;

class DashboardLivewire extends Component
{
    /**
     * The DashboardLivewire component now acts as a dispatcher,
     * rendering role-specific dashboard components.
     */
    public function render()
    {
        return view('livewire.dashboard.dashboard-livewire');
    }
}
