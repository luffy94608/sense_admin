<?php

class DataCenter
{
    private static $connections = array('db'=>array(),'redis'=>array(),'mongo'=>array(), 'mc'=>array());

    /**
     * @param $name
     * @return HaloDataSource
     * @throws Exception
     */
    public static function getDb($name)
    {
        if (isset(static::$connections['db'][$name]))
        {
            return static::$connections['db'][$name];
        }
        $config = Yaf_Registry::get('config');

        $dbConfig = $config->db->{$name};
//        YafDebug::log('get db config is  '.json_encode((Array)$dbConfig)."\n name is".$name);

        if (empty($dbConfig))
        {
//            $config = WeChatEnv::getCommonConfig();
//            $dbConfig = $config->db->{$name};
//            if(empty($dbConfig))
//            {
//                throw new Exception(sprintf('config of db %s is not found', $name), -9999);
//            }
        }

        $db = new HaloDataSource(array('host'=>$dbConfig->host, 'port'=>$dbConfig->port, 'user'=>$dbConfig->user, 'pass'=>$dbConfig->pass, 'dbname'=>$dbConfig->name),$name);

//        $db = new HaloDb(array('host'=>$dbConfig->host, 'user'=>$dbConfig->user, 'pass'=>$dbConfig->pass, 'dbname'=>$dbConfig->name));

        return static::$connections['db'][$name] = $db;
    }

    /**
     * @param string $name
     * @return Redis
     * @throws Exception
     */
    public static function getRedis($name='wechat')
    {
        if (isset(static::$connections['redis'][$name]))
        {
            return static::$connections['redis'][$name];
        }

        $config = Yaf_Registry::get('config');
        $redis = new Redis();
        $redisConfig = $config->redis->{$name};

        if (empty($redisConfig))
        {
            throw new Exception(sprintf('config of redis %s is not found', $name), -9998);
        }

        $redis->pconnect($redisConfig->host,$redisConfig->port);
        return static::$connections['redis'][$name] = $redis;
    }


    /**
     * @param string $name
     * @return MemCacheBase
     * @throws Exception
     */
    public static function getMc($name='wechat')
    {
        if (isset(static::$connections['mc'][$name]))
        {
            return static::$connections['mc'][$name];
        }

        $config = Yaf_Registry::get('config');
        $mcConfig = $config->memcache;
        if (empty($mcConfig))
        {
            throw new Exception(sprintf('config of memcache %s is not found', $name), -9999);
        }
        $serverCount = intval($mcConfig->$name->count);
        $mc = new Memcache();
        for($i = 1; $i<= $serverCount; $i++)
        {
            $hostKey = 'host_'.$i;
            $portKey = 'port_'.$i;
            $mc->addServer($mcConfig->$name->$hostKey, $mcConfig->$name->$portKey);
        }

        return static::$connections['mc'][$name] = $mc;
    }


    public static  function getMongo($name='hollo')
    {
        if (isset(static::$connections['mongo'][$name]))
        {
            return static::$connections['mongo'][$name];
        }
//        YafDebug::log(static::$connections['mongo']);
        $config = Yaf_Registry::get('config');

        $dbConfig = $config->mdb->{$name};
//        YafDebug::log('get mongo config is  '.json_encode((Array)$dbConfig)."\n name is".$name);

        if (empty($dbConfig))
        {
            $config = WeChatEnv::getCommonConfig();
            $dbConfig = $config->mdb->{$name};
            if(empty($dbConfig))
            {
                throw new Exception(sprintf('config of mongo %s is not found', $name), -9999);
            }
        }
        $username='';
        if(!empty($dbConfig->user) || !empty($dbConfig->pass))
        {
            $username="{$dbConfig->user}:{$dbConfig->pass}@";
        }
        $connect=array(
            'dsn'	=>	"mongodb://{$username}{$dbConfig->host}:{$dbConfig->port}/{$dbConfig->name}",
            'query_safety'	=>	null,
            'replica_set' => isset($dbConfig->replicaset) ? $dbConfig->replicaset : false
        );
        $db = new HaloMongo($connect);

//        $db = new HaloDb(array('host'=>$dbConfig->host, 'user'=>$dbConfig->user, 'pass'=>$dbConfig->pass, 'dbname'=>$dbConfig->name));
//        YafDebug::log($connect);
//        YafDebug::log($db);
        return static::$connections['mongo'][$name] = $db;
    }
}


