<div>
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2 align-items-center">
                <div class="col-sm-6">
                    <div class="d-flex align-items-center">
                        <a href="{{ route('organizations') }}" class="btn btn-sm btn-outline-secondary mr-3 shadow-sm" title="Back to Organizations">
                            <i class="fas fa-arrow-left"></i>
                        </a>
                        <div>
                            <h1 class="m-0 text-bold text-navy">
                                {{ $organization->name }}
                            </h1>
                            <small class="text-muted">Organization Profile & Dashboard</small>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('organizations') }}">Organizations</a></li>
                        <li class="breadcrumb-item active">{{ $organization->name }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Main content -->
    <div class="content">
        <div class="container-fluid">

            <!-- Organization Details Hero Card -->
            <div class="card card-outline card-navy shadow-sm mb-4">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <div class="d-flex align-items-start">
                                <div class="org-hero-icon mr-3">
                                    @if($organization->is_pharmacy)
                                        <div class="avatar-lg bg-success text-white">
                                            <i class="fas fa-pills fa-2x"></i>
                                        </div>
                                    @else
                                        <div class="avatar-lg bg-navy text-white">
                                            <i class="fas fa-hospital-alt fa-2x"></i>
                                        </div>
                                    @endif
                                </div>
                                <div>
                                    <div class="d-flex align-items-center mb-1">
                                        <h3 class="font-weight-bold text-navy mb-0 mr-2">{{ $organization->name }}</h3>
                                        @if($organization->is_pharmacy)
                                            <span class="badge badge-success px-2 py-1"><i class="fas fa-pills mr-1"></i> Pharmacy Facility</span>
                                        @else
                                            <span class="badge badge-info px-2 py-1"><i class="fas fa-clinic-medical mr-1"></i> Healthcare / Clinic</span>
                                        @endif
                                    </div>
                                    <p class="text-muted mb-2">{{ $organization->description ?? 'No description provided for this organization.' }}</p>
                                    
                                    <div class="d-flex flex-wrap text-muted small gap-3">
                                        <div class="mr-3 mb-1">
                                            <i class="fas fa-map-marker-alt text-danger mr-1"></i>
                                            <strong>{{ $organization->district->name ?? 'N/A' }}</strong> / {{ $organization->area->name ?? 'N/A' }}
                                            @if($organization->address)
                                                ({{ $organization->address }})
                                            @endif
                                        </div>
                                        @if($organization->email)
                                            <div class="mr-3 mb-1">
                                                <i class="fas fa-envelope text-muted mr-1"></i>
                                                <a href="mailto:{{ $organization->email }}">{{ $organization->email }}</a>
                                            </div>
                                        @endif
                                        @if($organization->phone)
                                            <div class="mr-3 mb-1">
                                                <i class="fas fa-phone text-muted mr-1"></i>
                                                <a href="tel:{{ $organization->phone }}">{{ $organization->phone }}</a>
                                            </div>
                                        @endif
                                        @if($organization->website)
                                            <div class="mr-3 mb-1">
                                                <i class="fas fa-globe text-muted mr-1"></i>
                                                <a href="{{ $organization->website }}" target="_blank">Visit Website</a>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4 text-md-right border-left-md pt-3 pt-md-0">
                            <div class="small text-muted mb-1">
                                Primary Owner / Contact:
                            </div>
                            <div class="font-weight-bold text-navy mb-2">
                                <i class="fas fa-user-tie text-muted mr-1"></i> {{ $organization->owner->name ?? 'System Admin' }}
                            </div>
                            <div class="small text-muted mb-3">
                                Registered: {{ $organization->created_at->format('M d, Y') }} ({{ $organization->created_at->diffForHumans() }})
                            </div>
                            <div>
                                <a href="{{ route('organizations') }}" class="btn btn-outline-secondary btn-sm shadow-sm">
                                    <i class="fas fa-building mr-1"></i> All Organizations
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Statistical Metrics Cards -->
            <div class="row">
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-info shadow-sm">
                        <div class="inner">
                            <h3>{{ $stats['staff'] }}</h3>
                            <p>Staff & Members</p>
                        </div>
                        <div class="icon"><i class="fas fa-user-md"></i></div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-purple shadow-sm">
                        <div class="inner">
                            <h3>{{ $stats['mothers'] }}</h3>
                            <p>Registered Mothers</p>
                        </div>
                        <div class="icon"><i class="fas fa-female"></i></div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    @if($organization->is_pharmacy)
                        <div class="small-box bg-success shadow-sm">
                            <div class="inner">
                                <h3>{{ $stats['ads'] }}</h3>
                                <p>Active Ad Products</p>
                            </div>
                            <div class="icon"><i class="fas fa-ad"></i></div>
                        </div>
                    @else
                        <div class="small-box bg-success shadow-sm">
                            <div class="inner">
                                <h3>{{ $stats['tips'] }}</h3>
                                <p>Healthcare Tips</p>
                            </div>
                            <div class="icon"><i class="fas fa-lightbulb"></i></div>
                        </div>
                    @endif
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-warning shadow-sm">
                        <div class="inner">
                            <h3>{{ $stats['pending'] }}</h3>
                            <p>Pending Verifications</p>
                        </div>
                        <div class="icon"><i class="fas fa-user-clock"></i></div>
                    </div>
                </div>
            </div>

            <!-- Navigation Tabs & Content Card -->
            <div class="card card-navy card-outline card-outline-tabs shadow-sm border-0">
                <div class="card-header p-0 border-bottom-0">
                    <ul class="nav nav-tabs" id="organizationTabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link {{ $activeTab === 'members' ? 'active font-weight-bold text-navy' : 'text-muted' }}" 
                               href="javascript:void(0)" 
                               wire:click="setTab('members')">
                                <i class="fas fa-users mr-1"></i> Staff & Members
                                <span class="badge badge-info ml-1">{{ $stats['staff'] }}</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ $activeTab === 'mothers' ? 'active font-weight-bold text-purple' : 'text-muted' }}" 
                               href="javascript:void(0)" 
                               wire:click="setTab('mothers')">
                                <i class="fas fa-female mr-1"></i> Registered Mothers
                                <span class="badge badge-purple ml-1">{{ $stats['mothers'] }}</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ $activeTab === 'overview' ? 'active font-weight-bold text-navy' : 'text-muted' }}" 
                               href="javascript:void(0)" 
                               wire:click="setTab('overview')">
                                <i class="fas fa-chart-line mr-1"></i> Overview & Activity
                            </a>
                        </li>
                    </ul>
                </div>

                <div class="card-body">
                    <!-- MEMBERS TAB -->
                    @if($activeTab === 'members')
                        <div>
                            <!-- Search & Filter Controls -->
                            <div class="row mb-3 align-items-center">
                                <div class="col-md-5 mb-2">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text bg-white border-right-0"><i class="fas fa-search text-muted"></i></span>
                                        </div>
                                        <input type="text" class="form-control border-left-0" wire:model.live.debounce.300ms="search" placeholder="Search members by name, email, phone...">
                                        @if(!empty($search))
                                            <div class="input-group-append">
                                                <button class="btn btn-outline-secondary" type="button" wire:click="$set('search', '')"><i class="fas fa-times"></i></button>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-2 col-sm-6 mb-2">
                                    <select wire:model.live="role_filter" class="form-control">
                                        <option value="">-- All Roles --</option>
                                        @foreach($roles as $r)
                                            <option value="{{ $r->id }}">{{ ucfirst($r->name) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2 col-sm-6 mb-2">
                                    <select wire:model.live="verification_filter" class="form-control">
                                        <option value="">-- All Statuses --</option>
                                        <option value="verified">Verified</option>
                                        <option value="pending">Pending</option>
                                        <option value="declined">Declined</option>
                                    </select>
                                </div>
                                <div class="col-md-3 mb-2 text-md-right">
                                    @if(auth()->user()->isSystemAdmin() || $organization->owner_id === auth()->id() || auth()->user()->isOrgAdmin())
                                        <button wire:click="createMember" class="btn btn-primary shadow-sm font-weight-bold">
                                            <i class="fas fa-user-plus mr-1"></i> Add Staff Member
                                        </button>
                                    @endif
                                </div>
                            </div>

                            <!-- Members Table -->
                            <div class="table-responsive">
                                <table class="table table-hover table-striped align-middle">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>#</th>
                                            <th>Member</th>
                                            <th>Contact</th>
                                            <th>Role</th>
                                            <th>Status</th>
                                            <th>Organization Verification</th>
                                            <th>Joined Date</th>
                                            <th class="text-right">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($members as $member)
                                            <tr wire:key="member-row-{{ $member->id }}">
                                                <td>{{ $loop->iteration }}</td>
                                                <td>
                                                    <div class="font-weight-bold text-navy">{{ $member->name }}</div>
                                                    <small class="text-muted">{{ $member->email }}</small>
                                                </td>
                                                <td>
                                                    {{ $member->phone ?? 'N/A' }}
                                                </td>
                                                <td>
                                                    @if($member->role)
                                                        @if($member->role->name === 'admin' || $member->role->name === 'system-admin')
                                                            <span class="badge badge-primary px-2 py-1"><i class="fas fa-shield-alt mr-1"></i> Admin</span>
                                                        @elseif($member->role->name === 'doctor')
                                                            <span class="badge badge-teal px-2 py-1"><i class="fas fa-user-md mr-1"></i> Doctor</span>
                                                        @elseif($member->role->name === 'practitioner')
                                                            <span class="badge badge-warning px-2 py-1"><i class="fas fa-user-nurse mr-1"></i> Practitioner</span>
                                                        @else
                                                            <span class="badge badge-secondary px-2 py-1">{{ ucfirst($member->role->name) }}</span>
                                                        @endif
                                                    @else
                                                        <span class="badge badge-secondary">No Role</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <span class="badge badge-{{ $member->is_active ? 'success' : 'danger' }}">
                                                        {{ $member->is_active ? 'Active' : 'Inactive' }}
                                                    </span>
                                                </td>
                                                <td>
                                                    @if($member->organization_verify === 'verified')
                                                        <span class="badge badge-success px-2 py-1"><i class="fas fa-check-circle mr-1"></i> Verified</span>
                                                    @elseif($member->organization_verify === 'pending')
                                                        <span class="badge badge-warning px-2 py-1"><i class="fas fa-hourglass-half mr-1"></i> Pending Approval</span>
                                                    @elseif($member->organization_verify === 'declined')
                                                        <span class="badge badge-danger px-2 py-1"><i class="fas fa-times-circle mr-1"></i> Declined</span>
                                                    @else
                                                        <span class="badge badge-secondary px-2 py-1">Unverified</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <small class="text-muted">{{ $member->created_at->format('M d, Y') }}</small>
                                                </td>
                                                <td class="text-right">
                                                    @if($member->organization_verify === 'pending' && (auth()->user()->isSystemAdmin() || $organization->owner_id === auth()->id()))
                                                        <button wire:click.prevent="approveUser({{ $member->id }})" class="btn btn-sm btn-success shadow-sm" title="Approve Member">
                                                            <i class="fas fa-check"></i> Approve
                                                        </button>
                                                        <button wire:click.prevent="declineUser({{ $member->id }})" class="btn btn-sm btn-outline-danger shadow-sm" title="Decline Member">
                                                            <i class="fas fa-times"></i>
                                                        </button>
                                                    @endif
                                                    <button wire:click.prevent="editMember({{ $member->id }})" class="btn btn-sm btn-outline-info shadow-sm" title="Edit Staff Member">
                                                        <i class="fas fa-edit mr-1"></i> Edit
                                                    </button>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="8" class="text-center py-5 text-muted">
                                                    <i class="fas fa-users-slash fa-3x mb-3 text-secondary"></i>
                                                    <h5>No Staff Members Found</h5>
                                                    <p class="small">No staff or practitioners match your filter criteria.</p>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            <!-- Members Pagination -->
                            <div class="d-flex justify-content-between align-items-center mt-3">
                                <div class="text-muted small">
                                    Showing {{ $members->firstItem() ?? 0 }} to {{ $members->lastItem() ?? 0 }} of {{ $members->total() }} members
                                </div>
                                <div>
                                    {{ $members->links() }}
                                </div>
                            </div>
                        </div>

                    <!-- MOTHERS TAB -->
                    @elseif($activeTab === 'mothers')
                        <div>
                            <!-- Search & Filter Controls -->
                            <div class="row mb-3">
                                <div class="col-md-6 mb-2">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text bg-white border-right-0"><i class="fas fa-search text-muted"></i></span>
                                        </div>
                                        <input type="text" class="form-control border-left-0" wire:model.live.debounce.300ms="search" placeholder="Search mothers by name, phone, address...">
                                        @if(!empty($search))
                                            <div class="input-group-append">
                                                <button class="btn btn-outline-secondary" type="button" wire:click="$set('search', '')"><i class="fas fa-times"></i></button>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Mothers Table -->
                            <div class="table-responsive">
                                <table class="table table-hover table-striped align-middle">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>#</th>
                                            <th>Mother Name</th>
                                            <th>Phone Number</th>
                                            <th>Age / DOB</th>
                                            <th>Location</th>
                                            <th>Obstetric Info</th>
                                            <th>Registered At</th>
                                            <th class="text-right">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($mothers as $mother)
                                            <tr wire:key="mother-row-{{ $mother->id }}">
                                                <td>{{ $loop->iteration }}</td>
                                                <td>
                                                    <div class="font-weight-bold text-navy">{{ $mother->name }}</div>
                                                    <small class="text-muted">{{ $mother->email }}</small>
                                                </td>
                                                <td>
                                                    <span class="badge badge-light border px-2 py-1">
                                                        <i class="fas fa-phone mr-1 text-muted"></i> {{ $mother->phone ?? 'N/A' }}
                                                    </span>
                                                </td>
                                                <td>
                                                    @if($mother->age)
                                                        <span>{{ $mother->age }} yrs</span>
                                                    @elseif($mother->date_of_birth)
                                                        <span>{{ \Carbon\Carbon::parse($mother->date_of_birth)->age }} yrs</span>
                                                    @else
                                                        <span class="text-muted">N/A</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <div>{{ $mother->district->name ?? 'N/A' }} / {{ $mother->area->name ?? 'N/A' }}</div>
                                                    @if($mother->address)
                                                        <small class="text-muted">{{ Str::limit($mother->address, 25) }}</small>
                                                    @endif
                                                </td>
                                                <td>
                                                    <small>
                                                        Deliveries: <strong>{{ $mother->deliveries ?? 0 }}</strong> | Abortions: <strong>{{ $mother->abortions ?? 0 }}</strong>
                                                    </small>
                                                </td>
                                                <td>
                                                    <small class="text-muted">{{ $mother->created_at->format('M d, Y') }}</small>
                                                </td>
                                                <td class="text-right">
                                                    <a href="{{ route('mothers.show', \App\Helper\StandardData::encrypt($mother->id)) }}" class="btn btn-sm btn-purple shadow-sm" title="View Mother Details">
                                                        <i class="fas fa-user-circle mr-1"></i> View Profile
                                                    </a>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="8" class="text-center py-5 text-muted">
                                                    <i class="fas fa-female fa-3x mb-3 text-secondary"></i>
                                                    <h5>No Registered Mothers Found</h5>
                                                    <p class="small">No mothers have been assigned to or registered under this organization yet.</p>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            <!-- Mothers Pagination -->
                            <div class="d-flex justify-content-between align-items-center mt-3">
                                <div class="text-muted small">
                                    Showing {{ $mothers->firstItem() ?? 0 }} to {{ $mothers->lastItem() ?? 0 }} of {{ $mothers->total() }} mothers
                                </div>
                                <div>
                                    {{ $mothers->links() }}
                                </div>
                            </div>
                        </div>

                    <!-- OVERVIEW / ACTIVITY TAB -->
                    @elseif($activeTab === 'overview')
                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <div class="card card-outline card-navy h-100 shadow-sm">
                                    <div class="card-header">
                                        <h4 class="card-title font-weight-bold text-navy">
                                            <i class="fas fa-info-circle mr-2"></i> Facility Summary
                                        </h4>
                                    </div>
                                    <div class="card-body p-0">
                                        <table class="table table-bordered mb-0">
                                            <tr>
                                                <th style="width: 35%;">Organization ID</th>
                                                <td>#{{ $organization->id }}</td>
                                            </tr>
                                            <tr>
                                                <th>Facility Category</th>
                                                <td>
                                                    @if($organization->is_pharmacy)
                                                        <span class="badge badge-success">Pharmacy / Commercial Partner</span>
                                                    @else
                                                        <span class="badge badge-info">Clinical / Hospital / Health Centre</span>
                                                    @endif
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>District & Area</th>
                                                <td>{{ $organization->district->name ?? 'N/A' }} &mdash; {{ $organization->area->name ?? 'N/A' }}</td>
                                            </tr>
                                            <tr>
                                                <th>Physical Address</th>
                                                <td>{{ $organization->address ?? 'Not specified' }}</td>
                                            </tr>
                                            <tr>
                                                <th>Staff Members</th>
                                                <td><strong>{{ $stats['staff'] }}</strong> ({{ $stats['doctors'] }} Doctors, {{ $stats['practitioners'] }} Practitioners, {{ $stats['admins'] }} Admins)</td>
                                            </tr>
                                            <tr>
                                                <th>Registered Mothers</th>
                                                <td><strong>{{ $stats['mothers'] }}</strong> mothers enrolled</td>
                                            </tr>
                                            <tr>
                                                <th>Registration Date</th>
                                                <td>{{ $organization->created_at->format('F d, Y \a\t h:i A') }}</td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6 mb-4">
                                @if($organization->is_pharmacy)
                                    <div class="card card-outline card-success h-100 shadow-sm">
                                        <div class="card-header">
                                            <h4 class="card-title font-weight-bold text-success">
                                                <i class="fas fa-pills mr-2"></i> Active Pharmacy Advertisements
                                            </h4>
                                        </div>
                                        <div class="card-body p-0">
                                            <ul class="list-group list-group-flush">
                                                @forelse($recentAds as $ad)
                                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                                        <div>
                                                            <div class="font-weight-bold">{{ $ad->product_name }}</div>
                                                            <small class="text-muted">{{ Str::limit($ad->ad_message, 60) }}</small>
                                                        </div>
                                                        <div class="text-right">
                                                            <span class="badge badge-info">{{ $ad->total_sent }} sent</span>
                                                            <span class="badge badge-{{ $ad->is_active ? 'success' : 'secondary' }}">
                                                                {{ $ad->is_active ? 'Active' : 'Inactive' }}
                                                            </span>
                                                        </div>
                                                    </li>
                                                @empty
                                                    <li class="list-group-item text-center py-4 text-muted">
                                                        No advertisements configured yet.
                                                    </li>
                                                @endforelse
                                            </ul>
                                        </div>
                                    </div>
                                @else
                                    <div class="card card-outline card-info h-100 shadow-sm">
                                        <div class="card-header">
                                            <h4 class="card-title font-weight-bold text-info">
                                                <i class="fas fa-heartbeat mr-2"></i> Institutional Health Tips
                                            </h4>
                                        </div>
                                        <div class="card-body p-0">
                                            <ul class="list-group list-group-flush">
                                                @forelse($recentTips as $tip)
                                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                                        <div>
                                                            <div class="font-weight-bold">{{ Str::limit($tip->tip, 70) }}</div>
                                                            <small class="text-muted">Week {{ $tip->week->week ?? 'N/A' }} | Day {{ $tip->day->day ?? 'N/A' }}</small>
                                                        </div>
                                                        <span class="badge badge-{{ $tip->status === 'approved' ? 'success' : 'warning' }}">
                                                            {{ ucfirst($tip->status ?? 'Draft') }}
                                                        </span>
                                                    </li>
                                                @empty
                                                    <li class="list-group-item text-center py-4 text-muted">
                                                        No health tips registered under this organization yet.
                                                    </li>
                                                @endforelse
                                            </ul>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>

    <!-- Create / Edit Staff Member Modal -->
    <x-modal status="{{ $memberModal }}" title="{{ $editingMemberId ? 'Edit Staff Member' : 'Add New Staff Member' }}">
        <form wire:submit.prevent="saveMember">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Full Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" wire:model="member_name" placeholder="Enter full name">
                        <x-input-error for="member_name" />
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Email Address <span class="text-danger">*</span></label>
                        <input type="email" class="form-control" wire:model="member_email" placeholder="jane@facility.com">
                        <x-input-error for="member_email" />
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Phone Number</label>
                        <input type="text" class="form-control" wire:model="member_phone" placeholder="+265 999 000 000">
                        <x-input-error for="member_phone" />
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Assigned Role <span class="text-danger">*</span></label>
                        <select wire:model="member_role_id" class="form-control">
                            <option value="">-- Select Role --</option>
                            @foreach($roles as $r)
                                <option value="{{ $r->id }}">{{ ucfirst($r->name) }}</option>
                            @endforeach
                        </select>
                        <x-input-error for="member_role_id" />
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <label class="mb-0">
                        {{ $editingMemberId ? 'Change Password (Leave blank to keep current)' : 'Password' }}
                    </label>
                    <button type="button" class="btn btn-xs btn-outline-secondary" wire:click="generateMemberPassword">
                        <i class="fas fa-key mr-1"></i> Auto-Generate
                    </button>
                </div>
                <input type="text" class="form-control" wire:model="member_password" placeholder="{{ $editingMemberId ? 'Enter new password if changing' : 'Leave blank to auto-generate' }}">
                <x-input-error for="member_password" />
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="member_is_active_switch" wire:model="member_is_active">
                            <label class="custom-control-label" for="member_is_active_switch">
                                Account is <strong>Active</strong>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="send_credentials_email_checkbox" wire:model="send_credentials_email">
                            <label class="custom-control-label" for="send_credentials_email_checkbox">
                                <i class="fas fa-envelope mr-1 text-primary"></i> Send Welcome Email with Credentials
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                <button type="button" class="btn btn-secondary mr-2" wire:click="cancelMember">Cancel</button>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save mr-1"></i> {{ $editingMemberId ? 'Update Member' : 'Save &amp; Create Member' }} <x-spinner for="saveMember" />
                </button>
            </div>
        </form>
    </x-modal>

    <!-- User Credentials Generated Modal -->
    <x-modal status="{{ $credentialsModal }}" title="User Credentials Generated">
        @if($createdUserCredentials)
            <div class="p-2">
                <div class="alert alert-success d-flex align-items-center mb-3">
                    <i class="fas fa-check-circle fa-2x mr-3"></i>
                    <div>
                        <h5 class="mb-1 font-weight-bold">Member Ready &amp; Credentials Generated!</h5>
                        <div>Account details have been prepared for <strong>{{ $createdUserCredentials['name'] }}</strong>.</div>
                    </div>
                </div>

                <div class="card bg-light border p-3 mb-3">
                    <div class="row mb-2">
                        <div class="col-sm-4 font-weight-bold text-muted">User Name:</div>
                        <div class="col-sm-8 text-navy font-weight-bold">{{ $createdUserCredentials['name'] }}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-sm-4 font-weight-bold text-muted">Organization:</div>
                        <div class="col-sm-8">{{ $createdUserCredentials['org_name'] }}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-sm-4 font-weight-bold text-muted">Role:</div>
                        <div class="col-sm-8"><span class="badge badge-primary px-2 py-1">{{ $createdUserCredentials['role'] }}</span></div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-sm-4 font-weight-bold text-muted">Login Email:</div>
                        <div class="col-sm-8"><code>{{ $createdUserCredentials['email'] }}</code></div>
                    </div>
                    <div class="row mb-0 align-items-center">
                        <div class="col-sm-4 font-weight-bold text-muted">Generated Password:</div>
                        <div class="col-sm-8">
                            <span class="badge badge-warning p-2 text-dark font-weight-bold" style="font-size: 15px; letter-spacing: 1px;">
                                {{ $createdUserCredentials['password'] }}
                            </span>
                            <small class="text-muted d-block mt-1">You can copy and securely share this password directly with the staff member.</small>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end">
                    <button type="button" class="btn btn-primary" wire:click="closeCredentialsModal">
                        <i class="fas fa-check mr-1"></i> Done
                    </button>
                </div>
            </div>
        @endif
    </x-modal>

    <style>
        .avatar-lg {
            width: 56px;
            height: 56px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .bg-purple { background-color: #6f42c1; color: #fff; }
        .badge-purple { background-color: #6f42c1; color: #fff; }
        .btn-purple { background-color: #6f42c1; color: #fff; border-color: #6f42c1; }
        .btn-purple:hover { background-color: #5a32a3; color: #fff; }
        .text-purple { color: #6f42c1 !important; }
        .bg-teal { background-color: #20c997; color: #fff; }
        .badge-teal { background-color: #20c997; color: #fff; }
        @media (min-width: 768px) {
            .border-left-md {
                border-left: 1px solid #dee2e6;
            }
        }
    </style>
</div>
