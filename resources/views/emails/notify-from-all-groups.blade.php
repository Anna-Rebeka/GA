@component('mail::message')
# {{ $text }} waiting in the "{{ $group }}" group.

Hello, check out the new updates from your "{{ $group }}" group.


@component('mail::button', ['url' => $url])
Show
@endcomponent

Your,<br>
{{ config('app.name') }}
@endcomponent
