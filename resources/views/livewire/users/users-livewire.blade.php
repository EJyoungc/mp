<div>
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Users</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                        <li class="breadcrumb-item active">User</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12 col-lg-3 col-md-4">
                    <div class="card bg-cyan">
                        <div class="card-body">
                            <h4>All Users</h4>
                            <h5>{{ $allUsersCount }}</h5>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-lg-3 col-md-4">
                    <div class="card bg-teal ">
                        <div class="card-body">
                            <h4>Doctors</h4>
                            <h5>{{ $doctorsCount }}</h5>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-3 col-md-4">
                    <div class="card bg-fuchsia ">
                        <div class="card-body">
                            <h4>Mothers</h4>
                            <h5>{{ $mothersCount }}</h5>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-3 col-md-4">
                    <div class="card bg-gray ">
                        <div class="card-body">
                            <h4>Practitioners</h4>
                            <h5>{{ $practitionersCount }}</h5>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-3">
                    <input type="text" wire:model.live="search" class="form-control" placeholder="Search by name, email, phone...">
                </div>
                <div class="col-md-2">
                    <select wire:model.live="role_filter" class="form-control">
                        <option value="">All Roles</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->id }}">{{ ucfirst($role->name) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <select wire:model.live="organization_filter" class="form-control">
                        <option value="">All Organizations</option>
                        @foreach($organizations as $org)
                            <option value="{{ $org->id }}">{{ $org->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select wire:model.live="perPage" class="form-control">
                        <option value="10">10 per page</option>
                        <option value="25">25 per page</option>
                        <option value="50">50 per page</option>
                    </select>
                </div>
                <div class="col-md-2 text-right">
                    <div class="dropdown">
                        <button class="btn btn-dark dropdown-toggle" type="button" id="triggerId" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Add User
                        </button>
                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="triggerId">
                            @forelse ($roles as $item)
                                <a class="dropdown-item text-capitalize" href="{{ route('users.create', $item->name) }}">{{ $item->name }}</a>
                            @empty
                                <a class="dropdown-item disabled" href="#">No roles available</a>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Name / Email</th>
                                            <th>Role</th>
                                            <th>Organization</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($users as $item)
                                            <tr>
                                                <td>{{ $users->firstItem() + $loop->index }}</td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="mr-2">
                                                            <svg width="30" height="30" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                <circle cx="12" cy="8" r="4" fill="#4A5568" />
                                                                <ellipse cx="12" cy="20" rx="7" ry="4" fill="#4A5568" />
                                                            </svg>
                                                        </div>
                                                        <div>
                                                            <span class="text-capitalize font-weight-bold d-block">{{ $item->name }}</span>
                                                            <small class="text-muted">{{ $item->email }}</small>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="badge bg-{{ $item->role->name === 'system-admin' ? 'success' : ($item->role->name === 'admin' ? 'cyan' : ($item->role->name === 'doctor' ? 'teal' : ($item->role->name === 'mother' ? 'fuchsia' : ($item->role->name === 'practitioner' ? 'warning' : 'secondary')))) }}">
                                                        {{ ucfirst($item->role->name) }}
                                                    </span>
                                                </td>
                                                <td>
                                                    {{ $item->organization->name ?? 'None' }}
                                                </td>
                                                <td>
                                                    <span class="badge bg-{{ $item->is_active == 1 ? 'success' : 'danger' }}">
                                                        {{ $item->is_active == 1 ? 'Active' : 'Inactive' }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="dropdown">
                                                        <button class="btn btn-dark btn-sm dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                            Options
                                                        </button>
                                                        <div class="dropdown-menu">
                                                            <a class="dropdown-item" href="{{ route('users.edit', [$item->role->name, $item->id]) }}">Edit</a>
                                                            <a class="dropdown-item" wire:click.prevent="toggleActive({{ $item->id }})" href="#">
                                                                {{ $item->is_active == 1 ? 'Deactivate' : 'Activate' }}
                                                            </a>
                                                            <a class="dropdown-item" wire:click.prevent="resetPassword({{ $item->id }})" href="#">
                                                                Reset Password
                                                            </a>
                                                            @if(Auth::user()->role->name === 'system-admin')
                                                                <div class="dropdown-divider"></div>
                                                                <a class="dropdown-item text-danger" href="#" wire:click.prevent="delete({{ $item->id }})" wire:confirm.prompt="Are you sure you want to delete this user? \n\nType DELETE to Confirm|DELETE">
                                                                    Delete
                                                                </a>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="text-center p-4">No users found matching your criteria.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="card-footer clearfix">
                            {{ $users->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
