<div>
    {{-- Because she competes with no one, no one can compete with her. --}}

    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">{{ $name }} Users</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">{{ $name }} Users</a></li>
                        <li class="breadcrumb-item active">Starter Page</li>
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
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <h2></h2>
                            <div class="table-responsive">
                                <table class="table table-hover table-inverse ">
                                    <thead class="thead-inverse">
                                        <tr>
                                            <th>#</th>
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>Status</th>
                                            <th>Verification</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                        @forelse ($users as $item)
                                            <tr wire:key="org-user-{{ $item->id }}">
                                                <td scope="row">{{ $loop->iteration }}</td>
                                                <td>{{ $item->name }}</td>
                                                <td>{{ $item->email }}</td>
                                                <td>
                                                    <span class="badge badge-{{ $item->is_active == 1 ? 'success' : 'danger' }}">
                                                        {{ $item->is_active == 1 ? 'Active' : 'Inactive' }}
                                                    </span>
                                                </td>
                                                <td>
                                                    @if($item->organization_verify === 'verified')
                                                        <span class="badge badge-success">Verified</span>
                                                    @elseif($item->organization_verify === 'pending')
                                                        <span class="badge badge-warning">Pending</span>
                                                    @elseif($item->organization_verify === 'declined')
                                                        <span class="badge badge-danger">Declined</span>
                                                    @else
                                                        <span class="badge badge-secondary">Unverified</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <div class="dropdown">
                                                        <button class="btn btn-navy btn-sm dropdown-toggle" type="button"
                                                            id="dropdownMenuButton{{ $item->id }}" data-toggle="dropdown" aria-haspopup="true"
                                                            aria-expanded="false">
                                                            Actions
                                                        </button>
                                                        <div class="dropdown-menu dropdown-menu-right shadow border-0" aria-labelledby="dropdownMenuButton{{ $item->id }}">
                                                            @if($item->organization_verify === 'pending')
                                                                <a wire:click.prevent='approve({{ $item->id }})' class="dropdown-item text-success" href="#">
                                                                    <i class="fas fa-check mr-2"></i> Approve
                                                                </a>
                                                                <a wire:click.prevent='decline({{ $item->id }})' class="dropdown-item text-danger" href="#">
                                                                    <i class="fas fa-times mr-2"></i> Decline
                                                                </a>
                                                                <div class="dropdown-divider"></div>
                                                            @endif
                                                            <a class="dropdown-item" href="{{ route('users.edit', [$item->role->name, $item->id]) }}">
                                                                <i class="fas fa-edit mr-2 text-info"></i> Edit
                                                            </a>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="text-center">No users found in this organization.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- /.content -->

    <style>
        .btn-navy { background-color: #001f3f; color: #fff; border: none; }
        .btn-navy:hover { background-color: #002d5c; color: #fff; }
    </style>
</div>
