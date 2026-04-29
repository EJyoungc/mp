<div>
    {{-- Care about people's approval and you will be their prisoner. --}}

    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Session History : </h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Session</li>
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
                <div class="col-lg-4 col-md-4 col-12">
                    <div class="card bg-success ">
                        <div class="card-body">
                            <h4>Messages Sent</h4>
                            <div>{{ $message_history->count() }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-4 col-12">
                    <div class="card bg-dark ">
                        <div class="card-body">
                            <h4>Messages Unsent</h4>
                            <div>{{ $unsent->count() }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-4 col-12">
                    <div class="card bg-danger ">
                        <div class="card-body">
                            <h4>Messages Failed</h4>
                            <div>{{ $failed->count() }}</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body p-0">
                            <h2></h2>
                            <div class="table-responsive">
                                <table class="table table-hover table-inverse ">
                                    <thead class="thead-inverse">
                                        <tr>
                                            <th>#</th>
                                            <th>Tip</th>
                                            <th>Mother</th>
                                            <th>Day</th>
                                            <th>Week</th>
                                            <th>Status</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                        @forelse ($message_history as $item)
                                        <tr>
                                            <td scope="row">{{ $item->id }}</td>
                                            <td>{{Str::limit($item->tip->tip, 10)}}</td>
                                            <td>{{ $item->mother->name }}</td>
                                            <td>{{ $item->day_id }}</td>
                                            <td>{{ $item->week->week }}</td>

                                            <td>
                                                <span class="badge @if($item->message_status == 'sent') bg-success @elseif($item->message_status == 'failed') bg-danger @else bg-dark @endif">
                                                    {{ strtoupper($item->message_status) }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="dropdown open">
                                                    <a class="btn btn-secondary dropdown-toggle" type="button" id="triggerId" data-toggle="dropdown" aria-haspopup="true"
                                                            aria-expanded="false">
                                                                option
                                                </a>
                                                    <div class="dropdown-menu" aria-labelledby="triggerId">
                                                        <a class="dropdown-item "wire:click.prevent="resend({{ $item->id }})"  href="#">Resend</a>
                                                        @if($item->api_response)
                                                            <a class="dropdown-item" wire:click.prevent="showLog({{ $item->id }})" href="#">View Log</a>
                                                        @endif
                                                        {{-- <a class="dropdown-item disabled" href="#">Disabled action</a> --}}
                                                        <a class="dropdown-item "wire:click.prevent="test"  href="#">Test</a>

                                                    </div>
                                                </div>

                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td class="text-center text-muted h4" colspan="7"  >EMPTY</td>

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

    <!-- API Log Modal -->
    @if($logModal)
    <div class="modal fade show" style="display: block; background: rgba(0,0,0,0.5);" id="logModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-info">
                    <h5 class="modal-title">API Response Log</h5>
                    <button type="button" class="close" wire:click="cancel()" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    @if($selectedLog)
                        <div class="row">
                            <div class="col-md-6">
                                <strong>Timestamp:</strong> {{ $selectedLog['timestamp'] ?? 'N/A' }}
                            </div>
                            <div class="col-md-6">
                                <strong>Status:</strong> 
                                <span class="badge {{ ($selectedLog['status'] ?? '') == 'Success' ? 'bg-success' : 'bg-danger' }}">
                                    {{ $selectedLog['status'] ?? 'Unknown' }}
                                </span>
                            </div>
                        </div>
                        <hr>
                        <h6>Full Response Data:</h6>
                        <div class="bg-light p-3 rounded" style="max-height: 400px; overflow-y: auto;">
                            <pre><code>{{ json_encode($selectedLog, JSON_PRETTY_PRINT) }}</code></pre>
                        </div>
                    @else
                        <p class="text-center">No log data available.</p>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" wire:click="cancel()">Close</button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
