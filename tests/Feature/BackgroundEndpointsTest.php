<?php

use App\Services\Unsplash\UnsplashImageService;
use Illuminate\Support\Facades\Config;
use function Pest\Laravel\get;

beforeEach(function () {
    Config::set('app.debug', true);
});

function fakePhoto(): array {
    return [
        'id' => 'photo_abc',
        'description' => 'A test photo',
        'alt_description' => 'Alt',
        'width' => 1920,
        'height' => 1080,
        'color' => '#ffffff',
        'blur_hash' => 'abc123',
        'created_at' => now()->toISOString(),
        'urls' => [
            'raw' => 'https://images.unsplash.com/photo-raw',
            'full' => 'https://images.unsplash.com/photo-full',
            'regular' => 'https://images.unsplash.com/photo-regular',
            'small' => 'https://images.unsplash.com/photo-small',
            'thumb' => 'https://images.unsplash.com/photo-thumb',
        ],
        'links' => [
            'download_location' => 'https://api.unsplash.com/photos/photo_abc/download?ixid=test',
        ],
        'user' => [
            'name' => 'John Doe',
            'username' => 'john',
            'links' => [],
        ],
    ];
}

it('redirects to background image URL for general endpoint', function () {
    // Ensure no default collections are used, force via URL
    Config::set('services.unsplash.collection_ids', []);

    $mock = Mockery::mock(UnsplashImageService::class);
    $mock->shouldReceive('getRandomPhotoFromCollections')->once()->andReturn(fakePhoto());
    $mock->shouldReceive('registerDownload')->once();
    $mock->shouldReceive('buildVariantUrl')->once()->andReturn('https://example.test/img.jpg');
    $this->app->instance(UnsplashImageService::class, $mock);

    get('/api/unsplash/image/general?collections=ID1,ID2')
        ->assertRedirect('https://example.test/img.jpg');
});

it('returns JSON mode when requested', function () {
    Config::set('services.unsplash.collection_ids', []);

    $mock = Mockery::mock(UnsplashImageService::class);
    $mock->shouldReceive('getRandomPhotoFromCollections')->once()->andReturn(fakePhoto());
    $mock->shouldReceive('registerDownload')->once();
    $mock->shouldReceive('buildVariantUrl')->once()->andReturn('https://example.test/img.jpg');
    $this->app->instance(UnsplashImageService::class, $mock);

    get('/api/unsplash/image/general?collections=ID1,ID2&response=json')
        ->assertOk()
        ->assertJsonStructure(['url', 'photo' => ['id','urls','user']]);
});

it('returns 422 when no collections provided and no defaults configured', function () {
    Config::set('services.unsplash.collection_ids', []);

    get('/api/unsplash/image/general')
        ->assertStatus(422);
});

it('returns 422 when seasonal mapping missing', function () {
    Config::set('services.unsplash.collections', []);

    get('/api/unsplash/image/seasonal')
        ->assertStatus(422);
});

it('redirects to seasonal background when mapping exists', function () {
    Config::set('services.unsplash.collections', [
        'spring' => 'SPRING_COLLECTION',
    ]);

    $mock = Mockery::mock(UnsplashImageService::class);
    $mock->shouldReceive('getRandomPhotoFromCollections')->once()->andReturn(fakePhoto());
    $mock->shouldReceive('registerDownload')->once();
    $mock->shouldReceive('buildVariantUrl')->once()->andReturn('https://example.test/seasonal.jpg');
    $this->app->instance(UnsplashImageService::class, $mock);

    get('/api/unsplash/image/seasonal?season=spring')
        ->assertRedirect('https://example.test/seasonal.jpg');
});
