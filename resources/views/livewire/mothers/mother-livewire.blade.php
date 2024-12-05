<div>
    {{-- Knowing others is intelligence; knowing yourself is true wisdom. --}}

    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Mother : {{ $mother_id->name }}</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Mother : {{ $mother_id->name }}</li>
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
                <div class="col-lg-6 col-6 ">
                    <div class="card bg-primary">
                        <div class="card-body">
                            <h4>Sessions</h4>
                            <h5 class="float-right" >{{ $history->count() }}</h5>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 col-6 ">
                    <div class="card bg-dark">
                        <div class="card-body">
                            <h4>Messages</h4>
                            <h5 class="float-right" >{{ $messages->count() }}</h5>

                            <h5 class="float-right d-none" wire:poll.5s='test' >{{$count}}</h5>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="d-flex py-2 justify-content-end">
                        <div class="form-group">
                            {{-- <input type="text" class="form-control" placeholder="Search" wire:model="search"> --}}

                        </div>
                        <div>
                            <button class="btn btn-primary" wire:click.prevent='create'>Add Session  <x-spinner for="create" /> </button>

                        </div>
                        <x-modal title="Add Session" status="{{ $modal }}">
                            <form wire:submit.prevent="store">

                                <div class="form-group">
                                    <label for="name">Infant Number</label>
                                    <input type="text" class="form-control" wire:model="infant_number">
                                    <x-input-error for="infant_number" class="mt-2" />

                                </div>
                                <div class="form-group">
                                    <label for="date">Last Menstrual Cycle</label>
                                    <input type="date" class="form-control" wire:model="last_menstrual_cycle">
                                    <x-input-error for="last_menstrual_cycle" class="mt-2" />
                                </div>
                                <button class="btn btn-dark">Save <x-spinner for="store" /></button>
                            </form>
                        </x-modal>
                    </div>
                    <div class="card">
                        <div class="card-header">
                            <h4>History</h4>
                        </div>
                        <div class="card-body p-0">

                            <div class="table-responsive">
                                <table class="table table-hover table-inverse ">
                                    <thead class="thead-inverse">
                                        <tr>
                                            <th>#</th>
                                            <th>Infant Number</th>
                                            <th>Last Menstrual Cycle</th>
                                            
                                            <th>Week</th>
                                            <th>Day</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                      @forelse ($history as $index => $item)
                                      <tr>
                                        <td >{{ $index + 1 }}</td>
                                        
                                        <td>{{ $item->infant_number }}</td>
                                        <td>{{ $item->last_menstrual_cycle }}</td>
                                       
                                        {{-- <td>@dump( $item->calculate_week() )</td> --}}
                                        <td>{{$item->calculate_week()['weeks']}}</td>
                                        <td>{{$item->calculate_week()['day_of_week']}}</td>
                                        <td>
                                            <div class="dropdown open">
                                                <a class="btn btn-secondary dropdown-toggle" type="button" id="triggerId" data-toggle="dropdown" aria-haspopup="true"
                                                        aria-expanded="false">
                                                            Option
                                                        </a>
                                                <div class="dropdown-menu" aria-labelledby="triggerId">
                                                    <a class="dropdown-item"  href="{{ route('mothers.history.show', [$mother_id->id,$item->id]) }}">View message History</a>
                                                    <a class="dropdown-item" wire:click.prevent='create({{ $item->id }})' href="#">Edit</a>
                                                   
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                      @empty
                                      <tr>
                                        <td colspan="4" class="text-center text-muted h3">No Data Found</td></td>
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
