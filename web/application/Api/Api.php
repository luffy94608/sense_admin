<?php

/**
 * Created by JetBrains PhpStorm.
 * User: WangHaoyu
 * Date: 14-3-5
 * Time: 上午11:43
 * To change this template use File | Settings | File Templates.
 */
class Api extends BaseApi
{

    private static $province = array(
        '0' => array
        (
            'id' => "1011",
            'name' => "北京"
        ),
        '1' => array
        (
            'id' => "1012",
            'name' => "天津"
        ),
        '2' => array
        (
            'id' => "1013",
            'name' => "河北"
        ),
        '3' => array
        (
            'id' => "1014",
            'name' => "山西"
        ),
        '4' => array
        (
            'id' => "1015",
            'name' => "内蒙古"
        ),

        '5' => array
        (
            'id' => "1021",
            'name' => "辽宁"
        ),

        '6' => array
        (
            'id' => "1022",
            'name' => "吉林"
        ),

        '7' => array
        (
            'id' => "1023",
            'name' => "黑龙江"
        ),

        '8' => array
        (
            'id' => "1031",
            'name' => "上海"
        ),

        '9' => array
        (
            'id' => "1032",
            'name' => "江苏"
        ),

        '10' => array
        (
            'id' => "1033",
            'name' => "浙江"
        ),

        '11' => array
        (
            'id' => "1034",
            'name' => "安徽"
        ),

        '12' => array
        (
            'id' => "1035",
            'name' => "福建"
        ),

        '13' => array
        (
            'id' => "1036",
            'name' => "江西"
        ),

        '14' => array
        (
            'id' => "1037",
            'name' => "山东"
        ),

        '15' => array
        (
            'id' => "1041",
            'name' => "河南"
        ),

        '16' => array
        (
            'id' => "1042",
            'name' => "湖北"
        ),

        '17' => array
        (
            'id' => "1043",
            'name' => "湖南"
        ),

        '18' => array
        (
            'id' => "1044",
            'name' => "广东"
        ),

        '19' => array
        (
            'id' => "1045",
            'name' => "广西"
        ),

        '20' => array
        (
            'id' => "1046",
            'name' => "海南"
        ),

        '21' => array
        (
            'id' => "1050",
            'name' => "重庆"
        ),

        '22' => array
        (
            'id' => "1051",
            'name' => "四川"
        ),

        '23' => array
        (
            'id' => "1052",
            'name' => "贵州"
        ),

        '24' => array
        (
            'id' => "1053",
            'name' => "云南"
        ),

        '25' => array
        (
            'id' => "1054",
            'name' => "西藏"
        ),

        '26' => array
        (
            'id' => "1061",
            'name' => "陕西"
        ),

        '27' => array
        (
            'id' => "1062",
            'name' => "甘肃"
        ),

        '28' => array
        (
            'id' => "1063",
            'name' => "青海"
        ),

        '29' => array
        (
            'id' => "1064",
            'name' => "宁夏"
        ),

        '30' => array
        (
            'id' => "1065",
            'name' => "新疆"
        ),

        '31' => array
        (
            'id' => "1071",
            'name' => "台湾"
        ),

        '32' => array
        (
            'id' => "1081",
            'name' => "香港"
        ),

        '33' => array
        (
            'id' => "1082",
            'name' => "澳门"
        ),

        '34' => array
        (
            'id' => "400",
            'name' => "海外"
        )
    );

    public static function getProvince()
    {
//        $url = '/other/get-province';
//
//        $result = self::getRequestData($url, array());

        return self::$province;
    }

    public static function getCity($provinceId)
    {
        $mc = WZhaopinEnv::getMemCache();
        $result = $mc->getByIdAndTag($provinceId, MemCacheBase::INFO_CITY_LIST);
        if ($result === false) {
            $url = '/other/get-city';
            $param = array('id' => $provinceId);
            $result = self::getRequestData($url, $param);
            if ($result != null) {
                $mc->setByIdAndTag($provinceId, MemCacheBase::INFO_CITY_LIST, $result);
            }
        }
        return $result;
    }

    public static function getIndustry()
    {
        $mc = WZhaopinEnv::getMemCache();
        $industries = $mc->getByIdAndTag("", MemCacheBase::INFO_INDUSTRY);
        if ($industries === false) {
            $url = '/other/get-industry';

            $industries = self::getRequestData($url, array());
            if ($industries != null) {
                $mc->setByIdAndTag("", MemCacheBase::INFO_INDUSTRY, $industries);
            }
        }

        return $industries;
    }

    public static function getArea()
    {
        /*$result = array(
            array('id'=>1, 'name' => '北京'),
            array('id'=>1, 'name' => '上海'),
            array('id'=>1, 'name' => '广州'),

        );*/
        $result = array();
        $result[] = array('id' => 1, 'name' => '北京');
        $result[] = array('id' => 2, 'name' => '上海');
        return $result;
    }

    public static function getYear()
    {
        $result = array();
        for ($i = date("Y"); $i >= 1910; $i--) {
            $result[] = array('id' => $i, 'name' => $i . '年');
        }
        return $result;
    }

    public static function getMonth($year = null)
    {
        if ($year == date("Y") && $year != null) {
            return static::getMonthOfThisYear();
        }

        $result = array();
        for ($i = 1; $i < 13; $i++) {
            $result[] = array('id' => $i, 'name' => $i . '月');
        }
        return $result;
    }

    //获取当前年到目前的月份
    public static function getMonthOfThisYear()
    {
        $result = array();
        for ($i = 1; $i <= date("m"); $i++) {
            $result[] = array('id' => $i, 'name' => $i . '月');
        }
        return $result;
    }

    //获取当前年到目前的月份
    public static function getDayOfThisMonth()
    {
        $result = array();
        for ($i = 1; $i <= date("d"); $i++) {
            $result[] = array('id' => $i, 'name' => $i . '日');
        }
        return $result;
    }

    /**
     * @param $year
     * @param $month
     * @param bool $cutToday : 是否截取到当前的所有日子
     * @return array
     */
    public static function getDay($year, $month, $cutToday = true)
    {
        if ($year == date("Y") && $month == date("m") && $cutToday) {
            return static::getDayOfThisMonth();
        }

        $result = array();
        for ($i = 1; $i <= 28; $i++) {
            $result[] = array('id' => $i, 'name' => $i . '日');
        }

        $runnian = false;
        if ($year % 100 == 0) {//判断世纪年

            if ($year % 400 == 0 && $year % 3200 != 0) {
                $runnian = true;
            }
        } else {//剩下的就是普通年了
            if ($year % 4 == 0 && $year % 100 != 0) {
                $runnian = true;
            }
        }

        if ($month == 2) {
            if ($runnian) {
                $result[] = array('id' => 29, 'name' => '29日');
            }
            return $result;
        }

        $result[] = array('id' => 29, 'name' => '29日');
        $result[] = array('id' => 30, 'name' => '30日');

        if ($month == 1 || $month == 3 || $month == 5 || $month == 7 || $month == 8 || $month == 10 || $month == 12) {
            $result[] = array('id' => 31, 'name' => '31日');
        }

        return $result;
    }


    public static function createAccount($param)
    {
        $url = "/user/active-company-user";
        $result = self::postRequestData($url, $param);
//        if($result != null)
        {
//            $mc =WZhaopinEnv::getMemCache();
//            $mc->deleteByIdAndTag($param['uid'], MemCacheBase::INFO_USER_STATUS);
//            $mc->deleteByIdAndTag($param['uid'], MemCacheBase::INFO_COMPANY_USER);

            WZhaopinEnv::refreshUserStatus();
        }
    }

    public static function createAccountForPreActivatedCompany($param)
    {
        $url = "/user/active-auto-company-user";
        $result = self::postRequestData($url, $param);
//        if($result != null)
        {
//            $mc =WZhaopinEnv::getMemCache();
//            $mc->deleteByIdAndTag($param['uid'], MemCacheBase::INFO_USER_STATUS);
//            $mc->deleteByIdAndTag($param['uid'], MemCacheBase::INFO_COMPANY_USER);

            WZhaopinEnv::refreshUserStatus();
        }
    }


    /**
     * @param $types 1私信，2mail(都发时，用逗号分隔)
     * @param null $toUid 接收方uid
     * @param null $toEmail 接收方email
     * @param null $subject email标题
     * @param null $content 私信内容 (发私信时必选)
     * @param null $emailContent email内容 (发Email时必选)
     * @param null $from email from (发Email时必选)
     * @param null $to email to (发Email时必选)
     * @param null $category 类别（防骚扰使用）
     * @param null $code 编号（防骚扰使用）
     */
    public static function sendPMEmail($types, $toUid = null, $subject = null, $content = null, $emailContent = null, $from = null, $to = null, $fromName = null, $category = null, $code = null)
    {
        $url = "/share/pm-email";

        $param = array(
            'types' => $types,
            'to_uid' => $toUid,
            'subject' => $subject,
            'content' => $content,
            'content_email' => $emailContent,
            'from' => $from,
            'to' => $to,
            'from_name' => $fromName,
            'category' => $category,
            'code' => $code,
        );

        self::postRequestData($url, $param);
    }

    public static function getToBenLaiUrl($card_nb)
    {
        $url = '/benlai/get-card-password';

        $param = array(
            'card_no' => $card_nb
        );

        $result = self::getRequestData($url, $param);

        return $result;
    }

    public static function getHost()
    {
        $config = Yaf_Registry::get('config');
        return $config->platform->host;
    }


    public static function UUID()
    {
        return md5(uniqid(rand(), true));
    }

    public static function handleLog($cid,$uid,$type,$desc,$params = '')
    {
        try {
            $model = new OperationModel();
            if(is_array($params)){
                $params = json_encode($params);
            }
            $model->create($cid,$uid,$type,$desc,$params);
        }
        catch(Exception $e){
            YafDebug::log($e);
        }
    }
    public static function generateOrderNo($order_no_pre){
        static $count = 0;
        $new = time();
        $ymd = date('Ymd',$new);
        $seconds = date('s',$new);
        for($i = 0;$i < 5; $i ++){
            if(strlen($seconds) == 5){
                break;
            }
            $seconds = '0'.$seconds;
        }

        $curr_str = (string)$count;
        for($j = 0;$j < 4;$j++){
            if(strlen($curr_str) == 4){
                break;
            }
            $curr_str = '0'.$curr_str;
        }
        $count++;
        return $order_no_pre.$ymd.$seconds.$curr_str;
    }
}