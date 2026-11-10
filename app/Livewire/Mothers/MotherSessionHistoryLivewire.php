<?php

namespace App\Livewire\Mothers;

use App\Helper\StandardData as SD;
use App\Models\DayRange;
use App\Models\History;
use App\Models\MessageHistory;
use App\Models\Organization;
use App\Models\Tip;
use App\Models\User;
use App\Services\SmsService;
use Illuminate\Support\Facades\Log;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use Livewire\WithPagination;

class MotherSessionHistoryLivewire extends Component
{
    use LivewireAlert;
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $mother_id;

    public $history_id;

    public $mother;

    public $session;

    // Filters & Pagination for Messages
    public $search = '';

    public $statusFilter = 'all'; // 'all', 'sent', 'unsent', 'failed'

    public $organizationFilter = '';

    public $perPage = 10;

    // Modals
    public $modal = false;

    public $logModal = false;

    public $selectedLog = null;

    public $sendModal = false;

    // Send / Modify SMS State
    public $isCustom = false; // Toggle switch: false = "Based on Tip", true = "Custom Message"

    public $selectedMessageId = null;

    public $selectedTipId = null;

    public $tipText = '';

    public $messageBody = '';

    public $selectedOrgId = null;

    // Recipient selection
    public $selectedMotherIds = [];

    public $motherSearch = '';

    public function mount($mother_id, $session_id): void
    {
        $mId = is_numeric($mother_id) ? (int) $mother_id : SD::decrypt($mother_id);
        $sId = is_numeric($session_id) ? (int) $session_id : SD::decrypt($session_id);

        $this->mother_id = $mId;
        $this->history_id = $sId;

        $this->mother = User::with('organization')->findOrFail($mId);
        $this->session = History::with(['mother', 'organization'])->findOrFail($sId);

        $user = auth()->user();
        if (! $user->isSystemAdmin() && $this->mother->organization_id !== $user->organization_id && $user->organization_id !== null) {
            abort(403, 'Unauthorized access to this session history.');
        }

        // Default sending organization
        $this->selectedOrgId = $this->mother->organization_id ?? $user->organization_id;

        // Default recipient is the current mother
        $this->selectedMotherIds = [$this->mother->id];
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatusFilter(): void
    {
        $this->resetPage();
    }

    public function updatingOrganizationFilter(): void
    {
        $this->resetPage();
    }

    public function updatingMotherSearch(): void
    {
        // keep input reactive
    }

    public function updatedIsCustom($value): void
    {
        if (! $value && ! empty($this->tipText)) {
            $this->messageBody = $this->tipText;
        } elseif ($value && $this->messageBody === $this->tipText) {
            $this->messageBody = '';
        }
    }

    // ========================================================
    // ROW ACTIONS: RESEND & VIEW LOG
    // ========================================================

    public function resend(int $id, SmsService $smsService): void
    {
        $message = MessageHistory::with(['tip', 'mother.organization', 'organization'])->findOrFail($id);

        if (empty($message->mother?->phone)) {
            $this->alert('error', 'Mother does not have a valid phone number registered.');

            return;
        }

        $rawText = $message->tip?->tip ?? 'Pregnancy Health Reminder';
        $org = $message->organization ?? $message->tip?->organization ?? $message->mother?->organization ?? auth()->user()->organization;
        $formattedText = SmsService::formatWithAcronym($rawText, $org?->acronym);

        try {
            $success = $smsService->sendSmsGeneric($message, $message->mother->phone, $formattedText);

            if ($success) {
                $message->update(['message_status' => 'sent']);
                $this->alert('success', 'SMS resent to '.$message->mother->name.' successfully.');
            } else {
                $message->update(['message_status' => 'failed']);
                $this->alert('warning', 'SMS gateway reported a failure while resending.');
            }
        } catch (\Throwable $e) {
            Log::error('Resend SMS Error: '.$e->getMessage());
            $this->alert('error', 'Error resending SMS: '.$e->getMessage());
        }
    }

    public function showLog(int $id): void
    {
        $mh = MessageHistory::findOrFail($id);
        $this->selectedLog = $mh->api_response;
        $this->logModal = true;
    }

    // ========================================================
    // COMPOSE / MODIFY TEXT MODAL
    // ========================================================

    public function openModifyModal(int $id): void
    {
        $this->resetErrorBag();
        $message = MessageHistory::with(['tip', 'organization'])->findOrFail($id);

        $this->selectedMessageId = $message->id;
        $this->selectedTipId = $message->tip_id;
        $this->tipText = $message->tip?->tip ?? '';
        $this->messageBody = $this->tipText;
        $this->isCustom = false;
        $this->selectedOrgId = $message->organization_id ?? $this->mother->organization_id ?? auth()->user()->organization_id;
        $this->selectedMotherIds = [$this->mother->id];
        $this->motherSearch = '';

        $this->sendModal = true;
    }

    public function openComposeModal(): void
    {
        $this->resetErrorBag();
        $this->selectedMessageId = null;
        $this->selectedTipId = null;
        $this->tipText = '';
        $this->messageBody = '';
        $this->isCustom = true;
        $this->selectedOrgId = $this->mother->organization_id ?? auth()->user()->organization_id;
        $this->selectedMotherIds = [$this->mother->id];
        $this->motherSearch = '';

        $this->sendModal = true;
    }

    public function toggleMotherSelection(int $motherId): void
    {
        if (in_array($motherId, $this->selectedMotherIds)) {
            $this->selectedMotherIds = array_values(array_diff($this->selectedMotherIds, [$motherId]));
        } else {
            $this->selectedMotherIds[] = $motherId;
        }
    }

    public function resetRecipientsToCurrentMother(): void
    {
        $this->selectedMotherIds = [$this->mother->id];
        $this->motherSearch = '';
    }

    public function selectAllMothersInOrg(): void
    {
        $orgId = $this->selectedOrgId ?? $this->mother->organization_id;
        $allIds = User::whereHas('role', fn ($q) => $q->where('name', 'mother'))
            ->when($orgId, fn ($q) => $q->where('organization_id', $orgId))
            ->whereNotNull('phone')
            ->where('phone', '!=', '')
            ->pluck('id')
            ->toArray();

        $this->selectedMotherIds = array_unique(array_merge($this->selectedMotherIds, $allIds));
    }

    public function sendModifiedSms(SmsService $smsService): void
    {
        $this->validate([
            'messageBody' => 'required|string|min:3|max:160',
            'selectedMotherIds' => 'required|array|min:1',
            'selectedOrgId' => 'nullable|exists:organizations,id',
        ], [
            'messageBody.required' => 'Please enter the SMS message text.',
            'messageBody.max' => 'The message text cannot exceed 160 characters.',
            'selectedMotherIds.min' => 'Please select at least one recipient mother.',
        ]);

        $org = $this->selectedOrgId ? Organization::find($this->selectedOrgId) : ($this->mother->organization ?? auth()->user()->organization);
        $acronym = $org?->acronym;
        $formattedMessage = SmsService::formatWithAcronym($this->messageBody, $acronym);

        $recipients = User::whereIn('id', $this->selectedMotherIds)->get();
        $sentCount = 0;
        $failCount = 0;

        foreach ($recipients as $recipientMother) {
            if (empty($recipientMother->phone)) {
                $failCount++;

                continue;
            }

            try {
                // Ensure tip record exists
                $tip = Tip::create([
                    'tip' => $this->messageBody,
                    'week_id' => 1,
                    'day_id' => 1,
                    'day_range_id' => DayRange::first()->id ?? 1,
                    'organization_id' => $org?->id,
                    'status' => Tip::STATUS_APPROVED,
                ]);

                $recipientSession = History::where('mother_id', $recipientMother->id)->latest()->first();

                $history = MessageHistory::create([
                    'tip_id' => $tip->id,
                    'week_id' => 1,
                    'day_id' => 1,
                    'day_range_id' => DayRange::first()->id ?? 1,
                    'history_id' => $recipientSession?->id ?? $this->history_id,
                    'mother_id' => $recipientMother->id,
                    'message_status' => 'pending',
                    'organization_id' => $org?->id,
                ]);

                $success = $smsService->sendSmsGeneric($history, $recipientMother->phone, $formattedMessage);

                if ($success) {
                    $history->update(['message_status' => 'sent']);
                    $sentCount++;
                } else {
                    $history->update(['message_status' => 'failed']);
                    $failCount++;
                }
            } catch (\Throwable $e) {
                Log::error('Send SMS Error to mother '.$recipientMother->id.': '.$e->getMessage());
                $failCount++;
            }
        }

        if ($sentCount > 0) {
            $this->alert('success', "SMS dispatched to {$sentCount} mother(s) successfully.".($failCount > 0 ? " ({$failCount} failed)" : ''));
        } else {
            $this->alert('error', 'Failed to dispatch SMS to selected mother(s).');
        }

        $this->cancel();
    }

    public function cancel(): void
    {
        $this->reset([
            'modal',
            'logModal',
            'selectedLog',
            'sendModal',
            'messageBody',
            'tipText',
            'isCustom',
            'motherSearch',
            'selectedMessageId',
            'selectedTipId',
        ]);

        if ($this->mother) {
            $this->selectedMotherIds = [$this->mother->id];
            $this->selectedOrgId = $this->mother->organization_id ?? auth()->user()->organization_id;
        }
    }

    public function getActiveAcronymProperty(): ?string
    {
        if ($this->selectedOrgId) {
            return Organization::where('id', $this->selectedOrgId)->value('acronym');
        }

        return $this->mother->organization?->acronym ?? auth()->user()->organization?->acronym;
    }

    public function getPreviewFormattedMessageProperty(): string
    {
        return SmsService::formatWithAcronym($this->messageBody, $this->activeAcronym);
    }

    public function render()
    {
        $user = auth()->user();

        // Query messages for this mother and session (or all messages for this mother)
        $query = MessageHistory::with(['tip', 'mother.organization', 'week', 'day', 'organization'])
            ->where('mother_id', $this->mother_id)
            ->where('history_id', $this->history_id);

        if (! empty($this->search)) {
            $s = $this->search;
            $query->where(function ($q) use ($s) {
                $q->whereHas('tip', function ($tq) use ($s) {
                    $tq->where('tip', 'like', "%{$s}%");
                })->orWhereHas('mother', function ($mq) use ($s) {
                    $mq->where('name', 'like', "%{$s}%")
                        ->orWhere('phone', 'like', "%{$s}%");
                });
            });
        }

        if ($this->statusFilter !== 'all') {
            if ($this->statusFilter === 'sent') {
                $query->whereIn('message_status', ['sent', 'Sent', 'Delivered', 'delivered']);
            } elseif ($this->statusFilter === 'failed') {
                $query->whereIn('message_status', ['failed', 'Failed', 'Error', 'error']);
            } elseif ($this->statusFilter === 'unsent') {
                $query->whereIn('message_status', ['unsent', 'Unsent', 'pending', 'Pending']);
            }
        }

        if (! empty($this->organizationFilter)) {
            $orgId = (int) $this->organizationFilter;
            $query->where(function ($q) use ($orgId) {
                $q->where('organization_id', $orgId)
                    ->orWhereHas('mother', fn ($mq) => $mq->where('organization_id', $orgId));
            });
        }

        $messages = $query->latest()->paginate($this->perPage);

        // Calculate session statistics
        $baseStatsQuery = MessageHistory::where('mother_id', $this->mother_id)->where('history_id', $this->history_id);
        $totalMessagesCount = (clone $baseStatsQuery)->count();
        $totalSentCount = (clone $baseStatsQuery)->whereIn('message_status', ['sent', 'Sent', 'Delivered', 'delivered'])->count();
        $totalUnsentCount = (clone $baseStatsQuery)->whereIn('message_status', ['unsent', 'Unsent', 'pending', 'Pending'])->count();
        $totalFailedCount = (clone $baseStatsQuery)->whereIn('message_status', ['failed', 'Failed', 'Error', 'error'])->count();

        // Organizations list for filtering & system admin selector
        $organizations = Organization::orderBy('name')->get();

        // Candidate mothers for multi-recipient picker
        $candidateMothersQuery = User::whereHas('role', fn ($q) => $q->where('name', 'mother'))
            ->when(! $user->isSystemAdmin(), fn ($q) => $q->where('organization_id', $user->organization_id))
            ->when($user->isSystemAdmin() && $this->selectedOrgId, fn ($q) => $q->where('organization_id', $this->selectedOrgId));

        if (! empty($this->motherSearch)) {
            $ms = $this->motherSearch;
            $candidateMothersQuery->where(function ($q) use ($ms) {
                $q->where('name', 'like', "%{$ms}%")
                    ->orWhere('phone', 'like', "%{$ms}%");
            });
        }

        $candidateMothers = $candidateMothersQuery->take(15)->get();
        $selectedMothersModels = User::whereIn('id', $this->selectedMotherIds)->get();

        return view('livewire.mothers.mother-session-history-livewire', [
            'messages' => $messages,
            'totalMessagesCount' => $totalMessagesCount,
            'totalSentCount' => $totalSentCount,
            'totalUnsentCount' => $totalUnsentCount,
            'totalFailedCount' => $totalFailedCount,
            'organizations' => $organizations,
            'candidateMothers' => $candidateMothers,
            'selectedMothersModels' => $selectedMothersModels,
            'isSystemAdmin' => $user->isSystemAdmin(),
        ]);
    }
}
