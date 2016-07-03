<?php
/**
 *	微信公众平台PHP-SDK, 官方API部分
 *  @author  dodge <dodgepudding@gmail.com>
 *  @link https://github.com/dodgepudding/wechat-php-sdk
 *  @version 1.2
 *  usage:
 *   $options = array(
 *			'token'=>'tokenaccesskey', //填写你设定的key
 *			'appid'=>'wxdk1234567890', //填写高级调用功能的app id
 *			'appsecret'=>'xxxxxxxxxxxxxxxxxxx', //填写高级调用功能的密钥
 *		);
 *	 $weObj = new Wechat($options);
 *   $weObj->valid();
 *   $type = $weObj->getRev()->getRevType();
 *   switch($type) {
 *   		case Wechat::MSGTYPE_TEXT:
 *   			$weObj->text("hello, I'm wechat")->reply();
 *   			exit;
 *   			break;
 *   		case Wechat::MSGTYPE_EVENT:
 *   			....
 *   			break;
 *   		case Wechat::MSGTYPE_IMAGE:
 *   			...
 *   			break;
 *   		default:
 *   			$weObj->text("help info")->reply();
 *   }
 *   //获取菜单操作:
 *   $menu = $weObj->getMenu();
 *   //设置菜单
 *   $newmenu =  array(
 *   		"button"=>
 *   			array(
 *   				array('type'=>'click','name'=>'最新消息','key'=>'MENU_KEY_NEWS'),
 *   				array('type'=>'view','name'=>'我要搜索','url'=>'http://www.baidu.com'),
 *   				)
 *  		);
 *   $result = $weObj->createMenu($newmenu);
 */

class WeChatUtil
{
    const MSGTYPE_TEXT = 'text';
    const MSGTYPE_IMAGE = 'image';
    const MSGTYPE_LOCATION = 'location';
    const MSGTYPE_LINK = 'link';
    const MSGTYPE_EVENT = 'event';//qy
    const MSGTYPE_MUSIC = 'music';
    const MSGTYPE_NEWS = 'news';
    const MSGTYPE_FILE = 'file';
    const MSGTYPE_VOICE = 'voice';
    const MSGTYPE_VIDEO = 'video';

/////////////////qy
    const API_URL_PREFIX = 'https://qyapi.weixin.qq.com/cgi-bin';
    const AUTH_URL = '/gettoken?';
    const MENU_CREATE_URL = '/menu/create?';
    const MENU_GET_URL = '/menu/get?';
    const MENU_DELETE_URL = '/menu/delete?';
    const MESSAGE_SEND = '/message/send?access_token=';

    const MEDIA_UPLOAD = '/media/upload?access_token=';

    //department
    const DEPARTMENT_CREATE = '/department/create?access_token=';
    const DEPARTMENT_UPDATE = '/department/update?access_token=';
    const DEPARTMENT_DELETE = '/department/delete?access_token=';
    const DEPARTMENT_LIST_GET = '/department/list?access_token=';

    //user
    const USER_CREATE = '/user/create?access_token=';
    const USER_UPDATE = '/user/update?access_token=';
    const USER_DELETE = '/user/delete?access_token=';
    const USER_GET = '/user/get?access_token=';
    const USER_GET_INFO = '/user/getuserinfo?access_token=';
    //agent
    const AGENT_GET = '/agent/get?access_token=';

///////////////////

    const MEDIA_GET_URL = '/media/get?';
    const QRCODE_CREATE_URL='/qrcode/create?';
    const QR_SCENE = 0;
    const QR_LIMIT_SCENE = 1;
    const QRCODE_IMG_URL='https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=';
    const USER_GET_URL='/user/get?';
    const GROUP_GET_URL='/groups/get?';
    const GROUP_CREATE_URL='/groups/create?';
    const GROUP_UPDATE_URL='/groups/update?';
    const GROUP_MEMBER_UPDATE_URL='/groups/members/update?';
    const GROUP_GET_ID='/groups/getid?';
    const OAUTH_PREFIX = 'https://open.weixin.qq.com/connect/oauth2';
    const OAUTH_AUTHORIZE_URL = '/authorize?';
    const OAUTH_TOKEN_PREFIX = 'https://api.weixin.qq.com/sns/oauth2';
    const OAUTH_TOKEN_SNS = 'https://api.weixin.qq.com/sns';
    const OAUTH_TOKEN_URL = '/access_token?';
    const OAUTH_REFRESH_URL = '/refresh_token?';
    const OAUTH_USERINFO_URL = '/userinfo?';


    private $token;
    private $appid;
    private $appsecret;
    private $access_token;
    private $user_token;
    private $_msg;
    private $_newsMsg;
    private $_receive;
    public $debug = true;
    public $errCode = 40001;
    public $errMsg = "no access";
    private $_logcallback;
    public $agentId;
    public $corpAes;

    public function __construct($corpId = null,$corpSec = null ,$agentId = null , $corpAes=null)
    {
        $config = Yaf_Registry::get('config');
        $options = array(
            'token'=>'yoluwechat', //填写你设定的key
            'debug'=>$config['app']['debug'],
            'logcallback'=>'logdebug',
            'appid' => WeChatEnv::getCorpId(),
            'appsecret' => WeChatEnv::getCorpSecret(),
            'logcallback'=>__NAMESPACE__ .'\Logger::DEBUG'
        );

        if(!empty($corpId) && !empty($corpSec))
        {
            $options['appid'] = $corpId;
            $options['appsecret'] = $corpSec;
        }

        if(!empty($agentId))
        {
            $this->agentId = $agentId;
        }
        else
        {
            $this->agentId = WeChatEnv::getAgentId();
        }
        if(empty($corpAes))
        {
            $this->corpAes = WeChatEnv::getCorpAes();
        }
        else
        {
            $this->corpAes = $corpAes;
        }

        $this->token = isset($options['token'])?$options['token']:'';
        $this->appid = isset($options['appid'])?$options['appid']:'';
        $this->appsecret = isset($options['appsecret'])?$options['appsecret']:'';
        $this->debug = isset($options['debug'])?$options['debug']:false;
        $this->_logcallback = isset($options['logcallback'])?$options['logcallback']:false;
//        $this->agentId = '00';
    }

    private function getAccessToken()
    {
        if(!$this->access_token)
        {
            $accessToken = $this->getTokenFromRedis(HaloRedis::WECHAT_TOKEN_TAG.$this->corpAes.$this->agentId);
            if(empty($accessToken))
            {
                YafDebug::log('access_token empty in db');
                YafDebug::log($accessToken);
                $this->checkAuth();
            }
            else
            {
                YafDebug::log('access_token good in db');
                YafDebug::log($accessToken);
                $this->access_token = $accessToken;
            }
        }
        else
        {
            YafDebug::log('access token is in good:');
            YafDebug::log($this->access_token);
        }
        return $this->access_token;
    }
    /**
     * For weixin server validation
     */
    private function checkSignature()
    {
        $signature = isset($_GET["signature"])?$_GET["signature"]:'';
        $timestamp = isset($_GET["timestamp"])?$_GET["timestamp"]:'';
        $nonce = isset($_GET["nonce"])?$_GET["nonce"]:'';

        $token = $this->token;
        $tmpArr = array($token, $timestamp, $nonce);
        sort($tmpArr, SORT_STRING);
        $tmpStr = implode( $tmpArr );
        YafDebug::log('sorted str is :'.$tmpStr);
        $tmpStr = sha1( $tmpStr );

        if( $tmpStr == $signature ){
            return true;
        }else{
            return false;
        }
    }

    /**
     * For weixin server validation
     * @param bool $return 是否返回
     */
    public function valid($return=false)
    {
        YafDebug::log('====valid pram=====:'.json_encode($_GET));
        $echoStr = isset($_GET["echostr"]) ? $_GET["echostr"]: '';
        if ($return)
        {
            if ($echoStr)
            {
                if ($this->checkSignature())
                {
                    YafDebug::log('====valid pram=====:1');
                    return $echoStr;
                }
                else
                {
                    YafDebug::log('====valid pram=====:2');
                    return false;
                }
            }
            else
                return $this->checkSignature();
        }
        else
        {
            if ($echoStr)
            {

                if ($this->checkSignature())
                {
                    YafDebug::log('====valid pram=====:3 echo str is '.$echoStr);
                    die($echoStr);
                }
                else
                {
                    YafDebug::log('====valid pram=====:4');
                    die('no access');
                }
            }
            else
            {
                YafDebug::log('====valid pram=====:5');
                if ($this->checkSignature())
                    return true;
                else
                    die('no access');
            }
        }
        YafDebug::log('====valid pram=====:6');
        return false;
    }

    /**
     * 设置发送消息
     * @param array $msg 消息数组
     * @param bool $append 是否在原消息数组追加
     */
    public function Message($msg = '',$append = false){
        if (is_null($msg)) {
            $this->_msg =array();
        }elseif (is_array($msg)) {
            if ($append)
                $this->_msg = array_merge($this->_msg,$msg);
            else
                $this->_msg = $msg;
            return $this->_msg;
        } else {
            return $this->_msg;
        }
    }

    private function log($log){
        if ($this->debug && $this->_logcallback) {
            if (is_array($log)) $log = print_r($log,true);
            return call_user_func($this->_logcallback,$log);
        }
    }

    /**
     * 获取微信服务器发来的信息
     */
    public function getRev()
    {
        if ($this->_receive) return $this;
        $postStr = file_get_contents("php://input");
        $this->log($postStr);
        if (!empty($postStr)) {
            $this->_receive = (array)simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
            Logger::DEBUG('getRev ::: data is: '.json_encode($this->_receive));
        }
        return $this;
    }

    /**
     * 获取微信服务器发来的信息
     */
    public function getRevData()
    {
        return $this->_receive;
    }

    /**
     * 获取微信服务器发来的信息
     */
    public function getRevAgentId()
    {
        if (isset($this->_receive['AgentID']))
            return $this->_receive['AgentID'];
        else
            return false;
    }


    /**
     * 获取消息发送者
     */
    public function getRevFrom() {
        if (isset($this->_receive['FromUserName']))
            return $this->_receive['FromUserName'];
        else
            return false;
    }

    /**
     * 获取消息接受者
     */
    public function getRevTo() {
        if (isset($this->_receive['ToUserName']))
            return $this->_receive['ToUserName'];
        else
            return false;
    }

    /**
     * 获取接收消息的类型
     */
    public function getRevType() {
        if (isset($this->_receive['MsgType']))
            return $this->_receive['MsgType'];
        else
            return false;
    }

    /**
     * 获取消息ID
     */
    public function getRevID() {
        if (isset($this->_receive['MsgId']))
            return $this->_receive['MsgId'];
        else
            return false;
    }

    /**
     * 获取消息发送时间
     */
    public function getRevCtime() {
        if (isset($this->_receive['CreateTime']))
            return $this->_receive['CreateTime'];
        else
            return false;
    }

    /**
     * 获取接收消息内容正文
     */
    public function getRevContent(){
        if (isset($this->_receive['Content']))
            return $this->_receive['Content'];
        else if (isset($this->_receive['Recognition'])) //获取语音识别文字内容，需申请开通
        return $this->_receive['Recognition'];
        else
            return false;
    }

    public function getRevMediaId() {
        if (isset($this->_receive['MediaId']))
            return $this->_receive['MediaId'];
        else
            return false;
    }

    /**
     * 获取接收消息图片
     */
    public function getRevPic(){
        if (isset($this->_receive['PicUrl']))
            return $this->_receive['PicUrl'];
        else
            return false;
    }

    /**
     * 获取接收消息链接
     */
    public function getRevLink(){
        if (isset($this->_receive['Url'])){
            return array(
                'url'=>$this->_receive['Url'],
                'title'=>$this->_receive['Title'],
                'description'=>$this->_receive['Description']
            );
        } else
            return false;
    }

    /**
     * 获取接收地理位置
     */
    public function getRevGeo(){
        if (isset($this->_receive['Location_X'])){
            return array(
                'x'=>$this->_receive['Location_X'],
                'y'=>$this->_receive['Location_Y'],
                'scale'=>$this->_receive['Scale'],
                'label'=>$this->_receive['Label']
            );
        } else
            return false;
    }

    /**
     * 获取接收地理位置
     */
    public function getRevLocation(){
        if ($this->_receive['Event'] == 'LOCATION')
        {
            return array(
                'time'=>$this->_receive['CreateTime'],
                'lat'=>$this->_receive['Latitude'],
                'lon'=>$this->_receive['Longitude'],
                'pre'=>$this->_receive['Precision']
            );
        }
        else
        {
            return false;
        }
    }

    /**
     * 获取接收事件推送
     */
    public function getRevEvent(){
        if (isset($this->_receive['Event'])){
            return array(
                'event'=>$this->_receive['Event'],
                'key'=>$this->_receive['EventKey'],
            );
        } else
            return false;
    }

    /**
     * 获取接收语言推送
     */
    public function getRevVoice(){
        if (isset($this->_receive['MediaId'])){
            return array(
                'mediaid'=>$this->_receive['MediaId'],
                'format'=>$this->_receive['Format'],
            );
        } else
            return false;
    }

    /**
     * 获取接收视频推送
     */

    public function getRevVideo(){
        if (isset($this->_receive['MediaId'])){
            return array(
                'mediaid'=>$this->_receive['MediaId'],
                'thumbmediaid'=>$this->_receive['ThumbMediaId']
            );
        } else
            return false;
    }

    public static function xmlSafeStr($str)
    {
        return '<![CDATA['.preg_replace("/[\\x00-\\x08\\x0b-\\x0c\\x0e-\\x1f]/",'',$str).']]>';
    }

    /**
     * 数据XML编码
     * @param mixed $data 数据
     * @return string
     */
    public static function data_to_xml($data) {
        $xml = '';
        foreach ($data as $key => $val) {
            is_numeric($key) && $key = "item id=\"$key\"";
            $xml    .=  "<$key>";
            $xml    .=  ( is_array($val) || is_object($val)) ? self::data_to_xml($val)  : self::xmlSafeStr($val);
            list($key, ) = explode(' ', $key);
            $xml    .=  "</$key>";
        }
        return $xml;
    }

    /**
     * XML编码
     * @param mixed $data 数据
     * @param string $root 根节点名
     * @param string $item 数字索引的子节点名
     * @param string $attr 根节点属性
     * @param string $id   数字索引子节点key转换的属性名
     * @param string $encoding 数据编码
     * @return string
     */
    public function xml_encode($data, $root='xml', $item='item', $attr='', $id='id', $encoding='utf-8') {
        if(is_array($attr)){
            $_attr = array();
            foreach ($attr as $key => $value) {
                $_attr[] = "{$key}=\"{$value}\"";
            }
            $attr = implode(' ', $_attr);
        }
        $attr   = trim($attr);
        $attr   = empty($attr) ? '' : " {$attr}";
        $xml   = "<{$root}{$attr}>";
        $xml   .= self::data_to_xml($data, $item, $id);
        $xml   .= "</{$root}>";
        return $xml;
    }

    /**
     * 设置回复音乐
     * @param string $title
     * @param string $desc
     * @param string $musicurl
     * @param string $hgmusicurl
     */
    public function music($title,$desc,$musicurl,$hgmusicurl='') {
        $msg = array(
            'ToUserName' => $this->getRevFrom(),
            'FromUserName'=>$this->getRevTo(),
            'CreateTime'=>time(),
            'MsgType'=>self::MSGTYPE_MUSIC,
            'Music'=>array(
                'Title'=>$title,
                'Description'=>$desc,
                'MusicUrl'=>$musicurl,
                'HQMusicUrl'=>$hgmusicurl
            ),
//            'FuncFlag'=>$FuncFlag
        );
        $this->Message($msg);
        return $this;
    }

    /**
     * 设置回复图文
     * @param array $newsData
     * 数组结构:
     *  array(
     *  	[0]=>array(
     *  		'Title'=>'msg title',
     *  		'Description'=>'summary text',
     *  		'PicUrl'=>'http://www.domain.com/1.jpg',
     *  		'Url'=>'http://www.domain.com/1.html'
     *  	),
     *  	[1]=>....
     *  )
     */
    public function news($newsData=array())
    {
        $count = count($newsData);

        $msg = array(
            'touser' => $this->getRevFrom(),
            'msgType'=>self::MSGTYPE_NEWS,
            'agentid'=>$this->agentId,
            'safe'=>0,
            'articles'=>$newsData,
        );
        $this->Message($msg);
        return $this;
    }

    /**
     * @param array $fileData
     * 数组结构:
     *  array(
     *      'media_id'=>'MEDIA_ID'
     *      )

     * @return $this
     */
    public function file($MEDIA_ID)
    {
        $count = count($fileData);

        $msg = array(
            'ToUserName' => $this->getRevFrom(),
            'FromUserName'=>$this->getRevTo(),
            'msgType'=>self::MSGTYPE_FILE,
            'agentid'=>$this->agentId,
            'file'=>array(
                'media_id'=>$MEDIA_ID
            )
        );
        $this->Message($msg);
        return $this;
    }

    /**
     *
     * 回复微信服务器, 此函数支持链式操作
     * @example $this->text('msg tips')->reply();
     * @param string $msg 要发送的信息, 默认取$this->_msg
     * @param bool $return 是否返回信息而不抛出到浏览器 默认:否
     */
    public function reply($msg=array(),$return = false)
    {

        if (empty($msg))
            $msg = $this->_msg;
        $xmldata=  $this->json_encode($msg);
        YafDebug::log('reply msg: '.$xmldata);
        $this->log($xmldata);
        if ($return)
            return $xmldata;
        else
            echo $xmldata;
    }

    /**
     * GET 请求
     * @param string $url
     */
    private function http_get($url){
        $oCurl = curl_init();
        if(stripos($url,"https://")!==FALSE){
            curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($oCurl, CURLOPT_SSL_VERIFYHOST, FALSE);
        }
        curl_setopt($oCurl, CURLOPT_URL, $url);
        curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1 );
        $sContent = curl_exec($oCurl);
        $aStatus = curl_getinfo($oCurl);
        curl_close($oCurl);
        if(intval($aStatus["http_code"])==200){
            return $sContent;
        }else{
            return false;
        }
    }

    private function http_get_with_token_agent($url,$needToken,$needAgentId)
    {
        if($needToken)
        {
            $url = $url.'access_token='.$this->token;
        }
        if($needAgentId)
        {
            if($needToken)
                $url = $url.'&agentid='.$this->agentId;
            else
                $url = $url.'agentid='.$this->agentId;

        }
        return $this->http_get($url);
    }

    /**
     * POST 请求
     * @param string $url
     * @param array $param
     * @return string content
     */
    private function http_post($url,$param){
        $oCurl = curl_init();
        Logger::DEBUG('url is '.$url);
        Logger::DEBUG('param is '.json_encode($param));
        if(stripos($url,"https://")!==FALSE){
            curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($oCurl, CURLOPT_SSL_VERIFYHOST, false);
        }
        if (is_string($param)) {
            $strPOST = $param;
        } else {
            $aPOST = array();
            foreach($param as $key=>$val){
                $aPOST[] = $key."=".urlencode($val);
            }
            $strPOST =  join("&", $aPOST);
        }
        curl_setopt($oCurl, CURLOPT_URL, $url);
        curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1 );
        curl_setopt($oCurl, CURLOPT_POST,true);
        curl_setopt($oCurl, CURLOPT_POSTFIELDS,$strPOST);
        $sContent = curl_exec($oCurl);
        $aStatus = curl_getinfo($oCurl);
        curl_close($oCurl);
        if(intval($aStatus["http_code"])==200){
            return $sContent;
        }else{
            return false;
        }
    }

    /**
     * POST 请求
     * @param string $url
     * @param array $param
     * @return string content
     */
    private function file_post($url,$param){
        $oCurl = curl_init();
        Logger::DEBUG('url is '.$url);
        Logger::DEBUG('param is '.json_encode($param));
        if(stripos($url,"https://")!==FALSE){
            curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($oCurl, CURLOPT_SSL_VERIFYHOST, false);
        }
        curl_setopt($oCurl, CURLOPT_URL, $url);
        curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1 );
        curl_setopt($oCurl, CURLOPT_POST,true);
        curl_setopt($oCurl, CURLOPT_POSTFIELDS,$param);
        $sContent = curl_exec($oCurl);
        $aStatus = curl_getinfo($oCurl);
        curl_close($oCurl);
        if(intval($aStatus["http_code"])==200){
            return $sContent;
        }else{
            return false;
        }
    }

    /**
     * 通用auth验证方法，暂时仅用于菜单更新操作
     * @param string $appid
     * @param string $appsecret
     */
    public function checkAuth($appid='',$appsecret=''){
        if (!$appid || !$appsecret)
        {
            $appid = $this->appid;
            $appsecret = $this->appsecret;
        }
        $result = $this->http_get(self::API_URL_PREFIX.self::AUTH_URL.'corpid='.$appid.'&corpsecret='.$appsecret);
        if ($result)
        {
            $json = json_decode($result,true);
            if (!$json || isset($json['errcode'])) {
                $this->errCode = $json['errcode'];
                $this->errMsg = $json['errmsg'];
                YafDebug::log('error::::::::');
                YafDebug::log($this->errCode);
                YafDebug::log($this->errMsg);
                return false;
            }
            YafDebug::log('check auth::::');
            YafDebug::log($json);
            $this->access_token = $json['access_token'];
            $expire = $json['expires_in'] ? intval($json['expires_in'])-100 : 3600;
            $this->setTokenToRedis(HaloRedis::WECHAT_TOKEN_TAG.$this->corpAes.$this->agentId,$this->access_token,$expire);
            return $this->access_token;
        }
        return false;
    }

    /**
     * 删除验证数据
     * @param string $appid
     */
    public function resetAuth($appid=''){
        $this->access_token = '';
        //TODO: remove cache
        return true;
    }

    /**
     * 微信api不支持中文转义的json结构
     * @param array $arr
     */
    static function json_encode($arr) {
        $parts = array ();
        $is_list = false;
        //Find out if the given array is a numerical array
        $keys = array_keys ( $arr );
        $max_length = count ( $arr ) - 1;
        if (($keys [0] === 0) && ($keys [$max_length] === $max_length )) { //See if the first key is 0 and last key is length - 1
            $is_list = true;
            for($i = 0; $i < count ( $keys ); $i ++) { //See if each key correspondes to its position
                if ($i != $keys [$i]) { //A key fails at position check.
                    $is_list = false; //It is an associative array.
                    break;
                }
            }
        }
        foreach ( $arr as $key => $value ) {
            if (is_array ( $value )) { //Custom handling for arrays
                if ($is_list)
                    $parts [] = self::json_encode ( $value ); /* :RECURSION: */
                else
                    $parts [] = '"' . $key . '":' . self::json_encode ( $value ); /* :RECURSION: */
            } else {
                $str = '';
                if (! $is_list)
                    $str = '"' . $key . '":';
                //Custom handling for multiple data types
                if (is_numeric ( $value ) && $value<2000000000)
                    $str .= $value; //Numbers
                elseif ($value === false)
                    $str .= 'false'; //The booleans
                elseif ($value === true)
                    $str .= 'true';
                else
                    $str .= '"' . addslashes ( $value ) . '"'; //All other things
                // :TODO: Is there any more datatype we should be in the lookout for? (Object?)
                $parts [] = $str;
            }
        }
        $json = implode ( ',', $parts );
        if ($is_list)
            return '[' . $json . ']'; //Return numerical JSON
        return '{' . $json . '}'; //Return associative JSON
    }

    /**
     * 创建菜单
     * @param array $data 菜单数组数据
     * example:
    {
    "button":[
    {
    "type":"click",
    "name":"今日歌曲",
    "key":"MENU_KEY_MUSIC"
    },
    {
    "type":"view",
    "name":"歌手简介",
    "url":"http://www.qq.com/"
    },
    {
    "name":"菜单",
    "sub_button":[
    {
    "type":"click",
    "name":"hello word",
    "key":"MENU_KEY_MENU"
    },
    {
    "type":"click",
    "name":"赞一下我们",
    "key":"MENU_KEY_GOOD"
    }]
    }]
    }
     */
    public function createMenu($data)
    {
        if (!$this->getAccessToken())
        {
            YafDebug::log('error');
            return false;
        }
        $url = self::API_URL_PREFIX.self::MENU_CREATE_URL.'access_token='.$this->access_token.'&agentid='.$this->agentId;
        return $this->sendPost($url,$data);
    }

    /**
     * 获取菜单
     * @return array('menu'=>array(....s))
     */
    public function getMenu(){
        if (!$this->getAccessToken())
        {
            return false;
        }
        $result = $this->http_get_with_token_agent(self::API_URL_PREFIX.self::MENU_GET_URL,true,true);
        if ($result)
        {
            $json = json_decode($result,true);
            if (!$json || isset($json['errcode'])) {
                $this->errCode = $json['errcode'];
                $this->errMsg = $json['errmsg'];
                return false;
            }
            return $json;
        }
        return false;
    }

    /**
     * 删除菜单
     * @return boolean
     */
    public function deleteMenu(){
        if (!$this->getAccessToken())
        {
            return false;
        }
        $result = $this->http_get_with_token_agent(self::API_URL_PREFIX.self::MENU_DELETE_URL,true,true);
        if ($result)
        {
            $json = json_decode($result,true);
            if (!$json || $json['errcode']>0) {
                $this->errCode = $json['errcode'];
                $this->errMsg = $json['errmsg'];
                return false;
            }
            return true;
        }
        return false;
    }

    /**
     * 根据媒体文件ID获取媒体文件
     * @param string $media_id 媒体文件id
     * @return raw data
     */
    public function getMedia($media_id){

        if (!$this->getAccessToken())
        {
            return false;
        }

        $result = $this->http_get(self::API_URL_PREFIX.self::MEDIA_GET_URL.'access_token='.$this->access_token.'&media_id='.$media_id);
        if ($result)
        {
            $json = json_decode($result,true);
            if (isset($json['errcode'])) {
                $this->errCode = $json['errcode'];
                $this->errMsg = $json['errmsg'];
                return false;
            }
            return $result;
        }
        return false;
    }

    /**
     * 创建二维码ticket
     * @param int $scene_id 自定义追踪id
     * @param int $type 0:临时二维码；1:永久二维码(此时expire参数无效)
     * @param int $expire 临时二维码有效期，最大为1800秒
     * @return array('ticket'=>'qrcode字串','expire_seconds'=>1800)
     */
    public function getQRCode($scene_id,$type=0,$expire=1800){
        if (!$this->getAccessToken())
        {
            return false;
        }
        $data = array(
            'action_name'=>$type?"QR_LIMIT_SCENE":"QR_SCENE",
            'expire_seconds'=>$expire,
            'action_info'=>array('scene'=>array('scene_id'=>$scene_id))
        );
        $result = $this->http_post(self::API_URL_PREFIX.self::QRCODE_CREATE_URL.'access_token='.$this->access_token,self::json_encode($data));
        if ($result)
        {
            $json = json_decode($result,true);
            if (!$json || $json['errcode']>0) {
                $this->errCode = $json['errcode'];
                $this->errMsg = $json['errmsg'];
                return false;
            }
            return $json;
        }
        return false;
    }

    /**
     * 获取二维码图片
     * @param string $ticket 传入由getQRCode方法生成的ticket参数
     * @return string url 返回http地址
     */
    public function getQRUrl($ticket) {
        return self::QRCODE_IMG_URL.$ticket;
    }

    /**
     * 批量获取关注用户列表
     * @param unknown $next_openid
     */
    public function getUserList($next_openid=''){
        if (!$this->getAccessToken())
        {
            return false;
        }
        $result = $this->http_get(self::API_URL_PREFIX.self::USER_GET_URL.'access_token='.$this->access_token.'&next_openid='.$next_openid);
        if ($result)
        {
            $json = json_decode($result,true);
            if (isset($json['errcode'])) {
                $this->errCode = $json['errcode'];
                $this->errMsg = $json['errmsg'];
                return false;
            }
            return $json;
        }
        return false;
    }

    /**
     * 获取用户user id
     * @return useid
     */
    public function getUserFakeId()
    {
        $code = isset($_GET['code'])?$_GET['code']:'';
        YafDebug::log('getUserFakeId ==code is'.$code);
        YafDebug::log('getUserFakeId http_post url is :'.self::API_URL_PREFIX.self::USER_GET_INFO.$this->access_token.'&code='.$code.'&agentid='.$this->agentId);

        if (!$this->getAccessToken())
        {
            return false;
        }
        $result = $this->http_get(self::API_URL_PREFIX.self::USER_GET_INFO.$this->access_token.'&code='.$code.'&agentid='.$this->agentId);
        $this->log($result);
        if ($result)
        {
            $json = json_decode($result,true);
            if (isset($json['errcode'])) {
                $this->errCode = $json['errcode'];
                $this->errMsg = $json['errmsg'];
                return false;
            }
            return $json['UserId'];
        }
        return false;
    }

    /**
     * 获取用户分组列表
     * @return boolean|array
     */
    public function getGroup(){
        if (!$this->getAccessToken())
        {
            return false;
        }
        $result = $this->http_get(self::API_URL_PREFIX.self::GROUP_GET_URL.'access_token='.$this->access_token);
        if ($result)
        {
            $json = json_decode($result,true);
            if (isset($json['errcode'])) {
                $this->errCode = $json['errcode'];
                $this->errMsg = $json['errmsg'];
                return false;
            }
            return $json;
        }
        return false;
    }

    /**
     * 新增自定分组
     * @param string $name 分组名称
     * @return boolean|array
     */
    public function createGroup($name){
        if (!$this->getAccessToken())
        {
            return false;
        }
        $data = array(
            'group'=>array('name'=>$name)
        );
        $result = $this->http_post(self::API_URL_PREFIX.self::GROUP_CREATE_URL.'access_token='.$this->access_token,self::json_encode($data));
        if ($result)
        {
            $json = json_decode($result,true);
            if (!$json || $json['errcode']>0) {
                $this->errCode = $json['errcode'];
                $this->errMsg = $json['errmsg'];
                return false;
            }
            return $json;
        }
        return false;
    }

    public  function getGroupId($id)
    {
        if (!$this->getAccessToken())
        {
            return false;
        }
        $data = array(
            'openid'=>$id
        );
        $result = $this->http_post(self::API_URL_PREFIX.self::GROUP_GET_ID.'access_token='.$this->access_token,self::json_encode($data));
        if ($result)
        {
            $json = json_decode($result,true);
            if (!$json || $json['errcode']>0) {
                $this->errCode = $json['errcode'];
                $this->errMsg = $json['errmsg'];
                return false;
            }
            return $json;
        }
        return false;
    }

    /**
     * 更改分组名称
     * @param int $groupid 分组id
     * @param string $name 分组名称
     * @return boolean|array
     */
    public function updateGroup($groupid,$name){
        if (!$this->getAccessToken())
        {
            return false;
        }
        $data = array(
            'group'=>array('id'=>$groupid,'name'=>$name)
        );
        $result = $this->http_post(self::API_URL_PREFIX.self::GROUP_UPDATE_URL.'access_token='.$this->access_token,self::json_encode($data));
        if ($result)
        {
            $json = json_decode($result,true);
            if (!$json || $json['errcode']>0) {
                $this->errCode = $json['errcode'];
                $this->errMsg = $json['errmsg'];
                return false;
            }
            return $json;
        }
        return false;
    }

    /**
     * 移动用户分组
     * @param int $groupid 分组id
     * @param string $openid 用户openid
     * @return boolean|array
     */
    public function updateGroupMembers($groupid,$openid){
        if (!$this->getAccessToken())
        {
            return false;
        }
        $data = array(
            'openid'=>$openid,
            'to_groupid'=>$groupid
        );
        $result = $this->http_post(self::API_URL_PREFIX.self::GROUP_MEMBER_UPDATE_URL.'access_token='.$this->access_token,self::json_encode($data));
        if ($result)
        {
            $json = json_decode($result,true);
            if (!$json || $json['errcode']>0) {
                $this->errCode = $json['errcode'];
                $this->errMsg = $json['errmsg'];
                return false;
            }
            return $json;
        }
        return false;
    }

    /**
     * oauth 授权跳转接口
     * @param string $callback 回调URI
     * @return string
     */
    public function getOauthRedirect($callback, $state='',$scope='snsapi_base')
    {
        Logger::DEBUG('!!!!!!!!!!!getOauthRedirect ::');
        return self::OAUTH_PREFIX.self::OAUTH_AUTHORIZE_URL.'appid='.$this->appid.'&redirect_uri='.urlencode($callback).'&response_type=code&scope='.$scope.'&state='.$state.'#wechat_redirect';
    }

    public function  getUserAuthCode($callback, $state='',$scope='snsapi_base')
    {
        Logger::DEBUG('!!!!!!!!!!!getUserAuthCode ::callBack = '.$callback.'state ='.$state);
        $url = self::OAUTH_PREFIX.self::OAUTH_AUTHORIZE_URL.'appid='.$this->appid.'&redirect_uri='.urlencode($callback).'&response_type=code&scope='.$scope.'&state='.$state.'#wechat_redirect';
        Logger::DEBUG('getUserAuthCode :: url : '.$url);

        $result = $this->http_get($url);
        Logger::DEBUG('getUserAuthCode :: res: '.$result);
        if ($result)
        {
            $json = json_decode($result,true);
            if (!$json || $json['errcode']>0) {
                $this->errCode = $json['errcode'];
                $this->errMsg = $json['errmsg'];
                return false;
            }
            return true;
        }
        return true;
    }

    public function getUserIdWithCode($code)
    {
        $code = isset($_GET['code'])?$_GET['code']:'';
        if (!$code) return false;
        $result = $this->http_get(self::OAUTH_TOKEN_PREFIX.self::OAUTH_TOKEN_URL.'appid='.$this->appid.'&secret='.$this->appsecret.'&code='.$code.'&grant_type=authorization_code');
        if ($result)
        {
            $json = json_decode($result,true);
            if (!$json || $json['errcode']>0) {
                $this->errCode = $json['errcode'];
                $this->errMsg = $json['errmsg'];
                return false;
            }
            $this->user_token = $json['access_token'];
            return $json;
        }
        return false;
    }

    /*
     * 通过code获取Access Token
     * @return array {access_token,expires_in,refresh_token,openid,scope}
     */
    public function getOauthAccessToken(){
        $code = isset($_GET['code'])?$_GET['code']:'';
        if (!$code) return false;
        $result = $this->http_get(self::API_URL_PREFIX.self::OAUTH_TOKEN_URL.'appid='.$this->appid.'&secret='.$this->appsecret.'&code='.$code.'&grant_type=authorization_code');
        if ($result)
        {
            $json = json_decode($result,true);
            if (!$json || $json['errcode']>0) {
                $this->errCode = $json['errcode'];
                $this->errMsg = $json['errmsg'];
                return false;
            }
            $this->user_token = $json['access_token'];
            return $json;
        }
        return false;
    }

    /**
     * 刷新access token并续期
     * @param string $refresh_token
     * @return boolean|mixed
     */
    public function getOauthRefreshToken($refresh_token){
        $result = $this->http_get(self::OAUTH_TOKEN_PREFIX.self::OAUTH_REFRESH_URL.'appid='.$this->appid.'&grant_type=refresh_token&refresh_token='.$refresh_token);
        if ($result)
        {
            $json = json_decode($result,true);
            if (!$json || $json['errcode']>0) {
                $this->errCode = $json['errcode'];
                $this->errMsg = $json['errmsg'];
                return false;
            }
            $this->user_token = $json['access_token'];
            return $json;
        }
        return false;
    }

    /**
     * 获取授权后的用户资料
     * @param string $access_token
     * @param string $openid
     * @return array {openid,nickname,sex,province,city,country,headimgurl,privilege}
     */
    public function getOauthUserinfo($access_token,$openid){
        $result = $this->http_get(self::OAUTH_TOKEN_SNS.self::OAUTH_USERINFO_URL.'access_token='.$access_token.'&openid='.$openid);
        if ($result)
        {
            $json = json_decode($result,true);
            if (!$json || $json['errcode']>0) {
                $this->errCode = $json['errcode'];
                $this->errMsg = $json['errmsg'];
                return false;
            }
            return $json;
        }
        return false;
    }



   public static  function isMobileMicroMessenger()
   {
       $result = array();
       $user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
       if(strstr($user_agent, 'AppleWebKit') && strstr($user_agent, 'Mobile'))
       {
           if(strpos($user_agent, 'iPhone') !== FALSE || (strstr($user_agent,'iPod')!== FALSE) || (strstr($user_agent,'iPad')!== FALSE))
           {
               $result['platform'] = 'iOS';
//               if(!strstr($user_agent, 'Safari'))
//               {
//                   $result['isMMM'] = true;
//               }
           }

           if(strstr($user_agent, 'MicroMessenger/'))
           {
               $result['isMMM'] = true;
           }
           else
           {
               $result['isMMM'] = false;
           }
       }
       else
       {
           $result['isMMM'] = false;
       }
       return $result;
   }

    public function getTokenFromRedis($key)
    {
        Logger::DEBUG('==========get redis=========== begin');
        $redis = WeChatEnv::getRedis();
        Logger::DEBUG('==========get redis=========== end');
        if($key==null)
        {
            $key = HaloRedis::WECHAT_TOKEN_TAG;
        }
        return $redis->get($key);
//        return false;

    }

    public function setTokenToRedis($key,$token,$expire)
    {
        $redis = WeChatEnv::getRedis();
        if($redis)
        {
            if($key==null)
            {
                $key = HaloRedis::WECHAT_TOKEN_TAG;
            }
            $redis->set($key,$token,0,0,$expire);
            Logger::DEBUG('$redis set done');
        }
        else
        {
            Logger::DEBUG('$redis is null');
        }

    }

//企业微信

    private function sendPost($url,$data)
    {
        $result = $this->http_post($url, $data);
        if ($result)
        {
            $json = json_decode($result,true);
            Logger::DEBUG('post recv is '.$result);
            if (!$json || $json['errcode']>0) {
                $this->errCode = $json['errcode'];
                $this->errMsg = $json['errmsg'];
                return false;
            }
            return $json;
        }
        return false;
    }

    private function sendGet($url)
    {
        $result = $this->http_get($url);
        if ($result)
        {
            $json = json_decode($result,true);
            Logger::DEBUG('post recv is '.$result);
            if (!$json || $json['errcode']>0) {
                $this->errCode = $json['errcode'];
                $this->errMsg = $json['errmsg'];
                return false;
            }
            return $json;
        }
        return false;
    }


    public function sendCunstomNewsReply($content,$userIds='@all',$partyIds=null)
    {
//        MESSAGE_SEND
        if (!$this->getAccessToken())
        {
            return false;
        }

        $msg = array();
        $hasToAllStatus=false;//touser 是否为@all 是忽略toparty

        if(isset($userIds))
        {
            if(is_array($userIds))
            {
                $msg['touser'] = implode('|',$userIds);
            }
            else if ($userIds === '@all')
            {
                $msg['touser'] = '@all';
                $hasToAllStatus=true;
            }
            else
            {
                $msg['touser'] = $userIds;
            }
        }

        if(isset($partIds))
        {
            if(!$hasToAllStatus)
            {
                $msg['toparty'] = implode('|',$partIds);
                $hasDes = true;
            }
        }
        $msg['msgtype'] = 'news';
        $msg['agentid'] = $this->agentId;
        $msg['safe'] = 0;


        $cardContent = array();
        $cardContent['articles'] = array();

        if(isset($content['title']))
        {
            $cardContent['articles'][] = $content;
        }
        else
        {
            $cardContent['articles'] = $content;
        }
        $msg['news'] = $cardContent;

        return $this->sendCustomReply($msg);
    }

    /**
     * @param $MEDIA_ID
     * @param string $userIds
     * @param null $partyIds
     * @param int $safe
     * @return bool|mixed
     */

    public function sendCunstomFileReply($MEDIA_ID,$userIds='@all',$partyIds=null,$safe=0)
    {
//        MESSAGE_SEND
        if (!$this->getAccessToken())
        {
            return false;
        }
        $msg = array();
        $hasToAllStatus=false;//touser 是否为@all 是忽略toparty

        if(isset($userIds))
        {
            if(is_array($userIds))
            {
                $msg['touser'] = implode('|',$userIds);
            }
            else if ($userIds === '@all')
            {
                $msg['touser'] = '@all';
                $hasToAllStatus=true;
            }
            else
            {
                $msg['touser'] = $userIds;
            }
        }

        if(isset($partIds))
        {
            if(!$hasToAllStatus)
            {
                $msg['toparty'] = implode('|',$partIds);
                $hasDes = true;
            }
        }

        if(!isset($msg['touser']) && !isset($msg['toparty']))
        {
            YafDebug::log('error:touser and toparty are null;/n');
            return false;
//            $msg['touser'] = $this->getRevFrom();
        }

        $msg['msgtype']='file';
        $msg['agentid']=$this->agentId;
        $msg['file']['media_id']=$MEDIA_ID;
        $msg['safe']=$safe;  //News 消息指定 safe 为 1 时会报错，错误码为 2003001(安全消息 无法转发)

        return $this->sendCustomReply($msg);
    }


    /***
     * 回复应用消息text
     * @param $content
     * @param $userIds
     * @param $partIds
     * @param $safe
     * @return bool|mixed
     */
    public function sendCunstomTextReply($content,$userIds=null,$partIds=null,$safe=0)
    {
        Logger::DEBUG('sendCunstomTextReply :: uid is'.$userIds.'content :'.$content);
        if (!$this->getAccessToken())
        {
            YafDebug::log('error');
            return false;
        }
        $msg = array();
        $hasDes = false;

        if(isset($userIds))
        {
            if(is_array($userIds))
            {
                $msg['touser'] = implode('|',$userIds);
                $hasDes = true;
            }
            else if ($userIds === '@all')
            {
                $msg['touser'] = '@all';
                $hasDes = true;
            }
            else
            {
                $msg['touser'] = $userIds;
                $hasDes = true;
            }
        }
        else if(isset($partIds))
        {
            $msg['toparty'] = implode('|',$partIds);
            $hasDes = true;
        }

        if(!$hasDes)
        {
            $msg['touser'] = $this->getRevFrom();
        }
        $msg['msgtype'] = self::MSGTYPE_TEXT;
        $msg['agentid'] = $this->agentId;
        $msg['text'] = array('content'=>$content);
        $msg['safe'] = $safe;
        YafDebug::log('sendCunstomTextReply is==========');
        YafDebug::log($msg);
        return $this->sendCustomReply($msg);
    }

    private function sendCustomReply($data)
    {
        $url = self::API_URL_PREFIX.self::MESSAGE_SEND. $this->access_token;
        if(is_array($data))
        {
            $data = self::json_encode($data);
        }
        return $this->sendPost($url,$data);
    }

    /**
     *
     * 回复微信服务器, 此函数支持链式操作
     * @example $this->text('msg tips')->reply();
     * @param string $msg 要发送的信息, 默认取$this->_msg
     * @param bool $return 是否返回信息而不抛出到浏览器 默认:否
     */
    public function replyWithJson($msg=array(),$return = true)
    {
        if (empty($msg))
            $msg = $this->_msg;

        $json=  $this->json_encode($msg);
        Logger::DEBUG('reply msg: '.$json);
        if ($return)
            return $json;
        else
            echo $json;
    }

    /**
     * 设置回复消息
     * Examle: $obj->text('hello')->reply();
     * @param string $text
     */

    public function text($text='',$userIds=null,$partyId=null)
    {
        $FuncFlag = $this->_funcflag ? 1 : 0;

        $msg = array(
            'touser' => $this->getRevFrom(),
            'FromUserName'=>$this->getRevTo(),
            'msgtype'=>self::MSGTYPE_TEXT,
            'Content'=>$text,
            'CreateTime'=>time(),
            'FuncFlag'=>$FuncFlag

        );
        $this->Message($msg);
        return $this;
    }

    public function upLoadMeida($path,$type='image')
    {
        if (!$this->getAccessToken())
        {
            YafDebug::log('error');
            return false;
        }
        $fileData = array('media'=>"@".$path.';filename=abcd.txt');
        $url = $this::API_URL_PREFIX.$this::MEDIA_UPLOAD.$this->access_token.'&type='.$type;
        $result = $this->file_post($url,$fileData);
        if ($result)
        {
            $json = json_decode($result,true);
            echo($result);
            if (!$json || $json['errcode']>0) {
                $this->errCode = $json['errcode'];
                $this->errMsg = $json['errmsg'];
                return false;
            }
            return $json;
        }
        return false;
    }


//Department

    public function createDepartment($name,$parentId=1)
    {
        if (!$this->getAccessToken())
        {
            YafDebug::log('error');
            return false;
        }
        $url = self::API_URL_PREFIX.self::DEPARTMENT_CREATE. $this->access_token;
        $data = array('access_token'=>$this->access_token,'name'=>$name,'parentid'=>$parentId);
        $json = json_encode($data);
        return $this->sendPost($url,$json);

    }

    public function updateDepartment($id,$name=null)
    {
        if (!$this->getAccessToken())
        {
            YafDebug::log('error');
            return false;
        }
        if(!isset($id))
        {
            YafDebug::log('id is null');
            return false;
        }

        $url = self::API_URL_PREFIX.self::DEPARTMENT_UPDATE. $this->access_token;
        $data = array('access_token'=>$this->access_token,'id'=>$id);
        if($name!==null)
        {
           $data['name']=$name;
        }
        $json = json_encode($data);
        return $this->sendPost($url,$json);

    }

    public function deleteDepartment($id)
    {
        if (!$this->getAccessToken())
        {
            YafDebug::log('error');
            return false;
        }
        if(!isset($id))
        {
            YafDebug::log('id is null');
            return false;
        }
        $url = self::API_URL_PREFIX.self::DEPARTMENT_DELETE. $this->access_token."id={$id}";
        return $this->sendGet($url);

    }

    public function getDepartmentList()
    {
        if (!$this->getAccessToken())
        {
            YafDebug::log('error');
            return false;
        }
        $url = self::API_URL_PREFIX.self::DEPARTMENT_LIST_GET. $this->access_token;

        return $this->sendGet($url);

    }

    public function createUser($userid,$name,$department,$mobile,$gender=null,$tel=null,$position=null,$email=null,$weixinid=null,$qq=null)
    {
        if (!$this->getAccessToken())
        {
            YafDebug::log('error');
            return false;
        }

        if(!isset($userid) || !isset($name) || !isset($department) || !isset($mobile))
        {
            YafDebug::log('params error');
            return false;
        }

        $url = self::API_URL_PREFIX.self::USER_CREATE. $this->access_token;

        $data = array(
            'access_token'=>$this->access_token,
            'userid'=>$userid,
            'name'=>$name,
            'department'=>$department,
            'mobile'=>$mobile,
            'gender'=>$gender,
            'tel'=>$tel,
            'position'=>$position,
            'email'=>$email,
            'weixinid'=>$weixinid,
            'qq'=>$qq
        );
        foreach($data as $k=>$v)
        {
            if($v===null)
            {
                unset($data[$k]);
            }
        }
        $json = json_encode($data);
        return $this->sendPost($url,$json);

    }

    public function updateUser($userid,$name=null,$department=null,$mobile=null,$gender=null,$tel=null,$position=null,$email=null,$weixinid=null)
    {
        if (!$this->getAccessToken())
        {
            YafDebug::log('error');
            return false;
        }

        if(!isset($userid))
        {
            YafDebug::log('params error');
            return false;
        }
        if(empty($mobile) && empty($email) && empty($weixinid))
        {
            YafDebug::log('params error:mobile and email and weixinid are null');
            return false;
        }

        $url = self::API_URL_PREFIX.self::USER_UPDATE. $this->access_token;
        $data = array(
            'access_token'=>$this->access_token,
            'userid'=>$userid,
            'name'=>$name,
            'department'=>$department,
            'mobile'=>$mobile,
            'gender'=>$gender,
            'tel'=>$tel,
            'position'=>$position,
            'email'=>$email,
            'weixinid'=>$weixinid,
        );

        foreach($data as $k=>$v)
        {
            if($v===null)
            {
                unset($data[$k]);
            }
        }

        $json = json_encode($data);
        return $this->sendPost($url,$json);

    }

    public function deleteUser($userid)
    {
        if (!$this->getAccessToken() || !isset($userid))
        {
            YafDebug::log('error');
            return false;
        }

        $url = self::API_URL_PREFIX.self::USER_DELETE. $this->access_token."&userid={$userid}";
        return $this->sendGet($url);

    }

    public function getUser($userid)
    {
        YafDebug::log('!!!!!!!!!!!!!getUser::::'.$userid);
        if (!$this->getAccessToken() || !isset($userid))
        {
            YafDebug::log('error');
            return false;
        }
        $url = self::API_URL_PREFIX.self::USER_GET. $this->access_token."&userid={$userid}";
        YafDebug::log('!!!!!!!!!!!!!getUser:::: url:'.$url);
        return $this->sendGet($url);

    }
    public function getAgent()
    {
        if (!$this->getAccessToken())
        {
            YafDebug::log('error');
            return false;
        }
        if($this->agentId===null){
            YafDebug::log('agentId is null');
            return false;
        }

        $url = self::API_URL_PREFIX.self::AGENT_GET. $this->access_token.'&agentid='.$this->agentId;
        return $this->sendGet($url);

    }

}