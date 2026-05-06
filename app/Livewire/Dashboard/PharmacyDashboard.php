<?php

namespace App\Livewire\Dashboard;

use App\Models\MessageHistory;
use App\Models\Tip;
use App\Models\User;
use Livewire\Component;

class PharmacyDashboard extends Component
{
    public function render()
    {
        $user = auth()->user();
        $messages = MessageHistory::where('organization_id', $user->organization_id)->get();
        $tips = Tip::all();

        $analytics = [
            'practitioners' => User::where('organization_id', $user->organization_id)
                ->whereHas('role', fn ($q) => $q->where('name', 'practitioner'))->count(),
            'delivery_rate' => $messages->count() > 0 ? round(($messages->where('message_status', 'sent')->count() / $messages->count()) * 100, 1) : 0,
        ];

        return view('livewire.dashboard.pharmacy-dashboard', [
            'tips' => $tips,
            'analytics' => $analytics,
        ]);
    }
}
