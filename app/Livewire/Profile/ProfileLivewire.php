<?php

namespace App\Livewire\Profile;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\WithFileUploads;

class ProfileLivewire extends Component
{
    use WithFileUploads;
    use LivewireAlert;
    public $name;
    public $email;
    public $current_password;
    public $new_password;
    public $photo;
    public $user;

    // public $description;
    public $occupation;

    // Organization details
    public $org_name;
    public $org_email;
    public $org_phone;
    public $org_website;
    public $org_address;
    public $org_description;
    public $is_pharmacy;

    public function mount()
    {
        $this->user = User::with('organization')->find(Auth::id());
        $this->name = $this->user->name;
        $this->email = $this->user->email;
        $this->occupation = $this->user->occupation;

        if ($this->user->organization) {
            $this->org_name = $this->user->organization->name;
            $this->org_email = $this->user->organization->email;
            $this->org_phone = $this->user->organization->phone;
            $this->org_website = $this->user->organization->website;
            $this->org_address = $this->user->organization->address;
            $this->org_description = $this->user->organization->description;
            $this->is_pharmacy = (bool) $this->user->organization->is_pharmacy;
        }
    }

    public function remove()
    {
        $user = User::find(Auth::id());
        Storage::disk('custom')->delete($user->profile_photo_path);
        $user->profile_photo_path = '';
        $user->save();
        $this->alert('success', 'Removed');
    }

    public function updatedPhoto()
    {

        $this->validate([
            'photo' => 'image',
        ]);
        if (Auth::user()->profile_photo_path = '') {
            $file = $this->photo->store('profile');
            $user = User::find(Auth::id());
            $user->profile_photo_path = $file;
            $user->save();
            $this->alert('success', 'Uploaded');
        } else {
            Storage::disk('custom')->delete(Auth::user()->profile_photo_path);
            $file = $this->photo->store('profile');
            $user = User::find(Auth::id());
            $user->profile_photo_path = $file;
            $user->save();
            $this->alert('success', 'Uploaded');
        }
    }

    public function update()
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'occupation' => 'nullable|string|max:255',
        ];

        $user = User::find(Auth::id());

        if ($user->isOrgAdmin() && $user->organization) {
            $rules = array_merge($rules, [
                'org_name' => 'required|string|max:255',
                'org_email' => 'nullable|email|max:255',
                'org_phone' => 'nullable|string|max:20',
                'org_website' => 'nullable|url|max:255',
                'org_address' => 'nullable|string|max:255',
                'org_description' => 'nullable|string|max:1000',
                'is_pharmacy' => 'boolean',
            ]);
        }

        $this->validate($rules);

        if (!empty($this->current_password)) {
            $this->validate([
                'current_password' => 'required|string',
                'new_password' => 'required|string|min:8',
            ]);

            if (Hash::check($this->current_password, $user->password)) {
                $user->password = Hash::make($this->new_password);
            } else {
                $this->addError('current_password', 'Invalid current password');
                return;
            }
        }

        $user->name = $this->name;
        $user->email = $this->email;
        $user->occupation = $this->occupation;
        $user->save();

        if ($user->isOrgAdmin() && $user->organization) {
            $user->organization()->update([
                'name' => $this->org_name,
                'email' => $this->org_email,
                'phone' => $this->org_phone,
                'website' => $this->org_website,
                'address' => $this->org_address,
                'description' => $this->org_description,
                'is_pharmacy' => (bool) $this->is_pharmacy,
            ]);
        }

        $this->alert('success', 'Profile updated successfully');
    }

    public function render()
    {
        return view('livewire.profile.profile-livewire');
    }
}
