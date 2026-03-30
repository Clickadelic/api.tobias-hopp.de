<?php

namespace App\Services\Unsplash;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use InvalidArgumentException;
use Unsplash\Collection as UnsplashCollection;
use Unsplash\HttpClient;
use Unsplash\Photo as UnsplashPhoto;

class UnsplashImageService
{
    private bool $isInitialized = false;

    public function getImagesFromCollections(array $collectionIds, int $page = 1, int $perPage = 30): array
    {
        $this->initializeClient();

        $collectionIds = array_values(array_unique(array_filter($collectionIds, static function (mixed $value): bool {
            return is_string($value) && $value !== '';
        })));

        if ($collectionIds === []) {
            throw new InvalidArgumentException('At least one Unsplash collection ID is required.');
        }

        $collections = collect($collectionIds)->map(function (string $collectionId) use ($page, $perPage): array {
            $collection = UnsplashCollection::find($collectionId);
            $photos = $collection->photos($page, $perPage);

            return [
                'id' => $collectionId,
                'total' => $photos->totalObjects(),
                'total_pages' => $photos->totalPages(),
                'links' => $photos->getPages(),
                'photos' => collect($photos->toArray())
                    ->map(fn (array $photo): array => $this->formatPhoto($photo, $collectionId))
                    ->values()
                    ->all(),
            ];
        });

        return [
            'data' => $collections
                ->flatMap(fn (array $collection): array => $collection['photos'])
                ->unique('id')
                ->values()
                ->all(),
            'meta' => [
                'page' => $page,
                'per_page' => $perPage,
                'collections' => $collections
                    ->map(fn (array $collection): array => Arr::except($collection, ['photos']))
                    ->values()
                    ->all(),
            ],
        ];
    }

    /**
     * Pick a random photo across the given collections. Optionally cache (e.g. for daily strategy).
     * Returns formatted photo array (same shape as formatPhoto) without collection_id.
     */
    public function getRandomPhotoFromCollections(array $collectionIds, array $filters = [], ?string $cacheKey = null, int $ttlSeconds = 0): array
    {
        $this->initializeClient();

        $collectionIds = array_values(array_unique(array_filter($collectionIds, static fn ($v) => is_string($v) && $v !== '')));
        if ($collectionIds === []) {
            throw new InvalidArgumentException('At least one Unsplash collection ID is required.');
        }

        $doFetch = function () use ($collectionIds, $filters): array {
            $filters = array_filter($filters, static fn ($v) => $v !== null && $v !== '');
            $filters['collections'] = implode(',', $collectionIds);

            $photo = UnsplashPhoto::random($filters);
            $photoArr = $photo->toArray();

            return [
                'id' => Arr::get($photoArr, 'id'),
                'description' => Arr::get($photoArr, 'description'),
                'alt_description' => Arr::get($photoArr, 'alt_description'),
                'width' => Arr::get($photoArr, 'width'),
                'height' => Arr::get($photoArr, 'height'),
                'color' => Arr::get($photoArr, 'color'),
                'blur_hash' => Arr::get($photoArr, 'blur_hash'),
                'created_at' => Arr::get($photoArr, 'created_at'),
                'urls' => Arr::only(Arr::get($photoArr, 'urls', []), ['raw', 'full', 'regular', 'small', 'thumb']),
                'links' => Arr::get($photoArr, 'links', []),
                'user' => [
                    'name' => Arr::get($photoArr, 'user.name'),
                    'username' => Arr::get($photoArr, 'user.username'),
                    'links' => Arr::get($photoArr, 'user.links', []),
                ],
            ];
        };

        if ($cacheKey && $ttlSeconds > 0) {
            return Cache::remember($cacheKey, $ttlSeconds, $doFetch);
        }

        return $doFetch();
    }

    /**
     * Register a download for the given photo id (Unsplash requirement).
     */
    public function registerDownload(string $photoId): void
    {
        $this->initializeClient();
        try {
            $photo = UnsplashPhoto::find($photoId);
            $photo->download();
        } catch (\Throwable $e) {
            report($e);
        }
    }

    /**
     * Build a variant URL for the given photo.
     */
    public function buildVariantUrl(array $photo, string $variant = 'regular', array $transforms = []): string
    {
        $variant = in_array($variant, ['raw', 'full', 'regular', 'small', 'thumb'], true) ? $variant : 'regular';
        $base = Arr::get($photo, "urls.{$variant}");
        if (!$base) {
            $base = Arr::get($photo, 'urls.regular');
        }

        $transforms = array_filter($transforms, static fn ($v) => $v !== null && $v !== '' && $v !== []);
        if ($variant === 'raw' || $variant === 'full') {
            if ($transforms !== []) {
                $qs = http_build_query($transforms);
                $separator = str_contains($base, '?') ? '&' : '?';
                return $base . $separator . $qs;
            }
        }

        return $base;
    }

    private function initializeClient(): void
    {
        if ($this->isInitialized) {
            return;
        }

        $accessKey = (string) config('services.unsplash.access_key', '');

        if (blank($accessKey)) {
            throw new InvalidArgumentException('UNSPLASH_ACCESS_KEY is not configured.');
        }

        $credentials = [
            'applicationId' => $accessKey,
            'utmSource' => (string) config('services.unsplash.utm_source', config('app.name')),
        ];

        if (filled(config('services.unsplash.secret'))) {
            $credentials['secret'] = config('services.unsplash.secret');
        }

        if (filled(config('services.unsplash.callback_url'))) {
            $credentials['callbackUrl'] = config('services.unsplash.callback_url');
        }

        $accessToken = array_filter([
            'access_token' => config('services.unsplash.access_token'),
            'refresh_token' => config('services.unsplash.refresh_token'),
            'expires' => filled(config('services.unsplash.access_token_expires_at'))
                ? (int) config('services.unsplash.access_token_expires_at')
                : null,
        ], static fn (mixed $value): bool => filled($value));

        HttpClient::init($credentials, $accessToken);

        $this->isInitialized = true;
    }

    private function formatPhoto(array $photo, string $collectionId): array
    {
        return [
            'id' => Arr::get($photo, 'id'),
            'collection_id' => $collectionId,
            'description' => Arr::get($photo, 'description'),
            'alt_description' => Arr::get($photo, 'alt_description'),
            'width' => Arr::get($photo, 'width'),
            'height' => Arr::get($photo, 'height'),
            'color' => Arr::get($photo, 'color'),
            'blur_hash' => Arr::get($photo, 'blur_hash'),
            'created_at' => Arr::get($photo, 'created_at'),
            'urls' => Arr::only(Arr::get($photo, 'urls', []), ['raw', 'full', 'regular', 'small', 'thumb']),
            'links' => Arr::get($photo, 'links', []),
            'user' => [
                'name' => Arr::get($photo, 'user.name'),
                'username' => Arr::get($photo, 'user.username'),
                'links' => Arr::get($photo, 'user.links', []),
            ],
        ];
    }
}
