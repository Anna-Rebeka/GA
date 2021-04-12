@component('mail::message')
# There is a new {{ substr($what, 0, -1) }} in the "{{ $group }}" group.

Hello, check out the new updates from your "{{ $group }}" group.


@component('mail::button', ['url' => $url])
Show
@endcomponent

Your,<br>
{{ config('app.name') }}
@endcomponent
