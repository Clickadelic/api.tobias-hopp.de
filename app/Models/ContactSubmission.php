<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class ContactSubmission extends BaseModel
{
	/** @use HasFactory<\Database\Factories\ContactSubmissionFactory> */
	use HasFactory;

	protected $primaryKey = 'uuid';

	protected $fillable = [
		'name',
		'phone',
		'email',
		'subject',
		'message',
	];
}
