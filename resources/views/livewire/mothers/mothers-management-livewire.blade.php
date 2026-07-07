<div>
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2 align-items-center">
                <div class="col-sm-6">
                    <h1 class="m-0 text-bold text-navy"><i class="fas fa-female mr-2"></i> Mothers Management</h1>
                    <p class="text-muted mb-0">Manage and track registered mothers within your organization.</p>
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
            <!-- Stats & Actions Row -->
            <div class="row mb-4">
                <div class="col-md-8">
                    <div class="input-group shadow-sm">
                        <div class="input-group-prepend">
                            <span class="input-group-text bg-white border-right-0"><i class="fas fa-search text-muted"></i></span>
                        </div>
                        <input type="text" class="form-control border-left-0" placeholder="Search by name or phone number..." wire:model.live="search">
                    </div>
                </div>
                <div class="col-md-4 text-right">
                    @if(auth()->user()->isSystemAdmin() && count($selectedMothers) > 0)
                        <button wire:click="reassign" class="btn btn-warning shadow-sm px-4 mr-2">
                            <i class="fas fa-exchange-alt mr-2"></i> Reassign ({{ count($selectedMothers) }})
                        </button>
                    @endif
                    <button wire:click="create" class="btn btn-navy shadow-sm px-4">
                        <i class="fas fa-plus-circle mr-2"></i> Register New Mother
                    </button>
                </div>
            </div>

            <!-- Mothers Grid/Table -->
            <div class="card shadow-sm border-0 rounded-lg overflow-hidden">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light text-navy">
                                <tr>
                                    @if(auth()->user()->isSystemAdmin())
                                        <th class="px-4 py-3 border-0" style="width: 50px;">
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" class="custom-control-input" id="selectAll" wire:model.live="selectAll">
                                                <label class="custom-control-label" for="selectAll"></label>
                                            </div>
                                        </th>
                                    @endif
                                    <th class="px-4 py-3 border-0">Mother Info</th>
                                    <th class="py-3 border-0">Contact Details</th>
                                    <th class="py-3 border-0">Status</th>
                                    <th class="py-3 border-0 text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($mothers as $mother)
                                    <tr wire:key="mother-{{ $mother->id }}">
                                        @if(auth()->user()->isSystemAdmin())
                                            <td class="px-4 py-3 border-top-0">
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" class="custom-control-input" id="mother-{{ $mother->id }}" value="{{ $mother->id }}" wire:model.live="selectedMothers">
                                                    <label class="custom-control-label" for="mother-{{ $mother->id }}"></label>
                                                </div>
                                            </td>
                                        @endif
                                        <td class="px-4 py-3 border-top-0">
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-circle bg-soft-navy text-navy mr-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; border-radius: 50%; background: #eef2f7; font-weight: bold;">
                                                    {{ strtoupper(substr($mother->name, 0, 1)) }}
                                                </div>
                                                <div>
                                                    <h6 class="mb-0 font-weight-bold">{{ $mother->name }}</h6>
                                                    <small class="text-muted"><i class="fas fa-birthday-cake mr-1"></i> {{ \Carbon\Carbon::parse($mother->date_of_birth)->format('M d, Y') }} ({{ $mother->age }} yrs)</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="py-3 border-top-0">
                                            <div class="mb-1 small"><i class="fas fa-phone-alt mr-2 text-info"></i> {{ $mother->phone }}</div>
                                            <div class="small text-muted"><i class="fas fa-map-marker-alt mr-2"></i> {{ $mother->address }}</div>
                                            @if($mother->district || $mother->area)
                                                <div class="small text-muted">
                                                    <i class="fas fa-location-arrow mr-2 text-secondary"></i>
                                                    {{ $mother->district->name ?? '' }}{{ $mother->area ? ', ' . $mother->area->name : '' }}
                                                </div>
                                            @endif
                                        </td>
                                        <td class="py-3 border-top-0">
                                            <span class="badge badge-pill badge-info px-2 py-1" style="font-weight: 500;">
                                                {{ $mother->organization->name ?? 'N/A' }}
                                            </span>
                                        </td>
                                        <td class="py-3 border-top-0 text-center">
                                            <div class="btn-group shadow-sm rounded">
                                                <a href="{{ route('mothers.show', \App\Helper\StandardData::encrypt($mother->id)) }}" class="btn btn-white btn-sm px-3" title="View Profile">
                                                    <i class="fas fa-eye text-primary"></i>
                                                </a>
                                                <button wire:click="edit({{ $mother->id }})" class="btn btn-white btn-sm px-3" title="Edit">
                                                    <i class="fas fa-edit text-info"></i>
                                                </button>
                                                <button wire:click="delete({{ $mother->id }})"
                                                        wire:confirm="Are you sure you want to delete this mother record? This will also remove her pregnancy history."
                                                        class="btn btn-white btn-sm px-3" title="Delete">
                                                    <i class="fas fa-trash text-danger"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-5">
                                            <div class="py-4">
                                                <i class="fas fa-users-slash fa-3x text-muted mb-3"></i>
                                                <h5 class="text-muted">No mothers found matching your criteria.</h5>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer bg-white border-0 py-3">
                    {{ $mothers->links() }}
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
                                    <select class="form-control" wire:model.live="district_id">
                                        <option value="">Select District</option>
                                        @foreach($districts as $district)
                                            <option value="{{ $district->id }}">{{ $district->name }}</option>
                                        @endforeach
                                    </select>
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
                                </div>
                                <div class="col-md-3 form-group">
                                    <label>Still Births</label>
                                    <select class="form-control" wire:model="stillBirths">
                                        <option value="No">No</option>
                                        <option value="Yes">Yes</option>
                                    </select>
                                </div>
                                <div class="col-md-3 form-group">
                                    <label>Multiple</label>
                                    <select class="form-control" wire:model="multiple">
                                        <option value="No">No</option>
                                        <option value="Yes">Yes</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4 form-group small">
                                    <label>Leg/Spine Deformity</label>
                                    <select class="form-control form-control-sm" wire:model="legOrSpine">
                                        <option value="No">No</option>
                                        <option value="Yes">Yes</option>
                                    </select>
                                </div>
                                <div class="col-md-4 form-group small">
                                    <label>Other Deformity</label>
                                    <select class="form-control form-control-sm" wire:model="deformity">
                                        <option value="No">No</option>
                                        <option value="Yes">Yes</option>
                                    </select>
                                </div>
                                <div class="col-md-4 form-group small">
                                    <label>C-Section History</label>
                                    <select class="form-control form-control-sm" wire:model="cSection">
                                        <option value="No">No</option>
                                        <option value="Yes">Yes</option>
                                    </select>
                                </div>
                                <div class="col-md-4 form-group small">
                                    <label>Vacuum Extraction</label>
                                    <select class="form-control form-control-sm" wire:model="vacum">
                                        <option value="No">No</option>
                                        <option value="Yes">Yes</option>
                                    </select>
                                </div>
                                <div class="col-md-4 form-group small">
                                    <label>Tuberculosis</label>
                                    <select class="form-control form-control-sm" wire:model="tuberculosis">
                                        <option value="No">No</option>
                                        <option value="Yes">Yes</option>
                                    </select>
                                </div>
                                <div class="col-md-4 form-group small">
                                    <label>Asthma</label>
                                    <select class="form-control form-control-sm" wire:model="asthma">
                                        <option value="No">No</option>
                                        <option value="Yes">Yes</option>
                                    </select>
                                </div>
                                <div class="col-md-12 form-group small">
                                    <label>Menstrual Cycle</label>
                                    <select class="form-control form-control-sm" wire:model="menstrualCycle">
                                        <option value="Regular">Regular</option>
                                        <option value="Irregular">Irregular</option>
                                    </select>
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
