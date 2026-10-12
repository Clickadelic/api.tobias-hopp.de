<?php

namespace App\Mail;

use App\Models\Team;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TeamInvitationMail extends Mailable
{
	use Queueable, SerializesModels;

	public function __construct(
		public Team $team,
		public string $inviterName,
		public string $plainToken,
	) {
	}

	public function envelope(): Envelope
	{
		return new Envelope(subject: "You've been invited to join {$this->team->name}");
	}

	public function content(): Content
	{
		return new Content(
			markdown: 'mail.team-invitation',
			with: [
				'acceptUrl' => rtrim(config('app.frontend_url'), '/') . '/team-invitations/' . $this->plainToken,
			],
		);
	}
}
