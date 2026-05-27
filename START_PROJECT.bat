@echo off
setlocal

set "XAMPP=C:\xampp"
set "APP_URL=http://127.0.0.1/campus-lost-found/login.php"

echo Starting Apache...
tasklist /FI "IMAGENAME eq httpd.exe" | find /I "httpd.exe" >nul
if errorlevel 1 (
  start "" /MIN "%XAMPP%\apache\bin\httpd.exe"
) else (
  echo Apache is already running.
)

echo Checking XAMPP MySQL on port 3306...
powershell -NoProfile -ExecutionPolicy Bypass -Command "$c=New-Object Net.Sockets.TcpClient; try{$c.Connect('127.0.0.1',3306); exit 0}catch{exit 1}finally{$c.Close()}"
if errorlevel 1 (
  echo Starting XAMPP MySQL on the default port...
  start "" /MIN "%XAMPP%\mysql\bin\mysqld.exe" --defaults-file="%XAMPP%\mysql\bin\my.ini"
) else (
  echo MySQL is already listening on port 3306.
)

echo Opening project...
timeout /t 3 >nul
start "" "%APP_URL%"

endlocal
