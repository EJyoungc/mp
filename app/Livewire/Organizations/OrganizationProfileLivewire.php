<?php

namespace App\Livewire\Organizations;

use App\Models\AdHistory;
use App\Models\Organization;
use App\Models\PharmacyAd;
use App\Models\Role;
use App\Models\Tip;
use App\Models\User;
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

    public function mount($id): void
    {
        $this->organization_id = (int) $id;
        $organization = Organization::findOrFail($this->organization_id);

        $user = auth()->user();
        // Authorization: System Admin, Organization Owner, or Member of this Organization
        if (! $user->isSystemAdmin() && $organization->owner_id !== $user->id && $user->organization_id !== $organization->id) {
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

        return view('livewire.organizations.organization-profile-livewire', [
            'organization' => $organization,
            'members' => $members,
            'mothers' => $mothers,
            'roles' => Role::where('name', '!=', 'mother')->get(),
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
