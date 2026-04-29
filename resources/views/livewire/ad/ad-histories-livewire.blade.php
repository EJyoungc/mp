<div>
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Ad Delivery History</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item active">Ad History</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-white">
                            <div class="row align-items-center">
                                <div class="col-md-4">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text bg-transparent border-right-0"><i class="fas fa-search"></i></span>
                                        </div>
                                        <input type="text" wire:model.live="search" class="form-control border-left-0" placeholder="Search by Mother, Phone, or Product...">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <select wire:model.live="status" class="form-control">
                                        <option value="">All Status</option>
                                        <option value="sent">Sent</option>
                                        <option value="pending">Pending</option>
                                        <option value="failed">Failed</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <select wire:model.live="perPage" class="form-control">
                                        <option value="10">10 per page</option>
                                        <option value="25">25 per page</option>
                                        <option value="50">50 per page</option>
                                        <option value="100">100 per page</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="bg-light">
                                        <tr>
                                            <th class="border-0">#</th>
                                            <th class="border-0">Mother</th>
                                            <th class="border-0">Product/Ad</th>
                                            <th class="border-0">Pharmacy</th>
                                            <th class="border-0">Status</th>
                                            <th class="border-0">Timestamp</th>
                                            <th class="border-0 text-center">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($ads as $index => $ad)
                                            <tr wire:key="ad-{{ $ad->id }}">
                                                <td>{{ $ads->firstItem() + $index }}</td>
                                                <td>
                                                    <div class="font-weight-bold">{{ $ad->mother->name ?? 'N/A' }}</div>
                                                    <small class="text-muted"><i class="fas fa-phone-alt mr-1"></i> {{ $ad->mother->phone ?? 'N/A' }}</small>
                                                </td>
                                                <td>
                                                    <div class="text-navy font-weight-bold">{{ $ad->pharmacyAd->product_name ?? 'N/A' }}</div>
                                                    <small class="text-muted d-block" title="{{ $ad->message }}">{{ Str::limit($ad->message, 40) }}</small>
                                                </td>
                                                <td>{{ $ad->organization->name ?? 'N/A' }}</td>
                                                <td>
                                                    @if($ad->status == 'sent')
                                                        <span class="badge badge-success px-2 py-1"><i class="fas fa-check-circle mr-1"></i> Sent</span>
                                                    @elseif($ad->status == 'failed')
                                                        <span class="badge badge-danger px-2 py-1"><i class="fas fa-times-circle mr-1"></i> Failed</span>
                                                    @else
                                                        <span class="badge badge-warning px-2 py-1"><i class="fas fa-clock mr-1"></i> Pending</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <div class="small">{{ $ad->created_at->format('M d, Y') }}</div>
                                                    <div class="small text-muted">{{ $ad->created_at->format('H:i:s') }}</div>
                                                </td>
                                                <td class="text-center">
                                                    <div class="btn-group">
                                                        <button wire:click="viewResponse({{ $ad->id }})" class="btn btn-sm btn-outline-info" title="View API Response">
                                                            <i class="fas fa-code"></i>
                                                        </button>
                                                        <button wire:click="resend({{ $ad->id }})" class="btn btn-sm btn-outline-primary" title="Resend Message" wire:confirm="Are you sure you want to resend this ad message?" wire:loading.attr="disabled">
                                                            <i class="fas fa-sync-alt" wire:loading.remove wire:target="resend({{ $ad->id }})"></i>
                                                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true" wire:loading wire:target="resend({{ $ad->id }})"></span>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="7" class="text-center py-5">
                                                    <i class="fas fa-history fa-3x text-muted mb-3"></i>
                                                    <p class="text-muted">No ad delivery history found.</p>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="card-footer bg-white border-0 py-3">
                            {{ $ads->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if($modal)
    <div class="modal fade show" style="display: block; padding-right: 17px;" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-navy text-white">
                    <h5 class="modal-title"><i class="fas fa-terminal mr-2"></i> API Response Detail</h5>
                    <button type="button" class="close text-white" wire:click="cancel">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body bg-light">
                    @if($selected_ad && $selected_ad->api_response)
                        <div class="mb-3">
                            <span class="badge badge-{{ ($selected_ad->api_response['status'] ?? '') === 'Success' ? 'success' : 'danger' }} px-3 py-2">
                                Status: {{ $selected_ad->api_response['status'] ?? 'Unknown' }}
                            </span>
                            <span class="text-muted ml-2 small">Timestamp: {{ $selected_ad->api_response['timestamp'] ?? 'N/A' }}</span>
                        </div>
                        <div class="position-relative">
                            <pre class="p-3 bg-dark text-light rounded shadow-inner mb-0" style="max-height: 400px; overflow-y: auto; font-size: 0.85rem;">{{ json_encode($selected_ad->api_response, JSON_PRETTY_PRINT) }}</pre>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-info-circle fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No API response recorded for this message.</p>
                        </div>
                    @endif
                </div>
                <div class="modal-footer bg-light border-top-0">
                    <button type="button" class="btn btn-secondary px-4" wire:click="cancel">Close</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-backdrop fade show"></div>
    @endif

    <style>
        .text-navy { color: #001f3f !important; }
        .bg-navy { background-color: #001f3f !important; }
        .shadow-inner { box-shadow: inset 0 2px 4px 0 rgba(0,0,0,0.06); }
    </style>
</div>
