<div>
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-bold text-navy">System Settings</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Settings</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-6">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-white border-bottom-0 py-3">
                            <h3 class="card-title text-bold text-navy">
                                <i class="fas fa-clock mr-2"></i> Time Configuration
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-info bg-soft-navy text-navy border-0 mb-4">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-info-circle fa-2x mr-3"></i>
                                    <div>
                                        <p class="mb-0">Current time in selected timezone:</p>
                                        <h4 class="mb-0 font-weight-bold" wire:poll.10s="updateServerTime">{{ $serverTime }}</h4>
                                    </div>
                                </div>
                            </div>

                            <form wire:submit.prevent="saveTimezone">
                                <div class="form-group">
                                    <label for="timezone" class="font-weight-bold">Application Timezone</label>
                                    <select wire:model="timezone" id="timezone" class="form-control select2 shadow-sm border-light">
                                        @foreach($timezones as $tz)
                                            <option value="{{ $tz }}">{{ $tz }}</option>
                                        @endforeach
                                    </select>
                                    <small class="text-muted mt-2 d-block">
                                        This timezone will be used for all scheduled SMS tip deliveries and system timestamps.
                                    </small>
                                </div>

                                <div class="mt-4">
                                    <button type="submit" class="btn btn-navy rounded-pill px-4 shadow-sm">
                                        <i class="fas fa-save mr-2"></i> Save Settings
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-white border-bottom-0 py-3">
                            <h3 class="card-title text-bold text-navy">
                                <i class="fas fa-shield-alt mr-2"></i> System Info
                            </h3>
                        </div>
                        <div class="card-body">
                            <table class="table table-borderless table-sm">
                                <tr>
                                    <th class="text-muted w-50">Laravel Version</th>
                                    <td class="font-weight-bold">{{ app()->version() }}</td>
                                </tr>
                                <tr>
                                    <th class="text-muted w-50">PHP Version</th>
                                    <td class="font-weight-bold">{{ PHP_VERSION }}</td>
                                </tr>
                                <tr>
                                    <th class="text-muted w-50">Database Engine</th>
                                    <td class="font-weight-bold">SQLite</td>
                                </tr>
                                <tr>
                                    <th class="text-muted w-50">Default Config Timezone</th>
                                    <td class="font-weight-bold">{{ config('app.timezone') }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .text-navy { color: #001f3f !important; }
        .btn-navy { background-color: #001f3f; color: #fff; border: none; transition: all 0.3s; }
        .btn-navy:hover { background-color: #002d5c; transform: translateY(-1px); color: #fff; }
        .bg-soft-navy { background-color: #eef2f7; }
    </style>
</div>
