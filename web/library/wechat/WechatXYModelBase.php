<?php
/**
 * Created by PhpStorm.
 * User: jet
 * Date: 14-7-24
 * Time: 上午11:53
 */

class WechatXYModelBase extends WechatModelBase
{
    /***
     * @var WechatXYService
     */
    public  $xyService;

    public function __construct()
    {
        parent::__construct();
        $this->xyService = new WechatXYService();
    }

    /***
     * 获取user的额外信息
     * @param $uid
     */
    public function getUserAddtionInfo($uid)
    {
        $res = $this->wechatDb->getRowByCondition('user_addtion_info_xy',HaloPdo::condition('Fuid = ?',$uid));
        return $res;
    }

    /***
     * 获取user的全部
     * @param $uid
     */
    public function getUserFullInfo($uid)
    {
        $user = parent::getUserBaseInfo($uid);
        if(!empty($user))
        {
            $addInfo = $this->getUserAddtionInfo($uid);
            return array_merge($user,$addInfo);
        }
        return false;
    }

    /***
     * 验证用户名密码
     * @param $uid
     * @return bool
     */
    public function isPassportValid($name,$psw)
    {
        return $this->xyService->verifyPassport($name,$psw);
    }

    /***
     * 设置与学校数据想通的user
     * @param $uid
     * @param $passportName
     * @param $passportPsw
     */
    public function setUserPassport($uid,$passportName,$passportPsw)
    {
        if($this->isPassportValid($passportName,$passportPsw))
        {
            $row = $this->wechatDb->getRowByCondition('user_addtion_info_xy',HaloPdo::condition('Fuid = ?',$uid));
            if(!empty($row))
            {
                $this->wechatDb->updateTable('user_addtion_info_xy',array('Fpass_name'=>$passportName,'Fpass_psw'=>$passportPsw),HaloPdo::condition('Fuid = ?',$uid));
            }
            else
            {
                $this->wechatDb->insertTable('user_addtion_info_xy',array('Fuid'=>$uid,'Fpass_name'=>$passportName,'Fpass_psw'=>$passportPsw));
            }
        }
        else
        {
            return WechatErrorUtil::ERROR_INVALID_NAME_PSW;
        }
    }

    /***
     * 获取用户校园的用户名密码
     * @param $uid
     */
    public function getUserPassport($uid)
    {
        $row = $this->getUserAddtionInfo($uid);
        if(!empty($row) && !empty($row['Fpass_name']) && !empty($row['Fpass_psw']))
        {
            if($this->isPassportValid($row['Fpass_name'],$row['Fpass_psw']))
            {
                return array('name'=>$row['Fpass_name'],'password'=>$row['Fpass_psw']);
            }
            else
            {
                $this->db->updateTable('user_addtion_info_xy',array('Fpass_name'=>'','Fpass_psw'=>''),HaloPdo::condition('Fuid = ?',$uid));
            }
        }
        return false;
    }
}