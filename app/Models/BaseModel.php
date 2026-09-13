<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

abstract class BaseModel extends Model
{
	use HasUuids;

	public $incrementing = false;

	protected $keyType = 'string';

	public function newCollection(array $models = []): Collection
	{
		return new Collection($models);
	}
}
