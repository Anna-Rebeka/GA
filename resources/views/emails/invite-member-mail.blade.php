@component('mail::message')
# You have a new group invitation!

Hello, {{ $user }} invites you to join a "{{ $group }}" group.

Click on the "Join!" button below if you wish to become a member.

@component('mail::button', ['url' => ''])
Join!
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
