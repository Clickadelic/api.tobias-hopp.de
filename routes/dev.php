<?php

use Illuminate\Support\Facades\Route;

use Illuminate\Support\Facades\Mail;
use App\Mail\ContactSubmissionMail;

// use App\Models\ContactSubmission;
// use App\Http\Requests\StoreContactSubmissionRequest;

Route::get('/mail-test', function () {
	Mail::to(config('mail.from.address'))
		->queue(new ContactSubmissionMail());
	return "OK";
});

Route::get('/mail-show', function () {
	$mail = new ContactSubmissionMail();
	return $mail->render();
});
