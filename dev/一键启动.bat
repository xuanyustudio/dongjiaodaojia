@echo off
chcp 65001 >nul
setlocal EnableExtensions
set "ROOT=D:\work\dongjiaodaojia\dev"
set "UPLOAD=D:\work\dongjiaodaojia\dev\uploads"
set "CLI=D:\Program Files\HBuilderX\cli.exe"
set "MYSQLD=D:\phpenv\server\mysql\mysql-5.7\bin\mysqld.exe"

echo ========================================
echo  享享到家 · 新后端一键启动
echo ========================================

echo.
echo [1/5] MySQL
netstat -ano | findstr ":3306" | findstr "LISTENING" >nul
if errorlevel 1 (
  if not exist "%MYSQLD%" (
    echo 找不到 MySQL。请先启动本机的 MySQL 5.7。
    pause
    exit /b 1
  )
  echo 正在启动 MySQL 5.7 ...
  start "mysql57" /MIN "%MYSQLD%" --defaults-file=D:\phpenv\server\mysql\mysql-5.7\my.ini --basedir=D:\phpenv\server\mysql\mysql-5.7 --datadir=D:\phpenv\server\mysql\mysql-5.7\data
  call :wait_port 3306 25
  if errorlevel 1 (
    echo MySQL 没有在 25 秒内起来。请检查 MySQL 5.7。
    pause
    exit /b 1
  )
) else (
  echo MySQL 已在运行
)

echo.
echo [2/5] 把 8088 让给 Go 后端
call :kill_dev_processes
timeout /t 1 /nobreak >nul
netstat -ano | findstr ":8088" | findstr "LISTENING" >nul
if not errorlevel 1 (
  echo 8088 仍被占用，新后端无法启动：
  netstat -ano | findstr ":8088"
  echo 请先关掉占用 8088 的程序，再重新运行本脚本。
  pause
  exit /b 1
)

echo.
echo [3/5] 编译并启动 Go 后端
cd /d "%ROOT%\server"
if not exist bin mkdir bin
go build -o bin\server.exe ./cmd/server
if errorlevel 1 (
  echo Go 编译失败。
  pause
  exit /b 1
)
start "dongjiao-server" cmd /k "title dongjiao-server & cd /d %ROOT%\server & set DJ_HTTP_ADDR=:8088& set DJ_UPLOAD_ROOT=%UPLOAD%& bin\server.exe"
call :wait_http "http://127.0.0.1:8088/health" 30
if errorlevel 1 (
  echo Go 后端没有在 30 秒内就绪。请看标题为 dongjiao-server 的窗口。
  pause
  exit /b 1
)
echo Go 后端已就绪: http://127.0.0.1:8088/health

echo.
echo [4/5] 管理后台
start "dongjiao-admin" cmd /k "title dongjiao-admin & cd /d %ROOT%\admin & npm run dev"
call :wait_port 5173 40
if errorlevel 1 (
  echo 管理后台没有起来。请看标题为 dongjiao-admin 的窗口。
) else (
  echo 管理后台: http://127.0.0.1:5173/#/admin/login
  echo 账号 admin    密码 admin123
  start "" "http://127.0.0.1:5173/#/admin/login"
)

echo.
echo [5/5] 用户端和技师端
set "BROWSER=Built"
if exist "%ProgramFiles%\Google\Chrome\Application\chrome.exe" set "BROWSER=Chrome"
if exist "%ProgramFiles(x86)%\Google\Chrome\Application\chrome.exe" set "BROWSER=Chrome"
if exist "%LocalAppData%\Google\Chrome\Application\chrome.exe" set "BROWSER=Chrome"
if "%BROWSER%"=="Built" (
  if exist "%ProgramFiles(x86)%\Microsoft\Edge\Application\msedge.exe" set "BROWSER=Edge"
  if exist "%ProgramFiles%\Microsoft\Edge\Application\msedge.exe" set "BROWSER=Edge"
)

if not exist "%CLI%" (
  echo 未找到 HBuilderX。请手动打开下面两个目录，运行到浏览器：
  echo   %ROOT%\user
  echo   %ROOT%\tech
  goto summary
)

echo 正在打开 HBuilderX ...
"%CLI%" open
set /a HBX_N=0
:wait_hbx
set /a HBX_N+=1
"%CLI%" project list >nul 2>&1
if not errorlevel 1 goto hbx_ready
if %HBX_N% GEQ 25 (
  echo HBuilderX 没有就绪。请手动运行用户端和技师端到浏览器。
  goto summary
)
timeout /t 2 /nobreak >nul
goto wait_hbx

:hbx_ready
"%CLI%" project open --path "%ROOT%\user"
"%CLI%" project open --path "%ROOT%\tech"
echo 正在把用户端运行到浏览器，首次编译会比较久 ...
"%CLI%" launch web --project "%ROOT%\user" --browser %BROWSER% --ui true
echo 正在把技师端运行到浏览器 ...
"%CLI%" launch web --project "%ROOT%\tech" --browser %BROWSER% --ui true

:summary
echo.
echo ========================================
echo  三个端都请求 http://127.0.0.1:8088
echo  管理后台  http://127.0.0.1:5173/#/admin/login
echo  用户端、技师端由 HBuilderX 打开
echo  停止请运行「停止新后端.bat」
echo ========================================
pause
exit /b 0

:wait_port
set /a LEFT=%~2
:wait_port_loop
netstat -ano | findstr ":%~1" | findstr "LISTENING" >nul
if not errorlevel 1 exit /b 0
set /a LEFT-=1
if %LEFT% LEQ 0 exit /b 1
timeout /t 1 /nobreak >nul
goto wait_port_loop

:wait_http
set /a LEFT=%~2
:wait_http_loop
curl.exe -fsS "%~1" 2>nul | findstr /I "ok" >nul
if not errorlevel 1 exit /b 0
set /a LEFT-=1
if %LEFT% LEQ 0 exit /b 1
timeout /t 1 /nobreak >nul
goto wait_http_loop

:kill_dev_processes
taskkill /FI "WINDOWTITLE eq dongjiao-server*" /T /F >nul 2>&1
taskkill /FI "WINDOWTITLE eq dongjiao-admin*" /T /F >nul 2>&1
powershell -NoProfile -Command "Get-CimInstance Win32_Process | Where-Object { $_.CommandLine -and ($_.CommandLine -like '*\dev\server\bin\server.exe*' -or ($_.Name -eq 'node.exe' -and $_.CommandLine -like '*\dev\admin*')) } | ForEach-Object { Stop-Process -Id $_.ProcessId -Force -ErrorAction SilentlyContinue }"
exit /b 0