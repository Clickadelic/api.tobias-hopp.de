<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Domain extends Model
{
	/** @use HasFactory<\Database\Factories\DomainFactory> */
	use HasFactory;

	protected $table = "domains";
	protected $fillable = [
		"domainname",
		"url",
		"score",
		"source",
		"technical_contact",
		"user_id"
	];

	public function user()
	{
		return $this->belongsTo(User::class);
	}
}
