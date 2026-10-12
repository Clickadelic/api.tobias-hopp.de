<x-mail::message>
    # Thank you for your message!

    Hi {{ $contactSubmission->name }},

    Thank you for contacting me! I will look into it as soon as I can and get back to you if necessary.

    Thanks, have a nice day!<br>
    Toby<br>

    {{ config('app.name') }}
</x-mail::message>
