<x-mail::message>
# Thank you for your message!

Hi {{ $contactSubmission->name }},

Thank you for contacting me! I will look into it as soon as I can and get back to you if necessary.

Here is a copy of your message:

<x-mail::panel>
**Subject:** {{ $contactSubmission->subject }}

**Message:**<br>
{{ $contactSubmission->message }}
@if($contactSubmission->phone)

**Phone:** {{ $contactSubmission->phone }}
@endif
</x-mail::panel>

Thanks, have a nice day!<br>
Toby<br>

{{ config('app.name') }}
</x-mail::message>

