<?php

namespace App\Livewire\Mothers;

use App\Helper\StandardData as SD;
use App\Models\Area;
use App\Models\District;
use App\Models\History;
use App\Models\MessageHistory;
use App\Models\Role;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class MothersManagementLivewire extends Component
{
    use LivewireAlert;
    use WithFileUploads;
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $search = '';

    public $perPage = 10;

    public $modal = false;

    public $areaModal = false;

    public $new_area_name;

    // Form fields - Personal & Contact
    public $mother_id;

    public $name;

    public $email;

    public $phone;

    public $date_of_birth;

    public $age;

    public $marital_status;

    public $religion;

    public $level_of_education;

    public $occupation;

    public $address;

    public $district_id;

    public $area_id;

    public $traditional_authority;

    public $next_of_kin;

    public $next_of_kin_mobile;

    // Form fields - Medical & Pregnancy History
    public $last_menstrual_cycle;

    public $infant_number = 1;

    public $height;

    public $legOrSpine = 'No';

    public $deformity = 'No';

    public $deliveries = 0;

    public $abortions = 0;

    public $stillBirths = 'No';

    public $cSection = 'No';

    public $vacum = 'No';

    public $multiple = 'No';

    public $tuberculosis = 'No';

    public $asthma = 'No';

    public $menstrualCycle = 'Regular';

    public function mount()
    {
        $user = auth()->user();
        if ($user->organization && $user->organization->is_pharmacy) {
            abort(403, 'Pharmacies are not authorized to manage mother records.');
        }
    }

    public function updatingSearch()
    {
        $this->resetPage();
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

    public function create()
    {
        $this->resetForm();
        $this->modal = true;
    }

    public function edit($id)
    {
        $this->resetForm();
        $this->mother_id = $id;
        $mother = User::findOrFail($id);

        // Map User fields
        $this->name = $mother->name;
        $this->email = $mother->email;
        $this->phone = $mother->phone;
        $this->date_of_birth = $mother->date_of_birth;
        $this->age = $mother->age;
        $this->marital_status = $mother->marital_status;
        $this->religion = $mother->religion;
        $this->level_of_education = $mother->level_of_education;
        $this->occupation = $mother->occupation;
        $this->address = $mother->address;
        $this->district_id = $mother->district_id;
        $this->area_id = $mother->area_id;
        $this->traditional_authority = $mother->traditional_authority;
        $this->next_of_kin = $mother->next_of_kin;
        $this->next_of_kin_mobile = $mother->next_of_kin_mobile;
        $this->height = $mother->height;
        $this->legOrSpine = $mother->leg_or_spine ?? 'No';
        $this->deformity = $mother->deformity ?? 'No';
        $this->deliveries = $mother->deliveries ?? 0;
        $this->abortions = $mother->abortions ?? 0;
        $this->stillBirths = $mother->still_births ?? 'No';
        $this->cSection = $mother->c_section ?? 'No';
        $this->vacum = $mother->vacum ?? 'No';
        $this->multiple = $mother->multiple ?? 'No';
        $this->tuberculosis = $mother->tuberculosis ?? 'No';
        $this->asthma = $mother->asthma ?? 'No';
        $this->menstrualCycle = $mother->menstrual_cycle ?? 'Regular';

        $history = History::where('mother_id', $id)->latest()->first();
        if ($history) {
            $this->last_menstrual_cycle = $history->last_menstrual_cycle;
            $this->infant_number = $history->infant_number;
        }

        $this->modal = true;
    }

    public function store()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'date_of_birth' => 'required|date',
            'marital_status' => 'required',
            'religion' => 'required',
            'level_of_education' => 'required',
            'occupation' => 'required',
            'next_of_kin' => 'required',
            'next_of_kin_mobile' => 'required',
            'address' => 'required',
            'district_id' => 'required|exists:districts,id',
            'area_id' => 'required|exists:areas,id',
            'traditional_authority' => 'required|string|max:255',
            'height' => 'required|numeric|min:0',
            'last_menstrual_cycle' => 'required|date',
            'deliveries' => 'required|integer|min:0|max:10',
            'abortions' => 'required|integer|min:0|max:3',
        ]);

        $user = auth()->user();

        $userData = [
            'name' => $this->name,
            'phone' => $this->phone,
            'date_of_birth' => $this->date_of_birth,
            'age' => $this->age ?? Carbon::parse($this->date_of_birth)->age,
            'marital_status' => $this->marital_status,
            'religion' => $this->religion,
            'level_of_education' => $this->level_of_education,
            'occupation' => $this->occupation,
            'address' => $this->address,
            'district_id' => $this->district_id,
            'area_id' => $this->area_id,
            'traditional_authority' => $this->traditional_authority,
            'next_of_kin' => $this->next_of_kin,
            'next_of_kin_mobile' => $this->next_of_kin_mobile,
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
        ];

        if ($this->mother_id) {
            $mother = User::findOrFail($this->mother_id);
            if ($this->email && $this->email !== $mother->email) {
                $userData['email'] = $this->email;
            }
            $mother->update($userData);

            History::updateOrCreate(
                ['mother_id' => $mother->id],
                [
                    'last_menstrual_cycle' => $this->last_menstrual_cycle,
                    'infant_number' => $this->infant_number ?? 1,
                    'organization_id' => $mother->organization_id,
                ]
            );

            $this->alert('success', 'Mother updated successfully');
        } else {
            // New Mother
            $userData['email'] = $this->email ?? $this->generateUniqueEmail($this->name);
            $userData['password'] = Hash::make('password');
            $userData['role_id'] = 4; // Mother role
            $userData['organization_id'] = $user->organization_id;
            $userData['organization_verify'] = 'verified';
            $userData['is_active'] = 1;

            $mother = User::create($userData);

            History::create([
                'mother_id' => $mother->id,
                'last_menstrual_cycle' => $this->last_menstrual_cycle,
                'infant_number' => $this->infant_number ?? 1,
                'organization_id' => $user->organization_id,
            ]);

            $this->alert('success', 'Mother registered successfully');
        }

        $this->cancel();
    }

    private function generateUniqueEmail($name)
    {
        $username = Str::slug($name, '.');

        return $username.'.'.Str::random(6).'@mother.com';
    }

    public function delete($id)
    {
        $mother = User::findOrFail($id);
        History::where('mother_id', $id)->delete();
        MessageHistory::where('mother_id', $id)->delete();
        $mother->delete();

        $this->alert('success', 'Mother record deleted');
    }

    public function cancel()
    {
        $this->resetForm();
        $this->modal = false;
        $this->areaModal = false;
    }

    private function resetForm()
    {
        $this->reset([
            'mother_id', 'name', 'email', 'phone', 'date_of_birth', 'age',
            'marital_status', 'religion', 'level_of_education', 'occupation',
            'address', 'district_id', 'area_id', 'traditional_authority', 'next_of_kin', 'next_of_kin_mobile',
            'last_menstrual_cycle', 'infant_number', 'height', 'legOrSpine',
            'deformity', 'deliveries', 'abortions', 'stillBirths', 'cSection',
            'vacum', 'multiple', 'tuberculosis', 'asthma', 'menstrualCycle',
        ]);
        $this->infant_number = 1;
        $this->legOrSpine = 'No';
        $this->deformity = 'No';
        $this->stillBirths = 'No';
        $this->cSection = 'No';
        $this->vacum = 'No';
        $this->multiple = 'No';
        $this->tuberculosis = 'No';
        $this->asthma = 'No';
        $this->menstrualCycle = 'Regular';
    }

    public function render()
    {
        $user = auth()->user();
        $query = User::where('role_id', 4);

        if (! $user->isSystemAdmin()) {
            $query->where('organization_id', $user->organization_id);
        }

        $query->when($this->search, function ($q) {
            $q->where(function ($sub) {
                $sub->where('name', 'like', '%'.$this->search.'%')
                    ->orWhere('phone', 'like', '%'.$this->search.'%');
            });
        });

        $mothers = $query->with(['organization', 'role', 'district', 'area'])->latest()->paginate($this->perPage);

        return view('livewire.mothers.mothers-management-livewire', [
            'mothers' => $mothers,
            'districts' => District::all(),
            'areas' => $this->district_id ? Area::where('district_id', $this->district_id)->get() : [],
            'religions' => SD::getReligions(),
            'educationLevels' => SD::getEducationLevels(),
            'maritalStatuses' => SD::getMaritalStatuses(),
        ]);
    }
}
