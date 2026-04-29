<div>
    {{-- The Master doesn't talk, he acts. --}}

    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0  text-capitalize ">Add {{ $role }}</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('users') }}">Users</a></li>
                        <li class="breadcrumb-item active">Add {{ $role }}</li>
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->
    <!-- Main content -->
    <div class="content">
        <div class="container-fluid">
           <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card card-navy card-outline shadow-sm border-0 rounded-lg">
                    <div class="card-header bg-white border-bottom-0 pt-4 px-4">
                        <h3 class="card-title text-bold"><i class="fas fa-user-plus mr-2 text-navy"></i> Registration Details</h3>
                    </div>
                    <div class="card-body px-4 pb-4">
                        <form wire:submit.prevent="store">
                            <div class="alert bg-soft-navy text-navy border mb-4">
                                <i class="fas fa-info-circle mr-2"></i>
                                You are registering a new <strong>{{ ucfirst($role) }}</strong>.
                                @if($role !== 'mother')
                                    An automated secure password will be generated and emailed to the user.
                                @endif
                            </div>

                            @if ($role == 'admin')
                                <x-inputs.users.admin/>
                            @endif

                            @if ($role == 'doctor')
                                <x-inputs.users.doctor/>
                            @endif

                            @if ($role == 'practitioner')
                                <x-inputs.users.practitioner/>
                            @endif

                            @if ($role == 'mother')
                                <x-inputs.users.mother :districts="$districts" :areas="$areas" :district-id="$district_id"/>
                            @endif

                            <div class="mt-4 border-top pt-4 text-right">
                                <a href="{{ route('users') }}" class="btn btn-light rounded-pill px-4 mr-2">Cancel</a>
                                <button type="submit" class="btn btn-navy rounded-pill px-5 shadow-sm">
                                    <i class="fas fa-save mr-1"></i> Register {{ ucfirst($role) }} <x-spinner for="store" />
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
           </div>
        </div>
    </div>

    <style>
        .text-navy { color: #001f3f !important; }
        .bg-soft-navy { background-color: #eef2f7; }
        .card-navy.card-outline { border-top: 3px solid #001f3f; }
        .btn-navy { background-color: #001f3f; color: #fff; border: none; transition: all 0.3s; }
        .btn-navy:hover { background-color: #002d5c; transform: translateY(-1px); color: #fff; }
        .rounded-pill { border-radius: 50rem !important; }
    </style>

    <!-- Modal for Adding New Area -->
    <x-modal title="Add New Area" status="{{ $areaModal }}">
        <form wire:submit.prevent="storeArea">
            <div class="form-group">
                <label>District</label>
                <input type="text" class="form-control" value="{{ $districts->find($district_id)->name ?? '' }}" readonly>
            </div>
            <div class="form-group">
                <label>Area Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control" wire:model="new_area_name" placeholder="Enter area name">
                @error('new_area_name') <small class="text-danger">{{ $message }}</small> @enderror
            </div>
            <div class="modal-footer px-0 pb-0">
                <button type="button" class="btn btn-secondary" wire:click="$set('areaModal', false)">Cancel</button>
                <button type="submit" class="btn btn-navy shadow-sm px-4">Save Area</button>
            </div>
        </form>
    </x-modal>
    <!-- /.content -->
</div>
