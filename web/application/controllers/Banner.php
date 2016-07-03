<?php
/**
 * Created by JetBrains PhpStorm.
 * User: crazyant
 * Date: 13-11-12
 * Time: AM9:56
 * To change this template use File | Settings | File Templates.
 */


class BannerController extends BaseController
{

    /**
     * @var BannerModel
     */
    protected $model = null;

    public function init()
    {
        parent::init();
        $this->model =  new BannerModel();
    }

    /**
     * 服务商管理
     */
    public function indexAction()
    {
        $this->view->page='banner-index-page';
        $list = $this->model->getList();
        $html = BannerBuilder::toBuildListHtml($list);
        $this->view->html = $html;
    }

    /**
     * 创建或者修改
     */
    public function updateBannerAjaxAction()
    {
        $params['uid']=$this->uid;
        $params['cid']=$this->cid;

        $params['title']=$this->getLegalParam('title','str','','');
        $params['sub_title']=$this->getLegalParam('sub_title','str','','');
        $params['url']=$this->getLegalParam('url','str','','');
        $params['btn_name']=$this->getLegalParam('btn_name','str','','');
        $params['btn_url']=$this->getLegalParam('btn_url','str','','');
        $params['img']=$this->getLegalParam('img','str');
        if(in_array(false,$params,true))
        {
            $this->inputParamErrorResult();
        }
        $id = $this->getLegalParam('id','str');
        if (empty($id))
        {
            $result = $this->model->create($params);
            $params['id'] = $result;
        }
        else
        {
            $result = $this->model->update($id,$params);
            $params['id'] = $id;
        }

        if($result>0)
        {
            $html = BannerBuilder::toBuildItem($params);
            $this->inputResult($html);
        }
        else
        {
            $this->inputErrorWithDesc('操作失败');
        }
    }

    /**
     * 删除
     */
    public function deleteAjaxAction()
    {
        $params['uid']=$this->uid;
        $params['cid']=$this->cid;

        $params['id']=$this->getLegalParam('id','str');
        if(in_array(false,$params,true))
        {
            $this->inputParamErrorResult();
        }

        $result = $this->model->delete($params['id']);

        if($result>0)
        {
            $this->inputResult($result);
        }
        else
        {
            $this->inputErrorWithDesc('操作失败');
        }
    }

}