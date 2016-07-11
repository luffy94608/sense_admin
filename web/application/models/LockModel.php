<?php

/**
 * Created by PhpStorm.
 * User: luffy
 * Date: 15/10/26
 * Time: 17:55
 */

class LockModel extends Halo_Model
{

    private $web;
    private $web_slave;
    public $tbl_type_name = 'lock_types';
    public $tbl_name = 'locks';
    public $timestamps = true;

    public $keyTypeMap = array(
        'name'=>'name',
        'title'=>'title',
        'img'=>'img',
        'content'=>'content',
    );

    public function __construct()
    {
        parent::__construct();
        $this->web = DataCenter::getDb('web');
        $this->web_slave = DataCenter::getDb('web_slave');
    }

    /**
     * 修改类别
     * @param $params
     * @return bool|int
     */
    public function createType($params)
    {
        $timeStr = date('Y-m-d H:i:s');
        $data = [];
        $maps  = $this->keyTypeMap;
        foreach($maps as $k=>$v)
        {
            if(array_key_exists($v,$params) && $v!==false)
            {
                $data[$k]=$params[$v];
            }
        }
        if($this->timestamps){
            $data['created_at'] = $timeStr;
            $data['updated_at'] = $timeStr;
        }

        $result = $this->web->insertTable($this->tbl_type_name,$data);
        return $result;
    }

    /**
     * 更新类别
     * @param $id
     * @param $params
     * @return bool|int
     */
    public function updateType($id,$params )
    {
        if(!empty($params))
        {
            $map=$this->keyTypeMap;
            $data=array();
            foreach($params as $k=>$v)
            {
                if(array_key_exists($k,$map) && $v!==false)
                {
                    $data[$map[$k]]=$v;
                }
            }
            if($this->timestamps){
                $data['updated_at'] = date('Y-m-d H:i:s');
            }
            return $this->web->updateTable($this->tbl_type_name,$data,HaloPdo::condition('id = ?',$id));
        }
        return false;
    }
    /**
     * 删除类别
     * @param $id
     * @return int
     */
    public function deleteType($id)
    {
        return  $this->web->delRowByCondition2($this->tbl_type_name,HaloPdo::condition('id = ?',$id));
    }

    /**
     * 获取类别 列表
     * @return array
     */
    public function getTypeList()
    {
        $result = $this->web_slave->getResultsByCondition($this->tbl_type_name,sprintf('id>0  ORDER BY updated_at DESC'));
        $total = $this->web_slave->getCountByCondition($this->tbl_type_name,HaloPdo::condition('id>0'));
        $data = [
          'list'=>$result ? $result : [],
          'total'=>intval($total),
        ];
        return $data;
    }

    /**
     * 获取类别 详情
     * @param $id
     * @return array|bool|mixed|null|string
     */
    public function getTypeDetail($id)
    {
        $result = $this->web_slave->getRowByCondition($this->tbl_type_name,HaloPdo::condition('id= ?',$id));
        return $result;
    }

}













