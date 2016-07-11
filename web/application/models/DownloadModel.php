<?php

/**
 * Created by PhpStorm.
 * User: luffy
 * Date: 15/10/26
 * Time: 17:55
 */

class DownloadModel extends Halo_Model
{

    private $web;
    private $web_slave;
    public $tbl_name = 'downloads';
    public $timestamps = true;
    public $keyMap = array(
        'title'=>'title',
        'lock_type_id'=>'lock_type_id',
        'btn_name'=>'btn_name',
        'content'=>'content',
        'url'=>'url',
    );

    public function __construct()
    {
        parent::__construct();
        $this->web = DataCenter::getDb('web');
        $this->web_slave = DataCenter::getDb('web_slave');
    }


    /**
     * 修改名称
     * @param $params
     * @return bool|int
     */
    public function create($params)
    {
        $timeStr = date('Y-m-d H:i:s');
        $data = [];
        $maps  = $this->keyMap;
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

        $result = $this->web->insertTable($this->tbl_name,$data);
        return $result;
    }

    /**
     * 更新
     * @param $id
     * @param $params
     * @return bool|int
     */
    public function update($id,$params )
    {
        if(!empty($params))
        {
            $map=$this->keyMap;
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
            return $this->web->updateTable($this->tbl_name,$data,HaloPdo::condition('id = ?',$id));
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
        return  $this->web->delRowByCondition2($this->tbl_name,HaloPdo::condition('id = ?',$id));
    }


    /**
     * 获取 列表
     * @return array
     */
    public function getList()
    {
        $result = $this->web_slave->getResultsByCondition($this->tbl_name,HaloPdo::condition('id>0  ORDER BY updated_at '));
        $total = $this->web_slave->getCountByCondition($this->tbl_name,HaloPdo::condition('id>0'));
        if($result)
        {
            $this->addDownloadTypeInfo($result);
        }

        $data = [
          'list'=>$result ? $result : [],
          'total'=>intval($total),
        ];
        return $data;
    }

    /**
     * 获取 详情
     * @param $id
     * @return array|bool|mixed|null|string
     */
    public function getDetail($id)
    {
        $result = $this->web_slave->getRowByCondition($this->tbl_name,HaloPdo::condition('id= ?',$id));
        if($result)
        {
            $tmp = [$result];
            $this->addDownloadTypeInfo($tmp);
            $result = $tmp[0];
        }
        return $result;
    }

    /**
     * 添加type info
     * @param $result
     */
    public function addDownloadTypeInfo(&$result)
    {
        $typeIds = [];
        foreach ($result as $v)
        {
            $typeIds[] = $v['lock_type_id'];
        }
        $typeIds = array_unique($typeIds);
        $types = $this->web_slave->getResultsByCondition('lock_types',sprintf('id IN (%s)',implode(',',$typeIds)),'id,name');
        $typeMap = [];
        if($types)
        {
            foreach ($types as $type)
            {
                $typeMap[$type['id']] = $type;
            }
        }

        foreach ($result as &$v)
        {
            $tmpTypeId = $v['lock_type_id'];
            if(array_key_exists($tmpTypeId,$typeMap))
            {
                $v['type'] = $typeMap[$tmpTypeId];
            }
        }
    }

}













