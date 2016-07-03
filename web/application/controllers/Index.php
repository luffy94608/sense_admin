<?php
/**
 * Created by JetBrains PhpStorm.
 * User: crazyant
 * Date: 13-11-12
 * Time: AM9:56
 * To change this template use File | Settings | File Templates.
 */


class IndexController extends BaseController
{

    protected $model = null;
    public function init()
    {
        parent::init();
    }

    public function loginAction()
    {
        $this->view->disableLayout();
        $this->display('login3');
        return false;
    }

    public function indexAction()
    {
//        $this->view->page = 'index-page';
//        $positionModel = new PositionModel();
//        $result=$positionModel->getDayStaticData();
//        $this->view->summary = $result;
    }

    /**
     * 登陆
     */
    public function checkPasswordAction()
    {
        $model=new AccountModel();
        $params['account']=$this->getLegalParam('account','str');
        $params['password']=$this->getLegalParam('password','str');
        $paramsData=file_get_contents('php://input');
        $jsonData=array();
        if($paramsData)
        {
            $jsonData=json_decode($paramsData,true);

        }
        if($jsonData['account'])
        {
            $params['account']=$jsonData['account'];
        }
        if($jsonData['password'])
        {
            $params['password']=$jsonData['password'];
        }

        YafDebug::log('checkPasswordAction $params'.json_encode($params));
        YafDebug::log('checkPasswordAction $jsonData'.json_encode($jsonData));
        if(in_array(false,$params))
        {
            $this->inputParamErrorResult();
        }

        $result=$model->checkPassword($params['account'],$params['password']);
        if($result)
        {
            $session = Yaf_Session::getInstance();
            setcookie('uid',$result['id'],time()+60*60*24*30*12,'/');
            setcookie('cid',$result['cid'],time()+60*60*24*30*12,'/');
            setcookie('user_info',json_encode($result),time()+60*60*24*30*12,'/');
            $session->offsetSet('uid',$result['id']);
            $session->offsetSet('cid',$result['cid']);
            $session->offsetSet('user_info',$result);
            $this->inputResult('登陆成功');
        }
        $this->inputErrorWithDesc('账号或密码错误');
    }
    /**
     * 修改个人密码
     */
    public function resetPasswordAjaxAction()
    {
        $model=new AccountModel();
        $params['uid']=$this->uid;
        $params['oldPass']=$this->getLegalParam('oldPass','str');
        $params['newPass']=$this->getLegalParam('newPass','str');
        if(in_array(false,$params))
        {
            $this->inputParamErrorResult();
        }
        if($params['uid']==-9999)//root user
        {
            $this->inputErrorWithDesc('oh no, You are root!');
        }

        $result=$model->resetPassword($params['uid'],$params['oldPass'],$params['newPass']);
        if($result>0)
        {
            $this->inputResult();
        }
        $desc='操作失败';
        if($result=-1)
        {
            $desc='原密码错误';
        }
        $this->inputErrorWithDesc($desc);
    }

    /**
     * 退出
     */
    public function logoutAction()
    {
        $session = Yaf_Session::getInstance();
        $session->offsetUnset('uid');
        $session->offsetUnset('cid');
        $session->offsetUnset('user_info');
        setcookie('uid',null,time()-1,'/');
        setcookie('cid',null,time()-1,'/');
        setcookie('user_info',null,time()-1,'/');

        $this->jumpDirect('/index/login');
    }



}