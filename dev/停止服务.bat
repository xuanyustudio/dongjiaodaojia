@echo off
chcp 65001 >nul
setlocal EnableExtensions
echo 正在停止 Go 后端和管理后台 ...
taskkill /FI "WINDOWTITLE eq dongjiao-server*" /T /F >nul 2>&1
taskkill /FI "WINDOWTITLE eq dongjiao-admin*" /T /F >nul 2>&1
powershell -NoProfile -Command "Get-CimInstance Win32_Process | Where-Object { $_.CommandLine -and ($_.CommandLine -like '*\dev\server\bin\server.exe*' -or ($_.Name -eq 'node.exe' -and $_.CommandLine -like '*\dev\admin*')) } | ForEach-Object { Stop-Process -Id $_.ProcessId -Force -ErrorAction SilentlyContinue }"
echo 已停止。
echo HBuilderX 里的用户端、技师端请在 HBuilderX 中点击停止运行。
echo MySQL 保持运行，下次启动更快。
pause