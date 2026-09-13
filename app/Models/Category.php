<?php

namespace App\Models;

class Category extends BaseModel
{
	protected $table = 'categories';

	protected $fillable = [
		'name',
		'slug',
		'type',
	];

	public function hyperlinks()
	{
		return $this->hasMany(Hyperlink::class);
	}
}
