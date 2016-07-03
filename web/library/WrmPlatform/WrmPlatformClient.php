<?php

/**
 * 微人脉SDK
 *
 * 平台文档：{@link https://client.youlu.com.cn/dokuwiki/doku.php?id=yolu:%E5%BE%AE%E4%BA%BA%E8%84%89:%E5%B9%B3%E5%8F%B0}
 *
 * @author William Qiao
 * @version 1.0
 */
class WrmPlatformClient
{
    public $source;
    public $url;
    public $host;
    public $uniqueId;
    public $cookie;
    public $timeout = 30;
    public $connecttimeout = 30;
    public $useragent = 'hollo-Platfrom-Client';
    public $debug = FALSE;
    public static $boundary = '';

    function __construct($source, $host, $uniqueId, $cookie)
    {
        $this->source = $source;
        $this->host = $host;
        $this->cookie = $cookie;
        $this->uniqueId = $uniqueId;
    }

    function genResult($response, $param)
    {
        $httpCode = $response['http_code'];
        if ($httpCode == 200) {
            $response['code'] = 0;
        } else {
            $response['code'] = $response['data']['err_code'];
        }
        if ($response['code'] != 0) {
            $response['error'] = $response['data']['error'];
            $response['url'] = $response['data']['url'];
            $response['param'] = $param;
            unset($response['data']);
        }
        return $response;
    }


    function get($url, $parameters = array())
    {
        $response = $this->sendRequest($url, 'GET', $parameters);
        return $this->genResult($response, $parameters);
    }

    function post($url, $parameters = array(), $json = true)
    {
        if ($json) {
            $response = $this->sendRequest($url, 'POST', json_encode($parameters), false);
        } else
            $response = $this->sendRequest($url, 'POST', $parameters, false, true);
        return $this->genResult($response, $parameters);
    }

    function postWithPic($url, $parameters = array())
    {
//        $parameters['picture'] = $pic;
        $response = $this->sendRequest($url, 'POST', $parameters, true);
        return $this->genResult($response, $parameters);
    }


    function sendRequest($url, $method, $parameters, $multi = false, $re = false)
    {

        if (strrpos($url, 'http://') !== 0 && strrpos($url, 'https://') !== 0) {
            $url = "{$this->host}{$url}";
        }

        switch ($method) {
            case 'GET':
                if (isset($parameters)) {
                    $url = $url . '?' . http_build_query($parameters);
                }
                return $this->http($url, 'GET');
            default:
                $headers = array(
                    'User-Agent: PinChe/1.0.0(wechat)',
                    'HOLLO-Version:400',
                    'HOLLO-Platform:wechat',
                    'HOLLO-OS:wechat',
                );
                if (!$multi) {
                    $body = $parameters;
                    $headers[] = "Content-Type: application/json;";
                } else {
                    $body = self::build_http_query_multi($parameters);
                    $headers[] = "Content-Type: multipart/form-data; boundary=" . self::$boundary;
                }
                return $this->http($url, $method, $body, $headers, $re);
        }
    }

    /**
     * Make an HTTP request
     *
     * @return string API results
     * @ignore
     */
    function http($url, $method, $postfields = NULL, $headers = array(), $re = false)
    {
//        echo $url;
        $httpInfo = array();
        $ci = curl_init();
        /* Curl settings */
        curl_setopt($ci, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
        curl_setopt($ci, CURLOPT_USERAGENT, $this->useragent);
        curl_setopt($ci, CURLOPT_CONNECTTIMEOUT, $this->connecttimeout);
        curl_setopt($ci, CURLOPT_TIMEOUT, $this->timeout);
        curl_setopt($ci, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ci, CURLOPT_ENCODING, "");
//        curl_setopt($ci, CURLOPT_SSL_VERIFYPEER, $this->ssl_verifypeer);
//        curl_setopt($ci, CURLOPT_SSL_VERIFYHOST, 1);
        curl_setopt($ci, CURLOPT_HEADERFUNCTION, array($this, 'getHeader'));
        curl_setopt($ci, CURLOPT_HEADER, FALSE);

        switch ($method) {
            case 'POST':
                curl_setopt($ci, CURLOPT_POST, TRUE);
                if (!empty($postfields)) {
                    curl_setopt($ci, CURLOPT_POSTFIELDS, $postfields);
                    $this->postdata = $postfields;
                }
                break;
        }

        curl_setopt($ci, CURLOPT_URL, $url);
        curl_setopt($ci, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ci, CURLINFO_HEADER_OUT, TRUE);

        $cookieArray = array();
        if ($this->cookie) {
            if (isset($this->cookie['uid'])) {
                $cookieArray[] = 'uid=' . urlencode($this->cookie['uid']);
            }
        }

        if ($this->uniqueId) {
            $cookieArray[] = 'UNIQUE_ID=' . urlencode($this->uniqueId);
        }

        if (count($cookieArray) > 0) {
            curl_setopt($ci, CURLOPT_COOKIE, implode(';', $cookieArray));
        }

        $response = curl_exec($ci);
        if ($re) {
            return $response;
        }
        $httpCode = curl_getinfo($ci, CURLINFO_HTTP_CODE);
        $httpInfo = array_merge($httpInfo, curl_getinfo($ci));
        if ($httpInfo['header_size'] == 0) {
            $response = array('err_code' => 404, 'error' => 'Server not found.', 'url' => $httpInfo['url']);
            $httpCode = 404;
        } else if ($httpCode != 200 && strcmp($httpInfo['content_type'], 'application/json') != 0) {
            $response = array('err_code' => $httpCode, 'error' => $response, 'url' => $httpInfo['url']);
        } else {
            $response = json_decode($response, true);
            if ($httpCode != 200) {
                $response['url'] = $httpInfo['url'];
            }
        }

        $jsonResponse = array_filter(array('http_code' => $httpCode, 'data' => $response));

        $this->url = $url;

        if ($this->debug) {
            echo "=====post data======\r\n";
            var_dump($postfields);

            echo "=====headers======\r\n";
            print_r($headers);

            echo '=====request info=====' . "\r\n";
            print_r(curl_getinfo($ci));

            echo '=====response=====' . "\r\n";
            print_r($response);
        }
        curl_close($ci);
        return $jsonResponse;
    }

    /**
     * Get the header info to store.
     *
     * @return int
     * @ignore
     */
    function getHeader($ch, $header)
    {
        $i = strpos($header, ':');
        if (!empty($i)) {
            $key = str_replace('-', '_', strtolower(substr($header, 0, $i)));
            $value = trim(substr($header, $i + 2));
            $this->http_header[$key] = $value;
        }
        return strlen($header);
    }

    /**
     *
     */
    public static function build_http_query_multi($params)
    {
        if (!$params)
            return '';

        uksort($params, 'strcmp');

        $pairs = array();

        self::$boundary = $boundary = uniqid('------------------');
        $MPboundary = '--' . $boundary;
        $endMPboundary = $MPboundary . '--';
        $multipartbody = '';

        foreach ($params as $parameter => $value) {

            if (in_array($parameter, array('picture', 'image', 'pic1', 'pic2', 'pic3', 'pic4', 'pic5', 'pic6', 'pic7', 'pic8', 'pic9', 'tipoffPic'))) {
                $url = $value;
                $content = file_get_contents($url);
                $array = explode('?', basename($url));
                $filename = $array[0];

                $multipartbody .= $MPboundary . "\r\n";
                $multipartbody .= 'Content-Disposition: form-data; name="' . $parameter . '"; filename="' . $filename . '"' . "\r\n";
                $multipartbody .= "Content-Type: image/unknown\r\n\r\n";
                $multipartbody .= $content . "\r\n";
            } else {
                $multipartbody .= $MPboundary . "\r\n";
                $multipartbody .= 'content-disposition: form-data; name="' . $parameter . "\"\r\n\r\n";
                $multipartbody .= $value . "\r\n";
            }

        }

        $multipartbody .= $endMPboundary;
        return $multipartbody;
    }
}