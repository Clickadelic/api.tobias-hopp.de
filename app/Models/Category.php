<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Concerns\GeneratesSlug;

class Category extends BaseModel
{
	use HasUuids, HasFactory, GeneratesSlug;

	protected $table = "categories";
	protected $fillable = [
		'name',
		'slug',
		'type',
	];
}
