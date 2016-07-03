<?php

/**
 * Created by PhpStorm.
 * User: luffy
 * Date: 15/10/26
 * Time: 17:55
 */
class OrderModel extends Halo_Model
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
     * 获取 列表
     * @param $type 0 //0带分配 1已分配 2已完成 3已关闭 -1全部
    //     * @param $status 0 需求提交(等待分单)  1供应商受理(已经分单)  2服务已完成  未评价 3已评价  4 关闭
     * @param $key
     * @param int $offset
     * @param int $length
     * @return array
     */
    public function getListWithStatus($type,$key='',$offset = 0,$length = 20)
    {
        $search = '';
        if ($key) {
            $search = sprintf('(order_no=\'%s\' OR job_id=\'%s\') AND ',$key,$key);
        }
        $statusMap = [
            -1 =>[0,1,2,3,4],
            0 =>[0],
            1 =>[1],
            2 =>[2,3],
            3 =>[4],
        ];
        $status = $statusMap[$type];

        $pageSql = '';
        if($type > -1){
            $pageSql = sprintf(' limit %s offset %s',$length,$offset);
        }
        $result = $this->web_slave->getResultsByCondition('orders',sprintf('%s status IN (%s) ORDER BY id DESC %s',$search,implode(',',$status),$pageSql));
        if ($result)
        {
            $result = $this->toBuildOrderInfo($result);
        }
        $total = $this->web_slave->getCountByCondition('orders',sprintf('%s status IN (%s)',$search,implode(',',$status)));
        $data = [
          'list'=>$result,
          'total'=>$total,
        ];
        return $data;
    }

    /**
     * 订单列表添加显示信息
     * @param $list
     * @return array
     */
    private function toBuildOrderInfo($list)
    {
        $result = [];

        $userMap = [];
        $supplierMap = [];
        $typeMap = [];
        $areaMap = [];

        $userIds = [];
        $supplierIds = [];
        $typeIds = [];
        $areaIds = [];

        foreach ($list as $v) {
            $id = $v['id'];
            $userIds[] = $v['user_id'];
            $supplierIds[] = $v['supplier_id'];
            $typeIds[] = $v['type_id'];
            $areaIds[] = $v['area_id'];
            $result[$id] = $v;
        }

        if (!empty($userIds)) {
            $users = $this->web_slave->getResultsByCondition('users',sprintf('id IN (%s)',implode(',',$userIds)));
            if ($users) {
                foreach ($users as $user){
                    $userMap[$user['id']]=$user;
                }
            }
        }
        if (!empty($supplierIds)) {
            $suppliers = $this->web_slave->getResultsByCondition('suppliers',sprintf('id IN (%s)',implode(',',$supplierIds)));
            if ($suppliers) {
                foreach ($suppliers as $supplier){
                    $supplierMap[$supplier['id']]=$supplier;
                }
            }
        }
        if (!empty($typeIds)) {
            $types = $this->web_slave->getResultsByCondition('types',sprintf('id IN (%s)',implode(',',$typeIds)));
            if ($types) {
                foreach ($types as $type){
                    $typeMap[$type['id']]=$type;
                }
            }
        }
        if (!empty($areaIds)) {
            $areas = $this->web_slave->getResultsByCondition('areas',sprintf('id IN (%s)',implode(',',$areaIds)));
            if ($areas) {
                foreach ($areas as $area){
                    $areaMap[$area['id']]=$area;
                }
            }
        }

        foreach ($result as &$item) {
            $userId = $item ['user_id'];
            $supplierId = $item ['supplier_id'];
            $typeId = $item ['type_id'];
            $areaId = $item ['area_id'];

            if (array_key_exists($userId,$userMap)) {
                $item['user'] = $userMap[$userId];
            }
            if (array_key_exists($supplierId,$supplierMap)) {
                $item['supplier'] = $supplierMap[$supplierId];
            }
            if (array_key_exists($typeId,$typeMap)) {
                $item['type'] = $typeMap[$typeId];
            }
            if (array_key_exists($areaId,$areaMap)) {
                $item['area'] = $typeMap[$areaId];
            }
            $item['statusTitle'] = $typeMap[$areaId];
        }
        return $result;
    }

    /**
     * 获取订单详情
     * @param $id
     * @return array|bool|string
     */
    public function getOrderDetail($id)
    {
        $result = $this->web_slave->getResultsByCondition('orders',HaloPdo::condition('id=?',$id));
        if($result)
        {
            $result = $this->toBuildOrderInfo($result);

            $result = array_shift($result);
        }
        return $result;
    }

    /**
     * 更新
     * @param $id
     * @param $params
     * @return bool|int
     */
    public function update($id,$params)
    {
        if(!empty($params))
        {
            $map=array(
                'supplier_id'=>'supplier_id',
                'operator_id'=>'operator_id',
                'status'=>'status',
            );
            $data=array();
            foreach($params as $k=>$v)
            {
                if(array_key_exists($k,$map) && $v!==false)
                {
                    $data[$map[$k]]=$v;
                }
            }
            $data['updated_at']=date('Y-m-d H:i:s',time());
            return $this->web->updateTable('orders',$data,HaloPdo::condition('id = ?',$id));
        }
        return false;
    }

    /**
     * 删除
     * @param $id
     * @return int
     */
    public function delete($id)
    {
        return  $this->web->delRowByCondition2('orders',HaloPdo::condition('id = ?',$id));
    }


    /////============   区域和服务  ============////

    /**
     * 修改区域或者服务名称
     * @param $name
     * @param int $type 0 区域 1服务
     * @return bool|int
     */
    public function createOrderOrType($name,$type = 0)
    {
        $params=array(
            'name'=>$name,
        );
        $tbl_name = 'areas';
        if ($type == 1) {
            $tbl_name = 'types';
        }

        $result = $this->web->insertTable($tbl_name,$params);
        return $result;
    }

    /**
     * 更新 type area
     * @param $id
     * @param $type
     * @param $params
     * @return bool|int
     */
    public function updateOrderOrType($id,$params,$type = 0 )
    {
        if(!empty($params))
        {
            $map=array(
                'name'=>'name',
            );
            $data=array();
            foreach($params as $k=>$v)
            {
                if(array_key_exists($k,$map) && $v!==false)
                {
                    $data[$map[$k]]=$v;
                }
            }
            $tbl_name = 'areas';
            if ($type == 1) {
                $tbl_name = 'types';
            }
            return $this->web->updateTable($tbl_name,$data,HaloPdo::condition('id = ?',$id));
        }
        return false;
    }

    /**
     * 删除
     * @param $id
     * @return int
     */
    public function deleteOrderOrType($id, $type = 0)
    {
        $tbl_name = 'areas';
        if ($type == 1) {
            $tbl_name = 'types';
        }
        return  $this->web->delRowByCondition2($tbl_name,HaloPdo::condition('id = ?',$id));
    }

    /**
     *
     * @param $id
     * @param int $type
     * @return int
     */
    public function getAreaOrTypeName($id, $type = 0)
    {
        $tbl_name = 'areas';
        if ($type == 1) {
            $tbl_name = 'types';
        }
        $result = $this->web->getRowByCondition($tbl_name,HaloPdo::condition('id = ?',$id));
        return $result ? $result['name'] :'';
    }


    /**
     * 区域列表
     * @return array|bool|string
     */
    public function getAreaList()
    {
        $result = $this->web_slave->getResultsByCondition('areas',HaloPdo::condition('id>0 ORDER BY id DESC'));
        return $result ? $result : [];
    }
    /**
     * 服务列表
     * @return array|bool|string
     */
    public function getTypeList()
    {
        $result = $this->web_slave->getResultsByCondition('types',HaloPdo::condition('id>0 ORDER BY id DESC'));
        return $result ? $result : [];
    }

}













