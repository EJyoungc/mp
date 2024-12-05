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

           <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <form wire:submit.prevent="store">
                                @if ($role == 'admin')
                                    <x-inputs.users.admin/>
                                @endif

                                @if ($role == 'mother')
                                    
                                <x-inputs.users.mother/>
                                @endif
                                @if ($role == 'doctor')
                                    
                                <x-inputs.users.doctor/>
                                @endif
                                @if ($role == 'practitioner')
                                    
                                <x-inputs.users.practitioner/>
                                @endif
                          
                            
                            <button type="submit" class="btn btn-dark">Save <x-spinner for="store" /> </button>
                        </form>                    
                    </div>
                </div>
            </div>
           </div>
        </div>
    </div>
    <!-- /.content -->
</div>
