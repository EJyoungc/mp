<?php

namespace App\Livewire\Dashboard;

use App\Models\Organization;
use App\Models\User;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class MembershipRequestsManager extends Component
{
    use LivewireAlert;

    public function approve($id)
    {
        $userToApprove = User::findOrFail($id);
        $user = auth()->user();

        if (! $user->isSystemAdmin()) {
            $isOwner = Organization::where('id', $userToApprove->organization_id)
                ->where('owner_id', $user->id)
                ->exists();

            if (! $isOwner) {
                $this->alert('error', 'Unauthorized action.');

                return;
            }
        }

        if (! empty($userToApprove->organization_id)) {
            $userToApprove->organization_verify = 'approved';
            $userToApprove->save();
            $this->alert('success', 'User Approved');
        } else {
            $this->alert('warning', 'User Has No Organization');
        }
    }

    public function decline($id)
    {
        $userToDecline = User::findOrFail($id);
        $user = auth()->user();

        if (! $user->isSystemAdmin()) {
            $isOwner = Organization::where('id', $userToDecline->organization_id)
                ->where('owner_id', $user->id)
                ->exists();

            if (! $isOwner) {
                $this->alert('error', 'Unauthorized action.');

                return;
            }
        }

        $userToDecline->organization_verify = 'declined';
        $userToDecline->save();
        $this->alert('warning', 'User Declined');
    }

    public function render()
    {
        $user = auth()->user();
        $requestsQuery = User::where('organization_id', '!=', null)
            ->where('organization_verify', 'pending')
            ->orderBy('created_at', 'desc');

        if (! $user->isSystemAdmin()) {
            $ownedOrgIds = Organization::where('owner_id', $user->id)->pluck('id');
            if ($ownedOrgIds->isNotEmpty()) {
                $requestsQuery->whereIn('organization_id', $ownedOrgIds);
            } else {
                $requestsQuery->where('id', 0);
            }
        }

        return view('livewire.dashboard.membership-requests-manager', [
            'requests' => $requestsQuery->get(),
        ]);
    }
}
