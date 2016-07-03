<?php

class LocalAutoLoader
{

    public static $map = array(
                                'Halo_Model'=>array(LIB_PATH, '/halo/HaloModel.php'),
                                'FastImage'=>array(LIB_PATH, '/utils/FastImage.php'),
                                'ApiInvoker'=>array(LIB_PATH, '/wechat/ApiInvoker.php'),
                                'SecurityUtils'=>array(LIB_PATH, '/utils/SecurityUtils.php'),
                                'PHPExcel'=>array(LIB_PATH, '/phpExcel/PHPExcel.php'),
                                'Excel5'=>array(LIB_PATH, '/phpExcel/PHPExcel/Writer/Excel5.php'),
                             );
    public static function register()
    {
        ini_set('unserialize_callback_func', 'spl_autoload_call');
        spl_autoload_register(array(new self(), 'autoload'));
    }
    /**
     * Handles autoloading of classes.
     *
     * @param  string  $class  A class name.
     *
     * @return boolean Returns true if the class has been loaded
     */
    public function autoload($class) {
        if (isset(static::$map[$class])){
            $pathInfo = static::$map[$class];
            Yaf_Loader::import(sprintf('%s%s',$pathInfo[0], $pathInfo[1]));
        }
        else if (strpos($class, 'Builder') === strlen($class) - 7)
        {
            Yaf_Loader::import(sprintf('%s/application/views/builder/%s.php', APPLICATION_PATH, $class));
        }
        else if (strpos($class, 'Pagelet') === strlen($class) - 7)
        {
            Yaf_Loader::import(sprintf('%s/application/pagelets/%s.php', APPLICATION_PATH, $class));
        }
        else if (strpos($class, 'Halo') === 0)
        {
            Yaf_Loader::import(sprintf('%s/halo/%s.php',LIB_PATH,$class));
        }
        else if (strpos($class, 'Util') == strlen($class) - 4 || strpos($class, 'Utils') == strlen($class) - 5)
        {
            Yaf_Loader::import(sprintf('%s/utils/%s.php',LIB_PATH,$class));
        }
        else if (strpos($class, 'Model') === strlen($class) - 5)
        {
            Yaf_Loader::import(sprintf('%s/application/models/%s.php',APPLICATION_PATH,$class));
        }
        else if (strpos($class, 'Api') === strlen($class) - 3){
            Yaf_Loader::import(sprintf('%s/application/Api/%s.php',APPLICATION_PATH,$class));
        }
        else if (strpos($class, 'Service') === strlen($class) - 7)
        {
            Yaf_Loader::import(sprintf('%s/application/service/%s.php',APPLICATION_PATH,$class));
        }
        else if (strpos($class, 'HTMLPurifier') !== false)
        {
            Yaf_Loader::import(sprintf('%s/htmlpurifier/HTMLPurifier.safe-includes.php',LIB_PATH));
        }
        else if (strpos($class, 'MemCache') === 0)
        {
            Yaf_Loader::import(sprintf('%s/wcard/%s.php',LIB_PATH, $class));
        }
    }
}

