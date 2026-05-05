<?php

namespace App\Livewire\Users;

use App\Models\Area;
use App\Models\District;
use App\Models\Role;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class UserEditLivewire extends Component
{
    use LivewireAlert;

    public $modal = false;

    public $user;

    public $role;

    public $role_id;

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

    public $traditional_authority;

    public $last_normal_menstrual_period_date;

    public $areaModal = false;

    public $new_area_name;

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

    public function create()
    {
        $this->modal = true;

    }

    public function mount($role, $user_id)
    {
        $loggedInUser = auth()->user();
        $this->user = User::findOrFail($user_id);

        // Authorization check: Must be system admin or from the same organization
        if (!$loggedInUser->isSystemAdmin()) {
            if ($loggedInUser->organization_id !== $this->user->organization_id) {
                return redirect()->route('access-denied');
            }
            
            // Pharmacy Admin restriction check
            if ($loggedInUser->isPharmacyAdmin() && !in_array($role, ['practitioner', 'admin'])) {
                return redirect()->route('access-denied');
            }
        }

        $role_model = Role::where('name', $role)->first();
        $this->role_id = $role_model->id;
        $this->role = $role;
        
        $this->name = $this->user->name;
        $this->email = $this->user->email;
        $this->phone = $this->user->phone;
        $this->date_of_birth = $this->user->date_of_birth;
        $this->marital_status = $this->user->marital_status;
        $this->religion = $this->user->religion;
        $this->level_of_education = $this->user->level_of_education;
        $this->occupation = $this->user->occupation;
        $this->age = $this->user->age;
        $this->next_of_kin = $this->user->next_of_kin;
        $this->next_of_kin_mobile = $this->user->next_of_kin_mobile;
        $this->address = $this->user->address;
        $this->district_id = $this->user->district_id;
        $this->area_id = $this->user->area_id;
        $this->traditional_authority = $this->user->traditional_authority;
        // $this->last_normal_menstrual_period_date = $this->user->last_normal_menstrual_period_date;
        $this->height = $this->user->height;
        $this->legOrSpine = $this->user->leg_or_spine;
        $this->deformity = $this->user->deformity;
        $this->deliveries = $this->user->deliveries;
        $this->abortions = $this->user->abortions;
        $this->stillBirths = $this->user->still_births;

        $this->multiple = $this->user->multiple;
        $this->cSection = $this->user->c_section;
        $this->vacum = $this->user->vacum;
        $this->tuberculosis = $this->user->tuberculosis;
        $this->asthma = $this->user->asthma;
        $this->menstrualCycle = $this->user->menstrual_cycle;

    }

    public function updatedDateOfBirth()
    {
        if ($this->date_of_birth) {
            $this->age = Carbon::parse($this->date_of_birth)->age;
        }
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
                    'name' => ['required', 'string', 'max:255'],
                    'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
                    // 'password' => ['required', 'string', 'min:8', 'confirmed'],
                ]);
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

                $this->user->update([
                    'role_id' => $this->role_id,
                    'name' => $this->name,
                    // 'email' => $this->generateUniqueEmail($this->name),
                    // 'password' => Hash::make(StandardData::generatePassword()),
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
                ]);
                // dd($this->stillBirths,$this->cSection,$this->vacum,$this->user);

                $this->alert('success', 'Mother Updated Successfully');
                sleep(5);

                return redirect(route('users'));
                break;
        }

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

        return view('livewire.users.user-edit-livewire', [
            'districts' => District::all(),
            'areas' => $this->district_id ? Area::where('district_id', $this->district_id)->get() : [],
        ]);
    }
}
