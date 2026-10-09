<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;

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
