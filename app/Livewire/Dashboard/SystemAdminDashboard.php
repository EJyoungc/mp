<?php

namespace App\Livewire\Dashboard;

use App\Models\MessageHistory;
use App\Models\Organization;
use App\Models\Tip;
use App\Models\User;
use Livewire\Component;

class SystemAdminDashboard extends Component
{
    public function render()
    {
        $messages = MessageHistory::all();
        $tips = Tip::all();

        $analytics = [
            'organizations' => Organization::count(),
            'delivery_rate' => $messages->count() > 0 ? round(($messages->where('message_status', 'sent')->count() / $messages->count()) * 100, 1) : 0,
        ];

        return view('livewire.dashboard.system-admin-dashboard', [
            'mothersCount' => User::where('role_id', 4)->count(),
            'tips' => $tips,
            'pendingTipsCount' => Tip::where('status', Tip::STATUS_PENDING)->count(),
            'analytics' => $analytics,
        ]);
    }
}
