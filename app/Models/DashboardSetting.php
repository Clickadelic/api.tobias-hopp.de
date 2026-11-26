<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Models\Concerns\GeneratesSlug;

class DashboardSetting extends BaseModel
{
	/** @use HasFactory<\Database\Factories\DashboardSettingFactory> */
	use HasUuids, HasFactory, GeneratesSlug;

	protected $fillable = [
		'name',
		'slug',
		'group',
		'type',
		'value',
		'description',
		'is_public',
	];

	protected function casts(): array
	{
		return [
			'value' => 'json',
			'is_public' => 'boolean',
		];
	}
}
