<?php

namespace App\Livewire\Mothers;

use App\Helper\StandardData as SD;
use App\Models\AdHistory;
use App\Models\Day;
use App\Models\DayRange;
use App\Models\History;
use App\Models\MessageHistory;
use App\Models\Tip;
use App\Models\Trimester;
use App\Models\User;
use App\Models\Week;
use App\Services\SmsService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use Livewire\WithPagination;

class MotherLivewire extends Component
{
    use LivewireAlert;
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $mother_id;

    public $mother;

    public $activeTab = 'sessions'; // 'sessions', 'messages', 'profile'

    // Pregnancy / Session Modal State
    public $modal = false;

    public $history_id;

    public $infant_number = 1;

    public $last_menstrual_cycle;

    public $deleteModal = false;

    public $confirmingDeleteSessionId = null;

    // Messages Filtering & Inspection
    public $messageSearch = '';

    public $messageTrimesterFilter = '';

    public $messageStatusFilter = '';

    public $messageTypeFilter = 'all'; // 'all', 'tips', 'ads'

    public $viewMessageModal = false;

    public $selectedMessage = null;

    // Custom SMS Modal
    public $customSmsModal = false;

    public $customMessageText = '';

    public function mount($mother_id): void
    {
        $id = SD::decrypt($mother_id);
        $user = User::with(['district', 'area', 'organization'])->findOrFail($id);

        // Authorization: System Admin or Staff from the same organization
        if (! auth()->user()->isSystemAdmin() && $user->organization_id !== auth()->user()->organization_id) {
            abort(403, 'Unauthorized access to this mother record.');
        }

        $this->mother = $user;
        $this->mother_id = $user;
    }

    public function setTab(string $tab): void
    {
        $this->activeTab = in_array($tab, ['sessions', 'messages', 'profile']) ? $tab : 'sessions';
    }

    public function updatingMessageSearch(): void
    {
        $this->resetPage('messages_page');
    }

    public function updatingMessageTrimesterFilter(): void
    {
        $this->resetPage('messages_page');
    }

    public function updatingMessageStatusFilter(): void
    {
        $this->resetPage('messages_page');
    }

    public function updatingMessageTypeFilter(): void
    {
        $this->resetPage('messages_page');
    }

    // ========================================================
    // PREGNANCY SESSIONS (INFANTS) CRUD
    // ========================================================

    public function create($id = null): void
    {
        $this->resetErrorBag();

        if (empty($id)) {
            $this->history_id = null;
            // Suggest next infant number
            $latestInfant = History::where('mother_id', $this->mother->id)->max('infant_number') ?? 0;
            $this->infant_number = $latestInfant + 1;
            $this->last_menstrual_cycle = now()->subWeeks(8)->format('Y-m-d');
        } else {
            $session = History::where('mother_id', $this->mother->id)->findOrFail($id);
            $this->history_id = $session->id;
            $this->infant_number = $session->infant_number;
            $this->last_menstrual_cycle = Carbon::parse($session->last_menstrual_cycle)->format('Y-m-d');
        }

        $this->modal = true;
    }

    public function store(): void
    {
        $this->validate([
            'infant_number' => 'required|numeric|min:1',
            'last_menstrual_cycle' => 'required|date|before_or_equal:today',
        ], [
            'infant_number.required' => 'Please enter the infant or pregnancy sequence number.',
            'last_menstrual_cycle.required' => 'Last Menstrual Period (LMP) date is required.',
            'last_menstrual_cycle.before_or_equal' => 'LMP date cannot be in the future.',
        ]);

        $orgId = $this->mother->organization_id ?? auth()->user()->organization_id;

        if (empty($this->history_id)) {
            History::create([
                'mother_id' => $this->mother->id,
                'infant_number' => $this->infant_number,
                'last_menstrual_cycle' => $this->last_menstrual_cycle,
                'organization_id' => $orgId,
            ]);

            $this->alert('success', "Pregnancy session (Infant #{$this->infant_number}) added successfully.");
        } else {
            $session = History::where('mother_id', $this->mother->id)->findOrFail($this->history_id);
            $session->update([
                'infant_number' => $this->infant_number,
                'last_menstrual_cycle' => $this->last_menstrual_cycle,
            ]);

            $this->alert('success', "Pregnancy session (Infant #{$this->infant_number}) updated successfully.");
        }

        $this->cancel();
    }

    public function confirmDeleteSession(int $id): void
    {
        $session = History::where('mother_id', $this->mother->id)->findOrFail($id);
        $this->confirmingDeleteSessionId = $session->id;
        $this->deleteModal = true;
    }

    public function deleteSession(): void
    {
        if (! $this->confirmingDeleteSessionId) {
            return;
        }

        $session = History::where('mother_id', $this->mother->id)->findOrFail($this->confirmingDeleteSessionId);
        $num = $session->infant_number;
        $session->delete();

        $this->deleteModal = false;
        $this->confirmingDeleteSessionId = null;
        $this->alert('success', "Pregnancy session (Infant #{$num}) deleted successfully.");
    }

    // ========================================================
    // MESSAGING & NOTIFICATIONS
    // ========================================================

    public function viewMessage(int $id, string $type = 'tip'): void
    {
        if ($type === 'ad') {
            $ad = AdHistory::with('pharmacyAd')->where('mother_id', $this->mother->id)->findOrFail($id);
            $this->selectedMessage = [
                'type' => 'ad',
                'title' => 'Pharmacy Health Alert / Ad',
                'body' => $ad->message ?? ($ad->pharmacyAd->ad_message ?? 'N/A'),
                'status' => $ad->status ?? 'Sent',
                'date' => $ad->created_at->format('M d, Y • h:i A'),
                'week' => null,
                'trimester' => null,
                'response' => $ad->response,
            ];
        } else {
            $msg = MessageHistory::with(['tip', 'week.trimester', 'day', 'day_ranger'])
                ->where('mother_id', $this->mother->id)
                ->findOrFail($id);

            $this->selectedMessage = [
                'type' => 'tip',
                'title' => 'Weekly Health Tip SMS',
                'body' => $msg->tip->tip ?? 'No message body available',
                'status' => $msg->message_status ?? 'Sent',
                'date' => $msg->created_at->format('M d, Y • h:i A'),
                'week' => $msg->week->week ?? null,
                'trimester' => $msg->week->trimester->trimester ?? ($msg->week->trimester_id ?? null),
                'day' => $msg->day->day_number ?? null,
                'time_range' => $msg->day_ranger->name ?? null,
                'response' => $msg->api_response,
            ];
        }

        $this->viewMessageModal = true;
    }

    public function openCustomSmsModal(): void
    {
        $this->resetErrorBag();
        $this->customMessageText = '';
        $this->customSmsModal = true;
    }

    public function sendCustomSms(SmsService $smsService): void
    {
        $this->validate([
            'customMessageText' => 'required|string|max:160',
        ], [
            'customMessageText.required' => 'Please enter SMS text to send.',
            'customMessageText.max' => 'SMS text cannot exceed 160 characters.',
        ]);

        if (empty($this->mother->phone)) {
            $this->alert('error', 'Mother does not have a phone number registered.');

            return;
        }

        $orgId = $this->mother->organization_id ?? auth()->user()->organization_id;

        try {
            $activeSession = History::where('mother_id', $this->mother->id)->latest()->first();
            $historyId = $activeSession ? $activeSession->id : 0;

            $day = Day::firstOrCreate(['day_number' => 1]);
            $dayRange = DayRange::firstOrCreate(['name' => 'General', 'start_time' => '08:00', 'end_time' => '17:00']);
            $trimester = Trimester::firstOrCreate(['trimester' => 1]);
            $week = Week::firstOrCreate(['week' => 1, 'trimester_id' => $trimester->id]);

            $tip = Tip::create([
                'tip' => $this->customMessageText,
                'week_id' => $week->id,
                'day_id' => $day->id,
                'day_range_id' => $dayRange->id,
                'organization_id' => $orgId,
                'status' => Tip::STATUS_APPROVED,
            ]);

            $history = MessageHistory::create([
                'tip_id' => $tip->id,
                'week_id' => $week->id,
                'day_id' => $day->id,
                'day_range_id' => $dayRange->id,
                'history_id' => $historyId,
                'mother_id' => $this->mother->id,
                'message_status' => 'Pending',
                'organization_id' => $orgId,
            ]);

            $success = $smsService->sendSmsGeneric($history, $this->mother->phone, $this->customMessageText);

            if ($success) {
                $history->update(['message_status' => 'Sent']);
                $this->alert('success', 'Custom SMS dispatched to mother successfully.');
            } else {
                $history->update(['message_status' => 'Failed']);
                $this->alert('warning', 'SMS gateway reported a dispatch issue.');
            }
        } catch (\Exception $e) {
            Log::error('Error sending custom SMS from mother profile: '.$e->getMessage());
            $this->alert('error', 'Failed to dispatch SMS: '.$e->getMessage());
        }

        $this->customSmsModal = false;
        $this->customMessageText = '';
    }

    public function resendTipSms(int $messageHistoryId, SmsService $smsService): void
    {
        $message = MessageHistory::with('tip')->where('mother_id', $this->mother->id)->findOrFail($messageHistoryId);

        if (empty($this->mother->phone)) {
            $this->alert('error', 'Mother does not have a valid phone number.');

            return;
        }

        $text = $message->tip->tip ?? 'Pregnancy Health Reminder';

        try {
            $success = $smsService->sendSmsGeneric($message, $this->mother->phone, $text);

            if ($success) {
                $message->update(['message_status' => 'Sent']);
                $this->alert('success', 'SMS resent successfully.');
            } else {
                $message->update(['message_status' => 'Failed']);
                $this->alert('warning', 'SMS gateway returned error.');
            }
        } catch (\Exception $e) {
            Log::error('Failed to resend SMS: '.$e->getMessage());
            $this->alert('error', 'Error sending SMS: '.$e->getMessage());
        }
    }

    // ========================================================
    // MODAL CANCEL (Strict project convention)
    // ========================================================

    public function cancel(): void
    {
        $this->reset([
            'modal',
            'deleteModal',
            'viewMessageModal',
            'customSmsModal',
            'history_id',
            'confirmingDeleteSessionId',
            'selectedMessage',
            'customMessageText',
        ]);
        $this->resetErrorBag();
    }

    // ========================================================
    // COMPUTED METRICS & RENDER
    // ========================================================

    public function render()
    {
        $this->mother->loadMissing(['district', 'area', 'organization']);

        // All pregnancy sessions
        $allSessions = History::where('mother_id', $this->mother->id)
            ->orderByDesc('infant_number')
            ->get();

        // Active / primary session is the most recent infant
        $activeSession = $allSessions->first();
        $pregnancyData = null;

        if ($activeSession && $activeSession->last_menstrual_cycle) {
            $lmp = Carbon::parse($activeSession->last_menstrual_cycle);
            $now = Carbon::now();
            $days = (int) max(0, $lmp->diffInDays($now));
            $weeks = (int) max(1, ceil($days / 7));
            $dayInWeek = ($days % 7) + 1;
            $edd = $lmp->copy()->addDays(280);
            $daysToEdd = (int) round($now->diffInDays($edd, false));

            $trimester = $weeks <= 12 ? 1 : ($weeks <= 27 ? 2 : 3);
            $trimesterName = $weeks <= 12 ? '1st Trimester' : ($weeks <= 27 ? '2nd Trimester' : '3rd Trimester');
            $progress = min(100, max(0, round(($weeks / 40) * 100)));

            $pregnancyData = [
                'session' => $activeSession,
                'lmp' => $lmp,
                'edd' => $edd,
                'days' => $days,
                'weeks' => $weeks,
                'dayInWeek' => $dayInWeek,
                'daysToEdd' => $daysToEdd,
                'trimester' => $trimester,
                'trimesterName' => $trimesterName,
                'progress' => $progress,
                'isDelivered' => $weeks > 40,
            ];
        }

        // Tips Messages Query
        $tipsQuery = MessageHistory::with(['tip', 'week.trimester', 'day', 'day_ranger'])
            ->where('mother_id', $this->mother->id);

        if (! empty($this->messageSearch)) {
            $s = trim($this->messageSearch);
            $tipsQuery->whereHas('tip', function ($q) use ($s) {
                $q->where('tip', 'like', "%{$s}%");
            });
        }

        if (! empty($this->messageTrimesterFilter)) {
            $t = (int) $this->messageTrimesterFilter;
            $tipsQuery->whereHas('week', function ($q) use ($t) {
                $q->where('trimester_id', $t);
            });
        }

        if (! empty($this->messageStatusFilter)) {
            $tipsQuery->where('message_status', $this->messageStatusFilter);
        }

        $tipsMessages = $tipsQuery->latest()->paginate(10, ['*'], 'messages_page');

        // Total Messages Counts
        $totalTipsCount = MessageHistory::where('mother_id', $this->mother->id)->count();
        $totalAdsCount = AdHistory::where('mother_id', $this->mother->id)->count();
        $totalSentTips = MessageHistory::where('mother_id', $this->mother->id)
            ->whereIn('message_status', ['Sent', 'Delivered', 'Success', 'sent', 'delivered'])
            ->count();
        $totalFailedTips = MessageHistory::where('mother_id', $this->mother->id)
            ->whereIn('message_status', ['Failed', 'failed', 'Error', 'error'])
            ->count();

        $deliveryRate = $totalTipsCount > 0 ? round(($totalSentTips / $totalTipsCount) * 100) : 100;

        // Pharmacy Ads outreach
        $adsMessages = [];
        if ($this->messageTypeFilter === 'all' || $this->messageTypeFilter === 'ads') {
            $adsMessages = AdHistory::with('pharmacyAd')
                ->where('mother_id', $this->mother->id)
                ->latest()
                ->take(15)
                ->get();
        }

        return view('livewire.mothers.mother-livewire', [
            'mother' => $this->mother,
            'sessions' => $allSessions,
            'activeSession' => $activeSession,
            'pregnancyData' => $pregnancyData,
            'tipsMessages' => $tipsMessages,
            'adsMessages' => $adsMessages,
            'totalTipsCount' => $totalTipsCount,
            'totalAdsCount' => $totalAdsCount,
            'totalSentTips' => $totalSentTips,
            'totalFailedTips' => $totalFailedTips,
            'deliveryRate' => $deliveryRate,
        ]);
    }
}
