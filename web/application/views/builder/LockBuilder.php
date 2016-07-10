<?php
/**
 * Created by PhpStorm.
 * User: luffy
 * Date: 16/1/25
 * Time: 15:11
 */
class OrderBuilder
{
    /**
     * 订单类型
     * @param bool $status
     * @return array
     */
    public static function toBuildTypeOptions($status = false)
    {
        $result = [
            [
                'id' => 0,
                'name' => '待分配',
                'statusIds' => [0],
            ],
            [
                'id' => 1,
                'name' => '已分配',
                'statusIds' => [1],
            ],
            [
                'id' => 2,
                'name' => '已完成',
                'statusIds' => [2,3],
            ],
            [
                'id' => 3,
                'name' => '已关闭',
                'statusIds' => [4],
            ]

        ];
        if ($status !== false) {
            $name = '';
            foreach ($result as $v) {
                if(in_array($status,$v['statusIds'])){
                    $name = $v['name'];
                }
            }
            return $name;
        }

        return $result;
    }


    /**
     * 生成 list html
     * @param $list
     * @return string
     */
    public static function toBuildList($list)
    {
        $html = '
            <tr >
                <td class="text-center" colspan="12">无订单信息</td>
            </tr>
        ';
        if(!empty($list))
        {
            $html = '';
            foreach($list as $v){
                $html.=OrderBuilder::toBuildItem($v);
            }
        }

        return $html;
    }

    /**
     *生成 list item
     * @param $item
     * @return string
     */
    public static function toBuildItem($item)
    {
        $scoreMap = [
            1=>'很差',
            2=>'差',
            3=>'一般',
            4=>'好',
            5=>'很好',
        ];
        $info = json_encode($item);
        $html = '';
        $id = $item['id'];
        $orderNo = $item['order_no'];
        $jobId = $item['job_id'];
        $mobile = $item['mobile'];
        $user = $item['user']['name'];
        $supplier = $item['supplier']['name'];
        $supplier = $supplier ? $supplier : '未分配';
        $type = $item['type']['name'];
        $area = $item['area']['name'];
        $time = $item['created_at'];
        $desc = $item['desc'];
        $score = $item['score'];
        $scoreTitle = $scoreMap[$score];
        $remark = $item['remark'];
        $status = OrderBuilder::toBuildTypeOptions($item['status']);
        $statusClassMap = [
            0=>'danger',
            1=>'warning',
            2=>'danger',
        ];
        $statusClass = $statusClassMap[$item['status']] ? $statusClassMap[$item['status']] : 'success';

        $disBtnHtml='';
        $comBtnHtml='';
        $closeBtnHtml='';
        if (in_array($item['status'],[0,1])) {
            $disBtnHtml = "
                <a href='javascript:;' class='btn default btn-sm green js_distribution'>
                    <i class='fa fa-send'></i> 
                    分配服务商 
                </a>
            ";
        }
        if (in_array($item['status'],[1])) {
            $comBtnHtml = "
                <a href='javascript:;' class='btn default btn-sm blue js_accomplish'>
                    <i class='fa icon-pin'></i> 
                    标记完成 
                </a>
            ";
        }
        if (in_array($item['status'],[0,1])) {
            $closeBtnHtml = "
                <a href='javascript:;' class='btn default btn-sm red js_close'>
                    <i class=' icon-close'></i> 
                    关闭 
                </a>
            ";
        }


        $html.="
                <tr data-info='{$info}'  data-id='{$id}' >
                    <td>{$orderNo}</td>
                    <td>{$jobId}</td>
                    <td>{$mobile}</td>
                    <td>{$area}</td>
                    <td>{$type}</td>
                    <td>{$desc}</td>
                    <td>{$supplier}</td>
                    <td>{$time}</td>
                    <td>{$score} ({$scoreTitle})</td>
                    <td>{$remark}</td>
                    <td class='$statusClass'>{$status}</td>
                    <td>
                       {$disBtnHtml}
                       {$comBtnHtml}
                       {$closeBtnHtml}
                    </td>
                </tr>
            ";

        return $html;
    }


    /**
     * @param $list
     * @return string
     */
    public static function toBuildAreaOrTypeListHtml($list)
    {
        $html = '';
        if(!empty($list))
        {
            foreach($list as $v){
                $html.=OrderBuilder::toBuildAreaOrTypeItemHtml($v);
            }
        }

        return $html;
    }

    /**
     * item
     * @param $item
     * @return string
     */
    public static function toBuildAreaOrTypeItemHtml($item)
    {
        $info = json_encode($item);
        $id = $item['id'];
        $name = $item['name'];
        $html="
                <tr  data-id='{$id}'  data-info='{$info}'>
                    <td>{$name}</td>
                    <td>
                        <a href='javascript:;' class='btn default btn-sm blue js_edit'>
                            <i class='fa fa-edit'></i> 
                            编辑
                        </a>
                        <a href='javascript:;' class='btn default btn-sm red js_delete'>
                            <i class='fa fa-trash-o'></i> 
                            删除
                        </a>
                    </td>
                </tr>
            ";
        return $html;
    }

}