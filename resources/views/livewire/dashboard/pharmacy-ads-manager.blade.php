<div class="col-12 mt-4">
    <div class="card">
        <div class="card-header bg-gradient-navy">
            <h3 class="card-title"><i class="fas fa-pills mr-2"></i> Pharmacy Advertisements {{ auth()->user()->isSystemAdmin() ? '(Monetization)' : '' }}</h3>
            <div class="card-tools">
                <button class="btn btn-sm btn-light" wire:click="createAd"><i class="fas fa-plus mr-1"></i> New Ad</button>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr><th>Product</th><th>Message</th><th>Target</th><th>Schedule</th><th>Total Sent</th><th>Status</th><th>Action</th></tr>
                    </thead>
                    <tbody>
                        @forelse($ads as $ad)
                            <tr>
                                <td><strong>{{ $ad->product_name }}</strong></td>
                                <td>{{ Str::limit($ad->ad_message, 50) }}</td>
                                <td>{{ $ad->trimester_id ? 'Trimester '.$ad->trimester->trimester : 'Week '.$ad->target_week_start.'-'.$ad->target_week_end }}</td>
                                <td><span class="badge badge-secondary">{{ ucfirst($ad->schedule_type ?? 'Daily') }} ({{ $ad->schedule_limit ?? 1 }}x)</span></td>
                                <td><span class="badge badge-info">{{ $ad->total_sent }}</span></td>
                                <td><span class="badge badge-{{ $ad->is_active ? 'success' : 'danger' }}">{{ $ad->is_active ? 'Active' : 'Inactive' }}</span></td>
                                <td>
                                    <div class="btn-group">
                                        <button class="btn btn-sm btn-outline-info" wire:click="editAd({{ $ad->id }})"><i class="fas fa-edit"></i></button>
                                        <button class="btn btn-sm btn-outline-{{ $ad->is_active ? 'danger' : 'success' }}" wire:click="toggleAd({{ $ad->id }})">{{ $ad->is_active ? 'Deactivate' : 'Activate' }}</button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="text-center">No ads created.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <x-modal title="{{ $ad_id ? 'Edit' : 'Create' }} Pharmacy Advertisement" :status="$adModal">
        <form wire:submit.prevent="storeAd">
            <div class="form-group"><label>Product Name</label><input type="text" class="form-control" wire:model="product_name"><x-input-error for="product_name" /></div>
            <div class="form-group"><label>SMS Content</label><textarea class="form-control" wire:model="ad_message"></textarea><x-input-error for="ad_message" /></div>
            <div class="form-group">
                <label>Target Trimester (Optional)</label>
                <select class="form-control" wire:model="trimester_id">
                    <option value="">-- No Trimester --</option>
                    @foreach($trimesters as $t)<option value="{{ $t->id }}">Trimester {{ $t->trimester }}</option>@endforeach
                </select>
            </div>
            <div class="row">
                <div class="col-6"><div class="form-group"><label>Start Week</label><input type="number" class="form-control" wire:model="target_week_start"></div></div>
                <div class="col-6"><div class="form-group"><label>End Week</label><input type="number" class="form-control" wire:model="target_week_end"></div></div>
            </div>
            <div class="row">
                <div class="col-6">
                    <div class="form-group">
                        <label>Frequency Type</label>
                        <select class="form-control" wire:model="schedule_type">
                            <option value="daily">Daily</option>
                            <option value="weekly">Weekly</option>
                            <option value="monthly">Monthly</option>
                        </select>
                        <x-input-error for="schedule_type" />
                    </div>
                </div>
                <div class="col-6">
                    <div class="form-group">
                        <label>Times per period</label>
                        <input type="number" class="form-control" wire:model="schedule_limit" min="1" max="31">
                        <x-input-error for="schedule_limit" />
                    </div>
                </div>
            </div>
            <div class="modal-footer px-0 pb-0">
                <button type="button" class="btn btn-secondary" wire:click="cancel">Cancel</button>
                <button type="submit" class="btn btn-primary">Save Ad</button>
            </div>
        </form>
    </x-modal>
</div>
