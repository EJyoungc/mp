<div>
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6"><h1 class="m-0">Pharmacy Dashboard</h1></div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right"><li class="breadcrumb-item active">Dashboard</li></ol>
                </div>
            </div>
        </div>
    </div>

    <div class="content">
        <div class="container-fluid">
            <!-- Stats Row -->
            <div class="row">
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-primary">
                        <div class="inner"><h3>{{ $analytics['practitioners'] }}</h3><p>Practitioners</p></div>
                        <div class="icon"><i class="fas fa-user-md"></i></div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-success">
                        <div class="inner"><h3>{{ $tips->count() }}</h3><p>Health Tips</p></div>
                        <div class="icon"><i class="fas fa-lightbulb"></i></div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-{{ $analytics['delivery_rate'] > 90 ? 'dark' : 'warning' }}">
                        <div class="inner"><h3>{{ $analytics['delivery_rate'] }}%</h3><p>Ad Delivery Rate</p></div>
                        <div class="icon"><i class="fas fa-paper-plane"></i></div>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Pharmacy Ads Manager Widget -->
                <livewire:dashboard.pharmacy-ads-manager />

                <!-- Ad Analytics Widget -->
                <div class="col-12 mt-4">
                    <div class="card">
                        <div class="card-header bg-gradient-success"><h3 class="card-title"><i class="fas fa-map-marker-alt mr-2"></i> Recent Ad Outreach</h3></div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead><tr><th>Ad Product</th><th>Mother's Area</th><th>Sent At</th><th>Status</th></tr></thead>
                                    <tbody>
                                        @php
                                            $recentReaches = \App\Models\AdHistory::with(['mother.area', 'pharmacyAd'])
                                                ->where('organization_id', auth()->user()->organization_id)
                                                ->latest()->limit(10)->get();
                                        @endphp
                                        @forelse($recentReaches as $reach)
                                            <tr>
                                                <td>{{ $reach->pharmacyAd->product_name ?? 'N/A' }}</td>
                                                <td>{{ $reach->mother->area->name ?? 'Unknown' }}</td>
                                                <td>{{ $reach->created_at->diffForHumans() }}</td>
                                                <td><span class="badge badge-{{ $reach->status === 'sent' ? 'success' : 'danger' }}">{{ ucfirst($reach->status) }}</span></td>
                                            </tr>
                                        @empty
                                            <tr><td colspan="4" class="text-center">No outreach data.</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Membership Requests Manager Widget (Staff requests) -->
                <livewire:dashboard.membership-requests-manager />
            </div>
        </div>
    </div>
</div>
