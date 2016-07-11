<?php
/**
 * Created by JetBrains PhpStorm.
 * User: crazyant
 * Date: 13-11-12
 * Time: AM9:56
 * To change this template use File | Settings | File Templates.
 */


class ManageController extends BaseController
{

    public function init()
    {
        parent::init();
    }

    /**
     * 招聘管理
     */
    public function recruitAction()
    {
        $this->view->page='manage-recruit-page';
    }

    /**
     * 获取招聘列表
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
     * 创建或者修改
     */
    public function updateRecruitAjaxAction()
    {
        $params['uid']=$this->uid;
        $params['cid']=$this->cid;

        $params['title']=$this->getLegalParam('title','str');
        $params['location']=$this->getLegalParam('location','str');
        $params['num']=$this->getLegalParam('num','str');
        $params['experience']=$this->getLegalParam('experience','str');
        $params['degree']=$this->getLegalParam('degree','str');
        $params['nature']=$this->getLegalParam('nature','str');
        $params['salary']=$this->getLegalParam('salary','str');
        $params['requirement']=$this->getLegalParam('requirement','str');
        $params['duty']=$this->getLegalParam('duty','str');
                        
        if(in_array(false,$params,true))
        {
            $this->inputParamErrorResult();
        }
        $id = $this->getLegalParam('id','str');
        $model=  new RecruitModel();
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
            $detail = $model->getRecruitDetail($params['id']);
            $html = ManageBuilder::toBuildRecruitItem($detail);
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
    public function deleteRecruitAjaxAction()
    {
        $params['uid']=$this->uid;
        $params['cid']=$this->cid;

        $params['id']=$this->getLegalParam('id','str');
        if(in_array(false,$params,true))
        {
            $this->inputParamErrorResult();
        }
        $model=  new RecruitModel();
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
     * 新闻
     */
    public function newsAction()
    {
        $this->view->page='manage-news-page';
    }

    /**
     * 获取新闻列表
     */
    public function getNewsListAjaxAction()
    {
        $params = $this->getPageParams();
        $model=  new NewsModel();
        $result = $model->getNewsList($params['offset'],$params['length']);
        $data = [
            'total'=>$result['total'],
            'html'=>ManageBuilder::toBuildNewsListHtml($result['list'])
        ];
        $this->inputResult($data);
    }

    /**
     * 创建或者修改
     */
    public function updateNewsAjaxAction()
    {
        $params['uid']=$this->uid;
        $params['cid']=$this->cid;

        $params['title']=$this->getLegalParam('title','str');
        $params['time']=$this->getLegalParam('time','str');
        $params['content']=$this->getLegalParam('content','str');

        if(in_array(false,$params,true))
        {
            $this->inputParamErrorResult();
        }
        $id = $this->getLegalParam('id','str');
        $model=  new NewsModel();
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
            $detail = $model->getNewsDetail($params['id']);
            $html = ManageBuilder::toBuildNewsItem($detail);
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
    public function deleteNewsAjaxAction()
    {
        $params['uid']=$this->uid;
        $params['cid']=$this->cid;

        $params['id']=$this->getLegalParam('id','str');
        if(in_array(false,$params,true))
        {
            $this->inputParamErrorResult();
        }
        $model=  new NewsModel();
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


    //
    /**
     * 网站地图
     */
    public function mapAction()
    {
        $this->view->page='manage-map-page';
    }
    

}