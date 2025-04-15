<div>
    {{-- A good traveler has no fixed plans and is not intent upon arriving. --}}

    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Organizations</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Organization</a></li>
                        {{-- <li class="breadcrumb-item active">Starter Page</li> --}}
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
                <div class="d-flex justify-content-end">
                    <div>
                        <button wire:click.prevent='create' class="btn btn-dark">add <x-spinner for="create" /> </button>
                    </div>

                    <x-modal status="{{$user_modal}}" title="Organization Users" >

                    <div class="d-flex justify-content-end ">
                        <div class="d-flex">
                            <div class="form-group">

                                <input type="text" class="form-control" wire:model="search" placeholder="Search">
                            </div>
                            <button class="btn btn-sm btn-dark" >add</button>
                        </div>
                    </div>
                    <div class="list-group">
                        <a href="#" class="list-group-item list-group-item-action active">Active item</a>
                        <a href="#" class="list-group-item list-group-item-action">Item</a>
                        <a href="#" class="list-group-item list-group-item-action disabled">Disabled item</a>
                    </div>

                    </x-modal>

                    <x-modal status="{{ $modal }}" title="Add Organization" >

                        <form wire:submit.prevent="store">
                            <div class="form-group">
                                <label for="">Name</label>
                                <input type="text" class="form-control" wire:model="name" placeholder="Enter name">
                                <x-input-error for="name" />
                            </div>
                            <div class="form-group">
                                <label for="">Email</label>
                                <input type="email" class="form-control" wire:model="email" placeholder="Enter email">
                                <x-input-error for="email" />
                            </div>
                            <div class="form-group">
                                <label for="">Phone</label>
                                <input type="text" class="form-control" wire:model="phone" placeholder="Enter phone">
                                <x-input-error for="phone" />
                            </div>
                            <div class="form-group">
                                <label for="">Address</label>
                                <input type="text" class="form-control" wire:model="address" placeholder="Enter address">
                                <x-input-error for="address" />
                            </div>
                            <div class="form-group">
                                <label for="">Website</label>
                                <input type="text" class="form-control" wire:model="website" placeholder="Enter website">
                                <x-input-error for="website" />
                            </div>

                            <div class="form-group">
                                <label for="">Description</label>
                                <textarea class="form-control" wire:model="description" placeholder="Enter description"></textarea>
                                <x-input-error for="description" />
                            </div>

                            <button type="submit" class="btn btn-dark" >Save <x-spinner for="store" /> </button>
                        </form>

                    </x-modal>
                </div>
                <div class="card">
                    <div class="card-body">
                        <h2></h2>
                        <div class="table-responsive">
                            <table class="table table-hover table-inverse ">
                            <thead class="thead-inverse">
                                <tr>
                                    <th>#</th>
                                    <th>Name</th>
                                    <th>Users</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Address</th>
                                    <th>Website</th>
                                    <th>Description</th>
                                    <th></th>
                                </tr>
                                </thead>
                                <tbody>

                                    @forelse ($orgs as $item )
                                    <tr>
                                        <td scope="row">{{ $item->id }}</td>
                                        <td>{{ $item->name }}</td>
                                        <td><span class="badge bg-success" ></span></td>
                                        <td>{{ $item->email }}</td>
                                        <td>{{ $item->phone }}</td>
                                        <td class="text-capitalize" >{{ $item->address }}</td>
                                        <td> <a href="{{ empty($item->website) ? '#' : $item->website }}" {{ empty($item->website) ? 'disabled' : '' }} >{{ $item->name }}</a> </td>
                                        <td>{{ $item->description }}</td>
                                        <td>
                                            <a href="{{ route('organizations.users',$item->id) }}" class="btn btn-info" >View </a>
                                            <button wire:click.prevent='create({{ $item->id }})' class="btn btn-success">edit <x-spinner for="edit" /> </button>
                                            <button wire:click.prevent='delete({{ $item->id }})' class="btn btn-danger">delete <x-spinner for="delete" /> </button>
                                        </td>
                                    </tr>
                                    @empty

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
