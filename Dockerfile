# syntax=docker/dockerfile:1
# ============================================================
# SIAGA POLMAN K3L — image produksi (Laravel 12 + FrankenPHP)
# Multi-stage: build asset (node) → vendor (composer) → runtime (frankenphp)
# Server lab TIDAK punya PHP/Node — semua build terjadi di dalam Docker.
# ============================================================

# ---------- Stage 1: build asset frontend (Vite/Tailwind) ----------
FROM node:20-alpine AS assets
WORKDIR /app
COPY package.json package-lock.json ./
RUN npm ci
COPY . .
RUN npm run build

# ---------- Stage 2: dependency PHP produksi (tanpa dev) ----------
# Catatan: image composer ini tidak punya semua ekstensi (mis. gd) yang
# diminta paket. Ekstensi nyata ada di stage runtime, jadi cek platform
# diabaikan di sini (--ignore-platform-reqs); hanya resolusi & download paket.
FROM composer:2 AS vendor
WORKDIR /app
COPY . .
RUN composer install --no-dev --optimize-autoloader --no-scripts --no-interaction --prefer-dist --ignore-platform-reqs

# ---------- Stage 3: runtime FrankenPHP ----------
FROM dunglas/frankenphp:1-php8.4 AS runtime

# Ekstensi PHP yang dibutuhkan (MySQL, phpspreadsheet, dll)
RUN install-php-extensions \
    pdo_mysql \
    mbstring \
    zip \
    gd \
    bcmath \
    intl \
    exif \
    opcache \
    pcntl

WORKDIR /app

# Konfigurasi PHP & web server
COPY docker/php.ini /usr/local/etc/php/conf.d/zz-app.ini
COPY docker/Caddyfile /etc/caddy/Caddyfile

# Kode aplikasi + vendor (tanpa node_modules), lalu asset hasil build
COPY --from=vendor /app /app
COPY --from=assets /app/public/build /app/public/build

# Entrypoint (normalkan CRLF agar tidak gagal di server)
COPY docker/entrypoint.sh /usr/local/bin/entrypoint
RUN sed -i 's/\r$//' /usr/local/bin/entrypoint \
    && chmod +x /usr/local/bin/entrypoint \
    && mkdir -p storage/framework/cache/data storage/framework/sessions storage/framework/views storage/logs storage/app/public bootstrap/cache \
    && chmod -R ug+rwX storage bootstrap/cache

# Serve HTTP-only di :80 (HTTPS di-handle Cloudflare Tunnel)
ENV SERVER_NAME=:80 \
    APP_ENV=production \
    APP_DEBUG=false

EXPOSE 80
ENTRYPOINT ["entrypoint"]
