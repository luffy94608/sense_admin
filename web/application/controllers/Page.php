<?php
/**
 * Created by JetBrains PhpStorm.
 * User: crazyant
 * Date: 13-11-12
 * Time: AM9:56
 * To change this template use File | Settings | File Templates.
 */


class PageController extends BaseController
{
    /**
     * @var PageModel
     */
    protected $model = null;
    public function init()
    {
        parent::init();
        $this->model=new PageModel();
    }

    /**
     * 类别
     */
    public function indexAction()
    {
        $this->view->page='page-index-page';
        $list = $this->model->getPageList();
        $html = PageBuilder::toBuildPageListHtml($list['list']);
        $this->view->html = $html;

        $types = $this->model->getPageTypes();
        $this->view->types = $types;

        $model = new DownloadModel();
        $options = $model->getDownloadOptions();
        $this->view->options = $options;

    }

    /**
     * 获取类别列表
     */
    public function getPageListAjaxAction()
    {
        $params = $this->getPageParams();
        $model=  new PageModel();
        $result = $model->getPageList();
        $data = [
            'total'=>$result['total'],
            'html'=>PageBuilder::toBuildPageListHtml($result['list'])
        ];
        $this->inputResult($data);
    }

    /**
     * 更新
     */
    public function updatePageAjaxAction()
    {
        $params['uid']=$this->uid;
        $params['cid']=$this->cid;

        $params['name']=$this->getLegalParam('name','str');
        $params['title']=$this->getLegalParam('title','str');
        $params['content']=$this->getLegalParam('content','str');
        $params['img']=$this->getLegalParam('img','str');

        if(in_array(false,$params,true))
        {
            $this->inputParamErrorResult();
        }
        $id = $this->getLegalParam('id','str');
        $model=  new PageModel();
        if (empty($id))
        {
            $result = $model->createPage($params);
            $params['id'] = $result;
        }
        else
        {
            $result = $model->updatePage($id,$params);
            $params['id'] = $id;
        }

        if($result>0)
        {
            $detail = $model->getPageDetail($params['id']);
            $html = PageBuilder::toBuildPageItemHtml($detail);
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
    public function deletePageAjaxAction()
    {
        $params['uid']=$this->uid;
        $params['cid']=$this->cid;

        $params['id']=$this->getLegalParam('id','str');
        if(in_array(false,$params,true))
        {
            $this->inputParamErrorResult();
        }
        $model=  new PageModel();
        $result = $model->deletePage($params['id']);

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