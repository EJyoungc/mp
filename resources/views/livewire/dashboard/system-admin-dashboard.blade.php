<div>
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6"><h1 class="m-0">System Admin Dashboard</h1></div>
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
                    <div class="small-box bg-info">
                        <div class="inner"><h3>{{ $mothersCount }}</h3><p>Mothers (Platform Wide)</p></div>
                        <div class="icon"><i class="fas fa-female"></i></div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-primary">
                        <div class="inner"><h3>{{ $analytics['organizations'] }}</h3><p>Organizations</p></div>
                        <div class="icon"><i class="fas fa-hospital"></i></div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-success">
                        <div class="inner"><h3>{{ $tips->count() }}</h3><p>Approved Tips @if($pendingTipsCount > 0) <span class="badge badge-warning">{{ $pendingTipsCount }} Pending</span> @endif</p></div>
                        <div class="icon"><i class="fas fa-lightbulb"></i></div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-{{ $analytics['delivery_rate'] > 90 ? 'dark' : 'warning' }}">
                        <div class="inner"><h3>{{ $analytics['delivery_rate'] }}%</h3><p>SMS Delivery Rate</p></div>
                        <div class="icon"><i class="fas fa-paper-plane"></i></div>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Mothers Manager Widget -->
                <livewire:dashboard.mothers-manager />

                <!-- Pharmacy Ads Manager Widget -->
                <livewire:dashboard.pharmacy-ads-manager />

                <!-- Membership Requests Manager Widget -->
                <livewire:dashboard.membership-requests-manager />
            </div>
        </div>
    </div>
</div>
