<?php

namespace App\Livewire\Organizations;

use App\Mail\UserCredentialsMail;
use App\Models\AdHistory;
use App\Models\Organization;
use App\Models\PharmacyAd;
use App\Models\Role;
use App\Models\Tip;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use Livewire\WithPagination;

class OrganizationProfileLivewire extends Component
{
    use LivewireAlert;
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $organization_id;

    public $activeTab = 'members'; // 'members', 'mothers', 'overview'

    public $search = '';

    public $role_filter = '';

    public $verification_filter = '';

    public $perPage = 10;

    // Staff Member Modal properties
    public $memberModal = false;

    public $editingMemberId = null;

    public $member_name = '';

    public $member_email = '';

    public $member_phone = '';

    public $member_role_id = '';

    public $member_is_active = true;

    public $member_password = '';

    public $send_credentials_email = true;

    public $credentialsModal = false;

    public $createdUserCredentials = null;

    public function mount($id = null, $organization = null): void
    {
        if ($id instanceof Organization) {
            $this->organization_id = $id->id;
        } elseif ($organization instanceof Organization) {
            $this->organization_id = $organization->id;
        } elseif ($organization !== null) {
            $this->organization_id = (int) $organization;
        } else {
            $this->organization_id = (int) $id;
        }

        $orgModel = Organization::findOrFail($this->organization_id);

        $user = auth()->user();
        // Authorization: System Admin, Organization Owner, or Member of this Organization
        if (! $user->isSystemAdmin() && $orgModel->owner_id !== $user->id && $user->organization_id !== $orgModel->id) {
            abort(403, 'Unauthorized access to this organization profile.');
        }
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingRoleFilter(): void
    {
        $this->resetPage();
    }

    public function updatingVerificationFilter(): void
    {
        $this->resetPage();
    }

    public function setTab(string $tab): void
    {
        $this->activeTab = $tab;
        $this->search = '';
        $this->role_filter = '';
        $this->verification_filter = '';
        $this->resetPage();
    }

    public function generateMemberPassword(): void
    {
        $this->member_password = Str::random(10);
    }

    public function createMember(): void
    {
        $user = auth()->user();
        $organization = Organization::findOrFail($this->organization_id);

        if (! $user->isSystemAdmin() && $organization->owner_id !== $user->id && ! $user->isOrgAdmin()) {
            $this->alert('error', 'Unauthorized action.');

            return;
        }

        $this->resetMemberForm();

        // Default role
        if ($organization->is_pharmacy) {
            $practitionerRole = Role::where('name', 'practitioner')->first();
            $this->member_role_id = $practitionerRole?->id;
        } else {
            $doctorRole = Role::where('name', 'doctor')->first();
            $this->member_role_id = $doctorRole?->id;
        }

        $this->memberModal = true;
    }

    public function editMember(int $userId): void
    {
        $user = auth()->user();
        $organization = Organization::findOrFail($this->organization_id);

        if (! $user->isSystemAdmin() && $organization->owner_id !== $user->id && ! $user->isOrgAdmin()) {
            $this->alert('error', 'Unauthorized action.');

            return;
        }

        $member = User::where('organization_id', $this->organization_id)->findOrFail($userId);

        $this->editingMemberId = $member->id;
        $this->member_name = $member->name;
        $this->member_email = $member->email;
        $this->member_phone = $member->phone ?? '';
        $this->member_role_id = $member->role_id;
        $this->member_is_active = (bool) $member->is_active;
        $this->member_password = '';
        $this->send_credentials_email = true;

        $this->memberModal = true;
    }

    public function cancel()
    {
        $this->reset([
            'memberModal',
            'editingMemberId',
            'member_name',
            'member_email',
            'member_phone',
            'member_role_id',
            'member_is_active',
            'member_password',
            'send_credentials_email',
        ]);
    }

    public function saveMember(): void
    {
        $user = auth()->user();
        $organization = Organization::findOrFail($this->organization_id);

        if (! $user->isSystemAdmin() && $organization->owner_id !== $user->id && ! $user->isOrgAdmin()) {
            $this->alert('error', 'Unauthorized action.');

            return;
        }

        $emailRule = 'required|email|max:255|unique:users,email';
        if ($this->editingMemberId) {
            $emailRule .= ','.$this->editingMemberId.',id';
        }

        $this->validate([
            'member_name' => 'required|string|max:255',
            'member_email' => $emailRule,
            'member_phone' => 'nullable|string|max:20',
            'member_role_id' => 'required|exists:roles,id',
            'member_is_active' => 'boolean',
            'member_password' => 'nullable|string|min:6',
        ]);

        $role = Role::findOrFail($this->member_role_id);

        // Pharmacy restriction rule: cannot assign clinical maternal roles (like doctor)
        if ($organization->is_pharmacy && ! in_array($role->name, ['practitioner', 'admin'])) {
            $this->addError('member_role_id', 'Pharmacy organizations can only have Practitioner or Admin staff.');
            $this->alert('error', 'Pharmacy organizations can only have Practitioner or Admin staff.');

            return;
        }

        if ($this->editingMemberId) {
            // Updating existing member
            $member = User::where('organization_id', $this->organization_id)->findOrFail($this->editingMemberId);

            $updateData = [
                'name' => $this->member_name,
                'email' => $this->member_email,
                'phone' => $this->member_phone,
                'role_id' => $this->member_role_id,
                'is_active' => $this->member_is_active,
            ];

            $passwordChanged = false;
            $plainPassword = '';
            if (! empty($this->member_password)) {
                $plainPassword = $this->member_password;
                $updateData['password'] = Hash::make($plainPassword);
                $passwordChanged = true;
            }

            $member->update($updateData);

            if ($passwordChanged && $this->send_credentials_email) {
                try {
                    Mail::to($member->email)->send(new UserCredentialsMail($member, $plainPassword, $organization, $role));
                } catch (\Exception $e) {
                    Log::error('Failed to send updated credentials email: '.$e->getMessage());
                }

                $this->createdUserCredentials = [
                    'name' => $member->name,
                    'email' => $member->email,
                    'password' => $plainPassword,
                    'role' => ucfirst($role->name),
                    'org_name' => $organization->name,
                ];
                $this->credentialsModal = true;
            }

            $this->alert('success', "Member '{$member->name}' updated successfully.");
            $this->cancelMember(keepCredentials: true);
        } else {
            // Creating new member
            $plainPassword = ! empty($this->member_password) ? $this->member_password : Str::random(10);

            $newMember = User::create([
                'name' => $this->member_name,
                'email' => $this->member_email,
                'phone' => $this->member_phone,
                'password' => Hash::make($plainPassword),
                'role_id' => $this->member_role_id,
                'organization_id' => $this->organization_id,
                'organization_verify' => 'verified',
                'is_active' => $this->member_is_active,
            ]);

            if ($this->send_credentials_email) {
                try {
                    Mail::to($newMember->email)->send(new UserCredentialsMail($newMember, $plainPassword, $organization, $role));
                } catch (\Exception $e) {
                    Log::error('Failed to send welcome credentials email: '.$e->getMessage());
                }
            }

            $this->createdUserCredentials = [
                'name' => $newMember->name,
                'email' => $newMember->email,
                'password' => $plainPassword,
                'role' => ucfirst($role->name),
                'org_name' => $organization->name,
            ];
            $this->credentialsModal = true;

            $this->alert('success', "Staff member '{$newMember->name}' created successfully.");
            $this->cancelMember(keepCredentials: true);
        }
    }

    public function cancelMember(bool $keepCredentials = false): void
    {
        $this->resetMemberForm();
        $this->memberModal = false;

        if (! $keepCredentials) {
            $this->credentialsModal = false;
            $this->createdUserCredentials = null;
        }
    }

    private function resetMemberForm(): void
    {
        $this->reset([
            'editingMemberId',
            'member_name',
            'member_email',
            'member_phone',
            'member_role_id',
            'member_password',
        ]);
        $this->member_is_active = true;
        $this->send_credentials_email = true;
    }

    public function closeCredentialsModal(): void
    {
        $this->credentialsModal = false;
        $this->createdUserCredentials = null;
    }

    public function approveUser(int $userId): void
    {
        $user = auth()->user();
        $organization = Organization::findOrFail($this->organization_id);

        if (! $user->isSystemAdmin() && $organization->owner_id !== $user->id) {
            $this->alert('error', 'Unauthorized action.');

            return;
        }

        $userToApprove = User::where('organization_id', $this->organization_id)->findOrFail($userId);
        $userToApprove->organization_verify = 'verified';
        $userToApprove->save();

        $this->alert('success', "User '{$userToApprove->name}' approved successfully.");
    }

    public function declineUser(int $userId): void
    {
        $user = auth()->user();
        $organization = Organization::findOrFail($this->organization_id);

        if (! $user->isSystemAdmin() && $organization->owner_id !== $user->id) {
            $this->alert('error', 'Unauthorized action.');

            return;
        }

        $userToDecline = User::where('organization_id', $this->organization_id)->findOrFail($userId);
        $userToDecline->organization_verify = 'declined';
        $userToDecline->save();

        $this->alert('success', "User '{$userToDecline->name}' verification declined.");
    }

    public function render()
    {
        $organization = Organization::with(['district', 'area', 'owner'])->findOrFail($this->organization_id);

        // Calculate statistics
        $staffCount = User::where('organization_id', $this->organization_id)
            ->whereHas('role', function ($q) {
                $q->where('name', '!=', 'mother');
            })->count();

        $mothersCount = User::where('organization_id', $this->organization_id)
            ->whereHas('role', function ($q) {
                $q->where('name', 'mother');
            })->count();

        $doctorsCount = User::where('organization_id', $this->organization_id)
            ->whereHas('role', function ($q) {
                $q->where('name', 'doctor');
            })->count();

        $practitionersCount = User::where('organization_id', $this->organization_id)
            ->whereHas('role', function ($q) {
                $q->where('name', 'practitioner');
            })->count();

        $adminsCount = User::where('organization_id', $this->organization_id)
            ->whereHas('role', function ($q) {
                $q->where('name', 'admin');
            })->count();

        $pendingCount = User::where('organization_id', $this->organization_id)
            ->where('organization_verify', 'pending')
            ->count();

        $tipsCount = Tip::where('organization_id', $this->organization_id)->count();
        $adsCount = PharmacyAd::where('organization_id', $this->organization_id)->count();
        $adHistoryCount = AdHistory::where('organization_id', $this->organization_id)->count();

        // Staff / Members Query
        $staffQuery = User::where('organization_id', $this->organization_id)
            ->whereHas('role', function ($q) {
                $q->where('name', '!=', 'mother');
            })
            ->with(['role', 'district', 'area']);

        if (! empty($this->search) && $this->activeTab === 'members') {
            $s = trim($this->search);
            $staffQuery->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                    ->orWhere('email', 'like', "%{$s}%")
                    ->orWhere('phone', 'like', "%{$s}%");
            });
        }

        if (! empty($this->role_filter) && $this->activeTab === 'members') {
            $staffQuery->where('role_id', $this->role_filter);
        }

        if (! empty($this->verification_filter) && $this->activeTab === 'members') {
            $staffQuery->where('organization_verify', $this->verification_filter);
        }

        $members = $staffQuery->latest()->paginate($this->perPage, ['*'], 'members_page');

        // Mothers Query
        $mothersQuery = User::where('organization_id', $this->organization_id)
            ->whereHas('role', function ($q) {
                $q->where('name', 'mother');
            })
            ->with(['role', 'district', 'area']);

        if (! empty($this->search) && $this->activeTab === 'mothers') {
            $s = trim($this->search);
            $mothersQuery->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                    ->orWhere('email', 'like', "%{$s}%")
                    ->orWhere('phone', 'like', "%{$s}%")
                    ->orWhere('address', 'like', "%{$s}%");
            });
        }

        $mothers = $mothersQuery->latest()->paginate($this->perPage, ['*'], 'mothers_page');

        // Recent Ad Outreach or Tips (for Activity/Overview tab)
        $recentAds = [];
        $recentTips = [];
        if ($organization->is_pharmacy) {
            $recentAds = PharmacyAd::where('organization_id', $this->organization_id)->latest()->take(5)->get();
        } else {
            $recentTips = Tip::where('organization_id', $this->organization_id)->latest()->take(5)->get();
        }

        // Available roles for creating/editing staff members
        $rolesQuery = Role::where('name', '!=', 'mother');
        if ($organization->is_pharmacy) {
            $rolesQuery->whereIn('name', ['practitioner', 'admin']);
        }

        return view('livewire.organizations.organization-profile-livewire', [
            'organization' => $organization,
            'members' => $members,
            'mothers' => $mothers,
            'roles' => $rolesQuery->get(),
            'recentAds' => $recentAds,
            'recentTips' => $recentTips,
            'stats' => [
                'total_users' => $staffCount + $mothersCount,
                'staff' => $staffCount,
                'mothers' => $mothersCount,
                'doctors' => $doctorsCount,
                'practitioners' => $practitionersCount,
                'admins' => $adminsCount,
                'pending' => $pendingCount,
                'tips' => $tipsCount,
                'ads' => $adsCount,
                'ad_outreach' => $adHistoryCount,
            ],
        ]);
    }
}
