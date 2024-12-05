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
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Starter Page</li>
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
                    <div class="card">
                        <div class="card-body">
                            <h2>Mothers</h2>
                            <div class="table-responsive">
                                <table class="table table-hover table-inverse ">
                                    <thead class="thead-inverse">
                                        <tr>
                                            <th>#</th>
                                            <th>Name</th>
                                            <th>Role</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($mothers as $item)
                                            <tr>
                                                <td scope="row">{{ $item->id }}</td>
                                                <td>{{ $item->name }}</td>
                                                <td> <span class="badge bg-info text-capitalize" >{{ $item->role->name }}</span></td>
                                                <td>

                                                    <div class="dropdown open">
                                                        <a class="btn btn-dark dropdown-toggle" type="button"
                                                            id="triggerId" data-toggle="dropdown" aria-haspopup="true"
                                                            aria-expanded="false">
                                                            Options
                                                        </a>
                                                        <div class="dropdown-menu" aria-labelledby="triggerId">
                                                            <a class="dropdown-item" href="{{ route('mothers.show', SD::encrypt($item->id)) }}">View</a>
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
            </div>
        </div>
    </div>
    <!-- /.content -->


</div>
