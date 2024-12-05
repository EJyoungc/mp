<?php

namespace App\Livewire\Dashboard;

use App\Models\MessageHistory;
use App\Models\Tip;
use App\Models\User;
use Livewire\Component;

class DashboardLivewire extends Component
{
    public function render()
    {

        $mothers = User::where('role_id', 4)->get();
        $users = User::all();
        $messages = MessageHistory::all();
        $tips = Tip::all();
        return view('livewire.dashboard.dashboard-livewire')
        ->with('mothers', $mothers)
        ->with('users', $users)
        ->with('messages', $messages)
        ->with('tips', $tips);
    }
}
