<?php

error_reporting(E_ALL & ~E_NOTICE);

define('APPLICATION_PATH', realpath(dirname(__FILE__).'/../'));
Yaf_Loader::import(APPLICATION_PATH.'/application/configs/SystemConfig.php');
$config = Yaf_Registry::get('config');


Logger::$PLATFORM = Logger::PLATFORM_WEB;
Logger::timeDebug('start');

session_set_cookie_params(864000,'/',str_replace('http://','',$config->host->web));
ini_set('session.gc_maxlifetime', 86400);

session_start();

$session_name=session_name();
$session_id=$_COOKIE[$session_name];

isset($session_id)?session_id($session_id):$session_id=session_id();
setcookie($session_name, $session_id, time() + 86400,'/');

header('P3P:CP="IDC DSP COR ADM DEVi TAIi PSA PSD IVAi IVDi CONi HIS OUR IND CNT"');
if($_REQUEST['trace_type'] == 'ajax')
{
    header('Content-type:application/json;charset=utf-8');
}
else
{
    header('Content-type:text/html;charset=utf-8');
}


define('APPLICATION_VIEW_SCRIPTS_PATH', sprintf('%s/application/views/scripts', APPLICATION_PATH));
$application = new Yaf_Application( APPLICATION_PATH . '/application/configs/application.ini');

$application->bootstrap()->run();

Logger::timeDebug('end');