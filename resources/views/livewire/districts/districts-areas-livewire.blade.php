<div>
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2 align-items-center">
                <div class="col-sm-6">
                    <h1 class="m-0 text-bold text-navy">
                        <i class="fas fa-map-marked-alt mr-2"></i> Districts & Areas Management
                    </h1>
                    <small class="text-muted">Configure hierarchical geographic locations for Malawi healthcare clinics, pharmacies, and mothers</small>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Districts & Areas</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="content">
        <div class="container-fluid">

            <!-- Summary Statistic Cards -->
            <div class="row">
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-navy shadow-sm">
                        <div class="inner">
                            <h3>{{ number_format($totalDistricts) }}</h3>
                            <p>Total Districts</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-map"></i>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-info shadow-sm">
                        <div class="inner">
                            <h3>{{ number_format($totalAreas) }}</h3>
                            <p>Total Areas</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-map-pin"></i>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-success shadow-sm">
                        <div class="inner">
                            <h3>{{ number_format($totalOrganizationsMapped) }}</h3>
                            <p>Organizations Mapped</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-hospital-alt"></i>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-purple shadow-sm">
                        <div class="inner">
                            <h3>{{ number_format($totalUsersMapped) }}</h3>
                            <p>Users / Mothers Mapped</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-users"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Management Card with Tabs -->
            <div class="card card-navy card-outline card-outline-tabs shadow-sm border-0">
                <div class="card-header p-0 border-bottom-0">
                    <ul class="nav nav-tabs" id="geoTabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link {{ $activeTab === 'districts' ? 'active font-weight-bold text-navy' : 'text-muted' }}" 
                               href="javascript:void(0)" 
                               wire:click="setTab('districts')">
                                <i class="fas fa-map mr-1"></i> Districts 
                                <span class="badge badge-navy ml-1">{{ $totalDistricts }}</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ $activeTab === 'areas' ? 'active font-weight-bold text-navy' : 'text-muted' }}" 
                               href="javascript:void(0)" 
                               wire:click="setTab('areas')">
                                <i class="fas fa-map-pin mr-1"></i> Areas / Sub-localities 
                                <span class="badge badge-info ml-1">{{ $totalAreas }}</span>
                            </a>
                        </li>
                    </ul>
                </div>

                <div class="card-body">
                    <!-- ============================================== -->
                    <!-- TAB 1: DISTRICTS MANAGEMENT -->
                    <!-- ============================================== -->
                    @if($activeTab === 'districts')
                        <div>
                            <!-- Search & Action Toolbar -->
                            <div class="row mb-3 align-items-center">
                                <div class="col-md-6 mb-2">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text bg-white border-right-0">
                                                <i class="fas fa-search text-muted"></i>
                                            </span>
                                        </div>
                                        <input type="text" class="form-control border-left-0" 
                                               wire:model.live.debounce.300ms="districtSearch" 
                                               placeholder="Search districts by name...">
                                        @if(!empty($districtSearch))
                                            <div class="input-group-append">
                                                <button class="btn btn-outline-secondary" type="button" wire:click="$set('districtSearch', '')">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-6 mb-2 text-md-right">
                                    <button class="btn btn-primary shadow-sm font-weight-bold" wire:click="createDistrict">
                                        <i class="fas fa-plus-circle mr-1"></i> Add New District
                                    </button>
                                </div>
                            </div>

                            <!-- Districts Table -->
                            <div class="table-responsive">
                                <table class="table table-hover table-striped align-middle">
                                    <thead class="thead-light">
                                        <tr>
                                            <th style="width: 50px;">#</th>
                                            <th>District Name</th>
                                            <th>Areas Count</th>
                                            <th>Organizations</th>
                                            <th>Registered Users / Mothers</th>
                                            <th>Created At</th>
                                            <th class="text-right" style="min-width: 220px;">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($districts as $district)
                                            <tr wire:key="district-row-{{ $district->id }}">
                                                <td>{{ $loop->iteration + ($districts->currentPage() - 1) * $districts->perPage() }}</td>
                                                <td>
                                                    <div class="font-weight-bold text-navy">
                                                        <i class="fas fa-map-marker-alt text-danger mr-1"></i> {{ $district->name }}
                                                    </div>
                                                </td>
                                                <td>
                                                    <button class="btn btn-sm btn-outline-info" 
                                                            wire:click="viewAreasForDistrict({{ $district->id }})" 
                                                            title="View all areas in this district">
                                                        <i class="fas fa-map-pin mr-1"></i> {{ $district->areas_count }} {{ Str::plural('Area', $district->areas_count) }}
                                                    </button>
                                                </td>
                                                <td>
                                                    <span class="badge badge-light border px-2 py-1">
                                                        <i class="fas fa-hospital-alt text-muted mr-1"></i> {{ $district->organizations_count }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="badge badge-light border px-2 py-1">
                                                        <i class="fas fa-users text-muted mr-1"></i> {{ $district->users_count }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <small class="text-muted">{{ $district->created_at ? $district->created_at->format('M d, Y') : 'N/A' }}</small>
                                                </td>
                                                <td class="text-right">
                                                    <button class="btn btn-sm btn-outline-success shadow-sm mr-1" 
                                                            wire:click="createArea({{ $district->id }})" 
                                                            title="Quick Add Area to this District">
                                                        <i class="fas fa-plus mr-1"></i> Add Area
                                                    </button>
                                                    <button class="btn btn-sm btn-outline-primary shadow-sm mr-1" 
                                                            wire:click="editDistrict({{ $district->id }})" 
                                                            title="Edit District">
                                                        <i class="fas fa-edit"></i> Edit
                                                    </button>
                                                    <button class="btn btn-sm btn-outline-danger shadow-sm" 
                                                            wire:click="confirmDeleteDistrict({{ $district->id }})" 
                                                            title="Delete District">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="7" class="text-center py-5 text-muted">
                                                    <i class="fas fa-map-slash fa-3x mb-3 text-secondary"></i>
                                                    <h5>No Districts Found</h5>
                                                    <p class="small">No districts match your search criteria. Click "Add New District" above to add one.</p>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            <!-- Districts Pagination -->
                            <div class="mt-3">
                                {{ $districts->links() }}
                            </div>
                        </div>

                    <!-- ============================================== -->
                    <!-- TAB 2: AREAS MANAGEMENT -->
                    <!-- ============================================== -->
                    @elseif($activeTab === 'areas')
                        <div>
                            <!-- Search, District Filter & Action Toolbar -->
                            <div class="row mb-3 align-items-center">
                                <div class="col-md-4 mb-2">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text bg-white border-right-0">
                                                <i class="fas fa-search text-muted"></i>
                                            </span>
                                        </div>
                                        <input type="text" class="form-control border-left-0" 
                                               wire:model.live.debounce.300ms="areaSearch" 
                                               placeholder="Search areas by name...">
                                        @if(!empty($areaSearch))
                                            <div class="input-group-append">
                                                <button class="btn btn-outline-secondary" type="button" wire:click="$set('areaSearch', '')">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-4 mb-2">
                                    <select class="form-control" wire:model.live="areaDistrictFilter">
                                        <option value="">-- All Districts (Filter by District) --</option>
                                        @foreach($allDistricts as $d)
                                            <option value="{{ $d->id }}">{{ $d->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4 mb-2 text-md-right">
                                    <button class="btn btn-primary shadow-sm font-weight-bold" wire:click="createArea">
                                        <i class="fas fa-plus-circle mr-1"></i> Add New Area
                                    </button>
                                </div>
                            </div>

                            <!-- Active Filter Alert -->
                            @if(!empty($areaDistrictFilter))
                                @php
                                    $activeFilterDistrict = $allDistricts->firstWhere('id', (int) $areaDistrictFilter);
                                @endphp
                                <div class="alert alert-light border d-flex justify-content-between align-items-center py-2 px-3 mb-3">
                                    <span>
                                        <i class="fas fa-filter text-primary mr-1"></i> 
                                        Filtered by District: <strong>{{ $activeFilterDistrict->name ?? 'Unknown' }}</strong>
                                    </span>
                                    <button class="btn btn-sm btn-link text-danger p-0 font-weight-bold" wire:click="$set('areaDistrictFilter', '')">
                                        <i class="fas fa-times mr-1"></i> Clear Filter
                                    </button>
                                </div>
                            @endif

                            <!-- Areas Table -->
                            <div class="table-responsive">
                                <table class="table table-hover table-striped align-middle">
                                    <thead class="thead-light">
                                        <tr>
                                            <th style="width: 50px;">#</th>
                                            <th>Area Name</th>
                                            <th>District</th>
                                            <th>Organizations</th>
                                            <th>Registered Users / Mothers</th>
                                            <th>Created At</th>
                                            <th class="text-right" style="min-width: 150px;">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($areas as $area)
                                            <tr wire:key="area-row-{{ $area->id }}">
                                                <td>{{ $loop->iteration + ($areas->currentPage() - 1) * $areas->perPage() }}</td>
                                                <td>
                                                    <div class="font-weight-bold text-navy">
                                                        <i class="fas fa-map-pin text-info mr-1"></i> {{ $area->name }}
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="badge badge-info px-2 py-1">
                                                        <i class="fas fa-map-marker-alt mr-1"></i> {{ $area->district->name ?? 'Unassigned' }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="badge badge-light border px-2 py-1">
                                                        <i class="fas fa-hospital-alt text-muted mr-1"></i> {{ $area->organizations_count }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="badge badge-light border px-2 py-1">
                                                        <i class="fas fa-users text-muted mr-1"></i> {{ $area->users_count }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <small class="text-muted">{{ $area->created_at ? $area->created_at->format('M d, Y') : 'N/A' }}</small>
                                                </td>
                                                <td class="text-right">
                                                    <button class="btn btn-sm btn-outline-primary shadow-sm mr-1" 
                                                            wire:click="editArea({{ $area->id }})" 
                                                            title="Edit Area">
                                                        <i class="fas fa-edit"></i> Edit
                                                    </button>
                                                    <button class="btn btn-sm btn-outline-danger shadow-sm" 
                                                            wire:click="confirmDeleteArea({{ $area->id }})" 
                                                            title="Delete Area">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="7" class="text-center py-5 text-muted">
                                                    <i class="fas fa-map-pin fa-3x mb-3 text-secondary"></i>
                                                    <h5>No Areas Found</h5>
                                                    <p class="small">No areas match your search and filter criteria. Click "Add New Area" above to add one.</p>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            <!-- Areas Pagination -->
                            <div class="mt-3">
                                {{ $areas->links() }}
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

    <!-- Create / Edit District Modal -->
    <x-modal status="{{ $districtModal }}" title="{{ $editingDistrictId ? 'Edit District' : 'Add New District' }}">
        <form wire:submit.prevent="saveDistrict">
            <div class="form-group">
                <label>District Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control" wire:model="district_name" placeholder="e.g. Lilongwe, Blantyre, Zomba">
                <x-input-error for="district_name" />
            </div>

            <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                <button type="button" class="btn btn-secondary mr-2" wire:click="cancelDistrict">
                    <i class="fas fa-times mr-1"></i> Cancel
                </button>
                <button type="submit" class="btn btn-primary font-weight-bold">
                    <i class="fas fa-save mr-1"></i> {{ $editingDistrictId ? 'Update District' : 'Create District' }}
                    <x-spinner for="saveDistrict" />
                </button>
            </div>
        </form>
    </x-modal>

    <!-- Confirm Delete District Modal -->
    <x-modal status="{{ $confirmDeleteDistrictModal }}" title="Confirm District Deletion">
        @if($districtToDelete)
            <div class="p-2">
                <div class="alert alert-danger d-flex align-items-center mb-3">
                    <i class="fas fa-exclamation-triangle fa-2x mr-3"></i>
                    <div>
                        <h5 class="mb-1 font-weight-bold">Warning: Deleting District</h5>
                        <div>You are about to delete <strong>{{ $districtToDelete['name'] }}</strong>.</div>
                    </div>
                </div>

                <div class="card bg-light border-0 mb-3">
                    <div class="card-body py-2">
                        <div class="row text-center">
                            <div class="col-4 border-right">
                                <div class="text-muted small">Associated Areas</div>
                                <div class="font-weight-bold text-navy h5 mb-0">{{ $districtToDelete['areas_count'] }}</div>
                            </div>
                            <div class="col-4 border-right">
                                <div class="text-muted small">Organizations</div>
                                <div class="font-weight-bold text-navy h5 mb-0">{{ $districtToDelete['organizations_count'] }}</div>
                            </div>
                            <div class="col-4">
                                <div class="text-muted small">Mothers / Users</div>
                                <div class="font-weight-bold text-navy h5 mb-0">{{ $districtToDelete['users_count'] }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <p class="text-muted small">
                    <i class="fas fa-info-circle text-info mr-1"></i> 
                    Deleting this district will cascade delete all its <strong>{{ $districtToDelete['areas_count'] }}</strong> associated areas. 
                    Any organizations or users mapped to this district will have their district field reset to null.
                </p>

                <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                    <button type="button" class="btn btn-secondary mr-2" wire:click="cancelDeleteDistrict">
                        <i class="fas fa-times mr-1"></i> Cancel
                    </button>
                    <button type="button" class="btn btn-danger font-weight-bold" wire:click="deleteDistrict">
                        <i class="fas fa-trash-alt mr-1"></i> Yes, Delete District <x-spinner for="deleteDistrict" />
                    </button>
                </div>
            </div>
        @endif
    </x-modal>

    <!-- Create / Edit Area Modal -->
    <x-modal status="{{ $areaModal }}" title="{{ $editingAreaId ? 'Edit Area' : 'Add New Area' }}">
        <form wire:submit.prevent="saveArea">
            <div class="form-group">
                <label>Parent District <span class="text-danger">*</span></label>
                <select class="form-control" wire:model="area_district_id">
                    <option value="">-- Select Parent District --</option>
                    @foreach($allDistricts as $d)
                        <option value="{{ $d->id }}">{{ $d->name }}</option>
                    @endforeach
                </select>
                <x-input-error for="area_district_id" />
            </div>

            <div class="form-group">
                <label>Area Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control" wire:model="area_name" placeholder="e.g. Area 25, Area 49, Chilomoni, Kawale">
                <x-input-error for="area_name" />
            </div>

            <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                <button type="button" class="btn btn-secondary mr-2" wire:click="cancelArea">
                    <i class="fas fa-times mr-1"></i> Cancel
                </button>
                <button type="submit" class="btn btn-primary font-weight-bold">
                    <i class="fas fa-save mr-1"></i> {{ $editingAreaId ? 'Update Area' : 'Create Area' }}
                    <x-spinner for="saveArea" />
                </button>
            </div>
        </form>
    </x-modal>

    <!-- Confirm Delete Area Modal -->
    <x-modal status="{{ $confirmDeleteAreaModal }}" title="Confirm Area Deletion">
        @if($areaToDelete)
            <div class="p-2">
                <div class="alert alert-danger d-flex align-items-center mb-3">
                    <i class="fas fa-exclamation-triangle fa-2x mr-3"></i>
                    <div>
                        <h5 class="mb-1 font-weight-bold">Warning: Deleting Area</h5>
                        <div>You are about to delete <strong>{{ $areaToDelete['name'] }}</strong> ({{ $areaToDelete['district_name'] }}).</div>
                    </div>
                </div>

                <div class="card bg-light border-0 mb-3">
                    <div class="card-body py-2">
                        <div class="row text-center">
                            <div class="col-6 border-right">
                                <div class="text-muted small">Organizations Mapped</div>
                                <div class="font-weight-bold text-navy h5 mb-0">{{ $areaToDelete['organizations_count'] }}</div>
                            </div>
                            <div class="col-6">
                                <div class="text-muted small">Mothers / Users Mapped</div>
                                <div class="font-weight-bold text-navy h5 mb-0">{{ $areaToDelete['users_count'] }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <p class="text-muted small">
                    <i class="fas fa-info-circle text-info mr-1"></i> 
                    Deleting this area will unassign it from any linked mothers or organizations.
                </p>

                <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                    <button type="button" class="btn btn-secondary mr-2" wire:click="cancelDeleteArea">
                        <i class="fas fa-times mr-1"></i> Cancel
                    </button>
                    <button type="button" class="btn btn-danger font-weight-bold" wire:click="deleteArea">
                        <i class="fas fa-trash-alt mr-1"></i> Yes, Delete Area <x-spinner for="deleteArea" />
                    </button>
                </div>
            </div>
        @endif
    </x-modal>

    <style>
        .text-navy { color: #001f3f !important; }
        .bg-navy { background-color: #001f3f !important; color: #fff; }
        .badge-navy { background-color: #001f3f !important; color: #fff; }
        .card-navy.card-outline { border-top: 3px solid #001f3f; }
        .bg-purple { background-color: #6f42c1; color: #fff; }
    </style>
</div>
