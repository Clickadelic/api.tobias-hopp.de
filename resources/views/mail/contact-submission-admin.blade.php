<x-mail::message>
# New contact submission

A new message was submitted through the contact form.

**Name:** {{ $contactSubmission->name }}<br>
**Email:** {{ $contactSubmission->email }}<br>
@if($contactSubmission->phone)
**Phone:** {{ $contactSubmission->phone }}<br>
@endif
**Subject:** {{ $contactSubmission->subject }}

**Message:**<br>
{{ $contactSubmission->message }}

{{ config('app.name') }}
</x-mail::message>
