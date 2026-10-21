<x-mail::message>
# Welcome, {{ $user->name }}!

An account has been created for you at **{{ $organization->name }}** with the role of **{{ ucfirst($roleName) }}**.

Here are your account login credentials:

<x-mail::panel>
**Email / Username:** {{ $user->email }}  
**Temporary Password:** `{{ $plainPassword }}`  
**Facility:** {{ $organization->name }}  
**Role:** {{ ucfirst($roleName) }}  
</x-mail::panel>

You can now log in to the {{ config('app.name') }} portal using the button below:

<x-mail::button :url="$loginUrl">
Log In to Dashboard
</x-mail::button>

> **Security Reminder:** For your security, please update your temporary password after your initial login from your Account Settings.

If you have questions or believe this was sent in error, please contact your organization administrator.

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
