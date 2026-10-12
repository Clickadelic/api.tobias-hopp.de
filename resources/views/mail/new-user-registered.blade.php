<x-mail::message>
# New user registration

A new user has registered on {{ config('app.name') }}.

**Name:** {{ $user->name }}<br>
**Email:** {{ $user->email }}<br>
**Registered at:** {{ $user->created_at->format('Y-m-d H:i') }}

{{ config('app.name') }}
</x-mail::message>
