<?php
/**
 * Created by PhpStorm.
 * User: jet
 * Date: 14/11/6
 * Time: 下午4:29
 */

class MediaModel
{

    const MediaTypeImg = 0;
    const MediaTypeFile = 1;
    const MediaTypeVoice = 2;
    const MediaTypeVideo = 3;

    public $mediaPathRoot = '';

    public function __construct()
    {
//        $this->mediaPathRoot = realpath(APPLICATION_PATH . '/../upload');
        $this->mediaPathRoot = (APPLICATION_PATH . '/public/upload/');
    }

    //获取文件后缀名函数
    private  function fileext($filename)
    {
        return substr(strrchr($filename, '.'), 1);
    }
    //生成随机文件名函数
    private function random($length)
    {
        $hash = 'CR-';
        $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz';
        $max = strlen($chars) - 1;
        mt_srand((double)microtime() * 1000000);
        for($i = 0; $i < $length; $i++)
        {
            $hash .= $chars[mt_rand(0, $max)];
        }
        return $hash;
    }

    public function newMsgMedia($type = self::MediaTypeImg)
    {
        switch($type)
        {
            case self::MediaTypeImg:
            {
                $folderPath = '/image/';
                $type=array("jpg","bmp","jpeg","png");
                $maxSize =  1024*1024*2;
                break;
            }
            case self::MediaTypeFile:
            {
                $folderPath = '/file/';
                $type=array("txt","xml","pdf",'zip','doc','ppt','xls','docx','pptx','xlsx','rar');
                $maxSize =  1024*1024*100;
                break;
            }
            case self::MediaTypeVoice:
            {
                $folderPath = '/voice/';
                $type=array("jpg","gif","bmp","jpeg","png");
                $maxSize =  1024*1024*2;
                break;
            }
            case self::MediaTypeVideo:
            {
                $folderPath = '/video/';
                $type=array("jpg","gif","bmp","jpeg","png");
                $maxSize =  1024*1024*2;
                break;
            }
            default:
            {
                $folderPath = '/image/';;
                $type=array("jpg","gif","bmp","jpeg","png");
                $maxSize =  1024*1024*2;
                break;
            }
        }
        $subFolder = sprintf('%s/',date('Y/m/d'));

        $uploadDir = $folderPath.$subFolder;
        $fullPath = $this->mediaPathRoot.$uploadDir;

        if(!file_exists($fullPath))
        {
            if(!mkdir($fullPath,0777,true))
            {
                return array('res'=>false,'desc'=>'创建文件失败');
            }
        }
        $fileArr = $_FILES['image'];
        $sourceName=$fileArr['name'];
        if(!in_array(strtolower($this->fileext($_FILES['image']['name'])),$type))
        {
            $text = implode(',',$type);
            return array('res'=>false,'desc'=>'您只能上传以下类型文件'.$text,'name'=>$sourceName);
        }
        else if ($fileArr['size'] > $maxSize)
        {
            return array('res'=>false,'desc'=>'您上传文件大小超出限制','name'=>$sourceName,'size'=>$fileArr['size']);
        }
        else
        {
            $filename=explode(".",$fileArr['name']);
            do
            {
                $filename[0] = $this->random(15); //设置随机数长度
                $name = implode(".",$filename);
                $fullPath = $fullPath.$name;
            }
            while(file_exists($fullPath));

            if (move_uploaded_file($fileArr['tmp_name'],$fullPath))
            {
                $savePath = $uploadDir.$name;
                return array('url'=>$savePath,'res'=>true,'name'=>$sourceName,'size'=>$fileArr['size']);
            }
            else
            {
                return array('desc'=>'上传失败','res'=>false,'name'=>$sourceName);
            }
        }
    }


}