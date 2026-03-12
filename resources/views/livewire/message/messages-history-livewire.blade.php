<div>
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Message History</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Message History</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="row">
                                <div class="col-md-4">
                                    <input type="text" wire:model.live="search" class="form-control" placeholder="Search by Mother name, phone or Tip...">
                                </div>
                                <div class="col-md-3">
                                    <select wire:model.live="status" class="form-control">
                                        <option value="">All Status</option>
                                        <option value="sent">Sent</option>
                                        <option value="unsent">Unsent</option>
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
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover table-bordered">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Mother Name</th>
                                            <th>Phone</th>
                                            <th>Tip</th>
                                            <th>Week/Day</th>
                                            <th>Status</th>
                                            <th>Sent At</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($messages as $index => $message)
                                            <tr>
                                                <td>{{ $messages->firstItem() + $index }}</td>
                                                <td>{{ $message->mother->name ?? 'N/A' }}</td>
                                                <td>{{ $message->mother->phone ?? 'N/A' }}</td>
                                                <td>{{ Str::limit($message->tip->tip ?? 'N/A', 50) }}</td>
                                                <td>W: {{ $message->week->week ?? 'N/A' }} / D: {{ $message->day->day ?? 'N/A' }}</td>
                                                <td>
                                                    @if($message->message_status == 'sent')
                                                        <span class="badge badge-success">Sent</span>
                                                    @elseif($message->message_status == 'failed')
                                                        <span class="badge badge-danger">Failed</span>
                                                    @else
                                                        <span class="badge badge-warning">Unsent</span>
                                                    @endif
                                                </td>
                                                <td>{{ $message->updated_at->format('Y-m-d H:i:s') }}</td>
                                                <td>
                                                    <button wire:click="viewResponse({{ $message->id }})" class="btn btn-sm btn-info">
                                                        <i class="fas fa-eye"></i> View API Response
                                                    </button>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="8" class="text-center">No message history found.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            <div class="mt-4">
                                {{ $messages->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if($modal)
    <div class="modal fade show" style="display: block;" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">API Response Detail</h5>
                    <button type="button" class="close" wire:click="cancel">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    @if($selected_message && $selected_message->api_response)
                        <pre style="background: #f4f4f4; padding: 15px; border-radius: 5px;">{{ json_encode($selected_message->api_response, JSON_PRETTY_PRINT) }}</pre>
                    @else
                        <p class="text-muted">No API response recorded for this message.</p>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" wire:click="cancel">Close</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-backdrop fade show"></div>
    @endif
</div>
