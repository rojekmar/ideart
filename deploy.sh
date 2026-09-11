#!/usr/bin/env bash
# Skrypt uruchamiany NA SERWERZE (VPS) przez GitHub Actions po każdym
# pushu do main. Wypakowuje nowe wydanie, podpina współdzielone zasoby
# (.env, storage, treść portfolio) i bezprzestojowo przełącza symlink
# "current" na nowe wydanie.
#
# Użycie: bash deploy.sh /sciezka/do/aplikacji
set -euo pipefail

APP_DIR="${1:?Podaj ścieżkę do aplikacji, np. /var/www/marcin-projekt}"
RELEASES_DIR="$APP_DIR/releases"
SHARED_DIR="$APP_DIR/shared"
KEEP_RELEASES=5

TIMESTAMP=$(date +%Y%m%d%H%M%S)
NEW_RELEASE="$RELEASES_DIR/$TIMESTAMP"

mkdir -p "$RELEASES_DIR" "$SHARED_DIR/storage" "$SHARED_DIR/public/assets/grafiki_animacje"
mkdir -p "$NEW_RELEASE"

echo "==> Rozpakowuję nowe wydanie do $NEW_RELEASE"
tar -xzf "$HOME/release.tar.gz" -C "$NEW_RELEASE"
rm -f "$HOME/release.tar.gz"

echo "==> Podpinam współdzielone zasoby (storage, .env, treść portfolio)"
rm -rf "$NEW_RELEASE/storage"
ln -s "$SHARED_DIR/storage" "$NEW_RELEASE/storage"
ln -sf "$SHARED_DIR/.env" "$NEW_RELEASE/.env"
ln -sfn "$SHARED_DIR/public/assets/grafiki_animacje" "$NEW_RELEASE/public/assets/grafiki_animacje"

echo "==> Optymalizacja Laravela"
cd "$NEW_RELEASE"
php artisan storage:link --force
# Ta strona nie używa bazy danych (treść jest czytana bezpośrednio z plików) —
# jeśli kiedyś dojdą funkcje wymagające bazy, odkomentuj:
# php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "==> Podmieniam symlink 'current' (bez przestoju)"
ln -sfn "$NEW_RELEASE" "$APP_DIR/current"

echo "==> Przeładowuję PHP-FPM (jeśli skonfigurowane, pomijam błąd jeśli nie)"
sudo systemctl reload php8.2-fpm 2>/dev/null || true

echo "==> Czyszczę stare wydania (zachowuję ostatnie $KEEP_RELEASES)"
cd "$RELEASES_DIR"
ls -1t | tail -n +$((KEEP_RELEASES + 1)) | xargs -r rm -rf

echo "==> Gotowe. Aktywne wydanie: $NEW_RELEASE"
