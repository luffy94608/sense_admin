<?php

class YafController extends Yaf_Controller_Abstract{
    protected $uid;

    /**
     * @var Logger
     */
    protected $logger = null;


    /* @var $client HaloClient */
    protected $client = null;

    public  function init()
    {
        $session = Yaf_Session::getInstance();
        $this->uid = $session->offsetGet('uid');
        $clazz = get_class($this);
        $this->logger = Logger::LOG($clazz);

        $this->_view->setRequest($this->getRequest());

        $this->client = HaloClient::singleton();
    }

    protected function getLegalParam($tag,$legalType,$legalList=array(),$default=null)
    {
        $param = $this->getRequest()->get($tag,$default);
        if($param!==null)
        {
            switch($legalType)
            {
                case 'eid': //encrypted id
                {
                    if($param)
                        return aesDecrypt(hex2bin($param), WAYGER_AES_KEY);
                    else
                        return null;
                    break;
                }
                case 'id':
                {
                    if (preg_match ('/^\d{1,20}$/', strval($param) ))
                    {
                        return strval($param);
                    }
                    break;
                }
                case 'time':
                {
                    return intval($param);
                    break;
                }
                case 'int':
                {
                    $val = intval($param);

                    if(count($legalList)==2)
                    {
                        if($val>=$legalList[0] && $val<=$legalList[1])
                            return $val;
                    }
                    else
                        return $val;
                    break;
                }
                case 'str':
                {
                    $val = strval($param);
                    if(count($legalList)==2)
                    {
                        if(strlen($val)>=$legalList[0] && strlen($val)<=$legalList[1])
                            return $val;
                    }
                    else
                        return $val;
                    break;
                }
                case 'trim_spec_str':
                {
                    $val = trim(strval($param));
                    if(!preg_match("/['.,:;*?~`!@#$%^&+=)(<>{}]|\]|\[|\/|\\\|\"|\|/",$val))
                    {
                        if(count($legalList)==2)
                        {
                            if(strlen($val)>=$legalList[0] && strlen($val)<=$legalList[1])
                                return $val;
                        }
                        else
                            return $val;
                    }
                    break;
                }
                case 'enum':
                {
                    if(in_array($param,$legalList))
                    {
                        return $param;
                    }
                    break;
                }
                case 'array':
                {
                    if(count($legalList)>0)
                        return explode($legalList[0],strval($param));
                    else
                    {
                        if (empty($param))
                            return array();
                        return explode(',',strval($param));
                    }

                    break;
                }
                case 'json':
                {
                    return json_decode(strval($param),true);
                    break;
                }
                case 'raw':
                {
                    return $param;
                    break;
                }
                default:
                    break;
            }
        }
        return false;
    }
    protected function getPageParams()
    {
        $param['offset'] = $this->getLegalParam('offset', 'int', array(), 0);
        $param['length'] = $this->getLegalParam('length', 'int', array(), 20);

        return $param;
    }
    protected function getSharpParam()
    {
        $url = $_SERVER['REQUEST_URI'];
        $idx = stripos($url, "#");
        if($idx === false)
            return array();
        $param = array();
        $paramstr = substr($url, $idx);
        return $paramstr;

    }
    protected function checkReferer()
    {
        $refer = $_SERVER['HTTP_REFERER'];
        if(empty($refer))
            $this->inputRefererErrorResult();
        else
        {
            $legalHost = array('operation.hollo.cn','opdev.hollo.cn','local.hollo.operation.com');
            $url = parse_url($refer);
            $result = false;
            foreach($legalHost as $v)
            {
                $pos = stripos($url['host'],$v);
                if($pos!==false)
                {
                    $result = true;
                    break;
                }
            }
            if($result===false)
                $this->inputRefererErrorResult();
            else
            {
//                if($_REQUEST['request_type']!='ajax')
//                    $this->inputRefererErrorResult();
            }
        }
    }

    protected function getLegalParamArray($fields)
    {
        $params = array();
        foreach($fields as $f => $type)
        {
            $params[$f] = $this->getLegalParam($f, $type);
        }
        return $params;
    }

    protected function getRequestDate($year='year', $month='month', $day='day')
    {
        $y = $this->getLegalParam($year, 'int');
        $m = $this->getLegalParam($month, 'int');
        $d = $this->getLegalParam($day, 'int');
        return mktime(0, 0, 0, $m, $d, $y);
    }

    protected function inputIdResult($result,$model)
    {
        if($result<0)
            $this->inputErrorResult($result, $model);
        else
            $this->inputResult(array('id'=>$result));
    }

    protected function inputStateResult($result,$model)
    {
        if($result<0)
            $this->inputErrorResult($result, $model);
        else
            $this->inputResult(array('state'=>$result));
    }

    protected function inputNullResult($result,$model)
    {
        if($result<0)
            $this->inputErrorResult($result, $model);
        else
            $this->inputResult();
    }

    protected function inputUpgradeResult($result, $model)
    {
        $desc = $model->getErrorText($result['code']);
        echo json_encode(array('data'=>$result,'code'=>$result['code'], 'desc'=>$desc));
        die();
    }

    protected function inputResult($data=null)
    {

        echo json_encode(array('data'=>$data,'code'=>0));
        haloDie();
    }

    protected function inputBase64Result($data=null)
    {
        $data['base64'] = true;
        if (isset($data['html']))
        {
            $data['html'] = base64_encode($data['html']);
        }

        echo json_encode(array('data'=>$data,'code'=>0));
        haloDie();
    }

    protected function inputErrorResult($code,$model)
    {
//        $desc = ErrorCode::errorMsgByCode($code);
        $desc = $model->getErrorText($code);
        echo json_encode(array('code'=>$code,'desc'=>$desc));
        haloDie();
    }

    protected function inputParamErrorResult()
    {
        echo json_encode(array('code'=>-100,'desc'=>'参数错误'));
        haloDie();
    }

    protected function inputRefererErrorResult()
    {
        echo json_encode(array('code'=>-101,'desc'=>'referer error'));
        haloDie();
    }

    protected function inputNotLoginResult($subType = null,$url = '')
    {
        if($subType)
        {
            if($subType == 'no_cookies')
            {
                echo json_encode(array('code'=>401,'sub_type'=>'no_cookies','url'=>$url));
            }
            else
            {
                echo json_encode(array('code'=>401,'sub_type'=>$subType,'desc'=>'user not login'));
            }
        }
        else
        {
            echo json_encode(array('code'=>401,'desc'=>'user not login'));
        }
        haloDie();
    }

    protected function inputErrorWithDesc($desc)
    {
        echo json_encode(array('code'=>410,'desc'=>$desc));
        haloDie();
    }

    protected function _forward($action,$controller='',$parameters=array())
    {
        $this->forward('Index', $controller, $action, $parameters);
    }

    protected function render($tpl, array $parameters = null)
    {
        $this->display($tpl, $parameters);

    }

    protected function htmlFromViewScript($name,$viewData=array())
    {
//        $viewScript = $this->_view->getScriptPath($name,true);
        $this->_view->assign($viewData);
        return $this->_view->render($name);
    }
}

