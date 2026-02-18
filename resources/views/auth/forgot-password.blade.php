<x-guest-layout>
    <div class="text-center mb-4">
        <h4 class="font-weight-bold">Reset Password</h4>
        <p class="text-muted small">Enter your email to receive a reset link</p>
    </div>

    <div class="mb-4 text-sm text-muted text-center px-2">
        {{ __('Forgot your password? No problem. Just let us know your email address and we will email you a password reset link.') }}
    </div>

    @if (session('status'))
        <div class="alert alert-success small py-2">
            {{ session('status') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger small py-2">
            <ul class="mb-0 px-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <div class="form-group">
            <label for="email" class="small font-weight-bold">Email Address</label>
            <input id="email" class="form-control" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" placeholder="name@example.com">
        </div>

        <div class="mt-4">
            <button type="submit" class="btn btn-primary btn-block shadow-sm">
                {{ __('Email Password Reset Link') }}
            </button>
        </div>

        <div class="text-center mt-4">
            <a href="{{ route('login') }}" class="small text-pink font-weight-bold">Back to Login</a>
        </div>
    </form>
</x-guest-layout>
