<?php
/**
 * Created by JetBrains PhpStorm.
 * User: crazyant
 * Date: 13-11-12
 * Time: AM9:56
 * To change this template use File | Settings | File Templates.
 */


class LockController extends BaseController
{
    /**
     * @var LockModel
     */
    protected $model = null;
    public function init()
    {
        parent::init();
        $this->model=new LockModel();
    }

    /**
     * 类别
     */
    public function typeAction()
    {
        $this->view->page='lock-type-page';
        $list = $this->model->getTypeList();
        $html = LockBuilder::toBuildLockTypeListHtml($list['list']);
        $this->view->html = $html;
    }

    /**
     * 获取类别列表
     */
    public function getRecruitListAjaxAction()
    {
        $params = $this->getPageParams();
        $model=  new RecruitModel();
        $result = $model->getRecruitList($params['offset'],$params['length']);
        $data = [
            'total'=>$result['total'],
            'html'=>ManageBuilder::toBuildRecruitListHtml($result['list'])
        ];
        $this->inputResult($data);
    }

    /**
     * 更新
     */
    public function updateTypeAjaxAction()
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
        $model=  new LockModel();
        if (empty($id))
        {
            $result = $model->createType($params);
            $params['id'] = $result;
        }
        else
        {
            $result = $model->updateType($id,$params);
            $params['id'] = $id;
        }

        if($result>0)
        {
            $detail = $model->getTypeDetail($params['id']);
            $html = LockBuilder::toBuildLockTypeItemHtml($detail);
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
    public function deleteTypeAjaxAction()
    {
        $params['uid']=$this->uid;
        $params['cid']=$this->cid;

        $params['id']=$this->getLegalParam('id','str');
        if(in_array(false,$params,true))
        {
            $this->inputParamErrorResult();
        }
        $model=  new LockModel();
        $result = $model->deleteType($params['id']);

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
     * 下载
     */
    public function downloadAction()
    {
        $this->view->page='lock-download-page';
        $model = new DownloadModel();
        $list = $model->getList();
        $options = $this->model->getTypeList();
        $html = LockBuilder::toBuildDownloadListHtml($list['list']);
        $this->view->html = $html;
        $this->view->options = $options['list'];
    }

    /**
     * 更新下载
     */
    public function updateDownloadAjaxAction()
    {
        $params['uid']=$this->uid;
        $params['cid']=$this->cid;

        $params['title']=$this->getLegalParam('title','str');
        $params['btn_name']=$this->getLegalParam('btn_name','str');
        $params['content']=$this->getLegalParam('content','str');
        $params['lock_type_id']=$this->getLegalParam('type_id','str');
        $params['url']=$this->getLegalParam('url','str');

        if(in_array(false,$params,true))
        {
            $this->inputParamErrorResult();
        }
        $id = $this->getLegalParam('id','str');
        $model=  new DownloadModel();
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
            $detail = $model->getDetail($params['id']);
            $html = LockBuilder::toBuildDownloadItem($detail);
            $this->inputResult($html);
        }
        else
        {
            $this->inputErrorWithDesc('操作失败');
        }
    }

    /**
     * 删除 下载
     */
    public function deleteDownloadAjaxAction()
    {
        $params['uid']=$this->uid;
        $params['cid']=$this->cid;

        $params['id']=$this->getLegalParam('id','str');
        if(in_array(false,$params,true))
        {
            $this->inputParamErrorResult();
        }
        $model=  new DownloadModel();
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
     * 产品列表
     */
    public function listAction()
    {
        $this->view->page='lock-list-page';
        $options = $this->model->getTypeList();
        $this->view->options = $options['list'];
    }

}