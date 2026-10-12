<?php

namespace App\Mail;

use App\Models\ContactSubmission;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContactSubmissionNotificationMail extends Mailable
{
	use Queueable, SerializesModels;

	public function __construct(public ContactSubmission $contactSubmission)
	{
	}

	public function envelope(): Envelope
	{
		return new Envelope(
			subject: 'New contact submission: '.$this->contactSubmission->subject,
		);
	}

	public function content(): Content
	{
		return new Content(
			markdown: 'mail.contact-submission-notification-mail',
		);
	}
}
