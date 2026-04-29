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
                                    <textarea type="text" class="form-control" wire:model="tip"></textarea>
                                    <x-input-error for="tip" />

                                </div>
                                <div class="form-group">
                                    <label for="">Day</label>
                                    <select wire:model="day" class="form-control">
                                        <option value="">Select Day</option>
                                        @foreach ($days as $item)
                                            <option value="{{ $item }}">{{ $item }}</option>
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
                                            <th>Status</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($tips as $index => $item)
                                            <tr>
                                                <td scope="row">{{ $item->id }}</td>

                                                <td>
                                                    {{ $item->tip }}
                                                    @if($item->creator)
                                                        <small class="d-block text-muted">By: {{ $item->creator->name }}</small>
                                                    @endif
                                                </td>
                                                <td>{{ $item->day_id }}</td>
                                                <td>
                                                    <div class="text-capitalize">{{ $item->day_range->name }}</div>
                                                    <small class="text-muted d-block">({{ $item->day_range->start_time }} - {{ $item->day_range->end_time }})</small>
                                                </td>
                                                <td>
                                                    @if($item->status === 'approved')
                                                        <span class="badge bg-success">Approved</span>
                                                        @if($item->approver)
                                                            <small class="d-block text-muted">By: {{ $item->approver->name }}</small>
                                                        @endif
                                                    @elseif($item->status === 'pending_approval')
                                                        <span class="badge bg-warning">Pending</span>
                                                    @else
                                                        <span class="badge bg-secondary">{{ $item->status }}</span>
                                                    @endif
                                                </td>
                                                

                                                <td>
                                                    <div class="dropdown open">
                                                        <a class="btn btn-dark btn-sm dropdown-toggle" type="button"
                                                            id="triggerId" data-toggle="dropdown" aria-haspopup="true"
                                                            aria-expanded="false">
                                                            Actions
                                                        </a>
                                                        <div class="dropdown-menu" aria-labelledby="triggerId">
                                                            @if(auth()->user()->isDoctor() && $item->status === 'pending_approval')
                                                                <a class="dropdown-item text-success"
                                                                    wire:click.prevent="approve({{ $item->id }})"
                                                                    href="#"><i class="fas fa-check"></i> Approve</a>
                                                            @endif
                                                            <a class="dropdown-item"
                                                                wire:click.prevent="create({{ $item->id }})"
                                                                href="#"><i class="fas fa-edit"></i> Edit</a>
                                                            @if(auth()->user()->isSystemAdmin())
                                                                <a class="dropdown-item {{ $item->is_template ? 'text-danger' : 'text-primary' }}"
                                                                    wire:click.prevent="markAsTemplate({{ $item->id }})"
                                                                    href="#">
                                                                    <i class="fas fa-star"></i> {{ $item->is_template ? 'Unmark Template' : 'Mark as Template' }}
                                                                </a>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="text-center" scope="row">No tips found for this week.</td>
                                            </tr>
                                        @endforelse

                                    </tbody>
                                </table>
                            </div>

                        </div>
                    </div>

                    @if($templates->count() > 0)
                        <div class="card mt-4">
                            <div class="card-header bg-dark">
                                <h3 class="card-title">Global Templates (Shared)</h3>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Tip</th>
                                                <th>Original Org</th>
                                                <th>Day/Time</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($templates as $template)
                                                <tr>
                                                    <td>{{ $template->tip }}</td>
                                                    <td>{{ $template->organization->name ?? 'System' }}</td>
                                                    <td>
                                                        {{ $template->day_id }} / {{ $template->day_range->name }}
                                                    </td>
                                                    <td>
                                                        <button wire:click="useTemplate({{ $template->id }})" class="btn btn-sm btn-outline-dark">
                                                            <i class="fas fa-copy"></i> Use as Template
                                                        </button>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <!-- /.content -->
</div>
