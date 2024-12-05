<div>
    {{-- If you look to others for fulfillment, you will never truly be fulfilled. --}}

    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Week {{ $week->week }}</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('trimester.weeks', $trimester_id) }}">Trimester
                                {{ $trimester_id }}</a></li>
                        <li class="breadcrumb-item active">Week {{ $week_id }}</li>
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

                <div class="col-12 col-lg-6 col-md-6">
                    <div class="card bg-success">
                        <div class="card-body">
                            <h3> <i class="fa fa-calendar" aria-hidden="true"></i> Tips </h3>
                            <h1>{{ $tips->count() }}</h1>
                        </div>
                    </div>
                </div>

            </div>
            <div class="row">
                <div class="col-12">
                    <div class="d-flex justify-content-end">
                        <button wire:click="create" class="btn btn-dark">Add Tips <x-spinner
                                for="create" /></button>
                        <x-modal title="Add Tip" status="{{ $modal }}">

                            <form wire:submit.prevent="store">


                                <div class="form-group">
                                    <label for="name">Tips</label>
                                    <input type="text" class="form-control" wire:model="tip">
                                    <x-input-error for="tip" />

                                </div>
                                <div class="form-group">
                                    <label for="">Day</label>
                                    <select wire:model="day" class="form-control">
                                        <option value="">Select Day</option>
                                        @foreach ($days as $item)
                                            <option value="{{ $item->id }}">{{ $item->day_number }}</option>
                                        @endforeach
                                    </select>
                                    <x-input-error for="day" />
                                </div>

                                <div class="form-group">
                                    <label for="">Time</label>
                                    <select wire:model="time" class="form-control" >
                                        <option value="">Select</option>
                                        @foreach ($day_ranges as $item)
                                            <option value="{{ $item->id }}">{{ $item->name }} ({{ $item->start_time }} - {{ $item->end_time }})</option>
                                        @endforeach
                                    </select>
                                    <x-input-error for="time" />
                                </div>


                                <button type="submit" class="btn btn-dark">save <x-spinner for="store_symptoms" />
                                </button>
                            </form>
                        </x-modal>

                    </div>
                    <div class="card">
                        <div class="card-body p-0">
                            <h2></h2>
                            <div class="table-responsive">
                                <table class="table table-hover table-inverse ">
                                    <thead class="thead-inverse">
                                        <tr>
                                            <th>#</th>
                                            <th>Tips</th>
                                            <th>Day</th>
                                            <th>Time</th>
                                            <th>Week</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($tips as $index => $item)
                                            <tr>
                                                <td scope="row">{{ $item->id }}</td>

                                                <td>{{ $item->tip }}</td>
                                                <td>{{ $item->day->day_number }}</td>
                                                <td>
                                                    <div class="text-capitalize"  >{{ $item->day_range->name }}</div>
                                                <small class=" text-muted d-block" >({{ $item->day_range->start_time }} - {{ $item->day_range->end_time }})</small>
                                                </td>
                                                <td><div class="badge bg-success">{{ $item->week->week }}</div></td>
                                                

                                                <td>
                                                    <div class="dropdown open">
                                                        <a class="btn btn-dark dropdown-toggle" type="button"
                                                            id="triggerId" data-toggle="dropdown" aria-haspopup="true"
                                                            aria-expanded="false">
                                                            options
                                                        </a>
                                                        <div class="dropdown-menu" aria-labelledby="triggerId">
                                                            {{-- <a class="dropdown-item" href="#">Add Tip</a> --}}
                                                            <a class="dropdown-item"
                                                                wire:click.prevent="create({{ $item->id }})"
                                                                href="#">Edit</a>
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
            </div>
        </div>
    </div>
    <!-- /.content -->
</div>
