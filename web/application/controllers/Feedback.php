<?php
/**
 * Created by JetBrains PhpStorm.
 * User: crazyant
 * Date: 13-11-12
 * Time: AM9:56
 * To change this template use File | Settings | File Templates.
 */


class FeedbackController extends BaseController
{

    public function init()
    {
        parent::init();
    }

    /**
     * 意见发聩
     */
    public function feedbackAction()
    {
        $this->view->page='feedback-feedback-page';
    }

    public function getFeedbackListAjaxAction()
    {
        $params['start_time'] = $this->getLegalParam('start_time','str');
        $params['end_time'] = $this->getLegalParam('end_time','str');
        if(in_array(false,$params,true))
        {
            $this->inputParamErrorResult();
        }

        $model = new FeedbackModel();
        $result = $model->getFeedbackList($params['start_time'],$params['end_time']);
        $html = FeedbackBuilder::toBuildFeedbackListHtml($result['list']);
        $this->view->html = $html;
        $data = [
//            'total' =>intval($result['total']),
            'total' =>0,
            'html' =>$html,
        ];
        $this->inputResult($data);
    }

    /**
     * 试用
     */
    public function applyAction()
    {
        $this->view->page='feedback-apply-page';
    }

    public function getApplyListAjaxAction()
    {
        $params['start_time'] = $this->getLegalParam('start_time','str');
        $params['end_time'] = $this->getLegalParam('end_time','str');
        if(in_array(false,$params,true))
        {
            $this->inputParamErrorResult();
        }

        $model = new FeedbackModel();
        $result = $model->getApplyList($params['start_time'],$params['end_time']);
        $html = FeedbackBuilder::toBuildApplyListHtml($result['list']);
        $this->view->html = $html;
        $data = [
//            'total' =>intval($result['total']),
            'total' =>0,
            'html' =>$html,
        ];
        $this->inputResult($data);
    }
}