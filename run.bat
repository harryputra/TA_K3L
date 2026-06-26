@echo off
REM ====================================================================
REM SIAGA POLMAN K3L - one-click runner (Laravel 12, native PHP/Vite)
REM Standar: ~/.claude/CLAUDE.md (mode demo vs produksi + pemisahan seed)
REM
REM   run.bat              = demo (lokal/dev): setup penuh + dev server
REM   run.bat deploy       = produksi: build optimized + seed ESENSIAL saja
REM   run.bat status / doctor / help
REM
REM Catatan: Windows untuk DEV/preview. Server produksi sesungguhnya
REM dijalankan di Linux lab via ./run.sh deploy (atau Docker/systemd).
REM ====================================================================
setlocal enabledelayedexpansion
cd /d "%~dp0"

set "DEFAULT_PORT=8000"
set "VITE_PORT=5173"

set "CMD=%~1"
if "%CMD%"=="" set "CMD=demo"

if /i "%CMD%"=="up"          goto demo
if /i "%CMD%"=="start"       goto demo
if /i "%CMD%"=="demo"        goto demo
if /i "%CMD%"=="deploy"      goto deploy
if /i "%CMD%"=="prod"        goto deploy
if /i "%CMD%"=="demo-reset"  goto demoreset
if /i "%CMD%"=="reset"       goto reset
if /i "%CMD%"=="hard-reset"  goto reset
if /i "%CMD%"=="status"      goto status
if /i "%CMD%"=="doctor"      goto doctor
if /i "%CMD%"=="help"        goto help
if /i "%CMD%"=="-h"          goto help
if /i "%CMD%"=="--help"      goto help

echo [x] Perintah tak dikenal: %CMD%
echo.
goto help

REM ------------------------------ prereq ------------------------------
:need_tools
where php >nul 2>nul || (echo [x] php tidak ditemukan di PATH ^(perlu PHP 8.2+^). & goto fail)
where composer >nul 2>nul || (echo [x] composer tidak ditemukan di PATH. & goto fail)
where node >nul 2>nul || (echo [x] node tidak ditemukan ^(perlu untuk build Vite^). & goto fail)
where npm >nul 2>nul || (echo [x] npm tidak ditemukan. & goto fail)
exit /b 0

REM --------------------------- env helpers ----------------------------
:ensure_env
if not exist ".env" (
  copy /y ".env.example" ".env" >nul
  echo [v] .env dibuat dari .env.example.
)
exit /b 0

:ensure_key
findstr /r /c:"^APP_KEY=." ".env" >nul 2>nul
if errorlevel 1 (
  echo [>] Membuat APP_KEY...
  php artisan key:generate --force >nul
)
exit /b 0

:read_port
set "PORT=%DEFAULT_PORT%"
for /f "usebackq tokens=2 delims==" %%a in (`findstr /b /c:"APP_PORT=" ".env" 2^>nul`) do set "PORT=%%a"
set "PORT=%PORT:"=%"
exit /b 0

REM =============================== DEMO ===============================
:demo
call :need_tools
call :ensure_env
call :ensure_key
if not exist "vendor"       (echo [>] composer install... & composer install)
if not exist "node_modules" (echo [>] npm install...      & npm install)
if not exist "public\storage" php artisan storage:link >nul 2>nul
echo [>] Migrasi database...
php artisan migrate --force
echo [>] Seed esensial...
php artisan db:seed --class=EssentialSeeder --force
echo [>] Seed data contoh (demo)...
php artisan db:seed --class=DemoSeeder --force
call :read_port
echo.
echo ============================================================
echo   SIAGA POLMAN K3L - mode DEMO (lokal/dev)
echo ============================================================
echo   Web (Laravel) : http://127.0.0.1:%PORT%
echo   Vite (asset)  : http://127.0.0.1:%VITE_PORT%  (hot reload)
echo   Akun contoh (password semua: password):
echo     - Admin     : admin@k3l.local
echo     - Satgas    : satgas@k3l.local
echo     - Mahasiswa : mahasiswa@k3l.local
echo   Portal publik bisa dipakai tanpa login.
echo   Reset data demo : run.bat demo-reset
echo   (Ini mode lokal/dev. Untuk server pakai ./run.sh deploy di Linux.)
echo ============================================================
echo.
echo [>] Menjalankan dev server (serve + queue + vite). Tutup window / Ctrl+C untuk berhenti.
set "SERVER_PORT=%PORT%"
call npx concurrently -k -n serve,queue,vite -c "#93c5fd,#fdba74,#86efac" "php artisan serve --host=127.0.0.1 --port=%PORT%" "php artisan queue:listen --tries=1 --timeout=0" "npm run dev"
goto end

REM ============================== DEPLOY ==============================
:deploy
call :need_tools
call :ensure_env
call :ensure_key
echo ============================================================
echo   Mode PRODUKSI - bersih, tanpa data contoh
echo ============================================================
findstr /r /c:"^APP_DEBUG=true" ".env" >nul 2>nul && echo [!] APP_DEBUG=true - set false untuk produksi.
findstr /r /c:"^ADMIN_PASSWORD=password" ".env" >nul 2>nul && echo [!] ADMIN_PASSWORD masih default - GANTI di .env sebelum produksi.
echo [>] composer install (produksi, tanpa dev)...
composer install --no-dev --optimize-autoloader
echo [>] Build asset (vite build)...
call npm install
call npm run build
if not exist "public\storage" php artisan storage:link >nul 2>nul
echo [>] Migrasi + seed ESENSIAL...
php artisan migrate --force
php artisan db:seed --class=EssentialSeeder --force
echo [>] Optimasi cache Laravel...
php artisan config:cache >nul
php artisan route:cache >nul
php artisan view:cache >nul
call :read_port
echo.
echo ============================================================
echo   SIAGA POLMAN K3L - mode PRODUKSI (preview Windows)
echo ============================================================
echo   Web   : http://0.0.0.0:%PORT%
echo   Login : admin dari .env (ADMIN_EMAIL / ADMIN_PASSWORD).
echo   Tanpa data contoh - hanya skema + referensi + 1 admin.
echo   CATATAN: di Windows ini berjalan FOREGROUND (preview).
echo   Server produktif sesungguhnya = Linux: ./run.sh deploy (atau Docker).
echo ============================================================
echo.
php artisan serve --host=0.0.0.0 --port=%PORT%
goto end

REM ============================ DEMO-RESET ============================
:demoreset
call :need_tools
call :ensure_env
call :ensure_key
echo [!] Ini akan MENGHAPUS dan mengisi ulang seluruh data DB dengan data demo.
set /p ANS="    Ketik HAPUS untuk lanjut: "
if /i not "%ANS%"=="HAPUS" (echo [x] Dibatalkan. & goto end)
php artisan migrate:fresh --force
php artisan db:seed --class=EssentialSeeder --force
php artisan db:seed --class=DemoSeeder --force
echo [v] Data demo di-reset.
goto end

REM ============================== RESET ===============================
:reset
call :need_tools
call :ensure_env
call :ensure_key
echo [!] PERINGATAN: menghapus SEMUA data lalu seed ESENSIAL saja (tanpa demo).
set /p ANS="    Ketik HAPUS untuk lanjut: "
if /i not "%ANS%"=="HAPUS" (echo [x] Dibatalkan. & goto end)
php artisan migrate:fresh --force
php artisan db:seed --class=EssentialSeeder --force
echo [v] Database di-reset ke kondisi esensial (produksi-bersih).
goto end

REM ============================== STATUS ==============================
:status
call :read_port
echo ============================================================
echo   Status
echo ============================================================
netstat -ano | findstr /r /c:":%PORT% .*LISTENING" >nul 2>nul
if errorlevel 1 (echo   Port %PORT%: bebas ^(tidak ada listener^).) else (echo   Port %PORT%: dipakai ^(ada listener^).)
echo   ^(Manajemen server persisten ada di Linux: ./run.sh status^)
echo ============================================================
goto end

REM ============================== DOCTOR ==============================
:doctor
echo ============================================================
echo   Doctor
echo ============================================================
where php >nul 2>nul && (for /f "delims=" %%v in ('php --version ^| findstr /n "^" ^| findstr /b "1:"') do echo [v] %%v) || echo [x] php: TIDAK ADA
where composer >nul 2>nul && echo [v] composer: ada || echo [x] composer: TIDAK ADA
where node >nul 2>nul && (for /f "delims=" %%v in ('node --version') do echo [v] node: %%v) || echo [x] node: TIDAK ADA
where npm >nul 2>nul && echo [v] npm: ada || echo [x] npm: TIDAK ADA
if exist ".env" (echo [v] .env ada) else (echo [!] .env belum ada)
if exist "vendor" (echo [v] vendor\ ada) else (echo [!] vendor\ belum ada - composer install)
if exist "node_modules" (echo [v] node_modules\ ada) else (echo [!] node_modules\ belum ada - npm install)
echo ============================================================
goto end

REM =============================== HELP ===============================
:help
echo SIAGA POLMAN K3L - runner (demo/deploy)
echo   Demo (lokal/dev)  : (kosong)^|up^|demo^|start, demo-reset, demo-down
echo   Produksi          : deploy^|prod   (Windows = preview foreground)
echo   Umum              : status, reset^|hard-reset, doctor, help
echo.
echo   demo   = data contoh + akun per-role + hot-reload Vite (default).
echo   deploy = build produksi + seed ESENSIAL saja, admin dari .env.
goto end

:fail
echo.
echo [x] Prasyarat belum lengkap. Perbaiki lalu jalankan ulang.

:end
echo.
pause
endlocal
