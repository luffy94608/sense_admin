<?php
/**
 * Created by JetBrains PhpStorm.
 * User: crazyant
 * Date: 13-11-12
 * Time: AM9:56
 * To change this template use File | Settings | File Templates.
 */


class DownloadController extends BaseController
{

    protected $model = null;
    public function init()
    {
        parent::init();
    }

    /**
     * 导出excel
     */
    public function downloadExcelAction()
    {
        $url=$this->getLegalParam('url','str');
        $name=$this->getLegalParam('name','str','','附件');
        if(!$url)
        {
            return false;
        }
        header("Content-type: text/plain; charset=utf-8");
        header("Content-Type: application/force-download");
        header("Content-type: application/octet-stream");
        header("Content-Transfer-Encoding: binary");
        header("Accept-Ranges: bytes");
        header("Accept-Length: ".filesize($url));
        header("Content-Type: application/download");
//        Header("Content-Disposition: attachment; filename=".rawurlencode(pathinfo($url,PATHINFO_BASENAME)));
        header("Content-Disposition: attachment; filename=".rawurlencode("{$name}.xls"));
        readfile($url);
        $this->inputResult();
    }


}