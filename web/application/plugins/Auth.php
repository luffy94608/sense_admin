<?php

class AuthPlugin extends Yaf_Plugin_Abstract
{
	public function dispatchLoopStartup(Yaf_Request_Abstract $request, Yaf_Response_Abstract $response)
    {
        $url =$request->getRequestUri();
        $controller = strtolower($request->getControllerName ());
        $action=strtolower($request->getActionName());

            $exceptMap = array(
            'error'=>array(),
            'download'=>array(),
            'upload'=>array(),
            'index'=>array(
                'check-password' =>1,
                'login' =>1,
            ),
            'path'=>array(
                'upLoadImageFile' => -1,
                'justUpLoadImage' => -1,
                'upLoadCarImage' => -1,
            ),
            'statistics'=>array(
                'down-excel'=>-1
            )
        );
        $exceptMap=$this->convertMapToCommonData($exceptMap);

        if(!isset($exceptMap[$controller]))
        {
            $this->checkNeedLogin($request);
        }
        else
        {
            if(!isset($exceptMap[$controller]))
            {
                $this->checkNeedLogin($request);
            }
            else
            {
                if(count($exceptMap[$controller])>0)
                {
                    if(!isset($exceptMap[$controller][$action]))
                    {
                        $this->checkNeedLogin($request);
                    }
                }
            }
        }
    }

    public function checkNeedLogin(Yaf_Request_Abstract $request)
    {
        $controller = strtolower($request->getControllerName ());
        $action=strtolower($request->getActionName());
        $session = Yaf_Session::getInstance();
        $uid = $session->offsetGet('uid');
        if(empty($uid))
        {
            if($_COOKIE['uid'])
            {
                $uid=$_COOKIE['uid'];
            }
        }

        $request_type = $_POST['request_type'];
        $isLegalRequest=$this->getLegalRequestStatus();
        if(empty($uid))
        {
            if($isLegalRequest)
            {
                echo json_encode(array('data'=>'未登录','code'=>-302));;
                die();
            }
            else
            {
                $this->headerLocation('/index/login');
            }
        }
        if($uid==-9999){//root
            return false;
        }
        $accountModel=new AccountModel();
        $privileges=$accountModel->getUsePrivileges($uid);
        if(!(array_key_exists($controller,$privileges) && array_key_exists($action,$privileges[$controller]['children'])))
        {
            YafDebug::log('$uid  ::'.$uid);
            YafDebug::log('$controller  ::'.$controller);
            YafDebug::log('$action  ::'.$action);
            if(!empty($uid) && ($controller=='index' && $action='index') || $isLegalRequest)
            {

            }
            else
            {
                $this->headerLocation('/error/not-found');
            }
        }
    }

    /**
     * 获取是否是ajax请求 域名是否合法
     * @return bool
     */
    public function getLegalRequestStatus()
    {
        $request_type = $_REQUEST['request_type'];
        $isLegalRequest=false;

        if($request_type=='ajax')
        {
            $refer = $_SERVER['HTTP_REFERER'];
            //TODO 关闭域名判断逻辑
            return true;
            if(!empty($refer))
            {
                $legalHost = array(
                    '111.13.89.48',
                    'hgtop.hollo.cn',
                    'hgtop-dev.hollo.cn',
                    'local.hollo.operation.com'
                );
                $url = parse_url($refer);
                foreach ($legalHost as $v)
                {
                    $pos = stripos($url['host'], $v);
                    if ($pos !== false) {
                        $isLegalRequest = true;
                        break;
                    }
                }
            }
        }

        return $isLegalRequest;
    }

    public function  convertMapToCommonData($exceptMap)
    {
        $result=array();
        foreach($exceptMap as $k=>$v)
        {
            $item=array();
            if(!empty($v) && is_array($v))
            {
                foreach($v as $k2=>$v2)
                {
                    $nk2=strtolower(str_replace('-','',$k2));
                    $item[$nk2]=$v2;
                }
            }
            $nk=strtolower(str_replace('-','',$k));
            $result[$nk]=$item;
        }
        return $result;
    }


    public function headerLocation($url)
    {
        header('Location: '.$url);
        haloDie();
    }
}
