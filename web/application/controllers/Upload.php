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
        $cid = $this->cid;
        if(empty($cid))
        {
            $this->inputParamErrorResult();
        }

        $upload = new MediaModel();
        $res = $upload->newMsgMedia($upload::MediaTypeImg);

        $config = Yaf_Registry::get('config');
        $host = $config->img->host;
        $res['img']=$host.$res['url'];
        $this->inputResult($res);

    }
}