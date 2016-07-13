<?php
class SideMenuBuilder
{

    /**
     * menu
     * @return array
     */
    public static function buildSideMenuArray()
	{
        $result= array(
            array(
                'module'=>'statistics',
                'title'=>'数据统计',
                'icon'=>'icon-bar-chart',
                'children'=>array(
                    array('href'=>'/statistics/index','title'=>'访问统计'),
                )),
            array(
                'module'=>'manage',
                'title'=>'综合管理',
                'icon'=>'  icon-grid',
                'children'=>array(
                    array('href'=>'/manage/menu','title'=>'导航栏'),
                    array('href'=>'/manage/map','title'=>'网站地图'),
                    array('href'=>'/manage/solution','title'=>'解决方案'),
                    array('href'=>'/manage/route','title'=>'成长历程'),
                    array('href'=>'/manage/cert','title'=>'资质和产权'),
                    array('href'=>'/manage/solution','title'=>'解决方案'),
                    array('href'=>'/manage/recruit','title'=>'招聘配置'),
                    array('href'=>'/manage/news','title'=>'新闻配置'),
                )),
            array(
                'module'=>'lock',
                'title'=>'加密锁管理',
                'icon'=>'  icon-lock',
                'children'=>array(
                    array('href'=>'/lock/type','title'=>'产品类别'),
                    array('href'=>'/lock/download','title'=>'资源下载'),
                    array('href'=>'/lock/list','title'=>'产品管理'),
                )),
            array(
                'module'=>'home',
                'title'=>'首页管理',
                'icon'=>'icon-home',
                'children'=>array(
                    array('href'=>'/home/banner','title'=>'轮播图配置'),
                    array('href'=>'/home/list','title'=>'首页列表'),
                    array('href'=>'/home/partner','title'=>'合作伙伴'),
                )),
            array(
                'module'=>'page',
                'title'=>'单页管理',
                'icon'=>'  icon-doc',
                'children'=>array(
                    array('href'=>'/order/list','title'=>'单页列表'),
                )),
            array(
                'module'=>'account',
                'title'=>'权限管理',
                'icon'=>'icon-user',
                'children'=>array(
                    array('href'=>'/account/user','title'=>'用户管理'),
                    array('href'=>'/account/role','title'=>'角色管理'),
                    array('href'=>'/account/module','title'=>'模块管理'),
                )),
            array(
                'module'=>'company',
                'title'=>'企业管理',
                'icon'=>'icon-users',
                'children'=>array(
                    array('href'=>'/company/index','title'=>'企业管理'),
                    array('href'=>'/company/user','title'=>'管理员设置'),
                )),
            );


        return $result;
	}

    public static function buildAccessSideMenuArr(&$arr)
    {
        if(empty($arr))
        {
            return false;
        }
        $uid = HolloEnv::getUid();
        if($uid==-9999)//root user
        {
            return false;
        }

        $accountModel=new AccountModel();
        $privileges=$accountModel->getUsePrivileges($uid);
        //判断用户的可见范围
        foreach($arr as $tk=>&$tv)
        {
            $module=strtolower(str_replace('-','',$tv['module']));
            if(array_key_exists($module,$privileges))
            {
                $subPrivileges=$privileges[$module]['children'];
                if(!empty($tv['children']) && is_array($tv['children']))
                {
                    $tmpSubPrivileges=array();
                    foreach($tv['children'] as $kv)
                    {
                        $subModule=strtolower(str_replace('-','',$kv['href']));
                        $subModule=explode('/',$subModule);
                        $subModule=array_pop($subModule);
                        if(array_key_exists($subModule,$subPrivileges)){
                            $tmpSubPrivileges[]=$kv;
                        }
                    }
                    $tv['children']=$tmpSubPrivileges;
                }
            }
            else
            {
                unset($arr[$tk]);
            }

        }
    }


}
