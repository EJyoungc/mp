<div>
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-bold text-navy"><i class="fas fa-hospital-alt mr-2"></i>Organizations</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Organizations</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Main content -->
    <div class="content">
        <div class="container-fluid">

            <!-- Stats Overview Cards -->
            <div class="row">
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-navy shadow-sm">
                        <div class="inner">
                            <h3>{{ $stats['total'] }}</h3>
                            <p>Total Organizations</p>
                        </div>
                        <div class="icon"><i class="fas fa-building"></i></div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-info shadow-sm">
                        <div class="inner">
                            <h3>{{ $stats['clinics'] }}</h3>
                            <p>Clinics & Facilities</p>
                        </div>
                        <div class="icon"><i class="fas fa-clinic-medical"></i></div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-success shadow-sm">
                        <div class="inner">
                            <h3>{{ $stats['pharmacies'] }}</h3>
                            <p>Pharmacies</p>
                        </div>
                        <div class="icon"><i class="fas fa-pills"></i></div>
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
            </div>

            <!-- Organizations Directory Card -->
            <div class="card shadow-sm border-0">
                <div class="card-header bg-gradient-navy d-flex justify-content-between align-items-center">
                    <h3 class="card-title text-white mb-0">
                        <i class="fas fa-list-alt mr-2"></i> Organizations Directory
                    </h3>
                    <div class="card-tools ml-auto">
                        @if(auth()->user()->isSystemAdmin())
                            <button wire:click.prevent="create" class="btn btn-light btn-sm shadow-sm font-weight-bold">
                                <i class="fas fa-plus mr-1 text-primary"></i> Add Organization
                            </button>
                        @endif
                    </div>
                </div>

                <div class="card-body">
                    <!-- Search & Filter Controls -->
                    <div class="row mb-3">
                        <div class="col-md-5 mb-2">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text bg-white border-right-0"><i class="fas fa-search text-muted"></i></span>
                                </div>
                                <input type="text" class="form-control border-left-0" wire:model.live.debounce.300ms="search" placeholder="Search by name, contact, district, area...">
                                @if(!empty($search))
                                    <div class="input-group-append">
                                        <button class="btn btn-outline-secondary" type="button" wire:click="$set('search', '')"><i class="fas fa-times"></i></button>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6 mb-2">
                            <select wire:model.live="type_filter" class="form-control">
                                <option value="">-- All Types --</option>
                                <option value="clinic">Clinics / Healthcare</option>
                                <option value="pharmacy">Pharmacies</option>
                            </select>
                        </div>
                        <div class="col-md-3 col-sm-6 mb-2">
                            <select wire:model.live="district_filter" class="form-control">
                                <option value="">-- All Districts --</option>
                                @foreach($districts as $d)
                                    <option value="{{ $d->id }}">{{ $d->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-1 mb-2">
                            @if(!empty($search) || !empty($type_filter) || !empty($district_filter))
                                <button wire:click="$set('search', ''); $set('type_filter', ''); $set('district_filter', '');" class="btn btn-secondary btn-block" title="Reset Filters">
                                    <i class="fas fa-undo"></i>
                                </button>
                            @endif
                        </div>
                    </div>

                    <!-- Organizations Table -->
                    <div class="table-responsive">
                        <table class="table table-hover table-striped align-middle">
                            <thead class="thead-light">
                                <tr>
                                    <th style="width: 50px;">#</th>
                                    <th>Organization</th>
                                    <th>Type</th>
                                    <th>Location</th>
                                    <th>Contact</th>
                                    <th>Members</th>
                                    <th>Mothers</th>
                                    <th class="text-right" style="min-width: 200px;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($orgs as $item)
                                    <tr wire:key="org-row-{{ $item->id }}">
                                        <td><strong>{{ $item->id }}</strong></td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="mr-2">
                                                    @if($item->is_pharmacy)
                                                        <span class="avatar-icon bg-success-light text-success"><i class="fas fa-pills"></i></span>
                                                    @else
                                                        <span class="avatar-icon bg-info-light text-info"><i class="fas fa-clinic-medical"></i></span>
                                                    @endif
                                                </div>
                                                <div>
                                                    <a href="{{ route('organizations.profile', $item->id) }}" class="text-bold text-navy text-decoration-none">
                                                        {{ $item->name }}
                                                    </a>
                                                    @if($item->owner)
                                                        <div class="small text-muted">Owner: {{ $item->owner->name }}</div>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            @if($item->is_pharmacy)
                                                <span class="badge badge-success px-2 py-1"><i class="fas fa-pills mr-1"></i> Pharmacy</span>
                                            @else
                                                <span class="badge badge-info px-2 py-1"><i class="fas fa-hospital mr-1"></i> Clinic / Hospital</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div><i class="fas fa-map-marker-alt text-danger mr-1"></i>{{ $item->district->name ?? 'N/A' }} / {{ $item->area->name ?? 'N/A' }}</div>
                                            @if($item->address)
                                                <small class="text-muted text-capitalize">{{ Str::limit($item->address, 30) }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            <div>
                                                @if($item->email)
                                                    <div><i class="fas fa-envelope text-muted mr-1"></i> <small>{{ $item->email }}</small></div>
                                                @endif
                                                @if($item->phone)
                                                    <div><i class="fas fa-phone text-muted mr-1"></i> <small>{{ $item->phone }}</small></div>
                                                @endif
                                                @if($item->website)
                                                    <div><i class="fas fa-globe text-muted mr-1"></i> <a href="{{ $item->website }}" target="_blank"><small>Website</small></a></div>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge badge-pill badge-primary px-2 py-1">
                                                <i class="fas fa-user-md mr-1"></i> {{ $item->staff_count ?? 0 }} Staff
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge badge-pill badge-purple px-2 py-1">
                                                <i class="fas fa-female mr-1"></i> {{ $item->mothers_count ?? 0 }} Mothers
                                            </span>
                                        </td>
                                        <td class="text-right">
                                            <div class="btn-group btn-group-sm">
                                                <!-- Open Organization Profile Dashboard -->
                                                <a href="{{ route('organizations.profile', $item->id) }}" class="btn btn-info shadow-sm" title="View Organization Dashboard & Profile">
                                                    <i class="fas fa-tachometer-alt mr-1"></i> Profile
                                                </a>
                                                <button wire:click.prevent="create({{ $item->id }})" class="btn btn-warning shadow-sm" title="Edit Organization">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button wire:click.prevent="confirmDelete({{ $item->id }})" class="btn btn-danger shadow-sm" title="Delete Organization">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-5 text-muted">
                                            <i class="fas fa-hospital fa-3x mb-3 text-secondary"></i>
                                            <h5>No Organizations Found</h5>
                                            <p class="small">Try adjusting your search criteria or add a new organization.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <div class="text-muted small">
                            Showing {{ $orgs->firstItem() ?? 0 }} to {{ $orgs->lastItem() ?? 0 }} of {{ $orgs->total() }} organizations
                        </div>
                        <div>
                            {{ $orgs->links() }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Create / Edit Organization Modal -->
            <x-modal status="{{ $modal }}" title="{{ empty($organization->id) ? 'Add New Organization' : 'Edit Organization' }}">
                <form wire:submit.prevent="store">
                    <div class="form-group">
                        <label for="name">Organization Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" wire:model="name" placeholder="e.g. Lilongwe Central Clinic">
                        <x-input-error for="name" />
                    </div>

                    <div class="form-group">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="is_pharmacy_switch" wire:model="is_pharmacy">
                            <label class="custom-control-label" for="is_pharmacy_switch">
                                This organization is a <strong>Pharmacy</strong> (Advertisements & Products)
                            </label>
                        </div>
                        <small class="form-text text-muted">Check this if the facility sells medicines/products and should not access clinical maternal charts.</small>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="email">Email Address</label>
                                <input type="email" class="form-control" wire:model="email" placeholder="contact@organization.com">
                                <x-input-error for="email" />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="phone">Phone Number</label>
                                <input type="text" class="form-control" wire:model="phone" placeholder="+265 999 000 000">
                                <x-input-error for="phone" />
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="district_id">District <span class="text-danger">*</span></label>
                                <select wire:model.live="district_id" class="form-control">
                                    <option value="">Select District</option>
                                    @foreach($districts as $district)
                                        <option value="{{ $district->id }}">{{ $district->name }}</option>
                                    @endforeach
                                </select>
                                <x-input-error for="district_id" />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="area_id">Area <span class="text-danger">*</span></label>
                                <select wire:model="area_id" class="form-control" @disabled(!$district_id)>
                                    <option value="">Select Area</option>
                                    @foreach($areas as $area)
                                        <option value="{{ $area->id }}">{{ $area->name }}</option>
                                    @endforeach
                                </select>
                                <x-input-error for="area_id" />
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="address">Physical Address</label>
                        <input type="text" class="form-control" wire:model="address" placeholder="Physical location or street address">
                        <x-input-error for="address" />
                    </div>

                    <div class="form-group">
                        <label for="website">Website URL</label>
                        <input type="text" class="form-control" wire:model="website" placeholder="https://www.example.com">
                        <x-input-error for="website" />
                    </div>

                    <div class="form-group">
                        <label for="description">Description / Notes</label>
                        <textarea class="form-control" rows="3" wire:model="description" placeholder="Brief summary of this facility..."></textarea>
                        <x-input-error for="description" />
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <button type="button" class="btn btn-secondary mr-2" wire:click="cancel">Cancel</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save mr-1"></i> Save Organization <x-spinner for="store" />
                        </button>
                    </div>
                </form>
            </x-modal>

            <!-- Delete Confirmation Modal -->
            <x-modal status="{{ $deleteModal }}" title="Confirm Organization Deletion">
                <div class="p-2">
                    <div class="alert alert-danger d-flex align-items-center mb-3">
                        <i class="fas fa-exclamation-triangle fa-2x mr-3"></i>
                        <div>
                            <h5 class="mb-1 font-weight-bold">Warning: Permanent Cascading Deletion</h5>
                            <div>You are about to delete <strong>{{ $confirmingDeleteName }}</strong>.</div>
                        </div>
                    </div>

                    <p class="text-muted">
                        This action <strong>cannot be undone</strong>. When confirmed, this organization and <strong>all related records across the database</strong> will be permanently deleted:
                    </p>
                    <ul class="text-danger small font-weight-bold">
                        <li>All registered staff members & practitioners attached to this organization</li>
                        <li>All registered mothers attached to this organization</li>
                        <li>All clinical history, session notes, and medical records</li>
                        <li>All sent SMS logs, message histories, and ad outreach records</li>
                        <li>All tips and advertisements created under this organization</li>
                        <li>All pending invitations and team affiliations</li>
                    </ul>

                    <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                        <button type="button" class="btn btn-secondary mr-2" wire:click="cancel">
                            <i class="fas fa-times mr-1"></i> Cancel
                        </button>
                        <button type="button" class="btn btn-danger font-weight-bold" wire:click="deleteConfirmed">
                            <i class="fas fa-trash-alt mr-1"></i> Yes, Delete Permanently <x-spinner for="deleteConfirmed" />
                        </button>
                    </div>
                </div>
            </x-modal>

        </div>
    </div>

    <style>
        .avatar-icon {
            width: 36px;
            height: 36px;
            border-radius: 8px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
        }
        .bg-info-light { background-color: #e3f2fd; }
        .bg-success-light { background-color: #e8f5e9; }
        .bg-purple { background-color: #6f42c1; color: #fff; }
        .badge-purple { background-color: #6f42c1; color: #fff; }
    </style>
</div>
