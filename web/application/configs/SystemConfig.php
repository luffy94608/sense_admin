<?php

date_default_timezone_set('Asia/Shanghai');
error_reporting(E_ALL & ~E_NOTICE);

defined('ERROR_LOG_FILE') || define('ERROR_LOG_FILE', 'error');

defined('APPLICATION_PATH') || define('APPLICATION_PATH', realpath(dirname(__DIR__).'/../'));
defined('LIB_PATH') || define('LIB_PATH', realpath(APPLICATION_PATH . '/library'));
defined('WECHAT_CONFIG_PATH') || define('WECHAT_CONFIG_PATH', realpath(APPLICATION_PATH . '/../../config'));
defined('CONFIG') || define('CONFIG', realpath(APPLICATION_PATH . '/../config'));

class SystemConfig
{
    public static function  init()
    {
//        $_ENV['APP_NAME']=(pathinfo(realpath(APPLICATION_PATH),  PATHINFO_BASENAME ));
//        $configurePath = sprintf('%s/../config/config.ini', APPLICATION_PATH);
//        $config = new Yaf_Config_Ini($configurePath, 'production');
//        Yaf_Registry::set('config', $config);

        self::initConfig();
        SystemConfig::loadEssentials();
    }

    public static function get($key)
    {
        if(empty($key))
            return null;

        if(HaloEnv::isRegistered($key))
            return HaloEnv::get($key);

        $obj = null;
        $methodName = self::getMethodName($key);
        if(method_exists('SystemConfig', $methodName))
        {
            $config = HaloEnv::get('config');
            $obj = call_user_func_array(array('SystemConfig', $methodName), array($key, $config));
            HaloEnv::set($key, $obj);
        }

        return $obj;
    }

    public static function loadEssentials()
    {
        Yaf_Loader::import(sprintf('%s/yaf/LocalAutoLoader.php', LIB_PATH));
        Yaf_Loader::import(sprintf('%s/halo/HaloMethod.php',LIB_PATH));
        Yaf_Loader::import(sprintf('%s/halo/Logger.php',LIB_PATH));
        Yaf_Loader::import(sprintf('%s/yaf/YafController.php', LIB_PATH));
        Yaf_Loader::import(sprintf('%s/yaf/YafDebug.php', LIB_PATH));
        Yaf_Loader::import(sprintf('%s/yaf/YafView.php', LIB_PATH));

        Yaf_Loader::import(sprintf('%s/WrmPlatform/WrmPlatformClient.php', LIB_PATH));
        Yaf_Loader::import(sprintf('%s/WrmPlatform/HaloClient.php', LIB_PATH));
        Yaf_Loader::import(sprintf('%s/WrmPlatform/WrmStatisticLog.php', LIB_PATH));
        Yaf_Loader::import(sprintf('%s/hollo/HolloEnv.php', LIB_PATH));
        Yaf_Loader::import(sprintf('%s/wechat/MemCacheBase.php', LIB_PATH));
        Yaf_Loader::import(sprintf('%s/upYun/upyun.class.php', LIB_PATH));

        //custom
        Yaf_Loader::import(sprintf('%s/wechat/DataCenter.php', LIB_PATH));

        LocalAutoLoader::register();

        $config = Yaf_Registry::get('config');

        YafDebug::$LOG_LEVEL = $config->log->level;
        YafDebug::$LOG_BASE_DIR = self::getLogDir();
        HaloEnv::instance($config);

    }

    public static  function initConfig()
    {
        $typePath = sprintf('%s/../config/%s', APPLICATION_PATH,'server_type.ini');
        $configName = 'config.ini';
        if(file_exists($typePath))
        {
            $typeConfig = new Yaf_Config_Ini($typePath, 'production');
            if($typeConfig->type == 1)
            {
                $configName = 'config_dev.ini';
            }
            elseif($typeConfig->type == 2)
            {
                $configName = 'config_loc.ini';
            }
        }

        $configurePath = sprintf('%s/../config/%s', APPLICATION_PATH,$configName);
        $config = new Yaf_Config_Ini($configurePath, 'production');
        Yaf_Registry::set('config', $config);
    }

    public static function getLogDir()
    {
        $config = Yaf_Registry::get('config');
        $basedir = '../logs';
        if($config->log->basedir)
            $basedir = $config->log->basedir;

        return sprintf('%s/%s/%s', APPLICATION_PATH, $basedir,  $_ENV['APP_NAME']);
    }
    //----------------------------------------------------------------------------
    //For test
    public static function printDebug($msg, $debug=false)
    {
        if($debug)
            echo "<div>$msg<div/>\n";
    }

    public static function checkConnection($debug=false)
    {
        $time = microtime();

        $dbs = array('web','task','company');
        foreach($dbs as $name)
        {
            $db = DataCenter::getDb($name);
            SystemConfig::printDebug('正在连接数据库：'.$name, true);
            $db->query("SET NAMES 'utf8'");
        }

        $startTime = microtime();
        SystemConfig::printDebug('正在连接memchace....', $debug);
        DataCenter::getMc();
        SystemConfig::printDebug(sprintf('连接耗时：%.3fms', microtime()-$startTime), $debug);

        $startTime = microtime();
        SystemConfig::printDebug(sprintf('连接耗时：%.3fms', microtime()-$startTime), $debug);
//
    }
}

SystemConfig::init();

