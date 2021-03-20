@component('mail::message')
# {{ $text }} from the "{{ $group }}" group.

For more information contact "{{ $user }}".


@component('mail::button', ['url' => $url])
Show profile
@endcomponent

Your,<br>
{{ config('app.name') }}
@endcomponent
