<?php

/**
 * Created by PhpStorm.
 * User: luffy
 * Date: 15/10/26
 * Time: 17:55
 */

class FeedbackModel extends Halo_Model
{

    private $web;
    private $web_slave;

    private $tbl_feedback = 'feedback';
    private $tbl_apply = 'apply';


    public function __construct()
    {
        parent::__construct();
        $this->web = DataCenter::getDb('web');
        $this->web_slave = DataCenter::getDb('web_slave');
    }

    /**
     * 获取feedback list
     * @param $startTime
     * @param $endTime
     * @return array
     */
    public function getFeedbackList($startTime,$endTime)
    {
        $startTime = date('Y-m-d H:i:s',strtotime($startTime));
        $endTime = date('Y-m-d H:i:s',strtotime($endTime)+(60*60*24-1));
        $result = $this->web_slave->getResultsByCondition($this->tbl_feedback,HaloPdo::condition('created_at>? AND created_at<?  ORDER BY created_at DESC ',$startTime,$endTime));
        $count = $this->web_slave->getCountByCondition($this->tbl_feedback,HaloPdo::condition('created_at>? AND created_at<?',$startTime,$endTime));
        $data = [
          'list'=>$result ? $result : [],
          'total'=>intval($count),
        ];
        return $data;
    }

    /**
     * 获取 apply list
     * @param $startTime
     * @param $endTime
     * @return array
     */
    public function getApplyList($startTime,$endTime)
    {
        $startTime = date('Y-m-d H:i:s',strtotime($startTime));
        $endTime = date('Y-m-d H:i:s',strtotime($endTime)+(60*60*24-1));
        $result = $this->web_slave->getResultsByCondition($this->tbl_apply,HaloPdo::condition('created_at>? AND created_at<?  ORDER BY created_at DESC ',$startTime,$endTime));
        $count = $this->web_slave->getCountByCondition($this->tbl_apply,HaloPdo::condition('created_at>? AND created_at<?',$startTime,$endTime));
        $data = [
            'list'=>$result ? $result : [],
            'total'=>intval($count),
        ];
        return $data;
    }


}













