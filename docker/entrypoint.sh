#!/bin/sh
# ============================================================
# Entrypoint produksi — SIAGA POLMAN K3L
# Idempoten: tunggu DB -> migrasi -> seed ESENSIAL -> cache -> serve.
# TIDAK PERNAH menjalankan seed demo/contoh di produksi.
# ============================================================
set -e
cd /app

# 1) Pastikan struktur storage ada (named volume bisa kosong saat pertama kali)
mkdir -p \
  storage/framework/cache/data \
  storage/framework/sessions \
  storage/framework/views \
  storage/logs \
  storage/app/public \
  bootstrap/cache

# 2) APP_KEY: pakai dari ENV bila di-set; jika kosong, simpan/baca dari volume
#    storage agar konsisten antar restart (tanpa perlu PHP di host).
KEYFILE=storage/app/.appkey
if [ -z "${APP_KEY:-}" ]; then
  if [ -f "$KEYFILE" ]; then
    APP_KEY="$(cat "$KEYFILE")"
  else
    APP_KEY="base64:$(php -r 'echo base64_encode(random_bytes(32));')"
    printf '%s' "$APP_KEY" > "$KEYFILE"
    echo "[entrypoint] APP_KEY baru dibuat & disimpan di volume."
  fi
  export APP_KEY
fi

# 3) Tunggu database siap (maks ~120 detik)
echo "[entrypoint] Menunggu database ${DB_HOST:-db}:${DB_PORT:-3306} ..."
i=0
until php -r '
  $h=getenv("DB_HOST")?:"db"; $p=getenv("DB_PORT")?:"3306";
  $u=getenv("DB_USERNAME")?:"root"; $w=getenv("DB_PASSWORD")?:"";
  try { new PDO("mysql:host=$h;port=$p", $u, $w, [PDO::ATTR_TIMEOUT=>2]); }
  catch (Throwable $e) { exit(1); }
' 2>/dev/null; do
  i=$((i + 1))
  if [ "$i" -ge 60 ]; then
    echo "[entrypoint] Database tidak kunjung siap. Berhenti."
    exit 1
  fi
  sleep 2
done
echo "[entrypoint] Database siap."

# 4) Discover package (manifest tidak dibuat saat build --no-scripts)
php artisan package:discover --ansi || true

# 5) Migrasi + seed ESENSIAL saja (idempoten, aman diulang)
echo "[entrypoint] Migrasi database..."
php artisan migrate --force
echo "[entrypoint] Seed esensial (referensi + admin dari .env)..."
php artisan db:seed --class=EssentialSeeder --force || true

# 6) Symlink storage publik
php artisan storage:link 2>/dev/null || true

# 7) Optimasi cache produksi (aman: tidak ada env() di app/)
echo "[entrypoint] Optimasi cache (config/route/view)..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 8) Jalankan server
echo "[entrypoint] Menjalankan FrankenPHP di :80 ..."
exec frankenphp run --config /etc/caddy/Caddyfile
