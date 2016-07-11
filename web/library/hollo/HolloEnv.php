<?php
/**
 * Created by PhpStorm.
 * User: luffy
 * Date: 15/9/6
 * Time: 15:17
 */

class HolloEnv
{
    /**
     * 判断是否对model具有操作权限 区分root或者超级管理员用户用
     * @param $cid
     * @return bool
     */
    public static function getAccessModelHandle($cid)
    {
        $cidMap=array(
            -9999,
        );
        $companyModel=new CompanyModel();
        $result=$companyModel->getSuperAdmin();
        if($result)
        {
            foreach($result as $v)
            {
                $cidMap[]=$v['id'];
            }
        }
        if(in_array($cid,$cidMap))
        {
            return true;
        }
        else
        {
            return false;
        }

    }

    /**
     * 获取用户uid
     * @return string
     */
    public static function getUid()
    {
        $session = Yaf_Session::getInstance();
        $uid = $session->offsetGet('uid');
        if(empty($uid))
        {
            if($_COOKIE['uid'])
            {
                $uid=$_COOKIE['uid'];
            }
        }
        return $uid;
    }

    /**
     * 获取用户cid
     * @return string
     */
    public static function getCid()
    {
        $session = Yaf_Session::getInstance();
        $cid = $session->offsetGet('cid');
        if(empty($uid))
        {
            if($_COOKIE['cid'])
            {
                $cid=$_COOKIE['cid'];
            }
        }
        return $cid;
    }

    /**
     * 获取用户info
     * @return string
     */
    public static function getUserInfo()
    {
        $session = Yaf_Session::getInstance();
        $user_info = $session->offsetGet('user_info');
        if(empty($uid))
        {
            if($_COOKIE['user_info'])
            {
                $user_info=json_decode($_COOKIE['user_info'],true);
            }
        }
        return $user_info;
    }

    /**
     * 获取静态资源host
     */
    public static function getImgHost()
    {
        $config = Yaf_Registry::get('config');
        $host = $config->img->host;
        return $host;
    }
} 