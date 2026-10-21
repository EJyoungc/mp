<?php

namespace App\Livewire\Organizations;

use App\Mail\UserCredentialsMail;
use App\Models\Area;
use App\Models\District;
use App\Models\Organization;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
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

    public $credentialsModal = false;

    public $createdUserCredentials = null;

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

    // Quick Add District and Area properties
    public $districtModal = false;

    public $new_district_name = '';

    public $areaModal = false;

    public $new_area_name = '';

    // Optional Lead Administrator Creation fields
    public $create_admin = false;

    public $admin_name = '';

    public $admin_email = '';

    public $admin_phone = '';

    public $admin_password = '';

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

    public function generateAdminPassword(): void
    {
        $this->admin_password = Str::random(10);
    }

    public function addDistrict(): void
    {
        $this->new_district_name = '';
        $this->districtModal = true;
    }

    public function storeDistrict(): void
    {
        $this->validate([
            'new_district_name' => 'required|string|max:255|unique:districts,name',
        ]);

        $district = District::create([
            'name' => trim($this->new_district_name),
        ]);

        $this->district_id = $district->id;
        $this->area_id = null;
        $this->districtModal = false;
        $this->alert('success', "District '{$district->name}' added successfully.");
    }

    public function addArea(): void
    {
        $this->validate([
            'district_id' => 'required|exists:districts,id',
        ]);
        $this->new_area_name = '';
        $this->areaModal = true;
    }

    public function storeArea(): void
    {
        $this->validate([
            'district_id' => 'required|exists:districts,id',
            'new_area_name' => 'required|string|max:255',
        ]);

        $area = Area::create([
            'district_id' => $this->district_id,
            'name' => trim($this->new_area_name),
        ]);

        $this->area_id = $area->id;
        $this->areaModal = false;
        $this->alert('success', "Area '{$area->name}' added successfully.");
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
                'create_admin',
                'admin_name',
                'admin_email',
                'admin_phone',
                'admin_password',
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
            $this->create_admin = false;
            $this->modal = true;
        }
    }

    public function store(): void
    {
        $user = auth()->user();

        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'website' => 'nullable|url|max:255',
            'address' => 'nullable|string|max:255',
            'district_id' => 'required|exists:districts,id',
            'area_id' => 'required|exists:areas,id',
            'description' => 'nullable|string|max:500',
            'is_pharmacy' => 'boolean',
        ];

        if ($this->create_admin && empty($this->organization->id)) {
            $rules['admin_name'] = 'required|string|max:255';
            $rules['admin_email'] = 'required|email|max:255|unique:users,email';
            $rules['admin_phone'] = 'nullable|string|max:20';
            $rules['admin_password'] = 'nullable|string|min:6';
        }

        $this->validate($rules);

        if (empty($this->organization->id)) {
            // Only System Admin can create
            if (! $user->isSystemAdmin()) {
                $this->alert('error', 'Unauthorized action.');

                return;
            }

            DB::transaction(function () use ($user) {
                $org = Organization::create([
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

                if ($this->create_admin) {
                    $plainPassword = ! empty($this->admin_password) ? $this->admin_password : Str::random(10);
                    $adminRole = Role::firstOrCreate(['name' => 'admin']);

                    $adminUser = User::create([
                        'name' => $this->admin_name,
                        'email' => $this->admin_email,
                        'phone' => $this->admin_phone,
                        'password' => Hash::make($plainPassword),
                        'role_id' => $adminRole->id,
                        'organization_id' => $org->id,
                        'organization_verify' => 'verified',
                        'is_active' => true,
                    ]);

                    $org->owner_id = $adminUser->id;
                    $org->save();

                    // Send credentials email
                    try {
                        Mail::to($adminUser->email)->send(new UserCredentialsMail($adminUser, $plainPassword, $org, $adminRole));
                    } catch (\Exception $e) {
                        Log::error('Failed to send welcome credentials email: '.$e->getMessage());
                    }

                    $this->createdUserCredentials = [
                        'name' => $adminUser->name,
                        'email' => $adminUser->email,
                        'password' => $plainPassword,
                        'role' => 'Administrator',
                        'org_name' => $org->name,
                    ];
                    $this->credentialsModal = true;
                }
            });

            $this->alert('success', 'Organization created successfully.');
            $this->cancel(keepCredentials: true);
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
            $org->delete();
        });

        $this->alert('success', "Organization '{$org->name}' and all associated records have been permanently deleted.");
        $this->cancel();
    }

    public function updatedDistrictId(): void
    {
        $this->area_id = null;
    }

    public function closeCredentialsModal(): void
    {
        $this->credentialsModal = false;
        $this->createdUserCredentials = null;
    }

    public function cancel(bool $keepCredentials = false): void
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
            'create_admin',
            'admin_name',
            'admin_email',
            'admin_phone',
            'admin_password',
            'districtModal',
            'new_district_name',
            'areaModal',
            'new_area_name',
        ]);

        if (! $keepCredentials) {
            $this->credentialsModal = false;
            $this->createdUserCredentials = null;
        }
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
