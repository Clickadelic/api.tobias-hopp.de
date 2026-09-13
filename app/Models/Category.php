<?php

namespace App\Models;

class Category extends BaseModel
{
	use HasUuids;

	protected $table = "categories";

	protected $fillable = [
		'name',
		'slug',
		'type',
	];
}
