<x-mail::message>
# Passwords Reset

Dear <span style="text-transform: capitalize; font-weight: bold" > {{ $user->name == null ? 'User' : $user->name }} </span>  

A request was sent for a Password reset  and the admin has Approved
Your new Password can be found below 

{{-- <x-mail::button :url="''">
Button Text
</x-mail::button> --}}

<x-mail::panel>
Password :  <h3>{{ $password  ==  null ? '': $password }}</h3>
</x-mail::panel>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
