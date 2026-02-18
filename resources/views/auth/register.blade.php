<x-guest-layout>
    <div class="text-center mb-4">
        <h4 class="font-weight-bold">Create Account</h4>
        <p class="text-muted small">Join the MaaSMS maternal health network</p>
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

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <div class="form-group">
            <label for="name" class="small font-weight-bold">Full Name</label>
            <input id="name" class="form-control" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name" placeholder="John Doe">
        </div>

        <div class="form-group mt-3">
            <label for="email" class="small font-weight-bold">Email Address</label>
            <input id="email" class="form-control" type="email" name="email" value="{{ old('email') }}" required autocomplete="username" placeholder="name@example.com">
        </div>

        <div class="form-group mt-3">
            <label for="password" class="small font-weight-bold">Password</label>
            <input id="password" class="form-control" type="password" name="password" required autocomplete="new-password" placeholder="••••••••">
        </div>

        <div class="form-group mt-3">
            <label for="password_confirmation" class="small font-weight-bold">Confirm Password</label>
            <input id="password_confirmation" class="form-control" type="password" name="password_confirmation" required autocomplete="new-password" placeholder="••••••••">
        </div>

        @if (Laravel\Jetstream\Jetstream::hasTermsAndPrivacyPolicyFeature())
            <div class="form-group mt-3">
                <div class="custom-control custom-checkbox">
                    <input type="checkbox" class="custom-control-input" id="terms" name="terms" required>
                    <label class="custom-control-label small text-muted" for="terms">
                        I agree to the <a target="_blank" href="{{ route('terms.show') }}" class="text-pink font-weight-bold">Terms of Service</a> and <a target="_blank" href="{{ route('policy.show') }}" class="text-pink font-weight-bold">Privacy Policy</a>
                    </label>
                </div>
            </div>
        @endif

        <div class="mt-4">
            <button type="submit" class="btn btn-primary btn-block shadow-sm">
                Register
            </button>
        </div>

        <div class="text-center mt-4">
            <p class="small text-muted">Already have an account? <a href="{{ route('login') }}" class="text-pink font-weight-bold">Log in here</a></p>
        </div>
    </form>
</x-guest-layout>
