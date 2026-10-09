<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use OpenApi\Attributes as OA;

#[OA\Tag(
	name: "NextCloud Uptime Monitor",
	description: "Endpoints for monitoring the Nextcloud uptime"
)]
class MonitorController extends Controller
{
	#[OA\Get(
		path: "/api/v1/monitor/nextcloud",
		summary: "Fetch the status of the Nextcloud monitor",
		description: "The result is cached for 30 seconds.",
		security: [["sanctum" => []]],
		tags: ["NextCloud Uptime Monitor"],
		responses: [
			new OA\Response(
				response: 200,
				description: "Monitor status",
				content: new OA\JsonContent(properties: [
					new OA\Property(property: "online", type: "boolean"),
					new OA\Property(property: "data", type: "object", nullable: true),
				])
			),
			new OA\Response(response: 401, description: "Unauthenticated", content: new OA\JsonContent(ref: "#/components/schemas/Message")),
			new OA\Response(response: 403, description: "Email address not verified"),
		]
	)]
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
