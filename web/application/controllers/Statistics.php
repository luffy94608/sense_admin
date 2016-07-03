<?php
/**
 * Created by PhpStorm.
 * User: luffy
 * Date: 15/8/27
 * Time: 09:42
 */

class StatisticsController extends BaseController
{
    /**
     * @var StatisticsModel
     */
    public $model;

    public function init()
    {
        parent::init();
        $this->model=new StatisticsModel();
    }

    /**
     * 用户平台统计
     */
    public function indexAction()
    {
        $this->view->page='statistics-index-page';
        $this->display('maintain');
        return false;

    }

    /**
     * 获取需要统计的数据
     */
    public function searchStatDataAjaxAction()
    {
        $params['start_time']=$this->getLegalParam('start_time','str');
        $params['end_time']=$this->getLegalParam('end_time','str');
        if(in_array(false,$params,true))
        {
            $this->inputParamErrorResult();
        }
        $result = $this->model->getStatPageData($params['start_time'],$params['end_time']);
        $this->inputResult($result);
    }

    /**
     * 导出excel
     */
    public function createFileAjaxAction()
    {
        $result=$this->getLegalParam('data','str');
        $result=json_decode($result,true);
        if(!$result)
        {
            $this->inputErrorWithDesc('没有可导出的数据');
        }
        $keyIndex=array();
        foreach($result as $v)
        {
            $keyIndex[]=$v['line_code'];
        }
        array_multisort($keyIndex,SORT_ASC,$result);
        $model=new ExcelModel();
        $result=$model->createPathExcel($result);
        $this->inputResult($result);
    }


    
}