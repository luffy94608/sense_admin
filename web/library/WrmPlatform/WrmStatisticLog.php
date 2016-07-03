<?php
define('R_STATISTIC', 'wrm-statistic');
define('R_STATISTIC_PATH', 'wrm-statistic-path');
date_default_timezone_set('Asia/Shanghai');

class WrmStatisticLog
{
    //$path = /opt/webroot/logs/statistic
    public static function start($path)
    {
        assert(strlen($path) > 0);
        $_REQUEST[R_STATISTIC] = array();
        $_REQUEST[R_STATISTIC_PATH] = $path;
    }

    public static function stop()
    {
        if (count($_REQUEST[R_STATISTIC]) > 0)
        {
            $time = $_SERVER['REQUEST_TIME'];
            //host.2014-04-08.00
            $date = date('Y-m-d.H', $time);
            $filepath = $_REQUEST[R_STATISTIC_PATH];
            WrmStatisticLog::ensureFilePath($filepath, true);

            $filepath = sprintf('%s/%s.%s', $filepath, $_SERVER['HTTP_HOST'], $date);

            //[08/Apr/2014:10:23:08 +0800]
            $requestTime = date('[d/M/Y:H:i:s O]', $time);

            @file_put_contents($filepath, $requestTime . "\t" . $_SERVER["UNIQUE_ID"] . "\t" . json_encode($_REQUEST[R_STATISTIC]) . "\n", FILE_APPEND);
            $_REQUEST[R_STATISTIC] = null;
        }
    }

    /**
     * @param $key
     * @param $value
     */
    public static function write($key, $value)
    {
        $_REQUEST[R_STATISTIC][$key] = $value;
    }

    private static function ensureFilePath($file_path, $is_dir = false)
    {
        if ($file_path == null || strlen($file_path) == 0)
        {
            return false;
        }

        if (!$is_dir)
        {
            $file_name = strrchr($file_path, "/");
            $dir = substr($file_path, 0, 0 - strlen($file_name));
            if (file_exists($file_path))
            {
                if (is_dir($file_path))
                {
                    return false;
                }
                return true;
            } else
            {
                if (@mkdir($dir, 0755, true))
                {
                    return true;
                } else
                {
                    return false;
                }
            }
        } else
        {
            if (file_exists($file_path))
            {
                if (!is_dir($file_path))
                {
                    return false;
                }
                return true;
            } else
            {
                if (@mkdir($file_path, 0755, true))
                {
                    return true;
                } else
                {
                    return false;
                }
            }
        }
    }
}
