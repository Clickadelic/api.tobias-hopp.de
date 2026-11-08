<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ResetPasswordNotification extends Notification implements ShouldQueue
{
	use Queueable;

	public string $token;
	public string $appUrl;

	public function __construct(string $token, string $appUrl = null)
	{
		$this->token = $token;
		$this->appUrl = $appUrl ?? config('app.frontend_url', 'http://localhost:5173');
		
		// Don't queue in local environment for testing
		if (config('app.env') === 'local') {
			$this->queue = null;
		}
	}

	public function via(object $notifiable): array
	{
		return ['mail'];
	}

	public function toMail(object $notifiable): MailMessage
	{
		$resetUrl = $this->appUrl . '/auth/reset-password?token=' . $this->token . '&email=' . urlencode($notifiable->email);

		return (new MailMessage)
			->subject('Passwort zurücksetzen')
			->line('Du erhältst diese E-Mail, weil wir eine Anfrage zum Zurücksetzen des Passworts für dein Konto erhalten haben.')
			->action('Passwort zurücksetzen', $resetUrl)
			->line('Dieser Link läuft in 60 Minuten ab.')
			->line('Wenn du diese Anfrage nicht gestellt hast, kannst du diese E-Mail ignorieren.')
			->salutation('Mit freundlichen Grüßen');
	}
}
