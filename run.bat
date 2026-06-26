@echo off
REM ====================================================================
REM SIAGA POLMAN K3L - one-click runner
REM Standar: ~/.claude/CLAUDE.md (mode demo vs produksi + pemisahan seed)
REM
REM   run.bat              = demo (lokal/dev, native php/vite)
REM   run.bat deploy       = produksi: SELURUH stack via Docker (app + db)
REM   run.bat status / prod-logs / prod-down / prod-restart / help
REM ====================================================================
setlocal enabledelayedexpansion
cd /d "%~dp0"

set "DEFAULT_PORT=8000"
set "VITE_PORT=5173"
set "PROD_PROJECT=ta-k3l-prod"

set "CMD=%~1"
if "%CMD%"=="" set "CMD=demo"

if /i "%CMD%"=="up"           goto demo
if /i "%CMD%"=="start"        goto demo
if /i "%CMD%"=="demo"         goto demo
if /i "%CMD%"=="deploy"       goto deploy
if /i "%CMD%"=="prod"         goto deploy
if /i "%CMD%"=="demo-reset"   goto demoreset
if /i "%CMD%"=="reset"        goto reset
if /i "%CMD%"=="hard-reset"   goto reset
if /i "%CMD%"=="prod-down"    goto proddown
if /i "%CMD%"=="down"         goto proddown
if /i "%CMD%"=="stop"         goto proddown
if /i "%CMD%"=="prod-restart" goto prodrestart
if /i "%CMD%"=="restart"      goto prodrestart
if /i "%CMD%"=="prod-logs"    goto prodlogs
if /i "%CMD%"=="logs"         goto prodlogs
if /i "%CMD%"=="status"       goto status
if /i "%CMD%"=="doctor"       goto doctor
if /i "%CMD%"=="help"         goto help
if /i "%CMD%"=="-h"           goto help
if /i "%CMD%"=="--help"       goto help

echo [x] Perintah tak dikenal: %CMD%
echo.
goto help

REM --------------------------- prasyarat ------------------------------
:need_tools
where php >nul 2>nul || (echo [x] php tidak ditemukan ^(mode demo perlu PHP 8.2+^). & goto fail)
where composer >nul 2>nul || (echo [x] composer tidak ditemukan. & goto fail)
where node >nul 2>nul || (echo [x] node tidak ditemukan. & goto fail)
where npm >nul 2>nul || (echo [x] npm tidak ditemukan. & goto fail)
exit /b 0

:need_docker
where docker >nul 2>nul || (echo [x] Docker tidak ditemukan. Pasang Docker Desktop. & goto fail)
docker info >nul 2>nul || (echo [x] Docker daemon mati. Jalankan Docker Desktop dulu. & goto fail)
exit /b 0

:read_port
set "PORT=%DEFAULT_PORT%"
if exist ".env" for /f "usebackq tokens=2 delims==" %%a in (`findstr /b /c:"APP_PORT=" ".env" 2^>nul`) do set "PORT=%%a"
set "PORT=%PORT:"=%"
exit /b 0

:ensure_free_port
REM Lewati bila port memang milik container app kita (redeploy)
docker ps --filter "name=%PROD_PROJECT%-app" --format "{{.Ports}}" 2>nul | findstr ":%PORT%->" >nul 2>nul && exit /b 0
:efp_check
netstat -ano | findstr /r /c:":%PORT% .*LISTENING" >nul 2>nul
if errorlevel 1 goto efp_write
echo [!] Port %PORT% dipakai proses lain - mencari port bebas...
set /a PORT+=1
goto efp_check
:efp_write
powershell -NoProfile -Command "(Get-Content .env) -replace '^APP_PORT=.*', 'APP_PORT=%PORT%' | Set-Content .env -Encoding utf8"
echo [v] Memakai port %PORT% (APP_PORT diperbarui di .env).
exit /b 0

REM =============================== DEMO ===============================
:demo
call :need_tools
if not exist ".env" (copy /y ".env.example" ".env" >nul & echo [v] .env dibuat dari .env.example.)
findstr /r /c:"^APP_KEY=." ".env" >nul 2>nul || (echo [^>] Membuat APP_KEY... & php artisan key:generate --force >nul)
if not exist "vendor"       (echo [^>] composer install... & composer install)
if not exist "node_modules" (echo [^>] npm install...      & npm install)
if not exist "public\storage" php artisan storage:link >nul 2>nul
echo [^>] Migrasi + seed (esensial + demo)...
php artisan migrate --force
php artisan db:seed --class=EssentialSeeder --force
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
echo   Reset data demo : run.bat demo-reset
echo   (Ini mode lokal/dev. Untuk server: ./run.sh deploy di Linux.)
echo ============================================================
echo.
echo [^>] Menjalankan dev server (serve + queue + vite). Ctrl+C untuk berhenti.
set "SERVER_PORT=%PORT%"
call npx concurrently -k -n serve,queue,vite -c "#93c5fd,#fdba74,#86efac" "php artisan serve --host=127.0.0.1 --port=%PORT%" "php artisan queue:listen --tries=1 --timeout=0" "npm run dev"
goto end

REM ============================== DEPLOY ==============================
:deploy
call :need_docker
set "COMPOSE_PROJECT_NAME=%PROD_PROJECT%"
if not exist ".env" (copy /y ".env.docker.example" ".env" >nul & echo [v] .env produksi dibuat dari .env.docker.example.)
echo [^>] Menyiapkan secret produksi (.env)...
set "ADMINPW="
for /f "delims=" %%p in ('powershell -NoProfile -ExecutionPolicy Bypass -File "docker\prep-env.ps1" -EnvFile ".env"') do set "ADMINPW=%%p"
findstr /r /c:"^APP_DEBUG=true" ".env" >nul 2>nul && echo [!] APP_DEBUG=true - set false untuk produksi.
call :read_port
call :ensure_free_port
echo [^>] Build image ^& start stack (app + db)...
docker compose up -d --build --remove-orphans
if errorlevel 1 (echo [x] docker compose gagal. & goto end)
echo [^>] Menunggu app sehat (http://127.0.0.1:%PORT%/up)...
set /a TRIES=0
:waitloop
where curl >nul 2>nul && curl -fsS "http://127.0.0.1:%PORT%/up" >nul 2>nul && goto waitok
set /a TRIES+=1
if %TRIES% geq 30 goto waitdone
timeout /t 2 /nobreak >nul
goto waitloop
:waitok
echo [v] App sehat.
:waitdone
echo.
echo ============================================================
echo   SIAGA POLMAN K3L - PRODUKSI (Docker) AKTIF
echo ============================================================
echo   Web (lokal)  : http://127.0.0.1:%PORT%  (publik via Cloudflare Tunnel)
echo   Health       : http://127.0.0.1:%PORT%/up
if defined ADMINPW if not "%ADMINPW%"=="" (
  echo   Password admin (BARU - SIMPAN!): %ADMINPW%
) else (
  echo   Password admin: sesuai ADMIN_PASSWORD di .env
)
echo   Tanpa data contoh - hanya skema + referensi + 1 admin.
echo   Kelola: run.bat prod-logs / prod-restart / prod-down / status
echo   Update: git pull lalu run.bat deploy
echo ============================================================
goto end

REM ============================ DEMO-RESET ============================
:demoreset
call :need_tools
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
call :need_docker
set "COMPOSE_PROJECT_NAME=%PROD_PROJECT%"
echo [!] PERINGATAN: menghapus SEMUA data (volume DB ^& storage) lalu deploy ulang.
set /p ANS="    Ketik HAPUS untuk lanjut: "
if /i not "%ANS%"=="HAPUS" (echo [x] Dibatalkan. & goto end)
docker compose down -v
goto deploy

REM ============================ PROD MGMT =============================
:proddown
call :need_docker
set "COMPOSE_PROJECT_NAME=%PROD_PROJECT%"
docker compose down
echo [v] Stack produksi dihentikan (data aman di volume).
goto end

:prodrestart
call :need_docker
set "COMPOSE_PROJECT_NAME=%PROD_PROJECT%"
docker compose up -d --build
echo [v] Restart/redeploy selesai.
goto end

:prodlogs
call :need_docker
set "COMPOSE_PROJECT_NAME=%PROD_PROJECT%"
echo [^>] Ctrl+C untuk keluar dari log (container tetap jalan).
docker compose logs -f --tail=100
goto end

REM ============================== STATUS ==============================
:status
call :need_docker
set "COMPOSE_PROJECT_NAME=%PROD_PROJECT%"
echo ============================================================
echo   Status produksi (%PROD_PROJECT%)
echo ============================================================
docker compose ps
goto end

REM ============================== DOCTOR ==============================
:doctor
echo ============================================================
echo   Doctor
echo ============================================================
echo [Native - untuk 'demo']
where php >nul 2>nul && echo [v] php: ada || echo [!] php: TIDAK ADA
where composer >nul 2>nul && echo [v] composer: ada || echo [!] composer: TIDAK ADA
where node >nul 2>nul && echo [v] node: ada || echo [!] node: TIDAK ADA
where npm >nul 2>nul && echo [v] npm: ada || echo [!] npm: TIDAK ADA
echo [Docker - untuk 'deploy']
where docker >nul 2>nul && echo [v] docker: ada || echo [!] docker: TIDAK ADA
if exist ".env" (echo [v] .env ada) else (echo [!] .env belum ada)
echo ============================================================
goto end

REM =============================== HELP ===============================
:help
echo SIAGA POLMAN K3L - runner
echo   Demo (lokal/dev, native)  : (kosong)^|up^|demo^|start, demo-reset, demo-down
echo   Produksi (server, Docker) : deploy^|prod, prod-down, prod-restart, prod-logs, reset
echo   Umum                      : status, doctor, help
echo.
echo   demo   = native php/vite + data contoh + akun per-role.
echo   deploy = SELURUH stack via Docker (app+db), seed ESENSIAL saja, admin dari .env.
goto end

:fail
echo.
echo [x] Prasyarat belum lengkap. Perbaiki lalu jalankan ulang.

:end
echo.
pause
endlocal
