@echo off

:: 压缩custom的二开包


pushd  %cd%

cd /d %~dp0

Set yyyymmdd=%Date:~0,4%%Date:~5,2%%Date:~8,2%
Set datetime=%date:~0,4%%date:~5,2%%date:~8,2%%time:~0,2%%time:~3,2%%time:~6,2%

set zipfile=zentao_extension_custom_%yyyymmdd%.zip
if exist %zipfile% del %zipfile%

7z a -xr!custom\banniu_rel*.sql -xr!custom\zentao*bk.sql -xr!custom\banniu_uninstall_rel*.sql -xr!custom\*客户*.* -xr!custom\*禅道*.* -xr!custom\*星链*.* -xr!custom\json2csv.py -xr!custom\demo %zipfile% custom

set sqlfile=banniu_rel%yyyymmdd:~2,6%.sql
if exist %sqlfile% del %sqlfile%
copy /Y custom\%sqlfile% .

popd
