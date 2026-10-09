<?php

namespace App\Http\Controllers\Api;

use App\Enums\Season;
use App\Http\Controllers\Controller;
use App\Http\Requests\FetchUnsplashImagesRequest;
use App\Services\Unsplash\UnsplashImageService;
use Carbon\CarbonImmutable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use InvalidArgumentException;
use Unsplash\Exception as UnsplashException;
use OpenApi\Attributes as OA;

#[OA\Parameter(parameter: "UnsplashVariant", name: "variant", in: "query", description: "Image size variant. Resizing params (w, h, q, fit) only apply to raw/full.", schema: new OA\Schema(type: "string", enum: ["raw", "full", "regular", "small", "thumb"], default: "regular"))]
#[OA\Parameter(parameter: "UnsplashStrategy", name: "strategy", in: "query", description: "random picks a new image each call; daily keeps one image per day.", schema: new OA\Schema(type: "string", enum: ["random", "daily"], default: "random"))]
#[OA\Parameter(parameter: "UnsplashResponse", name: "response", in: "query", description: "redirect answers with a 302 to the image; json returns the URL and photo data.", schema: new OA\Schema(type: "string", enum: ["redirect", "json"], default: "redirect"))]
#[OA\Parameter(parameter: "UnsplashWidth", name: "w", in: "query", description: "Image width in pixels.", schema: new OA\Schema(type: "integer", minimum: 1))]
#[OA\Parameter(parameter: "UnsplashHeight", name: "h", in: "query", description: "Image height in pixels.", schema: new OA\Schema(type: "integer", minimum: 1))]
#[OA\Parameter(parameter: "UnsplashQuality", name: "q", in: "query", description: "Image quality (1-100).", schema: new OA\Schema(type: "integer", minimum: 1, maximum: 100))]
#[OA\Parameter(parameter: "UnsplashFit", name: "fit", in: "query", description: "Imgix fit mode, e.g. crop or max.", schema: new OA\Schema(type: "string"))]
#[OA\Parameter(parameter: "UnsplashCacheTtl", name: "cache_ttl", in: "query", description: "Cache the selected image for this many seconds (daily defaults to 86400).", schema: new OA\Schema(type: "integer", minimum: 0))]
#[OA\Parameter(parameter: "UnsplashTimezone", name: "tz", in: "query", description: "IANA timezone used for the daily strategy.", schema: new OA\Schema(type: "string", example: "Europe/Berlin"))]
class BackgroundImageController extends Controller
{
	#[OA\Get(
		path: "/api/v1/unsplash/image/general",
		summary: "Fetch a random background image from Unsplash collections",
		description: "Public, throttled to 60 requests/minute. Falls back to the configured default collections when none are given.",
		parameters: [
			new OA\Parameter(name: "collection_ids", in: "query", description: "Unsplash collection IDs (comma-separated or repeated collection_ids[]).", explode: false, schema: new OA\Schema(type: "array", items: new OA\Items(type: "string"))),
			new OA\Parameter(name: "collection_id", in: "query", description: "A single Unsplash collection ID.", schema: new OA\Schema(type: "string")),
			new OA\Parameter(name: "collections", in: "query", description: "Alias for collection_ids.", schema: new OA\Schema(type: "string")),
			new OA\Parameter(ref: "#/components/parameters/UnsplashVariant"),
			new OA\Parameter(ref: "#/components/parameters/UnsplashStrategy"),
			new OA\Parameter(ref: "#/components/parameters/UnsplashResponse"),
			new OA\Parameter(ref: "#/components/parameters/UnsplashWidth"),
			new OA\Parameter(ref: "#/components/parameters/UnsplashHeight"),
			new OA\Parameter(ref: "#/components/parameters/UnsplashQuality"),
			new OA\Parameter(ref: "#/components/parameters/UnsplashFit"),
			new OA\Parameter(ref: "#/components/parameters/UnsplashCacheTtl"),
			new OA\Parameter(ref: "#/components/parameters/UnsplashTimezone"),
		],
		tags: ["Unsplash Images"],
		responses: [
			new OA\Response(response: 200, description: "Image URL and photo data (response=json)", content: new OA\JsonContent(ref: "#/components/schemas/UnsplashImageResponse")),
			new OA\Response(response: 302, description: "Redirect to the image (response=redirect, default)"),
			new OA\Response(response: 422, description: "No collection IDs provided or configured / invalid input", content: new OA\JsonContent(ref: "#/components/schemas/Message")),
			new OA\Response(response: 429, description: "Too many requests"),
			new OA\Response(response: 502, description: "Unsplash request failed", content: new OA\JsonContent(ref: "#/components/schemas/Message")),
		]
	)]
	public function general(FetchUnsplashImagesRequest $request, UnsplashImageService $service): RedirectResponse|JsonResponse
	{
		$validated = $request->validated();
		$collectionIds = $validated['collection_ids'] ?? [];
		if ($collectionIds === []) {
			$collectionIds = config('services.unsplash.collection_ids', []);
		}
		if ($collectionIds === []) {
			return response()->json(['message' => 'No Unsplash collection IDs provided or configured.'], 422);
		}

		$variant = (string) $request->query('variant', 'regular');
		$strategy = (string) $request->query('strategy', 'random'); // random | daily
		$responseMode = strtolower((string) $request->query('response', 'redirect')); // redirect | json
		$transforms = [
			'w' => $request->query('w'),
			'h' => $request->query('h'),
			'q' => $request->query('q'),
			'fit' => $request->query('fit'),
		];

		$cacheKey = null;
		$ttl = (int) $request->query('cache_ttl', 0);
		if ($strategy === 'daily') {
			$today = CarbonImmutable::now($request->query('tz', config('app.timezone')))->format('Y-m-d');
			$cacheKey = 'bg:daily:' . $today . ':' . md5(json_encode([$collectionIds, $variant, $transforms]));
			$ttl = $ttl > 0 ? $ttl : 86400; // default 24h unless overridden
		} elseif ($ttl > 0) {
			$cacheKey = 'bg:random:' . md5(json_encode([$collectionIds, $variant, $transforms]));
		}

		try {
			$photo = $service->getRandomPhotoFromCollections($collectionIds, [], $cacheKey, $ttl);
			$service->registerDownload($photo['id']); // best-effort

			$url = $service->buildVariantUrl($photo, $variant, $transforms);

			if ($responseMode === 'json') {
				return response()->json([
					'url' => $url,
					'photo' => $photo,
				]);
			}

			return redirect()->away($url, 302);
		} catch (InvalidArgumentException $e) {
			return response()->json(['message' => $e->getMessage()], 422);
		} catch (UnsplashException $e) {
			return response()->json(['message' => 'Unsplash request failed.', 'errors' => $e->getArray()], 502);
		} catch (\Throwable $e) {
			report($e);
			return response()->json(['message' => 'Unable to select background image.'], 502);
		}
	}

	#[OA\Get(
		path: "/api/v1/unsplash/image/seasonal",
		summary: "Fetch a seasonal background image from Unsplash",
		description: "Public, throttled to 60 requests/minute. Uses the collection configured for the season; the season is auto-detected when omitted.",
		parameters: [
			new OA\Parameter(name: "season", in: "query", description: "Season to use ('fall' is accepted as autumn). Defaults to the current season.", schema: new OA\Schema(type: "string", enum: ["spring", "summer", "autumn", "winter"])),
			new OA\Parameter(ref: "#/components/parameters/UnsplashVariant"),
			new OA\Parameter(ref: "#/components/parameters/UnsplashStrategy"),
			new OA\Parameter(ref: "#/components/parameters/UnsplashResponse"),
			new OA\Parameter(ref: "#/components/parameters/UnsplashWidth"),
			new OA\Parameter(ref: "#/components/parameters/UnsplashHeight"),
			new OA\Parameter(ref: "#/components/parameters/UnsplashQuality"),
			new OA\Parameter(ref: "#/components/parameters/UnsplashFit"),
			new OA\Parameter(ref: "#/components/parameters/UnsplashCacheTtl"),
			new OA\Parameter(ref: "#/components/parameters/UnsplashTimezone"),
		],
		tags: ["Unsplash Images"],
		responses: [
			new OA\Response(response: 200, description: "Image URL, photo data and season (response=json)", content: new OA\JsonContent(ref: "#/components/schemas/UnsplashImageResponse")),
			new OA\Response(response: 302, description: "Redirect to the image (response=redirect, default)"),
			new OA\Response(response: 422, description: "No collection configured for the season", content: new OA\JsonContent(ref: "#/components/schemas/Message")),
			new OA\Response(response: 429, description: "Too many requests"),
			new OA\Response(response: 502, description: "Unsplash request failed", content: new OA\JsonContent(ref: "#/components/schemas/Message")),
		]
	)]
	public function seasonal(Request $request, UnsplashImageService $service): RedirectResponse|JsonResponse
	{
		$seasonParam = strtolower((string) $request->query('season', ''));
		if ($seasonParam === 'fall') {
			$seasonParam = 'autumn';
		}

		$seasonEnum = in_array($seasonParam, ['spring', 'summer', 'autumn', 'winter'], true)
			? Season::from($seasonParam)
			: Season::current();

		$collectionId = $seasonEnum->collectionId();
		if (blank($collectionId)) {
			return response()->json(['message' => "No seasonal collection configured for '{$seasonEnum->value}'."], 422);
		}

		$season = $seasonEnum->value;

		$variant = (string) $request->query('variant', 'regular');
		$responseMode = strtolower((string) $request->query('response', 'redirect'));
		$transforms = [
			'w' => $request->query('w'),
			'h' => $request->query('h'),
			'q' => $request->query('q'),
			'fit' => $request->query('fit'),
		];

		$strategy = (string) $request->query('strategy', 'random');
		$tz = (string) $request->query('tz', config('app.timezone'));
		$cacheKey = null;
		$ttl = (int) $request->query('cache_ttl', 0);
		if ($strategy === 'daily') {
			$today = CarbonImmutable::now($tz)->format('Y-m-d');
			$cacheKey = 'bg:seasonal:daily:' . $season . ':' . $today . ':' . md5(json_encode([$collectionId, $variant, $transforms]));
			$ttl = $ttl > 0 ? $ttl : 86400;
		} elseif ($ttl > 0) {
			$cacheKey = 'bg:seasonal:random:' . $season . ':' . md5(json_encode([$collectionId, $variant, $transforms]));
		}

		try {
			$photo = $service->getRandomPhotoFromCollections([$collectionId], [], $cacheKey, $ttl);
			$service->registerDownload($photo['id']);
			$url = $service->buildVariantUrl($photo, $variant, $transforms);

			if ($responseMode === 'json') {
				return response()->json([
					'url' => $url,
					'photo' => $photo,
					'season' => $season,
				]);
			}

			return redirect()->away($url, 302);
		} catch (InvalidArgumentException $e) {
			return response()->json(['message' => $e->getMessage()], 422);
		} catch (UnsplashException $e) {
			return response()->json(['message' => 'Unsplash request failed.', 'errors' => $e->getArray()], 502);
		} catch (\Throwable $e) {
			report($e);
			return response()->json(['message' => 'Unable to select seasonal background.'], 502);
		}
	}
}
