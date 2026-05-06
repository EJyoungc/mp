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

    @php
        $invitation = null;
        if(request()->has('token')) {
            $invitation = \App\Models\Invitation::where('token', request('token'))
                ->whereNull('registered_at')
                ->where('expires_at', '>', now())
                ->first();
        }
    @endphp

    <form method="POST" action="{{ route('register') }}">
        @csrf

        @if($invitation)
            <input type="hidden" name="invitation_token" value="{{ $invitation->token }}">
            <div class="alert alert-info small py-2">
                You are joining <strong>{{ $invitation->organization->name ?? 'the system' }}</strong> as a <strong>{{ $invitation->role->name }}</strong>.
            </div>
        @endif

        <div class="form-group">
            <label for="name" class="small font-weight-bold">Full Name</label>
            <input id="name" class="form-control" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name" placeholder="John Doe">
        </div>

        <div class="form-group mt-3">
            <label for="email" class="small font-weight-bold">Email Address</label>
            <input id="email" class="form-control" type="email" name="email" value="{{ old('email', $invitation->email ?? '') }}" required autocomplete="username" placeholder="name@example.com" {{ $invitation ? 'readonly' : '' }}>
        </div>

        <div class="form-group mt-3">
            <label for="password" class="small font-weight-bold">Password</label>
            <input id="password" class="form-control" type="password" name="password" required autocomplete="new-password" placeholder="••••••••">
        </div>

        <div class="form-group mt-3">
            <label for="password_confirmation" class="small font-weight-bold">Confirm Password</label>
            <input id="password_confirmation" class="form-control" type="password" name="password_confirmation" required autocomplete="new-password" placeholder="••••••••">
        </div>

        <!-- Organization Section -->
        @if(!$invitation)
        <div class="card mt-4 border-light shadow-sm">
            <div class="card-body">
                <label class="small font-weight-bold">Organization Membership</label>
                <div class="custom-control custom-radio mb-2">
                    <input type="radio" id="org_action_join" name="org_action" class="custom-control-input" value="join" checked onchange="toggleOrgSection()">
                    <label class="custom-control-label small" for="org_action_join">Join an Existing Organization</label>
                </div>
                <div class="custom-control custom-radio mb-3">
                    <input type="radio" id="org_action_create" name="org_action" class="custom-control-input" value="create" onchange="toggleOrgSection()">
                    <label class="custom-control-label small" for="org_action_create">Create a New Organization (e.g., Pharmacy/Clinic)</label>
                </div>

                <div id="join_org_section">
                    <select name="organization_id" class="form-control form-control-sm">
                        <option value="">-- Select Organization --</option>
                        @foreach($organizations as $org)
                            <option value="{{ $org->id }}">{{ $org->name }} {{ $org->is_pharmacy ? '(Pharmacy)' : '' }}</option>
                        @endforeach
                    </select>
                    <small class="text-muted">Your membership will require approval from the organization owner.</small>
                </div>

                <div id="create_org_section" style="display: none;">
                    <div class="form-group mb-2">
                        <input type="text" name="org_name" class="form-control form-control-sm" placeholder="Organization Name">
                    </div>
                    <div class="form-group mb-2">
                        <input type="text" name="org_address" class="form-control form-control-sm" placeholder="Organization Physical Address">
                    </div>
                    <div class="form-group mb-2">
                        <select name="org_district_id" id="org_district_id" class="form-control form-control-sm" onchange="filterAreas()">
                            <option value="">-- Select District --</option>
                            @foreach($districts as $district)
                                <option value="{{ $district->id }}">{{ $district->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group mb-2">
                        <select name="org_area_id" id="org_area_id" class="form-control form-control-sm">
                            <option value="">-- Select Area --</option>
                        </select>
                    </div>
                    <div class="custom-control custom-checkbox small">
                        <input type="checkbox" class="custom-control-input" id="is_pharmacy" name="is_pharmacy" value="1">
                        <label class="custom-control-label" for="is_pharmacy">This is a Pharmacy</label>
                    </div>
                </div>
            </div>
        </div>
        @else
            <input type="hidden" name="org_action" value="join">
            <input type="hidden" name="organization_id" value="{{ $invitation->organization_id }}">
        @endif

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

    <script>
        const areas = @json(\App\Models\Area::all());

        function toggleOrgSection() {
            const isJoin = document.getElementById('org_action_join').checked;
            document.getElementById('join_org_section').style.display = isJoin ? 'block' : 'none';
            document.getElementById('create_org_section').style.display = isJoin ? 'none' : 'block';
        }

        function filterAreas() {
            const districtId = document.getElementById('org_district_id').value;
            const areaSelect = document.getElementById('org_area_id');
            
            // Clear current options
            areaSelect.innerHTML = '<option value="">-- Select Area --</option>';
            
            if (districtId) {
                const filteredAreas = areas.filter(area => area.district_id == districtId);
                filteredAreas.forEach(area => {
                    const option = document.createElement('option');
                    option.value = area.id;
                    option.textContent = area.name;
                    areaSelect.appendChild(option);
                });
            }
        }
    </script>
</x-guest-layout>
