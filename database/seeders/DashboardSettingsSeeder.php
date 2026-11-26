<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DashboardSetting;

class DashboardSettingsSeeder extends Seeder
{
	public function run(): void
	{
		DashboardSetting::updateOrCreate(
			['slug' => 'registration_enabled'],
			[
				'name' => 'Registrierung aktiviert',
				'group' => 'authentication',
				'type' => 'boolean',
				'value' => true,
			]
		);
	}
}
