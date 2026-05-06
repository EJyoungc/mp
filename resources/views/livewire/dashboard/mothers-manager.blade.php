<div class="col-12">
    <div class="d-flex py-3 justify-content-end align-items-center">
        <div class="btn-group">
            <a href="{{ route('users.create', 'mother') }}" class="btn btn-navy shadow-sm mr-2 rounded-pill px-4">
                <i class="fas fa-user-plus mr-1"></i> Add Mother
            </a>
            <button class="btn btn-outline-navy shadow-sm mr-2 rounded-pill px-4" wire:click.prevent="addMothers">
                <i class="fas fa-file-import mr-1"></i> Bulk Import <x-spinner for="addMothers" />
            </button>
            <button wire:click.prevent="export" class="btn btn-outline-secondary shadow-sm rounded-pill px-4">
                <i class="fas fa-file-excel mr-1"></i> Download Template <x-spinner for="export" />
            </button>
        </div>

        <x-modal title="Bulk Import Mothers" :status="$modal">
            <div>
                @if (session()->has('message'))
                    <div class="alert alert-success mt-2">{{ session('message') }}</div>
                @endif
                @if(auth()->user()->isSystemAdmin())
                    <div class="mb-3">
                        <label>Select Organization</label>
                        <select wire:model="organization_id" class="form-control">
                            <option value="">Select Organization</option>
                            @foreach($organizations as $org)
                                <option value="{{ $org->id }}">{{ $org->name }}</option>
                            @endforeach
                        </select>
                        @error('organization_id') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                @endif
                <div class="mb-3">
                    <input type="file" wire:model="file" wire:loading.attr="disabled" class="form-control">
                    @error('file') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
                <button wire:click="preview" wire:loading.attr='disabled' class="btn btn-secondary mb-3">Preview Data <x-spinner for="file" /></button>
                @if (count($previewData))
                    <div class="mt-4">
                        <h4>Data Preview</h4>
                        <div class="table-responsive">
                            <table class="table table-hover table-bordered">
                                @if (count($previewTitleData))
                                    <thead class="thead-dark">
                                        <tr>@foreach ($previewTitleData[0] as $header) <th>{{ $header }}</th> @endforeach</tr>
                                    </thead>
                                @endif
                                <tbody>
                                    @foreach ($previewData as $row)
                                        <tr>@foreach ($row as $key => $cell) <td>{{ $key == 2 || $key == 3 ? $this->convertDate($cell) : $cell }}</td> @endforeach</tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <button wire:click="confirmImport" class="btn btn-dark">Confirm Import <x-spinner for="confirmImport" /></button>
                    </div>
                @endif
            </div>
        </x-modal>
    </div>

    <div class="card mt-4">
        <div class="card-body p-0">
            <h2 class="py-2 px-2">Mothers</h2>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>#</th><th>Name</th>@if(auth()->user()->isSystemAdmin())<th>Organization</th>@endif<th>Role</th><th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($mothers as $index => $item)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td class="text-capitalize">{{ $item->name }}</td>
                                @if(auth()->user()->isSystemAdmin())<td>{{ $item->organization->name ?? "UNKNOWN" }}</td>@endif
                                <td><span class="badge bg-info text-capitalize">{{ $item->role->name ?? 'N/A' }}</span></td>
                                <td>
                                    <div class="dropdown">
                                        <a class="btn btn-dark btn-sm dropdown-toggle" href="#" role="button" data-toggle="dropdown">Options</a>
                                        <div class="dropdown-menu">
                                            <a class="dropdown-item" href="{{ route('mothers.show', \App\Helper\StandardData::encrypt($item->id)) }}">View</a>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center">EMPTY</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <style>
        .btn-navy { background-color: #001f3f; color: #fff; border: none; }
        .btn-outline-navy { border: 2px solid #001f3f; color: #001f3f; }
        .rounded-pill { border-radius: 50rem !important; }
    </style>
</div>
