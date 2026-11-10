<div>
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2 align-items-center">
                <div class="col-sm-6">
                    <h1 class="m-0 text-bold text-navy"><i class="fas fa-sms mr-2"></i> SMS Settings & Organization Acronyms</h1>
                    <p class="text-muted mb-0">Configure organization acronym prefixes to identify sender origin on all outgoing SMS health tips and reminders.</p>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">SMS Settings</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <!-- How It Works Banner -->
            <div class="alert alert-info border-0 shadow-sm mb-4">
                <div class="d-flex align-items-center">
                    <i class="fas fa-info-circle fa-2x mr-3 text-info"></i>
                    <div>
                        <h6 class="font-weight-bold mb-1">How Organization SMS Acronyms Work</h6>
                        <p class="mb-0 small">
                            When an organization has an acronym set (e.g. <code>uchembere</code>, <code>KCH</code>, <code>BHC</code>), all automated scheduled tips and custom SMS messages sent to mothers will automatically be prefixed like:
                            <span class="badge badge-light border ml-1 font-weight-bold">uchembere: Moni amayi! Onetsetsani kuti mukumwa madzi abwino...</span>
                        </p>
                    </div>
                </div>
            </div>

            @if($isSystemAdmin)
                <!-- System Admin View: All Organizations -->
                <div class="card shadow-sm border-0 rounded-lg">
                    <div class="card-header bg-white border-bottom-0 py-3">
                        <div class="row align-items-center">
                            <div class="col-md-6 mb-2 mb-md-0">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-light border-right-0"><i class="fas fa-search text-muted"></i></span>
                                    </div>
                                    <input type="text" class="form-control border-left-0" placeholder="Search organization by name, acronym, address..." wire:model.live.debounce.300ms="search">
                                </div>
                            </div>
                            <div class="col-md-6 text-md-right">
                                <div class="btn-group btn-group-toggle" data-toggle="buttons">
                                    <button wire:click="$set('typeFilter', 'all')" class="btn btn-sm {{ $typeFilter === 'all' ? 'btn-navy' : 'btn-outline-secondary' }}">All ({{ $organizations->total() }})</button>
                                    <button wire:click="$set('typeFilter', 'facilities')" class="btn btn-sm {{ $typeFilter === 'facilities' ? 'btn-navy' : 'btn-outline-secondary' }}">Health Facilities</button>
                                    <button wire:click="$set('typeFilter', 'pharmacies')" class="btn btn-sm {{ $typeFilter === 'pharmacies' ? 'btn-navy' : 'btn-outline-secondary' }}">Pharmacies</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-light text-navy">
                                    <tr>
                                        <th class="px-4 py-3 border-0">Organization Name</th>
                                        <th class="py-3 border-0">Type</th>
                                        <th class="py-3 border-0">Acronym / Prefix</th>
                                        <th class="py-3 border-0">Sample SMS Output Preview</th>
                                        <th class="py-3 border-0 text-center" style="width: 140px;">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($organizations as $org)
                                        <tr wire:key="org-sms-{{ $org->id }}">
                                            <td class="px-4 py-3">
                                                <div class="font-weight-bold text-navy">{{ $org->name }}</div>
                                                <small class="text-muted"><i class="fas fa-map-marker-alt mr-1"></i> {{ $org->address ?: 'No address specified' }}</small>
                                            </td>
                                            <td class="py-3">
                                                @if($org->is_pharmacy)
                                                    <span class="badge badge-pill badge-warning px-2 py-1"><i class="fas fa-pills mr-1"></i> Pharmacy</span>
                                                @else
                                                    <span class="badge badge-pill badge-info px-2 py-1"><i class="fas fa-hospital mr-1"></i> Health Facility</span>
                                                @endif
                                            </td>
                                            <td class="py-3">
                                                @if(!empty($org->acronym))
                                                    <span class="badge badge-pill badge-primary px-3 py-1 font-weight-bold" style="font-size: 0.9rem;">
                                                        <i class="fas fa-tag mr-1"></i> {{ $org->acronym }}
                                                    </span>
                                                @else
                                                    <span class="badge badge-pill badge-secondary px-2 py-1 font-italic">Not Set (No Prefix)</span>
                                                @endif
                                            </td>
                                            <td class="py-3">
                                                <div class="p-2 rounded bg-light border text-truncate small" style="max-width: 420px;" title="{{ \App\Services\SmsService::formatWithAcronym($sampleMessage, $org->acronym) }}">
                                                    <i class="fas fa-mobile-alt mr-1 text-muted"></i>
                                                    <strong>{{ \App\Services\SmsService::formatWithAcronym($sampleMessage, $org->acronym) }}</strong>
                                                </div>
                                            </td>
                                            <td class="py-3 text-center">
                                                <button wire:click="editOrgAcronym({{ $org->id }})" class="btn btn-sm btn-outline-navy rounded-pill px-3 shadow-sm">
                                                    <i class="fas fa-edit mr-1"></i> Set Acronym
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center py-5 text-muted">
                                                <i class="fas fa-building fa-3x mb-3 text-secondary"></i>
                                                <h5>No Organizations Found</h5>
                                                <p class="small mb-0">No organizations match your current search or filter.</p>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @if($organizations->hasPages())
                        <div class="card-footer bg-white border-0 py-3">
                            {{ $organizations->links() }}
                        </div>
                    @endif
                </div>

                <!-- Acronym Edit Modal for System Admin -->
                <x-modal title="Configure Organization SMS Acronym" :status="$modal">
                    <form wire:submit.prevent="saveOrgAcronym">
                        <div class="form-group">
                            <label class="text-muted small mb-1">Organization</label>
                            <input type="text" class="form-control bg-light font-weight-bold" value="{{ $editingOrgName }}" readonly>
                        </div>

                        <div class="form-group">
                            <label class="font-weight-bold text-navy">Acronym / Prefix <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-tag"></i></span>
                                </div>
                                <input type="text" class="form-control" wire:model.live.debounce.200ms="acronym" placeholder="e.g. uchembere, Bwaila, KCH" maxlength="30">
                            </div>
                            <small class="text-muted">Short identifier (recommended: 3 to 15 characters). Leave empty if no prefix is desired.</small>
                            @error('acronym') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        </div>

                        <!-- Live Preview Card -->
                        <div class="card bg-light border shadow-none mb-3">
                            <div class="card-body p-3">
                                <div class="small font-weight-bold text-muted mb-1"><i class="fas fa-eye mr-1"></i> Outgoing SMS Live Preview</div>
                                <div class="p-2 bg-white rounded border text-dark font-weight-bold small">
                                    {{ $this->previewMessage }}
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer px-0 pb-0 border-top">
                            <button type="button" class="btn btn-secondary rounded-pill px-4" wire:click="cancel">Cancel</button>
                            <button type="submit" class="btn btn-navy rounded-pill px-4 shadow-sm">
                                <i class="fas fa-save mr-1"></i> Save Acronym <x-spinner for="saveOrgAcronym" />
                            </button>
                        </div>
                    </form>
                </x-modal>

            @else
                <!-- Organization Admin / Doctor View -->
                <div class="row justify-content-center">
                    <div class="col-lg-8">
                        <div class="card card-navy card-outline shadow-sm border-0 rounded-lg">
                            <div class="card-header bg-white pt-4 px-4 border-bottom-0">
                                <h4 class="card-title font-weight-bold text-navy"><i class="fas fa-hospital mr-2"></i> {{ $myOrg->name ?? 'My Organization' }}</h4>
                            </div>
                            <div class="card-body px-4 pb-4">
                                <form wire:submit.prevent="saveMyOrgAcronym">
                                    <div class="form-group mb-4">
                                        <label class="font-weight-bold text-navy">Organization SMS Acronym / Alias</label>
                                        <div class="input-group input-group-lg">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text bg-soft-navy text-navy font-weight-bold"><i class="fas fa-tag"></i></span>
                                            </div>
                                            <input type="text" class="form-control" wire:model.live.debounce.200ms="acronym" placeholder="e.g. uchembere, Bwaila, KCH" maxlength="30">
                                        </div>
                                        <small class="text-muted mt-1 d-block">
                                            This acronym will be prefixed on all outgoing pregnancy tips and reminders sent to your mothers so they know who the message is from.
                                        </small>
                                        @error('acronym') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                    </div>

                                    <!-- SMS Live Phone Preview Screen -->
                                    <div class="card bg-light border mb-4">
                                        <div class="card-header bg-white py-2">
                                            <span class="font-weight-bold text-muted small"><i class="fas fa-mobile-alt mr-1"></i> What Mothers Will See On Their Phone</span>
                                        </div>
                                        <div class="card-body p-3">
                                            <div class="bg-white p-3 rounded shadow-sm border" style="border-left: 4px solid #001f3f !important;">
                                                <div class="d-flex justify-content-between align-items-center mb-1">
                                                    <span class="badge badge-secondary font-weight-normal text-xs">MaaSMS Health Tip</span>
                                                    <small class="text-muted">Just now</small>
                                                </div>
                                                <div class="text-dark font-weight-bold">
                                                    {{ $this->previewMessage }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="text-right">
                                        <button type="submit" class="btn btn-navy rounded-pill px-5 shadow-sm">
                                            <i class="fas fa-save mr-1"></i> Save SMS Acronym <x-spinner for="saveMyOrgAcronym" />
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

        </div>
    </section>

    <style>
        .text-navy { color: #001f3f !important; }
        .bg-soft-navy { background-color: #eef2f7; }
        .btn-navy { background-color: #001f3f; color: #fff; border: none; }
        .btn-navy:hover { background-color: #002d5c; color: #fff; }
        .btn-outline-navy { color: #001f3f; border-color: #001f3f; }
        .btn-outline-navy:hover { background-color: #001f3f; color: #fff; }
    </style>
</div>
