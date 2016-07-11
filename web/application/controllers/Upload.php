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
}