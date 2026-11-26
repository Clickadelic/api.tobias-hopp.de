<?php

namespace App\Services;

use App\Models\DashboardSetting;

class DashboardSettingsService
{
	public function get(string $key, mixed $default = null): mixed
	{
		$setting = DashboardSetting::where('slug', $key)->first();

		return $setting?->value ?? $default;
	}

	public function set(string $key, mixed $value): void
	{
		DashboardSetting::updateOrCreate(
			['slug' => $key],
			[
				'name' => $key,
				'value' => $value,
			]
		);
	}

	public function enabled(string $key): bool
	{
		return (bool) $this->get($key, false);
	}

	public function all(): array
	{
		return DashboardSetting::all()
			->pluck('value', 'slug')
			->toArray();
	}
}
