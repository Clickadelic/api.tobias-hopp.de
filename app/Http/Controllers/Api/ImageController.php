<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\FetchUnsplashImagesRequest;
use App\Services\Unsplash\UnsplashImageService;
use Illuminate\Http\JsonResponse;
use InvalidArgumentException;
use Unsplash\Exception as UnsplashException;

class ImageController extends Controller
{
    public function index(FetchUnsplashImagesRequest $request, UnsplashImageService $unsplashImageService): JsonResponse
    {
        $validated = $request->validated();
        $collectionIds = $validated['collection_ids'] ?? [];

        if (isset($validated['collection_id'])) {
            $collectionIds[] = $validated['collection_id'];
        }

        if ($collectionIds === []) {
            $collectionIds = config('services.unsplash.collection_ids', []);
        }
        $page = $validated['page'] ?? 1;
        $perPage = $validated['per_page'] ?? 30;

        if ($collectionIds === []) {
            return response()->json([
                'message' => 'No Unsplash collection IDs were provided.',
            ], 422);
        }

        try {
            $images = $unsplashImageService->getImagesFromCollections($collectionIds, $page, $perPage);

            return response()->json($images);
        } catch (InvalidArgumentException $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
            ], 422);
        } catch (UnsplashException $exception) {
            return response()->json([
                'message' => 'Unsplash request failed.',
                'errors' => $exception->getArray(),
            ], 502);
        } catch (\Throwable $exception) {
            report($exception);

            return response()->json([
                'message' => 'Unable to fetch images from Unsplash.',
            ], 502);
        }
    }
}
