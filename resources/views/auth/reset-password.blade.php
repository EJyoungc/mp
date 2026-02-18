<x-guest-layout>
    <div class="text-center mb-4">
        <h4 class="font-weight-bold">Set New Password</h4>
        <p class="text-muted small">Choose a secure password for your account</p>
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

    <form method="POST" action="{{ route('password.update') }}">
        @csrf

        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <div class="form-group">
            <label for="email" class="small font-weight-bold">Email Address</label>
            <input id="email" class="form-control" type="email" name="email" value="{{ old('email', $request->email) }}" required autofocus autocomplete="username" readonly>
        </div>

        <div class="form-group mt-3">
            <label for="password" class="small font-weight-bold">New Password</label>
            <input id="password" class="form-control" type="password" name="password" required autocomplete="new-password" placeholder="••••••••">
        </div>

        <div class="form-group mt-3">
            <label for="password_confirmation" class="small font-weight-bold">Confirm New Password</label>
            <input id="password_confirmation" class="form-control" type="password" name="password_confirmation" required autocomplete="new-password" placeholder="••••••••">
        </div>

        <div class="mt-4">
            <button type="submit" class="btn btn-primary btn-block shadow-sm">
                {{ __('Reset Password') }}
            </button>
        </div>
    </form>
</x-guest-layout>
