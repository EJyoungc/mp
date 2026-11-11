<?php

namespace App\Livewire\Tips;

use App\Models\Day;
use App\Models\DayRange;
use App\Models\History;
use App\Models\Organization;
use App\Models\Tip;
use App\Models\Trimester;
use App\Models\Week;
use Carbon\Carbon;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use Livewire\WithPagination;

class TipsManagementLivewire extends Component
{
    use LivewireAlert;
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    // Active View Tab: 'all' or 'forecast'
    public $activeTab = 'all';

    // Search and filters for All Tips directory
    public $search = '';

    public $organizationFilter = '';

    public $trimesterFilter = '';

    public $weekFilter = '';

    public $dayFilter = '';

    public $statusFilter = '';

    public $perPage = 15;

    // Daily Forecast date filter
    public $selectedDate = '';

    // Template Tips section filters
    public $templateSearch = '';

    public $templateTrimesterFilter = '';

    public $templateWeekFilter = '';

    // Modal state for Create / Edit Tip
    public $modal = false;

    public $editingTipId = null;

    public $tip_content = '';

    public $week_id = 1;

    public $day_id = 1;

    public $day_range_id = '';

    public $organization_id = '';

    public $status = Tip::STATUS_APPROVED;

    public $is_template = false;

    // Modal state for viewing recipient mothers of a specific tip
    public $recipientsModal = false;

    public $viewingTip = null;

    public $viewingRecipients = [];

    // Modal state for Delete confirmation
    public $deleteModal = false;

    public $deletingTipId = null;

    public $deletingTipPreview = '';

    public function mount(): void
    {
        $user = auth()->user();

        // Pharmacy staff and mothers are restricted from clinical tip management
        if ($user->isPharmacyAdmin() || $user->isMother()) {
            abort(403, 'Unauthorized access to maternal tips management.');
        }

        $this->selectedDate = now()->toDateString();

        // Non-system admins default to their organization
        if (! $user->isSystemAdmin()) {
            $this->organizationFilter = (string) $user->organization_id;
            $this->organization_id = (string) $user->organization_id;
        }

        // Default day_range_id to first available if present
        $firstRange = DayRange::first();
        if ($firstRange) {
            $this->day_range_id = $firstRange->id;
        }
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingOrganizationFilter(): void
    {
        $this->resetPage();
    }

    public function updatingTrimesterFilter(): void
    {
        $this->weekFilter = '';
        $this->resetPage();
    }

    public function updatingWeekFilter(): void
    {
        $this->resetPage();
    }

    public function updatingDayFilter(): void
    {
        $this->resetPage();
    }

    public function updatingStatusFilter(): void
    {
        $this->resetPage();
    }

    public function updatingTemplateSearch(): void
    {
        $this->resetPage('templatesPage');
    }

    public function updatingTemplateTrimesterFilter(): void
    {
        $this->templateWeekFilter = '';
        $this->resetPage('templatesPage');
    }

    public function updatingTemplateWeekFilter(): void
    {
        $this->resetPage('templatesPage');
    }

    public function resetTemplateFilters(): void
    {
        $this->reset([
            'templateSearch',
            'templateTrimesterFilter',
            'templateWeekFilter',
        ]);
        $this->resetPage('templatesPage');
    }

    public function resetFilters(): void
    {
        $user = auth()->user();
        $this->reset([
            'search',
            'trimesterFilter',
            'weekFilter',
            'dayFilter',
            'statusFilter',
        ]);

        if ($user->isSystemAdmin()) {
            $this->organizationFilter = '';
        } else {
            $this->organizationFilter = (string) $user->organization_id;
        }

        $this->resetPage();
    }

    public function setTab(string $tab): void
    {
        $this->activeTab = $tab;
    }

    public function setQuickDate(string $type): void
    {
        if ($type === 'today') {
            $this->selectedDate = now()->toDateString();
        } elseif ($type === 'tomorrow') {
            $this->selectedDate = now()->addDay()->toDateString();
        }
    }

    // ========================================================
    // CREATE / EDIT TIP MODAL
    // ========================================================

    public function openCreateModal(bool $isTemplate = false): void
    {
        $user = auth()->user();
        $this->resetErrorBag();
        $this->editingTipId = null;
        $this->tip_content = '';
        $this->week_id = 1;
        $this->day_id = 1;
        $this->is_template = $user->isSystemAdmin() ? $isTemplate : false;

        $firstRange = DayRange::first();
        $this->day_range_id = $firstRange ? $firstRange->id : 1;

        if ($user->isSystemAdmin()) {
            $this->organization_id = $this->organizationFilter ?: '';
        } else {
            $this->organization_id = (string) $user->organization_id;
        }

        $this->status = $user->isDoctor() || $user->isSystemAdmin() ? Tip::STATUS_APPROVED : Tip::STATUS_PENDING;
        $this->modal = true;
    }

    public function openEditModal(int $id): void
    {
        $user = auth()->user();
        $this->resetErrorBag();

        $query = Tip::withoutGlobalScope('organization');
        if (! $user->isSystemAdmin()) {
            $query->where('organization_id', $user->organization_id);
        }

        $tip = $query->findOrFail($id);

        $this->editingTipId = $tip->id;
        $this->tip_content = $tip->tip;
        $this->week_id = (int) $tip->week_id;
        $this->day_id = (int) $tip->day_id;
        $this->day_range_id = $tip->day_range_id;
        $this->organization_id = $tip->organization_id ? (string) $tip->organization_id : '';
        $this->status = $tip->status;
        $this->is_template = (bool) $tip->is_template;

        $this->modal = true;
    }

    public function store(): void
    {
        $user = auth()->user();

        $rules = [
            'tip_content' => 'required|string|min:5|max:160',
            'week_id' => 'required|integer|min:1|max:42',
            'day_id' => 'required|integer|min:1|max:7',
            'day_range_id' => 'required|exists:day_ranges,id',
            'status' => 'required|in:'.Tip::STATUS_APPROVED.','.Tip::STATUS_PENDING.','.Tip::STATUS_DRAFT,
        ];

        if ($user->isSystemAdmin()) {
            $rules['organization_id'] = 'nullable|exists:organizations,id';
        }

        $this->validate($rules);

        $orgId = $user->isSystemAdmin()
            ? (! empty($this->organization_id) ? (int) $this->organization_id : null)
            : $user->organization_id;

        $isTemplateVal = $user->isSystemAdmin() ? (bool) $this->is_template : false;

        if (empty($this->editingTipId)) {
            // Create New Tip
            Tip::create([
                'tip' => trim($this->tip_content),
                'week_id' => $this->week_id,
                'day_id' => $this->day_id,
                'day_range_id' => $this->day_range_id,
                'organization_id' => $orgId,
                'created_by' => $user->id,
                'status' => $this->status,
                'is_template' => $isTemplateVal,
            ]);

            $this->alert('success', 'New maternal tip created successfully.');
        } else {
            // Update Existing Tip
            $query = Tip::withoutGlobalScope('organization');
            if (! $user->isSystemAdmin()) {
                $query->where('organization_id', $user->organization_id);
            }

            $tip = $query->findOrFail($this->editingTipId);
            $tip->update([
                'tip' => trim($this->tip_content),
                'week_id' => $this->week_id,
                'day_id' => $this->day_id,
                'day_range_id' => $this->day_range_id,
                'organization_id' => $orgId,
                'status' => $this->status,
                'is_template' => $user->isSystemAdmin() ? $isTemplateVal : $tip->is_template,
                'approved_by' => ($this->status === Tip::STATUS_APPROVED && $tip->status !== Tip::STATUS_APPROVED) ? $user->id : $tip->approved_by,
            ]);

            $this->alert('success', 'Maternal tip updated successfully.');
        }

        $this->cancel();
    }

    public function toggleTemplate(int $id): void
    {
        $user = auth()->user();
        if (! $user->isSystemAdmin()) {
            $this->alert('error', 'Only System Administrators can mark tips as templates.');

            return;
        }

        $tip = Tip::withoutGlobalScope('organization')->findOrFail($id);
        $tip->update(['is_template' => ! $tip->is_template]);
        $statusMsg = $tip->is_template ? 'marked as template' : 'removed from templates';
        $this->alert('success', "Tip #{$tip->id} {$statusMsg}.");
    }

    public function useTemplate(int $id): void
    {
        $user = auth()->user();
        if (! $user->organization_id) {
            $this->alert('error', 'You must belong to an organization to adopt templates.');

            return;
        }

        $template = Tip::withoutGlobalScope('organization')->findOrFail($id);

        $status = ($user->isDoctor() || $user->isSystemAdmin()) ? Tip::STATUS_APPROVED : Tip::STATUS_PENDING;

        Tip::create([
            'tip' => $template->tip,
            'week_id' => $template->week_id,
            'day_id' => $template->day_id,
            'day_range_id' => $template->day_range_id,
            'organization_id' => $user->organization_id,
            'created_by' => $user->id,
            'status' => $status,
            'is_template' => false,
        ]);

        $this->alert('success', "Template successfully copied to your organization for Week {$template->week_id}, Day {$template->day_id}.");
    }

    public function approveTip(int $id): void
    {
        $user = auth()->user();
        if (! $user->isDoctor() && ! $user->isSystemAdmin()) {
            $this->alert('error', 'Only doctors and system administrators can approve tips.');

            return;
        }

        $query = Tip::withoutGlobalScope('organization');
        if (! $user->isSystemAdmin()) {
            $query->where('organization_id', $user->organization_id);
        }

        $tip = $query->findOrFail($id);
        $tip->update([
            'status' => Tip::STATUS_APPROVED,
            'approved_by' => $user->id,
        ]);

        $this->alert('success', 'Tip approved for automated SMS delivery.');
    }

    // ========================================================
    // DELETE MODAL
    // ========================================================

    public function confirmDelete(int $id): void
    {
        $user = auth()->user();
        $query = Tip::withoutGlobalScope('organization');
        if (! $user->isSystemAdmin()) {
            $query->where('organization_id', $user->organization_id);
        }

        $tip = $query->findOrFail($id);
        $this->deletingTipId = $tip->id;
        $this->deletingTipPreview = $tip->tip;
        $this->deleteModal = true;
    }

    public function deleteTip(): void
    {
        $user = auth()->user();
        if (empty($this->deletingTipId)) {
            return;
        }

        $query = Tip::withoutGlobalScope('organization');
        if (! $user->isSystemAdmin()) {
            $query->where('organization_id', $user->organization_id);
        }

        $tip = $query->findOrFail($this->deletingTipId);
        $tip->delete();

        $this->alert('success', 'Tip removed successfully.');
        $this->cancel();
    }

    // ========================================================
    // VIEW RECIPIENTS MODAL
    // ========================================================

    public function openRecipientsModal(int $tipId): void
    {
        $user = auth()->user();
        $query = Tip::withoutGlobalScope('organization')->with(['day_range', 'organization']);
        if (! $user->isSystemAdmin()) {
            $query->where('organization_id', $user->organization_id);
        }

        $this->viewingTip = $query->findOrFail($tipId);
        $this->viewingRecipients = $this->calculateRecipientsForTip($this->viewingTip, $this->selectedDate);
        $this->recipientsModal = true;
    }

    // ========================================================
    // RECIPIENT CALCULATION FOR GIVEN DATE
    // ========================================================

    /**
     * Calculate recipient mothers for a specific tip on a specific date.
     * Respects organization isolation.
     */
    public function calculateRecipientsForTip(Tip $tip, string $dateString): array
    {
        $user = auth()->user();
        $targetDate = Carbon::parse($dateString)->startOfDay();

        $historiesQuery = History::with(['mother.organization', 'organization', 'mother.district', 'mother.area'])
            ->whereHas('mother', function ($mq) {
                $mq->whereNotNull('phone')->where('phone', '!=', '');
            });

        // Strict organization scoping for non-system admin
        if (! $user->isSystemAdmin()) {
            $historiesQuery->where(function ($q) use ($user) {
                $q->where('organization_id', $user->organization_id)
                    ->orWhereHas('mother', fn ($mq) => $mq->where('organization_id', $user->organization_id));
            });
        } elseif (! empty($this->organizationFilter)) {
            $orgId = (int) $this->organizationFilter;
            $historiesQuery->where(function ($q) use ($orgId) {
                $q->where('organization_id', $orgId)
                    ->orWhereHas('mother', fn ($mq) => $mq->where('organization_id', $orgId));
            });
        }

        $histories = $historiesQuery->get();
        $recipients = [];

        foreach ($histories as $history) {
            if (empty($history->last_menstrual_cycle)) {
                continue;
            }

            $lmc = Carbon::parse($history->last_menstrual_cycle)->startOfDay();

            if ($targetDate->lt($lmc)) {
                continue;
            }

            $days = (int) $lmc->diffInDays($targetDate);
            if ($days > 300) {
                // Post-term or completed pregnancy session
                continue;
            }

            $weeks = max(1, (int) ceil(($days === 0 ? 1 : $days) / 7));
            $dayInWeek = $days > 0 ? ((($days - 1) % 7) + 1) : 1;
            $cumulativeDay = $days;

            $motherOrgId = $history->organization_id ?? $history->mother?->organization_id;

            // Check if gestational timing matches the tip
            $timingMatches = ((int) $tip->week_id === (int) $weeks) &&
                (((int) $tip->day_id === (int) $dayInWeek) || ((int) $tip->day_id === (int) $cumulativeDay));

            if (! $timingMatches) {
                continue;
            }

            // Organization check:
            // 1. If tip belongs to an organization, mother MUST belong to the exact same organization
            if (! empty($tip->organization_id)) {
                if (empty($motherOrgId) || (int) $tip->organization_id !== (int) $motherOrgId) {
                    continue;
                }
            } else {
                // 2. Global tip: if mother belongs to an organization that has created its own tip for this week & day, skip global fallback
                if (! empty($motherOrgId)) {
                    $orgHasCustomTip = Tip::withoutGlobalScope('organization')
                        ->where('organization_id', $motherOrgId)
                        ->where('week_id', (int) $weeks)
                        ->where(function ($q) use ($dayInWeek, $cumulativeDay) {
                            $q->where('day_id', $dayInWeek)->orWhere('day_id', $cumulativeDay);
                        })
                        ->approved()
                        ->exists();

                    if ($orgHasCustomTip) {
                        continue;
                    }
                }
            }

            $recipients[] = [
                'mother_id' => $history->mother_id,
                'name' => $history->mother?->name ?? 'Unknown Mother',
                'phone' => $history->mother?->phone ?? 'N/A',
                'age' => $history->mother?->age ?? '-',
                'gestation_weeks' => $weeks,
                'gestation_day' => $dayInWeek,
                'cumulative_days' => $days,
                'organization_name' => $history->mother?->organization?->name ?? ($history->organization?->name ?? 'None'),
                'organization_acronym' => $history->mother?->organization?->acronym ?? ($history->organization?->acronym ?? null),
                'district_name' => $history->mother?->district?->name ?? 'N/A',
                'area_name' => $history->mother?->area?->name ?? 'N/A',
                'session_id' => $history->id,
            ];
        }

        return $recipients;
    }

    // ========================================================
    // MODAL CANCEL (Strict project convention)
    // ========================================================

    public function cancel(): void
    {
        $this->modal = false;
        $this->recipientsModal = false;
        $this->deleteModal = false;
        $this->editingTipId = null;
        $this->deletingTipId = null;
        $this->deletingTipPreview = '';
        $this->viewingTip = null;
        $this->viewingRecipients = [];
        $this->is_template = false;
        $this->resetErrorBag();
    }

    // ========================================================
    // RENDER & COMPUTATIONS
    // ========================================================

    public function render()
    {
        $user = auth()->user();

        // Base query for tips respecting multi-tenancy
        $baseTipsQuery = Tip::withoutGlobalScope('organization')
            ->with(['week.trimester', 'day_range', 'day', 'organization', 'creator', 'approver']);

        if (! $user->isSystemAdmin()) {
            $baseTipsQuery->where('organization_id', $user->organization_id);
        }

        // Stats calculation
        $totalTipsCount = (clone $baseTipsQuery)->count();
        $approvedTipsCount = (clone $baseTipsQuery)->where('status', Tip::STATUS_APPROVED)->count();
        $pendingTipsCount = (clone $baseTipsQuery)->where('status', Tip::STATUS_PENDING)->count();

        // ----------------------------------------------------
        // TAB 1: ALL TIPS DIRECTORY
        // ----------------------------------------------------
        $directoryQuery = (clone $baseTipsQuery);

        if ($user->isSystemAdmin() && ! empty($this->organizationFilter)) {
            if ($this->organizationFilter === 'global') {
                $directoryQuery->whereNull('organization_id');
            } else {
                $directoryQuery->where('organization_id', $this->organizationFilter);
            }
        }

        if (! empty($this->trimesterFilter)) {
            $tri = $this->trimesterFilter;
            $directoryQuery->whereHas('week', function ($wq) use ($tri) {
                $wq->where('trimester_id', $tri);
            });
        }

        if (! empty($this->weekFilter)) {
            $directoryQuery->where('week_id', $this->weekFilter);
        }

        if (! empty($this->dayFilter)) {
            $directoryQuery->where('day_id', $this->dayFilter);
        }

        if (! empty($this->statusFilter)) {
            $directoryQuery->where('status', $this->statusFilter);
        }

        if (! empty($this->search)) {
            $s = trim($this->search);
            $directoryQuery->where(function ($q) use ($s) {
                $q->where('tip', 'like', "%{$s}%")
                    ->orWhereHas('organization', function ($oq) use ($s) {
                        $oq->where('name', 'like', "%{$s}%")
                            ->orWhere('acronym', 'like', "%{$s}%");
                    });
            });
        }

        $tips = $directoryQuery->orderBy('week_id')->orderBy('day_id')->paginate($this->perPage);

        // ----------------------------------------------------
        // TAB 2: DAILY SCHEDULE FORECAST FOR SELECTED DATE
        // ----------------------------------------------------
        $forecastDate = ! empty($this->selectedDate) ? $this->selectedDate : now()->toDateString();
        $scheduledTipsForDate = [];
        $totalMothersScheduledForDate = 0;

        if ($this->activeTab === 'forecast' || ! empty($this->selectedDate)) {
            // Find all active pregnancy sessions
            $historiesQuery = History::with(['mother.organization', 'organization'])
                ->whereHas('mother', fn ($mq) => $mq->whereNotNull('phone')->where('phone', '!=', ''));

            if (! $user->isSystemAdmin()) {
                $historiesQuery->where(function ($q) use ($user) {
                    $q->where('organization_id', $user->organization_id)
                        ->orWhereHas('mother', fn ($mq) => $mq->where('organization_id', $user->organization_id));
                });
            } elseif (! empty($this->organizationFilter)) {
                $orgId = (int) $this->organizationFilter;
                $historiesQuery->where(function ($q) use ($orgId) {
                    $q->where('organization_id', $orgId)
                        ->orWhereHas('mother', fn ($mq) => $mq->where('organization_id', $orgId));
                });
            }

            $targetCarbon = Carbon::parse($forecastDate)->startOfDay();
            $histories = $historiesQuery->get();

            // Collect gestational targets for that date
            $gestationalPairs = []; // [ ['week' => 10, 'day' => 2, 'org_id' => 1, 'history' => ...] ]
            $uniqueMotherIdsOnDate = [];

            foreach ($histories as $h) {
                if (empty($h->last_menstrual_cycle)) {
                    continue;
                }
                $lmc = Carbon::parse($h->last_menstrual_cycle)->startOfDay();
                if ($targetCarbon->lt($lmc)) {
                    continue;
                }

                $d = (int) $lmc->diffInDays($targetCarbon);
                if ($d > 300) {
                    continue;
                }

                $w = max(1, (int) ceil(($d === 0 ? 1 : $d) / 7));
                $dayInWeek = $d > 0 ? ((($d - 1) % 7) + 1) : 1;
                $mOrg = $h->organization_id ?? $h->mother?->organization_id;

                $gestationalPairs[] = [
                    'week' => $w,
                    'day' => $dayInWeek,
                    'cumulative_day' => $d,
                    'org_id' => $mOrg,
                    'history' => $h,
                ];
            }

            // For each distinct week/day/org combination, retrieve approved tips
            $tipsOnDateMap = [];

            foreach ($gestationalPairs as $pair) {
                $w = $pair['week'];
                $d = $pair['day'];
                $cum = $pair['cumulative_day'];
                $mOrg = $pair['org_id'];
                $h = $pair['history'];

                // Query tip for this pair
                $candidateTip = null;
                if (! empty($mOrg)) {
                    $candidateTip = Tip::withoutGlobalScope('organization')
                        ->with(['day_range', 'organization'])
                        ->where('week_id', $w)
                        ->where(function ($q) use ($d, $cum) {
                            $q->where('day_id', $d)->orWhere('day_id', $cum);
                        })
                        ->where('organization_id', $mOrg)
                        ->approved()
                        ->first();
                }

                if (! $candidateTip) {
                    $candidateTip = Tip::withoutGlobalScope('organization')
                        ->with(['day_range', 'organization'])
                        ->where('week_id', $w)
                        ->where(function ($q) use ($d, $cum) {
                            $q->where('day_id', $d)->orWhere('day_id', $cum);
                        })
                        ->whereNull('organization_id')
                        ->approved()
                        ->first();
                }

                if ($candidateTip) {
                    $tid = $candidateTip->id;
                    if (! isset($tipsOnDateMap[$tid])) {
                        $tipsOnDateMap[$tid] = [
                            'tip' => $candidateTip,
                            'mothers' => [],
                        ];
                    }

                    $tipsOnDateMap[$tid]['mothers'][] = [
                        'mother_id' => $h->mother_id,
                        'name' => $h->mother?->name ?? 'Unknown',
                        'phone' => $h->mother?->phone ?? 'N/A',
                        'gestation_week' => $w,
                        'gestation_day' => $d,
                        'org_name' => $h->mother?->organization?->name ?? ($h->organization?->name ?? 'None'),
                        'org_acronym' => $h->mother?->organization?->acronym ?? ($h->organization?->acronym ?? null),
                        'session_id' => $h->id,
                    ];

                    $uniqueMotherIdsOnDate[$h->mother_id] = true;
                }
            }

            $scheduledTipsForDate = array_values($tipsOnDateMap);
            $totalMothersScheduledForDate = count($uniqueMotherIdsOnDate);
        }

        // ----------------------------------------------------
        // TAB 3: SYSTEM TEMPLATES DIRECTORY
        // ----------------------------------------------------
        $templatesQuery = Tip::withoutGlobalScope('organization')
            ->with(['week.trimester', 'day_range', 'day', 'organization', 'creator'])
            ->where('is_template', true);

        if (! empty($this->templateTrimesterFilter)) {
            $tri = $this->templateTrimesterFilter;
            $templatesQuery->whereHas('week', fn ($wq) => $wq->where('trimester_id', $tri));
        }

        if (! empty($this->templateWeekFilter)) {
            $templatesQuery->where('week_id', $this->templateWeekFilter);
        }

        if (! empty($this->templateSearch)) {
            $ts = trim($this->templateSearch);
            $templatesQuery->where('tip', 'like', "%{$ts}%");
        }

        $templates = $templatesQuery->orderBy('week_id')->orderBy('day_id')->paginate(12, ['*'], 'templatesPage');
        $totalTemplatesCount = Tip::withoutGlobalScope('organization')->where('is_template', true)->count();

        // Dropdown data
        $organizations = $user->isSystemAdmin()
            ? Organization::where('is_pharmacy', false)->orderBy('name')->get()
            : collect([$user->organization])->filter();

        $trimesters = Trimester::orderBy('trimester')->get();
        $weeks = Week::orderBy('week')->get();
        $days = Day::orderBy('day_number')->get();
        $dayRanges = DayRange::all();

        return view('livewire.tips.tips-management-livewire', [
            'tips' => $tips,
            'templates' => $templates,
            'totalTemplatesCount' => $totalTemplatesCount,
            'stats' => [
                'total' => $totalTipsCount,
                'approved' => $approvedTipsCount,
                'pending' => $pendingTipsCount,
                'templates' => $totalTemplatesCount,
                'scheduledToday' => count($scheduledTipsForDate),
                'mothersScheduledToday' => $totalMothersScheduledForDate,
            ],
            'scheduledTipsForDate' => $scheduledTipsForDate,
            'totalMothersScheduledForDate' => $totalMothersScheduledForDate,
            'organizations' => $organizations,
            'trimesters' => $trimesters,
            'weeks' => $weeks,
            'days' => $days,
            'dayRanges' => $dayRanges,
            'isSystemAdmin' => $user->isSystemAdmin(),
        ]);
    }
}
