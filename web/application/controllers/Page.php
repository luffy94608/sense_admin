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

        //页面类型
        $types = $this->model->getPageTypes();
        $this->view->types = $types;

        //下载列表
        $model = new DownloadModel();
        $options = $model->getDownloadOptions();
        $this->view->options = $options;

        //加密锁列表
        $lockModel  = new LockModel();
        $lockTypes = $lockModel->getTypeList();
        $this->view->lockTypes = $lockTypes['list'];

        //解决方案列表
        $solutionModel  =  new SolutionModel();
        $solutions = $solutionModel->getList();
        $this->view->solutions = $solutions['list'];
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
        $params['page_type_id']=$this->getLegalParam('page_type_id','int');
        $params['banner']=$this->getLegalParam('banner','str');
        $params['extra']=$this->getLegalParam('extra','str','','');
        $params['contents']=$this->getLegalParam('contents','raw','',[]);

        if(in_array(false,$params,true))
        {
            $this->inputParamErrorResult();
        }
        $params['title']=$this->getLegalParam('title','str');
        $params['keywords']=$this->getLegalParam('keywords','str');
        $params['description']=$this->getLegalParam('description','str');

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