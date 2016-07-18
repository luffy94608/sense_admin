<?php

/**
 * Created by PhpStorm.
 * User: luffy
 * Date: 15/9/9
 * Time: 17:09
 */
class StatisticsModel extends Halo_Model
{

    private $web;
    private $web_slave;

    public function __construct()
    {
        parent::__construct();
        $this->web = DataCenter::getDb('web');
        $this->web_slave = DataCenter::getDb('web_slave');
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
        $pieces = array();
        $graphs = array();

        $gapTime = 60 * 60 * 24;
        $startTime = strtotime($startTimeStr);
        $endTime = strtotime($endTimeStr) + $gapTime;

        if ($startTimeStr == $endTimeStr) {
            $endTimeStr = date('Y-m-d',$endTime);
        }

        $result = $this->web_slave->getResultsByCondition('page_stats',HaloPdo::condition('time >= ? AND time <= ?',$startTimeStr,$endTimeStr));
        $result = PageStatEnumModel::toAddStatDataEmptyData($result,$startTimeStr);
        $allKeys = [];
        $emptyDataItem = [];
        if($result)
        {
            foreach ($result as $v) {
                $tk = $v['url'];
                $tpk = $v['url'].'_pv';
                $tuk = $v['url'].'_uv';
                $tTime = $v['time'];
                $tmpTitle = PageStatEnumModel::getPageTitle($tk);
                $tmpTitle = $tmpTitle ? $tmpTitle : $tk;

                $list[$tTime][$tpk] = $v['pv'];
                $list[$tTime][$tuk] = $v['uv'];
                $list[$tTime]['date'] = $tTime;

                $pieces[$tk]['data'][$tTime][$tpk]=$v['pv'];
                $pieces[$tk]['data'][$tTime][$tuk]=$v['uv'];
                $pieces[$tk]['data'][$tTime]['date']=$tTime;
                if(!isset($pieces[$tk]['graph'][$tpk])){
                    $pieces[$tk]['graph'][$tpk]=$this->getGranphsInfo($tpk,$tmpTitle.' pv');
                }
                if(!isset($pieces[$tk]['graph'][$tuk])){
                    $pieces[$tk]['graph'][$tuk]=$this->getGranphsInfo($tuk,$tmpTitle.' uv');
                }
                $pieces[$tk]['empty_data'][$tpk]=0;
                $pieces[$tk]['empty_data'][$tuk]=0;
                $pieces[$tk]['id']=$tk;
                $pieces[$tk]['title']=$tmpTitle;

                if (!array_key_exists($tk, $allKeys))
                {

                    $allKeys[$tpk] = $tmpTitle .' pv';
                    $allKeys[$tuk] = $tmpTitle .' uv';

                    $emptyDataItem[$tpk] = 0;
                    $emptyDataItem[$tuk] = 0;
                }
            }

            if (count($allKeys))
            {
                foreach ($allKeys as $key => $value) {
                    $graphs[$key] = $this->getGranphsInfo($key,$value);
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
            if($pieces)
            {
                foreach ($pieces as  &$piece) {
                    if(!array_key_exists($tmpTimeStr,$piece['data']))
                    {
                        $tmpData = $piece['empty_data'];
                        $tmpData['date'] = $tmpTimeStr;
                        $piece['data'][$tmpTimeStr]=$tmpData;
                    }
                }
            }
            $startTime = $startTime + $gapTime;
        }
        if($pieces)
        {
            foreach ($pieces as &$piece2) {
                $piece2['data'] = array_values($piece2['data']);
                $piece2['graph'] = array_values($piece2['graph']);
            }
        }

        //添加平均数
//        foreach ($graphs as $k => &$v) {
//            $v['legendValueText'] = '平均 ' . intval($pathIdToTotal[$k] / count($result)) . ' 人 / 当日 ' . $v['legendValueText'];
//        }
        $data = array(
            'pieces' => array_values($pieces),
            'data' => array_values($list),
            'graphs' => array_values($graphs),
        );

        return $data;
    }

    /**
     * 获取 $graphs
     * @param $key
     * @param $title
     * @return array
     */
    private function getGranphsInfo($key,$title)
    {
        $bullet = array('square', 'round', 'triangleUp', 'triangleDown', 'triangleLeft', 'triangleRight', 'bubble', 'diamond');

        $graphs = [
            'balloonText' => '[[title]] [[value]] 人',
            'legendValueText' => '[[value]]人',
            'bullet' => $bullet[rand(0, count($bullet) - 1)],
//                            'bulletBorderAlpha'=>1,
//                            'bulletBorderThickness'=>3,
            'dashLengthField' => 'dashLength',
            'fillAlphas' => 0,
            'title' => $title,
            'valueField' => $key,
            'valueAxis' => $key . 'Axis'
        ];
        return $graphs;
    }

}