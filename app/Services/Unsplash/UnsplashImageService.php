<?php

namespace App\Services\Unsplash;

use Illuminate\Support\Arr;
use InvalidArgumentException;
use Unsplash\Collection as UnsplashCollection;
use Unsplash\HttpClient;

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
