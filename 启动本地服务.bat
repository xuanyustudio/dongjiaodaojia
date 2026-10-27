@echo off
chcp 65001 >nul
echo 这是旧 PHP 站点。新 Go 后端请运行同目录下的「一键启动新后端.bat」。
echo 正在检查 MySQL、PHP 和网站...

netstat -ano | findstr ":3306" | findstr "LISTENING" >nul
if errorlevel 1 (
  echo 正在启动 MySQL 5.7 ...
  start "mysql57" /MIN "D:\phpenv\server\mysql\mysql-5.7\bin\mysqld.exe" --defaults-file=D:\phpenv\server\mysql\mysql-5.7\my.ini --basedir=D:\phpenv\server\mysql\mysql-5.7 --datadir=D:\phpenv\server\mysql\mysql-5.7\data
) else (
  echo MySQL 已在运行
)

netstat -ano | findstr ":9010" | findstr "LISTENING" >nul
if errorlevel 1 (
  echo 正在启动 PHP 7.4 ...
  start "php74" /MIN /D D:\phpenv\php\php-7.4 php-cgi.exe -b 127.0.0.1:9010 -c D:\phpenv\php\php-7.4\php.ini
) else (
  echo PHP 已在运行
)

netstat -ano | findstr ":8088" | findstr "LISTENING" >nul
if errorlevel 1 (
  echo 正在启动网站 ...
  start "nginx8088" /MIN /D D:\phpenv\server\nginx nginx.exe -p D:/work/dongjiaodaojia/backend/runtime/nginx -c conf/nginx.conf
) else (
  echo 网站已在运行
)

echo.
echo 管理后台: http://127.0.0.1:8088/admin/login
echo 账号 admin    密码 admin123
echo 用户端: http://127.0.0.1:8088/h5/
echo 技师端: http://127.0.0.1:8088/jsh5/
pause
