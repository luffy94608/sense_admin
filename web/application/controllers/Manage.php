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

    /**
     * @var BaseModel
     */
    public $solutionModel;

    public function init()
    {
        parent::init();
        $map = [
            'name'=>'name',
            'title'=>'title',
            'banner'=>'banner',
            'pic'=>'pic',
            'demand'=>'demand',
            'plan'=>'plan',
            'advantage'=>'advantage',
        ];
        $this->solutionModel=  new BaseModel('solutions',$map);
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

    /**
     * 解决方案
     */
    public function solutionAction()
    {
        $this->view->page='manage-solution-page';
        $list = $this->solutionModel->getList();
        $html = ManageBuilder::toBuildSolutionListHtml($list['list']);
        $this->view->html = $html;
    }


    /**
     * 创建或者修改
     */
    public function updateSolutionAjaxAction()
    {
        $params['uid']=$this->uid;
        $params['cid']=$this->cid;

        $params['name']=$this->getLegalParam('name','str');
        $params['title']=$this->getLegalParam('title','str');
        $params['banner']=$this->getLegalParam('banner','str');
        $params['pic']=$this->getLegalParam('pic','str');
        $params['demand']=$this->getLegalParam('demand','str');
        $params['plan']=$this->getLegalParam('plan','str');
        $params['advantage']=$this->getLegalParam('advantage','str');

        if(in_array(false,$params,true))
        {
            $this->inputParamErrorResult();
        }
        $id = $this->getLegalParam('id','str');

        $model=  $this->solutionModel;
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
            $html = ManageBuilder::toBuildSolutionItem($detail);
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
    public function deleteSolutionAjaxAction()
    {
        $params['uid']=$this->uid;
        $params['cid']=$this->cid;

        $params['id']=$this->getLegalParam('id','str');
        if(in_array(false,$params,true))
        {
            $this->inputParamErrorResult();
        }
        $model=  $this->solutionModel;
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
     * 成长历程
     */
    public function routeAction()
    {
        $this->view->page='manage-route-page';

        $model = new RouteModel();
        $list = $model->getList();
        $html = ManageBuilder::toBuildRouteListHtml($list['list']);
        $this->view->html = $html;
    }

    /**
     * 创建或者修改 成长历程
     */
    public function updateRouteAjaxAction()
    {
        $params['uid']=$this->uid;
        $params['cid']=$this->cid;

        $params['name']=$this->getLegalParam('name','str');
        $params['params']=$this->getLegalParam('params','raw');

        if(in_array(false,$params,true))
        {
            $this->inputParamErrorResult();
        }
        $id = $this->getLegalParam('id','str');

        $model=  new RouteModel();
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
            $html = ManageBuilder::toBuildRouteItem($detail);
            $this->inputResult($html);
        }
        else
        {
            $this->inputErrorWithDesc('操作失败');
        }
    }

    /**
     * 删除 成长历程
     */
    public function deleteRouteAjaxAction()
    {
        $params['uid']=$this->uid;
        $params['cid']=$this->cid;

        $params['id']=$this->getLegalParam('id','str');
        if(in_array(false,$params,true))
        {
            $this->inputParamErrorResult();
        }
        $model=  new RouteModel();
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
     * 保存排序 成长历程
     */
    public function saveRouteSortAjaxAction()
    {
        $params['params']=$this->getLegalParam('params','raw');
        if(in_array(false,$params,true))
        {
            $this->inputParamErrorResult();
        }
        $model=  new RouteModel();
        $model->saveSort($params['params']);
        $this->inputResult();
    }

    /**
     * 知识和产权
     */
    public function certAction()
    {
        $this->view->page='manage-cert-page';
    }

    public function getCertListAjaxAction()
    {
        $params = $this->getPageParams();
        $params['type'] = $this->getLegalParam('type','enum',[0,1],0);
        $model=  new CertsModel();
        $result = $model->getList($params['type'],$params['offset'],$params['length']);
        $html  = ManageBuilder::toBuildCertsListHtml($result['list']);

        $data = [
            'total' =>intval($result['total']),
            'html' =>$html,
        ];
        $this->inputResult($data);
    }


    /**
     * 创建或者修改 知识和产权
     */
    public function updateCertAjaxAction()
    {
        $params['uid']=$this->uid;
        $params['cid']=$this->cid;

        $params['name']=$this->getLegalParam('name','str');
        $params['pic']=$this->getLegalParam('pic','str');
        $params['type'] = $this->getLegalParam('type','enum',[0,1],0);

        if(in_array(false,$params,true))
        {
            $this->inputParamErrorResult();
        }
        $id = $this->getLegalParam('id','str');

        $model=  new CertsModel();
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
            $html = ManageBuilder::toBuildCertsItem($detail);
            $this->inputResult($html);
        }
        else
        {
            $this->inputErrorWithDesc('操作失败');
        }
    }

    /**
     * 删除 知识和产权
     */
    public function deleteCertAjaxAction()
    {
        $params['uid']=$this->uid;
        $params['cid']=$this->cid;

        $params['id']=$this->getLegalParam('id','str');
        if(in_array(false,$params,true))
        {
            $this->inputParamErrorResult();
        }
        $model=  new CertsModel();
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
     * 保存排序 知识和产权
     */
    public function saveCertSortAjaxAction()
    {
        $params['params']=$this->getLegalParam('params','raw');
        if(in_array(false,$params,true))
        {
            $this->inputParamErrorResult();
        }
        $model=  new CertsModel();
        $model->saveSort($params['params']);
        $this->inputResult();
    }


    /**
     * 网站地图
     */
    public function mapAction()
    {
        $this->view->page='manage-map-page';
    }
    

}