<div class="d-flex align-items-center justify-content-center vh-100 bg-light">
    <div class="text-center p-5 shadow-lg rounded bg-white" style="max-width: 500px;">
        <div class="mb-4">
            <i class="fas fa-clock text-warning" style="font-size: 4rem;"></i>
        </div>
        <h2 class="font-weight-bold mb-3">Waiting for Approval</h2>
        <p class="text-muted mb-4">
            Your membership request for <strong>{{ auth()->user()->organization->name ?? 'your organization' }}</strong> is currently pending.
        </p>
        <div class="alert alert-info small py-2 mb-4">
            An administrator or the organization owner needs to verify your account before you can access the platform features.
        </div>
        <div class="d-flex flex-column gap-2">
            <p class="small text-muted mb-3">Check back soon! You will be able to access the dashboard once approved.</p>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn btn-outline-danger btn-block shadow-sm">
                    Log Out
                </button>
            </form>
        </div>
    </div>
</div>
