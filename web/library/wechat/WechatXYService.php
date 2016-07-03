<?php
/**
 * Created by PhpStorm.
 * User: jet
 * Date: 14-7-28
 * Time: 上午11:37
 */

class WechatXYService extends Halo_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->db = DataCenter::getDb('wechat');
    }

    /***
     * 连接学校验证 用户名密码
     */
    public function verifyPassport($name,$psw)
    {
        //TODO
        return true;
    }

    /***
     * 获取用户课程表
     * @param $uid
     */
    public function getUserSchedule($uid,$passName,$password)
    {
        if(!$this->verifyPassport($passName,$password))
        {
            return WechatErrorUtil::ERROR_INVALID_NAME_PSW;
        }

        $currentYear = date("Y");
        $currentMonth = intval(date("m"));
        if($currentMonth >2 && $currentMonth < 9)
        {
            $semester = intval($currentYear.'01');
        }
        else
        {
            $semester = intval($currentYear.'02');
        }

        $res = $this->db->getResultsByCondition('user_course as a LEFT JOIN course_info as b ON a.Fcourse_id = b.Fid',sprintf('a.Fuid = %d AND a.Fsemester = %d',$uid,$semester));
        foreach($res as $r)
        {
            $schedule[$r['Fdays']][$r['Fnode']][] = array('class_name'=>$r['Fname'],'teacher'=>$r['Fteacher'],'credit'=>$r['Fcredit'],'room'=>$r['Froom'],'start'=>$r['Fstart_time'],'end'=>$r['Fend_time'],'odd_even'=>$r['Fodd_even']);
        }
        for($i=1;$i<8;$i++)
        {
            if(!isset($schedule[$i]))
            {
                $schedule[$i]=array();
            }
            else
            {
                $schedule[$i]=$schedule[$i];
            }
        }
        return $schedule;
    }

    /***
     * 获得用户的成绩单
     * @param $uid
     * @return mixed
     */
    public function getUserSocre($uid,$passName,$password)
    {
        if(!$this->verifyPassport($passName,$password))
        {
            return WechatErrorUtil::ERROR_INVALID_NAME_PSW;
        }

        $res = $this->db->getResultsByCondition('user_score as a LEFT JOIN course_info as b ON a.Fcourse_id = b.Fid',sprintf('a.Fuid = %d',$uid));
        foreach($res as $r)
        {
            $schedule[$r['Fsemester']][] = array('class_name'=>$r['Fname'],'credit'=>$r['Fcredit'],'score'=>$r['Fscore']);
        }
        return $schedule;
    }

} 