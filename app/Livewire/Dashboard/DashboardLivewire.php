<?php

namespace App\Livewire\Dashboard;

use App\Exports\MothersSampleExport;
use App\Imports\MothersImport;
use App\Models\MessageHistory;
use App\Models\Organization;
use App\Models\PharmacyAd;
use App\Models\Tip;
use App\Models\Trimester;
use App\Models\User;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use Livewire\WithFileUploads;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Shared\Date;

// Re-use for ads

class DashboardLivewire extends Component
{
    use LivewireAlert;
    use WithFileUploads;

    public $modal = false;

    public $modal2 = false;

    public $adModal = false; // New for Ads

    public $file;

    public $previewData = [];

    public $previewTitleData = [];

    public $search = '';

    public $liststatus = false;

    public $user;

    public $organization;

    public $organizations = [];

    public $product_name;

    public $ad_message;

    public $target_week_start = 1;

    public $target_week_end = 40;

    public $trimester_id;

    public $ad_id;

    public function createAd()
    {
        $user = auth()->user();
        $isPharmacyOwner = $user->organization && $user->organization->is_pharmacy && $user->organization->owner_id == $user->id;

        if (! $user->isSystemAdmin() && ! $isPharmacyOwner) {
            return;
        }

        $this->reset(['ad_id', 'product_name', 'ad_message', 'target_week_start', 'target_week_end', 'trimester_id']);
        $this->adModal = true;
    }

    public function editAd($id)
    {
        $user = auth()->user();
        $ad = PharmacyAd::findOrFail($id);

        if (! $user->isSystemAdmin() && $ad->organization_id != $user->organization_id) {
            $this->alert('error', 'Unauthorized access.');

            return;
        }

        $this->ad_id = $ad->id;
        $this->product_name = $ad->product_name;
        $this->ad_message = $ad->ad_message;
        $this->target_week_start = $ad->target_week_start;
        $this->target_week_end = $ad->target_week_end;
        $this->trimester_id = $ad->trimester_id;

        $this->adModal = true;
    }

    public function storeAd()
    {
        $user = auth()->user();
        $isPharmacyOwner = $user->organization && $user->organization->is_pharmacy && $user->organization->owner_id == $user->id;

        if (! $user->isSystemAdmin() && ! $isPharmacyOwner) {
            return;
        }

        $this->validate([
            'product_name' => 'required|string|max:255',
            'ad_message' => 'required|string|max:160',
            'target_week_start' => 'nullable|integer|min:1',
            'target_week_end' => 'nullable|integer|max:42',
            'trimester_id' => 'nullable|exists:trimesters,id',
        ]);

        $orgId = $user->isSystemAdmin() ? null : $user->organization_id;

        if ($this->ad_id) {
            $ad = PharmacyAd::findOrFail($this->ad_id);
            $ad->update([
                'product_name' => $this->product_name,
                'ad_message' => $this->ad_message,
                'target_week_start' => $this->target_week_start,
                'target_week_end' => $this->target_week_end,
                'trimester_id' => $this->trimester_id,
            ]);
            $this->alert('success', 'Pharmacy Advertisement updated successfully');
        } else {
            PharmacyAd::create([
                'product_name' => $this->product_name,
                'ad_message' => $this->ad_message,
                'target_week_start' => $this->target_week_start,
                'target_week_end' => $this->target_week_end,
                'trimester_id' => $this->trimester_id,
                'organization_id' => $orgId,
            ]);
            $this->alert('success', 'Pharmacy Advertisement created successfully');
        }

        $this->cancel();
    }

    public function toggleAd($id)
    {
        $user = auth()->user();
        $ad = PharmacyAd::findOrFail($id);

        // Only admin or owner can toggle
        if (! $user->isSystemAdmin() && $ad->organization_id != $user->organization_id) {
            return;
        }

        $ad->update(['is_active' => ! $ad->is_active]);
        $this->alert('success', 'Ad status updated');
    }

    public function save()
    {

        $this->user->organization_id = $this->organization->id;
        $this->user->save();
        $this->alert('success', 'Organization successfully selected');
        $this->cancel();
    }

    public function remove_organization($id)
    {
        $user = User::findOrFail($id);
        $user->organization_id = null;
        $user->save();
        $this->alert('success', 'Organization successfully removed');
    }

    public function select_org($id)
    {
        $this->organization = Organization::findOrFail($id);
        $this->search = '';
        $this->organizations = [];
        $this->liststatus = false;

    }

    public function remove_org()
    {
        $this->organization = null;
    }

    public function updatedSearch()
    {
        if (strlen($this->search) > 1) {
            $this->liststatus = false;
            $this->organizations = Organization::where('name', 'like', '%'.$this->search.'%')->limit(10)->get();
        } else {
            $this->organizations = [];
        }
    }

    #[Computed]
    public function convertDate($value)
    {
        if (is_numeric($value)) {
            return Date::excelToDateTimeObject($value)->format('Y-m-d');
        }

        return $value;
    }

    public function add_organization($id)
    {

        $this->user = User::findOrFail($id);
        $this->modal2 = true;

    }

    public function addMothers()
    {
        $this->modal = true;
    }

    /**
     * Validate and load a preview of the Excel file data.
     *
     * Reads the file and sets $previewData to rows starting from row 5.
     *
     * @return void
     */
    public function preview()
    {
        $this->validate([
            'file' => 'required|file|mimes:xlsx,xls',
        ]);

        $this->modal = true;

        // Read the entire Excel file as an array
        $data = Excel::toArray([], $this->file);

        if (count($data) > 0) {
            // Get data from the first sheet
            $sheetData = $data[0];

            // Get the first row as header
            $this->previewTitleData = array_slice($sheetData, 0, 1);

            // Remove the first 4 rows (indexes 0-3) so that we start at row 5.
            $this->previewData = array_slice($sheetData, 4);
        } else {
            $this->previewData = [];
            $this->previewTitleData = [];
        }
    }

    /**
     * Confirm the import and store the users in the database.
     *
     * @return void
     */
    public function confirmImport()
    {
        Excel::import(new MothersImport, $this->file);
        $this->alert('success', 'Users imported successfully.');

        // Reset file and preview data
        $this->previewData = [];
        $this->file = null;
    }

    public function approve($id)
    {
        $userToApprove = User::findOrFail($id);
        $user = auth()->user();

        // Authorization check: Only System Admin or Organization Owner
        if (! $user->isSystemAdmin()) {
            $isOwner = Organization::where('id', $userToApprove->organization_id)
                ->where('owner_id', $user->id)
                ->exists();

            if (! $isOwner) {
                $this->alert('error', 'Unauthorized action.');

                return;
            }
        }

        if (! empty($userToApprove->organization_id)) {
            $userToApprove->organization_verify = 'approved';
            $userToApprove->save();
            $this->alert('success', 'User Approved');
        } else {
            $this->alert('warning', 'User Has No Organization');
        }
    }

    public function decline($id)
    {
        $userToDecline = User::findOrFail($id);
        $user = auth()->user();

        // Authorization check: Only System Admin or Organization Owner
        if (! $user->isSystemAdmin()) {
            $isOwner = Organization::where('id', $userToDecline->organization_id)
                ->where('owner_id', $user->id)
                ->exists();

            if (! $isOwner) {
                $this->alert('error', 'Unauthorized action.');

                return;
            }
        }

        $userToDecline->organization_verify = 'declined';
        $userToDecline->save();
        $this->alert('warning', 'User Declined');
    }

    public function cancel()
    {
        $this->reset(['modal', 'file', 'modal2', 'previewData', 'adModal', 'product_name', 'ad_message', 'target_week_start', 'target_week_end', 'trimester_id', 'ad_id']);
    }

    public function export()
    {
        return Excel::download(new MothersSampleExport, 'mothers.xlsx');
    }

    public function render()
    {
        $user = auth()->user();

        // System Admins see all requests, Org Owners see their organization's requests
        $requestsQuery = User::where('organization_id', '!=', null)
            ->where('organization_verify', 'pending')
            ->orderBy('created_at', 'desc');

        if (! $user->isSystemAdmin()) {
            // Check if user is an owner of any organization
            $ownedOrgIds = Organization::where('owner_id', $user->id)->pluck('id');
            if ($ownedOrgIds->isNotEmpty()) {
                $requestsQuery->whereIn('organization_id', $ownedOrgIds);
            } else {
                $requestsQuery->where('id', 0); // Empty result if not admin or owner
            }
        }
        $requests = $requestsQuery->get();

        // Mothers logic
        if ($user->isMother()) {
            $mothers = User::where('id', $user->id)->get();
        } else {
            $mothersQuery = User::where('role_id', 4); // mother role
            if (! $user->isSystemAdmin()) {
                $mothersQuery->where('organization_id', $user->organization_id);
            }
            $mothers = $mothersQuery->get();
        }

        // Stats
        $usersQuery = User::query();
        $messagesQuery = MessageHistory::query();

        if (! $user->isSystemAdmin()) {
            $usersQuery->where('organization_id', $user->organization_id);
            $messagesQuery->where('organization_id', $user->organization_id);
        }

        $users = $usersQuery->get();
        $messages = $messagesQuery->get();
        $tips = Tip::all(); // Auto-scoped

        // Comprehensive Analytics
        $analytics = [
            'doctors' => $users->filter(fn ($u) => $u->role && $u->role->name === 'doctor')->count(),
            'practitioners' => $users->filter(fn ($u) => $u->role && $u->role->name === 'practitioner')->count(),
            'organizations' => $user->isSystemAdmin() ? Organization::count() : 1,
            'sent_success' => $messages->where('message_status', 'sent')->count(),
            'sent_failed' => $messages->where('message_status', 'failed')->count(),
            'delivery_rate' => $messages->count() > 0 ? round(($messages->where('message_status', 'sent')->count() / $messages->count()) * 100, 1) : 0,
        ];

        // Pharmacy Ads (System Admin sees all, Pharmacy Owners see theirs)
        $isPharmacyOwner = $user->organization && $user->organization->is_pharmacy && $user->organization->owner_id == $user->id;
        $adsQuery = PharmacyAd::latest();

        if ($user->isSystemAdmin()) {
            $ads = $adsQuery->get();
        } elseif ($isPharmacyOwner) {
            $ads = $adsQuery->where('organization_id', $user->organization_id)->get();
        } else {
            $ads = collect();
        }

        // Count tips pending approval for doctors/admins
        $pendingTipsCount = Tip::where('status', Tip::STATUS_PENDING)->count();

        $trimesters = Trimester::all();

        return view('livewire.dashboard.dashboard-livewire')
            ->with('mothers', $mothers)
            ->with('users', $users)
            ->with('messages', $messages)
            ->with('tips', $tips)
            ->with('requests', $requests)
            ->with('pendingTipsCount', $pendingTipsCount)
            ->with('analytics', $analytics)
            ->with('trimesters', $trimesters)
            ->with('ads', $ads);
    }
}
