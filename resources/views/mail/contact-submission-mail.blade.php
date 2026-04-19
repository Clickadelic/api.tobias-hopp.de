<x-mail::message>
    # Introduction

    The body of your message.

    <x-mail::button :url="''">
        Reply to this email
    </x-mail::button>

    Thanks,<br>
    Toby<br>
    {{ config('app.name') }}
</x-mail::message>
