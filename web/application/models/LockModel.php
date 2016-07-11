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

    public $keyLockMap = array(
        'lock_type_id'=>'lock_type_id',
        'version'=>'version',
        'try_status'=>'try_status',
        'pic'=>'pic',
        'description'=>'desc',
        'feature'=>'feature',
        'download_ids'=>'download_ids',
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


    /**
     * 修改产品
     * @param $params
     * @return bool|int
     */
    public function createLock($params)
    {
        $timeStr = date('Y-m-d H:i:s');
        $data = [];
        $maps  = $this->keyLockMap;
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
        if($result && !empty($params['params']))
        {
            $this->updateLockParams($result,$params['params']);
        }
        return $result;
    }

    /**
     * 更新产品
     * @param $id
     * @param $params
     * @return bool|int
     */
    public function updateLock($id,$params)
    {
        if(!empty($params))
        {
            $map=$this->keyLockMap;
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

            $result =  $this->web->updateTable($this->tbl_name,$data,HaloPdo::condition('id = ?',$id));
            if($id && !empty($params['params']))
            {
                $this->updateLockParams($id,$params['params']);
            }
            return $result;
        }
        return false;
    }


    /**
     * 更新产品 基本参数
     * @param $id
     * @param $params
     * @return bool|int
     */
    public function updateLockParams($id,$params)
    {
        if(!empty($params))
        {
            $subResultMap=array();
            $insertData=array();
            $updateData=array();
            $existIds=array();
            $updateIds=array();

            $subTmpResult=$this->db->getResultsByCondition('lock_params',HaloPdo::condition('lock_id=?',$id));
            if($subTmpResult)
            {
                foreach($subTmpResult as $v)
                {
                    $existIds[]=$v['id'];
                    $subResultMap[$v['id']]=$v;
                }
            }
            foreach($params as $param)
            {
                $tmpItem=array();
                $tmpId= $param['id'];
                $key = $param['key'];
                $value = $param['value'];
                $desc = $param['desc'];
                $sortNum = $param['sort_num'];
                $tmpItem['param_1']=$key;
                $tmpItem['param_2']=$value;
                $tmpItem['param_3']=$desc;
                $tmpItem['sort_num']=$sortNum;
                $tmpItem['lock_id']=$id;

                if(!empty($param['id']) && array_key_exists($param['id'],$subResultMap))
                {
                    $updateIds[]=$tmpId;
                    if(!($key==$subResultMap[$tmpId]['param_1'] && $value==$subResultMap[$tmpId]['param_2'] && $desc==$subResultMap[$tmpId]['param_3'] && $sortNum==$subResultMap[$tmpId]['sort_num']))
                    {
                        $tmpItem['id']= $tmpId;
                        $updateData[]=$tmpItem;
                    }
                }
                else
                {
                    $insertData[]=$tmpItem;
                }
            }
            if(count($updateData))
            {
                $this->db->batchUpdateData('lock_params',array_keys($updateData[0]),$updateData,"param_1=VALUES(param_1),param_2=VALUES(param_2),param_3=VALUES(param_3),sort_num=VALUES(sort_num)");
            }
            if(count($insertData))
            {
                $this->db->batchInsertData('lock_params',array_keys($insertData[0]),$insertData);
            }
            $deleteIds=array_diff($existIds,$updateIds);
            if(count($deleteIds))
            {
                $this->db->delRowByCondition2('lock_params',sprintf('id IN (%s)',implode(',',$deleteIds)));
            }

        }
        return false;
    }

    /**
     * 删除产品
     * @param $id
     * @return int
     */
    public function deleteLock($id)
    {
        $result = $this->web->delRowByCondition2($this->tbl_name,HaloPdo::condition('id = ?',$id));
        if($result)
        {
            $this->db->delRowByCondition2('lock_params',HaloPdo::condition('lock_id',$id));
        }
        return $result;
    }

    /**
     * 获取产品 列表
     * @return array
     */
    public function getLockList()
    {
        $result = $this->web_slave->getResultsByCondition($this->tbl_name,sprintf('id>0  ORDER BY updated_at DESC'));
        if($result)
        {
            $this->addLockExtraInfo($result);
        }

        $total = $this->web_slave->getCountByCondition($this->tbl_name,HaloPdo::condition('id>0'));
        $data = [
            'list'=>$result ? $result : [],
            'total'=>intval($total),
        ];
        return $data;
    }

    /**
     * 添加类别参数信息
     * @param $result
     */
    public function addLockExtraInfo(&$result)
    {
        $lockIds = [];
        $typeIds = [];
        foreach ($result as $v)
        {
            $lockIds[] = $v['id'];
            $typeIds[] = $v['lock_type_id'];
        }
        $paramsResult = $this->web_slave->getResultsByCondition('lock_params',sprintf('lock_id IN (%s) ORDER BY sort_num ASC',implode(',',$lockIds)));
        $paramsMap = [];
        if($paramsResult)
        {
            foreach ($paramsResult as $item)
            {
                $paramsMap[$item['lock_id']][] = $item;
            }
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
            $lockId = $v['id'];
            $tmpTypeId = $v['lock_type_id'];
            if(array_key_exists($tmpTypeId,$typeMap))
            {
                $v['type'] = $typeMap[$tmpTypeId];
            }
            if(array_key_exists($lockId,$paramsMap))
            {

                $v['params'] = $paramsMap[$lockId];
            }
        }
    }


    /**
     * 获取产品 详情
     * @param $id
     * @return array|bool|mixed|null|string
     */
    public function getLockDetail($id)
    {
        $result = $this->web_slave->getResultsByCondition($this->tbl_name,HaloPdo::condition('id= ?',$id));
        if($result)
        {
            $this->addLockExtraInfo($result);
            $result = $result[0];
        }
        return $result;
    }


}













