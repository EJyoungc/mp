<?php

namespace App\Livewire\Organizations;

use App\Models\Organization;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class OrganizationsLivewire extends Component
{

    use LivewireAlert;

    public $modal = false;
    public $user_modal = false;
    public $organization;
    public $name, $email, $phone, $website, $address, $description;



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


    public function store()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|numeric|min:10',
            'website' => 'nullable|url',
            'address' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:500',
        ]);

        if (empty($this->organization->id)) {

            Organization::create([
                'name' => $this->name,
                'email' => $this->email,
                'phone' => $this->phone,
                'website' => $this->website,
                'address' => $this->address,
                'description' => $this->description,
            ]);
            $this->cancel();
        } else {
            $this->organization->update([
                'name' => $this->name,
                'email' => $this->email,
                'phone' => $this->phone,
                'website' => $this->website,
                'address' => $this->address,
                'description' => $this->description,
            ]);
            $this->alert('success', 'Organization updated successfully');
            $this->cancel();
        }
    }

    public function cancel()
    {
        $this->reset([
            'modal',
            'name',
            'email',
            'phone',
            'website',
            'address',
            'description',
            'organization',
            'user_modal',

        ]);
    }
    public function render()
    {

        return view('livewire.organizations.organizations-livewire')->with('orgs', Organization::all());
    }
}
