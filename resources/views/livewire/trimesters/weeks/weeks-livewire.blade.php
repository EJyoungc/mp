<div>
    {{-- Success is as dangerous as failure. --}}

    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Trimester {{ $trimester->trimester }}</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                        <li class="breadcrumb-item active">Timester {{ $trimester->trimester }} </li>
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
                <div class="col-12 col-lg-4 col-md-6">
                    <div class="card card-fuchsia">
                        <div class="card-body">
                            <h3> <i class="fa fa-calendar" aria-hidden="true"></i> Weeks </h3>
                            <h1>{{ $weeks->count() }}</h1>
                        </div>
                    </div>
                </div>
            </div>
           <div class="row">
            <div class="col-12">
                <div class="d-flex py-3 justify-content-end">
                    <button wire:click="create" class="btn btn-navy shadow-sm px-4 rounded-pill">
                        <i class="fas fa-plus-circle mr-2"></i> Bulk Create Tips <x-spinner for="create" />
                    </button>
                </div>

                <x-modal title="Bulk Create Tips (Trimester {{ $trimester->trimester }})" :status="$modal">
                    <form wire:submit.prevent="storeBulk">
                        <div class="row">
                            <!-- Weeks Selection -->
                            <div class="col-md-4">
                                <label class="text-navy font-weight-bold">Select Weeks</label>
                                <div class="border rounded p-2" style="max-height: 200px; overflow-y: auto;">
                                    <div class="custom-control custom-checkbox mb-2 pb-2 border-bottom">
                                        <input type="checkbox" class="custom-control-input" id="selectAllWeeks" wire:model.live="selectAllWeeks">
                                        <label class="custom-control-label font-weight-bold" for="selectAllWeeks">Select All Weeks</label>
                                    </div>
                                    @foreach($weeks as $week)
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" id="week-{{ $week->id }}" value="{{ $week->id }}" wire:model="selectedWeeks">
                                            <label class="custom-control-label" for="week-{{ $week->id }}">Week {{ $week->week }}</label>
                                        </div>
                                    @endforeach
                                </div>
                                @error('selectedWeeks') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>

                            <!-- Days Selection -->
                            <div class="col-md-4">
                                <label class="text-navy font-weight-bold">Select Days</label>
                                <div class="border rounded p-2" style="max-height: 200px; overflow-y: auto;">
                                    <div class="custom-control custom-checkbox mb-2 pb-2 border-bottom">
                                        <input type="checkbox" class="custom-control-input" id="selectAllDays" wire:model.live="selectAllDays">
                                        <label class="custom-control-label font-weight-bold" for="selectAllDays">Select All Days</label>
                                    </div>
                                    @foreach($availableDays as $day)
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" id="day-{{ $day->id }}" value="{{ $day->id }}" wire:model="selectedDays">
                                            <label class="custom-control-label" for="day-{{ $day->id }}">Day {{ $day->day_number }}</label>
                                        </div>
                                    @endforeach
                                </div>
                                @error('selectedDays') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>

                            <!-- Ranges Selection -->
                            <div class="col-md-4">
                                <label class="text-navy font-weight-bold">Select Time Ranges</label>
                                <div class="border rounded p-2" style="max-height: 200px; overflow-y: auto;">
                                    <div class="custom-control custom-checkbox mb-2 pb-2 border-bottom">
                                        <input type="checkbox" class="custom-control-input" id="selectAllRanges" wire:model.live="selectAllRanges">
                                        <label class="custom-control-label font-weight-bold" for="selectAllRanges">Select All Ranges</label>
                                    </div>
                                    @foreach($availableRanges as $range)
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" id="range-{{ $range->id }}" value="{{ $range->id }}" wire:model="selectedRanges">
                                            <label class="custom-control-label" for="range-{{ $range->id }}">{{ $range->name }} ({{ $range->start_time }} - {{ $range->end_time }})</label>
                                        </div>
                                    @endforeach
                                </div>
                                @error('selectedRanges') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                        </div>

                        <div class="form-group mt-4">
                            <label class="text-navy font-weight-bold">Tip Content <span class="text-danger">*</span></label>
                            <textarea wire:model="tipContent" class="form-control" rows="4" placeholder="Enter the tip that will be applied to all selected combinations..."></textarea>
                            @error('tipContent') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="modal-footer px-0 pb-0 mt-4">
                            <button type="button" class="btn btn-secondary rounded-pill px-4" wire:click="cancel">Cancel</button>
                            <button type="submit" class="btn btn-navy shadow-sm px-4 rounded-pill">
                                <i class="fas fa-save mr-1"></i> Generate Bulk Tips <x-spinner for="storeBulk" />
                            </button>
                        </div>
                    </form>
                </x-modal>

                <div class="card shadow-sm border-0 rounded-lg overflow-hidden mt-2">
                    <div class="card-body p-0">
                        <div class="card-header bg-light border-0 py-3">
                            <h2 class="card-title text-navy font-weight-bold mb-0">Weeks List</h2>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-light text-navy">
                                    <tr>
                                        <th class="px-4 py-3 border-0"># Weeks</th>
                                        <th class="py-3 border-0 text-center">Tips Count</th>
                                        <th class="py-3 border-0 text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($weeks as $item)
                                        <tr>
                                            <td class="px-4 py-3 border-top-0">
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-circle bg-soft-navy text-navy mr-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; border-radius: 50%; background: #eef2f7; font-weight: bold;">
                                                        W
                                                    </div>
                                                    <h6 class="mb-0 font-weight-bold text-navy">Week {{ $item->week }}</h6>
                                                </div>
                                            </td>
                                            <td class="py-3 border-top-0 text-center">
                                                <span class="badge badge-pill badge-success px-3 py-2" style="font-weight: 500;">
                                                    {{ $item->tips->count() }} Tips
                                                </span>
                                            </td>
                                            <td class="py-3 border-top-0 text-center">
                                                <a href="{{ route('trimester.week.show', [$trimester->id, $item->id]) }}" class="btn btn-navy btn-sm px-4 rounded-pill shadow-sm">
                                                    <i class="fas fa-eye mr-1"></i> View Details
                                                </a>
                                            </td>
                                        </tr>    
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center py-5">
                                                <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                                                <h5 class="text-muted">No weeks found for this trimester.</h5>
                                            </td>
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
        .text-navy { color: #001f3f !important; }
        .bg-soft-navy { background-color: #eef2f7; }
        .btn-navy { background-color: #001f3f; color: #fff; border: none; }
        .btn-navy:hover { background-color: #002d5c; color: #fff; }
        .rounded-pill { border-radius: 50rem !important; }
        .custom-control-input:checked ~ .custom-control-label::before {
            background-color: #001f3f;
            border-color: #001f3f;
        }
    </style>
</div>
        </div>
    </div>
    <!-- /.content -->
</div>
