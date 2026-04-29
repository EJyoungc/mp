<?php

namespace App\Livewire\Users;

use App\Helper\StandardData;
use App\Models\Area;
use App\Models\District;
use App\Models\Role;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class UserCreateLivewire extends Component
{
    use LivewireAlert;

    public $modal = false;

    public $role;

    public $role_id;

    public $user_id;

    public $name;

    public $email;

    public $password;

    public $phone;

    public $date_of_birth;

    public $marital_status;

    public $religion;

    public $level_of_education;

    public $occupation;

    public $age;

    public $next_of_kin;

    public $next_of_kin_mobile;

    public $address;

    public $district_id;

    public $area_id;

    public $areaModal = false;

    public $new_area_name;

    public $traditional_authority;

    public $last_normal_menstrual_period_date;

    public $height;

    public $legOrSpine;

    public $deformity;

    public $deliveries;

    public $abortions;

    public $stillBirths;

    public $cSection;

    public $vacum;

    public $multiple;

    public $tuberculosis;

    public $asthma;

    public $menstrualCycle;

    public function generateUniqueEmail($name, $domain = 'mother.com')
    {
        // Format the name to be used as part of the email address (e.g., "John Doe" becomes "john.doe")
        $username = Str::slug($name, '.');
        // Generate a unique string (you could use timestamp or a random string)
        $uniquePart = Str::random(6); // generates a random 6-character string
        // Combine to form the email address
        $email = "{$username}.{$uniquePart}@{$domain}";

        return $email;
    }

    public function mount($role, $user_id = null)
    {
        if (Auth::user()->isPharmacyAdmin() && $role !== 'practitioner') {
            return redirect()->route('access-denied');
        }

        $this->role = $role;
        $role = Role::where('name', $role)->first();
        $this->role_id = $role->id;
        if (! empty($user_id)) {
            $this->User::findOrFail($user_id);
        }
    }

    public function UpdatedDateOfBirth()
    {
        $this->age = Carbon::parse($this->date_of_birth)->age;
    }

    public function updatedDistrictId()
    {
        $this->area_id = null;
    }

    public function addArea()
    {
        $this->validate([
            'district_id' => 'required|exists:districts,id',
        ]);
        $this->new_area_name = '';
        $this->areaModal = true;
    }

    public function storeArea()
    {
        $this->validate([
            'district_id' => 'required|exists:districts,id',
            'new_area_name' => 'required|string|max:255',
        ]);

        $area = Area::create([
            'district_id' => $this->district_id,
            'name' => $this->new_area_name,
        ]);

        $this->area_id = $area->id;
        $this->areaModal = false;
        $this->alert('success', 'New area added successfully');
    }

    public function store()
    {

        switch ($this->role) {
            case 'admin':
                $this->validate([
                    'name' => 'required|string|max:255',
                    'email' => 'required|string|email|max:255|unique:users',
                    'phone' => 'required|numeric',

                    // 'password' => ['required', 'string', 'min:8', 'confirmed'],
                ]);

                User::create([
                    'role_id' => $this->role_id,
                    'name' => $this->name,
                    'email' => $this->email,
                    'password' => Hash::make(StandardData::generatePassword()),
                    'phone' => $this->phone,
                    'organization_id' => Auth::user()->organization_id,
                    'organization_verify' => 'verified',

                ]);
                $this->alert('success', 'User created successfully');
                sleep(5);

                return redirect(route('users'));
                break;
            case 'doctor':
                $this->validate([
                    'name' => 'required|string|max:255',
                    'email' => 'required|string|email|max:255|unique:users',
                    'phone' => 'required|numeric',

                    // 'password' => ['required', 'string', 'min:8', 'confirmed'],
                ]);

                User::create([
                    'role_id' => $this->role_id,
                    'name' => $this->name,
                    'email' => $this->email,
                    'password' => Hash::make(StandardData::generatePassword()),
                    'phone' => $this->phone,
                    'organization_id' => Auth::user()->organization_id,
                    'organization_verify' => 'verified',

                ]);
                $this->alert('success', 'User created successfully');
                sleep(5);

                return redirect(route('users'));
                break;
            case 'practitioner':
                $this->validate([
                    'name' => 'required|string|max:255',
                    'email' => 'required|string|email|max:255|unique:users',
                    'phone' => 'required|numeric',

                    // 'password' => ['required', 'string', 'min:8', 'confirmed'],
                ]);

                User::create([
                    'role_id' => $this->role_id,
                    'name' => $this->name,
                    'email' => $this->email,
                    'password' => Hash::make(StandardData::generatePassword()),
                    'phone' => $this->phone,
                    'organization_id' => Auth::user()->organization_id,
                    'organization_verify' => 'verified',
                ]);
                $this->alert('success', 'User created successfully');
                sleep(5);

                return redirect(route('users'));
                break;
            case 'mother':

                $this->validate([
                    'name' => 'required | string | max:255',

                    // 'email' => 'required | string | email | max:255 | unique:users',
                    'date_of_birth' => 'required | date',
                    'marital_status' => 'required',
                    'religion' => 'required',
                    'level_of_education' => 'required',
                    'occupation' => 'required',
                    'age' => 'required',
                    'next_of_kin' => 'required',
                    'next_of_kin_mobile' => 'required',
                    'address' => 'required',
                    'district_id' => 'required|exists:districts,id',
                    'area_id' => 'required|exists:areas,id',
                    'phone' => 'required',
                    'traditional_authority' => 'required | string| max:255',
                    // 'last_normal_menstrual_period_date' => 'required | date',
                    'height' => 'required|numeric|min:0',
                    'legOrSpine' => 'required',
                    'deformity' => 'required',
                    'deliveries' => 'required|integer|min:0|max:10',
                    'abortions' => 'required|integer|min:0|max:3',
                    'stillBirths' => 'required',
                    'cSection' => 'required',
                    'vacum' => 'required',
                    'multiple' => 'required',
                    'tuberculosis' => 'required',
                    'asthma' => 'required',
                    'menstrualCycle' => 'required',

                ]);
                //  dd($this);
                $user = User::create([
                    'role_id' => $this->role_id,
                    'name' => $this->name,
                    'email' => $this->generateUniqueEmail($this->name),
                    'password' => Hash::make(StandardData::generatePassword()),
                    'date_of_birth' => $this->date_of_birth,
                    'marital_status' => $this->marital_status,
                    'religion' => $this->religion,
                    'level_of_education' => $this->level_of_education,
                    'occupation' => $this->occupation,
                    'age' => $this->age,
                    'next_of_kin' => $this->next_of_kin,
                    'next_of_kin_mobile' => $this->next_of_kin_mobile,
                    'address' => $this->address,
                    'district_id' => $this->district_id,
                    'area_id' => $this->area_id,
                    'phone' => $this->phone,
                    'traditional_authority' => $this->traditional_authority,
                    // 'last_normal_menstrual_period_date' => $this->last_normal_menstrual_period_date,
                    'height' => $this->height,
                    'leg_or_spine' => $this->legOrSpine,
                    'deformity' => $this->deformity,
                    'deliveries' => $this->deliveries,
                    'abortions' => $this->abortions,
                    'still_births' => $this->stillBirths,
                    'c_section' => $this->cSection,
                    'vacum' => $this->vacum,
                    'multiple' => $this->multiple,
                    'tuberculosis' => $this->tuberculosis,
                    'asthma' => $this->asthma,
                    'menstrual_cycle' => $this->menstrualCycle,
                    'organization_id' => Auth::user()->organization_id,
                    'organization_verify' => 'verified',

                ]);

                $this->alert('success', 'Mother Created Successfully');
                sleep(5);

                return redirect(route('users'));

                break;
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
            'areaModal',
        ]);
    }

    public function render()
    {
        return view('livewire.users.user-create-livewire', [
            'districts' => District::all(),
            'areas' => $this->district_id ? Area::where('district_id', $this->district_id)->get() : [],
        ]);
    }
}
