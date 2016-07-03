<?php
/**
 * Created by JetBrains PhpStorm.
 * User: WangHaoyu
 * Date: 14-3-18
 * Time: 下午8:50
 * To change this template use File | Settings | File Templates.
 */
class BaseApi
{
    protected static function getRequestData($url, $param)
    {
        $result = HaloClient::singleton()->getData(trim($url), $param);
        if ($result === false)
        {
            ob_end_clean();
            throw new Exception("", 404);
        }
        else
        {
            return $result;
        }
    }

    protected static function postRequestData($url, $data,$json=true)
    {

        $result = HaloClient::singleton()->postData(trim($url), $data,$json);

        if ($result === false)
        {
            ob_end_clean();
            throw new Exception("", 404);
        }
        else
        {
            return $result;
        }
    }



}