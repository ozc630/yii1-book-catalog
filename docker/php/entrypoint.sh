#!/usr/bin/env bash
set -euo pipefail

DB_HOST="${DB_HOST:-db}"
DB_PORT="${DB_PORT:-3306}"
DB_USER="${DB_USER:-books_user}"
DB_PASSWORD="${DB_PASSWORD:-books_pass}"

if [ ! -f composer.json ]; then
  echo "composer.json not found, aborting"
  exit 1
fi

if [ ! -d vendor ]; then
  echo "Installing PHP dependencies..."
  composer install --no-interaction --prefer-dist
fi

echo "Waiting for MySQL at ${DB_HOST}:${DB_PORT}..."
until mysqladmin ping -h"${DB_HOST}" -P"${DB_PORT}" -u"${DB_USER}" -p"${DB_PASSWORD}" --protocol=tcp --ssl=0 --silent; do
  sleep 2
done

echo "Running migrations..."
php yiic migrate --interactive=0 || {
  echo "Migration failed"
  exit 1
}

exec "$@"
