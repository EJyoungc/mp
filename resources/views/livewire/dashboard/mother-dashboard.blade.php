<div>
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">My Dashboard</h1>
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
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <h3>Welcome, {{ auth()->user()->name }}</h3>
                            <p>You can view your profile and pregnancy details here.</p>
                            <a href="{{ route('mothers.show', \App\Helper\StandardData::encrypt(auth()->id())) }}" class="btn btn-primary">View My Profile</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
