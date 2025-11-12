#!/usr/bin/env bash
set -euo pipefail

export COMPOSER_ALLOW_SUPERUSER=1
export COMPOSER_CACHE_DIR=/tmp/composer-cache

APP_DIR="/var/www/html"
TMP_DIR="/tmp/laravel_build"

set_env_kv() {
  local file="$1" key="$2" val="$3"
  php -r '
    $f=$argv[1]; $k=$argv[2]; $v=$argv[3];
    $env=file_exists($f)?file_get_contents($f):"";
    $kq=preg_quote($k,"/");
    if(preg_match("/^$kq=.*/m",$env)){$env=preg_replace("/^$kq=.*/m",$k."=".$v,$env);}
    else{$env.="\n".$k."=".$v;}
    file_put_contents($f,$env);
  ' "$file" "$key" "$val"
}

bootstrap_laravel() {
  rm -rf "$TMP_DIR" && mkdir -p "$TMP_DIR"

  echo ">> Laravel létrehozása: $TMP_DIR"
  composer create-project --prefer-dist laravel/laravel:^11.0 "$TMP_DIR" \
    --no-interaction --no-progress --no-ansi --no-scripts

  cd "$TMP_DIR"

  echo ">> Csomagok telepítése"
  composer install --no-interaction --no-progress --no-ansi

  if [ ! -f ".env" ]; then
    cp .env.example .env
    php artisan key:generate
  fi

  echo ">> Projekt szinkronizálása bind-mountra: $APP_DIR"
  rsync -a --delete "$TMP_DIR"/ "$APP_DIR"/
}

# Bootstrap, ha még nincs app
if [ ! -f "$APP_DIR/artisan" ]; then
  bootstrap_laravel
fi

cd "$APP_DIR"

mkdir -p storage/logs || true
touch storage/logs/laravel.log || true
chown -R www-data:www-data storage bootstrap/cache || true

php artisan config:clear || true

attempts=0
until php artisan migrate --force; do
  attempts=$((attempts+1))
  if [ "$attempts" -ge 10 ]; then
    echo "!! Migráció nem sikerült 10 próbálkozás után" >&2
    break
  fi
  echo ">> Várakozás DB-re... próbálkozás #$attempts"
  sleep 3
done

php artisan storage:link || true

exec "$@"
