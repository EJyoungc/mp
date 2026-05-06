<?php

namespace App\Actions\Fortify;

use App\Models\Invitation;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Laravel\Jetstream\Jetstream;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input): User
    {
        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => $this->passwordRules(),
            'terms' => Jetstream::hasTermsAndPrivacyPolicyFeature() ? ['accepted', 'required'] : '',
            'org_action' => ['required', 'string', 'in:join,create'],
            'organization_id' => [
                Rule::requiredIf(function () use ($input) {
                    return $input['org_action'] === 'join' && empty($input['invitation_token']);
                }),
                'nullable',
                'exists:organizations,id'
            ],
            'org_name' => ['required_if:org_action,create', 'string', 'max:255'],
            'org_address' => ['nullable', 'string', 'max:255'],
            'org_district_id' => ['required_if:org_action,create', 'nullable', 'exists:districts,id'],
            'org_area_id' => ['required_if:org_action,create', 'nullable', 'exists:areas,id'],
            'invitation_token' => ['nullable', 'string', 'exists:invitations,token'],
        ])->validate();

        return DB::transaction(function () use ($input) {
            $invitation = null;
            if (! empty($input['invitation_token'])) {
                $invitation = Invitation::where('token', $input['invitation_token'])
                    ->whereNull('registered_at')
                    ->where('expires_at', '>', now())
                    ->first();
            }

            $user = User::create([
                'name' => $input['name'],
                'email' => $input['email'],
                'password' => Hash::make($input['password']),
            ]);

            if ($invitation) {
                $user->update([
                    'organization_id' => $invitation->organization_id,
                    'role_id' => $invitation->role_id,
                    'organization_verify' => 'approved', // Invited users are pre-approved
                ]);

                $invitation->update([
                    'registered_at' => now(),
                ]);
            } elseif ($input['org_action'] === 'create') {
                $organization = Organization::create([
                    'name' => $input['org_name'],
                    'address' => $input['org_address'] ?? '',
                    'owner_id' => $user->id,
                    'is_pharmacy' => isset($input['is_pharmacy']),
                    'district_id' => $input['org_district_id'],
                    'area_id' => $input['org_area_id'],
                ]);

                $adminRole = \App\Models\Role::where('name', 'admin')->first();

                $user->update([
                    'organization_id' => $organization->id,
                    'organization_verify' => 'approved', // Creator is automatically approved
                    'role_id' => $adminRole ? $adminRole->id : 2, // Creator becomes Admin of their organization
                ]);
            } else {
                $user->update([
                    'organization_id' => $input['organization_id'],
                    'organization_verify' => 'pending',
                ]);
            }

            return $user;
        });
    }
}
