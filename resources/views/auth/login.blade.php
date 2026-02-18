<x-guest-layout>
    <div class="text-center mb-4">
        <h4 class="font-weight-bold">Welcome Back</h4>
        <p class="text-muted small">Log in to your MaaSMS account</p>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger small py-2">
            <ul class="mb-0 px-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if (session('status'))
        <div class="alert alert-success small py-2">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="form-group">
            <label for="email" class="small font-weight-bold">Email Address</label>
            <input id="email" class="form-control" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" placeholder="name@example.com">
        </div>

        <div class="form-group mt-3">
            <label for="password" class="small font-weight-bold">Password</label>
            <input id="password" class="form-control" type="password" name="password" required autocomplete="current-password" placeholder="••••••••">
        </div>

        <div class="d-flex justify-content-between align-items-center mt-4">
            <div class="custom-control custom-checkbox">
                <input type="checkbox" class="custom-control-input" id="remember_me" name="remember">
                <label class="custom-control-label small text-muted" for="remember_me">Remember me</label>
            </div>
            @if (Route::has('password.request'))
                <a class="small text-pink font-weight-bold" href="{{ route('password.request') }}">
                    Forgot password?
                </a>
            @endif
        </div>

        <div class="mt-4">
            <button type="submit" class="btn btn-primary btn-block shadow-sm">
                Log in
            </button>
        </div>

        @if (Route::has('register'))
            <div class="text-center mt-4">
                <p class="small text-muted">Don't have an account? <a href="{{ route('register') }}" class="text-pink font-weight-bold">Register here</a></p>
            </div>
        @endif
    </form>
</x-guest-layout>
