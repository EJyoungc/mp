<?php

namespace App\Livewire\Organizations;

use App\Models\Area;
use App\Models\District;
use App\Models\Organization;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class OrganizationsLivewire extends Component
{
    use LivewireAlert;

    public $modal = false;

    public $user_modal = false;

    public $organization;

    public $name;

    public $email;

    public $phone;

    public $website;

    public $address;

    public $district_id;

    public $area_id;

    public $description;

    public function create($id = null)
    {
        $user = auth()->user();
        if (empty($id)) {
            // Only System Admin can create new orgs from here (others create via register)
            if (! $user->isSystemAdmin()) {
                $this->alert('error', 'Unauthorized action.');

                return;
            }
            $this->modal = true;
        } else {
            $this->organization = Organization::findOrFail($id);

            // Authorization check: Only System Admin or Organization Owner
            if (! $user->isSystemAdmin() && $this->organization->owner_id !== $user->id) {
                $this->alert('error', 'Unauthorized access.');

                return;
            }

            $this->name = $this->organization->name;
            $this->email = $this->organization->email;
            $this->phone = $this->organization->phone;
            $this->website = $this->organization->website;
            $this->address = $this->organization->address;
            $this->district_id = $this->organization->district_id;
            $this->area_id = $this->organization->area_id;
            $this->description = $this->organization->description;
            $this->modal = true;
        }
    }

    public function store()
    {
        $user = auth()->user();
        $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255', // Now nullable
            'phone' => 'nullable|numeric|min:10',
            'website' => 'nullable|url',
            'address' => 'nullable|string|max:255',
            'district_id' => 'required|exists:districts,id',
            'area_id' => 'required|exists:areas,id',
            'description' => 'nullable|string|max:500',
        ]);

        if (empty($this->organization->id)) {
            // Only System Admin can create
            if (! $user->isSystemAdmin()) {
                $this->alert('error', 'Unauthorized action.');

                return;
            }

            Organization::create([
                'name' => $this->name,
                'email' => $this->email,
                'phone' => $this->phone,
                'website' => $this->website,
                'address' => $this->address,
                'district_id' => $this->district_id,
                'area_id' => $this->area_id,
                'description' => $this->description,
                'owner_id' => $user->id, // Default to current user if created here
            ]);
            $this->cancel();
        } else {
            // Authorization check
            if (! $user->isSystemAdmin() && $this->organization->owner_id !== $user->id) {
                $this->alert('error', 'Unauthorized action.');

                return;
            }

            $this->organization->update([
                'name' => $this->name,
                'email' => $this->email,
                'phone' => $this->phone,
                'website' => $this->website,
                'address' => $this->address,
                'district_id' => $this->district_id,
                'area_id' => $this->area_id,
                'description' => $this->description,
            ]);
            $this->alert('success', 'Organization updated successfully');
            $this->cancel();
        }
    }

    public function updatedDistrictId()
    {
        $this->area_id = null;
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
            'district_id',
            'area_id',
            'description',
            'organization',
            'user_modal',
        ]);
    }

    public function render()
    {
        $user = auth()->user();
        $query = Organization::query();

        if (! $user->isSystemAdmin()) {
            $query->where('owner_id', $user->id);
        }

        return view('livewire.organizations.organizations-livewire', [
            'orgs' => $query->get(),
            'districts' => District::all(),
            'areas' => $this->district_id ? Area::where('district_id', $this->district_id)->get() : [],
        ]);
    }
}
