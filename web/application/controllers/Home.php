<?php
/**
 * Created by JetBrains PhpStorm.
 * User: crazyant
 * Date: 13-11-12
 * Time: AM9:56
 * To change this template use File | Settings | File Templates.
 */


class HomeController extends BaseController
{

    public function init()
    {
        parent::init();
    }

    /**
     * banner管理
     */
    public function bannerAction()
    {
        $this->view->page='home-banner-page';
        $model = new BannerModel();
        $list = $model->getList();
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
        $model = new BannerModel();
        if (empty($id))
        {
            $result = $model->create($params);
            $params['id'] = $result;
        }
        else
        {
            $result = $model->update($id,$params);
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
    public function deleteBannerAjaxAction()
    {
        $params['uid']=$this->uid;
        $params['cid']=$this->cid;

        $params['id']=$this->getLegalParam('id','str');
        if(in_array(false,$params,true))
        {
            $this->inputParamErrorResult();
        }
        $model = new BannerModel();
        $result = $model->delete($params['id']);

        if($result>0)
        {
            $this->inputResult($result);
        }
        else
        {
            $this->inputErrorWithDesc('操作失败');
        }
    }


    /**
     * banner管理
     */
    public function partnerAction()
    {
        $this->view->page='home-partner-page';
        $model = new PartnerModel();
        $list = $model->getList();
        $html = BannerBuilder::toBuildPartnerListHtml($list);
        $this->view->html = $html;
    }

    /**
     * 创建或者修改
     */
    public function updatePartnerAjaxAction()
    {
        $params['uid']=$this->uid;
        $params['cid']=$this->cid;

        $params['logo']=$this->getLegalParam('logo','str');
        $params['url']=$this->getLegalParam('url','str');
        if(in_array(false,$params,true))
        {
            $this->inputParamErrorResult();
        }
        $id = $this->getLegalParam('id','str');
        $model = new PartnerModel();
        if (empty($id))
        {
            $result = $model->create($params);
            $params['id'] = $result;
        }
        else
        {
            $result = $model->update($id,$params);
            $params['id'] = $id;
        }

        if($result>0)
        {
            $html = BannerBuilder::toBuildPartnerItem($params);
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
    public function deletePartnerAjaxAction()
    {
        $params['uid']=$this->uid;
        $params['cid']=$this->cid;

        $params['id']=$this->getLegalParam('id','str');
        if(in_array(false,$params,true))
        {
            $this->inputParamErrorResult();
        }
        $model = new PartnerModel();
        $result = $model->delete($params['id']);

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