<?php

namespace App\Livewire\Organizations;

use App\Models\Organization;
use App\Models\User;
use Livewire\Component;

class OrganizationUsersLivewire extends Component
{

    public $modal = false;
    public $id;
    public $name;

    public function mount($id)
    {
        $this->id = $id;
        $this->name = Organization::findOrFail($id)->name;
    }

    public function create(){
        $this->modal = true;

    }

    public function cancel(){
        $this->reset([
            'modal',

        ]);
    }
    public function render()
    {

        $users = User::where('organization_id', $this->id)->get();
        return view('livewire.organizations.organization-users-livewire')->with('users',$users);
    }
}
