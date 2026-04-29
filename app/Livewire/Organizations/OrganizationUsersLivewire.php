<?php

namespace App\Livewire\Organizations;

use App\Models\Organization;
use App\Models\User;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class OrganizationUsersLivewire extends Component
{
    use LivewireAlert;

    public $modal = false;

    public $id;

    public $name;

    public function mount($id)
    {
        $this->id = $id;
        $organization = Organization::findOrFail($id);
        $this->name = $organization->name;

        // Authorization check: Only System Admin or Organization Owner can view
        $user = auth()->user();
        if (! $user->isSystemAdmin() && $organization->owner_id !== $user->id) {
            abort(403, 'Unauthorized access to this organization.');
        }
    }

    public function approve($userId)
    {
        $userToApprove = User::findOrFail($userId);
        $organization = Organization::findOrFail($this->id);

        // Authorization check: Only System Admin or Organization Owner can approve
        $user = auth()->user();
        if (! $user->isSystemAdmin() && $organization->owner_id !== $user->id) {
            $this->alert('error', 'Unauthorized action.');

            return;
        }

        $userToApprove->organization_verify = 'approved';
        $userToApprove->save();
        $this->alert('success', 'User approved successfully');
    }

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

        $users = User::where('organization_id', $this->id)->get();

        return view('livewire.organizations.organization-users-livewire')->with('users', $users);
    }
}
