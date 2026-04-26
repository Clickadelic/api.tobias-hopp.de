# api.tobias-hopp.de
Personal REST API of Tobias Hopp — powered by Laravel.
## Local Development Setup
### Requirements
- PHP 8.4
- Composer
- MySQL
### Install
```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
```
### Running Tests
The test suite requires an `APP_KEY` to boot the application. It is pre-configured in `phpunit.xml` for the test environment, so no `.env.testing` is needed.
```bash
php artisan test
```
### Windows: SSL Certificate Fix
PHP on Windows ships without a CA bundle, which causes `cURL error 60` for outbound HTTPS. Fix it once per machine:
1. Download the Mozilla CA bundle:
   ```powershell
   Invoke-WebRequest -Uri "https://curl.se/ca/cacert.pem" -OutFile "$env:USERPROFILE\scoop\apps\php84\current\cacert.pem"
   ```
2. Enable it in `php.ini` (find path with `php --ini`):
   ```ini
   curl.cainfo = "C:\path\to\cacert.pem"
   openssl.cafile = "C:\path\to\cacert.pem"
   ```
## Unsplash Image API
### Endpoints
| Method | URL | Description |
|---|---|---|
| GET | `/api/unsplash/image/general` | Random photo from configured collections (redirect or JSON) |
| GET | `/api/unsplash/image/seasonal` | Random photo for the current (or specified) season (redirect or JSON) |
### Query Parameters
Both endpoints support:
- `collections=ID1,ID2` — comma-separated collection override
- `collection_ids[]=ID1` — array form
- `collection_id=ID1` — single ID
- `response=redirect|json` — response mode (default: `redirect`)
- `variant=regular|full|raw|small|thumb` — image size variant
- `strategy=random|daily` — caching strategy
- `w`, `h`, `q`, `fit` — Unsplash image transform parameters
The `/seasonal` endpoint additionally accepts:
- `season=spring|summer|autumn|winter|fall` — override auto-detected season
### Required Environment Variables
```env
UNSPLASH_ACCESS_KEY=your_access_key
UNSPLASH_COLLECTION_SPRING_ID=collection_id
UNSPLASH_COLLECTION_SUMMER_ID=collection_id
UNSPLASH_COLLECTION_AUTUMN_ID=collection_id
UNSPLASH_COLLECTION_WINTER_ID=collection_id
```
Season detection uses the Northern Hemisphere calendar. The `Season` enum (`App\Enums\Season`) resolves the collection ID automatically from config.
