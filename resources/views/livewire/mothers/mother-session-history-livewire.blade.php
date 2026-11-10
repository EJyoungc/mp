<div>
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2 align-items-center">
                <div class="col-sm-7">
                    <h1 class="m-0 text-bold text-navy">
                        <i class="fas fa-history mr-2"></i> Session #{{ $session->infant_number }} SMS History
                    </h1>
                    <p class="text-muted mb-0">
                        SMS tips and notifications log for <strong>{{ $mother->name }}</strong>
                        <span class="badge badge-light border ml-1"><i class="fas fa-phone mr-1"></i> {{ $mother->phone ?: 'No Phone' }}</span>
                        @if($mother->organization)
                            <span class="badge badge-info ml-1">
                                <i class="fas fa-hospital mr-1"></i> {{ $mother->organization->name }}
                                @if($mother->organization->acronym)
                                    ({{ $mother->organization->acronym }})
                                @endif
                            </span>
                        @endif
                    </p>
                </div>
                <div class="col-sm-5 text-sm-right mt-2 mt-sm-0">
                    <a href="{{ route('mothers.show', \App\Helper\StandardData::encrypt($mother->id)) }}" class="btn btn-outline-secondary rounded-pill px-3 shadow-sm mr-2">
                        <i class="fas fa-arrow-left mr-1"></i> Back to Profile
                    </a>
                    <button wire:click="openComposeModal" class="btn btn-navy rounded-pill px-4 shadow-sm">
                        <i class="fas fa-paper-plane mr-1"></i> Compose / Send SMS
                    </button>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <!-- Stat Cards Row -->
            <div class="row mb-4">
                <div class="col-lg-3 col-6">
                    <div class="card shadow-sm border-0 bg-white rounded-lg">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-center">
                                <div class="rounded-circle bg-soft-navy text-navy d-flex align-items-center justify-content-center mr-3" style="width: 48px; height: 48px;">
                                    <i class="fas fa-envelope fa-lg"></i>
                                </div>
                                <div>
                                    <div class="text-muted small">Total Messages</div>
                                    <h3 class="font-weight-bold text-navy mb-0">{{ $totalMessagesCount }}</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-6">
                    <div class="card shadow-sm border-0 bg-white rounded-lg">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-center">
                                <div class="rounded-circle bg-light text-success d-flex align-items-center justify-content-center mr-3" style="width: 48px; height: 48px; background-color: #e8f5e9 !important;">
                                    <i class="fas fa-check-circle fa-lg"></i>
                                </div>
                                <div>
                                    <div class="text-muted small">Delivered / Sent</div>
                                    <h3 class="font-weight-bold text-success mb-0">{{ $totalSentCount }}</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-6">
                    <div class="card shadow-sm border-0 bg-white rounded-lg">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-center">
                                <div class="rounded-circle bg-light text-warning d-flex align-items-center justify-content-center mr-3" style="width: 48px; height: 48px; background-color: #fff8e1 !important;">
                                    <i class="fas fa-clock fa-lg"></i>
                                </div>
                                <div>
                                    <div class="text-muted small">Unsent / Pending</div>
                                    <h3 class="font-weight-bold text-warning mb-0">{{ $totalUnsentCount }}</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-6">
                    <div class="card shadow-sm border-0 bg-white rounded-lg">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-center">
                                <div class="rounded-circle bg-light text-danger d-flex align-items-center justify-content-center mr-3" style="width: 48px; height: 48px; background-color: #ffebee !important;">
                                    <i class="fas fa-exclamation-triangle fa-lg"></i>
                                </div>
                                <div>
                                    <div class="text-muted small">Failed</div>
                                    <h3 class="font-weight-bold text-danger mb-0">{{ $totalFailedCount }}</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filters and Search Toolbar -->
            <div class="card shadow-sm border-0 rounded-lg mb-4">
                <div class="card-body py-3">
                    <div class="row align-items-center">
                        <!-- Search Box -->
                        <div class="col-lg-4 col-md-6 mb-2 mb-lg-0">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text bg-white border-right-0"><i class="fas fa-search text-muted"></i></span>
                                </div>
                                <input type="text" class="form-control border-left-0" placeholder="Search tip text or mother..." wire:model.live.debounce.300ms="search">
                            </div>
                        </div>

                        <!-- Organization Filter -->
                        <div class="col-lg-4 col-md-6 mb-2 mb-lg-0">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text bg-white"><i class="fas fa-hospital text-muted"></i></span>
                                </div>
                                <select class="form-control" wire:model.live="organizationFilter">
                                    <option value="">All Organizations</option>
                                    @foreach($organizations as $org)
                                        <option value="{{ $org->id }}">
                                            {{ $org->name }} {{ $org->acronym ? "({$org->acronym})" : '' }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Status Filter Pills -->
                        <div class="col-lg-4 col-md-12 text-lg-right">
                            <div class="btn-group btn-group-toggle" data-toggle="buttons">
                                <button wire:click="$set('statusFilter', 'all')" class="btn btn-sm {{ $statusFilter === 'all' ? 'btn-navy' : 'btn-outline-secondary' }}">All</button>
                                <button wire:click="$set('statusFilter', 'sent')" class="btn btn-sm {{ $statusFilter === 'sent' ? 'btn-success' : 'btn-outline-secondary' }}">Sent</button>
                                <button wire:click="$set('statusFilter', 'unsent')" class="btn btn-sm {{ $statusFilter === 'unsent' ? 'btn-warning' : 'btn-outline-secondary' }}">Unsent</button>
                                <button wire:click="$set('statusFilter', 'failed')" class="btn btn-sm {{ $statusFilter === 'failed' ? 'btn-danger' : 'btn-outline-secondary' }}">Failed</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Messages Table Card -->
            <div class="card shadow-sm border-0 rounded-lg overflow-hidden">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light text-navy">
                                <tr>
                                    <th class="px-4 py-3 border-0" style="width: 60px;">#</th>
                                    <th class="py-3 border-0">Message Content (Health Tip)</th>
                                    <th class="py-3 border-0">Organization / Acronym</th>
                                    <th class="py-3 border-0 text-center">Schedule</th>
                                    <th class="py-3 border-0 text-center">Status</th>
                                    <th class="py-3 border-0 text-center">Timestamp</th>
                                    <th class="py-3 border-0 text-center" style="width: 220px;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($messages as $msg)
                                    @php
                                        $msgOrg = $msg->organization ?? $msg->tip?->organization ?? $msg->mother?->organization;
                                        $acronym = $msgOrg?->acronym;
                                        $displayTip = $msg->tip?->tip ?? 'No message body';
                                    @endphp
                                    <tr wire:key="msg-history-{{ $msg->id }}">
                                        <td class="px-4 py-3 text-muted font-weight-bold">{{ $msg->id }}</td>
                                        <td class="py-3">
                                            <div class="font-weight-bold text-dark mb-1">
                                                @if($acronym)
                                                    <span class="badge badge-light border text-primary font-weight-bold mr-1">{{ $acronym }}:</span>
                                                @endif
                                                {{ $displayTip }}
                                            </div>
                                            <small class="text-muted">
                                                <i class="fas fa-user mr-1"></i> {{ $msg->mother->name ?? 'Unknown' }}
                                                <span class="ml-2"><i class="fas fa-phone mr-1"></i> {{ $msg->mother->phone ?? 'N/A' }}</span>
                                            </small>
                                        </td>
                                        <td class="py-3">
                                            @if($msgOrg)
                                                <span class="font-weight-bold text-navy">{{ $msgOrg->name }}</span>
                                                @if($acronym)
                                                    <span class="badge badge-primary px-2 py-1 ml-1">{{ $acronym }}</span>
                                                @endif
                                            @else
                                                <span class="text-muted font-italic">Unassigned</span>
                                            @endif
                                        </td>
                                        <td class="py-3 text-center">
                                            @if($msg->week)
                                                <span class="badge badge-light border">Wk {{ $msg->week->week }}</span>
                                            @endif
                                            @if($msg->day_id)
                                                <span class="badge badge-light border">Day {{ $msg->day_id > 7 ? ((($msg->day_id - 1) % 7) + 1) : $msg->day_id }}</span>
                                            @endif
                                        </td>
                                        <td class="py-3 text-center">
                                            @php
                                                $st = strtolower($msg->message_status);
                                            @endphp
                                            @if($st === 'sent' || $st === 'delivered' || $st === 'success')
                                                <span class="badge badge-pill badge-success px-3 py-1 font-weight-bold">SENT</span>
                                            @elseif($st === 'failed' || $st === 'error')
                                                <span class="badge badge-pill badge-danger px-3 py-1 font-weight-bold">FAILED</span>
                                            @else
                                                <span class="badge badge-pill badge-warning px-3 py-1 font-weight-bold">UNSENT</span>
                                            @endif
                                        </td>
                                        <td class="py-3 text-center text-muted small">
                                            {{ $msg->created_at ? $msg->created_at->format('M d, Y H:i') : 'N/A' }}
                                        </td>
                                        <td class="py-3 text-center">
                                            <div class="btn-group shadow-sm rounded">
                                                <!-- Resend Action -->
                                                <button wire:click="resend({{ $msg->id }})" class="btn btn-white btn-sm px-2 text-primary" title="Resend SMS">
                                                    <i class="fas fa-redo-alt mr-1"></i> Resend
                                                </button>

                                                <!-- Modify Text & Send Action -->
                                                <button wire:click="openModifyModal({{ $msg->id }})" class="btn btn-white btn-sm px-2 text-info" title="Modify Text & Send">
                                                    <i class="fas fa-edit mr-1"></i> Modify
                                                </button>

                                                <!-- View Log Action -->
                                                @if($msg->api_response)
                                                    <button wire:click="showLog({{ $msg->id }})" class="btn btn-white btn-sm px-2 text-secondary" title="View API Log">
                                                        <i class="fas fa-file-alt"></i> Log
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-5 text-muted">
                                            <i class="fas fa-inbox fa-3x mb-3 text-secondary"></i>
                                            <h5>No Messages Found</h5>
                                            <p class="small mb-0">No SMS history matches the selected filters for this session.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if($messages->hasPages())
                    <div class="card-footer bg-white border-0 py-3">
                        {{ $messages->links() }}
                    </div>
                @endif
            </div>
        </div>
    </section>

    <!-- ======================================================== -->
    <!-- SEND / MODIFY SMS MODAL                                  -->
    <!-- ======================================================== -->
    <x-modal title="{{ $selectedMessageId ? 'Modify Health Tip & Send SMS' : 'Compose Instant SMS' }}" :status="$sendModal">
        <form wire:submit.prevent="sendModifiedSms">
            <!-- Toggle Switch: Based on Tip vs Custom Message -->
            <div class="card bg-light border-0 shadow-none mb-3">
                <div class="card-body p-2 d-flex justify-content-between align-items-center">
                    <span class="font-weight-bold text-navy small"><i class="fas fa-sliders-h mr-1"></i> Message Mode:</span>
                    <div class="btn-group btn-group-toggle" data-toggle="buttons">
                        <button type="button" wire:click="$set('isCustom', false)" class="btn btn-sm {{ !$isCustom ? 'btn-navy active font-weight-bold' : 'btn-outline-secondary' }}">
                            <i class="fas fa-book-medical mr-1"></i> Based on Health Tip
                        </button>
                        <button type="button" wire:click="$set('isCustom', true)" class="btn btn-sm {{ $isCustom ? 'btn-navy active font-weight-bold' : 'btn-outline-secondary' }}">
                            <i class="fas fa-comment-alt mr-1"></i> Custom SMS
                        </button>
                    </div>
                </div>
            </div>

            <!-- Organization Selector (System Admin) / Info Badge -->
            <div class="form-group mb-3">
                <label class="font-weight-bold text-navy small">Sending Organization & Acronym</label>
                @if($isSystemAdmin)
                    <select class="form-control" wire:model.live="selectedOrgId">
                        <option value="">Select Sending Organization</option>
                        @foreach($organizations as $org)
                            <option value="{{ $org->id }}">
                                {{ $org->name }} {{ $org->acronym ? "({$org->acronym})" : '' }}
                            </option>
                        @endforeach
                    </select>
                @else
                    <input type="text" class="form-control bg-light" value="{{ $mother->organization->name ?? auth()->user()->organization?->name ?? 'Default Organization' }}" readonly>
                @endif

                <div class="mt-2 d-flex align-items-center">
                    <span class="small text-muted mr-2">Organization SMS Prefix:</span>
                    @if($this->activeAcronym)
                        <span class="badge badge-primary px-3 py-1 font-weight-bold" style="font-size: 0.85rem;">
                            <i class="fas fa-tag mr-1"></i> {{ $this->activeAcronym }}
                        </span>
                        <small class="text-success ml-2"><i class="fas fa-check-circle mr-1"></i> Will be prepended to SMS</small>
                    @else
                        <span class="badge badge-secondary px-2 py-1 font-italic">No Acronym Configured</span>
                    @endif
                </div>
            </div>

            <!-- Recipient Mothers Multi-select and Search -->
            <div class="form-group mb-3">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <label class="font-weight-bold text-navy small mb-0">Recipient Mothers ({{ count($selectedMotherIds) }} selected)</label>
                    <div>
                        <button type="button" wire:click="resetRecipientsToCurrentMother" class="btn btn-link btn-xs text-muted p-0 mr-2">Reset to This Mother</button>
                        <button type="button" wire:click="selectAllMothersInOrg" class="btn btn-link btn-xs text-navy p-0">Select All in Org</button>
                    </div>
                </div>

                <!-- Selected Mothers Pills -->
                <div class="p-2 border rounded bg-white mb-2" style="max-height: 100px; overflow-y: auto;">
                    @forelse($selectedMothersModels as $selMother)
                        <span class="badge badge-pill badge-light border text-navy mr-1 mb-1 px-2 py-1 font-weight-normal">
                            {{ $selMother->name }} ({{ $selMother->phone ?: 'No phone' }})
                            <a href="#" wire:click.prevent="toggleMotherSelection({{ $selMother->id }})" class="text-danger ml-1 font-weight-bold">&times;</a>
                        </span>
                    @empty
                        <span class="text-danger small">No recipients selected.</span>
                    @endforelse
                </div>

                <!-- Recipient Search Input & Dropdown -->
                <div class="input-group input-group-sm mb-1">
                    <div class="input-group-prepend">
                        <span class="input-group-text bg-light"><i class="fas fa-search text-muted"></i></span>
                    </div>
                    <input type="text" class="form-control" placeholder="Search other mothers by name or phone to add..." wire:model.live.debounce.250ms="motherSearch">
                </div>

                @if(!empty($motherSearch) && $candidateMothers->count() > 0)
                    <div class="border rounded bg-light p-2 shadow-sm" style="max-height: 150px; overflow-y: auto;">
                        <small class="font-weight-bold text-muted d-block mb-1">Matching Mothers in Organization:</small>
                        @foreach($candidateMothers as $cm)
                            <div class="d-flex justify-content-between align-items-center py-1 border-bottom">
                                <span class="small font-weight-bold">{{ $cm->name }} ({{ $cm->phone }})</span>
                                @if(in_array($cm->id, $selectedMotherIds))
                                    <button type="button" wire:click="toggleMotherSelection({{ $cm->id }})" class="btn btn-xs btn-outline-danger">Remove</button>
                                @else
                                    <button type="button" wire:click="toggleMotherSelection({{ $cm->id }})" class="btn btn-xs btn-outline-navy">+ Add</button>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
                @error('selectedMotherIds') <div class="text-danger small">{{ $message }}</div> @enderror
            </div>

            <!-- Message Text Area -->
            <div class="form-group mb-3">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <label class="font-weight-bold text-navy small mb-0">Message Text Content <span class="text-danger">*</span></label>
                    <small class="{{ strlen($this->previewFormattedMessage) > 160 ? 'text-danger font-weight-bold' : 'text-muted' }}">
                        {{ strlen($this->previewFormattedMessage) }} / 160 chars
                    </small>
                </div>
                <textarea class="form-control" rows="3" wire:model.live.debounce.150ms="messageBody" placeholder="Type SMS content to send..."></textarea>
                @error('messageBody') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
            </div>

            <!-- Live SMS Preview Box -->
            <div class="card bg-light border shadow-none mb-3">
                <div class="card-body p-3">
                    <div class="small font-weight-bold text-muted mb-1">
                        <i class="fas fa-mobile-alt mr-1"></i> Recipient SMS Preview (Including Acronym):
                    </div>
                    <div class="p-2 bg-white rounded border text-dark font-weight-bold small" style="border-left: 4px solid #001f3f !important;">
                        {{ $this->previewFormattedMessage ?: 'Enter message content to preview...' }}
                    </div>
                </div>
            </div>

            <div class="modal-footer px-0 pb-0 border-top">
                <button type="button" class="btn btn-secondary rounded-pill px-4" wire:click="cancel">Cancel</button>
                <button type="submit" class="btn btn-navy rounded-pill px-4 shadow-sm">
                    <i class="fas fa-paper-plane mr-1"></i> Send SMS ({{ count($selectedMotherIds) }}) <x-spinner for="sendModifiedSms" />
                </button>
            </div>
        </form>
    </x-modal>

    <!-- ======================================================== -->
    <!-- API LOG MODAL                                            -->
    <!-- ======================================================== -->
    <x-modal title="Africa's Talking API Response Log" :status="$logModal">
        @if($selectedLog)
            <div class="row mb-3">
                <div class="col-md-6">
                    <small class="text-muted d-block">Log Timestamp</small>
                    <strong>{{ $selectedLog['timestamp'] ?? 'N/A' }}</strong>
                </div>
                <div class="col-md-6">
                    <small class="text-muted d-block">Gateway Status</small>
                    <span class="badge {{ ($selectedLog['status'] ?? '') == 'Success' || ($selectedLog['status'] ?? '') == 'Sent' ? 'badge-success' : 'badge-danger' }} px-2 py-1">
                        {{ $selectedLog['status'] ?? 'Unknown' }}
                    </span>
                    @if(isset($selectedLog['status_code']))
                        <span class="badge badge-light border ml-1">Code: {{ $selectedLog['status_code'] }}</span>
                    @endif
                </div>
            </div>

            @if(isset($selectedLog['error']))
                <div class="alert alert-danger py-2 px-3 small mb-3">
                    <strong>Error:</strong> {{ $selectedLog['error'] }}
                </div>
            @endif

            <label class="font-weight-bold text-navy small">Full JSON Gateway Payload:</label>
            <div class="bg-dark p-3 rounded text-light" style="max-height: 350px; overflow-y: auto;">
                <pre class="text-light mb-0" style="font-size: 0.8rem;"><code>{{ json_encode($selectedLog, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</code></pre>
            </div>
        @else
            <div class="text-center py-4 text-muted">
                <i class="fas fa-file-excel fa-2x mb-2"></i>
                <p>No response log recorded for this message history entry.</p>
            </div>
        @endif

        <div class="modal-footer px-0 pb-0 border-top mt-3">
            <button type="button" class="btn btn-secondary rounded-pill px-4" wire:click="cancel">Close</button>
        </div>
    </x-modal>

    <style>
        .text-navy { color: #001f3f !important; }
        .bg-soft-navy { background-color: #eef2f7; }
        .btn-navy { background-color: #001f3f; color: #fff; border: none; }
        .btn-navy:hover { background-color: #002d5c; color: #fff; }
        .btn-white { background-color: #fff; border: 1px solid #dee2e6; color: #495057; }
        .btn-white:hover { background-color: #f8f9fa; }
    </style>
</div>
