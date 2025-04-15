<?php

namespace App\Livewire\Organizations;

use App\Models\Organization;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;


class OrganizationUserCheckLivewire extends Component
{
    use LivewireAlert;
    public $modal = false;
    public $search = "";
    public $organization;
    public $name, $email, $phone, $website, $address, $description;
    public $liststatus = false;

    public $user;

    public $organizations =  [];


    public function mount(){
        $this->user = User::find(Auth::user()->id);
    }


    public function select_org($id){
        $this->organization = Organization::findOrFail($id);
        $this->search = "";
        $this->organizations = [];
        $this->liststatus = false;

    }

    public function remove_org(){
            $this->organization = null;
    }

    public function get_all_orgs()
    {

        if ($this->liststatus == false) {
            $this->liststatus = true;
            $this->search = "";
            $this->organizations = Organization::all();
            // $this->reset();
        } else {
            // $this->organizations = [];
            $this->search = "";
            $this->updatedSearch();
            $this->liststatus = false;
        }
    }


    public function store()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|numeric|min:10',
            'website' => 'nullable|url',
            'address' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:500',
        ]);


        Organization::create([
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'website' => $this->website,
            'address' => $this->address,
            'description' => $this->description,
        ]);



        $this->alert(
            'success',
            'Organization created successfully'
        );

    }


    public function save(){
        $user = User::find(Auth::user()->id);
        $user->organization_id = $this->organization->id;
        $user->save();

        // $this->user = $user;
        // $this->cancel();

        $this->alert('success', 'Organization successfully selected');



    }

    public function undo(){
        $user = User::find(Auth::user()->id);
        $user->organization_id = null;
        $user->save();
        $this->cancel();
        $this->user = $user;

    }


    public function updatedSearch()
    {
        if (strlen($this->search) > 1) {
            $this->liststatus = false;
            $this->organizations = Organization::where('name', 'like', '%' . $this->search . '%')->limit(10)->get();
        } else {
            $this->organizations = [];
        }
    }


    public function create($id = null)
    {
        if (empty($id)) {

            $this->modal = true;
        } else {
            $this->organization = Organization::findOrFail($id);
            $this->name = $this->organization->name;
            $this->email = $this->organization->email;
            $this->phone = $this->organization->phone;
            $this->website = $this->organization->website;
            $this->address = $this->organization->address;
            $this->description = $this->organization->description;
            $this->modal = true;
        }
    }

    public function cancel()
    {
        $this->reset([
            'modal',
            'search',
            'organization',
            'name',
            'email',
            'phone',
            'website',
            'address',
            'description',



        ]);
    }
    public function render()
    {
        return view('livewire.organizations.organization-user-check-livewire')->layout('layouts.blank');
    }
}
