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
                <div class="d-flex py-2 justify-content-end">
                    {{-- <button class="btn btn-dark">Add <x-spinner for="" /></button> --}}
                </div>
                <div class="card bg-info  ">
                    <div class="card-body  p-0">
                        <div class="card-header">
                            <h2>Weeks</h2>
                        </div>
                        <div class="table-responsive table-info">
                            <table class="table table-hover table-inverse ">
                            <thead class="thead-inverse">
                                <tr>
                                    
                                    <th># Weeks</th>
                                    <th>Tips</th>
                                    <th></th>
                                </tr>
                                </thead>
                                <tbody>
                                    @forelse ($weeks as $item)
                                    <tr>
                                        
                                        
                                        <td class="text-info"> Week {{ $item->week }}</td>
                                        <td><span class="badge bg-success" >{{ $item->tips->count() }}</span></td>
                                        <td>

                                            <div class="dropdown open">
                                                <a class="btn btn-dark dropdown-toggle" type="button" id="triggerId" data-toggle="dropdown" aria-haspopup="true"
                                                        aria-expanded="false">
                                                            Options
                                            </a>
                                                <div class="dropdown-menu" aria-labelledby="triggerId">
                                                    <a class="dropdown-item" href="{{ route('trimester.week.show', [$trimester->id, $item->id]) }}">View </a>
                                                    
                                                </div>
                                            </div>


                                        </td>
                                    </tr>    
                                    @empty
                                    <tr>
                                        <td scope="row"></td>
                                        
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
