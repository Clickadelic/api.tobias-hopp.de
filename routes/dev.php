<?php

use Illuminate\Support\Facades\Route;

use Illuminate\Support\Facades\Mail;
use App\Mail\ContactSubmissionMail;

// use App\Models\ContactSubmission;
// use App\Http\Requests\StoreContactSubmissionRequest;

Route::get('/emails/templates/contact-submissions/trigger', function () {
	Mail::to(config('mail.from.address'))
		->queue(new ContactSubmissionMail());
	return response()->json("OK", 200);
});

Route::get('/emails/templates/contact-submissions/preview', function () {
	$mail = new ContactSubmissionMail();
	return $mail->render();
});
