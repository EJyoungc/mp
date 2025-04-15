<div>
    {{-- Because she competes with no one, no one can compete with her. --}}

    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Dashboard</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        {{-- <li class="breadcrumb-item"><a href="#">Home</a></li> --}}
                        <li class="breadcrumb-item active">Dashboard</li>
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
                <div class="col-lg-3">
                    <div class="card">
                        <div class="card-body">
                            <h4>Users </h4>
                            <h6>{{ $users->count() }}</h6>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="card bg-blue ">
                        <div class="card-body">
                            <h4>Mothers</h4>
                            <h6>{{ $mothers->count() }}</h6>

                        </div>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="card bg-success ">
                        <div class="card-body">
                            <h4>Tips</h4>
                            <h6>{{ $tips->count() }}</h6>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="card bg-gradient-dark">
                        <div class="card-body">
                            <h4>Messages Sent </h4>
                            <h6>{{ $messages->count() }}</h6>
                        </div>
                    </div>
                </div>

            </div>
            <div class="row">
                <div class="col-12">
                    <div class="d-flex py-1 justify-content-end">
                        <div class="btn-group">
                            <button class="btn btn-outline-dark" wire:click.prevent="addMothers">Add Mothers <i
                                    class="fas fa-plus"></i> <x-spinner for="addMothers" /></button>
                            <button wire:click.prevent="export" class="btn btn-outline-dark">Download Excel <i
                                    class="fas fa-file-excel"></i> <x-spinner for="export" /> </button>
                            <x-modal title="Add Mothers" :status="$modal">

                                <div>
                                    @if (session()->has('message'))
                                        <div class="alert alert-success mt-2">
                                            {{ session('message') }}
                                        </div>
                                    @endif

                                    <div class="mb-3">
                                        <input type="file" wire:model="file" wire:loading.attr="disabled"
                                            class="form-control">
                                        @error('file')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <button wire:click="preview" wire:loading.attr='disabled'
                                        class="btn btn-secondary mb-3">Preview Data <x-spinner
                                            for="file" /></button>

                                    @if (count($previewData))
                                        <div class="mt-4">
                                            <h4>Data Preview</h4>
                                            <div class="table-responsive">
                                                <table class="table table-hover table-bordered">
                                                    @if (count($previewTitleData))
                                                        <thead class=" thead-dark">
                                                            <tr>
                                                                @foreach ($previewTitleData[0] as $header)
                                                                    <th>{{ $header }}</th>
                                                                @endforeach
                                                            </tr>
                                                        </thead>
                                                    @endif
                                                    <tbody>
                                                        @foreach ($previewData as $row)
                                                            <tr>
                                                                @foreach ($row as $key => $cell)
                                                                    <td>{{ $key == 2 || $key == 3 ? $this->convertDate($cell) : $cell }}
                                                                    </td>
                                                                @endforeach
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                            <button wire:click="confirmImport" class="btn btn-dark">Confirm
                                                Import <x-spinner for="confirmImport" /></button>
                                        </div>
                                    @endif
                                </div>

                            </x-modal>
                        </div>
                    </div>
                    <div class="card">

                        <div class="card-body p-0">
                            <h2 class="py-2 px-2">Mothers</h2>
                            <div class="table-responsive">
                                <table class="table table-hover table-inverse ">
                                    <thead class="thead-inverse">
                                        <tr>
                                            <th>#</th>
                                            <th>Name</th>
                                            <th>Organization</th>
                                            <th>Role</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($mothers as $index => $item)
                                            <tr>
                                                <td scope="row">{{ $index }}</td>
                                                <td class="text-capitalize">{{ $item->name }}</td>
                                                <td>{{ empty($item->organization->name) ? "UNKNOWN" : $item->organization->name }}</td>
                                                <td> <span
                                                        class="badge bg-info text-capitalize">{{ $item->role->name }}</span>
                                                </td>
                                                <td>

                                                    <div class="dropdown open">
                                                        <a class="btn btn-dark dropdown-toggle" type="button"
                                                            id="triggerId" data-toggle="dropdown" aria-haspopup="true"
                                                            aria-expanded="false">
                                                            Options
                                                        </a>
                                                        <div class="dropdown-menu" aria-labelledby="triggerId">
                                                            <a class="dropdown-item"
                                                                href="{{ route('mothers.show', SD::encrypt($item->id)) }}">View</a>
                                                            <a class="dropdown-item " href="#">Edit</a>

                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center" scope="row">EMPTY</td>

                                            </tr>
                                        @endforelse


                                    </tbody>
                                </table>
                            </div>

                        </div>
                    </div>
                </div>
                @if (Auth::user()->role->name == 'system-admin')
                    <div class="col-12">
                        <div class="card">
                            <div class="card-title p-2">
                                <h3>Users Organization Request</h3>
                            </div>
                            <div class="card-body p-0">
                                <x-modal title="Add Organization" status="{{ $modal2 }}">


                                    <div class="d-flex ">
                                        <div class="w-100  ">
                                            <input type="text"
                                                class="form-control border-1 border-primary rounded-pill  "
                                                wire:model.live="search" placeholder="Search for Organization">
                                            <x-input-error for="search" />
                                        </div>



                                    </div>


                                    <ul class="list-group py-1">
                                        @if (!empty($organization->id))
                                            <li
                                                class="list-group-item d-flex justify-content-between align-items-center text-capitalize text-primary border-primary ">
                                                <a href="#">{{ $organization->name }}</a>
                                                <button class="btn btn-outline-danger btn-sm rounded-circle"
                                                    wire:click.prevent="remove_org"><i class="fas fa-times"
                                                        wire:loading.remove wire:target='remove_org'></i> <x-spinner
                                                        for="remove_org" /></button>
                                            </li>
                                        @endif


                                    </ul>
                                    @if (!empty($organization->id))
                                        <button class="btn btn-primary w-100" wire:click.prevent="save">Save
                                            <x-spinner for="save" /></button>
                                    @endif

                                    @if (!empty($search))
                                        <ul class="list-group pt-1">
                                            @forelse ($organizations as $item)
                                                <li class="list-group-item list-group-item-action d-flex align-items-center"
                                                    wire:click.prevent="select_org({{ $item->id }})">
                                                    <i class="fas fa-angle-right"></i> <span
                                                        class="pl-2 text-capitalize">{{ $item->name }}</span>
                                                </li>
                                            @empty

                                                <li
                                                    class="list-group-item d-flex justify-content-center align-items-center ">
                                                    <h4 class="text-muted text-center">EMPTY</h4>

                                                </li>
                                            @endforelse
                                        </ul>
                                    @endif

                                    @if ($liststatus)
                                        <ul class="list-group pt-1">
                                            @forelse ($organizations as $item)
                                                <li class="list-group-item d-flex align-items-center"
                                                    wire:click.prevent="select_org({{ $item->id }})">
                                                    <i class="fas fa-angle-right"></i> <span
                                                        class="pl-2 text-capitalize">{{ $item->name }}</span>
                                                </li>
                                            @empty
                                            @endforelse
                                        </ul>
                                    @endif


                                </x-modal>

                                <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                                    <table class="table table-hover table-inverse ">
                                        <thead class="thead-inverse">
                                            <tr>
                                                <th>#</th>
                                                <th>Name</th>
                                                <th>Email</th>
                                                <th>Organization</th>
                                                <th>Status
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($requests as $index => $item)
                                                <tr>
                                                    <td scope="row">{{ $index }}</td>
                                                    <td>{{ $item->name }}</td>
                                                    <td>{{ $item->email }}</td>
                                                    <td class="text-bold">
                                                        {{ empty($item->organization->name) ? 'N/A' : $item->organization->name }}
                                                    </td>
                                                    <td> <span
                                                            class=" text-capitalize badge bg-{{ $item->organization_verify == 'pending' ? 'warning' : 'success' }}">{{ $item->organization_verify }}</span>
                                                    </td>
                                                    <td>


                                                        <a class="btn btn-dark dropdown-toggle" type="button"
                                                            id="triggerId" data-toggle="dropdown"
                                                            aria-haspopup="true" aria-expanded="false">
                                                            options
                                                        </a>
                                                        <div class="dropdown-menu" aria-labelledby="triggerId">
                                                            <a class="dropdown-item"
                                                                wire:click.prevent='accept({{ $item->id }})'
                                                                href="#">{{ $item->organization_verify == 'pending' ? 'Accept' : 'Decline' }}</a>
                                                            <a class="dropdown-item "
                                                                wire:click.prevent='add_organization({{ $item->id }})'
                                                                href="#">
                                                                Add /Edit Organization</a>

                                                            <a class="dropdown-item "
                                                                wire:click.prevent='remove_organization({{ $item->id }})'
                                                                href="#">
                                                                Remove Organization</a>
                                                        </div>


                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="4" class="text-muted">EMPTY</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>


                            </div>
                        </div>
                    </div>
                @endif


            </div>
        </div>
    </div>
    <!-- /.content -->


</div>
