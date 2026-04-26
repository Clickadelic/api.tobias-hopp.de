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

class BackgroundController extends Controller
{
    // GET /api/background
    public function background(FetchUnsplashImagesRequest $request, UnsplashImageService $service): RedirectResponse|JsonResponse
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

        $cacheKey = null; $ttl = (int) $request->query('cache_ttl', 0);
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

    // GET /api/unsplash/image/seasonal
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
        $cacheKey = null; $ttl = (int) $request->query('cache_ttl', 0);
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
