<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Services\Dashboard\DashboardSettingsService;

class DashboardSettingsController
{
	public function update(Request $request, string $slug)
	{
		$request->validate([
			'value' => ['required'],
		]);

		app(DashboardSettingsService::class)
			->set($slug, $request->input('value'));

		return response()->json([
			'message' => 'Setting updated.',
		]);
	}
}
