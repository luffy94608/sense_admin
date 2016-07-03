<?php

/**
 * Created by PhpStorm.
 * User: luffy
 * Date: 15/9/9
 * Time: 17:09
 */
class StatisticsModel extends Halo_Model
{

    private $academy;
    private $academy_slave;
    private $pageTitleMap = [
        'sacad_stat_home' => '首页',
        'sacad_stat_user_list' => '用户列表页',
        'sacad_stat_teacher_list' => '老师列表页',
        'sacad_stat_study' => '学习首页',
        'sacad_stat_activity' => '活动页',
        'sacad_stat_course_top' => '课程榜单页',
        'sacad_stat_org_top' => '机构榜单页',
        'sacad_stat_teacher_top' => '老师榜单页',
    ];

    public function __construct()
    {
        parent::__construct();
        $this->academy = DataCenter::getDb('web');
        $this->academy_slave = DataCenter::getDb('web_slave');
    }

    /**
     * 获取统计数据
     * @param $startTimeStr
     * @param $endTimeStr
     * @return array
     */
    public function getStatPageData($startTimeStr,$endTimeStr)
    {
        $list = array();
        $graphs = array();
        $bullet = array('square', 'round', 'triangleUp', 'triangleDown', 'triangleLeft', 'triangleRight', 'bubble', 'diamond');

        $gapTime = 60 * 60 * 24;
        $startTime = strtotime($startTimeStr);
        $endTime = strtotime($endTimeStr) + $gapTime;

        if ($startTimeStr == $endTimeStr) {
            $endTimeStr = date('Y-m-d',$endTime);
        }

        $result = $this->academy_slave->getResultsByCondition('page_stats',HaloPdo::condition('time >= ? AND time < ?',$startTimeStr,$endTimeStr));
        $allKeys = [];
        $emptyDataItem = [];
        if($result)
        {
            foreach ($result as $v) {
                $tk = $v['url'];
                $tpk = $v['url'].'_pv';
                $tuk = $v['url'].'_uv';
                $tTime = $v['time'];
                $list[$tTime][$tpk] = $v['pv'];
                $list[$tTime][$tuk] = $v['uv'];
                $list[$tTime]['date'] = $tTime;

                if (!array_key_exists($tk, $allKeys))
                {
                    $tmpTitle = PageStatEnumModel::getPageTitle($tk);
                    $tmpTitle = $tmpTitle ? $tmpTitle : $tk;
                    $allKeys[$tpk] = $tmpTitle .' pv';
                    $allKeys[$tuk] = $tmpTitle .' uv';

                    $emptyDataItem[$tpk] = 0;
                    $emptyDataItem[$tuk] = 0;
                }
            }

            if (count($allKeys))
            {
                foreach ($allKeys as $key => $value) {
                    $graphs[$key] = array(
                        'balloonText' => '[[title]] [[value]] 人',
                        'legendValueText' => '[[value]]人',
                        'bullet' => $bullet[rand(0, count($bullet) - 1)],
//                            'bulletBorderAlpha'=>1,
//                            'bulletBorderThickness'=>3,
                        'dashLengthField' => 'dashLength',
                        'fillAlphas' => 0,
                        'title' => $value,
                        'valueField' => $key,
                        'valueAxis' => $key . 'Axis'
                    );
                }
                //没有的补上
                foreach ($list as &$item) {
                    foreach ($allKeys as $tak => $tav) {
                        if (!array_key_exists($tak, $item)) {
                            $item[$tak] = 0;
                        }
                    }
                }
            }
        }

        while ($startTime < $endTime) {
            $tmpTimeStr = date('Y-m-d',$startTime);
            if(!array_key_exists($tmpTimeStr, $list)) {
                $list[$tmpTimeStr] = $emptyDataItem;
                $list[$tmpTimeStr]['date'] = $tmpTimeStr;
            }
            $startTime = $startTime + $gapTime;
        }

        //添加平均数
//        foreach ($graphs as $k => &$v) {
//            $v['legendValueText'] = '平均 ' . intval($pathIdToTotal[$k] / count($result)) . ' 人 / 当日 ' . $v['legendValueText'];
//        }
        return array(
            'data' => array_values($list),
            'graphs' => array_values($graphs),
        );


    }

}