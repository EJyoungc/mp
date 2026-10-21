<div>
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2 align-items-center">
                <div class="col-sm-6">
                    <div class="d-flex align-items-center">
                        <a href="{{ route('mothers') }}" class="btn btn-sm btn-outline-secondary mr-3 shadow-sm" title="Back to Mothers">
                            <i class="fas fa-arrow-left"></i>
                        </a>
                        <div>
                            <h1 class="m-0 text-bold text-navy">
                                {{ $mother->name }}
                            </h1>
                            <small class="text-muted">Maternal Care Profile, Pregnancy Tracker & SMS History</small>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 text-sm-right mt-2 mt-sm-0">
                    <button class="btn btn-primary shadow-sm mr-2 font-weight-bold" wire:click="create">
                        <i class="fas fa-baby mr-1"></i> Add Pregnancy Session
                    </button>
                    <button class="btn btn-outline-navy shadow-sm font-weight-bold" wire:click="openCustomSmsModal">
                        <i class="fas fa-paper-plane mr-1"></i> Send Quick SMS
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="content">
        <div class="container-fluid">

            <!-- Hero Maternal & Active Pregnancy Snapshot Card -->
            <div class="card card-outline card-navy shadow-sm mb-4">
                <div class="card-body">
                    <div class="row align-items-center">
                        <!-- Mother Personal Summary -->
                        <div class="col-lg-4 col-md-5 border-right-md mb-3 mb-md-0">
                            <div class="d-flex align-items-center">
                                <div class="mother-avatar bg-navy text-white mr-3 shadow-sm">
                                    <i class="fas fa-female fa-2x"></i>
                                </div>
                                <div>
                                    <h3 class="font-weight-bold text-navy mb-0">{{ $mother->name }}</h3>
                                    <div class="text-muted small mb-1">
                                        @if($mother->age)
                                            <span class="badge badge-light border mr-1">{{ $mother->age }} yrs old</span>
                                        @endif
                                        @if($mother->marital_status)
                                            <span class="badge badge-light border mr-1">{{ ucfirst($mother->marital_status) }}</span>
                                        @endif
                                        <span class="badge badge-success">Registered Mother</span>
                                    </div>
                                    <div class="small text-muted">
                                        <i class="fas fa-phone-alt text-success mr-1"></i>
                                        <a href="tel:{{ $mother->phone }}" class="text-dark font-weight-bold">{{ $mother->phone ?? 'No Phone' }}</a>
                                    </div>
                                    <div class="small text-muted">
                                        <i class="fas fa-map-marker-alt text-danger mr-1"></i>
                                        {{ $mother->district->name ?? 'N/A' }} / {{ $mother->area->name ?? 'N/A' }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Active Pregnancy & Trimester Snapshot -->
                        <div class="col-lg-5 col-md-7 border-right-lg mb-3 mb-lg-0">
                            @if($pregnancyData)
                                <div class="px-md-2">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <div>
                                            <span class="badge badge-purple px-2 py-1 font-weight-bold">
                                                <i class="fas fa-heartbeat mr-1"></i> Infant #{{ $pregnancyData['session']->infant_number }}
                                            </span>
                                            <span class="badge badge-info px-2 py-1 ml-1 font-weight-bold">
                                                {{ $pregnancyData['trimesterName'] }}
                                            </span>
                                        </div>
                                        <div class="text-right">
                                            <strong class="text-navy h5 font-weight-bold mb-0">Week {{ $pregnancyData['weeks'] }}</strong>
                                            <span class="text-muted small">(Day {{ $pregnancyData['dayInWeek'] }})</span>
                                        </div>
                                    </div>

                                    <!-- Visual Pregnancy Progress Bar -->
                                    <div class="progress progress-sm my-2" style="height: 10px; border-radius: 6px;">
                                        <div class="progress-bar bg-purple progress-bar-striped progress-bar-animated" 
                                             role="progressbar" 
                                             style="width: {{ $pregnancyData['progress'] }}%" 
                                             aria-valuenow="{{ $pregnancyData['progress'] }}" 
                                             aria-valuemin="0" 
                                             aria-valuemax="100"></div>
                                    </div>

                                    <div class="d-flex justify-content-between text-muted small">
                                        <span>LMP: <strong>{{ $pregnancyData['lmp']->format('M d, Y') }}</strong></span>
                                        <span>EDD: <strong>{{ $pregnancyData['edd']->format('M d, Y') }}</strong></span>
                                        <span class="{{ $pregnancyData['daysToEdd'] < 14 ? 'text-danger font-weight-bold' : 'text-purple' }}">
                                            @if($pregnancyData['daysToEdd'] > 0)
                                                {{ $pregnancyData['daysToEdd'] }} days remaining
                                            @elseif($pregnancyData['daysToEdd'] === 0)
                                                Due Today!
                                            @else
                                                Full Term / Delivered
                                            @endif
                                        </span>
                                    </div>
                                </div>
                            @else
                                <div class="text-center py-2 text-muted">
                                    <i class="fas fa-calendar-plus fa-2x mb-1 text-secondary"></i>
                                    <div>No active pregnancy session recorded.</div>
                                    <button class="btn btn-sm btn-outline-primary mt-1" wire:click="create">
                                        <i class="fas fa-plus mr-1"></i> Record LMP Date
                                    </button>
                                </div>
                            @endif
                        </div>

                        <!-- Organization & Facility Stats -->
                        <div class="col-lg-3 col-md-12 text-lg-right pt-2 pt-lg-0">
                            <div class="small text-muted mb-1">
                                Affiliated Facility:
                            </div>
                            <div class="font-weight-bold text-navy mb-2">
                                <i class="fas fa-hospital-alt text-muted mr-1"></i> {{ $mother->organization->name ?? 'Unassigned Facility' }}
                            </div>
                            <div class="d-flex justify-content-lg-end gap-2 text-muted small">
                                <div class="px-2 py-1 bg-light rounded text-center mr-1">
                                    <div class="font-weight-bold text-navy">{{ $sessions->count() }}</div>
                                    <small>{{ Str::plural('Session', $sessions->count()) }}</small>
                                </div>
                                <div class="px-2 py-1 bg-light rounded text-center mr-1">
                                    <div class="font-weight-bold text-navy">{{ $totalTipsCount }}</div>
                                    <small>SMS Tips</small>
                                </div>
                                <div class="px-2 py-1 bg-light rounded text-center">
                                    <div class="font-weight-bold text-success">{{ $deliveryRate }}%</div>
                                    <small>Success</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Top Metric Small Boxes -->
            <div class="row">
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-purple shadow-sm">
                        <div class="inner">
                            <h3>{{ $pregnancyData ? 'Wk '.$pregnancyData['weeks'] : 'N/A' }}</h3>
                            <p>{{ $pregnancyData ? $pregnancyData['trimesterName'] : 'No Active Pregnancy' }}</p>
                        </div>
                        <div class="icon"><i class="fas fa-baby"></i></div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-navy shadow-sm">
                        <div class="inner">
                            <h3>{{ $sessions->count() }}</h3>
                            <p>Infants / Sessions Recorded</p>
                        </div>
                        <div class="icon"><i class="fas fa-calendar-check"></i></div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-info shadow-sm">
                        <div class="inner">
                            <h3>{{ $totalTipsCount + $totalAdsCount }}</h3>
                            <p>Total SMS Dispatched</p>
                        </div>
                        <div class="icon"><i class="fas fa-sms"></i></div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-success shadow-sm">
                        <div class="inner">
                            <h3>{{ $deliveryRate }}%</h3>
                            <p>Delivery Success Rate</p>
                        </div>
                        <div class="icon"><i class="fas fa-check-circle"></i></div>
                    </div>
                </div>
            </div>

            <!-- Navigation Tabs & Content Section -->
            <div class="card card-navy card-outline card-outline-tabs shadow-sm border-0">
                <div class="card-header p-0 border-bottom-0">
                    <ul class="nav nav-tabs" id="motherTabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link {{ $activeTab === 'sessions' ? 'active font-weight-bold text-navy' : 'text-muted' }}" 
                               href="javascript:void(0)" 
                               wire:click="setTab('sessions')">
                                <i class="fas fa-baby mr-1"></i> Pregnancy & Infants 
                                <span class="badge badge-purple ml-1">{{ $sessions->count() }}</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ $activeTab === 'messages' ? 'active font-weight-bold text-navy' : 'text-muted' }}" 
                               href="javascript:void(0)" 
                               wire:click="setTab('messages')">
                                <i class="fas fa-sms mr-1"></i> Messages Received 
                                <span class="badge badge-info ml-1">{{ $totalTipsCount + $totalAdsCount }}</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ $activeTab === 'profile' ? 'active font-weight-bold text-navy' : 'text-muted' }}" 
                               href="javascript:void(0)" 
                               wire:click="setTab('profile')">
                                <i class="fas fa-notes-medical mr-1"></i> Clinical & Socio-Demographic Details
                            </a>
                        </li>
                    </ul>
                </div>

                <div class="card-body">
                    <!-- ============================================== -->
                    <!-- TAB 1: PREGNANCY & INFANTS SESSIONS -->
                    <!-- ============================================== -->
                    @if($activeTab === 'sessions')
                        <div>
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div>
                                    <h5 class="font-weight-bold text-navy mb-0">Infant & Pregnancy Sessions</h5>
                                    <small class="text-muted">Track each child's gestational timeline, trimesters, and health history</small>
                                </div>
                                <button class="btn btn-primary btn-sm shadow-sm font-weight-bold" wire:click="create">
                                    <i class="fas fa-plus mr-1"></i> Add Pregnancy Session
                                </button>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-hover table-striped align-middle">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>#</th>
                                            <th>Infant Sequence</th>
                                            <th>LMP (Last Period)</th>
                                            <th>Estimated Due Date (EDD)</th>
                                            <th>Gestational Age</th>
                                            <th>Trimester</th>
                                            <th>Progress</th>
                                            <th class="text-right">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($sessions as $index => $item)
                                            @php
                                                $sessionLmp = \Carbon\Carbon::parse($item->last_menstrual_cycle);
                                                $sessionNow = \Carbon\Carbon::now();
                                                $sDays = (int) max(0, $sessionLmp->diffInDays($sessionNow));
                                                $sWeeks = (int) max(1, ceil($sDays / 7));
                                                $sDayInWeek = ($sDays % 7) + 1;
                                                $sEdd = $sessionLmp->copy()->addDays(280);
                                                $sTrimester = $sWeeks <= 12 ? 1 : ($sWeeks <= 27 ? 2 : 3);
                                                $sProgress = min(100, max(0, round(($sWeeks / 40) * 100)));
                                            @endphp
                                            <tr wire:key="session-row-{{ $item->id }}" class="{{ $index === 0 ? 'table-primary-soft' : '' }}">
                                                <td>{{ $index + 1 }}</td>
                                                <td>
                                                    <div class="font-weight-bold text-navy">
                                                        <i class="fas fa-baby text-purple mr-1"></i> Infant #{{ $item->infant_number }}
                                                    </div>
                                                    @if($index === 0)
                                                        <span class="badge badge-success small">Current Active</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <div class="font-weight-bold text-dark">{{ $sessionLmp->format('M d, Y') }}</div>
                                                    <small class="text-muted">{{ $sessionLmp->diffForHumans() }}</small>
                                                </td>
                                                <td>
                                                    <div class="font-weight-bold text-navy">{{ $sEdd->format('M d, Y') }}</div>
                                                    <small class="text-muted">
                                                        @if($sEdd->isFuture())
                                                            {{ $sessionNow->diffInDays($sEdd) }} days to go
                                                        @else
                                                            Past Due / Delivered
                                                        @endif
                                                    </small>
                                                </td>
                                                <td>
                                                    <span class="badge badge-dark px-2 py-1 font-weight-bold">
                                                        Week {{ $sWeeks > 40 ? 'Complete (40+)' : $sWeeks }}
                                                    </span>
                                                    <small class="text-muted ml-1">Day {{ $sDayInWeek }}</small>
                                                </td>
                                                <td>
                                                    @if($sTrimester === 1)
                                                        <span class="badge badge-info px-2 py-1">1st Trimester (W1-12)</span>
                                                    @elseif($sTrimester === 2)
                                                        <span class="badge badge-purple px-2 py-1">2nd Trimester (W13-27)</span>
                                                    @else
                                                        <span class="badge badge-teal px-2 py-1">3rd Trimester (W28-40)</span>
                                                    @endif
                                                </td>
                                                <td style="min-width: 140px;">
                                                    <div class="progress progress-xs mb-1" style="height: 6px;">
                                                        <div class="progress-bar bg-purple" style="width: {{ $sProgress }}%"></div>
                                                    </div>
                                                    <small class="text-muted">{{ $sProgress }}% of term</small>
                                                </td>
                                                <td class="text-right">
                                                    <a href="{{ route('mothers.history.show', [$mother->id, $item->id]) }}" 
                                                       class="btn btn-sm btn-outline-info shadow-sm mr-1" 
                                                       title="View Session SMS History">
                                                        <i class="fas fa-history mr-1"></i> Messages
                                                    </a>
                                                    <button wire:click="create({{ $item->id }})" 
                                                            class="btn btn-sm btn-outline-primary shadow-sm mr-1" 
                                                            title="Edit Session">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button wire:click="confirmDeleteSession({{ $item->id }})" 
                                                            class="btn btn-sm btn-outline-danger shadow-sm" 
                                                            title="Delete Session">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="8" class="text-center py-5 text-muted">
                                                    <i class="fas fa-baby fa-3x mb-3 text-secondary"></i>
                                                    <h5>No Pregnancy Sessions Recorded</h5>
                                                    <p class="small">Click "Add Pregnancy Session" above to register an infant sequence and LMP date.</p>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                    <!-- ============================================== -->
                    <!-- TAB 2: MESSAGES RECEIVED SECTION -->
                    <!-- ============================================== -->
                    @elseif($activeTab === 'messages')
                        <div>
                            <!-- Messages Header & Action Toolbar -->
                            <div class="row mb-3 align-items-center">
                                <div class="col-md-4 mb-2">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text bg-white border-right-0"><i class="fas fa-search text-muted"></i></span>
                                        </div>
                                        <input type="text" class="form-control border-left-0" 
                                               wire:model.live.debounce.300ms="messageSearch" 
                                               placeholder="Search SMS message content...">
                                        @if(!empty($messageSearch))
                                            <div class="input-group-append">
                                                <button class="btn btn-outline-secondary" type="button" wire:click="$set('messageSearch', '')">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-2 col-6 mb-2">
                                    <select class="form-control" wire:model.live="messageTrimesterFilter">
                                        <option value="">-- All Trimesters --</option>
                                        <option value="1">1st Trimester</option>
                                        <option value="2">2nd Trimester</option>
                                        <option value="3">3rd Trimester</option>
                                    </select>
                                </div>
                                <div class="col-md-2 col-6 mb-2">
                                    <select class="form-control" wire:model.live="messageStatusFilter">
                                        <option value="">-- All Statuses --</option>
                                        <option value="Sent">Sent / Delivered</option>
                                        <option value="Failed">Failed</option>
                                    </select>
                                </div>
                                <div class="col-md-2 col-6 mb-2">
                                    <select class="form-control" wire:model.live="messageTypeFilter">
                                        <option value="all">All Message Types</option>
                                        <option value="tips">Health Tips Only</option>
                                        <option value="ads">Pharmacy Ads Only</option>
                                    </select>
                                </div>
                                <div class="col-md-2 col-6 mb-2 text-right">
                                    <button class="btn btn-navy btn-block shadow-sm font-weight-bold" wire:click="openCustomSmsModal">
                                        <i class="fas fa-paper-plane mr-1"></i> Send SMS
                                    </button>
                                </div>
                            </div>

                            <!-- Healthcare Tips SMS Feed -->
                            @if($messageTypeFilter === 'all' || $messageTypeFilter === 'tips')
                                <div class="mb-4">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <h6 class="font-weight-bold text-navy mb-0">
                                            <i class="fas fa-comment-medical text-primary mr-1"></i> Pregnancy Health Tips Dispatched ({{ $tipsMessages->total() }})
                                        </h6>
                                        <small class="text-muted">Weekly scheduled maternal healthcare guidance</small>
                                    </div>

                                    <div class="table-responsive">
                                        <table class="table table-hover table-striped align-middle">
                                            <thead class="thead-light">
                                                <tr>
                                                    <th style="width: 50px;">#</th>
                                                    <th>Target Stage</th>
                                                    <th>SMS Content</th>
                                                    <th>Status</th>
                                                    <th>Sent Date & Time</th>
                                                    <th class="text-right">Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($tipsMessages as $msg)
                                                    @php
                                                        $tNum = $msg->week->trimester->trimester ?? ($msg->week->trimester_id ?? null);
                                                        $isSent = in_array(strtolower($msg->message_status), ['sent', 'delivered', 'success']);
                                                    @endphp
                                                    <tr wire:key="msg-row-{{ $msg->id }}">
                                                        <td>{{ $loop->iteration + ($tipsMessages->currentPage() - 1) * $tipsMessages->perPage() }}</td>
                                                        <td>
                                                            @if($msg->week)
                                                                <div class="font-weight-bold text-navy">
                                                                    Week {{ $msg->week->week }}
                                                                </div>
                                                                @if($tNum)
                                                                    <span class="badge badge-purple small">Trimester {{ $tNum }}</span>
                                                                @endif
                                                                @if($msg->day)
                                                                    <small class="text-muted ml-1">Day {{ $msg->day->day_number }}</small>
                                                                @endif
                                                            @else
                                                                <span class="badge badge-light border">Direct SMS</span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <div class="sms-bubble p-2 bg-light rounded border">
                                                                <i class="fas fa-quote-left text-muted mr-1 small"></i>
                                                                {{ $msg->tip->tip ?? 'Direct Message' }}
                                                            </div>
                                                        </td>
                                                        <td>
                                                            @if($isSent)
                                                                <span class="badge badge-success px-2 py-1"><i class="fas fa-check-circle mr-1"></i> Delivered</span>
                                                            @else
                                                                <span class="badge badge-danger px-2 py-1"><i class="fas fa-exclamation-triangle mr-1"></i> {{ ucfirst($msg->message_status ?? 'Failed') }}</span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <div class="font-weight-bold text-dark">{{ $msg->created_at->format('M d, Y') }}</div>
                                                            <small class="text-muted">{{ $msg->created_at->format('h:i A') }} ({{ $msg->created_at->diffForHumans() }})</small>
                                                        </td>
                                                        <td class="text-right">
                                                            <button class="btn btn-sm btn-outline-info shadow-sm mr-1" 
                                                                    wire:click="viewMessage({{ $msg->id }}, 'tip')" 
                                                                    title="View SMS Delivery Details">
                                                                <i class="fas fa-eye"></i> Details
                                                            </button>
                                                            @if(!$isSent)
                                                                <button class="btn btn-sm btn-outline-warning shadow-sm" 
                                                                        wire:click="resendTipSms({{ $msg->id }})" 
                                                                        title="Resend SMS">
                                                                    <i class="fas fa-redo"></i> Resend
                                                                </button>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="6" class="text-center py-4 text-muted">
                                                            <i class="fas fa-sms fa-2x mb-2 text-secondary"></i>
                                                            <div>No health tips match the filter criteria.</div>
                                                        </td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>

                                    <div class="mt-3">
                                        {{ $tipsMessages->links() }}
                                    </div>
                                </div>
                            @endif

                            <!-- Pharmacy Health Alerts / Ads Section -->
                            @if(($messageTypeFilter === 'all' || $messageTypeFilter === 'ads') && count($adsMessages) > 0)
                                <div class="mt-4 pt-3 border-top">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <h6 class="font-weight-bold text-navy mb-0">
                                            <i class="fas fa-bullhorn text-success mr-1"></i> Targeted Health Alerts & Pharmacy Notifications ({{ count($adsMessages) }})
                                        </h6>
                                        <small class="text-muted">Delivered based on geographic area & pregnancy trimester</small>
                                    </div>

                                    <div class="table-responsive">
                                        <table class="table table-hover table-striped align-middle">
                                            <thead class="thead-light">
                                                <tr>
                                                    <th>#</th>
                                                    <th>Type</th>
                                                    <th>Alert / Ad Message</th>
                                                    <th>Status</th>
                                                    <th>Dispatched At</th>
                                                    <th class="text-right">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($adsMessages as $ad)
                                                    <tr wire:key="ad-row-{{ $ad->id }}">
                                                        <td>{{ $loop->iteration }}</td>
                                                        <td>
                                                            <span class="badge badge-success px-2 py-1"><i class="fas fa-prescription-bottle-alt mr-1"></i> Pharmacy Alert</span>
                                                        </td>
                                                        <td>
                                                            <div class="sms-bubble p-2 bg-light rounded border">
                                                                {{ $ad->message ?? ($ad->pharmacyAd->ad_message ?? 'N/A') }}
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <span class="badge badge-{{ strtolower($ad->status ?? 'sent') === 'sent' ? 'success' : 'warning' }}">
                                                                {{ ucfirst($ad->status ?? 'Sent') }}
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <small class="text-muted">{{ $ad->created_at->format('M d, Y • h:i A') }}</small>
                                                        </td>
                                                        <td class="text-right">
                                                            <button class="btn btn-sm btn-outline-info shadow-sm" 
                                                                    wire:click="viewMessage({{ $ad->id }}, 'ad')" 
                                                                    title="View Details">
                                                                <i class="fas fa-eye"></i> Details
                                                            </button>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            @endif
                        </div>

                    <!-- ============================================== -->
                    <!-- TAB 3: CLINICAL & SOCIO-DEMOGRAPHIC DETAILS -->
                    <!-- ============================================== -->
                    @elseif($activeTab === 'profile')
                        <div class="row">
                            <!-- Personal & Contact Information -->
                            <div class="col-md-4 mb-4">
                                <div class="card card-outline card-navy h-100 shadow-sm">
                                    <div class="card-header bg-light">
                                        <h5 class="card-title font-weight-bold text-navy mb-0">
                                            <i class="fas fa-user-circle mr-1"></i> Socio-Demographics
                                        </h5>
                                    </div>
                                    <div class="card-body p-0">
                                        <table class="table table-bordered mb-0">
                                            <tr>
                                                <th style="width: 45%;">Full Name</th>
                                                <td>{{ $mother->name }}</td>
                                            </tr>
                                            <tr>
                                                <th>Age / DOB</th>
                                                <td>
                                                    {{ $mother->age ? $mother->age.' yrs' : 'N/A' }}
                                                    @if($mother->date_of_birth)
                                                        ({{ \Carbon\Carbon::parse($mother->date_of_birth)->format('M d, Y') }})
                                                    @endif
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>Phone Number</th>
                                                <td>
                                                    <a href="tel:{{ $mother->phone }}" class="text-primary font-weight-bold">{{ $mother->phone ?? 'Not provided' }}</a>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>Email Address</th>
                                                <td>{{ $mother->email ?? 'Not provided' }}</td>
                                            </tr>
                                            <tr>
                                                <th>Marital Status</th>
                                                <td>{{ ucfirst($mother->marital_status ?? 'Unspecified') }}</td>
                                            </tr>
                                            <tr>
                                                <th>Religion</th>
                                                <td>{{ $mother->religion ?? 'Unspecified' }}</td>
                                            </tr>
                                            <tr>
                                                <th>Education</th>
                                                <td>{{ $mother->level_of_education ?? 'Unspecified' }}</td>
                                            </tr>
                                            <tr>
                                                <th>Occupation</th>
                                                <td>{{ $mother->occupation ?? 'Unspecified' }}</td>
                                            </tr>
                                            <tr>
                                                <th>Location</th>
                                                <td>
                                                    {{ $mother->district->name ?? 'N/A' }} / {{ $mother->area->name ?? 'N/A' }}<br>
                                                    <small class="text-muted">TA: {{ $mother->traditional_authority ?? 'N/A' }}</small>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>Physical Address</th>
                                                <td>{{ $mother->address ?? 'Not specified' }}</td>
                                            </tr>
                                            <tr>
                                                <th>Next of Kin</th>
                                                <td>
                                                    <strong>{{ $mother->next_of_kin ?? 'N/A' }}</strong><br>
                                                    <small class="text-muted">{{ $mother->next_of_kin_mobile ?? '' }}</small>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <!-- Obstetric & Delivery Background -->
                            <div class="col-md-4 mb-4">
                                <div class="card card-outline card-purple h-100 shadow-sm">
                                    <div class="card-header bg-light">
                                        <h5 class="card-title font-weight-bold text-purple mb-0">
                                            <i class="fas fa-female mr-1"></i> Obstetric History
                                        </h5>
                                    </div>
                                    <div class="card-body p-0">
                                        <table class="table table-bordered mb-0">
                                            <tr>
                                                <th style="width: 50%;">Total Deliveries</th>
                                                <td>
                                                    <span class="badge badge-info px-2 py-1 font-weight-bold">{{ $mother->deliveries ?? 0 }}</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>Abortions / Miscarriages</th>
                                                <td>
                                                    <span class="badge badge-{{ ($mother->abortions ?? 0) > 0 ? 'warning' : 'light border' }} px-2 py-1">
                                                        {{ $mother->abortions ?? 0 }}
                                                    </span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>Stillbirths</th>
                                                <td>
                                                    <span class="badge badge-{{ strtolower($mother->still_births ?? 'no') === 'yes' ? 'danger' : 'success' }}">
                                                        {{ ucfirst($mother->still_births ?? 'no') }}
                                                    </span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>Cesarean Section (C-Section)</th>
                                                <td>
                                                    <span class="badge badge-{{ strtolower($mother->c_section ?? 'no') === 'yes' ? 'warning' : 'light border' }}">
                                                        {{ ucfirst($mother->c_section ?? 'no') }}
                                                    </span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>Vacuum Extraction</th>
                                                <td>
                                                    <span class="badge badge-{{ strtolower($mother->vacum ?? 'no') === 'yes' ? 'warning' : 'light border' }}">
                                                        {{ ucfirst($mother->vacum ?? 'no') }}
                                                    </span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>Multiple Gestation (Twins/Triplets)</th>
                                                <td>
                                                    <span class="badge badge-{{ strtolower($mother->multiple ?? 'no') === 'yes' ? 'info' : 'light border' }}">
                                                        {{ ucfirst($mother->multiple ?? 'no') }}
                                                    </span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>Menstrual Cycle Pattern</th>
                                                <td>
                                                    <span class="badge badge-{{ strtolower($mother->menstrual_cycle ?? 'regular') === 'regular' ? 'success' : 'warning' }}">
                                                        {{ ucfirst($mother->menstrual_cycle ?? 'Regular') }}
                                                    </span>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <!-- Physical Anthropometry & Clinical Risk Factors -->
                            <div class="col-md-4 mb-4">
                                <div class="card card-outline card-danger h-100 shadow-sm">
                                    <div class="card-header bg-light">
                                        <h5 class="card-title font-weight-bold text-danger mb-0">
                                            <i class="fas fa-heartbeat mr-1"></i> Medical & Risk Factors
                                        </h5>
                                    </div>
                                    <div class="card-body p-0">
                                        <table class="table table-bordered mb-0">
                                            <tr>
                                                <th style="width: 50%;">Height</th>
                                                <td>
                                                    @if($mother->height)
                                                        <strong>{{ $mother->height }} cm</strong>
                                                        @if($mother->height < 150)
                                                            <span class="badge badge-danger ml-1" title="Short stature (<150cm) indicates higher obstetric risk">At Risk (<150cm)</span>
                                                        @endif
                                                    @else
                                                        <span class="text-muted">Not recorded</span>
                                                    @endif
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>Spine or Leg Deformity</th>
                                                <td>
                                                    <span class="badge badge-{{ strtolower($mother->leg_or_spine ?? 'no') === 'yes' ? 'danger' : 'success' }}">
                                                        {{ ucfirst($mother->leg_or_spine ?? 'no') }}
                                                    </span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>General Physical Deformity</th>
                                                <td>
                                                    <span class="badge badge-{{ strtolower($mother->deformity ?? 'no') === 'yes' ? 'danger' : 'success' }}">
                                                        {{ ucfirst($mother->deformity ?? 'no') }}
                                                    </span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>Tuberculosis (TB)</th>
                                                <td>
                                                    <span class="badge badge-{{ strtolower($mother->tuberculosis ?? 'no') === 'yes' ? 'danger' : 'success' }}">
                                                        {{ ucfirst($mother->tuberculosis ?? 'no') }}
                                                    </span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>Asthma / Respiratory</th>
                                                <td>
                                                    <span class="badge badge-{{ strtolower($mother->asthma ?? 'no') === 'yes' ? 'danger' : 'success' }}">
                                                        {{ ucfirst($mother->asthma ?? 'no') }}
                                                    </span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>Pre-Eclampsia Risk</th>
                                                <td>
                                                    <span class="badge badge-{{ strtolower($mother->pre_eclampsia ?? 'no') === 'yes' ? 'danger' : 'success' }}">
                                                        {{ ucfirst($mother->pre_eclampsia ?? 'no') }}
                                                    </span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>Hypertension</th>
                                                <td>
                                                    <span class="badge badge-{{ strtolower($mother->hypertension ?? 'no') === 'yes' ? 'danger' : 'success' }}">
                                                        {{ ucfirst($mother->hypertension ?? 'no') }}
                                                    </span>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>

    <!-- ============================================== -->
    <!-- MODALS -->
    <!-- ============================================== -->

    <!-- Add / Edit Pregnancy Session Modal -->
    <x-modal status="{{ $modal }}" title="{{ $history_id ? 'Edit Pregnancy Session' : 'Record New Pregnancy Session' }}">
        <form wire:submit.prevent="store">
            <div class="form-group">
                <label>Infant Sequence Number <span class="text-danger">*</span></label>
                <input type="number" class="form-control" wire:model="infant_number" min="1" placeholder="1">
                <small class="text-muted">Numeric sequence for this child/infant (e.g. 1 for first pregnancy, 2 for second, etc.)</small>
                <x-input-error for="infant_number" />
            </div>

            <div class="form-group">
                <label>Last Menstrual Period (LMP) Date <span class="text-danger">*</span></label>
                <input type="date" class="form-control" wire:model="last_menstrual_cycle" max="{{ date('Y-m-d') }}">
                <small class="text-muted">The first day of the mother's last normal menstrual cycle</small>
                <x-input-error for="last_menstrual_cycle" />
            </div>

            <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                <button type="button" class="btn btn-secondary mr-2" wire:click="cancel">
                    <i class="fas fa-times mr-1"></i> Cancel
                </button>
                <button type="submit" class="btn btn-primary font-weight-bold">
                    <i class="fas fa-save mr-1"></i> {{ $history_id ? 'Update Session' : 'Save Session' }}
                    <x-spinner for="store" />
                </button>
            </div>
        </form>
    </x-modal>

    <!-- Confirm Delete Session Modal -->
    <x-modal status="{{ $deleteModal }}" title="Confirm Session Deletion">
        <div class="p-2">
            <div class="alert alert-danger d-flex align-items-center mb-3">
                <i class="fas fa-exclamation-triangle fa-2x mr-3"></i>
                <div>
                    <h5 class="mb-1 font-weight-bold">Delete Pregnancy Session</h5>
                    <div>Are you sure you want to delete this pregnancy session?</div>
                </div>
            </div>
            <p class="text-muted small">
                This will remove the recorded gestational age, LMP, and associated timeline for this specific infant.
            </p>
            <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                <button type="button" class="btn btn-secondary mr-2" wire:click="cancel">
                    <i class="fas fa-times mr-1"></i> Cancel
                </button>
                <button type="button" class="btn btn-danger font-weight-bold" wire:click="deleteSession">
                    <i class="fas fa-trash-alt mr-1"></i> Yes, Delete Session
                </button>
            </div>
        </div>
    </x-modal>

    <!-- View SMS Message Details Modal -->
    <x-modal status="{{ $viewMessageModal }}" title="SMS Message Delivery Details">
        @if($selectedMessage)
            <div class="p-1">
                <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                    <div>
                        <span class="badge badge-purple px-2 py-1 font-weight-bold">{{ $selectedMessage['title'] }}</span>
                        @if($selectedMessage['trimester'])
                            <span class="badge badge-info ml-1">Trimester {{ $selectedMessage['trimester'] }}</span>
                        @endif
                        @if($selectedMessage['week'])
                            <span class="badge badge-dark ml-1">Week {{ $selectedMessage['week'] }}</span>
                        @endif
                    </div>
                    <div class="small text-muted">
                        <i class="fas fa-clock mr-1"></i> {{ $selectedMessage['date'] }}
                    </div>
                </div>

                <div class="card bg-light border-0 mb-3">
                    <div class="card-body">
                        <div class="text-muted small font-weight-bold mb-1">Message Content:</div>
                        <div class="h6 mb-0 text-dark font-italic">
                            "{{ $selectedMessage['body'] }}"
                        </div>
                    </div>
                </div>

                <div class="row small mb-3">
                    <div class="col-6">
                        <strong>Delivery Status:</strong> 
                        <span class="badge badge-{{ strtolower($selectedMessage['status']) === 'sent' || strtolower($selectedMessage['status']) === 'delivered' ? 'success' : 'danger' }} ml-1">
                            {{ ucfirst($selectedMessage['status']) }}
                        </span>
                    </div>
                    <div class="col-6 text-right">
                        <strong>Recipient Phone:</strong> {{ $mother->phone ?? 'N/A' }}
                    </div>
                </div>

                @if(!empty($selectedMessage['response']))
                    <div class="mt-3">
                        <div class="text-muted small font-weight-bold mb-1">Gateway API Response Payload:</div>
                        <pre class="bg-dark text-success p-3 rounded small" style="max-height: 180px; overflow-y: auto;">{{ json_encode($selectedMessage['response'], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
                    </div>
                @endif

                <div class="d-flex justify-content-end mt-4 pt-3 border-top">
                    <button type="button" class="btn btn-secondary" wire:click="cancel">
                        <i class="fas fa-check mr-1"></i> Done
                    </button>
                </div>
            </div>
        @endif
    </x-modal>

    <!-- Send Quick Custom SMS Modal -->
    <x-modal status="{{ $customSmsModal }}" title="Send Quick SMS to Mother">
        <form wire:submit.prevent="sendCustomSms">
            <div class="alert alert-info py-2 px-3 small mb-3">
                <i class="fas fa-info-circle mr-1"></i> 
                Dispatches an instant direct SMS to <strong>{{ $mother->name }}</strong> ({{ $mother->phone }}).
            </div>

            <div class="form-group">
                <label>SMS Message Text <span class="text-danger">*</span></label>
                <textarea class="form-control" rows="4" wire:model="customMessageText" placeholder="e.g. Please remember to attend your upcoming antenatal clinic visit tomorrow morning at 09:00 AM." maxlength="160"></textarea>
                <div class="d-flex justify-content-between mt-1">
                    <small class="text-muted">Standard SMS max length: 160 characters</small>
                    <small class="text-muted font-weight-bold">{{ strlen($customMessageText) }}/160</small>
                </div>
                <x-input-error for="customMessageText" />
            </div>

            <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                <button type="button" class="btn btn-secondary mr-2" wire:click="cancel">
                    <i class="fas fa-times mr-1"></i> Cancel
                </button>
                <button type="submit" class="btn btn-primary font-weight-bold">
                    <i class="fas fa-paper-plane mr-1"></i> Send SMS Now
                    <x-spinner for="sendCustomSms" />
                </button>
            </div>
        </form>
    </x-modal>

    <style>
        .text-navy { color: #001f3f !important; }
        .bg-navy { background-color: #001f3f !important; color: #fff; }
        .btn-navy { background-color: #001f3f; color: #fff; border-color: #001f3f; }
        .btn-navy:hover { background-color: #001730; color: #fff; }
        .badge-navy { background-color: #001f3f !important; color: #fff; }
        .card-navy.card-outline { border-top: 3px solid #001f3f; }
        .card-purple.card-outline { border-top: 3px solid #6f42c1; }
        .card-danger.card-outline { border-top: 3px solid #dc3545; }
        .bg-purple { background-color: #6f42c1 !important; color: #fff; }
        .text-purple { color: #6f42c1 !important; }
        .badge-purple { background-color: #6f42c1 !important; color: #fff; }
        .badge-teal { background-color: #20c997 !important; color: #fff; }
        .mother-avatar {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .table-primary-soft { background-color: #f3e8ff; }
        .border-right-md { border-right: 1px solid #dee2e6; }
        @media (max-width: 767.98px) {
            .border-right-md { border-right: none; border-bottom: 1px solid #dee2e6; padding-bottom: 1rem; }
        }
        .border-right-lg { border-right: 1px solid #dee2e6; }
        @media (max-width: 991.98px) {
            .border-right-lg { border-right: none; border-bottom: 1px solid #dee2e6; padding-bottom: 1rem; }
        }
    </style>
</div>
