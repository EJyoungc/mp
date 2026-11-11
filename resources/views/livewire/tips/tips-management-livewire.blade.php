<div>
    <!-- Content Header -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2 align-items-center">
                <div class="col-sm-6">
                    <h1 class="m-0 text-bold text-navy">
                        <i class="fas fa-lightbulb mr-2 text-warning"></i> Maternal Health Tips
                    </h1>
                    <p class="text-muted mb-0">Manage clinical maternal tips, organization targeting, and scheduled SMS delivery forecasting.</p>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Tips Management</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <section class="content">
        <div class="container-fluid">

            <!-- KPI Summary Cards -->
            <div class="row mb-3">
                <div class="col-6 col-md-3 mb-2">
                    <div class="small-box bg-navy shadow-sm mb-0 rounded">
                        <div class="inner p-3">
                            <h3 class="mb-1">{{ number_format($stats['total'] ?? 0) }}</h3>
                            <p class="mb-0 text-white-50 small text-uppercase font-weight-bold">Total Tips</p>
                        </div>
                        <div class="icon"><i class="fas fa-lightbulb"></i></div>
                    </div>
                </div>
                <div class="col-6 col-md-3 mb-2">
                    <div class="small-box bg-success shadow-sm mb-0 rounded">
                        <div class="inner p-3">
                            <h3 class="mb-1">{{ number_format($stats['approved'] ?? 0) }}</h3>
                            <p class="mb-0 text-white-50 small text-uppercase font-weight-bold">Approved Tips</p>
                        </div>
                        <div class="icon"><i class="fas fa-check-circle"></i></div>
                    </div>
                </div>
                <div class="col-6 col-md-3 mb-2">
                    <div class="small-box bg-warning shadow-sm mb-0 rounded">
                        <div class="inner p-3">
                            <h3 class="mb-1 text-dark">{{ number_format($stats['pending'] ?? 0) }}</h3>
                            <p class="mb-0 text-dark-50 small text-uppercase font-weight-bold text-dark">Pending Approval</p>
                        </div>
                        <div class="icon"><i class="fas fa-clock"></i></div>
                    </div>
                </div>
                <div class="col-6 col-md-3 mb-2">
                    <div class="small-box bg-info shadow-sm mb-0 rounded">
                        <div class="inner p-3">
                            <h3 class="mb-1">{{ number_format($stats['mothersScheduledToday'] ?? 0) }}</h3>
                            <p class="mb-0 text-white-50 small text-uppercase font-weight-bold">Mothers Due ({{ \Carbon\Carbon::parse($selectedDate ?: now())->format('M d') }})</p>
                        </div>
                        <div class="icon"><i class="fas fa-paper-plane"></i></div>
                    </div>
                </div>
            </div>

            <!-- Main Card with Tabs -->
            <div class="card shadow-sm border-0 rounded-lg overflow-hidden">
                <div class="card-header bg-white p-2 border-bottom d-flex align-items-center justify-content-between flex-wrap">
                    <!-- Nav Tabs -->
                    <ul class="nav nav-pills" id="tips-tabs">
                        <li class="nav-item">
                            <a class="nav-link font-weight-bold {{ $activeTab === 'all' ? 'active bg-navy text-white' : 'text-secondary' }} px-3"
                               href="javascript:void(0)" wire:click="setTab('all')">
                                <i class="fas fa-list mr-1"></i> All Tips Directory ({{ $stats['total'] }})
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link font-weight-bold {{ $activeTab === 'forecast' ? 'active bg-navy text-white' : 'text-secondary' }} px-3"
                               href="javascript:void(0)" wire:click="setTab('forecast')">
                                <i class="fas fa-calendar-alt mr-1"></i> Daily SMS Delivery Forecast
                                @if($totalMothersScheduledForDate > 0)
                                    <span class="badge badge-warning ml-1 text-dark">{{ $totalMothersScheduledForDate }}</span>
                                @endif
                            </a>
                        </li>
                    </ul>

                    <!-- Action Button -->
                    <div class="my-1 mr-2">
                        <button wire:click="openCreateModal" class="btn btn-navy btn-sm shadow-sm font-weight-bold">
                            <i class="fas fa-plus-circle mr-1"></i> Create New Tip
                        </button>
                    </div>
                </div>

                <div class="card-body p-0">

                    <!-- =================================================== -->
                    <!-- TAB 1: ALL TIPS DIRECTORY -->
                    <!-- =================================================== -->
                    @if($activeTab === 'all')
                        <div class="p-3 bg-light border-bottom">
                            <!-- Filters Row -->
                            <div class="row align-items-center">
                                <!-- Search -->
                                <div class="col-lg-3 col-md-6 mb-2">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text bg-white border-right-0"><i class="fas fa-search text-muted"></i></span>
                                        </div>
                                        <input type="text" class="form-control border-left-0" placeholder="Search tip text or org..." wire:model.live.debounce.300ms="search">
                                        @if(!empty($search))
                                            <div class="input-group-append">
                                                <button class="btn btn-outline-secondary" type="button" wire:click="$set('search', '')"><i class="fas fa-times"></i></button>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <!-- Organization Filter (System Admin) -->
                                @if($isSystemAdmin)
                                    <div class="col-lg-3 col-md-6 mb-2">
                                        <select wire:model.live="organizationFilter" class="form-control custom-select">
                                            <option value="">-- All Organizations --</option>
                                            <option value="global">Global / System Templates (No Org)</option>
                                            @foreach($organizations as $org)
                                                <option value="{{ $org->id }}">
                                                    {{ $org->name }} {{ $org->acronym ? '[' . $org->acronym . ']' : '' }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                @endif

                                <!-- Trimester Filter -->
                                <div class="col-lg-2 col-md-4 mb-2">
                                    <select wire:model.live="trimesterFilter" class="form-control custom-select">
                                        <option value="">-- Trimester --</option>
                                        @foreach($trimesters as $t)
                                            <option value="{{ $t->id }}">Trimester {{ $t->trimester }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Week Filter -->
                                <div class="col-lg-2 col-md-4 mb-2">
                                    <select wire:model.live="weekFilter" class="form-control custom-select">
                                        <option value="">-- Week --</option>
                                        @for($w = 1; $w <= 42; $w++)
                                            <option value="{{ $w }}">Week {{ $w }}</option>
                                        @endfor
                                    </select>
                                </div>

                                <!-- Day Filter -->
                                <div class="col-lg-1 col-md-4 mb-2">
                                    <select wire:model.live="dayFilter" class="form-control custom-select">
                                        <option value="">-- Day --</option>
                                        @for($d = 1; $d <= 7; $d++)
                                            <option value="{{ $d }}">Day {{ $d }}</option>
                                        @endfor
                                    </select>
                                </div>

                                <!-- Status Filter -->
                                <div class="col-lg-1 col-md-4 mb-2">
                                    <select wire:model.live="statusFilter" class="form-control custom-select">
                                        <option value="">-- Status --</option>
                                        <option value="{{ \App\Models\Tip::STATUS_APPROVED }}">Approved</option>
                                        <option value="{{ \App\Models\Tip::STATUS_PENDING }}">Pending</option>
                                        <option value="{{ \App\Models\Tip::STATUS_DRAFT }}">Draft</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Reset filters line if active -->
                            @if(!empty($search) || !empty($trimesterFilter) || !empty($weekFilter) || !empty($dayFilter) || !empty($statusFilter) || ($isSystemAdmin && !empty($organizationFilter)))
                                <div class="mt-2 d-flex align-items-center justify-content-between flex-wrap">
                                    <div class="small text-muted">
                                        <i class="fas fa-filter text-primary mr-1"></i> Filters active
                                    </div>
                                    <button wire:click="resetFilters" class="btn btn-outline-danger btn-xs font-weight-bold">
                                        <i class="fas fa-undo mr-1"></i> Clear All Filters
                                    </button>
                                </div>
                            @endif
                        </div>

                        <!-- Tips Table -->
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="thead-light">
                                    <tr>
                                        <th style="width: 50px;">#</th>
                                        <th>Tip Content & Acronym Preview</th>
                                        <th>Gestation</th>
                                        <th>Schedule Window</th>
                                        <th>Organization</th>
                                        <th>Status</th>
                                        <th class="text-center">Due Mothers</th>
                                        <th class="text-right pr-3" style="min-width: 150px;">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($tips as $tip)
                                        @php
                                            $orgAcronym = $tip->organization?->acronym;
                                            $formattedPreview = \App\Services\SmsService::formatWithAcronym($tip->tip, $orgAcronym);
                                        @endphp
                                        <tr wire:key="tip-row-{{ $tip->id }}">
                                            <td><strong>{{ $tip->id }}</strong></td>

                                            <!-- Tip Text -->
                                            <td style="max-width: 380px;">
                                                <div class="font-weight-bold text-dark mb-1" style="line-height: 1.35;">
                                                    {{ $tip->tip }}
                                                </div>
                                                <div class="small text-muted d-flex align-items-center">
                                                    <span class="badge badge-light border text-secondary px-1 mr-2 text-xs">
                                                        {{ strlen($tip->tip) }}/160 chars
                                                    </span>
                                                    <span class="text-truncate" style="max-width: 280px;" title="Formatted SMS: {{ $formattedPreview }}">
                                                        <i class="fas fa-sms text-info mr-1"></i> <span class="font-italic text-dark">&ldquo;{{ Str::limit($formattedPreview, 45) }}&rdquo;</span>
                                                    </span>
                                                </div>
                                            </td>

                                            <!-- Gestation Timing -->
                                            <td>
                                                <div>
                                                    <span class="badge badge-info px-2 py-1 font-weight-bold">
                                                        Week {{ $tip->week?->week ?? $tip->week_id }} &bull; Day {{ $tip->day?->day ?? $tip->day_id }}
                                                    </span>
                                                </div>
                                                @if($tip->week?->trimester)
                                                    <div class="small text-muted mt-1">
                                                        Trimester {{ $tip->week->trimester->trimester }}
                                                    </div>
                                                @endif
                                            </td>

                                            <!-- Time Range -->
                                            <td>
                                                @if($tip->day_range)
                                                    <div class="small font-weight-bold text-dark">
                                                        <i class="far fa-clock text-info mr-1"></i> {{ $tip->day_range->name }}
                                                    </div>
                                                    <div class="text-xs text-muted">
                                                        {{ \Carbon\Carbon::parse($tip->day_range->start_time)->format('h:i A') }} - {{ \Carbon\Carbon::parse($tip->day_range->end_time)->format('h:i A') }}
                                                    </div>
                                                @else
                                                    <span class="badge badge-light border text-muted">Anytime</span>
                                                @endif
                                            </td>

                                            <!-- Organization -->
                                            <td>
                                                @if($tip->organization)
                                                    <div class="font-weight-bold text-dark small">
                                                        {{ $tip->organization->name }}
                                                    </div>
                                                    @if($tip->organization->acronym)
                                                        <span class="badge badge-primary px-2 py-0 font-weight-bold text-monospace" style="font-size: 0.78rem;">
                                                            <i class="fas fa-tag mr-1 text-xs"></i>{{ $tip->organization->acronym }}
                                                        </span>
                                                    @endif
                                                @else
                                                    <span class="badge badge-secondary px-2 py-1 font-weight-bold">
                                                        <i class="fas fa-globe mr-1"></i> Global Tip
                                                    </span>
                                                @endif
                                            </td>

                                            <!-- Status -->
                                            <td>
                                                @if($tip->status === \App\Models\Tip::STATUS_APPROVED)
                                                    <span class="badge badge-success px-2 py-1">
                                                        <i class="fas fa-check-circle mr-1"></i> Approved
                                                    </span>
                                                @elseif($tip->status === \App\Models\Tip::STATUS_PENDING)
                                                    <span class="badge badge-warning px-2 py-1 text-dark">
                                                        <i class="fas fa-clock mr-1"></i> Pending
                                                    </span>
                                                @else
                                                    <span class="badge badge-secondary px-2 py-1">
                                                        {{ ucfirst($tip->status) }}
                                                    </span>
                                                @endif
                                            </td>

                                            <!-- Due Mothers Count on Selected Date -->
                                            <td class="text-center">
                                                <button wire:click="openRecipientsModal({{ $tip->id }})" class="btn btn-xs btn-outline-info shadow-none" title="View mothers currently due for this tip">
                                                    <i class="fas fa-users mr-1"></i> Check Due
                                                </button>
                                            </td>

                                            <!-- Actions -->
                                            <td class="text-right pr-3">
                                                <div class="btn-group btn-group-sm shadow-sm rounded">
                                                    @if($tip->status !== \App\Models\Tip::STATUS_APPROVED && (auth()->user()->isDoctor() || auth()->user()->isSystemAdmin()))
                                                        <button wire:click="approveTip({{ $tip->id }})" class="btn btn-white text-success px-2" title="Approve Tip">
                                                            <i class="fas fa-check"></i>
                                                        </button>
                                                    @endif
                                                    <button wire:click="openEditModal({{ $tip->id }})" class="btn btn-white text-info px-2" title="Edit Tip">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button wire:click="confirmDelete({{ $tip->id }})" class="btn btn-white text-danger px-2" title="Delete Tip">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center py-5 text-muted">
                                                <i class="fas fa-lightbulb fa-3x text-muted mb-3"></i>
                                                <h5>No maternal tips found matching your criteria.</h5>
                                                <button wire:click="openCreateModal" class="btn btn-primary btn-sm mt-2">
                                                    <i class="fas fa-plus mr-1"></i> Create First Tip
                                                </button>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="card-footer bg-white border-0 py-3 d-flex justify-content-between align-items-center flex-wrap">
                            <div class="small text-muted mb-2 mb-md-0">
                                Showing {{ $tips->firstItem() ?? 0 }} to {{ $tips->lastItem() ?? 0 }} of {{ $tips->total() }} tips
                            </div>
                            <div>
                                {{ $tips->links() }}
                            </div>
                        </div>
                    @endif


                    <!-- =================================================== -->
                    <!-- TAB 2: DAILY DELIVERY FORECAST & RECIPIENT MOTHERS -->
                    <!-- =================================================== -->
                    @if($activeTab === 'forecast')
                        <div class="p-4">
                            <!-- Date Selector Bar -->
                            <div class="card bg-light border-0 shadow-sm mb-4 rounded-lg">
                                <div class="card-body p-3">
                                    <div class="row align-items-center">
                                        <div class="col-md-4 mb-2 mb-md-0">
                                            <label class="font-weight-bold text-navy small mb-1">
                                                <i class="fas fa-calendar-day mr-1 text-primary"></i> Target Delivery Date:
                                            </label>
                                            <div class="input-group">
                                                <input type="date" class="form-control" wire:model.live="selectedDate">
                                            </div>
                                        </div>

                                        <div class="col-md-4 mb-2 mb-md-0 d-flex align-items-end">
                                            <button wire:click="setQuickDate('today')" class="btn btn-sm btn-outline-primary mr-2 {{ $selectedDate === now()->toDateString() ? 'active' : '' }}">
                                                <i class="fas fa-calendar-check mr-1"></i> Today
                                            </button>
                                            <button wire:click="setQuickDate('tomorrow')" class="btn btn-sm btn-outline-info mr-2 {{ $selectedDate === now()->addDay()->toDateString() ? 'active' : '' }}">
                                                Tomorrow
                                            </button>
                                        </div>

                                        <div class="col-md-4 text-md-right text-left">
                                            <div class="text-muted small">Forecast for:</div>
                                            <h5 class="mb-0 font-weight-bold text-navy">
                                                {{ \Carbon\Carbon::parse($selectedDate ?: now())->format('l, F j, Y') }}
                                            </h5>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Summary Alert Banner -->
                            <div class="alert alert-info shadow-sm border-0 d-flex align-items-center justify-content-between p-3 mb-4 rounded-lg flex-wrap">
                                <div class="d-flex align-items-center mb-2 mb-md-0">
                                    <i class="fas fa-paper-plane fa-2x mr-3 text-info"></i>
                                    <div>
                                        <h6 class="mb-0 font-weight-bold text-dark">
                                            {{ count($scheduledTipsForDate) }} Tip(s) Scheduled &bull; {{ $totalMothersScheduledForDate }} Unique Mother(s)
                                        </h6>
                                        <p class="mb-0 text-muted small">
                                            Automated SMS delivery forecast based on gestational calculation as of <strong>{{ \Carbon\Carbon::parse($selectedDate ?: now())->format('M d, Y') }}</strong>.
                                        </p>
                                    </div>
                                </div>
                                <span class="badge badge-navy px-3 py-2 font-weight-bold" style="font-size: 0.95rem;">
                                    <i class="fas fa-users mr-1"></i> {{ $totalMothersScheduledForDate }} Recipient Mothers
                                </span>
                            </div>

                            <!-- List of Scheduled Tips on that Date -->
                            @forelse($scheduledTipsForDate as $index => $item)
                                @php
                                    $tipModel = $item['tip'];
                                    $recipientsList = $item['mothers'];
                                    $orgAcronym = $tipModel->organization?->acronym;
                                    $formattedSms = \App\Services\SmsService::formatWithAcronym($tipModel->tip, $orgAcronym);
                                @endphp
                                <div class="card shadow-sm border rounded-lg mb-4" wire:key="scheduled-tip-{{ $tipModel->id }}-{{ $index }}">
                                    <!-- Tip Header -->
                                    <div class="card-header bg-white py-3 px-3 d-flex align-items-center justify-content-between flex-wrap border-bottom">
                                        <div class="d-flex align-items-center mb-2 mb-md-0">
                                            <span class="badge badge-info px-2 py-1 font-weight-bold mr-2">
                                                <i class="fas fa-calendar-week mr-1"></i> Week {{ $tipModel->week?->week ?? $tipModel->week_id }} &bull; Day {{ $tipModel->day?->day ?? $tipModel->day_id }}
                                            </span>
                                            @if($tipModel->day_range)
                                                <span class="badge badge-light border text-secondary px-2 py-1 mr-2">
                                                    <i class="far fa-clock mr-1 text-info"></i> {{ $tipModel->day_range->name }} ({{ \Carbon\Carbon::parse($tipModel->day_range->start_time)->format('h:i A') }} - {{ \Carbon\Carbon::parse($tipModel->day_range->end_time)->format('h:i A') }})
                                                </span>
                                            @endif
                                            @if($tipModel->organization)
                                                <span class="badge badge-primary px-2 py-1 font-weight-bold text-monospace mr-2">
                                                    <i class="fas fa-hospital mr-1"></i> {{ $tipModel->organization->name }} {{ $tipModel->organization->acronym ? '[' . $tipModel->organization->acronym . ']' : '' }}
                                                </span>
                                            @else
                                                <span class="badge badge-secondary px-2 py-1 font-weight-bold mr-2">
                                                    <i class="fas fa-globe mr-1"></i> Global System Tip
                                                </span>
                                            @endif
                                        </div>

                                        <div>
                                            <span class="badge badge-success px-3 py-1 font-weight-bold" style="font-size: 0.9rem;">
                                                <i class="fas fa-user-check mr-1"></i> {{ count($recipientsList) }} Mother(s)
                                            </span>
                                        </div>
                                    </div>

                                    <!-- Tip Message Box -->
                                    <div class="card-body p-3">
                                        <div class="p-3 rounded bg-light border-left border-primary mb-3" style="border-left-width: 4px !important;">
                                            <div class="small font-weight-bold text-muted text-uppercase mb-1">
                                                <i class="fas fa-sms mr-1 text-primary"></i> SMS Preview with Organization Acronym:
                                            </div>
                                            <h6 class="mb-0 text-dark font-weight-bold font-italic">
                                                &ldquo;{{ $formattedSms }}&rdquo;
                                            </h6>
                                        </div>

                                        <!-- Recipient Mothers Sub-Table -->
                                        <div class="table-responsive">
                                            <table class="table table-sm table-hover align-middle mb-0">
                                                <thead class="thead-light">
                                                    <tr>
                                                        <th>Recipient Mother</th>
                                                        <th>Phone Number</th>
                                                        <th>Gestation on {{ \Carbon\Carbon::parse($selectedDate)->format('M d') }}</th>
                                                        <th>Organization & Acronym</th>
                                                        <th class="text-right">Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($recipientsList as $m)
                                                        <tr>
                                                            <td>
                                                                <div class="font-weight-bold text-navy">
                                                                    {{ $m['name'] }}
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <a href="tel:{{ $m['phone'] }}" class="text-info font-weight-bold small">
                                                                    <i class="fas fa-phone-alt mr-1"></i> {{ $m['phone'] }}
                                                                </a>
                                                            </td>
                                                            <td>
                                                                <span class="badge badge-info px-2 py-0">
                                                                    Week {{ $m['gestation_week'] }}, Day {{ $m['gestation_day'] }}
                                                                </span>
                                                            </td>
                                                            <td>
                                                                <span class="small font-weight-bold text-dark">
                                                                    {{ $m['org_name'] }}
                                                                </span>
                                                                @if(!empty($m['org_acronym']))
                                                                    <span class="badge badge-primary px-1 text-xs font-weight-bold text-monospace ml-1">
                                                                        {{ $m['org_acronym'] }}
                                                                    </span>
                                                                @endif
                                                            </td>
                                                            <td class="text-right">
                                                                <a href="{{ route('mothers.show', \App\Helper\StandardData::encrypt($m['mother_id'])) }}" class="btn btn-xs btn-outline-primary" target="_blank" title="View Mother Profile">
                                                                    <i class="fas fa-user mr-1"></i> Profile
                                                                </a>
                                                                @if(!empty($m['session_id']))
                                                                    <a href="{{ route('mothers.history.show', ['mother_id' => $m['mother_id'], 'session_id' => $m['session_id']]) }}" class="btn btn-xs btn-outline-success" target="_blank" title="View SMS History">
                                                                        <i class="fas fa-history mr-1"></i> SMS
                                                                    </a>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-5 text-muted">
                                    <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                                    <h5>No tips are scheduled to be sent on {{ \Carbon\Carbon::parse($selectedDate ?: now())->format('F j, Y') }}.</h5>
                                    <p class="small text-muted">Mothers may have completed their sessions or are on gestational rest days.</p>
                                </div>
                            @endforelse
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </section>

    <!-- =================================================== -->
    <!-- CREATE / EDIT TIP MODAL -->
    <!-- =================================================== -->
    <x-modal status="{{ $modal }}" title="{{ $editingTipId ? 'Edit Maternal Health Tip' : 'Create New Maternal Health Tip' }}">
        <form wire:submit.prevent="store">
            <div class="row">
                <!-- Tip Content -->
                <div class="col-12 form-group">
                    <label class="font-weight-bold small text-navy">
                        Tip Message Content <span class="text-danger">*</span>
                    </label>
                    <textarea wire:model.live="tip_content" rows="3" class="form-control @error('tip_content') is-invalid @enderror" placeholder="Enter health tip content for the mother..."></textarea>
                    <div class="d-flex justify-content-between small text-muted mt-1">
                        <span>Max 160 characters per SMS.</span>
                        <span class="{{ strlen($tip_content) > 160 ? 'text-danger font-weight-bold' : '' }}">
                            {{ strlen($tip_content) }}/160
                        </span>
                    </div>
                    @error('tip_content') <span class="text-danger small">{{ $message }}</span> @enderror
                </div>

                <!-- Week Selector -->
                <div class="col-md-6 form-group">
                    <label class="font-weight-bold small text-navy">
                        Gestational Week (1 - 42) <span class="text-danger">*</span>
                    </label>
                    <select wire:model="week_id" class="form-control custom-select @error('week_id') is-invalid @enderror">
                        @for($w = 1; $w <= 42; $w++)
                            <option value="{{ $w }}">Week {{ $w }}</option>
                        @endfor
                    </select>
                    @error('week_id') <span class="text-danger small">{{ $message }}</span> @enderror
                </div>

                <!-- Day Selector -->
                <div class="col-md-6 form-group">
                    <label class="font-weight-bold small text-navy">
                        Day in Week (1 - 7) <span class="text-danger">*</span>
                    </label>
                    <select wire:model="day_id" class="form-control custom-select @error('day_id') is-invalid @enderror">
                        @for($d = 1; $d <= 7; $d++)
                            <option value="{{ $d }}">Day {{ $d }}</option>
                        @endfor
                    </select>
                    @error('day_id') <span class="text-danger small">{{ $message }}</span> @enderror
                </div>

                <!-- Day Range / Sending Time Window -->
                <div class="col-md-6 form-group">
                    <label class="font-weight-bold small text-navy">
                        Sending Time Window <span class="text-danger">*</span>
                    </label>
                    <select wire:model="day_range_id" class="form-control custom-select @error('day_range_id') is-invalid @enderror">
                        <option value="">-- Select Time Window --</option>
                        @foreach($dayRanges as $dr)
                            <option value="{{ $dr->id }}">{{ $dr->name }} ({{ $dr->start_time }} - {{ $dr->end_time }})</option>
                        @endforeach
                    </select>
                    @error('day_range_id') <span class="text-danger small">{{ $message }}</span> @enderror
                </div>

                <!-- Organization Selector (System Admin only) -->
                @if($isSystemAdmin)
                    <div class="col-md-6 form-group">
                        <label class="font-weight-bold small text-navy">
                            Target Organization
                        </label>
                        <select wire:model="organization_id" class="form-control custom-select @error('organization_id') is-invalid @enderror">
                            <option value="">-- Global / System Tip (All Orgs Fallback) --</option>
                            @foreach($organizations as $org)
                                <option value="{{ $org->id }}">
                                    {{ $org->name }} {{ $org->acronym ? '[' . $org->acronym . ']' : '' }}
                                </option>
                            @endforeach
                        </select>
                        <small class="text-muted">If selected, this tip is delivered ONLY to mothers of this organization.</small>
                        @error('organization_id') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>
                @endif

                <!-- Status Selector -->
                <div class="col-md-6 form-group">
                    <label class="font-weight-bold small text-navy">
                        Approval Status <span class="text-danger">*</span>
                    </label>
                    <select wire:model="status" class="form-control custom-select @error('status') is-invalid @enderror">
                        <option value="{{ \App\Models\Tip::STATUS_APPROVED }}">Approved (Will be sent)</option>
                        <option value="{{ \App\Models\Tip::STATUS_PENDING }}">Pending Approval</option>
                        <option value="{{ \App\Models\Tip::STATUS_DRAFT }}">Draft</option>
                    </select>
                    @error('status') <span class="text-danger small">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="modal-footer px-0 pb-0 mt-3 d-flex justify-content-between">
                <button type="button" wire:click="cancel" class="btn btn-secondary">
                    Cancel
                </button>
                <button type="submit" class="btn btn-navy font-weight-bold">
                    <i class="fas fa-save mr-1"></i> {{ $editingTipId ? 'Update Tip' : 'Save Tip' }}
                </button>
            </div>
        </form>
    </x-modal>

    <!-- =================================================== -->
    <!-- VIEW RECIPIENTS MODAL -->
    <!-- =================================================== -->
    <x-modal status="{{ $recipientsModal }}" title="Scheduled Recipient Mothers">
        @if($viewingTip)
            <div class="p-3 bg-light rounded mb-3 border">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <span class="badge badge-info font-weight-bold">
                        Week {{ $viewingTip->week?->week ?? $viewingTip->week_id }} &bull; Day {{ $viewingTip->day?->day ?? $viewingTip->day_id }}
                    </span>
                    <span class="badge badge-success font-weight-bold">
                        {{ count($viewingRecipients) }} Mother(s) Due
                    </span>
                </div>
                <div class="font-weight-bold text-dark font-italic">
                    &ldquo;{{ $viewingTip->tip }}&rdquo;
                </div>
            </div>

            <h6 class="font-weight-bold text-navy mb-2">
                Recipient Mothers on {{ \Carbon\Carbon::parse($selectedDate ?: now())->format('M d, Y') }}:
            </h6>

            @if(count($viewingRecipients) > 0)
                <div class="table-responsive" style="max-height: 350px; overflow-y: auto;">
                    <table class="table table-sm table-hover align-middle mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th>Mother</th>
                                <th>Phone</th>
                                <th>Gestation</th>
                                <th>Organization</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($viewingRecipients as $r)
                                <tr>
                                    <td><strong>{{ $r['name'] }}</strong></td>
                                    <td><a href="tel:{{ $r['phone'] }}" class="text-info">{{ $r['phone'] }}</a></td>
                                    <td><span class="badge badge-info">Wk {{ $r['gestation_weeks'] }}, Day {{ $r['gestation_day'] }}</span></td>
                                    <td>
                                        {{ $r['organization_name'] }}
                                        @if(!empty($r['organization_acronym']))
                                            <span class="badge badge-primary text-monospace">{{ $r['organization_acronym'] }}</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-4 text-muted">
                    <i class="fas fa-users-slash fa-2x mb-2 text-muted"></i>
                    <p class="mb-0">No mothers currently match this gestational week and day on {{ \Carbon\Carbon::parse($selectedDate ?: now())->format('M d, Y') }}.</p>
                </div>
            @endif

            <div class="modal-footer px-0 pb-0 mt-3 text-right">
                <button type="button" wire:click="cancel" class="btn btn-secondary btn-sm">Close</button>
            </div>
        @endif
    </x-modal>

    <!-- =================================================== -->
    <!-- DELETE CONFIRMATION MODAL -->
    <!-- =================================================== -->
    <x-modal status="{{ $deleteModal }}" title="Confirm Tip Removal">
        <div class="py-2">
            <p class="text-dark">Are you sure you want to delete this maternal health tip?</p>
            <div class="p-3 bg-light rounded border text-muted font-italic mb-3">
                &ldquo;{{ $deletingTipPreview }}&rdquo;
            </div>
            <p class="small text-danger mb-0">
                <i class="fas fa-exclamation-triangle mr-1"></i> Note: Deleting this tip will also remove any unperformed scheduled message history linked to it.
            </p>
        </div>
        <div class="modal-footer px-0 pb-0 mt-3 d-flex justify-content-between">
            <button type="button" wire:click="cancel" class="btn btn-secondary">Cancel</button>
            <button type="button" wire:click="deleteTip" class="btn btn-danger font-weight-bold">
                <i class="fas fa-trash mr-1"></i> Yes, Delete Tip
            </button>
        </div>
    </x-modal>
</div>
