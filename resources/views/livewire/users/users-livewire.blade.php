<div>
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-bold text-navy">Users</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Users</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="content">
        <div class="container-fluid">
            <!-- Analytics Row -->
            <div class="row">
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-info shadow-sm">
                        <div class="inner">
                            <h3>{{ $allUsersCount }}</h3>
                            <p>Total Staff</p>
                        </div>
                        <div class="icon"><i class="fas fa-users"></i></div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-teal shadow-sm">
                        <div class="inner">
                            <h3>{{ $doctorsCount }}</h3>
                            <p>Doctors</p>
                        </div>
                        <div class="icon"><i class="fas fa-user-md"></i></div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-fuchsia shadow-sm">
                        <div class="inner">
                            <h3>{{ $mothersCount }}</h3>
                            <p>Mothers</p>
                        </div>
                        <div class="icon"><i class="fas fa-female"></i></div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-warning shadow-sm">
                        <div class="inner">
                            <h3>{{ $practitionersCount }}</h3>
                            <p>Practitioners</p>
                        </div>
                        <div class="icon"><i class="fas fa-user-nurse"></i></div>
                    </div>
                </div>
            </div>

            <!-- Search & Actions Row -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-4">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text bg-white border-right-0"><i class="fas fa-search text-muted"></i></span>
                                </div>
                                <input type="text" wire:model.live="search" class="form-control border-left-0" placeholder="Search name, email or phone...">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <select wire:model.live="role_filter" class="form-control">
                                <option value="">All Roles</option>
                                @foreach($roles as $role)
                                    <option value="{{ $role->id }}">{{ ucfirst($role->name) }}</option>
                                @endforeach
                            </select>
                        </div>
                        @if(auth()->user()->isSystemAdmin())
                        <div class="col-md-2">
                            <select wire:model.live="organization_filter" class="form-control">
                                <option value="">All Organizations</option>
                                @foreach($organizations as $org)
                                    <option value="{{ $org->id }}">{{ $org->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        @endif
                        <div class="col text-right">
                            <div class="btn-group mr-2">
                                @if(auth()->user()->isSystemAdmin())
                                    <button type="button" wire:click="verifyAll" wire:confirm="Are you sure you want to verify all pending users?" class="btn btn-outline-success rounded-pill px-4 mr-2">
                                        <i class="fas fa-check-double mr-2"></i> Verify All Pending
                                    </button>
                                @endif
                                <button type="button" wire:click="showInviteModal" class="btn btn-outline-navy rounded-pill px-4">
                                    <i class="fas fa-paper-plane mr-2"></i> Invite User
                                </button>
                            </div>
                            <div class="btn-group">
                                <button type="button" class="btn btn-navy rounded-pill px-4 dropdown-toggle" data-toggle="dropdown">
                                    <i class="fas fa-user-plus mr-2"></i> Add New User
                                </button>
                                <div class="dropdown-menu dropdown-menu-right shadow border-0">
                                    @foreach ($roles as $item)
                                        <a class="dropdown-item py-2" href="{{ route('users.create', $item->name) }}">
                                            <i class="fas fa-chevron-right mr-2 text-xs text-muted"></i> {{ ucfirst($item->name) }}
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="card shadow-sm border-0 rounded-lg overflow-hidden">
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="bg-light text-navy">
                                        <tr>
                                            <th class="px-4 py-3">#</th>
                                            <th class="py-3">Staff / User Info</th>
                                            <th class="py-3">Role & Organization</th>
                                            <th class="py-3 text-center">Verification</th>
                                            <th class="py-3 text-center">Status</th>
                                            <th class="py-3 text-center">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($users as $item)
                                            <tr wire:key="user-{{ $item->id }}">
                                                <td class="px-4">{{ $users->firstItem() + $loop->index }}</td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar-circle bg-soft-navy text-navy mr-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; border-radius: 50%; background: #eef2f7; font-weight: bold;">
                                                            {{ strtoupper(substr($item->name, 0, 1)) }}
                                                        </div>
                                                        <div>
                                                            <span class="text-capitalize font-weight-bold d-block">{{ $item->name }}</span>
                                                            <small class="text-muted"><i class="fas fa-envelope mr-1"></i> {{ $item->email }}</small>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="mb-1">
                                                        <span class="badge badge-pill px-2 py-1 bg-soft-navy text-navy border">
                                                            {{ ucfirst($item->role->name) }}
                                                        </span>
                                                    </div>
                                                    <small class="text-info"><i class="fas fa-hospital-alt mr-1"></i> {{ $item->organization->name ?? 'System' }}</small>
                                                </td>
                                                <td class="text-center">
                                                    @if($item->organization_verify === 'verified' || $item->organization_verify === 'accepted')
                                                        <span class="badge badge-success px-2 py-1"><i class="fas fa-check-circle mr-1"></i> Verified</span>
                                                    @elseif($item->organization_verify === 'pending')
                                                        <div class="d-flex flex-column align-items-center gap-1">
                                                            <span class="badge badge-warning px-2 py-1 mb-2"><i class="fas fa-clock mr-1"></i> Pending</span>
                                                            @if(auth()->user()->isOrgAdmin() || auth()->user()->isSystemAdmin())
                                                                <button wire:click="showVerifyModal({{ $item->id }})" class="btn btn-xs btn-outline-success px-2 rounded-pill shadow-sm" title="Verify User">
                                                                    <i class="fas fa-user-check mr-1"></i> Verify Now
                                                                </button>
                                                            @endif
                                                        </div>
                                                    @else
                                                        <span class="badge badge-danger px-2 py-1"><i class="fas fa-times-circle mr-1"></i> {{ ucfirst($item->organization_verify ?? 'Unverified') }}</span>
                                                        @if(($item->organization_verify === 'declined') && (auth()->user()->isOrgAdmin() || auth()->user()->isSystemAdmin()))
                                                             <button wire:click="approve({{ $item->id }})" class="btn btn-xs btn-link text-success p-0 ml-1" title="Re-approve"><i class="fas fa-redo"></i></button>
                                                        @endif
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    <span class="badge badge-{{ $item->is_active == 1 ? 'success' : 'danger' }} px-2 py-1">
                                                        {{ $item->is_active == 1 ? 'Active' : 'Inactive' }}
                                                    </span>
                                                </td>
                                                <td class="text-center">
                                                    <div class="btn-group shadow-sm rounded">
                                                        <a href="{{ route('users.edit', [$item->role->name, $item->id]) }}" class="btn btn-white btn-sm px-3" title="Edit">
                                                            <i class="fas fa-edit text-info"></i>
                                                        </a>
                                                        <button type="button" class="btn btn-white btn-sm px-3 dropdown-toggle dropdown-icon" data-toggle="dropdown"></button>
                                                        <div class="dropdown-menu dropdown-menu-right shadow border-0">
                                                            @if($item->organization_verify === 'pending' && (auth()->user()->isOrgAdmin() || auth()->user()->isSystemAdmin()))
                                                                <a class="dropdown-item text-success" wire:click.prevent="showVerifyModal({{ $item->id }})" href="#">
                                                                    <i class="fas fa-check-circle mr-2"></i> Verify User
                                                                </a>
                                                                <a class="dropdown-item text-warning" wire:click.prevent="decline({{ $item->id }})" href="#">
                                                                    <i class="fas fa-times-circle mr-2"></i> Decline User
                                                                </a>
                                                                <div class="dropdown-divider"></div>
                                                            @endif
                                                            <a class="dropdown-item" wire:click.prevent="showRoleModal({{ $item->id }})" href="#">
                                                                <i class="fas fa-user-tag mr-2 text-muted"></i> Change Role
                                                            </a>
                                                            <a class="dropdown-item" wire:click.prevent="toggleActive({{ $item->id }})" href="#">
                                                                <i class="fas fa-power-off mr-2 {{ $item->is_active == 1 ? 'text-danger' : 'text-success' }}"></i> 
                                                                {{ $item->is_active == 1 ? 'Deactivate' : 'Activate' }}
                                                            </a>
                                                            <a class="dropdown-item" wire:click.prevent="resetPassword({{ $item->id }})" href="#">
                                                                <i class="fas fa-key mr-2 text-warning"></i> Reset Password
                                                            </a>
                                                            @if(auth()->user()->isSystemAdmin())
                                                                <div class="dropdown-divider"></div>
                                                                <a class="dropdown-item text-danger" href="#" wire:click.prevent="delete({{ $item->id }})" wire:confirm="Are you sure you want to delete this user?">
                                                                    <i class="fas fa-trash-alt mr-2"></i> Delete User
                                                                </a>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="text-center py-5">
                                                    <i class="fas fa-user-slash fa-3x text-muted mb-3"></i>
                                                    <h5 class="text-muted">No users found.</h5>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="card-footer bg-white border-top shadow-none py-3">
                            {{ $users->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <x-modal status="{{ $roleModal }}" title="Change User Role">
        @if($selectedUser)
            <div class="p-3">
                <p>Updating role for: <strong>{{ $selectedUser->name }}</strong></p>
                <form wire:submit.prevent="updateRole">
                    <div class="form-group">
                        <label for="newRoleId">Select New Role</label>
                        <select wire:model="newRoleId" class="form-control" id="newRoleId">
                            @foreach($roles as $role)
                                <option value="{{ $role->id }}">{{ ucfirst($role->name) }}</option>
                            @endforeach
                        </select>
                        <x-input-error for="newRoleId" />
                    </div>
                    <div class="mt-4 d-flex justify-content-between">
                        <button type="button" wire:click="cancel" class="btn btn-secondary">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update Role <x-spinner for="updateRole" /></button>
                    </div>
                </form>
            </div>
        @endif
    </x-modal>

    <x-modal status="{{ $verifyModal }}" title="Verify User Account">
        @if($selectedUser)
            <div class="p-3">
                <div class="text-center mb-4">
                    <div class="avatar-circle bg-soft-navy text-navy mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 60px; height: 60px; border-radius: 50%; background: #eef2f7; font-size: 1.5rem; font-weight: bold;">
                        {{ strtoupper(substr($selectedUser->name, 0, 1)) }}
                    </div>
                    <h5 class="font-weight-bold mb-1">{{ $selectedUser->name }}</h5>
                    <p class="text-muted small mb-0">{{ $selectedUser->email }}</p>
                    <span class="badge badge-pill badge-info px-3 py-1 mt-2">{{ ucfirst($selectedUser->role->name) }}</span>
                </div>

                <div class="alert alert-light border mb-4">
                    <p class="small mb-2"><strong>Organization:</strong> {{ $selectedUser->organization->name ?? 'System' }}</p>
                    <p class="small mb-0"><strong>Phone:</strong> {{ $selectedUser->phone ?? 'N/A' }}</p>
                </div>

                <p class="text-center text-muted small px-4">By verifying this user, they will gain full access to their assigned features within the platform.</p>

                <div class="mt-4 d-flex justify-content-between">
                    <button type="button" wire:click="cancel" class="btn btn-secondary px-4">Cancel</button>
                    <div class="btn-group">
                        <button type="button" wire:click="decline({{ $selectedUser->id }})" class="btn btn-outline-danger px-4 mr-2">Decline</button>
                        <button type="button" wire:click="approve({{ $selectedUser->id }})" class="btn btn-success px-4">
                            <i class="fas fa-check mr-2"></i> Approve & Activate
                        </button>
                    </div>
                </div>
            </div>
        @endif
    </x-modal>

    <x-modal status="{{ $inviteModal }}" title="Invite New User">
        <div class="p-3">
            <p class="text-muted">Send an email invitation for a user to register under <strong>{{ auth()->user()->organization->name ?? 'System' }}</strong>.</p>
            <form wire:submit.prevent="sendInvite">
                <div class="form-group">
                    <label for="inviteEmail">Email Address</label>
                    <input type="email" wire:model="inviteEmail" class="form-control" id="inviteEmail" placeholder="user@example.com">
                    <x-input-error for="inviteEmail" />
                </div>
                <div class="form-group">
                    <label for="inviteRoleId">Assign Role</label>
                    <select wire:model="inviteRoleId" class="form-control" id="inviteRoleId">
                        <option value="">Select Role</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->id }}">{{ ucfirst($role->name) }}</option>
                        @endforeach
                    </select>
                    <x-input-error for="inviteRoleId" />
                </div>
                <div class="mt-4 d-flex justify-content-between">
                    <button type="button" wire:click="cancel" class="btn btn-secondary">Cancel</button>
                    <button type="submit" class="btn btn-navy">Send Invitation <x-spinner for="sendInvite" /></button>
                </div>
            </form>
        </div>
    </x-modal>

    <style>
        .text-navy { color: #001f3f !important; }
        .bg-navy { background-color: #001f3f !important; color: #fff !important; }
        .btn-navy { background-color: #001f3f; color: #fff; border: none; transition: all 0.3s; }
        .btn-navy:hover { background-color: #002d5c; transform: translateY(-1px); color: #fff; }
        .btn-outline-navy { border: 2px solid #001f3f; color: #001f3f; background: transparent; transition: all 0.3s; }
        .btn-outline-navy:hover { background: #001f3f; color: #fff; }
        .bg-soft-navy { background-color: #eef2f7; }
        .badge-pill { border-radius: 50rem; }
        .btn-white { background: #fff; border: 1px solid #dee2e6; }
        .btn-white:hover { background: #f8f9fa; }
    </style>
</div>
