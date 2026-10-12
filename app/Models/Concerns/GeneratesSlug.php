<?php

namespace App\Models\Concerns;

use Illuminate\Support\Str;

trait GeneratesSlug
{
	protected static function bootGeneratesSlug(): void
	{
		static::creating(function ($model) {
			if (!empty($model->slug)) {
				return;
			}

			$base = Str::slug($model->name) ?: Str::lower(class_basename($model));
			$slug = $base;
			$i = 2;

			while (static::where('slug', $slug)->exists()) {
				$slug = $base . '-' . $i++;
			}

			$model->slug = $slug;
		});
	}
}
