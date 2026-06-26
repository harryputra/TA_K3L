#!/usr/bin/env bash
# ======================================================================
# SIAGA POLMAN K3L — one-click runner (Laravel 12, native PHP/Vite)
# Standar: ~/.claude/CLAUDE.md (mode demo vs produksi + pemisahan seed).
#
#   ./run.sh                 # = demo (lokal/dev): setup penuh + dev server
#   ./run.sh deploy          # = produksi: build optimized + seed ESENSIAL saja
#   ./run.sh status          # status server produksi
#   ./run.sh help
#
# demo  = data contoh + akun per-role (showcase/testing, hot-reload Vite).
# deploy= BERSIH tanpa data contoh; admin diambil dari .env (ADMIN_*).
#
# CATATAN: project ini berjalan native (BUKAN Docker). `deploy` menjalankan
# server produksi yang optimized + persisten (lepas dari terminal). Untuk
# server lab yang lebih tahan banting, disarankan containerize (bisa
# ditambahkan kemudian) atau pasang sebagai unit systemd.
# ======================================================================
set -euo pipefail
cd "$(dirname "$0")"

# --------------------------- konfigurasi ------------------------------
DEFAULT_PORT=8000
VITE_PORT=5173
PID_DIR="storage/app/run"
PID_FILE="$PID_DIR/prod-serve.pid"
PROD_LOG="storage/logs/prod-serve.log"

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

env_get(){ # baca 1 key dari .env
  [ -f .env ] || return 0
  grep -E "^$1=" .env | head -1 | cut -d= -f2- | sed 's/^"//; s/"$//' | tr -d '\r'
}

app_port(){ local p; p="$(env_get APP_PORT)"; echo "${p:-$DEFAULT_PORT}"; }

need_tools(){
  local miss=0
  for t in php composer; do
    have "$t" || { err "$t tidak ditemukan di PATH. Pasang dulu (php 8.2+, composer)."; miss=1; }
  done
  for t in node npm; do
    have "$t" || { err "$t tidak ditemukan (perlu untuk build asset Vite)."; miss=1; }
  done
  [ "$miss" = 0 ] || { err "Lengkapi prasyarat lalu jalankan ulang."; exit 1; }
}

ensure_env(){
  if [ ! -f .env ]; then cp .env.example .env; ok ".env dibuat dari .env.example."; fi
}

ensure_key(){
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

migrate(){ log "Migrasi database..."; php artisan migrate --force; }
seed_essential(){ log "Seed esensial (referensi/lookup + admin dari .env)..."; php artisan db:seed --class=EssentialSeeder --force; }
seed_demo(){ log "Seed data contoh (demo)..."; php artisan db:seed --class=DemoSeeder --force; }

# ----------------- proses server produksi (PID) -----------------------
prod_running(){ [ -f "$PID_FILE" ] && kill -0 "$(cat "$PID_FILE" 2>/dev/null)" 2>/dev/null; }
prod_stop_silent(){
  if prod_running; then kill "$(cat "$PID_FILE")" 2>/dev/null || true; sleep 1; fi
  rm -f "$PID_FILE" 2>/dev/null || true
}

# =============================== DEMO =================================
do_demo(){
  need_tools; ensure_env; ensure_key
  [ -d vendor ] || { log "composer install (lengkap dengan dev)..."; composer install; }
  [ -d node_modules ] || { log "npm install..."; npm install; }
  ensure_storage_link
  migrate; seed_essential; seed_demo

  local port; port="$(app_port)"
  if port_busy "$port"; then
    warn "Port ${BOLD}$port${N} sudah dipakai — kemungkinan app sudah jalan (mungkin mode produksi)."
    warn "Server: pakai ${BOLD}./run.sh deploy${N} • Lokal: ubah ${BOLD}APP_PORT=${N} di .env lalu jalankan lagi."
  fi

  demo_summary "$port"
  log "Menjalankan dev server (serve + queue + vite). Tekan ${BOLD}Ctrl+C${N} untuk berhenti."
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
  echo -e "  Portal publik bisa dipakai tanpa login."
  echo -e "  Reset data demo : ${Y}./run.sh demo-reset${N}"
  echo -e "  ${Y}Ini mode lokal/dev — untuk SERVER pakai: ${BOLD}./run.sh deploy${N}"
  hr
}

# ============================== DEPLOY ===============================
warn_secrets(){
  local v
  v="$(env_get APP_KEY)";   [ -z "$v" ] && warn "APP_KEY masih kosong."
  v="$(env_get APP_DEBUG)"; [ "$v" = "true" ] && warn "APP_DEBUG=true — set ${BOLD}false${N} untuk produksi."
  v="$(env_get APP_ENV)";   [ "$v" = "production" ] || warn "APP_ENV bukan 'production' (sekarang: '${v:-?}')."
  v="$(env_get ADMIN_PASSWORD)"
  if [ -z "$v" ] || [ "$v" = "password" ]; then
    warn "ADMIN_PASSWORD masih default — ${BOLD}GANTI${N} di .env sebelum produksi."
  fi
}

do_deploy(){
  need_tools; ensure_env; ensure_key
  hr; echo -e "${BOLD}  Mode PRODUKSI — bersih, tanpa data contoh${N}"; hr
  warn_secrets

  log "composer install (produksi, tanpa dev, optimized autoloader)..."
  composer install --no-dev --optimize-autoloader
  log "Build asset frontend (vite build)..."
  npm install
  npm run build
  ensure_storage_link
  migrate; seed_essential

  log "Optimasi cache Laravel (config/route/view)..."
  php artisan config:cache >/dev/null
  php artisan route:cache >/dev/null
  php artisan view:cache >/dev/null

  local port; port="$(app_port)"
  prod_stop_silent
  mkdir -p "$PID_DIR"
  log "Menjalankan server produksi di 0.0.0.0:${port} (background, lepas dari terminal)..."
  nohup php artisan serve --host=0.0.0.0 --port="$port" >"$PROD_LOG" 2>&1 &
  echo $! > "$PID_FILE"
  sleep 2
  if prod_running; then ok "Server produksi aktif (PID $(cat "$PID_FILE"))."; else
    err "Server gagal start. Lihat log: ${BOLD}./run.sh prod-logs${N}"; exit 1; fi
  prod_summary "$port"
}

prod_summary(){
  local port="$1"; hr
  echo -e "${BOLD}${G}  SIAGA POLMAN K3L — mode PRODUKSI${N}"; hr
  echo -e "  Web   : ${C}http://0.0.0.0:${port}${N}  (proxy reverse → 127.0.0.1:${port})"
  echo -e "  Login : pakai ${BOLD}admin${N} dari .env (ADMIN_EMAIL / ADMIN_PASSWORD)."
  echo -e "  Tanpa data contoh — hanya skema + data referensi + 1 admin."
  echo
  echo -e "  Kelola: ${C}./run.sh prod-logs${N} (lihat log) • ${C}./run.sh prod-restart${N} • ${C}./run.sh prod-down${N}"
  echo -e "  Update: ${C}git pull${N} → ${C}./run.sh deploy${N}"
  echo -e "  ${Y}Catatan: 'php artisan serve' = stop-gap. Untuk server produktif,${N}"
  echo -e "  ${Y}disarankan containerize (Docker) atau pasang sebagai unit systemd.${N}"
  hr
}

do_prod_down(){
  if prod_running; then kill "$(cat "$PID_FILE")" 2>/dev/null || true; rm -f "$PID_FILE"; ok "Server produksi dihentikan (data aman)."
  else warn "Tidak ada server produksi yang tercatat berjalan."; rm -f "$PID_FILE" 2>/dev/null || true; fi
}

do_prod_logs(){
  [ -f "$PROD_LOG" ] || { warn "Belum ada log produksi ($PROD_LOG)."; return 0; }
  log "Menampilkan log produksi (Ctrl+C keluar dari log, server tetap jalan)..."
  tail -f -n 100 "$PROD_LOG"
}

# =============================== UMUM ================================
do_demo_down(){
  warn "Mode demo berjalan di ${BOLD}foreground${N}. Hentikan dengan ${BOLD}Ctrl+C${N} di terminal-nya."
  warn "Untuk menghentikan server PRODUKSI gunakan: ${BOLD}./run.sh prod-down${N}"
}

confirm_destructive(){
  if [ -t 0 ]; then
    read -r -p "$1 Ketik 'HAPUS' untuk lanjut: " a
    [ "$a" = "HAPUS" ] || { err "Dibatalkan."; exit 1; }
  else warn "Non-interaktif: lanjut tanpa konfirmasi."; fi
}

do_demo_reset(){
  need_tools; ensure_env; ensure_key
  confirm_destructive "Ini akan ${BOLD}menghapus & isi ulang${N} seluruh data DB dengan data demo."
  log "migrate:fresh + seed esensial + demo..."
  php artisan migrate:fresh --force
  seed_essential; seed_demo
  ok "Data demo di-reset."
}

do_reset(){
  need_tools; ensure_env; ensure_key
  confirm_destructive "${R}PERINGATAN:${N} ini ${BOLD}menghapus SEMUA data${N} lalu seed ESENSIAL saja (tanpa demo)."
  log "migrate:fresh + seed esensial..."
  php artisan migrate:fresh --force
  seed_essential
  ok "Database di-reset ke kondisi esensial (produksi-bersih)."
}

do_status(){
  hr; echo -e "${BOLD}  Status${N}"; hr
  local port; port="$(app_port)"
  if prod_running; then ok "Server PRODUKSI: AKTIF (PID $(cat "$PID_FILE"), port $port)."
  else warn "Server PRODUKSI: tidak berjalan."; fi
  if port_busy "$port"; then echo -e "  Port ${BOLD}$port${N}: ${G}dipakai${N} (ada listener)."
  else echo -e "  Port ${BOLD}$port${N}: ${Y}bebas${N}."; fi
  hr
}

do_doctor(){
  hr; echo -e "${BOLD}  Doctor${N}"; hr
  for t in php composer node npm; do
    if have "$t"; then ok "$t: $("$t" --version 2>/dev/null | head -1)"; else err "$t: TIDAK ADA"; fi
  done
  [ -f .env ] && ok ".env ada" || warn ".env belum ada (akan dibuat dari .env.example)"
  [ -d vendor ] && ok "vendor/ ada" || warn "vendor/ belum ada (composer install)"
  [ -d node_modules ] && ok "node_modules/ ada" || warn "node_modules/ belum ada (npm install)"
  [ -n "$(env_get APP_KEY)" ] && ok "APP_KEY terisi" || warn "APP_KEY kosong"
  hr
}

usage(){
  cat <<EOF
$(echo -e "${BOLD}SIAGA POLMAN K3L — runner (demo/deploy)${N}")
  $(echo -e "${BOLD}Demo (lokal/dev)${N}")  : (kosong)|up|demo|start, demo-reset, demo-down
  $(echo -e "${BOLD}Produksi (server)${N}") : deploy|prod, prod-down, prod-restart, prod-logs
  $(echo -e "${BOLD}Umum${N}")             : status, reset|hard-reset, doctor, help

  demo   = data contoh + akun per-role + hot-reload Vite (default).
  deploy = build produksi + seed ESENSIAL saja, admin dari .env (BERSIH).
EOF
}

# ------------------------------- router -------------------------------
case "${1:-up}" in
  ""|up|demo|start)   do_demo ;;
  deploy|prod)        do_deploy ;;
  demo-reset)         do_demo_reset ;;
  demo-down)          do_demo_down ;;
  prod-down|down|stop) do_prod_down ;;
  prod-restart|restart) do_deploy ;;
  prod-logs|logs)     do_prod_logs ;;
  reset|hard-reset)   do_reset ;;
  status|ps)          do_status ;;
  doctor)             do_doctor ;;
  help|-h|--help)     usage ;;
  *) err "Perintah tak dikenal: $1"; echo; usage; exit 1 ;;
esac
