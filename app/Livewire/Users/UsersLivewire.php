<?php

namespace App\Livewire\Users;

use App\Helper\StandardData;
use App\Mail\Reset;
use App\Models\History;
use App\Models\MessageHistory;
use App\Models\Organization;
use App\Models\Role;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use Livewire\WithPagination;

class UsersLivewire extends Component
{
    use LivewireAlert;
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $modal = false;
    public $user;

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


    public function delete($id){
        $user = User::findOrFail($id);
        $h = History::where('mother_id', $id)->delete();
        $mh = MessageHistory::where('mother_id', $id)->delete();

        $user->delete();
        $this->alert('success', 'User deleted successfully');
    }


    public function resetPassword($id){


        $user = User::findOrFail($id);
        $password = StandardData::generatePassword();


        try{
            // $user = "eliajh";
            DB::table('users')->where('id', $id)->update(['password' => bcrypt($password)]);
            Mail::to($user->email)->send(new Reset($user, $password));
            $this->alert('success','Password reset successfully');
        }catch(Exception $e){
            $this->alert('error', $e->getMessage());
        }

    }







    public function create()
    {
        $this->modal = true;
    }

    public function cancel()
    {
        $this->reset([
            'modal',
            'user'
        ]);
    }


    public function render()
    {
        // Start building the query
        $usersQuery = User::with(['role', 'organization']);

        // Base restriction: If not system-admin, only show users from the same organization
        if (Auth::user()->role->name !== 'system-admin') {
            $usersQuery->where('organization_id', Auth::user()->organization_id);
        }

        // Filtering based on logged-in user role
        if (Auth::user()->role->name === 'admin') {
            $usersQuery->whereHas('role', function($q) {
                $q->where('name', '!=', 'system-admin');
            });
        } elseif (Auth::user()->role->name === 'doctor') {
             $usersQuery->whereHas('role', function($q) {
                $q->whereNotIn('name', ['system-admin', 'admin']);
            });
        } elseif (Auth::user()->role->name === 'practitioner') {
             $usersQuery->whereHas('role', function($q) {
                $q->whereNotIn('name', ['system-admin', 'admin', 'doctor', 'practitioner']);
            });
        }

        // Search and Filters
        $usersQuery->when($this->search, function ($query) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('email', 'like', '%' . $this->search . '%')
                  ->orWhere('phone', 'like', '%' . $this->search . '%');
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
        if (Auth::user()->role->name !== 'system-admin') {
            $statsQuery->where('organization_id', Auth::user()->organization_id);
        }

        $doctorsCount = (clone $statsQuery)->whereHas('role', function($q){ $q->where('name', 'doctor'); })->count();
        $mothersCount = (clone $statsQuery)->whereHas('role', function($q){ $q->where('name', 'mother'); })->count();
        $practitionersCount = (clone $statsQuery)->whereHas('role', function($q){ $q->where('name', 'practitioner'); })->count();
        $allUsersCount = $statsQuery->count();

        // Roles for adding users and filtering
        $rolesQuery = Role::query();
        if (Auth::user()->role->name == 'admin') {
            $rolesQuery->where('name', '!=', 'system-admin');
        } elseif (Auth::user()->role->name === 'doctor') {
            $rolesQuery->whereNotIn('name', ['system-admin', 'admin']);
        } elseif (Auth::user()->role->name === 'practitioner') {
            $rolesQuery->whereNotIn('name', ['system-admin', 'admin', 'doctor', 'practitioner']);
        }
        $roles = $rolesQuery->get();

        // Organizations list for filter
        if (Auth::user()->role->name === 'system-admin') {
            $organizations = Organization::all();
        } else {
            // Non-system admins can only "filter" by their own organization (effectively no filter choice)
            $organizations = Organization::where('id', Auth::user()->organization_id)->get();
        }

        return view('livewire.users.users-livewire', [
            'users' => $users,
            'roles' => $roles,
            'organizations' => $organizations,
            'doctorsCount' => $doctorsCount,
            'mothersCount' => $mothersCount,
            'practitionersCount' => $practitionersCount,
            'allUsersCount' => $allUsersCount,
        ]);
    }
}
