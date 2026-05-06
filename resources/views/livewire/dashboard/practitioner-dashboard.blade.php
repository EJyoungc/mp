<div>
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Practitioner Dashboard</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item active">Dashboard</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="content">
        <div class="container-fluid">
            <!-- Stats Row -->
            <div class="row">
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3>{{ $mothers->count() }}</h3>
                            <p>Mothers</p>
                        </div>
                        <div class="icon"><i class="fas fa-female"></i></div>
                    </div>
                </div>

                <div class="col-lg-3 col-6">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3>{{ $tips->count() }}</h3>
                            <p>Health Tips</p>
                        </div>
                        <div class="icon"><i class="fas fa-lightbulb"></i></div>
                    </div>
                </div>

                <div class="col-lg-3 col-6">
                    <div class="small-box bg-{{ $analytics['delivery_rate'] > 90 ? 'dark' : 'warning' }}">
                        <div class="inner">
                            <h3>{{ $analytics['delivery_rate'] }}%</h3>
                            <p>SMS Delivery Rate</p>
                        </div>
                        <div class="icon"><i class="fas fa-paper-plane"></i></div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12 mt-4">
                    <div class="card">
                        <div class="card-body p-0">
                            <h2 class="py-2 px-2">Assigned Mothers</h2>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>#</th><th>Name</th><th>Role</th><th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($mothers as $index => $item)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td class="text-capitalize">{{ $item->name }}</td>
                                                <td><span class="badge bg-info text-capitalize">{{ $item->role->name ?? 'N/A' }}</span></td>
                                                <td>
                                                    <a class="btn btn-dark btn-sm" href="{{ route('mothers.show', \App\Helper\StandardData::encrypt($item->id)) }}">View</a>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr><td colspan="4" class="text-center">No mothers assigned.</td></tr>
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
</div>
