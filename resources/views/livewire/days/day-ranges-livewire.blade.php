<div>
    {{-- Do your work, then step back. --}}

    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Day Ranges</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Days</li>
                        <li class="breadcrumb-item active">Day Ranges</li>
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
                        <button class="btn btn-primary" wire:click.prevent="create">add <x-spinner for="create" />
                        </button>
                        <x-modal status="{{ $modal }}" title="Add Day Range">
                            <form wire:submit.prevent="store">
                                <div class="form-group">
                                    <label for="">Name</label>
                                    <input type="text" class="form-control" wire:model="name" placeholder="name">
                                    <x-input-error for="name" />
                                </div>
                                <div class="form-group">
                                    <label for="">Start time</label>
                                    <input type="time" class="form-control" wire:model="start_time">
                                    <x-input-error for="start_time" />


                                </div>
                                <div class="form-group">
                                    <label for="">End time</label>
                                    <input type="time" class="form-control" wire:model="end_time">
                                    <x-input-error for="end_time" />
                                </div>
                                <button type="submit" class="btn btn-dark">save <x-spinner for="store" /></button>
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
                                            <th>Start Time</th>
                                            <th>End Time</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($ranges as $item)
                                            <tr>
                                                <td>{{ $item->id }}</td>
                                                <td>{{ $item->name }}</td>
                                                <td scope="row">{{ $item->start_time }}</td>
                                                <td>{{ $item->end_time }}</td>
                                                <td>
                                                   
                                                        <a class="btn btn-secondary dropdown-toggle" type="button"
                                                            id="triggerId" data-toggle="dropdown" aria-haspopup="true"
                                                            aria-expanded="false">
                                                            options
                                                        </a>
                                                        <div class="dropdown-menu" aria-labelledby="triggerId">
                                                            <a class="dropdown-item" wire:click.prevent="create({{ $item->id }})" href="#">Edit</a>
                                                            
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
