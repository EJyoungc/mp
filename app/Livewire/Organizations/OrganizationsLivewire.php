<?php

namespace App\Livewire\Organizations;

use App\Models\Area;
use App\Models\District;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use Livewire\WithPagination;

class OrganizationsLivewire extends Component
{
    use LivewireAlert;
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $modal = false;

    public $deleteModal = false;

    public $confirmingDeleteId = null;

    public $confirmingDeleteName = '';

    public $organization;

    public $name;

    public $email;

    public $phone;

    public $website;

    public $address;

    public $district_id;

    public $area_id;

    public $description;

    public $is_pharmacy = false;

    // Search and filter properties
    public $search = '';

    public $type_filter = '';

    public $district_filter = '';

    public $perPage = 10;

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingTypeFilter(): void
    {
        $this->resetPage();
    }

    public function updatingDistrictFilter(): void
    {
        $this->resetPage();
    }

    public function create($id = null): void
    {
        $user = auth()->user();
        if (empty($id)) {
            // Only System Admin can create new orgs from here (others create via register)
            if (! $user->isSystemAdmin()) {
                $this->alert('error', 'Unauthorized action.');

                return;
            }
            $this->reset([
                'organization',
                'name',
                'email',
                'phone',
                'website',
                'address',
                'district_id',
                'area_id',
                'description',
                'is_pharmacy',
            ]);
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
            $this->is_pharmacy = (bool) $this->organization->is_pharmacy;
            $this->modal = true;
        }
    }

    public function store(): void
    {
        $user = auth()->user();
        $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'website' => 'nullable|url|max:255',
            'address' => 'nullable|string|max:255',
            'district_id' => 'required|exists:districts,id',
            'area_id' => 'required|exists:areas,id',
            'description' => 'nullable|string|max:500',
            'is_pharmacy' => 'boolean',
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
                'is_pharmacy' => $this->is_pharmacy,
                'owner_id' => $user->id,
            ]);

            $this->alert('success', 'Organization created successfully');
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
                'is_pharmacy' => $this->is_pharmacy,
            ]);

            $this->alert('success', 'Organization updated successfully');
            $this->cancel();
        }
    }

    public function confirmDelete(int $id): void
    {
        $user = auth()->user();
        $org = Organization::findOrFail($id);

        if (! $user->isSystemAdmin() && $org->owner_id !== $user->id) {
            $this->alert('error', 'Unauthorized action.');

            return;
        }

        $this->confirmingDeleteId = $org->id;
        $this->confirmingDeleteName = $org->name;
        $this->deleteModal = true;
    }

    public function deleteConfirmed(): void
    {
        $user = auth()->user();

        if (! $this->confirmingDeleteId) {
            return;
        }

        $org = Organization::findOrFail($this->confirmingDeleteId);

        if (! $user->isSystemAdmin() && $org->owner_id !== $user->id) {
            $this->alert('error', 'Unauthorized action.');
            $this->deleteModal = false;

            return;
        }

        DB::transaction(function () use ($org) {
            // Organization::delete triggers booted deleting callback which cleans up all related tables
            $org->delete();
        });

        $this->alert('success', "Organization '{$org->name}' and all associated records have been permanently deleted.");
        $this->cancel();
    }

    public function updatedDistrictId(): void
    {
        $this->area_id = null;
    }

    public function cancel(): void
    {
        $this->reset([
            'modal',
            'deleteModal',
            'confirmingDeleteId',
            'confirmingDeleteName',
            'name',
            'email',
            'phone',
            'website',
            'address',
            'district_id',
            'area_id',
            'description',
            'organization',
            'is_pharmacy',
        ]);
    }

    public function render()
    {
        $user = auth()->user();
        $baseQuery = Organization::query();

        if (! $user->isSystemAdmin()) {
            $baseQuery->where('owner_id', $user->id);
        }

        // Stats calculation
        $totalOrgs = (clone $baseQuery)->count();
        $totalClinics = (clone $baseQuery)->where('is_pharmacy', false)->count();
        $totalPharmacies = (clone $baseQuery)->where('is_pharmacy', true)->count();

        // Staff and mothers count across available organizations
        $orgIds = (clone $baseQuery)->pluck('id');
        $totalStaff = User::whereIn('organization_id', $orgIds)
            ->whereHas('role', function ($q) {
                $q->where('name', '!=', 'mother');
            })->count();
        $totalMothers = User::whereIn('organization_id', $orgIds)
            ->whereHas('role', function ($q) {
                $q->where('name', 'mother');
            })->count();

        // Main filter query
        $query = (clone $baseQuery)
            ->with(['district', 'area', 'owner'])
            ->withCount([
                'members as staff_count' => function ($q) {
                    $q->whereHas('role', function ($r) {
                        $r->where('name', '!=', 'mother');
                    });
                },
                'members as mothers_count' => function ($q) {
                    $q->whereHas('role', function ($r) {
                        $r->where('name', 'mother');
                    });
                },
            ]);

        // Search
        if (! empty($this->search)) {
            $search = trim($this->search);
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('address', 'like', "%{$search}%")
                    ->orWhereHas('district', function ($dq) use ($search) {
                        $dq->where('name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('area', function ($aq) use ($search) {
                        $aq->where('name', 'like', "%{$search}%");
                    });
            });
        }

        // Type filter
        if ($this->type_filter === 'pharmacy') {
            $query->where('is_pharmacy', true);
        } elseif ($this->type_filter === 'clinic') {
            $query->where('is_pharmacy', false);
        }

        // District filter
        if (! empty($this->district_filter)) {
            $query->where('district_id', $this->district_filter);
        }

        $orgs = $query->latest()->paginate($this->perPage);

        return view('livewire.organizations.organizations-livewire', [
            'orgs' => $orgs,
            'districts' => District::orderBy('name')->get(),
            'areas' => $this->district_id ? Area::where('district_id', $this->district_id)->orderBy('name')->get() : [],
            'stats' => [
                'total' => $totalOrgs,
                'clinics' => $totalClinics,
                'pharmacies' => $totalPharmacies,
                'staff' => $totalStaff,
                'mothers' => $totalMothers,
            ],
        ]);
    }
}
