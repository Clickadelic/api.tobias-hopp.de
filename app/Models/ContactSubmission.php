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

	protected $attributes = [
		'is_read' => false,
	];

	protected function casts(): array
	{
		return [
			'is_read' => 'boolean',
		];
	}
}
