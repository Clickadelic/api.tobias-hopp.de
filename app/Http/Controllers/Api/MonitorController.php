<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MonitorController extends Controller
{
	public function status()
	{
		$data = Cache::remember('nextcloud_status', 30, function () {
			$url = config('services.nextcloud.monitor_url');
			$token = config('services.nextcloud.token');

			if (! $url || ! $token) {
				return null;
			}

			try {
				$response = Http::withHeaders([
					'OCS-APIRequest' => 'true',
					'NC-Token' => $token,
				])->timeout(5)->get($url);

				return $response->ok() ? $response->json() : null;
			} catch (\Throwable $e) {
				Log::error('Failed to fetch Nextcloud status: ' . $e->getMessage());
				return null;
			}
		});

		return response()->json([
			'online' => $data !== null,
			'data' => $data,
		]);
	}
}
