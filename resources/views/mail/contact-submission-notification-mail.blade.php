<x-mail::message>
# New contact submission

**Name:** {{ $contactSubmission->name }}

**Email:** {{ $contactSubmission->email }}

**Phone:** {{ $contactSubmission->phone ?: 'Not provided' }}

**Subject:** {{ $contactSubmission->subject }}

**Message:**

{{ $contactSubmission->message }}

{{ config('app.name') }}
</x-mail::message>
