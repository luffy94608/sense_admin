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


    /**
     * list 管理
     */
    public function listAction()
    {
        $this->view->page='home-list-page';
        $model = new PageModel();
        $list = $model->getPageContentList();
        $html = HomeBuilder::toBuildListHtml($list['list']);
        $this->view->html = $html;
    }

    /**
     * 创建或者修改
     */
    public function updateListAjaxAction()
    {
        $params['uid']=$this->uid;
        $params['cid']=$this->cid;

        $params['title']=$this->getLegalParam('title','str');
        $params['content']=$this->getLegalParam('content','str');
        $params['sub_title']=$this->getLegalParam('sub_title','str');
        $params['pic']=$this->getLegalParam('pic','str');
        $params['icon']=$this->getLegalParam('icon','str');
        $params['icon_active']=$this->getLegalParam('icon_active','str');
        $params['position']=$this->getLegalParam('position','str');
        $params['links']=$this->getLegalParam('links','raw','',[]);
        if(in_array(false,$params,true))
        {
            $this->inputParamErrorResult();
        }
        $id = $this->getLegalParam('id','str');
        $model = new PageModel();
        if (empty($id))
        {
            $result = $model->createPageContentInfo($params);
            $params['id'] = $result;
        }
        else
        {
            $result = $model->updatePageContentInfo($id,$params);
            $params['id'] = $id;
        }

        if($result>0)
        {
            $detail = $model->getPageContentDetail($params['id']);
            $html = HomeBuilder::toBuildItemHtml($detail);
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
    public function deleteListAjaxAction()
    {
        $params['uid']=$this->uid;
        $params['cid']=$this->cid;

        $params['id']=$this->getLegalParam('id','str');
        if(in_array(false,$params,true))
        {
            $this->inputParamErrorResult();
        }
        $model = new PageModel();
        $result = $model->deletePageContent($params['id']);

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
     * 保存排序
     */
    public function saveListSortAjaxAction()
    {
        $params['params']=$this->getLegalParam('params','raw');
        if(in_array(false,$params,true))
        {
            $this->inputParamErrorResult();
        }
        $model=  new PageModel();
        $model->saveContentSort($params['params']);
        $this->inputResult();
    }

    /**
     * 保存排序
     */
    public function saveBannerSortAjaxAction()
    {
        $params['params']=$this->getLegalParam('params','raw');
        if(in_array(false,$params,true))
        {
            $this->inputParamErrorResult();
        }
        $model=  new BannerModel();
        $model->saveSort($params['params']);
        $this->inputResult();
    }

    /**
     * 保存排序
     */
    public function savePartnerSortAjaxAction()
    {
        $params['params']=$this->getLegalParam('params','raw');
        if(in_array(false,$params,true))
        {
            $this->inputParamErrorResult();
        }
        $model=  new PartnerModel();
        $model->saveSort($params['params']);
        $this->inputResult();
    }

}