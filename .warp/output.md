Status update: I fixed the Sanctum wiring, made login public, registered your UUID token model, and verified migrations. I also created a test user and generated a working personal access token.

What I changed
•  routes/api.php
◦  Moved POST /api/login outside the auth:sanctum group so users can obtain a token.
◦  Imported Hash and left protected resources under auth:sanctum.
•  app/Models/User.php
◦  Removed your custom createToken() and tokens() overrides so HasApiTokens handles token generation/relations correctly.
•  app/Providers/AppServiceProvider.php
◦  Registered your custom UUID token model with Sanctum via Sanctum::usePersonalAccessTokenModel(App\Models\PersonalAccessToken::class).

What I verified
•  Routes: /api/login is public; /api/hyperlinks, /api/categories, and /api/user are protected by auth:sanctum. (Checked via php artisan route:list.)
•  Migrations: personal_access_tokens (UUID id) is applied. (Checked via php artisan migrate:status.)
•  Token creation works: I generated a token for a test user.

Your test token
•  I created user dev@example.com with password password and generated a token:
  431be6ee-c932-409a-b914-58d32082e4ea|gTDMK8B2Ob58HnjCDC4cnfCboMeFmKYoD64M5bFZ7c69f95c
•  This is a valid Sanctum personal access token. Use the full string after login in the Authorization header.

How to test locally
•  Start the app (if not already): php artisan serve
•  Log in to get a token (replace credentials as needed):
◦  POST http://127.0.0.1:8000/api/login
◦  Body JSON: { "email": "dev@example.com", "password": "password" }
•  Call a protected route with the token:
◦  Header: Authorization: Bearer 431be6ee-c932-409a-b914-58d32082e4ea|gTDMK8B2Ob58HnjCDC4cnfCboMeFmKYoD64M5bFZ7c69f95c
◦  GET http://127.0.0.1:8000/api/user

If you want, I can:
•  Add a /api/logout endpoint that deletes the current token.
•  Add a quick HTTP test to automatically verify login + authorized access.

Summary of changes: made login accessible, aligned the User model with Sanctum’s HasApiTokens, registered your UUID token model, confirmed the migrations and generated a working token.