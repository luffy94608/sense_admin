<?php
/**
 * Created by PhpStorm.
 * User: luffy
 * Date: 15/3/23
 * Time: 17:40
 */
class UploadController extends BaseController
{

    public function init()
    {
        parent::init();
    }
    /**
     * 上传图片
     */
    public function uploadImageAction()
    {
        ini_set('max_execution_time',60*60*20);
        $cid = $this->cid;
        $map = [
            MediaModel::MediaTypeImg,
            MediaModel::MediaTypeFile,
        ];
        $type = $this->getLegalParam('type','enum',$map,MediaModel::MediaTypeImg);
        if(empty($cid))
        {
            $this->inputParamErrorResult();
        }

        $upload = new MediaModel();
        $res = $upload->newMsgMedia($type);

        $host = HolloEnv::getImgHost();
        $url = $host.$res['url'];
        $res['path']= $res['url'];
        $res['url']= $url;
        $res['img']= $url;
        $this->inputResult($res);

    }

    /**
     * ck editor upload
     */
    public function ckEditorUploadAction()
    {
        $upload = new MediaModel();
        $res = $upload->newMsgMedia(MediaModel::MediaTypeImg,'upload');
        $host = HolloEnv::getImgHost();
        $url = $host.$res['url'];
        $fn = intval($_GET['CKEditorFuncNum']); //ckeditor的funID
        $fileUrl=$url;//图片的保存地址
        $message=$_FILES['upload']['name'];//图片名称
        $str="<script type='text/javascript'>window.parent.CKEDITOR.tools.callFunction({$fn}, '{$fileUrl}','','{$message}');</script>";
        exit($str);//执行script，插入图片到编辑器
        die();
    }
}