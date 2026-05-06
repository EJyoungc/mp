<?php

namespace App\Livewire\Dashboard;

use App\Models\User;
use Livewire\Component;

class MotherDashboard extends Component
{
    public function render()
    {
        $user = auth()->user();
        $mothers = User::where('id', $user->id)->get();

        return view('livewire.dashboard.mother-dashboard', [
            'mothers' => $mothers,
        ]);
    }
}
