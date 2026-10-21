<?php

namespace App\Livewire\Districts;

use App\Models\Area;
use App\Models\District;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Validation\Rule;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use Livewire\WithPagination;

class DistrictsAreasLivewire extends Component
{
    use LivewireAlert;
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $activeTab = 'districts'; // 'districts' or 'areas'

    // DISTRICT CRUD PROPERTIES
    public $districtModal = false;

    public $confirmDeleteDistrictModal = false;

    public $editingDistrictId = null;

    public $district_name = '';

    public $districtSearch = '';

    public $districtToDelete = null;

    // AREA CRUD PROPERTIES
    public $areaModal = false;

    public $confirmDeleteAreaModal = false;

    public $editingAreaId = null;

    public $area_district_id = '';

    public $area_name = '';

    public $areaSearch = '';

    public $areaDistrictFilter = '';

    public $areaToDelete = null;

    public function mount(): void
    {
        $user = auth()->user();
        if (! $user || ! $user->isSystemAdmin()) {
            abort(403, 'Unauthorized. Only System Administrators can manage geographical districts and areas.');
        }
    }

    public function setTab(string $tab): void
    {
        $this->activeTab = in_array($tab, ['districts', 'areas']) ? $tab : 'districts';
    }

    public function updatingDistrictSearch(): void
    {
        $this->resetPage('districts_page');
    }

    public function updatingAreaSearch(): void
    {
        $this->resetPage('areas_page');
    }

    public function updatingAreaDistrictFilter(): void
    {
        $this->resetPage('areas_page');
    }

    // ==========================================
    // DISTRICT ACTIONS
    // ==========================================

    public function createDistrict(): void
    {
        $this->editingDistrictId = null;
        $this->district_name = '';
        $this->resetErrorBag();
        $this->districtModal = true;
    }

    public function editDistrict(int $id): void
    {
        $district = District::findOrFail($id);
        $this->editingDistrictId = $district->id;
        $this->district_name = $district->name;
        $this->resetErrorBag();
        $this->districtModal = true;
    }

    public function saveDistrict(): void
    {
        $uniqueRule = Rule::unique('districts', 'name');
        if ($this->editingDistrictId) {
            $uniqueRule->ignore($this->editingDistrictId);
        }

        $this->validate([
            'district_name' => ['required', 'string', 'max:255', $uniqueRule],
        ], [
            'district_name.required' => 'The district name is required.',
            'district_name.unique' => 'A district with this name already exists.',
        ]);

        $name = trim($this->district_name);

        if ($this->editingDistrictId) {
            $district = District::findOrFail($this->editingDistrictId);
            $district->update(['name' => $name]);
            $this->alert('success', "District '{$district->name}' updated successfully.");
        } else {
            $district = District::create(['name' => $name]);
            $this->alert('success', "District '{$district->name}' created successfully.");
        }

        $this->cancelDistrict();
    }

    public function cancel(): void
    {
        $this->cancelDistrict();
        $this->cancelDeleteDistrict();
        $this->cancelArea();
        $this->cancelDeleteArea();
    }

    public function cancelDistrict(): void
    {
        $this->districtModal = false;
        $this->editingDistrictId = null;
        $this->district_name = '';
        $this->resetErrorBag();
    }

    public function confirmDeleteDistrict(int $id): void
    {
        $district = District::withCount(['areas', 'users', 'organizations'])->findOrFail($id);
        $this->districtToDelete = [
            'id' => $district->id,
            'name' => $district->name,
            'areas_count' => $district->areas_count,
            'users_count' => $district->users_count,
            'organizations_count' => $district->organizations_count,
        ];
        $this->confirmDeleteDistrictModal = true;
    }

    public function deleteDistrict(): void
    {
        if (! $this->districtToDelete) {
            return;
        }

        $district = District::findOrFail($this->districtToDelete['id']);
        $name = $district->name;
        $district->delete();

        $this->confirmDeleteDistrictModal = false;
        $this->districtToDelete = null;
        $this->alert('success', "District '{$name}' deleted successfully.");
    }

    public function cancelDeleteDistrict(): void
    {
        $this->confirmDeleteDistrictModal = false;
        $this->districtToDelete = null;
    }

    public function viewAreasForDistrict(int $districtId): void
    {
        $this->areaDistrictFilter = (string) $districtId;
        $this->areaSearch = '';
        $this->activeTab = 'areas';
        $this->resetPage('areas_page');
    }

    // ==========================================
    // AREA ACTIONS
    // ==========================================

    public function createArea(?int $districtId = null): void
    {
        $this->editingAreaId = null;
        $this->area_name = '';
        $this->area_district_id = $districtId ? (string) $districtId : ($this->areaDistrictFilter ?: '');
        $this->resetErrorBag();
        $this->areaModal = true;
    }

    public function editArea(int $id): void
    {
        $area = Area::findOrFail($id);
        $this->editingAreaId = $area->id;
        $this->area_district_id = (string) $area->district_id;
        $this->area_name = $area->name;
        $this->resetErrorBag();
        $this->areaModal = true;
    }

    public function saveArea(): void
    {
        $uniqueRule = Rule::unique('areas', 'name')->where('district_id', $this->area_district_id);
        if ($this->editingAreaId) {
            $uniqueRule->ignore($this->editingAreaId);
        }

        $this->validate([
            'area_district_id' => 'required|exists:districts,id',
            'area_name' => ['required', 'string', 'max:255', $uniqueRule],
        ], [
            'area_district_id.required' => 'Please select a parent district.',
            'area_district_id.exists' => 'The selected district is invalid.',
            'area_name.required' => 'The area name is required.',
            'area_name.unique' => 'An area with this name already exists in the selected district.',
        ]);

        $name = trim($this->area_name);

        if ($this->editingAreaId) {
            $area = Area::findOrFail($this->editingAreaId);
            $area->update([
                'district_id' => $this->area_district_id,
                'name' => $name,
            ]);
            $this->alert('success', "Area '{$area->name}' updated successfully.");
        } else {
            $area = Area::create([
                'district_id' => $this->area_district_id,
                'name' => $name,
            ]);
            $this->alert('success', "Area '{$area->name}' created successfully.");
        }

        $this->cancelArea();
    }

    public function cancelArea(): void
    {
        $this->areaModal = false;
        $this->editingAreaId = null;
        $this->area_name = '';
        $this->area_district_id = '';
        $this->resetErrorBag();
    }

    public function confirmDeleteArea(int $id): void
    {
        $area = Area::with(['district'])->withCount(['users', 'organizations'])->findOrFail($id);
        $this->areaToDelete = [
            'id' => $area->id,
            'name' => $area->name,
            'district_name' => $area->district->name ?? 'Unknown',
            'users_count' => $area->users_count,
            'organizations_count' => $area->organizations_count,
        ];
        $this->confirmDeleteAreaModal = true;
    }

    public function deleteArea(): void
    {
        if (! $this->areaToDelete) {
            return;
        }

        $area = Area::findOrFail($this->areaToDelete['id']);
        $name = $area->name;
        $area->delete();

        $this->confirmDeleteAreaModal = false;
        $this->areaToDelete = null;
        $this->alert('success', "Area '{$name}' deleted successfully.");
    }

    public function cancelDeleteArea(): void
    {
        $this->confirmDeleteAreaModal = false;
        $this->areaToDelete = null;
    }

    // ==========================================
    // RENDER
    // ==========================================

    public function render()
    {
        // Global Metrics
        $totalDistricts = District::count();
        $totalAreas = Area::count();
        $totalOrganizationsMapped = Organization::whereNotNull('district_id')->count();
        $totalUsersMapped = User::whereNotNull('district_id')->count();

        // Districts Query with Counts
        $districtsQuery = District::withCount(['areas', 'users', 'organizations']);
        if (! empty($this->districtSearch)) {
            $search = trim($this->districtSearch);
            $districtsQuery->where('name', 'like', "%{$search}%");
        }
        $districts = $districtsQuery->orderBy('name')->paginate(10, ['*'], 'districts_page');

        // Areas Query with Counts
        $areasQuery = Area::with(['district'])->withCount(['users', 'organizations']);
        if (! empty($this->areaSearch)) {
            $search = trim($this->areaSearch);
            $areasQuery->where('name', 'like', "%{$search}%");
        }
        if (! empty($this->areaDistrictFilter)) {
            $areasQuery->where('district_id', $this->areaDistrictFilter);
        }
        $areas = $areasQuery->orderBy('name')->paginate(15, ['*'], 'areas_page');

        // All districts for dropdown selection
        $allDistricts = District::orderBy('name')->get();

        return view('livewire.districts.districts-areas-livewire', [
            'totalDistricts' => $totalDistricts,
            'totalAreas' => $totalAreas,
            'totalOrganizationsMapped' => $totalOrganizationsMapped,
            'totalUsersMapped' => $totalUsersMapped,
            'districts' => $districts,
            'areas' => $areas,
            'allDistricts' => $allDistricts,
        ]);
    }
}
