<?php

namespace App\Livewire\Users;

use App\Mail\InvitationMail;
use App\Mail\Reset;
use App\Models\District;
use App\Models\History;
use App\Models\Invitation;
use App\Models\MessageHistory;
use App\Models\Organization;
use App\Models\Role;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use Livewire\WithPagination;

class UsersLivewire extends Component
{
    use LivewireAlert;
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $modal = false;

    public $roleModal = false;

    public $inviteModal = false;

    public $user;

    public $selectedUser;

    public $newRoleId;

    // Invitation properties
    public $inviteEmail;

    public $inviteRoleId;

    public $search = '';

    public $role_filter = '';

    public $organization_filter = '';

    public $perPage = 10;

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingRoleFilter()
    {
        $this->resetPage();
    }

    public function updatingOrganizationFilter()
    {
        $this->resetPage();
    }

    public function toggleActive($id)
    {

        $user = User::findOrFail($id);
        if ($user->is_active == 1) {
            $user->is_active = 0;
            $user->save();
            $this->alert('success', 'User status updated successfully');
        } else {
            $user->is_active = 1;
            $user->save();
            $this->alert('success', 'User status updated successfully');
        }
    }

    public function approve($id)
    {
        $user = User::findOrFail($id);

        // Authorization check: Must be admin of same org or system-admin
        if (! auth()->user()->isSystemAdmin() && auth()->user()->organization_id !== $user->organization_id) {
            $this->alert('error', 'Unauthorized');

            return;
        }

        $user->update(['organization_verify' => 'verified']);
        $this->alert('success', 'User approved successfully');
    }

    public function decline($id)
    {
        $user = User::findOrFail($id);

        // Authorization check
        if (! auth()->user()->isSystemAdmin() && auth()->user()->organization_id !== $user->organization_id) {
            $this->alert('error', 'Unauthorized');

            return;
        }

        $user->update(['organization_verify' => 'declined']);
        $this->alert('success', 'User declined successfully');
    }

    public function delete($id)
    {
        $user = User::findOrFail($id);
        $h = History::where('mother_id', $id)->delete();
        $mh = MessageHistory::where('mother_id', $id)->delete();

        $user->delete();
        $this->alert('success', 'User deleted successfully');
    }

    public function resetPassword($id)
    {

        $user = User::findOrFail($id);
        $password = StandardData::generatePassword();

        try {
            // $user = "eliajh";
            DB::table('users')->where('id', $id)->update(['password' => bcrypt($password)]);
            Mail::to($user->email)->send(new Reset($user, $password));
            $this->alert('success', 'Password reset successfully');
        } catch (Exception $e) {
            $this->alert('error', $e->getMessage());
        }

    }

    public function showRoleModal($userId)
    {
        $this->selectedUser = User::findOrFail($userId);
        $this->newRoleId = $this->selectedUser->role_id;
        $this->roleModal = true;
    }

    public function updateRole()
    {
        $this->validate([
            'newRoleId' => 'required|exists:roles,id',
        ]);

        $this->selectedUser->update([
            'role_id' => $this->newRoleId,
        ]);

        $this->roleModal = false;
        $this->alert('success', 'User role updated successfully');
    }

    public function showInviteModal()
    {
        $this->reset(['inviteEmail', 'inviteRoleId']);
        $this->inviteModal = true;
    }

    public function sendInvite()
    {
        $this->validate([
            'inviteEmail' => 'required|email|unique:users,email',
            'inviteRoleId' => 'required|exists:roles,id',
        ]);

        $loggedInUser = auth()->user();

        // Check if role is allowed for this user
        $role = Role::findOrFail($this->inviteRoleId);
        if ($loggedInUser->isPharmacyAdmin() && $role->name !== 'practitioner') {
            $this->alert('error', 'Pharmacy admins can only invite practitioners.');

            return;
        }

        $invitation = Invitation::create([
            'email' => $this->inviteEmail,
            'organization_id' => $loggedInUser->organization_id,
            'role_id' => $this->inviteRoleId,
            'token' => Str::random(40),
            'expires_at' => now()->addDays(7),
        ]);

        Mail::to($this->inviteEmail)->send(new InvitationMail($invitation));

        $this->inviteModal = false;
        $this->alert('success', 'Invitation sent successfully');
    }

    public function create()
    {
        $this->modal = true;
    }

    public function cancel()
    {
        $this->reset([
            'modal',
            'roleModal',
            'inviteModal',
            'user',
            'selectedUser',
            'newRoleId',
            'inviteEmail',
            'inviteRoleId',
        ]);
    }

    public function render()
    {
        $loggedInUser = auth()->user();

        // Start building the query
        $usersQuery = User::with(['role', 'organization']);

        // Base restriction: If not system-admin, only show users from the same organization
        if (! $loggedInUser->isSystemAdmin()) {
            $usersQuery->where('organization_id', $loggedInUser->organization_id);
        }

        // Filtering based on logged-in user role
        if ($loggedInUser->role->name === 'admin') {
            $usersQuery->whereHas('role', function ($q) {
                $q->where('name', '!=', 'system-admin');
            });
        } elseif ($loggedInUser->isDoctor()) {
            $usersQuery->whereHas('role', function ($q) {
                $q->whereNotIn('name', ['system-admin', 'admin']);
            });
        } elseif ($loggedInUser->isPractitioner()) {
            $usersQuery->whereHas('role', function ($q) {
                $q->whereNotIn('name', ['system-admin', 'admin', 'doctor', 'practitioner']);
            });
        }

        // Search and Filters
        $usersQuery->when($this->search, function ($query) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%'.$this->search.'%')
                    ->orWhere('email', 'like', '%'.$this->search.'%')
                    ->orWhere('phone', 'like', '%'.$this->search.'%');
            });
        })
            ->when($this->role_filter, function ($query) {
                $query->where('role_id', $this->role_filter);
            })
            ->when($this->organization_filter, function ($query) {
                $query->where('organization_id', $this->organization_filter);
            });

        $users = $usersQuery->latest()->paginate($this->perPage);

        // For stats (also restricted by organization if not system-admin)
        $statsQuery = User::query();
        if (! $loggedInUser->isSystemAdmin()) {
            $statsQuery->where('organization_id', $loggedInUser->organization_id);
        }

        $doctorsCount = (clone $statsQuery)->whereHas('role', function ($q) {
            $q->where('name', 'doctor');
        })->count();
        $mothersCount = (clone $statsQuery)->whereHas('role', function ($q) {
            $q->where('name', 'mother');
        })->count();
        $practitionersCount = (clone $statsQuery)->whereHas('role', function ($q) {
            $q->where('name', 'practitioner');
        })->count();
        $allUsersCount = $statsQuery->count();

        // Roles for adding users and filtering
        $rolesQuery = Role::query()->select('id', 'name')->distinct();
        if ($loggedInUser->isPharmacyAdmin()) {
            $rolesQuery->where('name', 'practitioner');
        } elseif ($loggedInUser->role->name == 'admin') {
            $rolesQuery->where('name', '!=', 'system-admin');
        } elseif ($loggedInUser->isDoctor()) {
            $rolesQuery->whereNotIn('name', ['system-admin', 'admin']);
        } elseif ($loggedInUser->isPractitioner()) {
            $rolesQuery->whereNotIn('name', ['system-admin', 'admin', 'doctor', 'practitioner']);
        }
        $roles = $rolesQuery->get();

        // Organizations list for filter
        if ($loggedInUser->isSystemAdmin()) {
            $organizations = Organization::all();
        } else {
            $organizations = Organization::where('id', $loggedInUser->organization_id)->get();
        }

        return view('livewire.users.users-livewire', [
            'users' => $users,
            'roles' => $roles,
            'organizations' => $organizations,
            'doctorsCount' => $doctorsCount,
            'mothersCount' => $mothersCount,
            'practitionersCount' => $practitionersCount,
            'allUsersCount' => $allUsersCount,
            'districts' => District::all(),
        ]);
    }
}
