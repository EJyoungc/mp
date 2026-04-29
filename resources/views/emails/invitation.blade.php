<x-mail::message>
# Hello!

You have been invited to join **{{ $organizationName }}** as a **{{ $roleName }}**.

Click the button below to register your account.

<x-mail::button :url="$url">
Register Account
</x-mail::button>

If you did not expect this invitation, you can safely ignore this email.

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
