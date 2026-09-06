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

# Generate an app key if one isn't set yet.
if ! grep -q "^APP_KEY=base64:" .env 2>/dev/null; then
  php artisan key:generate --force
fi

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

exec "$@"
