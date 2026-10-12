<x-mail::message>
# Team invitation

{{ $inviterName }} invited you to join the team **{{ $team->name }}**.

<x-mail::button :url="$acceptUrl">
Accept invitation
</x-mail::button>

The invitation expires in 7 days. If you did not expect it, you can ignore this email.

{{ config('app.name') }}
</x-mail::message>
