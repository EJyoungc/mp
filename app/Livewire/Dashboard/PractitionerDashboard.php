<?php

namespace App\Livewire\Dashboard;

use App\Models\MessageHistory;
use App\Models\Tip;
use App\Models\User;
use Livewire\Component;

class PractitionerDashboard extends Component
{
    public function render()
    {
        $user = auth()->user();

        $mothers = User::where('role_id', 4)
            ->where('organization_id', $user->organization_id)
            ->get();

        $messages = MessageHistory::where('organization_id', $user->organization_id)->get();
        $tips = Tip::all();

        $analytics = [
            'delivery_rate' => $messages->count() > 0 ? round(($messages->where('message_status', 'sent')->count() / $messages->count()) * 100, 1) : 0,
        ];

        return view('livewire.dashboard.practitioner-dashboard', [
            'mothers' => $mothers,
            'tips' => $tips,
            'analytics' => $analytics,
        ]);
    }
}
