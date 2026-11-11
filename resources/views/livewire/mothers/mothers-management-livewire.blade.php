<div>
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2 align-items-center">
                <div class="col-sm-6">
                    <h1 class="m-0 text-bold text-navy"><i class="fas fa-female mr-2 text-pink"></i> Mothers Management</h1>
                    <p class="text-muted mb-0">Clinical pregnancy tracking, organization monitoring, and SMS tip delivery history.</p>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Mothers</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">

            <!-- KPI Dashboard Overview Cards -->
            <div class="row mb-3">
                <div class="col-6 col-md-3 mb-2">
                    <div class="small-box bg-navy shadow-sm mb-0 rounded">
                        <div class="inner p-3">
                            <h3 class="mb-1">{{ number_format($stats['total'] ?? 0) }}</h3>
                            <p class="mb-0 text-white-50 small text-uppercase font-weight-bold">Total Mothers</p>
                        </div>
                        <div class="icon"><i class="fas fa-female"></i></div>
                    </div>
                </div>
                <div class="col-6 col-md-3 mb-2">
                    <div class="small-box bg-info shadow-sm mb-0 rounded">
                        <div class="inner p-3">
                            <h3 class="mb-1">{{ number_format($stats['withSession'] ?? 0) }}</h3>
                            <p class="mb-0 text-white-50 small text-uppercase font-weight-bold">Active Sessions</p>
                        </div>
                        <div class="icon"><i class="fas fa-baby"></i></div>
                    </div>
                </div>
                <div class="col-6 col-md-3 mb-2">
                    <div class="small-box bg-success shadow-sm mb-0 rounded">
                        <div class="inner p-3">
                            <h3 class="mb-1">{{ number_format($stats['messagesSent'] ?? 0) }}</h3>
                            <p class="mb-0 text-white-50 small text-uppercase font-weight-bold">Tips Sent</p>
                        </div>
                        <div class="icon"><i class="fas fa-paper-plane"></i></div>
                    </div>
                </div>
                <div class="col-6 col-md-3 mb-2">
                    <div class="small-box bg-purple shadow-sm mb-0 rounded">
                        <div class="inner p-3">
                            <h3 class="mb-1">{{ number_format($stats['orgsRepresented'] ?? 0) }}</h3>
                            <p class="mb-0 text-white-50 small text-uppercase font-weight-bold">Facilities / Orgs</p>
                        </div>
                        <div class="icon"><i class="fas fa-hospital"></i></div>
                    </div>
                </div>
            </div>

            <!-- Filters & Actions Card -->
            <div class="card shadow-sm border-0 mb-4 rounded-lg">
                <div class="card-body p-3">
                    <div class="row align-items-center">
                        <!-- Search Box -->
                        <div class="col-lg-3 col-md-6 mb-2">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text bg-white border-right-0"><i class="fas fa-search text-muted"></i></span>
                                </div>
                                <input type="text" class="form-control border-left-0" placeholder="Search by name, phone, kin, org..." wire:model.live.debounce.300ms="search">
                                @if(!empty($search))
                                    <div class="input-group-append">
                                        <button class="btn btn-outline-secondary" type="button" wire:click="$set('search', '')"><i class="fas fa-times"></i></button>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Organization Filter (Visible to System Admin) -->
                        @if(auth()->user()->isSystemAdmin())
                            <div class="col-lg-3 col-md-6 mb-2">
                                <select wire:model.live="organizationFilter" class="form-control custom-select">
                                    <option value="">-- All Organizations --</option>
                                    @foreach($organizations as $org)
                                        <option value="{{ $org->id }}">
                                            {{ $org->name }} {{ $org->acronym ? '[' . $org->acronym . ']' : '' }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        @endif

                        <!-- District Filter -->
                        <div class="col-lg-2 col-md-4 mb-2">
                            <select wire:model.live="districtFilter" class="form-control custom-select">
                                <option value="">-- All Districts --</option>
                                @foreach($districts as $d)
                                    <option value="{{ $d->id }}">{{ $d->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Session / Message Status Filter -->
                        <div class="col-lg-2 col-md-4 mb-2">
                            <select wire:model.live="statusFilter" class="form-control custom-select">
                                <option value="">-- All Statuses --</option>
                                <option value="with_session">Has Active Session</option>
                                <option value="no_session">No Active Session</option>
                                <option value="with_messages">Has Sent SMS Tips</option>
                            </select>
                        </div>

                        <!-- Actions & Reset -->
                        <div class="col-lg-2 col-md-4 mb-2 text-md-right text-left d-flex justify-content-md-end justify-content-start align-items-center">
                            @if(!empty($search) || !empty($organizationFilter) || !empty($districtFilter) || !empty($statusFilter))
                                <button wire:click="resetFilters" class="btn btn-outline-secondary btn-sm mr-2 shadow-sm" title="Clear all active filters">
                                    <i class="fas fa-undo mr-1"></i> Reset
                                </button>
                            @endif

                            @if(auth()->user()->isSystemAdmin() && count($selectedMothers) > 0)
                                <button wire:click="reassign" class="btn btn-warning btn-sm mr-2 shadow-sm font-weight-bold" title="Reassign selected mothers to another organization">
                                    <i class="fas fa-exchange-alt mr-1"></i> Reassign ({{ count($selectedMothers) }})
                                </button>
                            @endif

                            <button wire:click="create" class="btn btn-navy btn-sm shadow-sm font-weight-bold text-nowrap">
                                <i class="fas fa-plus-circle mr-1"></i> Register Mother
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Active Filter Badges Indicator -->
            @if(!empty($organizationFilter) || !empty($districtFilter) || !empty($statusFilter) || !empty($search))
                <div class="mb-3 d-flex align-items-center flex-wrap">
                    <span class="small font-weight-bold text-muted mr-2"><i class="fas fa-filter text-primary mr-1"></i> Active Filters:</span>
                    @if(!empty($organizationFilter))
                        @php
                            $filteredOrg = $organizations->firstWhere('id', $organizationFilter);
                        @endphp
                        <span class="badge badge-primary px-2 py-1 mr-2 mb-1">
                            Org: {{ $filteredOrg->name ?? 'ID: ' . $organizationFilter }} {{ $filteredOrg && $filteredOrg->acronym ? '(' . $filteredOrg->acronym . ')' : '' }}
                            <i class="fas fa-times ml-1 cursor-pointer" wire:click="$set('organizationFilter', '')"></i>
                        </span>
                    @endif
                    @if(!empty($districtFilter))
                        @php
                            $filteredDist = $districts->firstWhere('id', $districtFilter);
                        @endphp
                        <span class="badge badge-info px-2 py-1 mr-2 mb-1">
                            District: {{ $filteredDist->name ?? 'Selected' }}
                            <i class="fas fa-times ml-1 cursor-pointer" wire:click="$set('districtFilter', '')"></i>
                        </span>
                    @endif
                    @if(!empty($statusFilter))
                        <span class="badge badge-secondary px-2 py-1 mr-2 mb-1">
                            Status: {{ ucfirst(str_replace('_', ' ', $statusFilter)) }}
                            <i class="fas fa-times ml-1 cursor-pointer" wire:click="$set('statusFilter', '')"></i>
                        </span>
                    @endif
                    @if(!empty($search))
                        <span class="badge badge-light border px-2 py-1 mr-2 mb-1 text-dark">
                            Search: "{{ $search }}"
                            <i class="fas fa-times ml-1 cursor-pointer text-danger" wire:click="$set('search', '')"></i>
                        </span>
                    @endif
                    <a href="javascript:void(0)" wire:click="resetFilters" class="small text-danger font-weight-bold ml-1 mb-1">Clear All</a>
                </div>
            @endif

            <!-- Mothers Directory Card -->
            <div class="card shadow-sm border-0 rounded-lg overflow-hidden">
                <div class="card-body p-0">

                    <!-- DESKTOP / TABLET VIEW (TABLE) -->
                    <div class="table-responsive d-none d-lg-block">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="thead-light">
                                <tr>
                                    @if(auth()->user()->isSystemAdmin())
                                        <th class="px-3 py-3 border-0" style="width: 40px;">
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" class="custom-control-input" id="selectAll" wire:model.live="selectAll">
                                                <label class="custom-control-label" for="selectAll"></label>
                                            </div>
                                        </th>
                                    @endif
                                    <th class="px-3 py-3 border-0">Mother & Demographics</th>
                                    <th class="py-3 border-0">Organization & Acronym</th>
                                    <th class="py-3 border-0">Pregnancy Session</th>
                                    <th class="py-3 border-0" style="min-width: 290px;">Latest Session Tip & SMS Sent</th>
                                    <th class="py-3 border-0 text-right pr-3" style="min-width: 140px;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($mothers as $mother)
                                    <tr wire:key="mother-desktop-{{ $mother->id }}">
                                        @if(auth()->user()->isSystemAdmin())
                                            <td class="px-3 py-3 border-top-0 align-middle">
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" class="custom-control-input" id="mother-{{ $mother->id }}" value="{{ $mother->id }}" wire:model.live="selectedMothers">
                                                    <label class="custom-control-label" for="mother-{{ $mother->id }}"></label>
                                                </div>
                                            </td>
                                        @endif

                                        <!-- Mother & Contact Details -->
                                        <td class="px-3 py-3 border-top-0 align-middle">
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-circle bg-soft-navy text-navy mr-3 d-flex align-items-center justify-content-center shadow-sm" style="width: 44px; height: 44px; border-radius: 50%; background: #eef2f7; font-weight: bold; font-size: 1.1rem; flex-shrink: 0;">
                                                    {{ strtoupper(substr($mother->name, 0, 1)) }}
                                                </div>
                                                <div>
                                                    <a href="{{ route('mothers.show', \App\Helper\StandardData::encrypt($mother->id)) }}" class="font-weight-bold text-navy text-decoration-none">
                                                        {{ $mother->name }}
                                                    </a>
                                                    <div class="small text-muted">
                                                        <i class="fas fa-birthday-cake mr-1 text-secondary"></i> {{ \Carbon\Carbon::parse($mother->date_of_birth)->format('M d, Y') }} ({{ $mother->age }} yrs)
                                                    </div>
                                                    <div class="small mt-1">
                                                        <a href="tel:{{ $mother->phone }}" class="text-info font-weight-bold">
                                                            <i class="fas fa-phone-alt mr-1"></i> {{ $mother->phone }}
                                                        </a>
                                                    </div>
                                                    @if($mother->district || $mother->area)
                                                        <div class="small text-muted mt-1">
                                                            <i class="fas fa-map-marker-alt text-danger mr-1"></i> {{ $mother->district->name ?? '' }}{{ $mother->area ? ', ' . $mother->area->name : '' }}
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>

                                        <!-- Organization & Acronym -->
                                        <td class="py-3 border-top-0 align-middle">
                                            @if($mother->organization)
                                                <div class="font-weight-bold text-dark mb-1">
                                                    {{ $mother->organization->name }}
                                                </div>
                                                @if($mother->organization->acronym)
                                                    <span class="badge badge-primary px-2 py-1 font-weight-bold text-monospace" title="Organization SMS Prefix">
                                                        <i class="fas fa-tag mr-1 text-xs"></i>{{ $mother->organization->acronym }}
                                                    </span>
                                                @else
                                                    <span class="badge badge-light border text-muted px-2 py-1">No Acronym</span>
                                                @endif
                                            @else
                                                <span class="badge badge-secondary px-2 py-1">Unassigned</span>
                                            @endif
                                        </td>

                                        <!-- Pregnancy Session Info -->
                                        <td class="py-3 border-top-0 align-middle">
                                            @if($mother->latestHistory)
                                                @php
                                                    $weekData = $mother->latestHistory->calculate_weekv2();
                                                    $gestWeeks = $weekData['weeks'] ?? 0;
                                                    $dayInWeek = $weekData['day_in_week'] ?? 1;
                                                @endphp
                                                <div>
                                                    <span class="badge badge-info px-2 py-1 font-weight-bold">
                                                        <i class="fas fa-baby mr-1"></i> Week {{ $gestWeeks }}, Day {{ $dayInWeek }}
                                                    </span>
                                                    @if($gestWeeks <= 13)
                                                        <span class="badge badge-light border text-info px-2 py-1 ml-1">1st Tri</span>
                                                    @elseif($gestWeeks <= 27)
                                                        <span class="badge badge-light border text-primary px-2 py-1 ml-1">2nd Tri</span>
                                                    @else
                                                        <span class="badge badge-light border text-purple px-2 py-1 ml-1">3rd Tri</span>
                                                    @endif
                                                </div>
                                                <div class="small text-muted mt-1">
                                                    <i class="far fa-calendar-alt mr-1"></i> LMP: {{ \Carbon\Carbon::parse($mother->latestHistory->last_menstrual_cycle)->format('M d, Y') }}
                                                </div>
                                                <div class="small text-secondary mt-1">
                                                    <i class="fas fa-child mr-1"></i> Infant #{{ $mother->latestHistory->infant_number ?? 1 }}
                                                </div>
                                            @else
                                                <span class="badge badge-warning text-dark px-2 py-1">
                                                    <i class="fas fa-exclamation-triangle mr-1"></i> No Session
                                                </span>
                                                <div class="small text-muted mt-1 font-italic">No pregnancy registered</div>
                                            @endif
                                        </td>

                                        <!-- Latest Message Sent & Tip Content -->
                                        <td class="py-3 border-top-0 align-middle">
                                            @if($mother->latestMessageHistory)
                                                @php
                                                    $latestMsg = $mother->latestMessageHistory;
                                                    $rawTip = $latestMsg->tip?->tip ?? $latestMsg->api_response['message'] ?? 'Pregnancy Health Tip';
                                                    $msgAcronym = $latestMsg->organization?->acronym ?? $mother->organization?->acronym;
                                                    $formattedTip = \App\Services\SmsService::formatWithAcronym($rawTip, $msgAcronym);
                                                @endphp
                                                <div class="p-2 rounded bg-light border-left border-primary shadow-2xs" style="border-left-width: 4px !important; font-size: 0.85rem;">
                                                    <div class="d-flex align-items-center justify-content-between mb-1">
                                                        @if($latestMsg->message_status === 'sent')
                                                            <span class="badge badge-success px-2 py-0"><i class="fas fa-check-circle mr-1"></i> Sent</span>
                                                        @elseif($latestMsg->message_status === 'failed')
                                                            <span class="badge badge-danger px-2 py-0"><i class="fas fa-times-circle mr-1"></i> Failed</span>
                                                        @else
                                                            <span class="badge badge-warning px-2 py-0"><i class="fas fa-clock mr-1"></i> {{ ucfirst($latestMsg->message_status) }}</span>
                                                        @endif
                                                        <small class="text-muted"><i class="far fa-clock mr-1"></i> {{ $latestMsg->created_at->diffForHumans() }}</small>
                                                    </div>

                                                    <div class="text-dark font-italic mb-1" style="max-height: 48px; overflow: hidden; line-height: 1.3;" title="{{ $formattedTip }}">
                                                        &ldquo;{{ Str::limit($formattedTip, 80) }}&rdquo;
                                                    </div>

                                                    <div class="d-flex justify-content-between align-items-center small mt-1">
                                                        <span class="badge badge-secondary px-1 text-xs">
                                                            Wk {{ $latestMsg->week?->week ?? $latestMsg->week_id ?? '-' }} &bull; D {{ $latestMsg->day?->day ?? $latestMsg->day_id ?? '-' }}
                                                        </span>
                                                        <a href="{{ route('mothers.history.show', ['mother_id' => $mother->id, 'session_id' => $latestMsg->history_id ?? $mother->latestHistory?->id]) }}" class="text-primary font-weight-bold" style="font-size: 0.78rem;">
                                                            View Session SMS &rarr;
                                                        </a>
                                                    </div>
                                                </div>
                                            @elseif($mother->latestHistory)
                                                <div class="p-2 rounded bg-light border text-muted small" style="max-width: 280px;">
                                                    <div><i class="fas fa-envelope-open-text text-secondary mr-1"></i> <em>No tips sent yet</em></div>
                                                    <a href="{{ route('mothers.history.show', ['mother_id' => $mother->id, 'session_id' => $mother->latestHistory->id]) }}" class="btn btn-xs btn-outline-primary mt-1">
                                                        <i class="fas fa-paper-plane mr-1"></i> Open Session & Send
                                                    </a>
                                                </div>
                                            @else
                                                <span class="text-muted small font-italic"><i class="fas fa-comment-slash mr-1"></i> No SMS activity</span>
                                            @endif
                                        </td>

                                        <!-- Actions -->
                                        <td class="py-3 border-top-0 align-middle text-right pr-3">
                                            <div class="btn-group shadow-sm rounded">
                                                <a href="{{ route('mothers.show', \App\Helper\StandardData::encrypt($mother->id)) }}" class="btn btn-white btn-sm px-2" title="View Profile">
                                                    <i class="fas fa-eye text-primary"></i>
                                                </a>
                                                @if($mother->latestHistory)
                                                    <a href="{{ route('mothers.history.show', ['mother_id' => $mother->id, 'session_id' => $mother->latestHistory->id]) }}" class="btn btn-white btn-sm px-2" title="Session SMS Log">
                                                        <i class="fas fa-history text-success"></i>
                                                    </a>
                                                @endif
                                                <button wire:click="edit({{ $mother->id }})" class="btn btn-white btn-sm px-2" title="Edit Profile">
                                                    <i class="fas fa-edit text-info"></i>
                                                </button>
                                                <button wire:click="delete({{ $mother->id }})"
                                                        wire:confirm="Are you sure you want to delete this mother record? This will also remove her pregnancy history."
                                                        class="btn btn-white btn-sm px-2" title="Delete Mother">
                                                    <i class="fas fa-trash text-danger"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="{{ auth()->user()->isSystemAdmin() ? 6 : 5 }}" class="text-center py-5">
                                            <div class="py-4">
                                                <i class="fas fa-users-slash fa-3x text-muted mb-3"></i>
                                                <h5 class="text-muted">No mothers found matching your criteria.</h5>
                                                @if(!empty($organizationFilter) || !empty($districtFilter) || !empty($statusFilter) || !empty($search))
                                                    <button wire:click="resetFilters" class="btn btn-outline-primary btn-sm mt-2">
                                                        <i class="fas fa-undo mr-1"></i> Clear Filters
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- MOBILE / TABLET VIEW (RESPONSIVE CARDS) -->
                    <div class="d-block d-lg-none p-3">
                        @forelse($mothers as $mother)
                            <div class="card shadow-sm border rounded-lg mb-3" wire:key="mother-mobile-{{ $mother->id }}">
                                <div class="card-header bg-white py-2 px-3 d-flex align-items-center justify-content-between">
                                    <div class="d-flex align-items-center">
                                        @if(auth()->user()->isSystemAdmin())
                                            <div class="custom-control custom-checkbox mr-2">
                                                <input type="checkbox" class="custom-control-input" id="mother-mobile-check-{{ $mother->id }}" value="{{ $mother->id }}" wire:model.live="selectedMothers">
                                                <label class="custom-control-label" for="mother-mobile-check-{{ $mother->id }}"></label>
                                            </div>
                                        @endif
                                        <div class="avatar-circle bg-soft-navy text-navy mr-2 d-flex align-items-center justify-content-center font-weight-bold" style="width: 36px; height: 36px; border-radius: 50%; background: #eef2f7;">
                                            {{ strtoupper(substr($mother->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <a href="{{ route('mothers.show', \App\Helper\StandardData::encrypt($mother->id)) }}" class="font-weight-bold text-navy text-decoration-none">
                                                {{ $mother->name }}
                                            </a>
                                            <div class="text-muted" style="font-size: 0.75rem;">
                                                {{ $mother->age }} yrs &bull; <i class="fas fa-birthday-cake"></i> {{ \Carbon\Carbon::parse($mother->date_of_birth)->format('M d, Y') }}
                                            </div>
                                        </div>
                                    </div>
                                    <div>
                                        @if($mother->organization && $mother->organization->acronym)
                                            <span class="badge badge-primary px-2 py-1 font-weight-bold text-monospace" style="font-size: 0.8rem;">
                                                <i class="fas fa-tag mr-1 text-xs"></i>{{ $mother->organization->acronym }}
                                            </span>
                                        @elseif($mother->organization)
                                            <span class="badge badge-info px-2 py-1" style="font-size: 0.75rem;">
                                                {{ Str::limit($mother->organization->name, 12) }}
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <div class="card-body p-3">
                                    <!-- Contact & Location Pills -->
                                    <div class="mb-2 d-flex flex-wrap align-items-center">
                                        <a href="tel:{{ $mother->phone }}" class="badge badge-light border text-info px-2 py-1 mr-2 mb-1" style="font-size: 0.82rem;">
                                            <i class="fas fa-phone-alt mr-1"></i> {{ $mother->phone }}
                                        </a>
                                        @if($mother->district || $mother->area)
                                            <span class="badge badge-light border text-secondary px-2 py-1 mr-2 mb-1" style="font-size: 0.82rem;">
                                                <i class="fas fa-map-marker-alt text-danger mr-1"></i> {{ $mother->district->name ?? '' }}{{ $mother->area ? ', ' . $mother->area->name : '' }}
                                            </span>
                                        @endif
                                        @if($mother->next_of_kin)
                                            <span class="badge badge-light border text-muted px-2 py-1 mb-1" style="font-size: 0.82rem;">
                                                <i class="fas fa-user-friends mr-1"></i> Kin: {{ $mother->next_of_kin }} ({{ $mother->next_of_kin_mobile ?? '-' }})
                                            </span>
                                        @endif
                                    </div>

                                    <!-- Pregnancy Session Info -->
                                    <div class="mb-2 p-2 rounded bg-light border">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <span class="small font-weight-bold text-navy"><i class="fas fa-baby mr-1 text-pink"></i> Pregnancy Status:</span>
                                            @if($mother->latestHistory)
                                                @php
                                                    $weekData = $mother->latestHistory->calculate_weekv2();
                                                    $gestWeeks = $weekData['weeks'] ?? 0;
                                                    $dayInWeek = $weekData['day_in_week'] ?? 1;
                                                @endphp
                                                <span class="badge badge-info px-2 py-0">Week {{ $gestWeeks }}, Day {{ $dayInWeek }}</span>
                                            @else
                                                <span class="badge badge-warning text-dark px-2 py-0">No Session</span>
                                            @endif
                                        </div>
                                        @if($mother->latestHistory)
                                            <div class="small text-muted d-flex justify-content-between">
                                                <span>LMP: {{ \Carbon\Carbon::parse($mother->latestHistory->last_menstrual_cycle)->format('M d, Y') }}</span>
                                                <span class="font-weight-bold">Infant #{{ $mother->latestHistory->infant_number ?? 1 }}</span>
                                            </div>
                                        @else
                                            <div class="small text-muted font-italic">No pregnancy session currently registered.</div>
                                        @endif
                                    </div>

                                    <!-- Latest Message Sent -->
                                    <div class="p-2 rounded bg-light border-left border-primary" style="border-left-width: 4px !important;">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <span class="small font-weight-bold text-dark"><i class="fas fa-paper-plane text-primary mr-1"></i> Latest SMS Tip:</span>
                                            @if($mother->latestMessageHistory)
                                                @if($mother->latestMessageHistory->message_status === 'sent')
                                                    <span class="badge badge-success px-2 py-0">Sent</span>
                                                @elseif($mother->latestMessageHistory->message_status === 'failed')
                                                    <span class="badge badge-danger px-2 py-0">Failed</span>
                                                @else
                                                    <span class="badge badge-warning px-2 py-0">{{ ucfirst($mother->latestMessageHistory->message_status) }}</span>
                                                @endif
                                            @endif
                                        </div>

                                        @if($mother->latestMessageHistory)
                                            @php
                                                $latestMsg = $mother->latestMessageHistory;
                                                $rawTip = $latestMsg->tip?->tip ?? $latestMsg->api_response['message'] ?? 'Tip sent';
                                                $msgAcronym = $latestMsg->organization?->acronym ?? $mother->organization?->acronym;
                                                $formattedTip = \App\Services\SmsService::formatWithAcronym($rawTip, $msgAcronym);
                                            @endphp
                                            <div class="small text-dark font-italic mb-1">
                                                &ldquo;{{ $formattedTip }}&rdquo;
                                            </div>
                                            <div class="d-flex justify-content-between align-items-center text-xs text-muted">
                                                <span><i class="far fa-clock mr-1"></i> {{ $latestMsg->created_at->diffForHumans() }} (Wk {{ $latestMsg->week?->week ?? $latestMsg->week_id ?? '-' }} &bull; D {{ $latestMsg->day?->day ?? $latestMsg->day_id ?? '-' }})</span>
                                                <a href="{{ route('mothers.history.show', ['mother_id' => $mother->id, 'session_id' => $latestMsg->history_id ?? $mother->latestHistory?->id]) }}" class="text-primary font-weight-bold">
                                                    Session SMS Log &rarr;
                                                </a>
                                            </div>
                                        @elseif($mother->latestHistory)
                                            <div class="small text-muted font-italic mb-1">No tips sent yet for this session.</div>
                                            <a href="{{ route('mothers.history.show', ['mother_id' => $mother->id, 'session_id' => $mother->latestHistory->id]) }}" class="btn btn-xs btn-outline-primary">
                                                <i class="fas fa-paper-plane mr-1"></i> Open Session & Send
                                            </a>
                                        @else
                                            <div class="small text-muted font-italic">No SMS activity recorded.</div>
                                        @endif
                                    </div>
                                </div>

                                <div class="card-footer bg-white py-2 px-3 d-flex justify-content-between align-items-center">
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('mothers.show', \App\Helper\StandardData::encrypt($mother->id)) }}" class="btn btn-outline-primary btn-sm">
                                            <i class="fas fa-eye mr-1"></i> Profile
                                        </a>
                                        @if($mother->latestHistory)
                                            <a href="{{ route('mothers.history.show', ['mother_id' => $mother->id, 'session_id' => $mother->latestHistory->id]) }}" class="btn btn-outline-success btn-sm">
                                                <i class="fas fa-history mr-1"></i> SMS Log
                                            </a>
                                        @endif
                                    </div>
                                    <div class="btn-group btn-group-sm">
                                        <button wire:click="edit({{ $mother->id }})" class="btn btn-outline-info btn-sm">
                                            <i class="fas fa-edit"></i> Edit
                                        </button>
                                        <button wire:click="delete({{ $mother->id }})"
                                                wire:confirm="Are you sure you want to delete this mother record? This will also remove her pregnancy history."
                                                class="btn btn-outline-danger btn-sm">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-5">
                                <i class="fas fa-users-slash fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">No mothers found matching your criteria.</h5>
                                @if(!empty($organizationFilter) || !empty($districtFilter) || !empty($statusFilter) || !empty($search))
                                    <button wire:click="resetFilters" class="btn btn-outline-primary btn-sm mt-2">
                                        <i class="fas fa-undo mr-1"></i> Clear Filters
                                    </button>
                                @endif
                            </div>
                        @endforelse
                    </div>

                </div>
                <div class="card-footer bg-white border-0 py-3 d-flex justify-content-between align-items-center flex-wrap">
                    <div class="small text-muted mb-2 mb-md-0">
                        Showing {{ $mothers->firstItem() ?? 0 }} to {{ $mothers->lastItem() ?? 0 }} of {{ $mothers->total() }} mothers
                    </div>
                    <div>
                        {{ $mothers->links() }}
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Registration/Edit Modal (Comprehensive) -->
    <x-modal title="{{ $mother_id ? 'Edit Mother Profile' : 'Register New Mother' }}" status="{{ $modal }}">
        <form wire:submit.prevent="store">
            <div class="card card-navy card-outline card-tabs shadow-none border-0">
                <div class="card-header p-0 border-bottom-0">
                    <ul class="nav nav-tabs" id="mother-form-tabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="personal-tab" data-toggle="pill" href="#personal-pane" role="tab"><i class="fas fa-user mr-1"></i> Personal</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="contact-tab" data-toggle="pill" href="#contact-pane" role="tab"><i class="fas fa-address-book mr-1"></i> Contact & Kin</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="clinical-tab" data-toggle="pill" href="#clinical-pane" role="tab"><i class="fas fa-stethoscope mr-1"></i> Clinical</a>
                        </li>
                    </ul>
                </div>
                <div class="card-body px-0">
                    <div class="tab-content">
                        <!-- Personal Info Pane -->
                        <div class="tab-pane fade show active" id="personal-pane" role="tabpanel">
                            <div class="row">
                                <div class="col-md-6 form-group">
                                    <label>Full Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" wire:model="name" placeholder="Full name">
                                    @error('name') <small class="text-danger">{{ $message }}</small> @enderror
                                </div>
                                <div class="col-md-6 form-group">
                                    <label>Phone Number <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" wire:model="phone" placeholder="+265...">
                                    @error('phone') <small class="text-danger">{{ $message }}</small> @enderror
                                </div>
                                <div class="col-md-6 form-group">
                                    <label>Date of Birth <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control" wire:model.live="date_of_birth">
                                    @error('date_of_birth') <small class="text-danger">{{ $message }}</small> @enderror
                                </div>
                                <div class="col-md-6 form-group">
                                    <label>Age</label>
                                    <input type="text" class="form-control" wire:model="age" readonly>
                                </div>
                                <div class="col-md-4 form-group">
                                    <label>Marital Status <span class="text-danger">*</span></label>
                                    <select class="form-control" wire:model="marital_status">
                                        <option value="">Select</option>
                                        @foreach($maritalStatuses as $status)
                                            <option value="{{ $status }}">{{ $status }}</option>
                                        @endforeach
                                    </select>
                                    @error('marital_status') <small class="text-danger">{{ $message }}</small> @enderror
                                </div>
                                <div class="col-md-4 form-group">
                                    <label>Religion <span class="text-danger">*</span></label>
                                    <select class="form-control" wire:model="religion">
                                        <option value="">Select</option>
                                        @foreach($religions as $rel)
                                            <option value="{{ $rel }}">{{ $rel }}</option>
                                        @endforeach
                                    </select>
                                    @error('religion') <small class="text-danger">{{ $message }}</small> @enderror
                                </div>
                                <div class="col-md-4 form-group">
                                    <label>Education <span class="text-danger">*</span></label>
                                    <select class="form-control" wire:model="level_of_education">
                                        <option value="">Select</option>
                                        @foreach($educationLevels as $level)
                                            <option value="{{ $level }}">{{ $level }}</option>
                                        @endforeach
                                    </select>
                                    @error('level_of_education') <small class="text-danger">{{ $message }}</small> @enderror
                                </div>
                                <div class="col-md-12 form-group">
                                    <label>Occupation <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" wire:model="occupation" placeholder="Current occupation">
                                    @error('occupation') <small class="text-danger">{{ $message }}</small> @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Contact & Kin Pane -->
                        <div class="tab-pane fade" id="contact-pane" role="tabpanel">
                            <div class="row">
                                <div class="col-md-12 form-group">
                                    <label>Current Address <span class="text-danger">*</span></label>
                                    <textarea class="form-control" wire:model="address" rows="2"></textarea>
                                    @error('address') <small class="text-danger">{{ $message }}</small> @enderror
                                </div>
                                <div class="col-md-6 form-group">
                                    <label>District <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <select class="form-control" wire:model.live="district_id">
                                            <option value="">Select District</option>
                                            @foreach($districts as $district)
                                                <option value="{{ $district->id }}">{{ $district->name }}</option>
                                            @endforeach
                                        </select>
                                        <div class="input-group-append">
                                            <button class="btn btn-outline-navy" type="button" wire:click="addDistrict" title="Add New District">
                                                <i class="fas fa-plus"></i>
                                            </button>
                                        </div>
                                    </div>
                                    @error('district_id') <small class="text-danger">{{ $message }}</small> @enderror
                                </div>
                                <div class="col-md-6 form-group">
                                    <label>Area <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <select class="form-control" wire:model="area_id" @disabled(!$district_id)>
                                            <option value="">Select Area</option>
                                            @foreach($areas as $area)
                                                <option value="{{ $area->id }}">{{ $area->name }}</option>
                                            @endforeach
                                        </select>
                                        <div class="input-group-append">
                                            <button class="btn btn-outline-navy" type="button" wire:click="addArea" @disabled(!$district_id) title="Add New Area">
                                                <i class="fas fa-plus"></i>
                                            </button>
                                        </div>
                                    </div>
                                    @error('area_id') <small class="text-danger">{{ $message }}</small> @enderror
                                </div>
                                <div class="col-md-6 form-group">
                                    <label>Traditional Authority <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" wire:model="traditional_authority">
                                    @error('traditional_authority') <small class="text-danger">{{ $message }}</small> @enderror
                                </div>
                                <div class="col-md-6 form-group">
                                    <label>Email Address</label>
                                    <input type="email" class="form-control" wire:model="email" placeholder="Optional">
                                    @error('email') <small class="text-danger">{{ $message }}</small> @enderror
                                </div>
                                <div class="col-md-6 form-group">
                                    <label>Next of Kin <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" wire:model="next_of_kin">
                                    @error('next_of_kin') <small class="text-danger">{{ $message }}</small> @enderror
                                </div>
                                <div class="col-md-6 form-group">
                                    <label>Next of Kin Mobile <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" wire:model="next_of_kin_mobile">
                                    @error('next_of_kin_mobile') <small class="text-danger">{{ $message }}</small> @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Clinical Pane -->
                        <div class="tab-pane fade" id="clinical-pane" role="tabpanel">
                            <div class="row">
                                <div class="col-md-4 form-group">
                                    <label>LMP Date <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control" wire:model="last_menstrual_cycle">
                                    @error('last_menstrual_cycle') <small class="text-danger">{{ $message }}</small> @enderror
                                </div>
                                <div class="col-md-4 form-group">
                                    <label>Infant #</label>
                                    <input type="number" class="form-control" wire:model="infant_number">
                                </div>
                                <div class="col-md-4 form-group">
                                    <label>Height (cm) <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" wire:model="height">
                                    @error('height') <small class="text-danger">{{ $message }}</small> @enderror
                                </div>
                            </div>
                            <div class="row bg-light p-2 rounded mb-3">
                                <div class="col-md-3 form-group">
                                    <label>Deliveries</label>
                                    <input type="number" class="form-control" wire:model="deliveries">

                                </div>
                                <div class="col-md-3 form-group">
                                    <label>Abortions</label>
                                    <input type="number" class="form-control" wire:model="abortions">
                                     @error('number') <small class="text-danger">{{ $message }}</small> @enderror
                                </div>
                                <div class="col-md-3 form-group">
                                    <label>Still Births</label>
                                    <select class="form-control" wire:model="stillBirths">
                                        <option value="">Select</option>
                                        <option value="No">No</option>
                                        <option value="Yes">Yes</option>
                                    </select>
                                     @error('stillBirths') <small class="text-danger">{{ $message }}</small> @enderror

                                </div>
                                <div class="col-md-3 form-group">
                                    <label>Multiple</label>
                                    <select class="form-control" wire:model="multiple">
                                        <option value="">Select</option>
                                        <option value="No">No</option>
                                        <option value="Yes">Yes</option>
                                    </select>
                                     @error('multiple') <small class="text-danger">{{ $message }}</small> @enderror
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4 form-group small">
                                    <label>Leg/Spine Deformity</label>
                                    <select class="form-control form-control-sm" wire:model="legOrSpine">
                                        <option value="">Select</option>
                                        <option value="No">No</option>
                                        <option value="Yes">Yes</option>
                                    </select>
                                     @error('legOrSpine') <small class="text-danger">{{ $message }}</small> @enderror
                                </div>
                                <div class="col-md-4 form-group small">
                                    <label>Other Deformity</label>
                                    <select class="form-control form-control-sm" wire:model="deformity">
                                        <option value="">Select</option>
                                        <option value="No">No</option>
                                        <option value="Yes">Yes</option>
                                    </select>
                                     @error('deformity') <small class="text-danger">{{ $message }}</small> @enderror
                                </div>
                                <div class="col-md-4 form-group small">
                                    <label>C-Section History</label>
                                    <select class="form-control form-control-sm" wire:model="cSection">
                                        <option value="">Select</option>
                                        <option value="No">No</option>
                                        <option value="Yes">Yes</option>
                                    </select>
                                     @error('cSection') <small class="text-danger">{{ $message }}</small> @enderror
                                </div>
                                <div class="col-md-4 form-group small">
                                    <label>Vacuum Extraction</label>
                                    <select class="form-control form-control-sm" wire:model="vacum">
                                        <option value="">Select</option>
                                        <option value="No">No</option>
                                        <option value="Yes">Yes</option>
                                    </select>
                                     @error('vacum') <small class="text-danger">{{ $message }}</small> @enderror
                                </div>
                                <div class="col-md-4 form-group small">
                                    <label>Tuberculosis</label>
                                    <select class="form-control form-control-sm" wire:model="tuberculosis">
                                        <option value="">Select</option>
                                        <option value="No">No</option>
                                        <option value="Yes">Yes</option>
                                    </select>
                                     @error('tuberculosis') <small class="text-danger">{{ $message }}</small> @enderror
                                </div>
                                <div class="col-md-4 form-group small">
                                    <label>Asthma</label>
                                    <select class="form-control form-control-sm" wire:model="asthma">
                                        <option value="">Select</option>
                                        <option value="No">No</option>
                                        <option value="Yes">Yes</option>
                                    </select>
                                     @error('asthma') <small class="text-danger">{{ $message }}</small> @enderror
                                </div>
                                <div class="col-md-12 form-group small">
                                    <label>Menstrual Cycle</label>
                                    <select class="form-control form-control-sm" wire:model="menstrualCycle">
                                        <option value="">Select</option>
                                        <option value="Regular">Regular</option>
                                        <option value="Irregular">Irregular</option>
                                    </select>
                                     @error('menstrualCycle') <small class="text-danger">{{ $message }}</small> @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer px-0 pb-0">
                <button type="button" class="btn btn-secondary" wire:click="cancel">Cancel</button>
                <button type="submit" class="btn btn-navy shadow-sm px-4">
                    <i class="fas fa-save mr-1"></i> {{ $mother_id ? 'Update Records' : 'Register Mother' }}
                </button>
            </div>
        </form>
    </x-modal>

    <style>
        .text-navy { color: #001f3f !important; }
        .card-navy.card-outline { border-top: 3px solid #001f3f; }
        .btn-navy { background-color: #001f3f; color: #fff; }
        .btn-navy:hover { background-color: #002d5c; color: #fff; }
        .nav-tabs .nav-link.active { color: #001f3f; font-weight: bold; border-top: 3px solid #001f3f; }
    </style>

    <!-- Modal for Adding New District -->
    <x-modal title="Add New District" status="{{ $districtModal }}">
        <form wire:submit.prevent="storeDistrict">
            <div class="form-group">
                <label>District Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control" wire:model="new_district_name" placeholder="Enter district name">
                @error('new_district_name') <small class="text-danger">{{ $message }}</small> @enderror
            </div>
            <div class="modal-footer px-0 pb-0">
                <button type="button" class="btn btn-secondary" wire:click="$set('districtModal', false)">Cancel</button>
                <button type="submit" class="btn btn-navy shadow-sm px-4">Save District</button>
            </div>
        </form>
    </x-modal>

    <!-- Modal for Adding New Area -->
    <x-modal title="Add New Area" status="{{ $areaModal }}">
        <form wire:submit.prevent="storeArea">
            <div class="form-group">
                <label>District</label>
                <input type="text" class="form-control" value="{{ $districts->find($district_id)->name ?? '' }}" readonly>
            </div>
            <div class="form-group">
                <label>Area Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control" wire:model="new_area_name" placeholder="Enter area name">
                @error('new_area_name') <small class="text-danger">{{ $message }}</small> @enderror
            </div>
            <div class="modal-footer px-0 pb-0">
                <button type="button" class="btn btn-secondary" wire:click="$set('areaModal', false)">Cancel</button>
                <button type="submit" class="btn btn-navy shadow-sm px-4">Save Area</button>
            </div>
        </form>
    </x-modal>

    <!-- Modal for Bulk Reassignment -->
    <x-modal title="Bulk Reassign Mothers" status="{{ $reassignModal }}">
        <form wire:submit.prevent="confirmReassign">
            <div class="alert alert-info">
                <i class="fas fa-info-circle mr-2"></i> You are reassigning <strong>{{ count($selectedMothers) }}</strong> mothers to a new organization.
            </div>
            <div class="form-group">
                <label>Target Organization <span class="text-danger">*</span></label>
                <select class="form-control" wire:model="bulkOrganizationId">
                    <option value="">Select Organization</option>
                    @foreach($organizations as $org)
                        <option value="{{ $org->id }}">{{ $org->name }}</option>
                    @endforeach
                </select>
                @error('bulkOrganizationId') <small class="text-danger">{{ $message }}</small> @enderror
            </div>
            <div class="modal-footer px-0 pb-0">
                <button type="button" class="btn btn-secondary" wire:click="$set('reassignModal', false)">Cancel</button>
                <button type="submit" class="btn btn-warning shadow-sm px-4">Confirm Reassignment</button>
            </div>
        </form>
    </x-modal>
</div>
