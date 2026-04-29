<div>
    {{-- Because she competes with no one, no one can compete with her. --}}

    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Dashboard</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        {{-- <li class="breadcrumb-item"><a href="#">Home</a></li> --}}
                        <li class="breadcrumb-item active">Dashboard</li>
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->
    <!-- Main content -->
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <!-- Mothers Count -->
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3>{{ $mothers->count() }}</h3>
                            <p>Mothers</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-female"></i>
                        </div>
                    </div>
                </div>

                @if(auth()->user()->isSystemAdmin())
                <!-- Organizations (Super Admin Only) -->
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-primary">
                        <div class="inner">
                            <h3>{{ $analytics['organizations'] }}</h3>
                            <p>Organizations</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-hospital"></i>
                        </div>
                    </div>
                </div>
                @else
                <!-- Doctors (Org Admin/Doctor) -->
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-primary">
                        <div class="inner">
                            <h3>{{ $analytics['doctors'] }}</h3>
                            <p>Doctors</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-user-md"></i>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Tips/Pending Approval -->
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3>{{ $tips->count() }}</h3>
                            <p>Approved Tips @if($pendingTipsCount > 0) <span class="badge badge-warning" title="Pending Approval">{{ $pendingTipsCount }} Pending</span> @endif</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-lightbulb"></i>
                        </div>
                    </div>
                </div>

                <!-- Delivery Rate -->
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-{{ $analytics['delivery_rate'] > 90 ? 'dark' : 'warning' }}">
                        <div class="inner">
                            <h3>{{ $analytics['delivery_rate'] }}%</h3>
                            <p>SMS Delivery Rate</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-paper-plane"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="d-flex py-1 justify-content-end">
                        <div class="btn-group">
                            @if(!(auth()->user()->organization && auth()->user()->organization->is_pharmacy))
                                <button class="btn btn-outline-dark" wire:click.prevent="addMothers">Add Mothers <i
                                        class="fas fa-plus"></i> <x-spinner for="addMothers" /></button>
                                <button wire:click.prevent="export" class="btn btn-outline-dark">Download Excel <i
                                        class="fas fa-file-excel"></i> <x-spinner for="export" /> </button>
                            @endif
                            <x-modal title="Add Mothers" :status="$modal">

                                <div>
                                    @if (session()->has('message'))
                                        <div class="alert alert-success mt-2">
                                            {{ session('message') }}
                                        </div>
                                    @endif

                                    <div class="mb-3">
                                        <input type="file" wire:model="file" wire:loading.attr="disabled"
                                            class="form-control">
                                        @error('file')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <button wire:click="preview" wire:loading.attr='disabled'
                                        class="btn btn-secondary mb-3">Preview Data <x-spinner
                                            for="file" /></button>

                                    @if (count($previewData))
                                        <div class="mt-4">
                                            <h4>Data Preview</h4>
                                            <div class="table-responsive">
                                                <table class="table table-hover table-bordered">
                                                    @if (count($previewTitleData))
                                                        <thead class=" thead-dark">
                                                            <tr>
                                                                @foreach ($previewTitleData[0] as $header)
                                                                    <th>{{ $header }}</th>
                                                                @endforeach
                                                            </tr>
                                                        </thead>
                                                    @endif
                                                    <tbody>
                                                        @foreach ($previewData as $row)
                                                            <tr>
                                                                @foreach ($row as $key => $cell)
                                                                    <td>{{ $key == 2 || $key == 3 ? $this->convertDate($cell) : $cell }}
                                                                    </td>
                                                                @endforeach
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                            <button wire:click="confirmImport" class="btn btn-dark">Confirm
                                                Import <x-spinner for="confirmImport" /></button>
                                        </div>
                                    @endif
                                </div>

                            </x-modal>
                        </div>
                    </div>
                    @if(!(auth()->user()->organization && auth()->user()->organization->is_pharmacy))
                    <div class="card">

                        <div class="card-body p-0">
                            <h2 class="py-2 px-2">Mothers</h2>
                            <div class="table-responsive">
                                <table class="table table-hover table-inverse ">
                                    <thead class="thead-inverse">
                                        <tr>
                                            <th>#</th>
                                            <th>Name</th>
                                            <th>Organization</th>
                                            <th>Role</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                @forelse($mothers as $index => $item)
                                            <tr>
                                                <td scope="row">{{ $index + 1 }}</td>
                                                <td class="text-capitalize">{{ $item->name }}</td>
                                                <td>{{ empty($item->organization->name) ? "UNKNOWN" : $item->organization->name }}</td>
                                                <td> <span
                                                        class="badge bg-info text-capitalize">{{ $item->role->name ?? 'N/A' }}</span>
                                                </td>
                                                <td>

                                                    <div class="dropdown open">
                                                        <a class="btn btn-dark dropdown-toggle" type="button"
                                                            id="triggerId" data-toggle="dropdown" aria-haspopup="true"
                                                            aria-expanded="false">
                                                            Options
                                                        </a>
                                                        <div class="dropdown-menu" aria-labelledby="triggerId">
                                                            <a class="dropdown-item"
                                                                href="{{ route('mothers.show', SD::encrypt($item->id)) }}">View</a>
                                                            <a class="dropdown-item " href="#">Edit</a>

                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center" scope="row">EMPTY</td>

                                            </tr>
                                        @endforelse


                                    </tbody>
                                </table>
                            </div>

                        </div>
                    </div>
                    @endif
                </div>

                @php
                    $isPharmacyOwner = auth()->user()->organization && auth()->user()->organization->is_pharmacy && auth()->user()->organization->owner_id == auth()->id();
                    $isOrgOwner = \App\Models\Organization::where('owner_id', auth()->id())->exists();
                @endphp

                @if (Auth::user()->isSystemAdmin() || $isPharmacyOwner)
                    <!-- Pharmacy Ads Section (Monetization) -->
                    <div class="col-12 mt-4">
                        <div class="card">
                            <div class="card-header bg-gradient-navy">
                                <h3 class="card-title"><i class="fas fa-pills mr-2"></i> Pharmacy Advertisements {{ auth()->user()->isSystemAdmin() ? '(Monetization)' : '' }}</h3>
                                <div class="card-tools">
                                    <button class="btn btn-sm btn-light" wire:click="createAd">
                                        <i class="fas fa-plus mr-1"></i> New Ad
                                    </button>
                                </div>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-hover table-striped">
                                        <thead>
                                            <tr>
                                                <th>Product</th>
                                                <th>Message</th>
                                                <th>Target</th>
                                                <th>Total Sent</th>
                                                <th>Status</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($ads as $ad)
                                                <tr>
                                                    <td><strong>{{ $ad->product_name }}</strong></td>
                                                    <td>{{ Str::limit($ad->ad_message, 50) }}</td>
                                                    <td>
                                                        @if($ad->trimester_id)
                                                            <span class="badge badge-primary">Trimester {{ $ad->trimester->trimester ?? $ad->trimester_id }}</span>
                                                        @elseif($ad->target_week_start || $ad->target_week_end)
                                                            Week {{ $ad->target_week_start }} - {{ $ad->target_week_end }}
                                                        @else
                                                            <span class="text-muted">All Stages</span>
                                                        @endif
                                                    </td>
                                                    <td><span class="badge badge-info">{{ $ad->total_sent }}</span></td>
                                                    <td>
                                                        <span class="badge badge-{{ $ad->is_active ? 'success' : 'danger' }}">
                                                            {{ $ad->is_active ? 'Active' : 'Inactive' }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <div class="btn-group">
                                                            <button class="btn btn-sm btn-outline-info" wire:click="editAd({{ $ad->id }})">
                                                                <i class="fas fa-edit"></i>
                                                            </button>
                                                            <button class="btn btn-sm btn-outline-{{ $ad->is_active ? 'danger' : 'success' }}" 
                                                                wire:click="toggleAd({{ $ad->id }})">
                                                                {{ $ad->is_active ? 'Deactivate' : 'Activate' }}
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="7" class="text-center text-muted">No advertisements created yet.</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Ad Creation/Edit Modal -->
                        <x-modal title="{{ $ad_id ? 'Edit' : 'Create' }} Pharmacy Advertisement" status="{{ $adModal }}">
                            <form wire:submit.prevent="storeAd">
                                <div class="form-group">
                                    <label>Product Name</label>
                                    <input type="text" class="form-control" wire:model="product_name" placeholder="e.g. Folic Acid Plus">
                                    <x-input-error for="product_name" />
                                </div>
                                <div class="form-group">
                                    <label>SMS Content</label>
                                    <textarea class="form-control" wire:model="ad_message" rows="3" placeholder="Enter the ad message mothers will receive..."></textarea>
                                    <small class="text-muted">Keep it under 160 characters for a single SMS.</small>
                                    <x-input-error for="ad_message" />
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label>Target Trimester (Optional)</label>
                                            <select class="form-control" wire:model="trimester_id">
                                                <option value="">-- No Trimester (Use Weeks) --</option>
                                                @foreach($trimesters as $t)
                                                    <option value="{{ $t->id }}">Trimester {{ $t->trimester }}</option>
                                                @endforeach
                                            </select>
                                            <x-input-error for="trimester_id" />
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-6">
                                        <div class="form-group">
                                            <label>Start Week</label>
                                            <input type="number" class="form-control" wire:model="target_week_start" placeholder="e.g. 1">
                                            <x-input-error for="target_week_start" />
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-group">
                                            <label>End Week</label>
                                            <input type="number" class="form-control" wire:model="target_week_end">
                                            <x-input-error for="target_week_end" />
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer px-0 pb-0">
                                    <button type="button" class="btn btn-secondary" wire:click="cancel">Cancel</button>
                                    <button type="submit" class="btn btn-primary">{{ $ad_id ? 'Update' : 'Save' }} Advertisement</button>
                                </div>
                            </form>
                        </x-modal>
                    </div>
                @endif

                @if($isPharmacyOwner || auth()->user()->isSystemAdmin())
                    <!-- Ad Analytics (Recent Reaches) -->
                    <div class="col-12 mt-4">
                        <div class="card">
                            <div class="card-header bg-gradient-success">
                                <h3 class="card-title"><i class="fas fa-map-marker-alt mr-2"></i> Recent Ad Outreach (Reached Locations)</h3>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-hover table-striped">
                                        <thead>
                                            <tr>
                                                <th>Ad Product</th>
                                                <th>Mother's Area</th>
                                                <th>Sent At</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                                $recentReaches = \App\Models\AdHistory::with(['mother.area', 'pharmacyAd']);
                                                
                                                if (!auth()->user()->isSystemAdmin()) {
                                                    $recentReaches->where('organization_id', auth()->user()->organization_id);
                                                }

                                                $recentReaches = $recentReaches->latest()
                                                    ->limit(10)
                                                    ->get();
                                            @endphp

                                            @forelse($recentReaches as $reach)
                                                <tr>
                                                    <td>{{ $reach->pharmacyAd->product_name ?? 'Ad #' . ($reach->pharmacy_ad_id ?? 'Unknown') }}</td>
                                                    <td>
                                                        {{ $reach->mother->area->name ?? ($reach->mother->address ?? 'Unknown Location') }}
                                                    </td>
                                                    <td>{{ $reach->created_at->diffForHumans() }}</td>
                                                    <td>
                                                        <span class="badge badge-{{ $reach->status === 'sent' ? 'success' : 'danger' }}">
                                                            {{ ucfirst($reach->status) }}
                                                        </span>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="4" class="text-center text-muted p-4">No ad outreach data available yet.</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                @if (Auth::user()->isSystemAdmin() || $isOrgOwner)
                    <div class="col-12 mt-4">
                        <div class="card">
                            <div class="card-header bg-warning">
                                <h3 class="card-title text-dark"><i class="fas fa-id-card-alt mr-2"></i> Organization Membership Requests</h3>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                                    <table class="table table-hover table-striped">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Name</th>
                                                <th>Email</th>
                                                <th>Organization</th>
                                                <th>Status</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($requests as $index => $item)
                                                <tr>
                                                    <td scope="row">{{ $index + 1 }}</td>
                                                    <td>{{ $item->name }}</td>
                                                    <td>{{ $item->email }}</td>
                                                    <td class="text-bold">
                                                        {{ $item->organization->name ?? 'N/A' }}
                                                    </td>
                                                    <td> 
                                                        <span class="text-capitalize badge bg-warning">
                                                            {{ $item->organization_verify }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <button class="btn btn-success btn-sm" wire:click.prevent="approve({{ $item->id }})">
                                                            Approve
                                                        </button>
                                                        <button class="btn btn-danger btn-sm" wire:click.prevent="decline({{ $item->id }})">
                                                            Decline
                                                        </button>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="6" class="text-center text-muted p-4">No pending membership requests.</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif


            </div>
        </div>
    </div>
    <!-- /.content -->


</div>
