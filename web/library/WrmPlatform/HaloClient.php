<?php

/**
 * Created by JetBrains PhpStorm.
 * User: WangHaoyu
 * Date: 14-3-21
 * Time: 下午6:29
 * To change this template use File | Settings | File Templates.
 */
class HaloClient
{


    /**
     * @var HaloClient
     * */
    private static $_instance;

    private $uid;
    /**
     * @var WrmPlatformClient
     */
    private $platformClient;


//----------tag----------
    const HOLLO_TEST_URL = '/user/get-test-ajax';
    const MONITOR_GET_ALL_CARS = '/exlive/getallcars';


//----------tag----------


    private function __construct()
    {
        $config = Yaf_Registry::get('config');
        $host = $config->platform->host;
        $session = Yaf_Session::getInstance();
        $uid = $session->offsetGet('uid');
        $this->uid = $uid;
        $this->platformClient = new WrmPlatformClient($config->platform->name, $host, $_SERVER['UNIQUE_ID'], $_COOKIE);
    }

    public static function singleton()
    {
        if (!isset(self::$_instance)) {
            $c = __CLASS__;
            self::$_instance = new $c;
        }
        return self::$_instance;
    }

    public function __clone()
    {
        trigger_error('Clone is not allow', E_USER_ERROR);
    }

    public function getData($url, $parameters = array())
    {
        YafDebug::httpLogForServer("\nGetData::" . $this->uid . "::[Json]\n" . $url . "\n" . json_encode($parameters, JSON_PRETTY_PRINT));
        YafDebug::httpLog("\nGetData::" . $this->uid . "::\n" . $url . "\n" . print_r($parameters, true));
        YafDebug::httpLog("\nGetData::" . $this->uid . "::\n" . $url . '?' . http_build_query($parameters) . "\n");

        $startTime = $this->getMicroTime();

        $result = $this->platformClient->get($url, $parameters);

        $endTime = $this->getMicroTime();
        YafDebug::httpLog("\n" . $this->uid . "::Request Time:\n" . ($endTime - $startTime));
        {
            YafDebug::httpLog("\nReceiveData::" . $this->uid . "::\n" . $url . "\n" . print_r($result, true));
        }
        YafDebug::httpLogForServer("\nReceiveData::" . $this->uid . "::[Json]\n" . $url . "\n" . json_encode($result, JSON_PRETTY_PRINT));

        $success = $this->checkError($result);

        if ($success) {
            $data = CommonUtils::filterHtmlSpecialChars($result['data']);

            return $data;
        } else {
            return null;
        }

    }

    public function postData_new($url, $postData = null)
    {
        $startTime = $this->getMicroTime();
        $result = $this->platformClient->post($url, $postData);

        $endTime = $this->getMicroTime();
        YafDebug::httpLog("\n" . $this->uid . "::Request Time:\n" . ($endTime - $startTime));
        YafDebug::httpLog("\nReceiveData::" . $this->uid . "::\n" . $url . "\n" . print_r($result, true));
        YafDebug::httpLogForServer("\nReceiveData::" . $this->uid . "::[Json]\n" . $url . "\n" . json_encode($result, JSON_PRETTY_PRINT));

        $success = $this->checkError($result);

        if ($success) {
            $data = CommonUtils::filterHtmlSpecialChars($result['data']);
            if ($data['code'] == 0) {
                return $data['result'] ? $data['result'] : $data['data'];
            } else {
                return $data;
            }
        } else {
            return $result;
        }
    }

    public function postData($url, $postData = null, $json = true)
    {
        YafDebug::httpLogForServer("\nPostData::" . $this->uid . "::[Json]\n" . $url . "\n" . json_encode($postData, JSON_PRETTY_PRINT));
        YafDebug::httpLog("\nPostData::" . $this->uid . "::\n" . $url . "\n" . print_r($postData, true));
        $startTime = $this->getMicroTime();

        $result = $this->platformClient->post($url, $postData, $json);

        $endTime = $this->getMicroTime();
        YafDebug::httpLog("\n" . $this->uid . "::Request Time:\n" . ($endTime - $startTime));
        YafDebug::httpLog("\nReceiveData::" . $this->uid . "::\n" . $url . "\n" . print_r($result, true));
        YafDebug::httpLogForServer("\nReceiveData::" . $this->uid . "::[Json]\n" . $url . "\n" . json_encode($result, JSON_PRETTY_PRINT));
        if (!$json) {
            return $result;
        }
        $success = $this->checkError($result);

        if ($success) {
            $data = CommonUtils::filterHtmlSpecialChars($result['data']);
            if ($data['code'] == 0) {
                return $data['result'] ? $data['result'] : $data['data'];
            } else {
                return $data;
            }
        } else {
            return $result;
        }
    }

    public function postWithPic($url, $postData = null)
    {
        YafDebug::httpLog("\nPostData::" . $this->uid . "::\n" . $url . "\n" . print_r($postData, true));
        $startTime = $this->getMicroTime();

        $result = $this->platformClient->postWithPic($url, $postData);

        $endTime = $this->getMicroTime();
        YafDebug::httpLog("\n" . $this->uid . "::Request Time:\n" . ($endTime - $startTime));
        YafDebug::httpLog("\nReceiveData::" . $this->uid . "::\n" . $url . "\n" . print_r($result, true));

        $this->checkError($result);

        $data = CommonUtils::filterHtmlSpecialChars($result['data']);

        return $data;
    }

    protected function checkError($result)
    {
        $hasError = (empty($result) || !is_array($result) || $result['code'] != 0);

        return !$hasError;
    }

    // 获取毫秒级别（13位整数）的时间戳
    private static function getMicroTime()
    {
        $time = explode(" ", microtime());
        $time = $time[1] + $time[0];
        return $time;
    }

}