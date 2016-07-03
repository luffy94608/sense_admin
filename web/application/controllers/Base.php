<?php
/**
 * Created by JetBrains PhpStorm.
 * User: crazyant
 * Date: 13-11-12
 * Time: AM9:56
 * To change this template use File | Settings | File Templates.
 */

class BaseController extends YafController
{
    protected $uid;
    protected $cid;//公司id
    protected $logger = null;
    /* @var YafView */
    protected $view;
    protected $isAngular = false;

    public function init()
    {

        $session = Yaf_Session::getInstance();
        $this->uid = $session->offsetGet('uid');
        if(empty($this->uid))
        {
            $this->uid=false;
            if($_COOKIE['uid'])
            {
                $this->uid=$_COOKIE['uid'];
            }
        }
        $this->cid = $session->offsetGet('cid');
        if(empty($this->cid))
        {
            $this->cid=false;
            if($_COOKIE['cid'])
            {
                $this->cid=$_COOKIE['cid'];
            }
        }
        $clazz = get_class($this);
        $this->logger = Logger::LOG($clazz);
        $this->_view->setRequest($this->getRequest());
        $this->view = $this->_view;
        $this->view->bodyId =$this->_request->controller . "_" . $this->_request->action;
        $this->view->uid=$this->uid;
        $this->view->cid=$this->cid;
        $this->view->title='管理后台';
        $this->view->page  = "base";
        $this->doInit();
    }

    public function doInit()
    {

    }

    public function jumpDirect($url='/')
    {
        header('Location: '.$url);
        haloDie();
    }

    public  function notFound ()
    {
        $this->redirect("/index/login");
        return;
    }

    /**
     * 加密
     * @param string $str   需加密的字符串
     * @return type
     */
    public function encode( $str )
    {
        $config = HaloEnv::getConfig();
        $key = $config['backstage']['aes']['key'];
        $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_ECB);
        $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
        return base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $key, $str, MCRYPT_MODE_ECB, $iv));

    }
    /**
     * 解密
     * @param type $str
     * @return type
     */
    public function decode($str)
    {
        $config = HaloEnv::getConfig();
        $key = $config['backstage']['aes']['key'];
        $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_ECB);
        $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
        return preg_replace("/[\\x00-\\x08\\x0b-\\x0c\\x0e-\\x1f]/",'',mcrypt_decrypt(MCRYPT_RIJNDAEL_128,$key,base64_decode($str),MCRYPT_MODE_ECB,$iv));
    }



}