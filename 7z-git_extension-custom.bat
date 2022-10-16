@echo off

:: 压缩module中加入git管理的模块目录


pushd  %cd%

cd /d %~dp0

Set yyyymmdd=%Date:~0,4%%Date:~5,2%%Date:~8,2%
Set datetime=%date:~0,4%%date:~5,2%%date:~8,2%%time:~0,2%%time:~3,2%%time:~6,2%


7z a -t7z "backup\git-zentao_extesion_custom-%datetime%.7z" .git\ extension\custom\

popd
