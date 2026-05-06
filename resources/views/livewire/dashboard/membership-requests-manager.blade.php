<div class="col-12 mt-4">
    <div class="card">
        <div class="card-header bg-warning">
            <h3 class="card-title text-dark"><i class="fas fa-id-card-alt mr-2"></i> Membership Requests</h3>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr><th>#</th><th>Name</th><th>Email</th>@if(auth()->user()->isSystemAdmin())<th>Organization</th>@endif<th>Status</th><th>Actions</th></tr>
                    </thead>
                    <tbody>
                        @forelse ($requests as $index => $item)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $item->name }}</td>
                                <td>{{ $item->email }}</td>
                                @if(auth()->user()->isSystemAdmin())<td>{{ $item->organization->name ?? 'N/A' }}</td>@endif
                                <td><span class="badge bg-warning">{{ $item->organization_verify }}</span></td>
                                <td>
                                    <button class="btn btn-success btn-sm" wire:click="approve({{ $item->id }})">Approve</button>
                                    <button class="btn btn-danger btn-sm" wire:click="decline({{ $item->id }})">Decline</button>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center">No pending requests.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
