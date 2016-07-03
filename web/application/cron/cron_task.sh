#!/bin/sh

baseDirForScriptSelf=$(cd "$(dirname "$0")"; pwd)

echo `date`


#检查 weibocard_get_ocr_results.php 是否已运行 & 运行之
process_name="cron_task.php"

pid=`ps -ef | grep $process_name | grep -v grep | awk '{print $2}'`
if [ -z $pid ]; then
    echo "run $process_name!"
	/opt/php/bin/php $baseDirForScriptSelf/$process_name
else
	echo "$process_name is running!"
	echo $pid
fi