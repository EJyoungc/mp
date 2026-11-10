<?php

namespace App\Livewire\Settings;

use App\Models\Organization;
use App\Services\SmsService;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use Livewire\WithPagination;

class SmsSettingsLivewire extends Component
{
    use LivewireAlert;
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $modal = false;

    // Search and filters for System Admin
    public $search = '';

    public $typeFilter = 'all'; // 'all', 'facilities', 'pharmacies'

    public $perPage = 10;

    // Organization edit state
    public $editingOrgId = null;

    public $editingOrgName = '';

    public $acronym = '';

    // Sample preview text
    public $sampleMessage = 'Moni amayi! Onetsetsani kuti mukumwa madzi abwino komanso kudya zakudya zopatsa thanzi.';

    public function mount(): void
    {
        $user = auth()->user();

        if (! $user->isSystemAdmin() && ! $user->isOrgAdmin() && ! $user->isDoctor()) {
            abort(403, 'Unauthorized access to SMS settings.');
        }

        // For Org Admin or Doctor, load their organization acronym by default
        if (! $user->isSystemAdmin() && $user->organization) {
            $this->editingOrgId = $user->organization_id;
            $this->editingOrgName = $user->organization->name;
            $this->acronym = $user->organization->acronym ?? '';
        }
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingTypeFilter(): void
    {
        $this->resetPage();
    }

    public function editOrgAcronym(int $orgId): void
    {
        $user = auth()->user();
        if (! $user->isSystemAdmin() && $user->organization_id !== $orgId) {
            $this->alert('error', 'Unauthorized action.');

            return;
        }

        $org = Organization::findOrFail($orgId);
        $this->editingOrgId = $org->id;
        $this->editingOrgName = $org->name;
        $this->acronym = $org->acronym ?? '';
        $this->modal = true;
    }

    public function saveOrgAcronym(): void
    {
        $user = auth()->user();

        if (! $user->isSystemAdmin() && $user->organization_id !== $this->editingOrgId) {
            $this->alert('error', 'Unauthorized action.');

            return;
        }

        $this->validate([
            'acronym' => 'nullable|string|max:30',
        ]);

        $org = Organization::findOrFail($this->editingOrgId);
        $cleanAcronym = trim((string) $this->acronym);
        $org->update([
            'acronym' => $cleanAcronym !== '' ? $cleanAcronym : null,
        ]);

        $this->alert('success', "SMS acronym for '{$org->name}' updated successfully.");
        $this->modal = false;
    }

    public function saveMyOrgAcronym(): void
    {
        $user = auth()->user();
        if (! $user->organization_id) {
            $this->alert('error', 'No organization associated with your profile.');

            return;
        }

        $this->validate([
            'acronym' => 'nullable|string|max:30',
        ]);

        $org = Organization::findOrFail($user->organization_id);
        $cleanAcronym = trim((string) $this->acronym);
        $org->update([
            'acronym' => $cleanAcronym !== '' ? $cleanAcronym : null,
        ]);

        $this->alert('success', "Your organization SMS acronym was saved as '{$cleanAcronym}'.");
    }

    public function cancel(): void
    {
        $user = auth()->user();
        $this->modal = false;

        if ($user->isSystemAdmin()) {
            $this->reset(['editingOrgId', 'editingOrgName', 'acronym']);
        } elseif ($user->organization) {
            $this->acronym = $user->organization->acronym ?? '';
        }
    }

    public function getPreviewMessageProperty(): string
    {
        return SmsService::formatWithAcronym($this->sampleMessage, $this->acronym);
    }

    public function render()
    {
        $user = auth()->user();
        $organizations = collect();
        $myOrg = null;

        if ($user->isSystemAdmin()) {
            $query = Organization::query();

            if (! empty($this->search)) {
                $s = $this->search;
                $query->where(function ($q) use ($s) {
                    $q->where('name', 'like', "%{$s}%")
                        ->orWhere('acronym', 'like', "%{$s}%")
                        ->orWhere('phone', 'like', "%{$s}%")
                        ->orWhere('address', 'like', "%{$s}%");
                });
            }

            if ($this->typeFilter === 'facilities') {
                $query->where('is_pharmacy', false);
            } elseif ($this->typeFilter === 'pharmacies') {
                $query->where('is_pharmacy', true);
            }

            $organizations = $query->orderBy('name')->paginate($this->perPage);
        } else {
            $myOrg = $user->organization;
        }

        return view('livewire.settings.sms-settings-livewire', [
            'isSystemAdmin' => $user->isSystemAdmin(),
            'organizations' => $organizations,
            'myOrg' => $myOrg,
        ]);
    }
}
