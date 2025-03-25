<div>
    {{-- Close your eyes. Count to one. That is how long forever feels. --}}

    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Users</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                        <li class="breadcrumb-item active">User</li>
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
                <div class="col-12 col-lg-3 col-md-4">
                    <div class="card bg-cyan">
                        <div class="card-body">
                            <h4>All Users</h4>
                            <h5>{{ $users->count() }}</h5>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-lg-3 col-md-4">
                    <div class="card bg-teal ">
                        <div class="card-body">
                            <h4>Doctors</h4>
                            <h5>{{ $doctors->count() }}</h5>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-3 col-md-4">
                    <div class="card bg-fuchsia ">
                        <div class="card-body">
                            <h4>Mothers</h4>
                            <h5>{{ $mothers->count() }}</h5>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-3 col-md-4">
                    <div class="card bg-gray ">
                        <div class="card-body">
                            <h4>Practitioner</h4>
                            <h5>{{ $practioners->count() }}</h5>
                        </div>
                    </div>
                </div>

            </div>
            <div class="row">
                <div class="col-12">
                    <div class="d-flex justify-content-end pb-2">

                        <a class="btn btn-dark dropdown-toggle" type="button" id="triggerId" data-toggle="dropdown"
                            aria-haspopup="true" aria-expanded="false">
                            Add
                        </a>
                        <div class="dropdown-menu" aria-labelledby="triggerId">
                            @forelse ($roles as $item)
                                <a class="dropdown-item text-capitalize "
                                    href="{{ route('users.create', $item->name) }}">{{ $item->name }}</a>
                            @empty
                                <a class="dropdown-item disabled" href="#">EMPTY</a>
                            @endforelse



                        </div>
                        <x-modal title="Add user" status="{{ $modal }}">

                        </x-modal>
                    </div>
                    <div class="card">
                        <div class="card-body p-0 ">
                            <div class="table-responsive">
                                <table class="table table-hover table-inverse ">
                                    <thead class="thead-inverse">
                                        <tr>
                                            <th>#</th>
                                            <th>Name</th>
                                            <th>Role</th>
                                            <th>Status</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($users as $item)
                                            <tr>
                                                <td scope="row">{{ $item->id }}</td>
                                                <td class=" text-bold">
                                                    <div class="d-flex">
                                                        <div>
                                                            <svg width="40" height="40" viewBox="0 0 24 24"
                                                                fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                <!-- Circle for the head -->
                                                                <circle cx="12" cy="8" r="4"
                                                                    fill="#4A5568" />

                                                                <!-- Ellipse for the body -->
                                                                <ellipse cx="12" cy="20" rx="7"
                                                                    ry="4" fill="#4A5568" />
                                                            </svg>
                                                        </div>
                                                        <div>
                                                            <span
                                                                class="text-capitalize text-bold d-block">{{ $item->name }}</span>
                                                            <span
                                                                class="  text-muted font-weight-normal">{{ $item->email }}</span>
                                                        </div>

                                                    </div>
                                                </td>
                                                <td> <span
                                                        class="text-capitalize badge bg-{{ $item->role->name === 'system-admin'
                                                            ? 'success'
                                                            : ($item->role->name === 'admin'
                                                                ? 'cyan'
                                                                : ($item->role->name === 'doctor'
                                                                    ? 'teal'
                                                                    : ($item->role->name === 'mother'
                                                                        ? 'fuchsia'
                                                                        : ($item->role->name === 'practitioner'
                                                                            ? 'warning'
                                                                            : 'secondary')))) }} ">{{ $item->role->name }}</span>
                                                </td>
                                                <td>
                                                    <span
                                                        class="text-capitalize badge bg-{{ $item->is_active == 1 ? 'success' : 'danger' }} ">{{ $item->is_active == 1 ? 'Active' : 'Inactive' }}</span>
                                                </td>
                                                <td>

                                                    
                                                    <a class="btn btn-dark btn-sm dropdown-toggle" type="button"
                                                        id="triggerId" data-toggle="dropdown" aria-haspopup="true"
                                                        aria-expanded="false">
                                                        Options
                                                    </a>
                                                    <div class="dropdown-menu" aria-labelledby="triggerId">
                                                        <a class="dropdown-item"
                                                            {{-- wire:click.prevent='edit({{ $item->id }})' --}}
                                                            href="{{ route('users.edit', [$item->role->name, $item->id])  }}">Edit</a>
                                                        <a class="dropdown-item"
                                                            wire:click.prevent='toggleActive({{ $item->id }})'
                                                            href="#">{{ $item->is_active == 1 ? 'Deactivate' : 'Activate' }}
                                                            <x-spinner for="toggleActive({{ $item->id }})" /> </a>

                                                        <a class="dropdown-item"
                                                            wire:click.prevent='resetPassword({{ $item->id }})'
                                                            href="#">Reset
                                                            Password</a>

                                                    </div>

                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td scope="row"></td>
                                                <td></td>
                                                <td></td>
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
</div>
