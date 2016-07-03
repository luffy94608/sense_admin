<?php
/**
 * Created by PhpStorm.
 * User: luffy
 * Date: 14-8-11
 * Time: 下午5:11
 */

class MyPhpMailerUtil extends PHPMailer
{
    // Set default variables for all new objects


    public $From        = 'xiufei.lu@youyun-inc.com';
    public $FromName    = '鹿秀飞';

    public $SMTPAuth    = true;                         // 启用 SMTP 验证功能
    public $SMTPSecure  = 'ssl';                       // 安全协议
    public $Mailer      = 'smtp';                         // Alternative to isSMTP()
    public $Host        = 'smtp.exmail.qq.com';           // SMTP 服务器
    public $Port        = 465;                              //SMTP服务器的端口号

    public $Username    = 'xiufei.lu@youyun-inc.com';     // SMTP服务器用户名
    public $Password    = 'luffy0828';                    // SMTP服务器密码

    public $Sender      ='';//发件人地址
    public $ReturnPath  ='';//邮件回复地址
    public $Subject     ='重置你的友云密码';//
    public $Body        ='';//
    public $AltBody     ='';//



    public $WordWrap    = 100;
    public $CharSet     = 'UTF-8';

    public function __construct($exceptions = false)
    {
        parent::__construct($exceptions);
        $this->SetFrom($this->From,$this->FromName);
        $this->AddReplyTo($this->From,$this->FromName);
    }

    // Replace the default debug output function
    protected function edebug($msg) {
        print('My Site Error');
        print('Description:');
        printf('%s', $msg);
        exit;
    }

    //Extend the send function
    public function send() {
        $this->Subject = '[友云] '.$this->Subject;
        return parent::send();
    }

    // Create an additional function
    public function createBodyHtml() {
        // Place your new code here

    }
}