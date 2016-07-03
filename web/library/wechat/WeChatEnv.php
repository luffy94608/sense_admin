<?php

const SYS_MODEL_POST = 0;
const SYS_MODEL_OPPORTUNITY = 0;

const LOGIN_STATUS_NONE = 0;
const LOGIN_STATUS_WEIBO = 1;
const LOGIN_STATUS_WCONTACT = 2;



class WeChatEnv
{
    static $memCache = null;
    static $redis = null;

    public static function setWechatEnv($corpKey,$moudleName)
    {
        YafDebug::log('setWechatEnv :: cropKey :'.$corpKey.' moudleName :'.$moudleName);
        $wechatModel  = new WechatModelBase();
        $cropInfo =  $wechatModel->getCorpInfoByKey($corpKey);
        $moudleId = $wechatModel->getAgentIdByMoudle($cropInfo['Fid'],$moudleName);
        self::setCorpAes($corpKey);
        self::setCorpDbId($cropInfo['Fid']);
        self::setCorpId($cropInfo['Fcorp_id']);
        self::setCorpSecret($cropInfo['Fcorp_secret']);
        self::setAgentId($moudleId);
        self::setWeiboId($cropInfo['Fweibo_id']);
        self::setMoudleName($moudleName);
        self::setDepartmentId($cropInfo['Fdepartment_id']);
    }

    public static function getCorpAes()
    {

        return Yaf_Registry::get('corpAes');
    }

    public static function setCorpAes($value)
    {
        return Yaf_Registry::set('corpAes',$value);
    }

    public static function getCorpDbId()
    {
        return Yaf_Registry::get('corpDbId');
    }

    public static function setCorpDbId($value)
    {
        return Yaf_Registry::set('corpDbId',$value);
    }

    public static function getCorpId()
    {
        return Yaf_Registry::get('corpId');

    }
    public static function setCorpId($value)
    {
        return Yaf_Registry::set('corpId',$value);
    }

    public static function getCorpSecret()
    {
        return Yaf_Registry::get('corpSec');
    }

    public static function setCorpSecret($value)
    {
        return Yaf_Registry::set('corpSec',$value);
    }

    public static function getWeiboId()
    {
        return Yaf_Registry::get('weiboId');
    }

    public static function setWeiboId($value)
    {
        return Yaf_Registry::set('weiboId',$value);
    }

    public static function getMoudleName()
    {
        return Yaf_Registry::get('moudleName');
    }

    public static function setMoudleName($value)
    {
        return Yaf_Registry::set('moudleName',$value);
    }

    public static function setDepartmentId($value)
    {
        return Yaf_Registry::set('departmentId',$value);
    }

    public static function getDepartmentId()
    {
        return Yaf_Registry::get('departmentId');
    }

    public static function setPassportName($value)
    {
        return Yaf_Registry::set('passportName',$value);
    }

    public static function getPassportName()
    {
        return Yaf_Registry::get('passportName');
    }


    public static function setPassportPwd($value)
    {
        return Yaf_Registry::set('passportPwd',$value);
    }

    public static function getPassportPwd()
    {
        return Yaf_Registry::get('passportPwd');
    }

    public static function getCommonConfig()
    {
        $commonConfig = Yaf_Registry::get('config');
        if(!isset($commonConfig))
        {
            $commonConfPath= sprintf('%s/config.ini', CONFIG);

            $commonConfig = new Yaf_Config_Ini($commonConfPath, 'production');
            Yaf_Registry::set('config', $commonConfig);
        }
        return $commonConfig;
    }

    public static function getCustomConfig()
    {
        $customConfig = Yaf_Registry::get('custom_config');
        if($customConfig == null)
        {
            $configurePath = sprintf('%s/../../../config/customConfig/%s.ini', APPLICATION_PATH,self::getCorpAes());
            if(file_exists($configurePath))
            {
                $config = new Yaf_Config_Ini($configurePath, 'production');
                Yaf_Registry::set('custom_config', $config);
                $customConfig = $config;
            }
        }
        return $customConfig;
    }

    public static function getAgentId()
    {
        return Yaf_Registry::get('agentId');
    }
    public static function setAgentId($value)
    {
        return Yaf_Registry::set('agentId',$value);
    }

    public static function getHostUrl($dir = null)
    {
        $config = self::getCommonConfig();
        $moduleName = Yaf_Registry::get('moudleName');
        YafDebug::log('getHostUrl =====config url host is '.$config->url->hostName);
        $dir2 = '';
        if(!empty($dir))
        {
            $dir2 = '/'.$dir;
        }
        $urlPrefix = "http://".self::getCorpAes().'.'.$moduleName.".".$config->url->hostName.$dir2;
        return $urlPrefix;
    }

    public static function isWeiboLogined()
    {
        $isLogin = false;

        if(isset($_COOKIE['SUW']) || isset($_COOKIE['SUE']))
        {
            $data = array();
            if(isset($_COOKIE['SUW']))
            {
                parse_str(urldecode($_COOKIE['SUW']),$data);
            }
            else
            {
                parse_str(urldecode($_COOKIE['SUP']),$data);
            }
            //判断cookies是否过期
            if( isset($data['et']) && time() > $data['et'])
            {
                return false;
            }

            if(isset($_COOKIE['WRMU']))
            {

                $uid = WeChatEnv::getDecryptStr($_COOKIE['WRMU']);
                if($uid == $data['uid'] )
                {
                    $isLogin = true;
                    if(count($data) == 1)
                    {
                        $type = isset($_COOKIE['SUW']) ? WContact_Session_Handler::SESSION_TYPE_MOBILE : WContact_Session_Handler::SESSION_TYPE_WEB ;
                        WeChatEnv::setCookies($uid,$type);
                    }
                    return $isLogin;
                }
                else
                {
                    unset($_COOKIE['WRMU']);
                    setcookie('WRMU','');
                }
            }

            $config = HaloEnv::get('config');
            $akey = $config['weibo']['akey'];
            $auth_client = new SaeTClientV2($akey, null, $_COOKIE, null, '' );
            $isLogin = $auth_client->isCookiesAvailable();
            if($isLogin)
            {

                $wrmUser = WeChatEnv::getEncryptStr($data['uid']);
                setcookie('WRMU',$wrmUser,$data['et']);
                $_COOKIE['WRMU'] = $wrmUser;
            }
            else
            {
                unset($_COOKIE['SUW']);
                unset($_COOKIE['SUE']);
            }
        }

        return $isLogin;
    }


    public static function getEncryptStr($str)
    {
        $config = HaloEnv::get('config');
        $key = $config->aes->key;
        return bin2hex(aesEncrypt(base64_encode($str),$key));
    }

    public static function getDecryptStr($str)
    {
        $config = HaloEnv::get('config');
        $key = $config->aes->key;
        return  base64_decode(aesDecrypt(hex2bin($str),$key));
    }
    public static function setCookies($uid,$type)
    {
        $model = new AccountModel();
        $cookies = $model->getUserCookies($uid,$type);
        $config = HaloEnv::get('config');
        $host = $config['host']['web'];
        $data = null;
        if(isset($cookies['SUP']))
        {
            parse_str(urldecode($cookies['SUP']),$data);
        }
        else
        {
            parse_str(urldecode($cookies['SUW']),$data);
        }
        $et = $data['et'];
        foreach($cookies as $key=>$value)
        {
            setcookie($key,$value,$et,'/',$host);
            $_COOKIE[$key] = $value;
        }
        $wrm = WeChatEnv::getEncryptStr($uid);
        setcookie("WRMU",$wrm,$et,'/',$host);
    }

    public static function getMemCache()
    {
        if (self::$memCache == null)
        {
            self::$memCache = new MemCacheBase();
        }
        return self::$memCache;
    }

    public static function getRedis()
    {
        if (self::$redis == null)
        {
            $config = Yaf_Registry::get('config');
            Logger::DEBUG('Redis :'.$config['redis']['host'].$config['redis']['port'].$config['redis']['password']);
            self::$redis = new HaloRedis($config['redis']['host'],$config['redis']['port'],$config['redis']['password']);
        }
        return self::$redis;
    }

    public static function checkCookies()
    {
        $config = HaloEnv::get('config');
        $isWechat = $config['app']['wehcat'];
        if($isWechat)
        {

        }
        else
        {
            self::isWeiboLogined();
        }
    }

    public static function syncSession()
    {
        $config = HaloEnv::get('config');
        $isWechat = $config['app']['wehcat'];
        if($isWechat)
        {

        }
        else
        {
            $session = Yaf_Session::getInstance();
            $suw = array();
            $uid = $session->offsetGet('uid');
            $et = $session->offsetGet('et');

            if(!empty($_COOKIE['SUW']))
            {
                parse_str(urldecode($_COOKIE['SUW']),$suw);

                $session->offsetSet('et',$suw['et']);
                $session->offsetSet('uid', $suw['uid']);
                $session->offsetSet('account', urldecode($_COOKIE['USER_LAST_LOGIN_NAME']));
                $session->offsetSet('gsid', $_COOKIE['gsid_CTandWM']);
                $session->offsetSet('SUW', $_COOKIE['SUW']);
            }
            else
            {
                $session->offsetSet('uid', '');
                $session->offsetSet('account', '');
                $session->offsetSet('gsid', '');
                $session->offsetSet('SUW', '');
                $session->offsetSet('et', 0);
            }
            $newEt = $session->offsetGet('et');
            if($uid && $newEt != $et)
            {
                $client = HaloClient::singleton();
                $client->deleteUserStatus($uid);
            }
        }
    }

    public static function initWechatEnv()
    {
        $commonConfig = Yaf_Registry::get('config');
        if(!isset($commonConfig))
        {
            $commonConfPath= sprintf('%s/config.ini', CONFIG);
            $commonConfig = new Yaf_Config_Ini($commonConfPath, 'production');
            Yaf_Registry::set('config', $commonConfig);
        }

        $host = $_SERVER['SERVER_NAME'];
        $hostArray = explode('.',$host);
        $config = HaloEnv::get('config');
        if(count($hostArray) > 2)
        {
            WeChatEnv::setWechatEnv($hostArray[0],$hostArray[1]);
        }
        else
        {
            die('host error');
        }
    }

}
