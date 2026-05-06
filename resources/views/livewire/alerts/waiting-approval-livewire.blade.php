<div class="d-flex align-items-center justify-content-center vh-100 bg-light">
    <div class="text-center p-5 shadow-lg rounded bg-white" style="max-width: 500px;">
        <div class="mb-4">
            @if(auth()->user()->is_active == 0)
                <i class="fas fa-user-slash text-danger" style="font-size: 4rem;"></i>
            @else
                <i class="fas fa-clock text-warning" style="font-size: 4rem;"></i>
            @endif
        </div>
        
        <h2 class="font-weight-bold mb-3">
            {{ auth()->user()->is_active == 0 ? 'Account Deactivated' : 'Waiting for Approval' }}
        </h2>

        <p class="text-muted mb-4">
            @if(auth()->user()->is_active == 0)
                Your account has been deactivated by an administrator.
            @else
                Your membership request for <strong>{{ auth()->user()->organization->name ?? 'the system' }}</strong> is currently pending.
            @endif
        </p>

        <div class="alert alert-info small py-3 mb-4 text-left">
            <h6 class="font-weight-bold mb-2"><i class="fas fa-info-circle mr-1"></i> What should I do?</h6>
            <p class="mb-0">
                Please contact your <strong>Organization Admin</strong> or a <strong>System Administrator</strong> to verify and activate your account.
            </p>
        </div>

        <div class="d-flex flex-column gap-2">
            <p class="small text-muted mb-3">You will be able to access the dashboard once your account is fully verified and active.</p>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn btn-navy btn-block shadow-sm py-2">
                    <i class="fas fa-sign-out-alt mr-2"></i> Log Out
                </button>
            </form>
        </div>
    </div>
</div>

<style>
    .btn-navy { background-color: #001f3f; color: #fff; border: none; transition: all 0.3s; }
    .btn-navy:hover { background-color: #002d5c; transform: translateY(-1px); color: #fff; }
</style>
