#!/bin/sh
set -e

cd /app

# Seed .env from the dev template on first run (the real .env is gitignored
# and lives only inside the bind-mounted volume / container).
if [ ! -f .env ]; then
  cp .env.dev .env
fi

# `php artisan serve` re-executes the app for every request and doesn't
# reliably forward container environment overrides to those child processes,
# so bake the values docker-compose sets into .env itself instead of relying
# on ambient env vars at request time.
set_env() {
  key="$1"; value="$2"
  if grep -q "^${key}=" .env; then
    sed -i "s|^${key}=.*|${key}=${value}|" .env
  else
    printf '\n%s=%s\n' "$key" "$value" >> .env
  fi
}

for key in DB_HOST DB_PORT DB_DATABASE DB_USERNAME DB_PASSWORD APP_URL SANCTUM_STATEFUL_DOMAINS SESSION_DOMAIN; do
  eval "value=\$$key"
  [ -n "$value" ] && set_env "$key" "$value"
done

# The compose vendor volume can outlive the image, so reconcile it with the
# current lock file before running any Artisan commands.
composer install --no-scripts --no-interaction --prefer-dist --no-progress

# Generate an app key if one isn't set yet.
if ! grep -q "^APP_KEY=base64:" .env 2>/dev/null; then
  php artisan key:generate --force
fi

# The shared mysql container only auto-creates the api's database on first
# boot, so create this app's database too (idempotent, safe to run every start).
php -r '
$host = getenv("DB_HOST") ?: "127.0.0.1";
$port = getenv("DB_PORT") ?: "3306";
$db = getenv("DB_DATABASE");
$user = getenv("DB_USERNAME") ?: "root";
$pass = getenv("DB_PASSWORD") ?: "";
if (!$db) { exit(0); }
for ($i = 0; $i < 30; $i++) {
    try {
        $pdo = new PDO("mysql:host=$host;port=$port", $user, $pass);
        $pdo->exec("CREATE DATABASE IF NOT EXISTS `$db`");
        exit(0);
    } catch (Throwable $e) {
        sleep(2);
    }
}
fwrite(STDERR, "create-database: giving up waiting for mysql\n");
'

attempt=1
until php artisan migrate --force; do
  if [ "$attempt" -ge 10 ]; then
    echo "migrate: giving up after $attempt attempts" >&2
    break
  fi
  echo "migrate: database not ready yet (attempt $attempt), retrying..." >&2
  attempt=$((attempt + 1))
  sleep 3
done

php artisan db:seed --force

exec "$@"
