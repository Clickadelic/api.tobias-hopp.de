<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class ContactSubmission extends Model
{
	/** @use HasFactory<\Database\Factories\ContactSubmissionFactory> */
	use HasFactory, HasUuids;

	protected $primaryKey = 'uuid';
	public $incrementing = false;
	protected $keyType = 'string';

	protected $fillable = [
		'name',
		'phone',
		'email',
		'subject',
		'message',
	];
}
