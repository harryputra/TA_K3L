#!/usr/bin/env bash
# ======================================================================
# SIAGA POLMAN K3L — one-click runner
# Standar: ~/.claude/CLAUDE.md (mode demo vs produksi + pemisahan seed).
#
#   ./run.sh                 # = demo (lokal/dev): native PHP/Vite + hot-reload
#   ./run.sh deploy          # = produksi: SELURUH stack via Docker (app + db)
#   ./run.sh status|prod-logs|prod-down|prod-restart
#   ./run.sh help
#
# demo  = native (butuh php/composer/node di mesin dev). Data contoh + akun per-role.
# deploy= Docker (server lab TIDAK punya php/node). BERSIH; admin dari .env.
# ======================================================================
set -euo pipefail
cd "$(dirname "$0")"

# --------------------------- konfigurasi ------------------------------
DEFAULT_PORT=8000          # default port demo (native)
VITE_PORT=5173
PROD_PROJECT="ta-k3l-prod" # COMPOSE_PROJECT_NAME stack produksi
NEW_ADMIN_PW=""            # diisi bila run.sh men-generate password admin

# ------------------------------ warna ---------------------------------
if [ -t 1 ]; then
  R='\033[0;31m'; G='\033[0;32m'; Y='\033[1;33m'; B='\033[0;34m'; C='\033[0;36m'; N='\033[0m'; BOLD='\033[1m'
else R=''; G=''; Y=''; B=''; C=''; N=''; BOLD=''; fi
log(){ echo -e "${C}▶${N} $*"; }
ok(){ echo -e "${G}✔${N} $*"; }
warn(){ echo -e "${Y}⚠${N} $*"; }
err(){ echo -e "${R}✖${N} $*" >&2; }
hr(){ echo -e "${B}────────────────────────────────────────────────────────${N}"; }

# ------------------------------ helpers -------------------------------
have(){ command -v "$1" >/dev/null 2>&1; }

env_get(){ # baca 1 key dari .env (selalu return 0)
  [ -f .env ] || return 0
  grep -E "^$1=" .env 2>/dev/null | head -1 | cut -d= -f2- | sed 's/^"//; s/"$//' | tr -d '\r' || true
}

set_env(){ # set/replace key di .env
  local k="$1" v="$2" esc
  esc="$(printf '%s' "$v" | sed -e 's/[\\&|]/\\&/g')"
  if grep -qE "^${k}=" .env 2>/dev/null; then
    sed -i "s|^${k}=.*|${k}=${esc}|" .env
  else
    printf '%s=%s\n' "$k" "$v" >> .env
  fi
}

gen_secret(){ openssl rand -hex 16 2>/dev/null || { head -c 16 /dev/urandom | od -An -tx1 | tr -d ' \n'; }; }

app_port(){ local p; p="$(env_get APP_PORT)"; echo "${p:-$DEFAULT_PORT}"; }

confirm_destructive(){
  if [ -t 0 ]; then
    read -r -p "$(echo -e "$1") Ketik 'HAPUS' untuk lanjut: " a
    [ "$a" = "HAPUS" ] || { err "Dibatalkan."; exit 1; }
  else warn "Non-interaktif: lanjut tanpa konfirmasi."; fi
}

# ======================================================================
#  MODE DEMO (native, lokal/dev)
# ======================================================================
need_tools(){
  local miss=0
  for t in php composer node npm; do
    have "$t" || { err "$t tidak ada di PATH (mode demo butuh php 8.2+, composer, node, npm)."; miss=1; }
  done
  if [ "$miss" != 0 ]; then
    err "Mesin ini tanpa toolchain native."
    warn "Di SERVER (hanya Docker) gunakan: ${BOLD}./run.sh deploy${N}"
    exit 1
  fi
}

ensure_env_native(){ [ -f .env ] || { cp .env.example .env; ok ".env dibuat dari .env.example."; }; }
ensure_key_native(){
  local k; k="$(env_get APP_KEY)"
  if [ -z "$k" ]; then log "Membuat APP_KEY..."; php artisan key:generate --force >/dev/null; ok "APP_KEY dibuat."; fi
}
ensure_storage_link(){ [ -e public/storage ] || php artisan storage:link >/dev/null 2>&1 || true; }

port_busy(){
  local port="$1"
  if have ss; then ss -ltn 2>/dev/null | grep -q ":$port "
  elif have lsof; then lsof -iTCP:"$port" -sTCP:LISTEN >/dev/null 2>&1
  elif have netstat; then netstat -an 2>/dev/null | grep -E "[:.]$port[[:space:]].*LISTEN" >/dev/null
  else return 1; fi
}

do_demo(){
  need_tools; ensure_env_native; ensure_key_native
  [ -d vendor ] || { log "composer install..."; composer install; }
  [ -d node_modules ] || { log "npm install..."; npm install; }
  ensure_storage_link
  log "Migrasi + seed (esensial + demo)..."
  php artisan migrate --force
  php artisan db:seed --class=EssentialSeeder --force
  php artisan db:seed --class=DemoSeeder --force

  local port; port="$(app_port)"
  if port_busy "$port"; then
    warn "Port ${BOLD}$port${N} sudah dipakai — mungkin app sudah jalan."
    warn "Ubah ${BOLD}APP_PORT=${N} di .env atau hentikan proses lama."
  fi
  demo_summary "$port"
  log "Menjalankan dev server (serve + queue + vite). ${BOLD}Ctrl+C${N} untuk berhenti."
  SERVER_PORT="$port" npx concurrently -k -n serve,queue,vite -c "#93c5fd,#fdba74,#86efac" \
    "php artisan serve --host=127.0.0.1 --port=$port" \
    "php artisan queue:listen --tries=1 --timeout=0" \
    "npm run dev"
}

demo_summary(){
  local port="$1"; hr
  echo -e "${BOLD}${G}  SIAGA POLMAN K3L — mode DEMO (lokal/dev)${N}"; hr
  echo -e "  Web (Laravel) : ${C}http://127.0.0.1:${port}${N}"
  echo -e "  Vite (asset)  : ${C}http://127.0.0.1:${VITE_PORT}${N}  (hot reload)"
  echo -e "  ${BOLD}Akun contoh${N} (password semua: ${BOLD}password${N}):"
  echo -e "    • Admin     : ${C}admin@k3l.local${N}"
  echo -e "    • Satgas    : ${C}satgas@k3l.local${N}"
  echo -e "    • Mahasiswa : ${C}mahasiswa@k3l.local${N}"
  echo -e "  Reset data demo : ${Y}./run.sh demo-reset${N}"
  echo -e "  ${Y}Ini mode lokal/dev — untuk SERVER pakai: ${BOLD}./run.sh deploy${N}"
  hr
}

do_demo_reset(){
  need_tools; ensure_env_native; ensure_key_native
  confirm_destructive "Ini akan ${BOLD}menghapus & isi ulang${N} seluruh data DB dengan data demo."
  php artisan migrate:fresh --force
  php artisan db:seed --class=EssentialSeeder --force
  php artisan db:seed --class=DemoSeeder --force
  ok "Data demo di-reset."
}

do_demo_down(){
  warn "Mode demo berjalan di ${BOLD}foreground${N}. Hentikan dengan ${BOLD}Ctrl+C${N} di terminal-nya."
  warn "Untuk stack PRODUKSI (Docker) gunakan: ${BOLD}./run.sh prod-down${N}"
}

# ======================================================================
#  MODE DEPLOY (Docker, produksi)
# ======================================================================
need_docker(){
  have docker || { err "Docker belum terpasang."; exit 1; }
  docker info >/dev/null 2>&1 || { err "Docker daemon mati / tak bisa diakses (cek 'docker info')."; exit 1; }
  docker compose version >/dev/null 2>&1 || { err "Plugin 'docker compose' tidak tersedia."; exit 1; }
}
dc(){ docker compose "$@"; }

prod_ctx(){ export COMPOSE_PROJECT_NAME="$PROD_PROJECT"; }

# Deteksi port host yang sudah dipakai (termasuk yang dibind Docker)
host_port_busy(){
  local port="$1"
  if have ss; then ss -ltnH 2>/dev/null | awk '{print $4}' | grep -qE "[:.]${port}\$"
  elif have netstat; then netstat -ltn 2>/dev/null | awk '{print $4}' | grep -qE "[:.]${port}\$"
  else docker ps --format '{{.Ports}}' 2>/dev/null | grep -qE ":${port}->"; fi
}
# Apakah port sedang dipublish oleh container app kita sendiri? (jangan bump saat redeploy)
port_owned_by_app(){ docker ps --filter "name=${PROD_PROJECT}-app" --format '{{.Ports}}' 2>/dev/null | grep -qE ":${1}->"; }
# Cari port bebas mulai dari $1
find_free_port(){
  local p="$1" tries=0
  while host_port_busy "$p"; do
    p=$((p + 1)); tries=$((tries + 1))
    [ "$tries" -ge 50 ] && return 1
  done
  echo "$p"
}
# Pastikan APP_PORT bebas; bila bentrok (dan bukan milik app ini), pindah otomatis
ensure_free_port(){
  local port; port="$(app_port)"
  if host_port_busy "$port" && ! port_owned_by_app "$port"; then
    warn "Port ${BOLD}$port${N} sudah dipakai proses lain di host."
    local free; free="$(find_free_port "$((port + 1))" || true)"
    if [ -n "$free" ]; then
      set_env APP_PORT "$free"
      warn "Otomatis pindah ke port bebas ${BOLD}$free${N} (APP_PORT diperbarui di .env)."
      warn "➜ Arahkan Cloudflare Tunnel ke ${BOLD}127.0.0.1:$free${N} (bukan $port)."
    else
      err "Tidak menemukan port bebas dari $port. Set APP_PORT manual di .env."; exit 1
    fi
  fi
}

ensure_env_docker(){
  if [ ! -f .env ]; then cp .env.docker.example .env; ok ".env produksi dibuat dari .env.docker.example."; fi
  if [ "$(env_get DB_USERNAME)" = "root" ]; then
    warn "DB_USERNAME=root terdeteksi (mungkin .env native). Untuk Docker sebaiknya 'k3l'."
  fi
}

prep_secrets(){
  local v
  v="$(env_get DB_PASSWORD)";      case "$v" in ""|__*) set_env DB_PASSWORD "$(gen_secret)"; ok "DB_PASSWORD digenerate.";; esac
  v="$(env_get DB_ROOT_PASSWORD)"; case "$v" in ""|__*) set_env DB_ROOT_PASSWORD "$(gen_secret)"; ok "DB_ROOT_PASSWORD digenerate.";; esac
  v="$(env_get ADMIN_PASSWORD)";   case "$v" in ""|__*|password) NEW_ADMIN_PW="$(gen_secret)"; set_env ADMIN_PASSWORD "$NEW_ADMIN_PW"; warn "ADMIN_PASSWORD digenerate (lihat ringkasan).";; esac
}

warn_secrets_prod(){
  if [ "$(env_get APP_DEBUG)" = "true" ]; then warn "APP_DEBUG=true — set ${BOLD}false${N} untuk produksi."; fi
  if [ -z "$(env_get FONNTE_DEVICE_TOKEN)" ]; then warn "FONNTE_DEVICE_TOKEN kosong — notifikasi WhatsApp non-aktif sampai diisi."; fi
}

wait_ready_docker(){
  local port url t=60
  port="$(app_port)"; url="http://127.0.0.1:${port}/up"
  log "Menunggu app sehat (${url})..."
  while [ "$t" -gt 0 ]; do
    if curl -fsS "$url" >/dev/null 2>&1; then ok "App sehat (HTTP /up OK)."; return 0; fi
    t=$((t - 1)); sleep 2
  done
  warn "Belum merespon. Cek log: ${BOLD}./run.sh prod-logs${N}"
}

do_deploy(){
  need_docker; prod_ctx; ensure_env_docker
  hr; echo -e "${BOLD}  Mode PRODUKSI (Docker) — bersih, tanpa data contoh${N}"; hr
  prep_secrets
  warn_secrets_prod
  ensure_free_port
  log "Build image & start stack (app + db)..."
  dc up -d --build --remove-orphans
  wait_ready_docker
  prod_summary_docker
}

prod_summary_docker(){
  local port; port="$(app_port)"; hr
  echo -e "${BOLD}${G}  SIAGA POLMAN K3L — PRODUKSI (Docker) AKTIF${N}"; hr
  echo -e "  Web (lokal)  : ${C}http://127.0.0.1:${port}${N}  (publik via Cloudflare Tunnel)"
  echo -e "  Health       : ${C}http://127.0.0.1:${port}/up${N}"
  echo -e "  Login admin  : ${C}$(env_get ADMIN_EMAIL)${N}"
  if [ -n "$NEW_ADMIN_PW" ]; then
    echo -e "  Password admin (BARU — SIMPAN!): ${BOLD}${NEW_ADMIN_PW}${N}"
  else
    echo -e "  Password admin: sesuai ${BOLD}ADMIN_PASSWORD${N} di .env"
  fi
  echo -e "  Tanpa data contoh — hanya skema + data referensi + 1 admin."
  echo
  echo -e "  Kelola : ${C}./run.sh prod-logs${N} • ${C}./run.sh prod-restart${N} • ${C}./run.sh prod-down${N} • ${C}./run.sh status${N}"
  echo -e "  Update : ${C}git pull${N} → ${C}./run.sh deploy${N}"
  echo -e "  Publik : Cloudflare Tunnel → Public Hostname → ${BOLD}127.0.0.1:${port}${N}"
  echo -e "  TTFB   : ${C}curl -s -o /dev/null -w 'TTFB:%{time_starttransfer} Total:%{time_total}\\n' http://127.0.0.1:${port}/up${N}"
  hr
}

do_prod_down(){ need_docker; prod_ctx; ensure_env_docker; dc down; ok "Stack produksi dihentikan (data aman di volume)."; }
do_prod_restart(){ need_docker; prod_ctx; ensure_env_docker; log "Redeploy..."; dc up -d --build; wait_ready_docker; ok "Restart selesai."; }
do_prod_logs(){ need_docker; prod_ctx; ensure_env_docker; shift || true; dc logs -f --tail=100 "$@"; }
do_status(){ need_docker; prod_ctx; ensure_env_docker; hr; echo -e "${BOLD}  Status produksi (${PROD_PROJECT})${N}"; hr; dc ps; hr; }

do_reset(){
  need_docker; prod_ctx; ensure_env_docker
  confirm_destructive "${R}PERINGATAN:${N} hapus ${BOLD}SEMUA data${N} (volume DB & storage) lalu deploy ulang bersih."
  dc down -v; ok "Volume produksi dihapus."
  do_deploy
}

# ============================== UMUM =================================
do_doctor(){
  hr; echo -e "${BOLD}  Doctor${N}"; hr
  echo -e "${BOLD}Native (untuk 'demo')${N}:"
  for t in php composer node npm; do
    if have "$t"; then ok "$t: $("$t" --version 2>/dev/null | head -1)"; else warn "$t: TIDAK ADA"; fi
  done
  echo -e "${BOLD}Docker (untuk 'deploy')${N}:"
  if have docker; then ok "docker: $(docker --version 2>/dev/null)"; else warn "docker: TIDAK ADA"; fi
  if docker compose version >/dev/null 2>&1; then ok "docker compose: tersedia"; else warn "docker compose: TIDAK ADA"; fi
  [ -f .env ] && ok ".env ada" || warn ".env belum ada"
  hr
}

usage(){
  cat <<EOF
$(echo -e "${BOLD}SIAGA POLMAN K3L — runner${N}")
  $(echo -e "${BOLD}Demo (lokal/dev, native)${N}") : (kosong)|up|demo|start, demo-reset, demo-down
  $(echo -e "${BOLD}Produksi (server, Docker)${N}") : deploy|prod, prod-down, prod-restart, prod-logs, reset
  $(echo -e "${BOLD}Umum${N}")                     : status, doctor, help

  demo   = native php/vite + data contoh + akun per-role (butuh toolchain).
  deploy = SELURUH stack via Docker (app+db), seed ESENSIAL saja, admin dari .env.
EOF
}

# ------------------------------- router -------------------------------
case "${1:-up}" in
  ""|up|demo|start)     do_demo ;;
  deploy|prod)          do_deploy ;;
  demo-reset)           do_demo_reset ;;
  demo-down)            do_demo_down ;;
  prod-down|down|stop)  do_prod_down ;;
  prod-restart|restart) do_prod_restart ;;
  prod-logs|logs)       do_prod_logs "$@" ;;
  reset|hard-reset)     do_reset ;;
  status|ps)            do_status ;;
  doctor)               do_doctor ;;
  help|-h|--help)       usage ;;
  *) err "Perintah tak dikenal: $1"; echo; usage; exit 1 ;;
esac
