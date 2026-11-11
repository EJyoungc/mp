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
                        <li class="nav-item">
                            <a class="nav-link font-weight-bold {{ $activeTab === 'templates' ? 'active bg-navy text-white' : 'text-secondary' }} px-3"
                               href="javascript:void(0)" wire:click="setTab('templates')">
                                <i class="fas fa-bookmark mr-1"></i> Template Library
                                @if($totalTemplatesCount > 0)
                                    <span class="badge ml-1 text-white" style="background-color: #6f42c1;">{{ $totalTemplatesCount }}</span>
                                @endif
                            </a>
                        </li>
                    </ul>

                    <!-- Action Button -->
                    <div class="my-1 mr-2">
                        @if($activeTab === 'templates' && $isSystemAdmin)
                            <button wire:click="openCreateModal(true)" class="btn btn-sm shadow-sm font-weight-bold text-white" style="background-color: #6f42c1;">
                                <i class="fas fa-plus-circle mr-1"></i> Create Template Tip
                            </button>
                        @else
                            <button wire:click="openCreateModal(false)" class="btn btn-navy btn-sm shadow-sm font-weight-bold">
                                <i class="fas fa-plus-circle mr-1"></i> Create New Tip
                            </button>
                        @endif
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
                                            <td>
                                                <strong>{{ $tip->id }}</strong>
                                                @if($tip->is_template)
                                                    <div>
                                                        <span class="badge text-white px-1 text-xs" style="background-color: #6f42c1;" title="System Template Tip">
                                                            <i class="fas fa-bookmark"></i> Template
                                                        </span>
                                                    </div>
                                                @endif
                                            </td>

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
                                                    @if($isSystemAdmin)
                                                        <button wire:click="toggleTemplate({{ $tip->id }})" class="btn btn-white px-2" style="{{ $tip->is_template ? 'color: #6f42c1 !important;' : 'color: #6c757d !important;' }}" title="{{ $tip->is_template ? 'Remove from Template Library' : 'Mark as Template' }}">
                                                            <i class="fas fa-bookmark"></i>
                                                        </button>
                                                    @endif
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

                    <!-- =================================================== -->
                    <!-- TAB 3: TEMPLATE TIPS LIBRARY -->
                    <!-- =================================================== -->
                    @if($activeTab === 'templates')
                        <div class="p-3 bg-light border-bottom">
                            <!-- Banner info & View Switcher -->
                            <div class="alert alert-light border shadow-sm mb-3 d-flex align-items-center justify-content-between flex-wrap p-3" style="border-left: 4px solid #6f42c1 !important;">
                                <div class="d-flex align-items-center">
                                    <div class="mr-3" style="font-size: 2.2rem; color: #6f42c1;">
                                        <i class="fas fa-calendar-alt"></i>
                                    </div>
                                    <div>
                                        <h5 class="mb-1 font-weight-bold text-navy">Maternal Health Template Tips Schedule</h5>
                                        <p class="mb-0 text-muted small">
                                            Clinically standardized maternal tips arranged by trimester, gestational week, day, and delivery time window.
                                            @if(!auth()->user()->isSystemAdmin() && auth()->user()->organization)
                                                Click <strong>"Adopt"</strong> on any slot to copy it directly into <strong>{{ auth()->user()->organization->name }}</strong>'s scheduled messaging.
                                            @else
                                                System Administrators can configure, slot, and manage global templates accessible to all health organizations.
                                            @endif
                                        </p>
                                    </div>
                                </div>
                                <div class="mt-2 mt-md-0 d-flex align-items-center flex-wrap">
                                    <!-- View Mode Switcher -->
                                    <div class="btn-group btn-group-sm shadow-sm mr-2 mb-1">
                                        <button type="button" wire:click="setTemplateViewMode('calendar')"
                                                class="btn font-weight-bold {{ $templateViewMode === 'calendar' ? 'text-white' : 'btn-white text-dark' }}"
                                                style="{{ $templateViewMode === 'calendar' ? 'background-color: #6f42c1 !important; border-color: #6f42c1 !important;' : '' }}"
                                                title="Weekly Gestational Calendar Matrix">
                                            <i class="fas fa-calendar-week mr-1"></i> Calendar Schedule
                                        </button>
                                        <button type="button" wire:click="setTemplateViewMode('grid')"
                                                class="btn font-weight-bold {{ $templateViewMode === 'grid' ? 'text-white' : 'btn-white text-dark' }}"
                                                style="{{ $templateViewMode === 'grid' ? 'background-color: #6f42c1 !important; border-color: #6f42c1 !important;' : '' }}"
                                                title="Browse all templates as cards">
                                            <i class="fas fa-th-large mr-1"></i> Cards Directory
                                        </button>
                                    </div>
                                    <span class="badge badge-pill text-white px-3 py-2 mb-1" style="background-color: #6f42c1; font-size: 0.9rem;">
                                        <i class="fas fa-layer-group mr-1"></i> {{ $totalTemplatesCount }} Total Templates
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- =============================================== -->
                        <!-- VIEW MODE 1: CUSTOM WEEKLY CALENDAR MATRIX     -->
                        <!-- =============================================== -->
                        @if($templateViewMode === 'calendar')
                            <div class="p-3">
                                <!-- Trimester & Week Selector Bar -->
                                <div class="card bg-white border shadow-sm mb-3 rounded-lg overflow-hidden">
                                    <div class="card-body p-3">
                                        <div class="d-flex align-items-center justify-content-between flex-wrap">
                                            <!-- Trimester Tabs -->
                                            <div class="d-flex align-items-center flex-wrap mb-2 mb-lg-0">
                                                <span class="text-uppercase text-xs font-weight-bold text-muted mr-3">
                                                    <i class="fas fa-layer-group mr-1 text-primary"></i> Trimester:
                                                </span>
                                                <div class="btn-group shadow-sm">
                                                    @foreach($trimesters as $t)
                                                        @php
                                                            $isActiveTrimester = (int)$calendarTrimesterId === (int)$t->id;
                                                            $tCount = $trimesterCounts[$t->id] ?? 0;
                                                        @endphp
                                                        <button type="button" wire:click="selectCalendarTrimester({{ $t->id }})"
                                                                class="btn btn-sm font-weight-bold px-3 {{ $isActiveTrimester ? 'text-white' : 'btn-light text-dark' }}"
                                                                style="{{ $isActiveTrimester ? 'background-color: #6f42c1; border-color: #6f42c1;' : '' }}">
                                                            <i class="fas fa-baby mr-1"></i> Trimester {{ $t->trimester }}
                                                            <span class="badge badge-pill {{ $isActiveTrimester ? 'badge-light' : 'badge-secondary' }} ml-1" style="{{ $isActiveTrimester ? 'color: #6f42c1 !important;' : '' }}">
                                                                {{ $tCount }}
                                                            </span>
                                                        </button>
                                                    @endforeach
                                                </div>
                                            </div>

                                            <!-- Week Navigation Controls -->
                                            <div class="d-flex align-items-center">
                                                <button type="button" wire:click="prevCalendarWeek" class="btn btn-sm btn-outline-secondary font-weight-bold mr-1" {{ !$prevCalendarWeek ? 'disabled' : '' }}>
                                                    <i class="fas fa-chevron-left mr-1"></i> Prev Week
                                                </button>
                                                <span class="badge px-3 py-2 text-white font-weight-bold shadow-sm mx-1" style="background-color: #17a2b8; font-size: 0.95rem;">
                                                    Week {{ $calendarWeek?->week ?? 1 }}
                                                </span>
                                                <button type="button" wire:click="nextCalendarWeek" class="btn btn-sm btn-outline-secondary font-weight-bold ml-1" {{ !$nextCalendarWeek ? 'disabled' : '' }}>
                                                    Next Week <i class="fas fa-chevron-right ml-1"></i>
                                                </button>
                                            </div>
                                        </div>

                                        <!-- Horizontal Quick-Jump Week Pills for active trimester -->
                                        @if($activeTrimesterWeeks->count() > 0)
                                            <div class="mt-3 pt-2 border-top d-flex align-items-center flex-wrap">
                                                <span class="text-xs text-muted font-weight-bold mr-2 text-uppercase">Jump to Week:</span>
                                                <div class="d-flex flex-wrap" style="gap: 5px;">
                                                    @foreach($activeTrimesterWeeks as $w)
                                                        @php $isSelected = (int)$calendarWeekId === (int)$w->id; @endphp
                                                        <button type="button" wire:click="selectCalendarWeek({{ $w->id }})"
                                                                class="btn btn-xs px-2 py-1 font-weight-bold rounded {{ $isSelected ? 'text-white shadow-sm' : 'btn-outline-secondary' }}"
                                                                style="{{ $isSelected ? 'background-color: #6f42c1; border-color: #6f42c1;' : '' }}">
                                                            W{{ $w->week }}
                                                        </button>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <!-- Week Title Bar & Stats -->
                                <div class="d-flex justify-content-between align-items-center mb-3 px-1 flex-wrap">
                                    <div>
                                        <h5 class="mb-0 font-weight-bold text-navy d-flex align-items-center">
                                            <i class="fas fa-calendar-week mr-2" style="color: #6f42c1;"></i>
                                            Week {{ $calendarWeek?->week ?? 1 }} Gestational Schedule Matrix
                                            <span class="badge badge-info ml-2 font-weight-bold">
                                                Trimester {{ $calendarWeek?->trimester?->trimester ?? 1 }}
                                            </span>
                                        </h5>
                                        <span class="text-muted small">
                                            Pregnancy Days {{ (($calendarWeek?->week ?? 1) - 1) * 7 + 1 }} &ndash; {{ ($calendarWeek?->week ?? 1) * 7 }} &bull;
                                            <strong>{{ $weekTemplatesCount }}</strong> template tips configured for this week
                                        </span>
                                    </div>

                                    @if($isSystemAdmin)
                                        <div class="mt-2 mt-md-0">
                                            <button type="button" wire:click="openCreateForCalendarSlot({{ $calendarWeek?->id ?? 1 }}, 1, null)" class="btn btn-sm text-white font-weight-bold shadow-sm" style="background-color: #6f42c1;">
                                                <i class="fas fa-plus mr-1"></i> Add Tip for Week {{ $calendarWeek?->week ?? 1 }}
                                            </button>
                                        </div>
                                    @endif
                                </div>

                                <!-- Custom 7-Day & Time-Frame Matrix Table -->
                                <div class="card border shadow-sm rounded-lg overflow-hidden mb-4">
                                    <div class="table-responsive">
                                        <table class="table table-bordered mb-0 calendar-schedule-table" style="min-width: 980px;">
                                            <thead class="text-white text-center" style="background-color: #1a252f;">
                                                <tr>
                                                    <th style="width: 150px; vertical-align: middle; background-color: #151e27;" class="text-uppercase small font-weight-bold py-2">
                                                        <i class="far fa-clock mr-1 text-warning"></i> Time Frame
                                                    </th>
                                                    @for($d = 1; $d <= 7; $d++)
                                                        @php
                                                            $dayNum = (($calendarWeek?->week ?? 1) - 1) * 7 + $d;
                                                            $countForDay = $dayTipsCount[$d] ?? 0;
                                                        @endphp
                                                        <th style="width: 12.1%; vertical-align: middle;" class="px-2 py-2">
                                                            <div class="font-weight-bold" style="font-size: 0.95rem;">Day {{ $d }}</div>
                                                            <div class="text-xs text-white-50">Day {{ $dayNum }}</div>
                                                            <div class="mt-1">
                                                                @if($countForDay > 0)
                                                                    <span class="badge badge-pill badge-warning text-dark font-weight-bold px-2 py-0 text-xs">
                                                                        {{ $countForDay }} {{ Str::plural('Tip', $countForDay) }}
                                                                    </span>
                                                                @else
                                                                    <span class="badge badge-pill badge-secondary text-white-50 px-2 py-0 text-xs">
                                                                        Empty
                                                                    </span>
                                                                @endif
                                                            </div>
                                                        </th>
                                                    @endfor
                                                </tr>
                                            </thead>
                                            <tbody class="bg-white">
                                                @foreach($calendarDayRanges as $range)
                                                    <tr>
                                                        <!-- Time Frame Header Cell -->
                                                        <td class="bg-light align-middle text-center p-2 font-weight-bold text-dark border-right" style="vertical-align: middle;">
                                                            <div class="text-navy font-weight-bold" style="font-size: 0.9rem;">
                                                                @if(str_contains(strtolower($range->name), 'morning'))
                                                                    <i class="fas fa-sun text-warning mr-1"></i>
                                                                @elseif(str_contains(strtolower($range->name), 'afternoon'))
                                                                    <i class="fas fa-cloud-sun text-warning mr-1"></i>
                                                                @elseif(str_contains(strtolower($range->name), 'evening'))
                                                                    <i class="fas fa-moon text-indigo mr-1"></i>
                                                                @else
                                                                    <i class="far fa-clock text-info mr-1"></i>
                                                                @endif
                                                                {{ $range->name }}
                                                            </div>
                                                            @if(!empty($range->start_time) && !empty($range->end_time))
                                                                <div class="text-muted text-xs font-weight-normal mt-1">
                                                                    {{ \Carbon\Carbon::parse($range->start_time)->format('h:i A') }} - {{ \Carbon\Carbon::parse($range->end_time)->format('h:i A') }}
                                                                </div>
                                                            @endif
                                                        </td>

                                                        <!-- 7 Days Slots -->
                                                        @for($d = 1; $d <= 7; $d++)
                                                            @php
                                                                $slotTips = $calendarMatrix[$d][$range->id] ?? [];
                                                            @endphp
                                                            <td class="p-2 align-top text-left" style="vertical-align: top; {{ count($slotTips) > 0 ? 'background-color: #faf8ff;' : '' }}">
                                                                @if(count($slotTips) > 0)
                                                                    @foreach($slotTips as $st)
                                                                        @php
                                                                            $userOrgAcronym = auth()->user()->organization?->acronym;
                                                                            $slotPreview = $userOrgAcronym ? \App\Services\SmsService::formatWithAcronym($st->tip, $userOrgAcronym) : null;
                                                                        @endphp
                                                                        <div class="card shadow-xs mb-2 border rounded p-2" style="border-left: 3px solid #6f42c1 !important; background: #ffffff;">
                                                                            <div class="d-flex justify-content-between align-items-center mb-1">
                                                                                <span class="badge text-white px-1 text-xs" style="background-color: #6f42c1;">
                                                                                    #{{ $st->id }}
                                                                                </span>
                                                                                <span class="text-muted text-xs">
                                                                                    {{ strlen($st->tip) }}/160
                                                                                </span>
                                                                            </div>

                                                                            <div class="small font-weight-bold text-dark mb-1" style="line-height: 1.35; font-size: 0.82rem;" title="{{ $st->tip }}">
                                                                                &ldquo;{{ Str::limit($st->tip, 80) }}&rdquo;
                                                                            </div>

                                                                            @if($slotPreview)
                                                                                <div class="text-xs text-muted font-italic mb-1 text-truncate" title="SMS: {{ $slotPreview }}">
                                                                                    <i class="fas fa-sms text-info mr-1"></i>{{ Str::limit($slotPreview, 35) }}
                                                                                </div>
                                                                            @endif

                                                                            <!-- Slot Action Buttons -->
                                                                            <div class="d-flex justify-content-between align-items-center pt-1 border-top mt-1">
                                                                                @if(auth()->user()->organization_id)
                                                                                    <button type="button" wire:click="useTemplate({{ $st->id }})" class="btn btn-xs text-white font-weight-bold px-2 shadow-none" style="background-color: #6f42c1;" title="Adopt this template for my organization">
                                                                                        <i class="fas fa-copy mr-1"></i> Adopt
                                                                                    </button>
                                                                                @endif

                                                                                <div class="btn-group btn-group-xs ml-auto">
                                                                                    <button type="button" wire:click="openRecipientsModal({{ $st->id }})" class="btn btn-xs btn-outline-info p-1" title="Check Due Mothers">
                                                                                        <i class="fas fa-users"></i>
                                                                                    </button>
                                                                                    @if($isSystemAdmin)
                                                                                        <button type="button" wire:click="openEditModal({{ $st->id }})" class="btn btn-xs btn-outline-secondary p-1" title="Edit Template">
                                                                                            <i class="fas fa-edit"></i>
                                                                                        </button>
                                                                                        <button type="button" wire:click="toggleTemplate({{ $st->id }})" class="btn btn-xs btn-outline-warning p-1" title="Remove from Template Library">
                                                                                            <i class="fas fa-bookmark"></i>
                                                                                        </button>
                                                                                        <button type="button" wire:click="confirmDelete({{ $st->id }})" class="btn btn-xs btn-outline-danger p-1" title="Delete Tip">
                                                                                            <i class="fas fa-trash"></i>
                                                                                        </button>
                                                                                    @endif
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    @endforeach

                                                                    <!-- Add another tip to this slot -->
                                                                    @if($isSystemAdmin)
                                                                        <div class="text-center mt-1">
                                                                            <button type="button" wire:click="openCreateForCalendarSlot({{ $calendarWeek?->id ?? 1 }}, {{ $d }}, {{ $range->id }})"
                                                                                    class="btn btn-xs font-weight-bold" style="color: #6f42c1; font-size: 0.75rem;">
                                                                                <i class="fas fa-plus mr-1"></i> Add Another
                                                                            </button>
                                                                        </div>
                                                                    @endif
                                                                @else
                                                                    <!-- Empty Slot -->
                                                                    <div class="py-3 px-1 text-center" style="min-height: 80px; display: flex; flex-direction: column; justify-content: center; align-items: center;">
                                                                        @if($isSystemAdmin)
                                                                            <button type="button" wire:click="openCreateForCalendarSlot({{ $calendarWeek?->id ?? 1 }}, {{ $d }}, {{ $range->id }})"
                                                                                    class="btn btn-xs btn-outline-secondary btn-block text-muted p-2"
                                                                                    style="border-style: dashed; font-size: 0.78rem;"
                                                                                    title="Add template tip for Week {{ $calendarWeek?->week ?? 1 }}, Day {{ $d }} in {{ $range->name }}">
                                                                                <i class="fas fa-plus-circle mr-1"></i> Add Tip
                                                                            </button>
                                                                        @else
                                                                            <span class="text-muted text-xs font-italic" style="opacity: 0.6;">
                                                                                No tip
                                                                            </span>
                                                                        @endif
                                                                    </div>
                                                                @endif
                                                            </td>
                                                        @endfor
                                                    </tr>
                                                @endforeach

                                                <!-- Row for Flexible / Anytime Tips (day_range_id == null or 0) -->
                                                @php
                                                    $hasFlexibleTips = false;
                                                    for($d = 1; $d <= 7; $d++) {
                                                        if(!empty($calendarMatrix[$d][0])) {
                                                            $hasFlexibleTips = true;
                                                            break;
                                                        }
                                                    }
                                                @endphp
                                                @if($hasFlexibleTips || $isSystemAdmin)
                                                    <tr class="border-top">
                                                        <td class="bg-light align-middle text-center p-2 font-weight-bold text-muted border-right" style="vertical-align: middle;">
                                                            <div class="text-secondary font-weight-bold" style="font-size: 0.85rem;">
                                                                <i class="fas fa-arrows-alt mr-1"></i> Flexible / Anytime
                                                            </div>
                                                            <div class="text-muted text-xs font-weight-normal mt-1">
                                                                No time window
                                                            </div>
                                                        </td>
                                                        @for($d = 1; $d <= 7; $d++)
                                                            @php $flexTips = $calendarMatrix[$d][0] ?? []; @endphp
                                                            <td class="p-2 align-top text-left" style="vertical-align: top; {{ count($flexTips) > 0 ? 'background-color: #faf8ff;' : '' }}">
                                                                @if(count($flexTips) > 0)
                                                                    @foreach($flexTips as $st)
                                                                        <div class="card shadow-xs mb-2 border rounded p-2" style="border-left: 3px solid #6c757d !important; background: #ffffff;">
                                                                            <div class="d-flex justify-content-between align-items-center mb-1">
                                                                                <span class="badge badge-secondary px-1 text-xs">#{{ $st->id }}</span>
                                                                                <span class="text-muted text-xs">{{ strlen($st->tip) }}/160</span>
                                                                            </div>
                                                                            <div class="small font-weight-bold text-dark mb-1" style="font-size: 0.82rem;">
                                                                                &ldquo;{{ Str::limit($st->tip, 80) }}&rdquo;
                                                                            </div>
                                                                            <div class="d-flex justify-content-between align-items-center pt-1 border-top mt-1">
                                                                                @if(auth()->user()->organization_id)
                                                                                    <button type="button" wire:click="useTemplate({{ $st->id }})" class="btn btn-xs text-white font-weight-bold px-2 shadow-none" style="background-color: #6f42c1;">
                                                                                        <i class="fas fa-copy mr-1"></i> Adopt
                                                                                    </button>
                                                                                @endif
                                                                                <div class="btn-group btn-group-xs ml-auto">
                                                                                    @if($isSystemAdmin)
                                                                                        <button type="button" wire:click="openEditModal({{ $st->id }})" class="btn btn-xs btn-outline-secondary p-1"><i class="fas fa-edit"></i></button>
                                                                                        <button type="button" wire:click="toggleTemplate({{ $st->id }})" class="btn btn-xs btn-outline-warning p-1"><i class="fas fa-bookmark"></i></button>
                                                                                    @endif
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    @endforeach
                                                                @else
                                                                    <div class="text-center py-2 text-muted text-xs font-italic" style="opacity: 0.5;">-</div>
                                                                @endif
                                                            </td>
                                                        @endfor
                                                    </tr>
                                                @endif
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- =============================================== -->
                        <!-- VIEW MODE 2: CARDS DIRECTORY WITH FILTERS      -->
                        <!-- =============================================== -->
                        @if($templateViewMode === 'grid')
                            <div class="p-3 bg-light border-bottom">
                                <!-- Filters Row -->
                                <div class="row align-items-center">
                                    <!-- Search -->
                                    <div class="col-lg-4 col-md-6 mb-2">
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text bg-white border-right-0"><i class="fas fa-search text-muted"></i></span>
                                            </div>
                                            <input type="text" class="form-control border-left-0" placeholder="Search template content..." wire:model.live.debounce.300ms="templateSearch">
                                            @if(!empty($templateSearch))
                                                <div class="input-group-append">
                                                    <button class="btn btn-outline-secondary" type="button" wire:click="$set('templateSearch', '')"><i class="fas fa-times"></i></button>
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Trimester Filter -->
                                    <div class="col-lg-3 col-md-6 mb-2">
                                        <select class="form-control custom-select" wire:model.live="templateTrimesterFilter">
                                            <option value="">All Trimesters (1 - 3)</option>
                                            @foreach($trimesters as $tri)
                                                <option value="{{ $tri->id }}">Trimester {{ $tri->trimester }} ({{ $tri->name }})</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <!-- Week Filter -->
                                    <div class="col-lg-3 col-md-6 mb-2">
                                        <select class="form-control custom-select" wire:model.live="templateWeekFilter">
                                            <option value="">All Gestational Weeks (1 - 42)</option>
                                            @foreach($weeks as $wk)
                                                @if(empty($templateTrimesterFilter) || (int)$wk->trimester_id === (int)$templateTrimesterFilter)
                                                    <option value="{{ $wk->id }}">Week {{ $wk->week }}</option>
                                                @endif
                                            @endforeach
                                        </select>
                                    </div>

                                    <!-- Reset Filters -->
                                    <div class="col-lg-2 col-md-12 mb-2 text-right">
                                        @if(!empty($templateSearch) || !empty($templateTrimesterFilter) || !empty($templateWeekFilter))
                                            <button type="button" wire:click="resetTemplateFilters" class="btn btn-outline-secondary btn-block">
                                                <i class="fas fa-undo mr-1"></i> Reset
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Template Cards Grid -->
                            <div class="p-3">
                                <div class="row">
                                    @forelse($templates as $tpl)
                                        @php
                                            $userOrgAcronym = auth()->user()->organization?->acronym;
                                            $previewWithAcronym = $userOrgAcronym ? \App\Services\SmsService::formatWithAcronym($tpl->tip, $userOrgAcronym) : null;
                                        @endphp
                                        <div class="col-lg-4 col-md-6 mb-3" wire:key="template-card-{{ $tpl->id }}">
                                            <div class="card h-100 shadow-sm border rounded-lg" style="border-top: 3px solid #6f42c1 !important;">
                                                <!-- Card Header -->
                                                <div class="card-header bg-white py-2 px-3 border-bottom d-flex justify-content-between align-items-center">
                                                    <div>
                                                        <span class="badge badge-info px-2 py-1 font-weight-bold">
                                                            Week {{ $tpl->week?->week ?? $tpl->week_id }} &bull; Day {{ $tpl->day?->day ?? $tpl->day_id }}
                                                        </span>
                                                        @if($tpl->week?->trimester)
                                                            <span class="badge badge-light border text-muted ml-1">
                                                                T{{ $tpl->week->trimester->trimester }}
                                                            </span>
                                                        @endif
                                                    </div>
                                                    <div>
                                                        <span class="badge text-white px-2 py-1" style="background-color: #6f42c1;">
                                                            <i class="fas fa-bookmark mr-1"></i> Template #{{ $tpl->id }}
                                                        </span>
                                                    </div>
                                                </div>

                                                <!-- Card Body -->
                                                <div class="card-body p-3 d-flex flex-column justify-content-between">
                                                    <div>
                                                        <!-- Tip Text -->
                                                        <div class="text-dark font-weight-bold mb-2" style="font-size: 0.95rem; line-height: 1.45;">
                                                            &ldquo;{{ $tpl->tip }}&rdquo;
                                                        </div>

                                                        <!-- Character count & timing info -->
                                                        <div class="small text-muted mb-2 d-flex align-items-center flex-wrap">
                                                            <span class="badge badge-light border text-secondary mr-2">
                                                                {{ strlen($tpl->tip) }}/160 chars
                                                            </span>
                                                            @if($tpl->day_range)
                                                                <span class="text-muted">
                                                                    <i class="far fa-clock text-info mr-1"></i> {{ $tpl->day_range->name }}
                                                                </span>
                                                            @endif
                                                        </div>

                                                        <!-- Acronym Preview if applicable -->
                                                        @if($previewWithAcronym)
                                                            <div class="p-2 bg-light rounded border text-xs text-muted mb-2">
                                                                <div class="font-weight-bold text-navy mb-1">
                                                                    <i class="fas fa-mobile-alt mr-1"></i> Preview with [{{ $userOrgAcronym }}]:
                                                                </div>
                                                                <div class="font-italic text-dark">&ldquo;{{ $previewWithAcronym }}&rdquo;</div>
                                                            </div>
                                                        @endif
                                                    </div>

                                                    <!-- Actions Bar -->
                                                    <div class="pt-3 border-top mt-2 d-flex justify-content-between align-items-center flex-wrap">
                                                        @if(auth()->user()->organization_id)
                                                            <button type="button" wire:click="useTemplate({{ $tpl->id }})" class="btn btn-sm text-white font-weight-bold px-3 shadow-sm mr-2 mb-1" style="background-color: #6f42c1;" title="Copy this template to your organization">
                                                                <i class="fas fa-copy mr-1"></i> Adopt Template
                                                            </button>
                                                        @endif

                                                        <div class="btn-group btn-group-sm ml-auto mb-1">
                                                            <button type="button" wire:click="openRecipientsModal({{ $tpl->id }})" class="btn btn-outline-info" title="Check Due Mothers">
                                                                <i class="fas fa-users"></i>
                                                            </button>
                                                            @if($isSystemAdmin)
                                                                <button type="button" wire:click="openEditModal({{ $tpl->id }})" class="btn btn-outline-secondary" title="Edit Template">
                                                                    <i class="fas fa-edit"></i>
                                                                </button>
                                                                <button type="button" wire:click="toggleTemplate({{ $tpl->id }})" class="btn btn-outline-warning" title="Remove from Template Library">
                                                                    <i class="fas fa-bookmark"></i>
                                                                </button>
                                                                <button type="button" wire:click="confirmDelete({{ $tpl->id }})" class="btn btn-outline-danger" title="Delete Tip">
                                                                    <i class="fas fa-trash"></i>
                                                                </button>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="col-12 py-5 text-center text-muted">
                                            <i class="fas fa-bookmark fa-3x text-muted mb-3" style="opacity: 0.4;"></i>
                                            <h5>No template tips match your filter criteria.</h5>
                                            <p class="small text-muted">Try clearing your filters or check back later.</p>
                                            @if($isSystemAdmin)
                                                <button type="button" wire:click="openCreateModal(true)" class="btn btn-sm text-white mt-2 font-weight-bold" style="background-color: #6f42c1;">
                                                    <i class="fas fa-plus mr-1"></i> Create First Template Tip
                                                </button>
                                            @endif
                                        </div>
                                    @endforelse
                                </div>
                            </div>

                            <!-- Pagination -->
                            <div class="card-footer bg-white border-0 py-3 d-flex justify-content-between align-items-center flex-wrap">
                                <div class="small text-muted mb-2 mb-md-0">
                                    Showing {{ $templates->firstItem() ?? 0 }} to {{ $templates->lastItem() ?? 0 }} of {{ $templates->total() }} templates
                                </div>
                                <div>
                                    {{ $templates->links() }}
                                </div>
                            </div>
                        @endif
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

                <!-- Template Toggle (System Admin only) -->
                @if($isSystemAdmin)
                    <div class="col-12 form-group">
                        <div class="custom-control custom-checkbox p-2 rounded bg-light border">
                            <input type="checkbox" class="custom-control-input" id="is_template" wire:model="is_template">
                            <label class="custom-control-label font-weight-bold text-navy" for="is_template" style="cursor: pointer;">
                                <i class="fas fa-bookmark mr-1" style="color: #6f42c1;"></i> System Template Tip (Available to all organizations in Template Library)
                            </label>
                            <div class="small text-muted pl-4">Organizations can browse and copy this template into their organization repository with one click.</div>
                        </div>
                    </div>
                @endif
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
